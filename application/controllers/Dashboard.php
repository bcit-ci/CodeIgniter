<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('phpmailer');
		$this->load->model('request_model');
    }

	public function index()
	{
		 $request = $this->request_model->get();
		var_dump($request);
		exit;
		$data['request'] = $request;
		$this->load->view('dashboard', $data);
	}
}
