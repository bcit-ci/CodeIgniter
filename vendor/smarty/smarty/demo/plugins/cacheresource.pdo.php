<?php

/**
 * PDO Cache Handler
 * Allows you to store Smarty Cache files into your db.
 * Example table :
 * CREATE TABLE `smarty_cache` (
 * `id` char(40) NOT NULL COMMENT 'sha1 hash',
 * `name` varchar(250) NOT NULL,
 * `cache_id` varchar(250) DEFAULT NULL,
 * `compile_id` varchar(250) DEFAULT NULL,
 * `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 * `expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
 * `content` mediumblob NOT NULL,
 * PRIMARY KEY (`id`),
 * KEY `name` (`name`),
 * KEY `cache_id` (`cache_id`),
 * KEY `compile_id` (`compile_id`),
 * KEY `modified` (`modified`),
 * KEY `expire` (`expire`)
 * ) ENGINE=InnoDB
 * Example usage :
 *      $cnx    =   new PDO("mysql:host=localhost;dbname=mydb", "username", "password");
 *      $smarty->setCachingType('pdo');
 *      $smarty->loadPlugin('Smarty_CacheResource_Pdo');
 *      $smarty->registerCacheResource('pdo', new Smarty_CacheResource_Pdo($cnx, 'smarty_cache'));
 *
 * @author Beno!t POLASZEK - 2014
 */
class Smarty_CacheResource_Pdo extends Smarty_CacheResource_Custom
{

    protected $fetchStatements = Array('default'                 => 'SELECT %2$s
                                                                                    FROM %1$s 
                                                                                    WHERE 1 
                                                                                    AND id          = :id 
                                                                                    AND cache_id    IS NULL 
                                                                                    AND compile_id  IS NULL',

                                       'withCacheId'             => 'SELECT %2$s
                                                                                FROM %1$s 
                                                                                WHERE 1 
                                                                                AND id          = :id 
                                                                                AND cache_id    = :cache_id 
                                                                                AND compile_id  IS NULL',

                                       'withCompileId'           => 'SELECT %2$s
                                                                                FROM %1$s 
                                                                                WHERE 1 
                                                                                AND id          = :id 
                                                                                AND compile_id  = :compile_id 
                                                                                AND cache_id    IS NULL',

                                       'withCacheIdAndCompileId' => 'SELECT %2$s
                                                                                FROM %1$s 
                                                                                WHERE 1 
                                                                                AND id          = :id 
                                                                                AND cache_id    = :cache_id 
                                                                                AND compile_id  = :compile_id');
    protected $insertStatement = 'INSERT INTO %s

                                                SET id          =   :id, 
                                                    name        =   :name, 
                                                    cache_id    =   :cache_id, 
                                                    compile_id  =   :compile_id, 
                                                    modified    =   CURRENT_TIMESTAMP, 
                                                    expire      =   DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :expire SECOND), 
                                                    content     =   :content 

