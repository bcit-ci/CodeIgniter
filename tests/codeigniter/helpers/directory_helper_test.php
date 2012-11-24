<?php

class Directory_helper_test extends CI_TestCase {

	public function set_up()
	{
		$this->helper('directory');

		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));

		$this->_test_dir = vfsStreamWrapper::getRoot();
	}

	public function test_directory_map()
	{
		$structure = array(
			'libraries' => array(
				'benchmark.html' => '',
				'database' => array('active_record.html' => '', 'binds.html' => ''),
				'email.html' => '',
				'0' => '',
				'.hiddenfile.txt' => ''
			)
		);

		vfsStream::create($structure, $this->_test_dir);

		// test default recursive behavior
		$expected = array(
			'libraries/' => array(
				'benchmark.html',
				'database/' => array('active_record.html', 'binds.html'),
				'email.html',
				'0'
			)
		);

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir')));

		// test detection of hidden files
		$expected['libraries/'][] = '.hiddenfile.txt';

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir'), FALSE, TRUE));

		// test recursion depth behavior
		$this->assertEquals(array('libraries/'), directory_map(vfsStream::url('testDir'), 1));
	}

}

/* End of file directory_helper_test.php */