<?php 

//require_once('Model.php');
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Repository extends CI_Model{
    protected $tableName;
    public function __construct($tableName){ parent::__construct(); $this->tableName = $tableName; }
    public function GetAll(){ return $this->db->query("Select * from $this->tableName")->result(); }
    public function GetById($id, $toArray=false){
        $entity = $this->db->query("Select * from $this->tableName where id=?", array($id))->result();
        if($toArray){ $entity = get_object_vars($entity[0]); }
        return $entity;
    }
    public function Remove($id){ $this->db->query("Delete from $this->tableName where id=?", array($id)); }
    public function Add($entity){
        if(is_object ($entity)){ $entity = $this->objectToArray($entity); }
        $query = "INSERT INTO $this->tableName";
        $values = array();
        $questionMarks = "";
        $keys = "" ;
        foreach($entity as $key=>$value){ $keys .='`'.$key.'`,'; $questionMarks .= '?,'; $values[]=$value; }
        $keys = rtrim($keys,',');
        $questionMarks = rtrim($questionMarks,',');
        $query.=' ('.$keys.') VALUES ('.$questionMarks.')';
        $this->db->query($query , $values);
        return $this->db->insert_id();
    }
    public function Update($entity){
        if(is_object ($entity)){ $entity = $this->objectToArray($entity); }
        $id = $entity['id'];
        unset($entity['id']);
        $this->db->where('id', $id);
        $this->db->update($this->tableName, $entity);
    }

    protected function objectToArray($d) {
        if (is_object($d)) { $d = get_object_vars($d); }
        if (is_array($d)) { return array_map(null, $d); }
        else {  return $d; }
    }
}