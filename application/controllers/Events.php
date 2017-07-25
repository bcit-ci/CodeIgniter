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
		$this->load->library('parser');
		$data['events'] = $this->Event_model->getEvents();
		//print '<pre>';print_r($data);die;
		$this->parser->parse('events/listing', $data);
		//$this->load->view('events/listing', $data);		
	}
	
	public function listing() {
		
	}
	
	/*
     * User registration
     */
    public function registration(){
        $data = array();
        $eventData = array();
		//print '<pre>';print_r($this->input);die;
        if($this->input->post('eventSubmit')){
			
            $this->form_validation->set_rules('event_title', 'Event Title', 'required');
			$this->form_validation->set_rules('event_description', 'Event Description', 'required');
            if($this->input->post('event_address') != 'This is Online event') {
				$this->form_validation->set_rules('event_address', 'Address', 'required');
				$this->form_validation->set_rules('event_city', 'City', 'required');
				$this->form_validation->set_rules('event_state', 'State', 'required');
				$this->form_validation->set_rules('event_zipcode', 'Zipcode', 'required');
			}
			$this->form_validation->set_rules('event_type', 'Event Type', 'required');
			$this->form_validation->set_rules('event_contact', 'Event Contact', 'required');
			//$this->form_validation->set_rules('event_location', 'Event End Time', 'required');
			$this->form_validation->set_rules('event_starttime', 'Event Start Time', 'required');
			$this->form_validation->set_rules('event_endtime', 'Event End Time', 'required');
			$this->form_validation->set_rules('event_privacy', 'Visibility', 'required');
			
			
			$eventData = array(				
                'event_title' => strip_tags($this->input->post('event_title')),
				'event_description' => strip_tags($this->input->post('event_description')),
				'event_address' => strip_tags($this->input->post('event_address')),
				'event_city' => strip_tags($this->input->post('event_city')),
				'event_state' => strip_tags($this->input->post('event_state')),
				'event_zipcode' => strip_tags($this->input->post('event_zipcode')),
                'event_type' => strip_tags($this->input->post('event_type')),
				'event_contact' => strip_tags($this->input->post('event_contact')),
				'event_starttime' => strip_tags($this->input->post('event_starttime')),
				'event_endtime' => strip_tags($this->input->post('event_endtime')),
				'event_privacy' => strip_tags($this->input->post('event_privacy'))
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
                    redirect('events');
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