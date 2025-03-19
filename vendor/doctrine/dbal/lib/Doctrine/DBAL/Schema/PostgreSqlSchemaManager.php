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

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Types\Type;

/**
 * PostgreSQL Schema Manager.
 *
 * @author Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @since  2.0
 */
class PostgreSqlSchemaManager extends AbstractSchemaManager
{
    /**
     * @var array
     */
    private $existingSchemaPaths;

    /**
     * Gets all the existing schema names.
     *
     * @return array
     */
    public function getSchemaNames()
    {
        $rows = $this->_conn->fetchAll("SELECT nspname as schema_name FROM pg_namespace WHERE nspname !~ '^pg_.*' and nspname != 'information_schema'");

        return array_map(function ($v) { return $v['schema_name']; }, $rows);
    }

    /**
     * Returns an array of schema search paths.
     *
     * This is a PostgreSQL only function.
     *
     * @return array
     */
    public function getSchemaSearchPaths()
    {
        $params = $this->_conn->getParams();
        $schema = explode(",", $this->_conn->fetchColumn('SHOW search_path'));

        if (isset($params['user'])) {
            $schema = str_replace('"$user"', $params['user'], $schema);
        }

        return array_map('trim', $schema);
    }

    /**
     * Gets names of all existing schemas in the current users search path.
     *
     * This is a PostgreSQL only function.
     *
     * @return array
     */
    public function getExistingSchemaSearchPaths()
    {
        if ($this->existingSchemaPaths === null) {
            $this->determineExistingSchemaSearchPaths();
        }

        return $this->existingSchemaPaths;
    }

