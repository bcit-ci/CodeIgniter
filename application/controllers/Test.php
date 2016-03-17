<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	
	private $cookie_name;
	private $session_hash;

	const regex_4_bits = '/^[0-9a-f]{40,128}$/';
	const regex_5_bits = '/^[0-9a-v]{32,103}$/';
	const regex_6_bits = '/^[0-9a-zA-Z\-,]{27,86}$/';
	
	private $index = 0;
	
	public function index()
	{	
		$this->load->library('unit_test');
		
		$this->load->helper('cookie');
	
		$this->cookie_name = $this->config->item('sess_cookie_name');
		
		echo $this->unit->run( $this->default_values(), 'is_true', 'Default_values', $this->session_hash);
		echo $this->unit->run( $this->sha_512_4(), 'is_true', 'Sha512_4', $this->session_hash);
		echo $this->unit->run( $this->sha_512_5(), 'is_true', 'Sha512_5', $this->session_hash);
		echo $this->unit->run( $this->sha_512_6(), 'is_true', 'Sha512_6', $this->session_hash);
	}
	
	private function default_values()
	{
		$this->config->set_item('sess_hash_function', '1');
		$this->config->set_item('sess_hash_bits_per_character', '4');
		
		$regex = $this::regex_4_bits;
		
		return $this->run_test($regex);
	}
	
	private function sha_512_4()
	{
		$this->config->set_item('sess_hash_function', 'sha512');
		$this->config->set_item('sess_hash_bits_per_character', '4');

		$regex = $this::regex_4_bits;
		
		return $this->run_test($regex);
	}
	
	private function sha_512_5()
	{
		$this->config->set_item('sess_hash_function', 'sha512');
		$this->config->set_item('sess_hash_bits_per_character', '5');

		$regex = $this::regex_5_bits;
		
		return $this->run_test($regex);
	}
	
	private function sha_512_6()
	{
		$this->config->set_item('sess_hash_function', 'sha512');
		$this->config->set_item('sess_hash_bits_per_character', '6');

		$regex = $this::regex_6_bits;
		
		return $this->run_test($regex);
	}
	
	public function get_session_hash()
	{	
		$headers = headers_list();
		
		$cookie_text = 'Set-Cookie: ';
		
		foreach ($headers as $header)
		{
			if (strpos($header, $cookie_text) === 0) 
			{
				$cookie_value = str_replace(array('&', urlencode(',')), array(urlencode('&'), ',') , substr($header, strlen($cookie_text)));
				$cookie_items = explode(';', $cookie_value);
				
				foreach ($cookie_items as $cookie_item)
				{
					$cookie_pieces = explode('=', $cookie_item);
					
					if (isset($cookie_pieces[1]) && $cookie_pieces[0] == $this->cookie_name)
					{
						return $cookie_pieces[1];
					}	
				}	
			}
		}
		
		return false;
	}
	
	public function run_test($regex)
	{
		$this->index ++;
		
		$session_instance = 'session' . $this->index;
		
		$this->load->library('session', array(), $session_instance);
		
		$this->$session_instance->sess_regenerate();
		
		$this->session_hash = $this->get_session_hash();
		
		$this->$session_instance->sess_destroy();
		
		if ($this->session_hash)
		{
			return (bool) preg_match($regex, $this->session_hash);
				
		}
		else
		{
			return false;
		}
		
	}

}