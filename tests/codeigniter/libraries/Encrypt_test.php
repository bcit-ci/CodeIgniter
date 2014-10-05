<?php
/**
 * @requires extension mcrypt
 */
class Encrypt_test extends CI_TestCase {

	public function set_up()
	{
		if ( ! extension_loaded('mcrypt'))
		{
			return;
		}

		$this->encrypt = new Mock_Libraries_Encrypt();
		$this->ci_instance_var('encrypt', $this->encrypt);

		$this->ci_set_config('encryption_key', "Encryptin'glike@boss!");
		$this->msg = 'My secret message';
	}

	// --------------------------------------------------------------------

	public function test_encode()
	{
		$this->assertNotEquals($this->msg, $this->encrypt->encode($this->msg));
	}

	// --------------------------------------------------------------------

	public function test_decode()
	{
		$encoded_msg = $this->encrypt->encode($this->msg);
		$this->assertEquals($this->msg, $this->encrypt->decode($encoded_msg));
	}

	// --------------------------------------------------------------------

	public function test_optional_key()
	{
		$key = 'Ohai!ù0129°03182%HD1892P0';
		$encoded_msg = $this->encrypt->encode($this->msg, $key);
		$this->assertEquals($this->msg, $this->encrypt->decode($encoded_msg, $key));
	}

	// --------------------------------------------------------------------

	public function test_default_cipher()
	{
		$this->assertEquals('rijndael-256', $this->encrypt->get_cipher());
	}

	// --------------------------------------------------------------------

	public function test_set_cipher()
	{
		$this->encrypt->set_cipher(MCRYPT_BLOWFISH);
		$this->assertEquals('blowfish', $this->encrypt->get_cipher());
	}

	// --------------------------------------------------------------------

	public function test_default_mode()
	{
		$this->assertEquals('cbc', $this->encrypt->get_mode());
	}

	// --------------------------------------------------------------------

	public function test_set_mode()
	{
		$this->encrypt->set_mode(MCRYPT_MODE_CFB);
		$this->assertEquals('cfb', $this->encrypt->get_mode());
	}

}