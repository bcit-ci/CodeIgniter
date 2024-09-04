<?php
class Notes_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function notes_list()
{
    $user_id = $this->session->userdata('id'); 
    $this->db->where('user_id', $user_id); 
    $query = $this->db->get('notes');
    return $query->result();
}

    public function get_notes_by_id($id)
    {
        $query = $this->db->get_where('notes', array('id' => $id));
        return $query->row();
    }

    public function createOrUpdate()
    {
		$this->load->helper('url');
		$user_id = $this->session->userdata('id'); // Get logged-in user ID
		$id = $this->input->post('id');
		$data = array(
			'user_id' => $user_id, // Add user_id to data
			'title' => $this->input->post('title'),
			'description' => $this->input->post('description')
		);
        if (empty($id)) {
            return $this->db->insert('notes', $data);
        } else {
            $this->db->where('id', $id);
            return $this->db->update('notes', $data);
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('notes');
    }
}
