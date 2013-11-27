<?php

class View_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('view');
	}

	// ------------------------------------------------------------------------

	public function test_element_with_existing_item()
	{
		$CI =& get_instance();
		$CI->config->set_item('application_name', 'My Application');

		$this->assertEquals('My Application | Home', title('Home'));
		$this->assertEquals('My Application - Home', title('Home','-'));
		$this->assertEquals('My Application', title());

		$CI->config->set_item('application_name', '');
		$this->assertEquals('', title());
		$this->assertEquals('Home', title('Home'));

	}

}