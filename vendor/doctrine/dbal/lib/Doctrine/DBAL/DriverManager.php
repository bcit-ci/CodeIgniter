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

namespace Doctrine\DBAL;

use Doctrine\Common\EventManager;

/**
 * Factory for creating Doctrine\DBAL\Connection instances.
 *
 * @author Roman Borschel <roman@code-factory.org>
 * @since 2.0
 */
final class DriverManager
{
    /**
     * List of supported drivers and their mappings to the driver classes.
     *
     * To add your own driver use the 'driverClass' parameter to
     * {@link DriverManager::getConnection()}.
     *
     * @var array
     */
     private static $_driverMap = array(
         'pdo_mysql'          => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
         'pdo_sqlite'         => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
         'pdo_pgsql'          => 'Doctrine\DBAL\Driver\PDOPgSql\Driver',
         'pdo_oci'            => 'Doctrine\DBAL\Driver\PDOOracle\Driver',
         'oci8'               => 'Doctrine\DBAL\Driver\OCI8\Driver',
         'ibm_db2'            => 'Doctrine\DBAL\Driver\IBMDB2\DB2Driver',
         'pdo_sqlsrv'         => 'Doctrine\DBAL\Driver\PDOSqlsrv\Driver',
         'mysqli'             => 'Doctrine\DBAL\Driver\Mysqli\Driver',
         'drizzle_pdo_mysql'  => 'Doctrine\DBAL\Driver\DrizzlePDOMySql\Driver',
         'sqlanywhere'        => 'Doctrine\DBAL\Driver\SQLAnywhere\Driver',
         'sqlsrv'             => 'Doctrine\DBAL\Driver\SQLSrv\Driver',
    );

    /**
     * List of URL schemes from a database URL and their mappings to driver.
     */
    private static $driverSchemeAliases = array(
        'db2'        => 'ibm_db2',
        'mssql'      => 'pdo_sqlsrv',
        'mysql'      => 'pdo_mysql',
        'mysql2'     => 'pdo_mysql', // Amazon RDS, for some weird reason
        'postgres'   => 'pdo_pgsql',
        'postgresql' => 'pdo_pgsql',
        'pgsql'      => 'pdo_pgsql',
        'sqlite'     => 'pdo_sqlite',
        'sqlite3'    => 'pdo_sqlite',
    );

    /**
     * Private constructor. This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Creates a connection object based on the specified parameters.
     * This method returns a Doctrine\DBAL\Connection which wraps the underlying
     * driver connection.
     *
     * $params must contain at least one of the following.
     *
     * Either 'driver' with one of the following values:
     *
     *     pdo_mysql
     *     pdo_sqlite
     *     pdo_pgsql
     *     pdo_oci (unstable)
     *     pdo_sqlsrv
     *     pdo_sqlsrv
     *     mysqli
     *     sqlanywhere
     *     sqlsrv
     *     ibm_db2 (unstable)
     *     drizzle_pdo_mysql
     *
     * OR 'driverClass' that contains the full class name (with namespace) of the
     * driver class to instantiate.
     *
     * Other (optional) parameters:
     *
     * <b>user (string)</b>:
     * The username to use when connecting.
     *
     * <b>password (string)</b>:
     * The password to use when connecting.
     *
     * <b>driverOptions (array)</b>:
     * Any additional driver-specific options for the driver. These are just passed
     * through to the driver.
     *
     * <b>pdo</b>:
     * You can pass an existing PDO instance through this parameter. The PDO
     * instance will be wrapped in a Doctrine\DBAL\Connection.
     *
     * <b>wrapperClass</b>:
     * You may specify a custom wrapper class through the 'wrapperClass'
     * parameter but this class MUST inherit from Doctrine\DBAL\Connection.
     *
     * <b>driverClass</b>:
     * The driver class to use.
     *
     * @param array                              $params       The parameters.
     * @param \Doctrine\DBAL\Configuration|null  $config       The configuration to use.
     * @param \Doctrine\Common\EventManager|null $eventManager The event manager to use.
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnection(
            array $params,
            Configuration $config = null,
            EventManager $eventManager = null)
    {
        // create default config and event manager, if not set
        if ( ! $config) {
            $config = new Configuration();
        }
        if ( ! $eventManager) {
            $eventManager = new EventManager();
        }

        $params = self::parseDatabaseUrl($params);
        
        // check for existing pdo object
        if (isset($params['pdo']) && ! $params['pdo'] instanceof \PDO) {
            throw DBALException::invalidPdoInstance();
        } elseif (isset($params['pdo'])) {
            $params['pdo']->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $params['driver'] = 'pdo_' . $params['pdo']->getAttribute(\PDO::ATTR_DRIVER_NAME);
        } else {
            self::_checkParams($params);
        }
        if (isset($params['driverClass'])) {
            $className = $params['driverClass'];
        } else {
            $className = self::$_driverMap[$params['driver']];
        }

        $driver = new $className();

        $wrapperClass = 'Doctrine\DBAL\Connection';
        if (isset($params['wrapperClass'])) {
            if (is_subclass_of($params['wrapperClass'], $wrapperClass)) {
               $wrapperClass = $params['wrapperClass'];
            } else {
                throw DBALException::invalidWrapperClass($params['wrapperClass']);
            }
        }

        return new $wrapperClass($params, $driver, $config, $eventManager);
    }

    /**
     * Returns the list of supported drivers.
     *
     * @return array
     */
    public static function getAvailableDrivers()
    {
        return array_keys(self::$_driverMap);
    }

    /**
     * Checks the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private static function _checkParams(array $params)
    {
        // check existence of mandatory parameters

        // driver
        if ( ! isset($params['driver']) && ! isset($params['driverClass'])) {
            throw DBALException::driverRequired();
        }

        // check validity of parameters

        // driver
        if (isset($params['driver']) && ! isset(self::$_driverMap[$params['driver']])) {
            throw DBALException::unknownDriver($params['driver'], array_keys(self::$_driverMap));
        }

        if (isset($params['driverClass']) && ! in_array('Doctrine\DBAL\Driver', class_implements($params['driverClass'], true))) {
            throw DBALException::invalidDriverClass($params['driverClass']);
        }
    }

    /**
     * Extracts parts from a database URL, if present, and returns an
     * updated list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @param array A modified list of parameters with info from a database
     *              URL extracted into indidivual parameter parts.
     *
     */
    private static function parseDatabaseUrl(array $params)
    {
        if (!isset($params['url'])) {
            return $params;
        }
        
        // (pdo_)?sqlite3?:///... => (pdo_)?sqlite3?://localhost/... or else the URL will be invalid
        $url = preg_replace('#^((?:pdo_)?sqlite3?):///#', '$1://localhost/', $params['url']);
        
        $url = parse_url($url);
        
        if ($url === false) {
            throw new DBALException('Malformed parameter "url".');
        }
        
        if (isset($url['scheme'])) {
            $params['driver'] = str_replace('-', '_', $url['scheme']); // URL schemes must not contain underscores, but dashes are ok
            if (isset(self::$driverSchemeAliases[$params['driver']])) {
                $params['driver'] = self::$driverSchemeAliases[$params['driver']]; // use alias like "postgres", else we just let checkParams decide later if the driver exists (for literal "pdo-pgsql" etc)
            }
        }
        
        if (isset($url['host'])) {
            $params['host'] = $url['host'];
        }
        if (isset($url['port'])) {
            $params['port'] = $url['port'];
        }
        if (isset($url['user'])) {
            $params['user'] = $url['user'];
        }
        if (isset($url['pass'])) {
            $params['password'] = $url['pass'];
        }
        
        if (isset($url['path'])) {
            if (!isset($url['scheme']) || (strpos($url['scheme'], 'sqlite') !== false && $url['path'] == ':memory:')) {
                $params['dbname'] = $url['path']; // if the URL was just "sqlite::memory:", which parses to scheme and path only
            } else {
                $params['dbname'] = substr($url['path'], 1); // strip the leading slash from the URL
            }
        }
        
        if (isset($url['query'])) {
            $query = array();
            parse_str($url['query'], $query); // simply ingest query as extra params, e.g. charset or sslmode
            $params = array_merge($params, $query); // parse_str wipes existing array elements
        }
        
        return $params;
    }
}
