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

	public function test_xss_clean()
	{
		$harm_string = "Hello, i try to <script>alert('Hack');</script> your site";

		$harmless_string = $this->security->xss_clean($harm_string);

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_string);
	}

        // --------------------------------------------------------------------

	public function test_xss_clean_string_array()
	{
		$harm_strings = array(
			"Hello, i try to <script>alert('Hack');</script> your site",
			"Simple clean string",
			"Hello, i try to <script>alert('Hack');</script> your site"
		);

		$harmless_strings = $this->security->xss_clean($harm_strings);

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_strings[0]);
		$this->assertEquals("Simple clean string", $harmless_strings[1]);
		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_strings[2]);
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_image_valid()
	{
		$harm_string = '<img src="test.png">';

		$xss_clean_return = $this->security->xss_clean($harm_string, TRUE);

		$this->assertTrue($xss_clean_return);
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_image_invalid()
	{
		$harm_string = '<img src=javascript:alert(String.fromCharCode(88,83,83))>';

		$xss_clean_return = $this->security->xss_clean($harm_string, TRUE);

		$this->assertFalse($xss_clean_return);
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_entity_double_encoded()
	{
		$input = '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>';
		$this->assertEquals('<a >Clickhere</a>', $this->security->xss_clean($input));
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_js_img_removal()
	{
		$input = '<img src="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere';
		$this->assertEquals('<img >', $this->security->xss_clean($input));
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_sanitize_naughty_html()
	{
		$input = '<blink>';
		$this->assertEquals('&lt;blink&gt;', $this->security->xss_clean($input));
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

	public function test_xss_hash()
	{
		$this->assertEmpty($this->security->xss_hash);

		// Perform hash
		$this->security->xss_hash();

		$this->assertTrue(preg_match('#^[0-9a-f]{32}$#iS', $this->security->xss_hash) === 1);
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
