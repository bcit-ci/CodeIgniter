<?php

/**
 * @runTestsInSeparateProcesses
 */
class Utf8_test extends CI_TestCase {

	public function test___constructUTF8_ENABLED()
	{
		if ( ! defined('PREG_BAD_UTF8_ERROR') OR (ICONV_ENABLED === FALSE && MB_ENABLED === FALSE))
		{
			return $this->markTestSkipped('PCRE_UTF8 and/or both ext/mbstring & ext/iconv are unavailable');
		}

		new CI_Utf8('UTF-8');
		$this->assertTrue(UTF8_ENABLED);
	}

	// --------------------------------------------------------------------

	public function test__constructUTF8_DISABLED()
	{
		new CI_Utf8('WINDOWS-1251');
		$this->assertFalse(UTF8_ENABLED);
	}

	// --------------------------------------------------------------------

	/**
	 * is_ascii() test
	 *
	 * Note: DO NOT move this below test_clean_string()
	 */
	public function test_is_ascii()
	{
		$utf8 = new CI_Utf8('UTF-8');
		$this->assertTrue($utf8->is_ascii('foo bar'));
		$this->assertFalse($utf8->is_ascii('тест'));
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
		$utf8 = new CI_Utf8('UTF-8');
		$this->assertEquals('foo bar', $utf8->clean_string('foo bar'));

		$illegal_utf8 = "\xc0тест";
		if (MB_ENABLED)
		{
			$this->assertEquals('тест', $utf8->clean_string($illegal_utf8));
		}
		elseif (ICONV_ENABLED)
		{
			// This is a known issue, iconv doesn't always work with //IGNORE
			$this->assertContains($utf8->clean_string($illegal_utf8), array('тест', ''));
		}
		else
		{
			$this->assertEquals($illegal_utf8, $utf8->clean_string($illegal_utf8));
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
		$utf8 = new CI_Utf8('UTF-8');
		if (MB_ENABLED OR ICONV_ENABLED)
		{
			$this->assertEquals('тест', $utf8->convert_to_utf8('����', 'WINDOWS-1251'));
		}
		else
		{
			$this->assertFalse($utf8->convert_to_utf8('����', 'WINDOWS-1251'));
		}
	}

}
