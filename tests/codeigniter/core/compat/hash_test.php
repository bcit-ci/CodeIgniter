<?php

class hash_test extends CI_TestCase {

	public function test_bootstrap()
	{
		if (is_php('5.6'))
		{
			return $this->markTestSkipped('ext/hash is available on PHP 5.6');
		}

		$this->assertTrue(function_exists('hash_equals'));
		is_php('5.5') OR $this->assertTrue(function_exists('hash_pbkdf2'));
	}

	// ------------------------------------------------------------------------

	/**
	 * hash_equals() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_hash_equals()
	{
		$this->assertTrue(hash_equals('same', 'same'));
		$this->assertFalse(hash_equals('not1same', 'not2same'));
		$this->assertFalse(hash_equals('short', 'longer'));
		$this->assertFalse(hash_equals('longer', 'short'));
		$this->assertFalse(hash_equals('', 'notempty'));
		$this->assertFalse(hash_equals('notempty', ''));
		$this->assertTrue(hash_equals('', ''));
	}

	// ------------------------------------------------------------------------

	/**
	 * hash_pbkdf2() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_hash_pbkdf2()
	{
		if (is_php('5.5'))
		{
			return $this->markTestSkipped('hash_pbkdf2() is available on PHP 5.5');
		}

		$this->assertEquals('0c60c80f961f0e71f3a9', hash_pbkdf2('sha1', 'password', 'salt', 1, 20));
		$this->assertEquals(
			"\x0c\x60\xc8\x0f\x96\x1f\x0e\x71\xf3\xa9\xb5\x24\xaf\x60\x12\x06\x2f\xe0\x37\xa6",
			hash_pbkdf2('sha1', 'password', 'salt', 1, 20, TRUE)
		);
		$this->assertEquals('3d2eec4fe41c849b80c8d8366', hash_pbkdf2('sha1', 'passwordPASSWORDpassword', 'saltSALTsaltSALTsaltSALTsaltSALTsalt', 4096, 25));
		$this->assertEquals(
			"\x3d\x2e\xec\x4f\xe4\x1c\x84\x9b\x80\xc8\xd8\x36\x62\xc0\xe4\x4a\x8b\x29\x1a\x96\x4c\xf2\xf0\x70\x38",
			hash_pbkdf2('sha1', 'passwordPASSWORDpassword', 'saltSALTsaltSALTsaltSALTsaltSALTsalt', 4096, 25, TRUE)
		);
		$this->assertEquals('120fb6cffcf8b32c43e7', hash_pbkdf2('sha256', 'password', 'salt', 1, 20));
		$this->assertEquals(
			"\x12\x0f\xb6\xcf\xfc\xf8\xb3\x2c\x43\xe7\x22\x52\x56\xc4\xf8\x37\xa8\x65\x48\xc9",
			hash_pbkdf2('sha256', 'password', 'salt', 1, 20, TRUE)
		);
		$this->assertEquals(
			'348c89dbcbd32b2f32d814b8116e84cf2b17347e',
			hash_pbkdf2('sha256', 'passwordPASSWORDpassword', 'saltSALTsaltSALTsaltSALTsaltSALTsalt', 4096, 40)
		);
		$this->assertEquals(
			"\x34\x8c\x89\xdb\xcb\xd3\x2b\x2f\x32\xd8\x14\xb8\x11\x6e\x84\xcf\x2b\x17\x34\x7e\xbc\x18\x00\x18\x1c\x4e\x2a\x1f\xb8\xdd\x53\xe1\xc6\x35\x51\x8c\x7d\xac\x47\xe9",
			hash_pbkdf2('sha256', 'passwordPASSWORDpassword', 'saltSALTsaltSALTsaltSALTsaltSALTsalt', 4096, 40, TRUE)
		);
	}

}