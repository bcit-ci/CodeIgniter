<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sessions extends CI_Model {
    var $sess_table_name;
    
	function __construct()
    {
        parent::__construct();
        $this->load->database();
		$this->sess_table_name =  $this->config->item('sess_table_name');
        
    }
	
    function get_session($criteria) 
    {
        $query = $this->db->get_where($this->sess_table_name,$criteria);
        if ($query->num_rows()==0)
        {
            return FALSE;
        }
        return $query->row();
    }
    
    function update_session($session_id, $data) 
    {
		$this->db->where('session_id', $session_id);
		$this->db->update($this->sess_table_name, $data);
    }
    
    function create_session($data) 
    {
        $this->db->query($this->db->insert_string($this->sess_table_name, $data));
    }
    
    function delete_session($session_id) 
    {
        $this->db->where('session_id', $session_id);
        $this->db->delete($this->sess_table_name);
    }
    
    function purge($expire) 
    {
        $this->db->where("last_activity < {$expire}");
        $this->db->delete($this->sess_table_name);
    }
    
}