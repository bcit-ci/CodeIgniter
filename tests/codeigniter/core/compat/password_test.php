<?php

class password_test extends CI_TestCase {

	public function test_bootstrap()
	{
		if (is_php('5.5'))
		{
			return $this->markTestSkipped('ext/standard/password is available on PHP 5.5');
		}
		// defined as of HHVM 2.3.0, which is also when they introduce password_*() as well
		// Note: Do NOT move this after the CRYPT_BLOWFISH check
		elseif (defined('HHVM_VERSION'))
		{
			$this->markTestSkipped('HHVM 2.3.0+ already has it');
		}
		elseif ( ! defined('CRYPT_BLOWFISH') OR CRYPT_BLOWFISH !== 1)
		{
			$this->assertFalse(defined('PASSWORD_BCRYPT'));
			return $this->markTestSkipped('CRYPT_BLOWFISH is not available');
		}

		$this->assertTrue(defined('PASSWORD_BCRYPT'));
		$this->assertTrue(defined('PASSWORD_DEFAULT'));
		$this->assertEquals(1, PASSWORD_BCRYPT);
		$this->assertEquals(PASSWORD_BCRYPT, PASSWORD_DEFAULT);
		$this->assertTrue(function_exists('password_get_info'));
		$this->assertTrue(function_exists('password_hash'));
		$this->assertTrue(function_exists('password_needs_rehash'));
		$this->assertTrue(function_exists('password_verify'));
	}

	// ------------------------------------------------------------------------

	/**
	 * password_get_info() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_password_get_info()
	{
		$expected = array(
			'algo' => 1,
			'algoName' => 'bcrypt',
			'options' => array('cost' => 10)
		);

		// default
		$this->assertEquals($expected, password_get_info('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y'));

		$expected['options']['cost'] = 11;

		// cost
		$this->assertEquals($expected, password_get_info('$2y$11$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y'));

		$expected = array(
			'algo' => 0,
			'algoName' => 'unknown',
			'options' => array()
		);

		// invalid length
		$this->assertEquals($expected, password_get_info('$2y$11$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100'));

		// non-bcrypt
		$this->assertEquals($expected, password_get_info('$1$rasmusle$rISCgZzpwk3UhDidwXvin0'));
	}

	// ------------------------------------------------------------------------

	/**
	 * password_hash() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_password_hash()
	{
		// FALSE is returned if no CSPRNG source is available
		if ( ! defined('MCRYPT_DEV_URANDOM') && ! function_exists('openssl_random_pseudo_bytes')
			&& (DIRECTORY_SEPARATOR !== '/' OR ! is_readable('/dev/arandom') OR ! is_readable('/dev/urandom'))
			)
		{
			$this->assertFalse(password_hash('foo', PASSWORD_BCRYPT));
		}
		else
		{
			$this->assertEquals(60, strlen(password_hash('foo', PASSWORD_BCRYPT)));
			$this->assertTrue(($hash = password_hash('foo', PASSWORD_BCRYPT)) === crypt('foo', $hash));
		}

		$this->assertEquals(
			'$2y$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi',
			password_hash('rasmuslerdorf', PASSWORD_BCRYPT, array('cost' => 7, 'salt' => 'usesomesillystringforsalt'))
		);

		$this->assertEquals(
			'$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y',
			password_hash('test', PASSWORD_BCRYPT, array('salt' => '123456789012345678901'.chr(0)))
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * password_needs_rehash() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_password_get_info
	 */
	public function test_password_needs_rehash()
	{
		// invalid hash: always rehash
		$this->assertTrue(password_needs_rehash('', PASSWORD_BCRYPT));

		// valid, because it's an unknown algorithm
		$this->assertFalse(password_needs_rehash('', 0));

		// valid with same cost
		$this->assertFalse(password_needs_rehash('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT, array('cost' => 10)));

		// valid with same cost and additional parameters
		$this->assertFalse(password_needs_rehash('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT, array('cost' => 10, 'foo' => 3)));

		// invalid: different (lower) cost
		$this->assertTrue(password_needs_rehash('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT, array('cost' => 9)));

		// invalid: different (higher) cost
		$this->assertTrue(password_needs_rehash('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT, array('cost' => 11)));

		// valid with default cost
		$this->assertFalse(password_needs_rehash('$2y$'.str_pad(10, 2, '0', STR_PAD_LEFT).'$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT));

		// invalid: 'foo' is cast to 0
		$this->assertTrue(password_needs_rehash('$2y$10$MTIzNDU2Nzg5MDEyMzQ1Nej0NmcAWSLR.oP7XOR9HD/vjUuOj100y', PASSWORD_BCRYPT, array('cost' => 'foo')));
	}

	// ------------------------------------------------------------------------

	/**
	 * password_verify() test
	 *
	 * Borrowed from PHP's own tests
	 *
	 * @depends	test_bootstrap
	 */
	public function test_password_verify()
	{
		$this->assertFalse(password_verify(123, 123));
		$this->assertFalse(password_verify('foo', '$2a$07$usesomesillystringforsalt$'));
		$this->assertFalse(password_verify('rasmusler', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
		$this->assertTrue(password_verify('rasmuslerdorf', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
	}

}