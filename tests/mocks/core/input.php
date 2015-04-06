<?php

class Mock_Core_Input extends CI_Input {

	/**
	 * Since we use GLOBAL to fetch Security and Utf8 classes,
	 * we need to use inversion of control to mock up
	 * the same process within CI_Input class constructor.
	 *
	 * @covers CI_Input::__construct()
	 */
	public function __construct($security, $utf8)
	{
		$this->_allow_get_array	= (config_item('allow_get_array') === TRUE);
		$this->_enable_xss	= (config_item('global_xss_filtering') === TRUE);
		$this->_enable_csrf	= (config_item('csrf_protection') === TRUE);

		// Assign Security and Utf8 classes
		$this->security = $security;
		$this->uni = $utf8;

		// Sanitize global arrays
		$this->_sanitize_globals();
	}

	public function fetch_from_array($array, $index = '', $xss_clean = FALSE)
	{
		return parent::_fetch_from_array($array, $index, $xss_clean);
	}

	/**
	 * Lie about being a CLI request
	 *
	 * We take advantage of this in libraries/Session_test
	 */
	public function is_cli_request()
	{
		return FALSE;
	}

	public function __set($name, $value)
	{
		if ($name === 'ip_address')
		{
			$this->ip_address = $value;
		}
	}

}