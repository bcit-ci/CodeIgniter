<?php

class Mock_Libraries_Calendar extends CI_Calendar {

	public function __construct($config = array())
	{
		$this->CI = new stdClass;
		$this->CI->lang = new Mock_Core_Lang();

		if ( ! in_array('calendar_lang.php', $this->CI->lang->is_loaded, TRUE))
		{
			$this->CI->lang->load('calendar');
		}

		$this->local_time = time();

		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		log_message('debug', 'Calendar Class Initialized');
	}

}