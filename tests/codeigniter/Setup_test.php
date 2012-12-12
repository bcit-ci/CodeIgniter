<?php

class Setup_test extends PHPUnit_Framework_TestCase {

	public function test_bootstrap_constants()
	{
		$this->assertTrue(defined('PROJECT_BASE'));
		$this->assertTrue(defined('BASEPATH'));
		$this->assertTrue(defined('APPPATH'));
		$this->assertTrue(defined('VIEWPATH'));
	}

}