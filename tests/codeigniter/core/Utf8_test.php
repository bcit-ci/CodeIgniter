<?php

class Utf8_test extends CI_TestCase {

	public function set_up()
	{
		$this->ci_set_config('charset', 'UTF-8');
		$this->utf8 = new Mock_Core_Utf8();
		$this->ci_instance_var('utf8', $this->utf8);
	}

	// --------------------------------------------------------------------

	/**
	 * __construct() test
	 *
	 * @covers	CI_Utf8::__construct
	 */
	public function test___construct()
	{
		if (defined('PREG_BAD_UTF8_ERROR') && (ICONV_ENABLED === TRUE OR MB_ENABLED === TRUE) && strtoupper(config_item('charset')) === 'UTF-8')
		{
			$this->assertTrue(UTF8_ENABLED);
		}
		else
		{
			$this->assertFalse(UTF8_ENABLED);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * is_ascii() test
	 *
	 * Note: DO NOT move this below test_clean_string()
	 */
	public function test_is_ascii()
	{
		$this->assertTrue($this->utf8->is_ascii('foo bar'));
		$this->assertFalse($this->utf8->is_ascii('тест'));
	}

	// --------------------------------------------------------------------

	/**
	 * clean_string() test
	 *
	 * @depends	test_is_ascii
	 * @covers	CI_Utf8::clean_string
	 */
	public function test_clean_string()
	{
		$this->assertEquals('foo bar', $this->utf8->clean_string('foo bar'));

		$illegal_utf8 = "\xc0тест";
		if (MB_ENABLED)
		{
			$this->assertEquals('тест', $this->utf8->clean_string($illegal_utf8));
		}
		elseif (ICONV_ENABLED)
		{
			// This is a known issue, iconv doesn't always work with //IGNORE
			$this->assertContains($utf8->clean_string($illegal_utf8), array('тест', ''));
		}
		else
		{
			$this->assertEquals($illegal_utf8, $this->utf8->clean_string($illegal_utf8));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * convert_to_utf8() test
	 *
	 * @covers	CI_Utf8::convert_to_utf8
	 */
	public function test_convert_to_utf8()
	{
		if (MB_ENABLED OR ICONV_ENABLED)
		{
			$this->assertEquals('тест', $this->utf8->convert_to_utf8('����', 'WINDOWS-1251'));
		}
		else
		{
			$this->assertFalse($this->utf8->convert_to_utf8('����', 'WINDOWS-1251'));
		}
	}

}
