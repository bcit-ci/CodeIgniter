<?php

class CI_TestConfig {

	public $config = array();
	public $_config_paths = array(APPPATH);
	public $loaded = array();

	public function item($key)
	{
		return isset($this->config[$key]) ? $this->config[$key] : FALSE;
	}

	public function load($file, $arg2 = FALSE, $arg3 = FALSE)
	{
		$this->loaded[] = $file;
		return TRUE;
	}

}