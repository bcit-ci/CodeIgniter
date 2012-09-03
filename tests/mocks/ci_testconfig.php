<?php

class CI_TestConfig {
	public $config = array();
	public $_config_paths = array();
	public $to_get = FALSE;

	public function item($key)
	{
		return isset($this->config[$key]) ? $this->config[$key] : FALSE;
	}

	public function load($arg1, $arg2, $arg3)
	{
		return TRUE;
	}

	public function get($arg1, $arg2)
	{
		return $this->to_get;
	}
}