    /**
     * Sets or resets the order of the existing schemas in the current search path of the user.
     *
     * This is a PostgreSQL only function.
     *
     * @return void
     */
    public function determineExistingSchemaSearchPaths()
    {
        $names = $this->getSchemaNames();
        $paths = $this->getSchemaSearchPaths();

        $this->existingSchemaPaths = array_filter($paths, function ($v) use ($names) {
            return in_array($v, $names);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function dropDatabase($database)
    {
        try {
            parent::dropDatabase($database);
        } catch (DriverException $exception) {
            // If we have a SQLSTATE 55006, the drop database operation failed
            // because of active connections on the database.
            // To force dropping the database, we first have to close all active connections
            // on that database and issue the drop database operation again.
            if ($exception->getSQLState() !== '55006') {
                throw $exception;
            }

            $this->_execSql(
                array(
                    $this->_platform->getDisallowDatabaseConnectionsSQL($database),
                    $this->_platform->getCloseActiveDatabaseConnectionsSQL($database),
                )
            );

            parent::dropDatabase($database);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeyDefinition($tableForeignKey)
    {
        $onUpdate = null;
        $onDelete = null;

        if (preg_match('(ON UPDATE ([a-zA-Z0-9]+( (NULL|ACTION|DEFAULT))?))', $tableForeignKey['condef'], $match)) {
            $onUpdate = $match[1];
        }
        if (preg_match('(ON DELETE ([a-zA-Z0-9]+( (NULL|ACTION|DEFAULT))?))', $tableForeignKey['condef'], $match)) {
            $onDelete = $match[1];
        }

        if (preg_match('/FOREIGN KEY \((.+)\) REFERENCES (.+)\((.+)\)/', $tableForeignKey['condef'], $values)) {
            // PostgreSQL returns identifiers that are keywords with quotes, we need them later, don't get
            // the idea to trim them here.
            $localColumns = array_map('trim', explode(",", $values[1]));
            $foreignColumns = array_map('trim', explode(",", $values[3]));
            $foreignTable = $values[2];
        }

        return new ForeignKeyConstraint(
            $localColumns, $foreignTable, $foreignColumns, $tableForeignKey['conname'],
            array('onUpdate' => $onUpdate, 'onDelete' => $onDelete)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTriggerDefinition($trigger)
    {
        return $trigger['trigger_name'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableViewDefinition($view)
    {
        return new View($view['schemaname'].'.'.$view['viewname'], $view['definition']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableUserDefinition($user)
    {
        return array(
            'user' => $user['usename'],
            'password' => $user['passwd']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableDefinition($table)
    {
        $schemas = $this->getExistingSchemaSearchPaths();
        $firstSchema = array_shift($schemas);

        if ($table['schema_name'] == $firstSchema) {
            return $table['table_name'];
        } else {
            return $table['schema_name'] . "." . $table['table_name'];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @license New BSD License
     * @link http://ezcomponents.org/docs/api/trunk/DatabaseSchema/ezcDbSchemaPgsqlReader.html
     */
    protected function _getPortableTableIndexesList($tableIndexes, $tableName=null)
    {
        $buffer = array();
        foreach ($tableIndexes as $row) {
            $colNumbers = explode(' ', $row['indkey']);
            $colNumbersSql = 'IN (' . join(' ,', $colNumbers) . ' )';
            $columnNameSql = "SELECT attnum, attname FROM pg_attribute
                WHERE attrelid={$row['indrelid']} AND attnum $colNumbersSql ORDER BY attnum ASC;";

            $stmt = $this->_conn->executeQuery($columnNameSql);
            $indexColumns = $stmt->fetchAll();

            // required for getting the order of the columns right.
            foreach ($colNumbers as $colNum) {
                foreach ($indexColumns as $colRow) {
                    if ($colNum == $colRow['attnum']) {
                        $buffer[] = array(
                            'key_name' => $row['relname'],
                            'column_name' => trim($colRow['attname']),
                            'non_unique' => !$row['indisunique'],
                            'primary' => $row['indisprimary'],
                            'where' => $row['where'],
                        );
                    }
                }
            }
        }

        return parent::_getPortableTableIndexesList($buffer, $tableName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableDatabaseDefinition($database)
    {
        return $database['datname'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequencesList($sequences)
    {
        $sequenceDefinitions = array();

        foreach ($sequences as $sequence) {
            if ($sequence['schemaname'] != 'public') {
                $sequenceName = $sequence['schemaname'] . "." . $sequence['relname'];
            } else {
                $sequenceName = $sequence['relname'];
            }

            $sequenceDefinitions[$sequenceName] = $sequence;
        }

        $list = array();

        foreach ($this->filterAssetNames(array_keys($sequenceDefinitions)) as $sequenceName) {
            $list[] = $this->_getPortableSequenceDefinition($sequenceDefinitions[$sequenceName]);
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPortableNamespaceDefinition(array $namespace)
    {
        return $namespace['nspname'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence)
    {
        if ($sequence['schemaname'] != 'public') {
            $sequenceName = $sequence['schemaname'] . "." . $sequence['relname'];
        } else {
            $sequenceName = $sequence['relname'];
        }

        $data = $this->_conn->fetchAll('SELECT min_value, increment_by FROM ' . $this->_platform->quoteIdentifier($sequenceName));

        return new Sequence($sequenceName, $data[0]['increment_by'], $data[0]['min_value']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        if (strtolower($tableColumn['type']) === 'varchar' || strtolower($tableColumn['type']) === 'bpchar') {
            // get length from varchar definition
            $length = preg_replace('~.*\(([0-9]*)\).*~', '$1', $tableColumn['complete_type']);
            $tableColumn['length'] = $length;
        }

        $matches = array();

        $autoincrement = false;
        if (preg_match("/^nextval\('(.*)'(::.*)?\)$/", $tableColumn['default'], $matches)) {
            $tableColumn['sequence'] = $matches[1];
            $tableColumn['default'] = null;
            $autoincrement = true;
        }

        if (preg_match("/^'(.*)'::.*$/", $tableColumn['default'], $matches)) {
            $tableColumn['default'] = $matches[1];
        }

        if (stripos($tableColumn['default'], 'NULL') === 0) {
            $tableColumn['default'] = null;
        }

        $length = (isset($tableColumn['length'])) ? $tableColumn['length'] : null;
        if ($length == '-1' && isset($tableColumn['atttypmod'])) {
            $length = $tableColumn['atttypmod'] - 4;
        }
        if ((int) $length <= 0) {
            $length = null;
        }
        $fixed = null;

        if (!isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }

        $precision = null;
        $scale = null;

        $dbType = strtolower($tableColumn['type']);
        if (strlen($tableColumn['domain_type']) && !$this->_platform->hasDoctrineTypeMappingFor($tableColumn['type'])) {
            $dbType = strtolower($tableColumn['domain_type']);
            $tableColumn['complete_type'] = $tableColumn['domain_complete_type'];
        }

        $type = $this->_platform->getDoctrineTypeMapping($dbType);
        $type = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
        $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);

        switch ($dbType) {
            case 'smallint':
            case 'int2':
                $length = null;
                break;
            case 'int':
            case 'int4':
            case 'integer':
                $length = null;
                break;
            case 'bigint':
            case 'int8':
                $length = null;
                break;
            case 'bool':
            case 'boolean':
                if ($tableColumn['default'] === 'true') {
                    $tableColumn['default'] = true;
                }

                if ($tableColumn['default'] === 'false') {
                    $tableColumn['default'] = false;
                }

                $length = null;
                break;
            case 'text':
                $fixed = false;
                break;
            case 'varchar':
            case 'interval':
            case '_varchar':
                $fixed = false;
                break;
            case 'char':
            case 'bpchar':
                $fixed = true;
                break;
            case 'float':
            case 'float4':
            case 'float8':
            case 'double':
            case 'double precision':
            case 'real':
            case 'decimal':
            case 'money':
            case 'numeric':
                if (preg_match('([A-Za-z]+\(([0-9]+)\,([0-9]+)\))', $tableColumn['complete_type'], $match)) {
                    $precision = $match[1];
                    $scale = $match[2];
                    $length = null;
                }
                break;
            case 'year':
                $length = null;
                break;
        }

        if ($tableColumn['default'] && preg_match("('([^']+)'::)", $tableColumn['default'], $match)) {
            $tableColumn['default'] = $match[1];
        }

        $options = array(
            'length'        => $length,
            'notnull'       => (bool) $tableColumn['isnotnull'],
            'default'       => $tableColumn['default'],
            'primary'       => (bool) ($tableColumn['pri'] == 't'),
            'precision'     => $precision,
            'scale'         => $scale,
            'fixed'         => $fixed,
            'unsigned'      => false,
            'autoincrement' => $autoincrement,
            'comment'       => isset($tableColumn['comment']) && $tableColumn['comment'] !== ''
                ? $tableColumn['comment']
                : null,
        );

        $column = new Column($tableColumn['field'], Type::getType($type), $options);

        if (isset($tableColumn['collation']) && !empty($tableColumn['collation'])) {
            $column->setPlatformOption('collation', $tableColumn['collation']);
        }

        return $column;
    }
}
