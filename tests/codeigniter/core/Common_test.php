<?php

require_once(BASEPATH.'helpers/email_helper.php');

class Common_test extends CI_TestCase
{
	
	// ------------------------------------------------------------------------
	
	public function test_is_php()
	{
		$this->assertEquals(TRUE, is_php('1.2.0'));
		$this->assertEquals(FALSE, is_php('9999.9.9'));
	}
	
}