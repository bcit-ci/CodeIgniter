<?php

class Security_helper_tests extends CI_TestCase {

	function setUp()
	{
		$this->helper('security');
		$obj = new stdClass;
		$obj->security = new Mock_Core_Security('UTF-8');
		$this->ci_instance($obj);
	}

	function test_xss_clean()
	{
		$this->assertEquals('foo', xss_clean('foo'));

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", xss_clean("Hello, i try to <script>alert('Hack');</script> your site"));
	}

	function test_sanitize_filename()
	{
		$this->assertEquals('hello.doc', sanitize_filename('hello.doc'));

		$filename = './<!--foo-->';
		$this->assertEquals('foo', sanitize_filename($filename));
	}

	function test_strip_image_tags()
	{
		$this->assertEquals('http://example.com/spacer.gif', strip_image_tags('http://example.com/spacer.gif'));

		$this->assertEquals('http://example.com/spacer.gif', strip_image_tags('<img src="http://example.com/spacer.gif" alt="Who needs CSS when you have a spacer.gif?" />'));
	}

	function test_encode_php_tags()
	{
		$this->assertEquals('&lt;? echo $foo; ?&gt;', encode_php_tags('<? echo $foo; ?>'));
	}

}
