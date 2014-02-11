<?php

class Mock_Core_Utf8 extends CI_Utf8 {

	/**
	 * We need to define several constants as
	 * the same process within CI_Utf8 class constructor.
	 *
	 * @covers CI_Utf8::__construct()
	 */
	public function __construct()
	{
		defined('UTF8_ENABLED') OR define('UTF8_ENABLED', TRUE);

		if (extension_loaded('mbstring'))
		{
			defined('MB_ENABLED') OR define('MB_ENABLED', TRUE);
			mb_internal_encoding('UTF-8');
			ini_set('mbstring.substitute_character', 'none');
		}
		else
		{
			defined('MB_ENABLED') OR define('MB_ENABLED', FALSE);
		}

		if (extension_loaded('iconv'))
		{
			defined('ICONV_ENABLED') OR define('ICONV_ENABLED', TRUE);
			iconv_set_encoding('internal_encoding', 'UTF-8');
		}
		else
		{
			defined('ICONV_ENABLED') OR define('ICONV_ENABLED', FALSE);
		}
	}

	public function is_ascii_test($str)
	{
		return $this->_is_ascii($str);
	}

}