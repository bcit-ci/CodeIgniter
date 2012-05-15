<?php

class Security_test extends CI_TestCase {
	
	public function set_up()
	{
		$this->ci_set_config('csrf_protection', TRUE);
		$this->ci_set_config('csrf_token_name', 'ci_csrf_token');
		// @see : ./Bootstrap.php Line 16
		$this->ci_set_config('csrf_cookie_name', 'ci_csrf_cookie');
		$this->ci_set_config('csrf_expire', 7200);
		$this->ci_set_config('csrf_regenerate', TRUE);
		$this->ci_set_config('csrf_exclude_uris', array());

		$this->ci_set_config('cookie_prefix', "");
		$this->ci_set_config('cookie_domain', "");
		$this->ci_set_config('cookie_path', "/");
		$this->ci_set_config('cookie_secure', FALSE);
		$this->ci_set_config('cookie_httponly',	FALSE);

		$this->security = new Mock_Core_Security();
	}
	
	// --------------------------------------------------------------------
	
	public function test_csrf_verify()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertInstanceOf('CI_Security', $this->security->csrf_verify());
	}

	// --------------------------------------------------------------------
	
	public function test_csrf_verify_invalid()
	{
		// Without issuing $_POST[csrf_token_name], this request will triggering CSRF error
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->setExpectedException('RuntimeException', 'CI Error: The action you have requested is not allowed');

		$this->security->csrf_verify();
	}

	// --------------------------------------------------------------------
	
	public function test_csrf_verify_valid()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST[$this->security->csrf_token_name] = $this->security->csrf_hash;

		$this->assertInstanceOf('CI_Security', $this->security->csrf_verify());
	}

	// --------------------------------------------------------------------
	
	public function test_get_csrf_hash()
	{
		$this->assertEquals($this->security->csrf_hash, $this->security->get_csrf_hash());
	}

	// --------------------------------------------------------------------
	
	public function test_get_csrf_token_name()
	{
		$this->assertEquals('ci_csrf_token', $this->security->get_csrf_token_name());
	}

	// --------------------------------------------------------------------
	
	public function test_xss_clean()
	{
		$harm_string = "Hello, i try to <script>alert('Hack');</script> your site";

		$harmless_string = $this->security->xss_clean($harm_string);

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_string);
	}
}