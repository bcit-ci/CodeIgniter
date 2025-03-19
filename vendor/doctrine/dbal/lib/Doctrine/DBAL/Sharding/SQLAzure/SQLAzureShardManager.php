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

use Doctrine\DBAL\Sharding\ShardManager;
use Doctrine\DBAL\Sharding\ShardingException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

/**
 * Sharding using the SQL Azure Federations support.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class SQLAzureShardManager implements ShardManager
{
    /**
     * @var string
     */
    private $federationName;

    /**
     * @var boolean
     */
    private $filteringEnabled;

    /**
     * @var string
     */
    private $distributionKey;

    /**
     * @var string
     */
    private $distributionType;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $currentDistributionValue;

    /**
     * @param \Doctrine\DBAL\Connection $conn
     *
     * @throws \Doctrine\DBAL\Sharding\ShardingException
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
        $params = $conn->getParams();

        if ( ! isset($params['sharding']['federationName'])) {
            throw ShardingException::missingDefaultFederationName();
        }

        if ( ! isset($params['sharding']['distributionKey'])) {
            throw ShardingException::missingDefaultDistributionKey();
        }

        if ( ! isset($params['sharding']['distributionType'])) {
            throw ShardingException::missingDistributionType();
        }

        $this->federationName = $params['sharding']['federationName'];
        $this->distributionKey = $params['sharding']['distributionKey'];
        $this->distributionType = $params['sharding']['distributionType'];
        $this->filteringEnabled = (isset($params['sharding']['filteringEnabled'])) ? (bool) $params['sharding']['filteringEnabled'] : false;
    }

    /**
     * Gets the name of the federation.
     *
     * @return string
     */
    public function getFederationName()
    {
        return $this->federationName;
    }

    /**
     * Gets the distribution key.
     *
     * @return string
     */
    public function getDistributionKey()
    {
        return $this->distributionKey;
    }

    /**
     * Gets the Doctrine Type name used for the distribution.
     *
     * @return string
     */
    public function getDistributionType()
    {
        return $this->distributionType;
    }

    /**
     * Sets Enabled/Disable filtering on the fly.
     *
     * @param boolean $flag
     *
     * @return void
     */
    public function setFilteringEnabled($flag)
    {
        $this->filteringEnabled = (bool) $flag;
    }

    /**
     * {@inheritDoc}
     */
    public function selectGlobal()
    {
        if ($this->conn->isTransactionActive()) {
            throw ShardingException::activeTransaction();
        }

        $sql = "USE FEDERATION ROOT WITH RESET";
        $this->conn->exec($sql);
        $this->currentDistributionValue = null;
    }

    /**
     * {@inheritDoc}
     */
    public function selectShard($distributionValue)
    {
        if ($this->conn->isTransactionActive()) {
            throw ShardingException::activeTransaction();
        }

        if ($distributionValue === null || is_bool($distributionValue) || !is_scalar($distributionValue)) {
            throw ShardingException::noShardDistributionValue();
        }

        $platform = $this->conn->getDatabasePlatform();
        $sql = sprintf(
            "USE FEDERATION %s (%s = %s) WITH RESET, FILTERING = %s;",
            $platform->quoteIdentifier($this->federationName),
            $platform->quoteIdentifier($this->distributionKey),
            $this->conn->quote($distributionValue),
            ($this->filteringEnabled ? 'ON' : 'OFF')
        );

        $this->conn->exec($sql);
        $this->currentDistributionValue = $distributionValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentDistributionValue()
    {
        return $this->currentDistributionValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getShards()
    {
        $sql = "SELECT member_id as id,
                      distribution_name as distribution_key,
                      CAST(range_low AS CHAR) AS rangeLow,
                      CAST(range_high AS CHAR) AS rangeHigh
                      FROM sys.federation_member_distributions d
                      INNER JOIN sys.federations f ON f.federation_id = d.federation_id
                      WHERE f.name = " . $this->conn->quote($this->federationName);

        return $this->conn->fetchAll($sql);
    }

     /**
      * {@inheritDoc}
      */
    public function queryAll($sql, array $params = array(), array $types = array())
    {
        $shards = $this->getShards();
        if (!$shards) {
            throw new \RuntimeException("No shards found for " . $this->federationName);
        }

        $result = array();
        $oldDistribution = $this->getCurrentDistributionValue();

        foreach ($shards as $shard) {
            $this->selectShard($shard['rangeLow']);
            foreach ($this->conn->fetchAll($sql, $params, $types) as $row) {
                $result[] = $row;
            }
        }

        if ($oldDistribution === null) {
            $this->selectGlobal();
        } else {
            $this->selectShard($oldDistribution);
        }

        return $result;
    }

    /**
     * Splits Federation at a given distribution value.
     *
     * @param mixed $splitDistributionValue
     *
     * @return void
     */
    public function splitFederation($splitDistributionValue)
    {
        $type = Type::getType($this->distributionType);

        $sql = "ALTER FEDERATION " . $this->getFederationName() . " " .
               "SPLIT AT (" . $this->getDistributionKey() . " = " .
               $this->conn->quote($splitDistributionValue, $type->getBindingType()) . ")";
        $this->conn->exec($sql);
    }
}
