<?php
/**
 * author: lzf
 * createTime: 15/7/15 11:30
 * description:
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class CI_DB_mongo_driver extends CI_DB {

    public $dbdriver = 'mongo';

    private $_db_conn = NULL;

    private $_collections = array();

    private $_select = array();

    private $_where = array();

    private $_limit = array();

    private $_table = '';


    public function __construct($params){
        parent::__construct($params);
    }

    public function db_connect(){
        $this->conn_id = new MongoClient($this->hostname.':'.$this->port);

        if ( ! $this->conn_id){
            return FALSE;
        }

        if ($this->database !== '' && ! $this->db_select()){
            log_message('error', 'Unable to select database: '.$this->database);

            return ($this->db_debug === TRUE)
                ? $this->display_error('db_unable_to_select', $this->database)
                : FALSE;
        }
        return $this->conn_id;
    }

    public function db_select($database = '')
    {
        if ($database === ''){
            $database = $this->database;
        }

        if ($this->_db_conn = $this->conn_id->selectDB($this->database)){
            $this->database = $database;
            $this->_collections = $this->_db_conn->getCollectionNames();
            return TRUE;
        }

        return FALSE;
    }

    public function insert($table = '', $set = NULL, $escape = NULL){
        $this->_collection_exists($table);
        return $this->_db_conn->{$table}->insert($set);
    }

    public function select($select = '*', $escape = NULL){
        $this->_select = explode(',',$select);
    }

    public function from($from = ''){
        $this->_collection_exists($from);
        $this->_table = $from;
    }

    public function delete($table = '', $where = '', $limit = NULL, $reset_data = true){
        $this->_collection_exists($table);

        if(count($where) <= 0){
            $this->display_error('delete need conditions!');
        }

        return $this->_db_conn->{$table}->remove($where);
    }

    public function update($table = '', $set = NULL, $where = NULL, $limit = NULL){
        $this->_collection_exists($table);

        if(count($set) <= 0){
            $this->display_error('mongo need update data!');
        }

        return $this->_db_conn->{$table}->update($where,array('$set' => $set));
    }

    public function limit($value, $offset = 0){
        $this->_limit = array('offset' => $offset,'value' => $value);
    }

    public function count_all_results($table = '', $reset = true){
        $this->_collection_exists($table);

        return $this->_db_conn->{$table}->count();
    }

    public function where($key, $value = NULL, $escape = NULL){
        if(!empty($key) && !empty($value)){
            $this->_where[$key] = $value;
        }
    }

    public function get($table = '', $limit = NULL, $offset = NULL){
        !empty($table)?$this->from($table):0;
        ($limit > 0 && $offset > 0)?$this->limit($limit,$offset):0;

        $chain = NULL;
        $result = array();
        $chain = $this->_db_conn->{$this->_table}->find($this->_where);

        if($this->_limit['value'] > 0 ){
            $chain->skip($this->_limit['offset'])->limit($this->_limit['value']);
        }

        foreach($chain as $v){
            $result[] = $v;
        }
        $this->_reset_params();

        unset($chain);
        return $result;
    }

    private function _collection_exists($name){
        if(empty($name) || !in_array($name,$this->_collections)){
            $this->display_error("collection not exists!");
        }
    }

    private function _reset_params(){
        $this->_select = array();
        $this->_where = array();
        $this->_limit = array();
        $this->_table = '';
    }

}