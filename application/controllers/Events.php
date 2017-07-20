<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User Management class
 */
class Events extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Event_model');
    }
	
	public function index() {
		
	}
	
	public function list() {
		
	}
	
	/*
     * User registration
     */
    public function registration(){
        $data = array();
        $eventData = array();
        if($this->input->post('eventSubmit')){
			
            $this->form_validation->set_rules('event_title', 'Event Title', 'required');
			$this->form_validation->set_rules('event_description', 'Event Description', 'required');			
            $this->form_validation->set_rules('event_type', 'Event Type', 'required');
			$this->form_validation->set_rules('event_contact', 'Event Contact', 'required');
			$this->form_validation->set_rules('event_starttime', 'Event Start Time', 'required');
			$this->form_validation->set_rules('event_endtime', 'Event End Time', 'required');           
			
			$eventData = array(				
                'event_title' => strip_tags($this->input->post('event_title')),
				'event_description' => strip_tags($this->input->post('event_description')),                        
                'event_type' => strip_tags($this->input->post('event_type')),
				'event_contact' => strip_tags($this->input->post('event_contact')),
				'event_starttime' => strip_tags($this->input->post('event_starttime')),
				'event_endtime' => strip_tags($this->input->post('event_endtime'))
            );

            if($this->form_validation->run() == true){
				
				$config['upload_path']   = './assets/files/event/'; 
				$config['allowed_types'] = 'gif|jpg|png'; 
				//$config['max_size']      = 100; 
				//$config['max_width']     = 1024; 
				//$config['max_height']    = 768;  
				$this->load->library('upload', $config);
				
				if ( ! $this->upload->do_upload('event_image')) {
					$error = array('error' => $this->upload->display_errors()); 
					$this->load->helper('form');
					$data['event'] = $eventData;
					$this->load->view('events/registration', $error); 
				} else {				
					$eventData['event_image'] = $this->upload->data('file_name');					
				}
				
                $insert = $this->Event_model->insert($eventData);
                if($insert){					
                    $this->session->set_userdata('success_msg', 'Your Event registration was successfully.');
                    redirect('event');
                }else{
                    $data['error_msg'] = 'Some problems occured, please try again.';
                }
            }
        }
        $data['event'] = $eventData;
        //load the view
        $this->load->view('events/registration', $data);
    }

}

?>