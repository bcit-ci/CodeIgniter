<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Request_model extends CI_Model {

	public function __construct() {
			parent::__construct();
			 $this->load->database();
    	}

        public function get(){
 
        $response = array();
        
        // Select record
        $this->db->select('*');
        $q = $this->db->get(TABLE_REQUESTS);
        $response = $q->result_array();
    
        return $response;
        }

  
  public function create($data){
 
  return $this->db->insert(TABLE_REQUESTS, $data);
  }

  
}