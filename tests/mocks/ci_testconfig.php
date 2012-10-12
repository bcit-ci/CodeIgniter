<?php

class CI_TestConfig {

	public $config = array();
	public $_config_paths = array(APPPATH);

	public function item($key)
	{
		return isset($this->config[$key]) ? $this->config[$key] : FALSE;
	}

	public function load($arg1, $arg2, $arg3)
	{
		return TRUE;
	}

}
