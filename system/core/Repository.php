<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CI_Repository
 *
 * @author	Enmanuel Toribio
 */
class CI_Repository extends CI_Model{
    protected $table_name;
	
	/**
	 * Construct the Repository object
	 *
	 * @access	public
	 * @param	string	table name
	 */
    public function __construct($table_name=''){
		parent::__construct(); 
		$this->table_name = $table_name; 
	}

	/**
	 * Get all rows of the given table
	 *
	 * @access	public
	 * @param	string	the name of the fields in the table to return
	 * @param	int		limit of rows
	 * @param	int		offset
	 * @return	Array(Object)
	 */
    public function get_all($fields='*', $limit='',$offset=''){ 
		$this->db->select($fields)->from($this->table_name)->limit($limit,$offset);
		return $this->db->get()->result(); 
	}
	
	/**
	 * Get a specific row by the id field
	 *
	 * @access	public
	 * @param	int/string	id of the row
	 * @return	Object
	 */
    public function get_by_id($id){
        $entity = $this->db->get_where($this->table_name, array('id'=>$id))->result();
        if($entity) return $entity[0];
		return false;
    }
	
	/**
	 * Remove a specific row by the id field
	 *
	 * @access	public
	 * @param	int/string	id of the row
	 */
    public function remove($id){ 
		$this->db->delete($this->table_name, array('id' => $id)); 
	}
	
	/**
	 * Add a row to the table
	 *
	 * @access	public
	 * @param	int/string	id of the row
	 */
    public function add($entity){
		$this->db->insert($this->table_name, $entity); 
        return $this->db->insert_id();
    }
	
	/**
	 * Update a specific row by the entity id field value
	 *
	 * @access	public
	 * @param	Array/Object	entity to be updated
	 */
    public function update($entity){
        if(is_object ($entity)){ $entity = $this->object_to_array($entity); }
        $id = $entity['id'];
        unset($entity['id']);
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $entity);
    }
	
    protected function object_to_array($d) {
        if (is_object($d)) { $d = get_object_vars($d); }
        if (is_array($d)) { return array_map(null, $d); }
        else {  return $d; }
    }
}

/* End of file Repository.php */
/* Location: ./system/core/Repository.php */