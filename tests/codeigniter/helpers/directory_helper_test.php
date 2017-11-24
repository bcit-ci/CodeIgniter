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
		$ds = DIRECTORY_SEPARATOR;

		$structure = [
			'libraries' => [
				'benchmark.html' => '',
				'database' => ['active_record.html' => '', 'binds.html' => ''],
				'email.html' => '',
				'0' => '',
				'.hiddenfile.txt' => '',
			],
		];

		vfsStream::create($structure, $this->_test_dir);

		// is_dir(), opendir(), etc. seem to fail on Windows + vfsStream when there are trailing backslashes in directory names
		if ( ! is_dir(vfsStream::url('testDir').DIRECTORY_SEPARATOR))
		{
			$this->markTestSkipped("Can't test this under Windows");
			return;
		}

		// test default recursive behavior
		$expected = [
			'libraries'.$ds => [
				'benchmark.html',
				'database'.$ds => ['active_record.html', 'binds.html'],
				'email.html',
				'0',
			],
		];

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir')));

		// test detection of hidden files
		$expected['libraries'.$ds][] = '.hiddenfile.txt';

		$this->assertEquals($expected, directory_map(vfsStream::url('testDir'), 0, TRUE));

		// test recursion depth behavior
		$this->assertEquals(['libraries'.$ds], directory_map(vfsStream::url('testDir'), 1));
	}

}
