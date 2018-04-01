<?php

class Email_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('email');
	}

	public function test_valid_email()
	{
		$this->assertEquals(FALSE, valid_email('test'));
		$this->assertEquals(FALSE, valid_email('test@test@test.com'));
		$this->assertEquals(TRUE, valid_email('test@test.com'));
		$this->assertEquals(TRUE, valid_email('my.test@test.com'));
		$this->assertEquals(TRUE, valid_email('my.test@subdomain.test.com'));
	}

	public function test_send_mail()
	{
		$this->markTestSkipped("Can't test");
	}

}