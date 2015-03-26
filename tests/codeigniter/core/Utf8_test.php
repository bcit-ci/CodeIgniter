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

		$examples = array(
			// Valid UTF-8
				"κόσμε"                    => array("κόσμε" => "κόσμε"),
				"中"                       => array("中" => "中"),
				"«foobar»"                 => array("«foobar»" => "«foobar»"),
			// Valid UTF-8 + Invalied Chars
				"κόσμε\xa0\xa1-öäü"        => array("κόσμε-öäü" => "κόσμε-öäü"),
			// Valid ASCII
				"a"                        => array("a" => "a"),
			// Valid ASCII + Invalied Chars
				"a\xa0\xa1-öäü"            => array("a-öäü" => "a-öäü"),
			// Valid 2 Octet Sequence
				"\xc3\xb1"                 => array("ñ" => "ñ"),
			// Invalid 2 Octet Sequence
				"\xc3\x28"                 => array("�(" => "("),
			// Invalid Sequence Identifier
				"\xa0\xa1"                 => array("��" => ""),
			// Valid 3 Octet Sequence
				"\xe2\x82\xa1"             => array("₡" => "₡"),
			// Invalid 3 Octet Sequence (in 2nd Octet)
				"\xe2\x28\xa1"             => array("�(�" => "("),
			// Invalid 3 Octet Sequence (in 3rd Octet)
				"\xe2\x82\x28"             => array("�(" => "("),
			// Valid 4 Octet Sequence
				"\xf0\x90\x8c\xbc"         => array("𐌼" => ""),
			// Invalid 4 Octet Sequence (in 2nd Octet)
				"\xf0\x28\x8c\xbc"         => array("�(��" => "("),
			// Invalid 4 Octet Sequence (in 3rd Octet)
				"\xf0\x90\x28\xbc"         => array("�(�" => "("),
			// Invalid 4 Octet Sequence (in 4th Octet)
				"\xf0\x28\x8c\x28"         => array("�(�(" => "(("),
			// Valid 5 Octet Sequence (but not Unicode!)
				"\xf8\xa1\xa1\xa1\xa1"     => array("�" => ""),
			// Valid 6 Octet Sequence (but not Unicode!)
				"\xfc\xa1\xa1\xa1\xa1\xa1" => array("�" => ""),
		);

		$counter = 0;
		foreach ($examples as $testString => $testResults) {
			foreach ($testResults as $before => $after) {
				if (MB_ENABLED)
				{
					$this->assertEquals($after, $this->utf8->clean_string($before), $counter);
				}
				elseif (ICONV_ENABLED)
				{
					// This is a known issue, iconv doesn't always work with //IGNORE
					$this->assertTrue(in_array($this->utf8->clean_string($before), array($after, ''), TRUE), $counter);
				}
				else
				{
					// TODO
				}
			}
			$counter++;
		}

		$illegal_utf8 = "\xc0тест";
		if (MB_ENABLED)
		{
			$this->assertEquals('тест', $this->utf8->clean_string($illegal_utf8));
		}
		elseif (ICONV_ENABLED)
		{
			// This is a known issue, iconv doesn't always work with //IGNORE
			$this->assertTrue(in_array($this->utf8->clean_string($illegal_utf8), array('тест', ''), TRUE));
		}
		else
		{
			// TODO
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