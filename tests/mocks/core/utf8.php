<?php

class Mock_Core_Utf8 extends CI_Utf8 {

	/**
	 * We need to define UTF8_ENABLED the same way that
	 * CI_Utf8 constructor does.
	 */
	public function __construct()
	{
		if (defined('UTF8_ENABLED'))
		{
			return;
		}

		parent::__construct();
	}

}