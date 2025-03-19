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

namespace Doctrine\DBAL\Portability;

use Doctrine\DBAL\Cache\QueryCacheProfile;

/**
 * Portability wrapper for a Connection.
 *
 * @link   www.doctrine-project.org
 * @since  2.0
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Connection extends \Doctrine\DBAL\Connection
{
    const PORTABILITY_ALL               = 255;
    const PORTABILITY_NONE              = 0;
    const PORTABILITY_RTRIM             = 1;
    const PORTABILITY_EMPTY_TO_NULL     = 4;
    const PORTABILITY_FIX_CASE          = 8;

    const PORTABILITY_DB2               = 13;
    const PORTABILITY_ORACLE            = 9;
    const PORTABILITY_POSTGRESQL        = 13;
    const PORTABILITY_SQLITE            = 13;
    const PORTABILITY_OTHERVENDORS      = 12;
    const PORTABILITY_DRIZZLE           = 13;
    const PORTABILITY_SQLANYWHERE       = 13;
    const PORTABILITY_SQLSRV            = 13;

    /**
     * @var integer
     */
    private $portability = self::PORTABILITY_NONE;

    /**
     * @var integer
     */
    private $case;

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        $ret = parent::connect();
        if ($ret) {
            $params = $this->getParams();
            if (isset($params['portability'])) {
                if ($this->getDatabasePlatform()->getName() === "oracle") {
                    $params['portability'] = $params['portability'] & self::PORTABILITY_ORACLE;
                } elseif ($this->getDatabasePlatform()->getName() === "postgresql") {
                    $params['portability'] = $params['portability'] & self::PORTABILITY_POSTGRESQL;
                } elseif ($this->getDatabasePlatform()->getName() === "sqlite") {
                    $params['portability'] = $params['portability'] & self::PORTABILITY_SQLITE;
                } elseif ($this->getDatabasePlatform()->getName() === "drizzle") {
                    $params['portability'] = self::PORTABILITY_DRIZZLE;
                } elseif ($this->getDatabasePlatform()->getName() === 'sqlanywhere') {
                    $params['portability'] = self::PORTABILITY_SQLANYWHERE;
                } elseif ($this->getDatabasePlatform()->getName() === 'db2') {
                    $params['portability'] = self::PORTABILITY_DB2;
                } elseif ($this->getDatabasePlatform()->getName() === 'mssql') {
                    $params['portability'] = $params['portability'] & self::PORTABILITY_SQLSRV;
                } else {
                    $params['portability'] = $params['portability'] & self::PORTABILITY_OTHERVENDORS;
                }
                $this->portability = $params['portability'];
            }
            if (isset($params['fetch_case']) && $this->portability & self::PORTABILITY_FIX_CASE) {
                if ($this->_conn instanceof \Doctrine\DBAL\Driver\PDOConnection) {
                    // make use of c-level support for case handling
                    $this->_conn->setAttribute(\PDO::ATTR_CASE, $params['fetch_case']);
                } else {
                    $this->case = ($params['fetch_case'] == \PDO::CASE_LOWER) ? CASE_LOWER : CASE_UPPER;
                }
            }
        }

        return $ret;
    }

    /**
     * @return integer
     */
    public function getPortability()
    {
        return $this->portability;
    }

    /**
     * @return integer
     */
    public function getFetchCase()
    {
        return $this->case;
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        $stmt = new Statement(parent::executeQuery($query, $params, $types, $qcp), $this);
        $stmt->setFetchMode($this->defaultFetchMode);

        return $stmt;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($statement)
    {
        $stmt = new Statement(parent::prepare($statement), $this);
        $stmt->setFetchMode($this->defaultFetchMode);

        return $stmt;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $this->connect();

        $stmt = call_user_func_array(array($this->_conn, 'query'), func_get_args());
        $stmt = new Statement($stmt, $this);
        $stmt->setFetchMode($this->defaultFetchMode);

        return $stmt;
    }
}
