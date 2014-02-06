<?php

class Encryption_test extends CI_TestCase {

	public function set_up()
	{
		$this->ci_set_config('encryption_key', "\xd0\xc9\x08\xc4\xde\x52\x12\x6e\xf8\xcc\xdb\x03\xea\xa0\x3a\x5c");
		$this->encryption = new CI_Encryption();
		$this->ci_instance_var('encryption', $this->encryption);
	}

	// --------------------------------------------------------------------

	public function test_portability()
	{
		if ( ! $this->encryption->drivers['mcrypt'] OR ! $this->encryption->drivers['openssl'])
		{
			$this->markTestAsSkipped('Both MCrypt and OpenSSL support are required for portability tests.');
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
			array('cast5', 'cbc', 5),
			array('cast5', 'cfb', 5),
			array('cast5', 'ofb', 5),
			array('cast5', 'ecb', 5),
			array('cast5', 'cbc', 8),
			array('cast5', 'cfb', 8),
			array('cast5', 'ofb', 8),
			array('cast5', 'ecb', 8),
			array('cast5', 'cbc', 10),
			array('cast5', 'cfb', 10),
			array('cast5', 'ofb', 10),
			array('cast5', 'ecb', 10),
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

	public function test_hkdf()
	{
		// Test vectors are described in RFC5869, Appendix A(1-3).
		// Vectors 4-7 cover SHA-1, which we don't support.
		//
		// URL: https://tools.ietf.org/rfc/rfc5869.txt
		//
		// Our implementation doesn't split into hkdf_extract(), hkdf_expand()
		// and therefore we can't test for the PRK value (it is included below
		// just for consistency, hence why it's also commented out).
		//
		// As long as OKM is correct, then we're all fine though.
		$vectors = array(
			// Appendix A.1: Basic test case with SHA-256
			array(
				'digest' => 'sha256',
				'ikm' => "\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b",
				'salt' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c",
				'length' => 42,
				'info' => "\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9",
//				'prk' => "\x07\x77\x09\x36\x2c\x2e\x32\xdf\x0d\xdc\x3f\x0d\xc4\x7b\xba\x63\x90\xb6\xc7\x3b\xb5\x0f\x9c\x31\x22\xec\x84\x4a\xd7\xc2\xb3\xe5",
				'okm' => "\x3c\xb2\x5f\x25\xfa\xac\xd5\x7a\x90\x43\x4f\x64\xd0\x36\x2f\x2a\x2d\x2d\x0a\x90\xcf\x1a\x5a\x4c\x5d\xb0\x2d\x56\xec\xc4\xc5\xbf\x34\x00\x72\x08\xd5\xb8\x87\x18\x58\x65"
			),
			// Appendix A.2: Test with SHA-256 and longer inputs/outputs
			array(
				'digest' => 'sha256',
				'ikm' => "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2a\x2b\x2c\x2d\x2e\x2f\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3a\x3b\x3c\x3d\x3e\x3f\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4a\x4b\x4c\x4d\x4e\x4f",
				'salt' => "\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6a\x6b\x6c\x6d\x6e\x6f\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7a\x7b\x7c\x7d\x7e\x7f\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf",
				'length' => 82,
				'info' => "\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff",
//				'prk' => "\x06\xa6\xb8\x8c\x58\x53\x36\x1a\x06\x10\x4c\x9c\xeb\x35\xb4\x5c\xef\x76\x00\x14\x90\x46\x71\x01\x4a\x19\x3f\x40\xc1\x5f\xc2\x44",
				'okm' => "\xb1\x1e\x39\x8d\xc8\x03\x27\xa1\xc8\xe7\xf7\x8c\x59\x6a\x49\x34\x4f\x01\x2e\xda\x2d\x4e\xfa\xd8\xa0\x50\xcc\x4c\x19\xaf\xa9\x7c\x59\x04\x5a\x99\xca\xc7\x82\x72\x71\xcb\x41\xc6\x5e\x59\x0e\x09\xda\x32\x75\x60\x0c\x2f\x09\xb8\x36\x77\x93\xa9\xac\xa3\xdb\x71\xcc\x30\xc5\x81\x79\xec\x3e\x87\xc1\x4c\x01\xd5\xc1\xf3\x43\x4f\x1d\x87",
			),
			// Appendix A.3: Test with SHA-256 and zero-length salt/info
			array(
				'digest' => 'sha256',
				'ikm' => "\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b\x0b",
				'salt' => "",
				'length' => 42,
				'info' => "",
//				'prk' => "\x19\xef\x24\xa3\x2c\x71\x7b\x16\x7f\x33\xa9\x1d\x6f\x64\x8b\xdf\x96\x59\x67\x76\xaf\xdb\x63\x77\xac\x43\x4c\x1c\x29\x3c\xcb\x04",
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
	}

}