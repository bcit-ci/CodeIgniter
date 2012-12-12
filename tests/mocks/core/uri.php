<?php

class Mock_Core_URI extends CI_URI {

	public function __construct()
	{
		$test = CI_TestCase::instance();
		$cls =& $test->ci_core_class('cfg');

		// set predictable config values
		$test->ci_set_config(array(
			'index_page'		=> 'index.php',
			'base_url'			=> 'http://example.com/',
			'subclass_prefix'	=> 'MY_'
		));

		$this->config = new $cls;

	}

	protected function _is_cli_request()
	{
		return FALSE;
	}

}