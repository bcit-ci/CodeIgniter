<?php

class Output_test extends CI_TestCase {

	public $output;
	protected $_output_data = '';

	public function set_up()
	{
		$this->_output_data =<<<HTML
		<html>
			<head>
				<title>Basic HTML</title>
			</head>
			<body>
				Test
			</body>
		</html>
HTML;
		$this->ci_set_config('charset', 'UTF-8');
		$output = $this->ci_core_class('output');
		$this->output = new $output();
	}

	// --------------------------------------------------------------------

	public function test_set_get_append_output()
	{
		$append = "<!-- comment /-->\n";

		$this->assertEquals(
			$this->_output_data.$append,
			$this->output
				->set_output($this->_output_data)
				->append_output("<!-- comment /-->\n")
				->get_output()
		);
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
