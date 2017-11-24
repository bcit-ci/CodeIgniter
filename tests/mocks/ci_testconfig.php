<?php

class CI_TestConfig extends CI_Config {

	public $config = [];
	public $_config_paths = [APPPATH];
	public $loaded = [];

	public function item($key, $index = '')
	{
		return isset($this->config[$key]) ? $this->config[$key] : FALSE;
	}

	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$this->loaded[] = $file;
		return TRUE;
	}

}
