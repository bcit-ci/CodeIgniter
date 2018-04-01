<?php

class Path_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('path');
	}

	public function test_set_realpath()
	{
		$this->assertEquals(getcwd().DIRECTORY_SEPARATOR, set_realpath(getcwd()));
	}

	public function test_set_realpath_nonexistent_directory()
	{
		$expected = '/path/to/nowhere';
		$this->assertEquals($expected, set_realpath('/path/to/nowhere', FALSE));
	}

	public function test_set_realpath_error_trigger()
	{
		$this->setExpectedException(
				'RuntimeException', 'CI Error: Not a valid path: /path/to/nowhere'
		);

		set_realpath('/path/to/nowhere', TRUE);
	}

}
