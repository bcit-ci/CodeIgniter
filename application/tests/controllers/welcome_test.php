<?php

class WelcomeTest extends CI_Controller_TestCase {
	
	public function testIndex()
	{
		$this->dispatch(array('controller' => 'welcome', 'function' => 'index'));
		$this->expectOutputRegex('/<h1>Welcome to CodeIgniter!<\/h1>/');
	}
	
}
