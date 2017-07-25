<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event_model extends CI_Model{
	
    function __construct() {
        $this->eventTbl = 'events';
		$this->userdetailsTbl = 'user_details';
    }
	
    /*
     * get rows from the event table
     */
    function getEvents($params = array()){
        $this->db->select('*');
        $this->db->from($this->eventTbl.' u');
		//$this->db->join($this->userdetailsTbl. ' p','p.uid = e.user_id');
        
        //fetch data by conditions
        if(array_key_exists("conditions",$params)){
            foreach ($params['conditions'] as $key => $value) {
                $this->db->where($key,$value);
            }
        }
        
        if(array_key_exists("event_id",$params)){
            $this->db->where('event_id',$params['event_id']);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            //set start and limit
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }
            $query = $this->db->get();
            if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
                $result = $query->num_rows();
            }elseif(array_key_exists("returnType",$params) && $params['returnType'] == 'single'){
                $result = ($query->num_rows() > 0)?$query->row_array():FALSE;
            }else{
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }

        //return fetched data
        return $result;
    }
    
    /*
     * Insert event information
     */
    public function insert($data = array()) {
        //add created and modified data if not included
        if(!array_key_exists("event_created", $data)){
            $data['event_created'] = date("Y-m-d H:i:s");
        }
        if(!array_key_exists("event_modified", $data)){
            $data['event_modified'] = date("Y-m-d H:i:s");
        }
        
        //insert event data into event table		
		$insert = $this->db->insert($this->eventTbl, $data);		
        
        //return the status
        if($insert){
            return $this->db->insert_id();;
        }else{
            return false;
        }
    }

}