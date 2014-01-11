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
<<<<<<< develop
=======
		$ds = DIRECTORY_SEPARATOR;

>>>>>>> local
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

<<<<<<< develop
		// test default recursive behavior
		$expected = array(
			'libraries/' => array(
				'benchmark.html',
				'database/' => array('active_record.html', 'binds.html'),
=======
		// is_dir(), opendir(), etc. seem to fail on Windows + vfsStream when there are trailing backslashes in directory names
		if ( ! is_dir(vfsStream::url('testDir').DIRECTORY_SEPARATOR))
		{
			$this->markTestSkipped();
			return;
		}

		// test default recursive behavior
		$expected = array(
			'libraries'.$ds => array(
				'benchmark.html',
				'database'.$ds => array('active_record.html', 'binds.html'),
>>>>>>> local
				'email.html',
				'0'
			)
		);

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir')));

		// test detection of hidden files
<<<<<<< develop
		$expected['libraries/'][] = '.hiddenfile.txt';

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir'), FALSE, TRUE));

		// test recursion depth behavior
		$this->assertEquals(array('libraries/'), directory_map(vfsStream::url('testDir'), 1));
=======
		$expected['libraries'.$ds][] = '.hiddenfile.txt';

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir'), 0, TRUE));

		// test recursion depth behavior
		$this->assertEquals(array('libraries'.$ds), directory_map(vfsStream::url('testDir'), 1));
>>>>>>> local
	}

}

/* End of file directory_helper_test.php */