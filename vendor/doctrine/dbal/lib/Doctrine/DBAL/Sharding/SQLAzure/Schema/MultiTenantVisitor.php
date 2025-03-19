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

namespace Doctrine\DBAL\Sharding\SQLAzure\Schema;

use Doctrine\DBAL\Schema\Visitor\Visitor;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Index;

/**
 * Converts a single tenant schema into a multi-tenant schema for SQL Azure
 * Federations under the following assumptions:
 *
 * - Every table is part of the multi-tenant application, only explicitly
 *   excluded tables are non-federated. The behavior of the tables being in
 *   global or federated database is undefined. It depends on you selecting a
 *   federation before DDL statements or not.
 * - Every Primary key of a federated table is extended by another column
 *   'tenant_id' with a default value of the SQLAzure function
 *   `federation_filtering_value('tenant_id')`.
 * - You always have to work with `filtering=On` when using federations with this
 *   multi-tenant approach.
 * - Primary keys are either using globally unique ids (GUID, Table Generator)
 *   or you explicitly add the tenent_id in every UPDATE or DELETE statement
 *   (otherwise they will affect the same-id rows from other tenents as well).
 *   SQLAzure throws errors when you try to create IDENTIY columns on federated
 *   tables.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class MultiTenantVisitor implements Visitor
{
    /**
     * @var array
     */
    private $excludedTables = array();

    /**
     * @var string
     */
    private $tenantColumnName;

    /**
     * @var string
     */
    private $tenantColumnType = 'integer';

    /**
     * Name of the federation distribution, defaulting to the tenantColumnName
     * if not specified.
     *
     * @var string
     */
    private $distributionName;

    /**
     * @param array       $excludedTables
     * @param string      $tenantColumnName
     * @param string|null $distributionName
     */
    public function __construct(array $excludedTables = array(), $tenantColumnName = 'tenant_id', $distributionName = null)
    {
        $this->excludedTables = $excludedTables;
        $this->tenantColumnName = $tenantColumnName;
        $this->distributionName = $distributionName ?: $tenantColumnName;
    }

    /**
     * {@inheritdoc}
     */
    public function acceptTable(Table $table)
    {
        if (in_array($table->getName(), $this->excludedTables)) {
            return;
        }

        $table->addColumn($this->tenantColumnName, $this->tenantColumnType, array(
            'default' => "federation_filtering_value('". $this->distributionName ."')",
        ));

        $clusteredIndex = $this->getClusteredIndex($table);

        $indexColumns = $clusteredIndex->getColumns();
        $indexColumns[] = $this->tenantColumnName;

        if ($clusteredIndex->isPrimary()) {
            $table->dropPrimaryKey();
            $table->setPrimaryKey($indexColumns);
        } else {
            $table->dropIndex($clusteredIndex->getName());
            $table->addIndex($indexColumns, $clusteredIndex->getName());
            $table->getIndex($clusteredIndex->getName())->addFlag('clustered');
        }
    }

    /**
     * @param \Doctrine\DBAL\Schema\Table $table
     *
     * @return \Doctrine\DBAL\Schema\Index
     *
     * @throws \RuntimeException
     */
    private function getClusteredIndex($table)
    {
        foreach ($table->getIndexes() as $index) {
            if ($index->isPrimary() && ! $index->hasFlag('nonclustered')) {
                return $index;
            } elseif ($index->hasFlag('clustered')) {
                return $index;
            }
        }
        throw new \RuntimeException("No clustered index found on table " . $table->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSchema(Schema $schema)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptColumn(Table $table, Column $column)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptForeignKey(Table $localTable, ForeignKeyConstraint $fkConstraint)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptIndex(Table $table, Index $index)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function acceptSequence(Sequence $sequence)
    {
    }
}
