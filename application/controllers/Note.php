<?php
class Note extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('notes_model');
        $this->load->helper('url_helper');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['notes'] = $this->notes_model->notes_list();
        $data['title'] = 'Notes List';
        $this->load->view('list', $data);
    }

    public function create()
    {
        $data['title'] = 'Create Note';
        $this->load->view('create', $data);
    }

    public function edit($id)
    {
        $id = $this->uri->segment(3);
        $data = array();
        if (empty($id))
        {
         show_404();
        }else{
          $data['note'] = $this->notes_model->get_notes_by_id($id);
          $this->load->view('edit', $data);
        }
    }
    public function store()
    {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $id = $this->input->post('id');
        if ($this->form_validation->run() === FALSE)
        {
            if(empty($id)){
              redirect( base_url('note/create') );
            }else{
             redirect( base_url('note/edit/'.$id) );
            }
        }
        else
        {
            $data['note'] = $this->notes_model->createOrUpdate();
            redirect( base_url('note') );
        }

    }


    public function delete()
    {
        $id = $this->uri->segment(3);

        if (empty($id))
        {
            show_404();
        }

        $notes = $this->notes_model->delete($id);

        redirect( base_url('note') );
    }
	public function logout() {
        $this->session->sess_destroy();
        redirect(base_url('register')); 
    }
}
