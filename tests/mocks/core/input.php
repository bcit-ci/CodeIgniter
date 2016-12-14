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
		$this->_enable_csrf	= (config_item('csrf_protection') === TRUE);

		// Assign Security and Utf8 classes
		$this->security = $security;
	}

	public function fetch_from_array($array, $index = '', $xss_clean = FALSE)
	{
		return parent::_fetch_from_array($array, $index, $xss_clean);
	}

	public function __set($name, $value)
	{
		if ($name === 'ip_address')
		{
			$this->ip_address = $value;
		}
	}
}
