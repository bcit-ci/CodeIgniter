<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Management class
 */
class Events extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('event');
    }
	
	public function index() {
		echo "this is test";
		//$this->load->view('users/login');
	}
	
	/*
     * User registration
     */
    public function registration(){
        $data = array();
        $eventData = array();
        if($this->input->post('eventSubmit')){
            $this->form_validation->set_rules('event_title', 'First Name', 'required');
			$this->form_validation->set_rules('event_description', 'Last Name', 'required');			
            $this->form_validation->set_rules('event_type', 'password', 'required');
			$this->form_validation->set_rules('event_contact', 'Address', 'required');
			$this->form_validation->set_rules('event_starttime', 'Country', 'required');
			$this->form_validation->set_rules('event_endtime', 'State', 'required');
			$this->form_validation->set_rules('event_image', 'City', 'required');			
            
			
			$eventData = array(				
                'event_title' => strip_tags($this->input->post('event_title')),
				'event_description' => strip_tags($this->input->post('event_description')),                        
                'event_type' => strip_tags($this->input->post('event_type')),
				'event_contact' => strip_tags($this->input->post('event_contact')),
				'event_starttime' => strip_tags($this->input->post('event_starttime')),
				'event_endtime' => strip_tags($this->input->post('event_endtime')),
				'city' => strip_tags($this->input->post('city')),
				'zipcode' => strip_tags($this->input->post('zipcode'))				
            );

            if($this->form_validation->run() == true){
                $insert = $this->user->insert($userData, 'user');
                if($insert){
					$userDetailsData['uid'] = $insert;
					$insert = $this->user->insert($userDetailsData, 'profile');
                    $this->session->set_userdata('success_msg', 'Your registration was successfully. Please login to your account.');
                    redirect('login');
                }else{
                    $data['error_msg'] = 'Some problems occured, please try again.';
                }
            }
        }
        $data['user'] = $userData;
        //load the view
        $this->load->view('events/registration', $data);
    }

}

?>