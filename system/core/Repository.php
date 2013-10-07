<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class CI_Repository extends CI_Model{
    protected $tableName;
    public function __construct($tableName=''){
		parent::__construct(); 
		$this->tableName = $tableName; 
	}
    public function GetAll($fields='*', $limit='',$offset=''){ 
		$this->db->select($fields)->from($this->tableName)->limit($limit,$offset);
		return $this->db->get()->result(); 
	}
    public function GetById($id){
        $entity = $this->db->get_where($this->tableName, array('id'=>$id))->result();
        if($entity) return $entity[0];
		return false;
    }
    public function Remove($id){ 
		$this->db->delete($this->tableName, array('id' => $id)); 
	}
    public function Add($entity){
		$this->db->insert($this->tableName, $entity); 
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

/* End of file Repository.php */
/* Location: ./system/core/Repository.php */