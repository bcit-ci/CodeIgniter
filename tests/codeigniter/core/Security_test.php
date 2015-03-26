<?php

class Security_test extends CI_TestCase {

	public function set_up()
	{
		// Set cookie for security test
		$_COOKIE['ci_csrf_cookie'] = md5(uniqid(mt_rand(), TRUE));

		// Set config for Security class
		$this->ci_set_config('csrf_protection', TRUE);
		$this->ci_set_config('csrf_token_name', 'ci_csrf_token');
		$this->ci_set_config('csrf_cookie_name', 'ci_csrf_cookie');

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

	public function test_remove_evil_attributes()
	{
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttribute="bar">', FALSE));
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttributeNoQuotes=bar>', FALSE));
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttributeWithSpaces = bar>', FALSE));
		$this->assertEquals('<foo prefixOnAttribute="bar">', $this->security->remove_evil_attributes('<foo prefixOnAttribute="bar">', FALSE));
		$this->assertEquals('<foo>onOutsideOfTag=test</foo>', $this->security->remove_evil_attributes('<foo>onOutsideOfTag=test</foo>', FALSE));
		$this->assertEquals('onNoTagAtAll = true', $this->security->remove_evil_attributes('onNoTagAtAll = true', FALSE));
	}

	// --------------------------------------------------------------------

	public function test_get_random_bytes()
	{
		$length = "invalid";
		$this->assertFalse($this->security->get_random_bytes($length));

		$length = 10;
		$this->assertNotEmpty($this->security->get_random_bytes($length));
	}

	// --------------------------------------------------------------------

	public function test_entity_decode()
	{
		$encoded = '&lt;div&gt;Hello &lt;b&gt;Booya&lt;/b&gt;&lt;/div&gt;';
		$decoded = $this->security->entity_decode($encoded);

		$this->assertEquals('<div>Hello <b>Booya</b></div>', $decoded);

		// Issue #3057 (https://github.com/bcit-ci/CodeIgniter/issues/3057)
		$this->assertEquals(
			'&foo should not include a semicolon',
			$this->security->entity_decode('&foo should not include a semicolon')
		);
	}

	// --------------------------------------------------------------------

	public function test_sanitize_filename()
	{
		$filename = './<!--foo-->';
		$safe_filename = $this->security->sanitize_filename($filename);

		$this->assertEquals('foo', $safe_filename);
	}

	// --------------------------------------------------------------------

	public function test_strip_image_tags()
	{
		$imgtags = array(
			'<img src="smiley.gif" alt="Smiley face" height="42" width="42">',
			'<img alt="Smiley face" height="42" width="42" src="smiley.gif">',
			'<img src="http://www.w3schools.com/images/w3schools_green.jpg">',
			'<img src="/img/sunset.gif" height="100%" width="100%">',
			'<img src="mdn-logo-sm.png" alt="MD Logo" srcset="mdn-logo-HD.png 2x, mdn-logo-small.png 15w, mdn-banner-HD.png 100w 2x" />',
			'<img sqrc="/img/sunset.gif" height="100%" width="100%">',
			'<img srqc="/img/sunset.gif" height="100%" width="100%">',
			'<img srcq="/img/sunset.gif" height="100%" width="100%">'
		);

		$urls = array(
			'smiley.gif',
			'smiley.gif',
			'http://www.w3schools.com/images/w3schools_green.jpg',
			'/img/sunset.gif',
			'mdn-logo-sm.png',
			'<img sqrc="/img/sunset.gif" height="100%" width="100%">',
			'<img srqc="/img/sunset.gif" height="100%" width="100%">',
			'<img srcq="/img/sunset.gif" height="100%" width="100%">'
		);

		for ($i = 0; $i < count($imgtags); $i++)
		{
			$this->assertEquals($urls[$i], $this->security->strip_image_tags($imgtags[$i]));
		}
	}

	// --------------------------------------------------------------------

	public function test_csrf_set_hash()
	{
		// Set cookie for security test
		$_COOKIE['ci_csrf_cookie'] = md5(uniqid(mt_rand(), TRUE));

		// Set config for Security class
		$this->ci_set_config('csrf_protection', TRUE);
		$this->ci_set_config('csrf_token_name', 'ci_csrf_token');

		// leave csrf_cookie_name as blank to test _csrf_set_hash function
		$this->ci_set_config('csrf_cookie_name', '');

		$this->security = new Mock_Core_Security();

		$this->assertNotEmpty($this->security->get_csrf_hash());
	}
}