                                                ON DUPLICATE KEY UPDATE 
                                                    name        =   :name, 
                                                    cache_id    =   :cache_id, 
                                                    compile_id  =   :compile_id, 
                                                    modified    =   CURRENT_TIMESTAMP, 
                                                    expire      =   DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :expire SECOND), 
                                                    content     =   :content';

    protected $deleteStatement = 'DELETE FROM %1$s WHERE %2$s';
    protected $truncateStatement = 'TRUNCATE TABLE %s';

    protected $fetchColumns = 'modified, content';
    protected $fetchTimestampColumns = 'modified';

    protected $pdo, $table, $database;

    /* 
     * Constructor 
     * 
     * @param PDO $pdo PDO : active connection 
     * @param string $table : table (or view) name 
     * @param string $database : optionnal - if table is located in another db 
     */
    public function __construct(PDO $pdo, $table, $database = null)
    {

        if (is_null($table)) {
            throw new SmartyException("Table name for caching can't be null");
        }

        $this->pdo = $pdo;
        $this->table = $table;
        $this->database = $database;

        $this->fillStatementsWithTableName();
    }

    /* 
     * Fills the table name into the statements. 
     * 
     * @return Current Instance 
     * @access protected 
     */
    protected function fillStatementsWithTableName()
    {

        foreach ($this->fetchStatements AS &$statement) {
            $statement = sprintf($statement, $this->getTableName(), '%s');
        }

        $this->insertStatement = sprintf($this->insertStatement, $this->getTableName());
        $this->deleteStatement = sprintf($this->deleteStatement, $this->getTableName(), '%s');
        $this->truncateStatement = sprintf($this->truncateStatement, $this->getTableName());

        return $this;
    }

    /* 
     * Gets the fetch statement, depending on what you specify 
     * 
     * @param string        $columns : the column(s) name(s) you want to retrieve from the database 
     * @param string        $id unique cache content identifier 
     * @param string|null   $cache_id cache id 
     * @param string|null   $compile_id compile id 
     * @access protected 
     */
    protected function getFetchStatement($columns, $id, $cache_id = null, $compile_id = null)
    {

        if (!is_null($cache_id) && !is_null($compile_id)) {
            $query = $this->fetchStatements['withCacheIdAndCompileId'] AND $args = Array('id' => $id, 'cache_id' => $cache_id, 'compile_id' => $compile_id);
        } elseif (is_null($cache_id) && !is_null($compile_id)) {
            $query = $this->fetchStatements['withCompileId'] AND $args = Array('id' => $id, 'compile_id' => $compile_id);
        } elseif (!is_null($cache_id) && is_null($compile_id)) {
            $query = $this->fetchStatements['withCacheId'] AND $args = Array('id' => $id, 'cache_id' => $cache_id);
        } else {
            $query = $this->fetchStatements['default'] AND $args = Array('id' => $id);
        }

        $query = sprintf($query, $columns);

        $stmt = $this->pdo->prepare($query);

        foreach ($args AS $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt;
    }

    /**
     * fetch cached content and its modification time from data source
     *
     * @param  string      $id         unique cache content identifier
     * @param  string      $name       template name
     * @param  string|null $cache_id   cache id
     * @param  string|null $compile_id compile id
     * @param  string      $content    cached content
     * @param  integer     $mtime      cache modification timestamp (epoch)
     *
     * @return void
     * @access protected
     */
    protected function fetch($id, $name, $cache_id = null, $compile_id = null, &$content, &$mtime)
    {

        $stmt = $this->getFetchStatement($this->fetchColumns, $id, $cache_id, $compile_id);
        $stmt       ->execute();
        $row = $stmt->fetch();
        $stmt       ->closeCursor();

        if ($row) {
            $content = $this->outputContent($row['content']);
            $mtime = strtotime($row['modified']);
        } else {
            $content = null;
            $mtime = null;
        }
    }

    /**
     * Fetch cached content's modification timestamp from data source
     * {@internal implementing this method is optional.
     *  Only implement it if modification times can be accessed faster than loading the complete cached content.}}
     *
     * @param  string      $id         unique cache content identifier
     * @param  string      $name       template name
     * @param  string|null $cache_id   cache id
     * @param  string|null $compile_id compile id
     *
     * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
     * @access protected
     */
    //    protected function fetchTimestamp($id, $name, $cache_id = null, $compile_id = null) {
    //        $stmt       =   $this->getFetchStatement($this->fetchTimestampColumns, $id, $cache_id, $compile_id);
    //        $stmt       ->  execute();
    //        $mtime      =   strtotime($stmt->fetchColumn());
    //        $stmt       ->  closeCursor();
    //        return $mtime;
    //    }

    /**
     * Save content to cache
     *
     * @param string       $id         unique cache content identifier
     * @param string       $name       template name
     * @param string|null  $cache_id   cache id
     * @param string|null  $compile_id compile id
     * @param integer|null $exp_time   seconds till expiration time in seconds or null
     * @param string       $content    content to cache
     *
     * @return boolean success
     * @access protected
     */
    protected function save($id, $name, $cache_id = null, $compile_id = null, $exp_time, $content)
    {

        $stmt = $this->pdo->prepare($this->insertStatement);

        $stmt       ->bindValue('id', $id);
        $stmt       ->bindValue('name', $name);
        $stmt       ->bindValue('cache_id', $cache_id, (is_null($cache_id)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt       ->bindValue('compile_id', $compile_id, (is_null($compile_id)) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt       ->bindValue('expire', (int) $exp_time, PDO::PARAM_INT);
        $stmt       ->bindValue('content', $this->inputContent($content));
        $stmt       ->execute();

        return !!$stmt->rowCount();
    }

    /* 
     * Encodes the content before saving to database 
     * 
     * @param string $content 
     * @return string $content 
     * @access protected 
     */
    protected function inputContent($content)
    {
        return $content;
    }

    /* 
     * Decodes the content before saving to database 
     * 
     * @param string $content 
     * @return string $content 
     * @access protected 
     */
    protected function outputContent($content)
    {
        return $content;
    }

    /**
     * Delete content from cache
     *
     * @param string|null $name       template name
     * @param string|null $cache_id   cache id
     * @param string|null $compile_id compile id
     * @param  integer|null|-1 $exp_time seconds till expiration or null
     *
     * @return integer number of deleted caches
     * @access protected
     */
    protected function delete($name = null, $cache_id = null, $compile_id = null, $exp_time = null)
    {

        // delete the whole cache 
        if ($name === null && $cache_id === null && $compile_id === null && $exp_time === null) {
            // returning the number of deleted caches would require a second query to count them 
            $this->pdo->query($this->truncateStatement);
            return - 1;
        }
        // build the filter 
        $where = array();
        // equal test name 
        if ($name !== null) {
            $where[] = 'name = ' . $this->pdo->quote($name);
        }
        // equal test cache_id and match sub-groups 
        if ($cache_id !== null) {
            $where[] = '(cache_id = ' . $this->pdo->quote($cache_id)
                . ' OR cache_id LIKE ' . $this->pdo->quote($cache_id . '|%') . ')';
        }
        // equal test compile_id 
        if ($compile_id !== null) {
            $where[] = 'compile_id = ' . $this->pdo->quote($compile_id);
        }
        // for clearing expired caches 
        if ($exp_time === Smarty::CLEAR_EXPIRED) {
            $where[] = 'expire < CURRENT_TIMESTAMP';
        } // range test expiration time
        elseif ($exp_time !== null) {
            $where[] = 'modified < DATE_SUB(NOW(), INTERVAL ' . intval($exp_time) . ' SECOND)';
        }
        // run delete query 
        $query = $this->pdo->query(sprintf($this->deleteStatement, join(' AND ', $where)));
        return $query->rowCount();
    }

    /**
     * Gets the formatted table name
     *
     * @return string
     * @access protected
     */
    protected function getTableName()
    {
        return (is_null($this->database)) ? "`{$this->table}`" : "`{$this->database}`.`{$this->table}`";
    }
}
 