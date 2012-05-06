<?php

class Welcome_test extends CI_Selenium_TestCase {

	public function set_up()
	{
		$this->setBrowser('firefox');
		
        // Change below url to your servername, eg : $this->url('http://codeigniter.dev/');
        $this->setBrowserUrl('http://127.0.0.1/codeigniter/index.php');
        $this->setBrowserUrl('http://codeigniter.dev/');
	}
	
	// ------------------------------------------------------------------------
	
	public function test_title()
	{
        // Change below url to your servername, eg : $this->url('http://codeigniter.dev/');
		$this->url('http://127.0.0.1/codeigniter/index.php');

		$this->assertEquals('Welcome to CodeIgniter', $this->title());
	}

	// ------------------------------------------------------------------------
	
	public function test_body()
	{
        // Change below url to your servername, eg : $this->url('http://codeigniter.dev/');
		$this->url('http://127.0.0.1/codeigniter/index.php');

		$expected_body = "The page you are looking at is being generated dynamically by CodeIgniter.
If you would like to edit this page you'll find it located at:
application/views/welcome_message.php
The corresponding controller for this page is found at:
application/controllers/welcome.php
If you are exploring CodeIgniter for the very first time, you should start by reading the User Guide.";
		
		$body = $this->byId('body');

		$this->assertEquals($expected_body, $body->text());
	}

	// ------------------------------------------------------------------------
	
	public function test_footer()
	{
        // Change below url to your servername, eg : $this->url('http://codeigniter.dev/');
		$this->url('http://127.0.0.1/codeigniter/index.php');

		$footer_pattern = "/^Page rendered in (.+) seconds. CodeIgniter Version (.+)$/";
		
		$footer = $this->byClassName('footer');

		$this->assertEquals(1, preg_match($footer_pattern, $footer->text()));
	}
	
}