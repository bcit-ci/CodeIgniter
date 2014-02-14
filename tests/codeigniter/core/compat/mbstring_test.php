<?php

class mbstring_test extends CI_TestCase {

	public function test_bootstrap()
	{
		if (MB_ENABLED)
		{
			return $this->markTestSkipped('ext/mbstring is loaded');
		}

		$this->assertTrue(function_exists('mb_strlen'));
		$this->assertTrue(function_exists('mb_substr'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @depends	test_bootstrap
	 */
	public function test_mb_strlen()
	{
		$this->assertEquals(ICONV_ENABLED ? 4 : 8, mb_strlen('тест'));
		$this->assertEquals(ICONV_ENABLED ? 4 : 8, mb_strlen('тест', 'UTF-8'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @depends	test_boostrap
	 */
	public function test_mb_strpos()
	{
		$this->assertEquals(ICONV_ENABLED ? 3 : 6, mb_strpos('тест', 'с'));
		$this->assertFalse(mb_strpos('тест', 'с', 3));
		$this->assertEquals(ICONV_ENABLED ? 3 : 6, mb_strpos('тест', 'с', 1, 'UTF-8'));
	}

	// ------------------------------------------------------------------------

	/**
	 * @depends	test_boostrap
	 */
	public function test_mb_substr()
	{
		$this->assertEquals(ICONV_ENABLED ? 'стинг' : 'естинг', mb_substr('тестинг', 2));
		$this->assertEquals(ICONV_ENABLED ? 'нг' : 'г', mb_substr('тестинг', -2));
		$this->assertEquals(ICONV_ENABLED ? 'ст' : 'е', mb_substr('тестинг', 2, 2));
		$this->assertEquals(ICONV_ENABLED ? 'стинг' : 'естинг', mb_substr('тестинг', 2, 'UTF-8'));
		$this->assertEquals(ICONV_ENABLED ? 'нг' : 'г', mb_substr('тестинг', -2, 'UTF-8'));
		$this->assertEquals(ICONV_ENABLED ? 'ст' : 'е', mb_substr('тестинг', 2, 2, 'UTF-8'));
	}

}