<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Sharding\SQLAzure;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

use Doctrine\DBAL\Schema\Synchronizer\AbstractSchemaSynchronizer;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer;

/**
 * SQL Azure Schema Synchronizer.
 *
 * Will iterate over all shards when performing schema operations. This is done
 * by partitioning the passed schema into subschemas for the federation and the
 * global database and then applying the operations step by step using the
 * {@see \Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer}.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class SQLAzureFederationsSynchronizer extends AbstractSchemaSynchronizer
{
    const FEDERATION_TABLE_FEDERATED   = 'azure.federated';
    const FEDERATION_DISTRIBUTION_NAME = 'azure.federatedOnDistributionName';

    /**
     * @var \Doctrine\DBAL\Sharding\SQLAzure\SQLAzureShardManager
     */
    private $shardManager;

    /**
     * @var \Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer
     */
    private $synchronizer;

    /**
     * @param \Doctrine\DBAL\Connection                                  $conn
     * @param \Doctrine\DBAL\Sharding\SQLAzure\SQLAzureShardManager      $shardManager
     * @param \Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer|null $sync
     */
    public function __construct(Connection $conn, SQLAzureShardManager $shardManager, SchemaSynchronizer $sync = null)
    {
        parent::__construct($conn);
        $this->shardManager = $shardManager;
        $this->synchronizer = $sync ?: new SingleDatabaseSynchronizer($conn);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateSchema(Schema $createSchema)
    {
        $sql = array();

        list($global, $federation) = $this->partitionSchema($createSchema);

        $globalSql = $this->synchronizer->getCreateSchema($global);
        if ($globalSql) {
            $sql[] = "-- Create Root Federation\n" .
                     "USE FEDERATION ROOT WITH RESET;";
            $sql = array_merge($sql, $globalSql);
        }

        $federationSql = $this->synchronizer->getCreateSchema($federation);

        if ($federationSql) {
            $defaultValue = $this->getFederationTypeDefaultValue();

            $sql[] = $this->getCreateFederationStatement();
            $sql[] = "USE FEDERATION " . $this->shardManager->getFederationName() . " (" . $this->shardManager->getDistributionKey() . " = " . $defaultValue . ") WITH RESET, FILTERING = OFF;";
            $sql = array_merge($sql, $federationSql);
        }

        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateSchema(Schema $toSchema, $noDrops = false)
    {
        return $this->work($toSchema, function ($synchronizer, $schema) use ($noDrops) {
            return $synchronizer->getUpdateSchema($schema, $noDrops);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getDropSchema(Schema $dropSchema)
    {
        return $this->work($dropSchema, function ($synchronizer, $schema) {
            return $synchronizer->getDropSchema($schema);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function createSchema(Schema $createSchema)
    {
        $this->processSql($this->getCreateSchema($createSchema));
    }

    /**
     * {@inheritdoc}
     */
    public function updateSchema(Schema $toSchema, $noDrops = false)
    {
        $this->processSql($this->getUpdateSchema($toSchema, $noDrops));
    }

    /**
     * {@inheritdoc}
     */
    public function dropSchema(Schema $dropSchema)
    {
        $this->processSqlSafely($this->getDropSchema($dropSchema));
    }

    /**
     * {@inheritdoc}
     */
    public function getDropAllSchema()
    {
        $this->shardManager->selectGlobal();
        $globalSql = $this->synchronizer->getDropAllSchema();

        if ($globalSql) {
            $sql[] = "-- Work on Root Federation\nUSE FEDERATION ROOT WITH RESET;";
            $sql = array_merge($sql, $globalSql);
        }

        $shards = $this->shardManager->getShards();
        foreach ($shards as $shard) {
            $this->shardManager->selectShard($shard['rangeLow']);

            $federationSql = $this->synchronizer->getDropAllSchema();
            if ($federationSql) {
                $sql[] = "-- Work on Federation ID " . $shard['id'] . "\n" .
                         "USE FEDERATION " . $this->shardManager->getFederationName() . " (" . $this->shardManager->getDistributionKey() . " = " . $shard['rangeLow'].") WITH RESET, FILTERING = OFF;";
                $sql = array_merge($sql, $federationSql);
            }
        }

        $sql[] = "USE FEDERATION ROOT WITH RESET;";
        $sql[] = "DROP FEDERATION " . $this->shardManager->getFederationName();

        return $sql;
    }

    /**
     * {@inheritdoc}
     */
    public function dropAllSchema()
    {
        $this->processSqlSafely($this->getDropAllSchema());
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     *
     * @return array
     */
    private function partitionSchema(Schema $schema)
    {
        return array(
            $this->extractSchemaFederation($schema, false),
            $this->extractSchemaFederation($schema, true),
        );
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @param boolean                      $isFederation
     *
     * @return \Doctrine\DBAL\Schema\Schema
     *
     * @throws \RuntimeException
     */
    private function extractSchemaFederation(Schema $schema, $isFederation)
    {
        $partitionedSchema = clone $schema;

        foreach ($partitionedSchema->getTables() as $table) {
            if ($isFederation) {
                $table->addOption(self::FEDERATION_DISTRIBUTION_NAME, $this->shardManager->getDistributionKey());
            }

            if ($table->hasOption(self::FEDERATION_TABLE_FEDERATED) !== $isFederation) {
                $partitionedSchema->dropTable($table->getName());
            } else {
                foreach ($table->getForeignKeys() as $fk) {
                    $foreignTable = $schema->getTable($fk->getForeignTableName());
                    if ($foreignTable->hasOption(self::FEDERATION_TABLE_FEDERATED) !== $isFederation) {
                        throw new \RuntimeException("Cannot have foreign key between global/federation.");
                    }
                }
            }
        }

        return $partitionedSchema;
    }

    /**
     * Work on the Global/Federation based on currently existing shards and
     * perform the given operation on the underlying schema synchronizer given
     * the different partitioned schema instances.
     *
     * @param \Doctrine\DBAL\Schema\Schema $schema
     * @param \Closure                     $operation
     *
     * @return array
     */
    private function work(Schema $schema, \Closure $operation)
    {
        list($global, $federation) = $this->partitionSchema($schema);
        $sql = array();

        $this->shardManager->selectGlobal();
        $globalSql = $operation($this->synchronizer, $global);

        if ($globalSql) {
            $sql[] = "-- Work on Root Federation\nUSE FEDERATION ROOT WITH RESET;";
            $sql   = array_merge($sql, $globalSql);
        }

        $shards = $this->shardManager->getShards();

        foreach ($shards as $shard) {
            $this->shardManager->selectShard($shard['rangeLow']);

            $federationSql = $operation($this->synchronizer, $federation);
            if ($federationSql) {
                $sql[] = "-- Work on Federation ID " . $shard['id'] . "\n" .
                         "USE FEDERATION " . $this->shardManager->getFederationName() . " (" . $this->shardManager->getDistributionKey() . " = " . $shard['rangeLow'].") WITH RESET, FILTERING = OFF;";
                $sql   = array_merge($sql, $federationSql);
            }
        }

        return $sql;
    }

    /**
     * @return string
     */
    private function getFederationTypeDefaultValue()
    {
        $federationType = Type::getType($this->shardManager->getDistributionType());

        switch ($federationType->getName()) {
            case Type::GUID:
                $defaultValue = '00000000-0000-0000-0000-000000000000';
                break;
            case Type::INTEGER:
            case Type::SMALLINT:
            case Type::BIGINT:
                $defaultValue = '0';
                break;
            default:
                $defaultValue = '';
                break;
        }

        return $defaultValue;
    }

    /**
     * @return string
     */
    private function getCreateFederationStatement()
    {
        $federationType = Type::getType($this->shardManager->getDistributionType());
        $federationTypeSql = $federationType->getSqlDeclaration(array(), $this->conn->getDatabasePlatform());

        return "--Create Federation\n" .
               "CREATE FEDERATION " . $this->shardManager->getFederationName() . " (" . $this->shardManager->getDistributionKey() . " " . $federationTypeSql ."  RANGE)";
    }
}
