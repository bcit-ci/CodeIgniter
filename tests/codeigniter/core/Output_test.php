<?php

class Output_test extends CI_TestCase {

	public $output;

	public function set_up()
	{
		$this->ci_set_config('charset', 'UTF-8');
		$output = $this->ci_core_class('output');
		$this->output = new $output();
	}

	// --------------------------------------------------------------------

	public function test_get_content_type()
	{
		$this->assertEquals('text/html', $this->output->get_content_type());
	}

	// --------------------------------------------------------------------

	public function test_get_header()
	{
		$this->assertNull($this->output->get_header('Non-Existent-Header'));

		// TODO: Find a way to test header() values as well. Currently,
		//	 PHPUnit prevents this by not using output buffering.

		$this->output->set_content_type('text/plain', 'WINDOWS-1251');
		$this->assertEquals(
			'text/plain; charset=WINDOWS-1251',
			$this->output->get_header('content-type')
		);
	}

}