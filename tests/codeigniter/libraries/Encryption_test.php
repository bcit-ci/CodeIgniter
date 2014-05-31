<?php

class Encryption_test extends CI_TestCase {

	public function set_up()
	{
		$this->encryption = new Mock_Libraries_Encryption();
	}

	// --------------------------------------------------------------------

	/**
	 * __construct test
	 *
	 * Covers behavior with $config['encryption_key'] set or not
	 */
	public function test___construct()
	{
		// Assume no configuration from set_up()
		$this->assertNull($this->encryption->get_key());

		// Try with an empty value
		$this->ci_set_config('encryption_key');
		$this->encrypt = new Mock_Libraries_Encryption();
		$this->assertNull($this->encrypt->get_key());

		$this->ci_set_config('encryption_key', str_repeat("\x0", 16));
		$this->encrypt = new Mock_Libraries_Encryption();
		$this->assertEquals(str_repeat("\x0", 16), $this->encrypt->get_key());
	}

	// --------------------------------------------------------------------

	/**
	 * hkdf() test
	 *
	 * Applies test vectors described in Appendix A(1-3) RFC5869.
	 * Described vectors 4-7 SHA-1, which we don't support and are
	 * therefore excluded.
	 *
	 * Because our implementation is a single method instead of being
	 * split into hkdf_extract() and hkdf_expand(), we cannot test for
	 * the PRK value. As long as the OKM is correct though, it's fine.
	 *
	 * @link	https://tools.ietf.org/rfc/rfc5869.txt
	 */
	public function test_hkdf()
	{
		$vectors = array(
			// A.1: Basic test case with SHA-256
			array(
				'digest' => 'sha256',
				'ikm' => "\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b",
				'salt' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c",
				'length' => 42,
				'info' => "\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9",
			//	'prk' => "\x07\x77\x09\x36\x2c\x2e\x32\xdf\x0d\xdc\x3f\x0d\xc4\x7b\xba\x63\x90\xb6\xc7\x3b\xb5\x0f\x9c\x31\x22\xec\x84\x4a\xd7\xc2\xb3\xe5",
				'okm' => "\x3c\xb2\x5f\x25\xfa\xac\xd5\x7a\x90\x43\x4f\x64\xd0\x36\x2f\x2a\x2d\x2d\x0a\x90\xcf\x1a\x5a\x4c\x5d\xb0\x2d\x56\xec\xc4\xc5\xbf\x34\x00\x72\x08\xd5\xb8\x87\x18\x58\x65"
			),
			// A.2: Test with SHA-256 and longer inputs/outputs
			array(
				'digest' => 'sha256',
				'ikm' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2a\x2b\x2c\x2d\x2e\x2f\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3a\x3b\x3c\x3d\x3e\x3f\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f",
				'salt' => "\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x7b\x7c\x7d\x7e\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf",
				'length' => 82,
				'info' => "\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff",
			//	'prk' => "\x06\xa6\xb8\x8c\x58\x53\x36\x1a\x06\x10\x4c\x9c\xeb\x35\xb4\x5c\xef\x76\x00\x14\x90\x46\x71\x01\x4a\x19\x3f\x40\xc1\x5f\xc2\x44",
				'okm' => "\xb1\x1e\x39\x8d\xc8\x03\x27\xa1\xc8\xe7\xf7\x8c\x59\x6a\x49\x34\x4f\x01\x2e\xda\x2d\x4e\xfa\xd8\xa0\x50\xcc\x4c\x19\xaf\xa9\x7c\x59\x04\x5a\x99\xca\xc7\x82\x72\x71\xcb\x41\xc6\x5e\x59\x0e\x09\xda\x32\x75\x60\x0c\x2f\x09\xb8\x36\x77\x93\xa9\xac\xa3\xdb\x71\xcc\x30\xc5\x81\x79\xec\x3e\x87\xc1\x4c\x01\xd5\xc1\xf3\x43\x4f\x1d\x87",
			),
			// A.3: Test with SHA-256 and zero-length salt/info
			array(
				'digest' => 'sha256',
				'ikm' => "\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b",
				'salt' => '',
				'length' => 42,
				'info' => '',
			//	'prk' => "\x19\xef\x24\xa3\x2c\x71\x7b\x16\x7f\x33\xa9\x1d\x6f\x64\x8b\xdf\x96\x59\x67\x76\xaf\xdb\x63\x77\xac\x43\x4c\x1c\x29\x3c\xcb\x04",
				'okm' => "\x8d\xa4\xe7\x75\xa5\x63\xc1\x8f\x71\x5f\x80\x2a\x06\x3c\x5a\x31\xb8\xa1\x1f\x5c\x5e\xe1\x87\x9e\xc3\x45\x4e\x5f\x3c\x73\x8d\x2d\x9d\x20\x13\x95\xfa\xa4\xb6\x1a\x96\xc8",
			)
		);

		foreach ($vectors as $test)
		{
			$this->assertEquals(
				$test['okm'],
				$this->encryption->hkdf(
					$test['ikm'],
					$test['digest'],
					$test['salt'],
					$test['length'],
					$test['info']
				)
			);
		}

		// Test default length, it must match the digest size
		$this->assertEquals(64, strlen($this->encryption->hkdf('foobar', 'sha512')));

		// Test maximum length (RFC5869 says that it must be up to 255 times the digest size)
		$this->assertEquals(12240, strlen($this->encryption->hkdf('foobar', 'sha384', NULL, 48 * 255)));
		$this->assertFalse($this->encryption->hkdf('foobar', 'sha224', NULL, 28 * 255 + 1));

		// CI-specific test for an invalid digest
		$this->assertFalse($this->encryption->hkdf('fobar', 'sha1'));
	}

