<?php

class Mock_Core_URI extends CI_URI {

	public function __construct()
	{
		$test = CI_TestCase::instance();
		$cls =& $test->ci_core_class('cfg');

		// set predictable config values
		$test->ci_set_config(array(
			'index_page'		=> 'index.php',
			'base_url'		=> 'http://example.com/',
			'subclass_prefix'	=> 'MY_',
			'enable_query_strings'	=> FALSE,
			'permitted_uri_chars'	=> 'a-z 0-9~%.:_\-'
		));

		$this->config = new $cls;

		if ($this->config->item('enable_query_strings') !== TRUE OR is_cli())
		{
			$this->_permitted_uri_chars = $this->config->item('permitted_uri_chars');
		}
	}

	public function _set_permitted_uri_chars($value)
	{
		$this->_permitted_uri_chars = $value;
	}

}