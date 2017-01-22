<?php

class Cookie_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('cookie');
	}

	// ------------------------------------------------------------------------

	function test_set_cookie()
	{
		/*$input_cls = $this->ci_core_class('input');
		$this->ci_instance_var('input', new $input_cls);

		$this->assertTrue(set_cookie(
			'my_cookie',
			'foobar'
		));*/

		$this->markTestSkipped('Need to find a way to overcome a headers already set exception');
	}

	// ------------------------------------------------------------------------

	function test_get_cookie()
	{
		$_COOKIE['foo'] = 'bar';

		$security = new Mock_Core_Security('UTF-8');
		$input_cls = $this->ci_core_class('input');
		$this->ci_instance_var('input', new CI_Input($security));

		$this->assertEquals('bar', get_cookie('foo', FALSE));
		$this->assertEquals('bar', get_cookie('foo', TRUE));

		$_COOKIE['bar'] = "Hello, i try to <script>alert('Hack');</script> your site";

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", get_cookie('bar', TRUE));
		$this->assertEquals("Hello, i try to <script>alert('Hack');</script> your site", get_cookie('bar', FALSE));
	}

	// ------------------------------------------------------------------------

	function test_delete_cookie()
	{
		/*$input_cls = $this->ci_core_class('input');
		$this->ci_instance_var('input', new $input_cls);

		$this->assertTrue(delete_cookie(
			'my_cookie'
		));*/

		$this->markTestSkipped('Need to find a way to overcome a headers already set exception');
	}

}
