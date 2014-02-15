<?php

class Mock_Core_Utf8 extends CI_Utf8 {

	/**
	 * We need to define UTF8_ENABLED the same way that
	 * CI_Utf8 constructor does.
	 *
	 * @covers CI_Utf8::__construct()
	 */
	public function __construct()
	{
		defined('UTF8_ENABLED') OR define('UTF8_ENABLED', TRUE);
	}

}