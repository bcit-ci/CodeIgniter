<?php

class Password_generator_test extends CI_TestCase 
{
	public function set_up()
	{
		$this->pwgen = new CI_Password_generator();
		$CI =& get_instance();
		$CI->security = new Mock_Core_Security();
	}
	
	public function test_generate()
	{
		$string = $this->pwgen->create_password(20, array('upper' => TRUE));
		// This should be 20 characters long
		$this->assertEquals(strlen($string), 20);
		
		// This should only be uppercase characters
		$this->assertTrue(1 === preg_match('#^[A-Z]{20}$#', $string));
	}
	
	public function test_random_string()
	{
		$string = $this->pwgen->get_random_string();
		$this->assertEquals(strlen($string), 16);
	}

	public function test_random_number()
	{
		$this->markTestSkipped(
			'This test can fail randomly; only enable it when needed.'
		);

		// Populate:
		$buffer = array_fill(0, 10, 0);

		// Perform the experiment:
		for ($i = 0; $i < 500; ++$i) {
			// Increase a random index by 1
			$j = $this->pwgen->get_random_number(0, 9);
			++$buffer[$j];
		}

		// Analyze the results:
		for ($i = 0; $i < 10; ++$i) {
			// If any of these are 0, then our RNG is failing us
			$this->assertNotEquals($buffer[$i], 0);
		}
    }
}
