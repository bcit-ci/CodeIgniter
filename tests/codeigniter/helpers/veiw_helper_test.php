<?php

class View_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('view');
	}

	// ------------------------------------------------------------------------

	public function test_title()
	{
		$application_name = 'My Application';
		$this->ci_set_config('application_name', $application_name);

		$this->assertEquals('My Application | Home', title('Home'));
		$this->assertEquals('My Application - Home', title('Home','-'));
		$this->assertEquals('My Application', title());

		$this->ci_set_config('application_name', '');
		$this->assertEquals('', title());
		$this->assertEquals('Home', title('Home'));

	}

}