	// --------------------------------------------------------------------

	/**
	 * _get_params() test
	 */
	public function test__get_params()
	{
		$key = str_repeat("\x0", 16);

		// Invalid custom parameters
		$params = array(
			// No cipher, mode or key
			array('cipher' => 'aes-128', 'mode' => 'cbc'),
			array('cipher' => 'aes-128', 'key' => $key),
			array('mode' => 'cbc', 'key' => $key),
			// No HMAC key or not a valid digest
			array('cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key),
			array('cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $key, 'hmac_digest' => 'sha1', 'hmac_key' => $key),
			// Invalid mode
			array('cipher' => 'aes-128', 'mode' => 'foo', 'key' => $key, 'hmac_digest' => 'sha256', 'hmac_key' => $key)
		);

		for ($i = 0, $c = count($params); $i < $c; $i++)
		{
			$this->assertFalse($this->encryption->__get_params($params[$i]));
		}

		// Valid parameters
		$params = array(
			'cipher' => 'aes-128',
			'mode' => 'cbc',
			'key' => str_repeat("\x0", 16),
			'hmac_key' => str_repeat("\x0", 16)
		);

		$this->assertTrue(is_array($this->encryption->__get_params($params)));

		$params['iv'] = NULL;
		$params['base64'] = TRUE;
		$params['hmac_digest'] = 'sha512';

		// Including all parameters
		$params = array(
			'cipher' => 'aes-128',
			'mode' => 'cbc',
			'key' => str_repeat("\x0", 16),
			'iv' => str_repeat("\x0", 16),
			'raw_data' => TRUE,
			'hmac_key' => str_repeat("\x0", 16),
			'hmac_digest' => 'sha256'
		);

		$output = $this->encryption->__get_params($params);
		unset($output['handle'], $params['raw_data']);
		$params['base64'] = FALSE;
		$this->assertEquals($params, $output);

		// HMAC disabled
		unset($params['hmac_key'], $params['hmac_digest']);
		$params['hmac'] = $params['raw_data'] = FALSE;
		$output = $this->encryption->__get_params($params);
		unset($output['handle'], $params['hmac'], $params['raw_data']);
		$params['base64'] = TRUE;
		$params['hmac_digest'] = $params['hmac_key'] = NULL;
		$this->assertEquals($params, $output);
	}

	// --------------------------------------------------------------------

	/**
	 * initialize(), encrypt(), decrypt() test
	 *
	 * Testing the three methods separately is not realistic as they are
	 * designed to work together. A more thorough test for initialize()
	 * though is the OpenSSL/MCrypt compatibility test.
	 *
	 * @depends	test_hkdf
	 * @depends	test__get_params
	 */
	public function test_initialize_encrypt_decrypt()
	{
		$message = 'This is a plain-text message.';
		$key = "\xd0\xc9\x08\xc4\xde\x52\x12\x6e\xf8\xcc\xdb\x03\xea\xa0\x3a\x5c";

		// Default state (AES-128/Rijndael-128 in CBC mode)
		$this->encryption->initialize(array('key' => $key));

		// Was the key properly set?
		$this->assertEquals($key, $this->encryption->get_key());

		$this->assertEquals($message, $this->encryption->decrypt($this->encryption->encrypt($message)));

		// Try DES in ECB mode, just for the sake of changing stuff
		$this->encryption->initialize(array('cipher' => 'des', 'mode' => 'ecb'));
		$this->assertEquals($message, $this->encryption->decrypt($this->encryption->encrypt($message)));
	}

	// --------------------------------------------------------------------

	/**
	 * encrypt(), decrypt test with custom parameters
	 *
	 * @depends	test___get_params
	 */
	public function test_encrypt_decrypt_custom()
	{
		$message = 'Another plain-text message.';

		// A random invalid parameter
		$this->assertFalse($this->encryption->encrypt($message, array('foo')));
		$this->assertFalse($this->encryption->decrypt($message, array('foo')));

		// Custom IV (we'll check it), no HMAC, binary output
		$params = array(
			'cipher' => 'tripledes',
			'mode' => 'cfb',
			'key' => str_repeat("\x1", 16),
			'iv' => str_repeat("\x2", 8),
			'base64' => FALSE,
			'hmac' => FALSE
		);

		$ciphertext = $this->encryption->encrypt($message, $params);
		$this->assertEquals(0, strncmp($params['iv'], $ciphertext, 8));

		// IV should be found in the cipher-text, no matter if it was supplied or not
		$this->assertEquals($message, $this->encryption->decrypt($ciphertext, $params));
		unset($params['iv']);
		$this->assertEquals($message, $this->encryption->decrypt($ciphertext, $params));
	}

	// --------------------------------------------------------------------

	/**
	 * _mcrypt_get_handle() test
	 */
	public function test__mcrypt_get_handle()
	{
		if ($this->encryption->drivers['mcrypt'] === FALSE)
		{
			return $this->markTestSkipped('Cannot test MCrypt because it is not available.');
		}

		$this->assertTrue(is_resource($this->encryption->__driver_get_handle('mcrypt', 'rijndael-128', 'cbc')));
	}

	// --------------------------------------------------------------------

	/**
	 * _openssl_get_handle() test
	 */
	public function test__openssl_mcrypt_get_handle()
	{
		if ($this->encryption->drivers['openssl'] === FALSE)
		{
			return $this->markTestSkipped('Cannot test OpenSSL because it is not available.');
		}

		$this->assertEquals('aes-128-cbc', $this->encryption->__driver_get_handle('openssl', 'aes-128', 'cbc'));
		$this->assertEquals('rc4-40', $this->encryption->__driver_get_handle('openssl', 'rc4-40', 'stream'));
	}

	// --------------------------------------------------------------------

	/**
	 * OpenSSL/MCrypt portability test
	 *
	 * Amongst the obvious stuff, _cipher_alias() is also tested here.
	 */
	public function test_portability()
	{
		if ( ! $this->encryption->drivers['mcrypt'] OR ! $this->encryption->drivers['openssl'])
		{
			$this->markTestSkipped('Both MCrypt and OpenSSL support are required for portability tests.');
			return;
		}

		$message = 'This is a message encrypted via MCrypt and decrypted via OpenSSL, or vice-versa.';

		// Format is: <Cipher name>, <Cipher mode>, <Key size>
		$portable = array(
			array('aes-128', 'cbc', 16),
			array('aes-128', 'cfb', 16),
			array('aes-128', 'cfb8', 16),
			array('aes-128', 'ofb', 16),
			array('aes-128', 'ecb', 16),
			array('aes-128', 'ctr', 16),
			array('aes-192', 'cbc', 24),
			array('aes-192', 'cfb', 24),
			array('aes-192', 'cfb8', 24),
			array('aes-192', 'ofb', 24),
			array('aes-192', 'ecb', 24),
			array('aes-192', 'ctr', 24),
			array('aes-256', 'cbc', 32),
			array('aes-256', 'cfb', 32),
			array('aes-256', 'cfb8', 32),
			array('aes-256', 'ofb', 32),
			array('aes-256', 'ecb', 32),
			array('aes-256', 'ctr', 32),
			array('des', 'cbc', 7),
			array('des', 'cfb', 7),
			array('des', 'cfb8', 7),
			array('des', 'ofb', 7),
			array('des', 'ecb', 7),
			array('tripledes', 'cbc', 7),
			array('tripledes', 'cfb', 7),
			array('tripledes', 'cfb8', 7),
			array('tripledes', 'ofb', 7),
			array('tripledes', 'cbc', 14),
			array('tripledes', 'cfb', 14),
			array('tripledes', 'cfb8', 14),
			array('tripledes', 'ofb', 14),
			array('tripledes', 'cbc', 21),
			array('tripledes', 'cfb', 21),
			array('tripledes', 'cfb8', 21),
			array('tripledes', 'ofb', 21),
			array('blowfish', 'cbc', 16),
			array('blowfish', 'cfb', 16),
			array('blowfish', 'ofb', 16),
			array('blowfish', 'ecb', 16),
			array('blowfish', 'cbc', 56),
			array('blowfish', 'cfb', 56),
			array('blowfish', 'ofb', 56),
			array('blowfish', 'ecb', 56),
			array('cast5', 'cbc', 11),
			array('cast5', 'cfb', 11),
			array('cast5', 'ofb', 11),
			array('cast5', 'ecb', 11),
			array('cast5', 'cbc', 16),
			array('cast5', 'cfb', 16),
			array('cast5', 'ofb', 16),
			array('cast5', 'ecb', 16),
			array('rc4', 'stream', 5),
			array('rc4', 'stream', 8),
			array('rc4', 'stream', 16),
			array('rc4', 'stream', 32),
			array('rc4', 'stream', 64),
			array('rc4', 'stream', 128),
			array('rc4', 'stream', 256)
		);
		$driver_index = array('mcrypt', 'openssl');

		foreach ($portable as &$test)
		{
			// Add some randomness to the selected driver
			$driver = mt_rand(0,1);
			$params = array(
				'driver' => $driver_index[$driver],
				'cipher' => $test[0],
				'mode' => $test[1],
				'key' => openssl_random_pseudo_bytes($test[2])
			);

			$this->encryption->initialize($params);
			$ciphertext = $this->encryption->encrypt($message);

			$driver = (int) ! $driver;
			$params['driver'] = $driver_index[$driver];

			$this->encryption->initialize($params);
			$this->assertEquals($message, $this->encryption->decrypt($ciphertext));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __get() test
	 */
	public function test_magic_get()
	{
		$this->assertNull($this->encryption->foo);
		$this->assertEquals(array('mcrypt', 'openssl'), array_keys($this->encryption->drivers));

		// 'stream' mode is translated into an empty string for OpenSSL
		$this->encryption->initialize(array('cipher' => 'rc4', 'mode' => 'stream'));
		$this->assertEquals('stream', $this->encryption->mode);
	}

}