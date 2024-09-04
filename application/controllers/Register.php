<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('id'))
        {
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->load->model('register_model');
    }

    public function index()
    {
        $this->load->view('register');
    }

    public function validation()
    {
        // Set validation rules
        $this->form_validation->set_rules('user_name', 'Name', 'required|trim');
        $this->form_validation->set_rules('user_email', 'Email Address', 'required|trim|valid_email|is_unique[codeigniter_register.email]');
        $this->form_validation->set_rules('user_password', 'Password', 'required');
        
        if ($this->form_validation->run())
        {
            $password = $this->input->post('user_password');
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $data = array(
                'name'       => $this->input->post('user_name'),
                'email'      => $this->input->post('user_email'),
                'password'   => $hashed_password,
            );

            $id = $this->register_model->insert($data);
            if ($id)
            {
                $this->session->set_flashdata('message', 'Registration successful. You can now log in.');
                redirect('login');
            }
            else
            {
                $this->session->set_flashdata('error', 'An error occurred during registration. Please try again.');
                $this->index();
            }
        }
        else
        {
            $this->index();
        }
    }
}
?>
