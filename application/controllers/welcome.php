<?php

class Welcome extends CI_Controller {

	function Welcome()
	{
		parent::CI_Controller();
	}

	function index()
	{
		$this->load->view('welcome_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */