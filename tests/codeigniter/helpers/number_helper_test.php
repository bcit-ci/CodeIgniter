<?php

class Number_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('number');

		// Grab the core lang class
		$lang_cls = $this->ci_core_class('lang');

		// Mock away load, too much going on in there,
		// we'll just check for the expected parameter
		$lang = $this->getMock($lang_cls, array('load'));
		$lang->expects($this->once())
			 ->method('load')
			 ->with($this->equalTo('number'));

		// Assign the proper language array
		$lang->language = $this->lang('number');

		// We don't have a controller, so just create
		// a cheap class to act as our super object.
		// Make sure it has a lang attribute.
		$this->ci_instance_var('lang', $lang);
	}

	public function test_byte_format()
	{
		$this->assertEquals('456 Bytes', byte_format(456));
	}

	public function test_kb_format()
	{
		$this->assertEquals('4.5 KB', byte_format(4567));
	}

	public function test_kb_format_medium()
	{
		$this->assertEquals('44.6 KB', byte_format(45678));
	}

	public function test_kb_format_large()
	{
		$this->assertEquals('446.1 KB', byte_format(456789));
	}

	public function test_mb_format()
	{
		$this->assertEquals('3.3 MB', byte_format(3456789));
	}

	public function test_gb_format()
	{
		$this->assertEquals('1.8 GB', byte_format(1932735283.2));
	}

	public function test_tb_format()
	{
		$this->assertEquals('112,283.3 TB', byte_format(123456789123456789));
	}

}