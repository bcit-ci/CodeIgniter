<?php

class Upload_test extends CI_TestCase {

	function set_up()
	{
		$ci = $this->ci_instance();
		$ci->upload = new Mock_Libraries_Upload();
		$ci->security = new Mock_Core_Security();
		$ci->lang = $this->getMock('CI_Lang', array('load', 'line'));
		$ci->lang->expects($this->any())->method('line')->will($this->returnValue(FALSE));
		$this->upload = $ci->upload;
	}

	function test_do_upload()
	{
		$this->markTestSkipped('We can\'t really test this at the moment because of the call to `is_uploaded_file` in do_upload which isn\'t supported by vfsStream');
	}

	function test_data()
	{
		$data = array(
				'file_name'		=> 'hello.txt',
				'file_type'		=> 'text/plain',
				'file_path'		=> '/tmp/',
				'full_path'		=> '/tmp/hello.txt',
				'raw_name'		=> 'hello',
				'orig_name'		=> 'hello.txt',
				'client_name'		=> '',
				'file_ext'		=> '.txt',
				'file_size'		=> 100,
				'is_image'		=> FALSE,
				'image_width'		=> '',
				'image_height'		=> '',
				'image_type'		=> '',
				'image_size_str'	=> ''
			);

		$this->upload->set_upload_path('/tmp/');

		foreach ($data as $k => $v)
		{
			$this->upload->{$k}	= $v;
		}

		$this->assertEquals('hello.txt', $this->upload->data('file_name'));
		$this->assertEquals($data, $this->upload->data());
	}

	function test_set_upload_path()
	{
		$this->upload->set_upload_path('/tmp/');
		$this->assertEquals('/tmp/', $this->upload->upload_path);

		$this->upload->set_upload_path('/tmp');
		$this->assertEquals('/tmp/', $this->upload->upload_path);
	}

	function test_set_filename()
	{
		$dir = 'uploads';
		$isnew = 'helloworld.txt';
		$exists = 'hello-world.txt';
		$this->ci_vfs_create($exists, 'Hello world.', $this->ci_app_root, $dir);
		$path = $this->ci_vfs_path($dir.'/', APPPATH);
		$this->upload->file_ext = '.txt';

		$this->assertEquals($isnew, $this->upload->set_filename($path, $isnew));
		$this->assertEquals('hello-world1.txt', $this->upload->set_filename($path, $exists));
	}

	function test_set_max_filesize()
	{
		$this->upload->set_max_filesize(100);
		$this->assertEquals(100, $this->upload->max_size);
	}

	function test_set_max_filename()
	{
		$this->upload->set_max_filename(100);
		$this->assertEquals(100, $this->upload->max_filename);
	}

	function test_set_max_width()
	{
		$this->upload->set_max_width(100);
		$this->assertEquals(100, $this->upload->max_width);
	}

	function test_set_max_height()
	{
		$this->upload->set_max_height(100);
		$this->assertEquals(100, $this->upload->max_height);
	}

	function test_set_allowed_types()
	{
		$this->upload->set_allowed_types('*');
		$this->assertEquals('*', $this->upload->allowed_types);

		$this->upload->set_allowed_types('foo|bar');
		$this->assertEquals(array('foo', 'bar'), $this->upload->allowed_types);
	}

	function test_set_image_properties()
	{
		$this->upload->file_type = 'image/gif';
		$this->upload->file_temp = realpath(PROJECT_BASE.'tests/mocks/uploads/ci_logo.gif');

		$props = array(
			'image_width'	=>	170,
			'image_height'	=>	73,
			'image_type'	=>	'gif',
			'image_size_str'	=>	'width="170" height="73"'
		);

		$this->upload->set_image_properties($this->upload->file_temp);

		$this->assertEquals($props['image_width'], $this->upload->image_width);
		$this->assertEquals($props['image_height'], $this->upload->image_height);
		$this->assertEquals($props['image_type'], $this->upload->image_type);
		$this->assertEquals($props['image_size_str'], $this->upload->image_size_str);
	}

	function test_set_xss_clean()
	{
		$this->upload->set_xss_clean(TRUE);
		$this->assertTrue($this->upload->xss_clean);

		$this->upload->set_xss_clean(FALSE);
		$this->assertFalse($this->upload->xss_clean);
	}

	function test_is_image()
	{
		$this->upload->file_type = 'image/x-png';
		$this->assertTrue($this->upload->is_image());

		$this->upload->file_type = 'text/plain';
		$this->assertFalse($this->upload->is_image());
	}

	function test_is_allowed_filetype()
	{
		$this->upload->allowed_types = array('html', 'gif');

		$this->upload->file_ext = '.txt';
		$this->upload->file_type = 'text/plain';
		$this->assertFalse($this->upload->is_allowed_filetype(FALSE));
		$this->assertFalse($this->upload->is_allowed_filetype(TRUE));

		$this->upload->file_ext = '.html';
		$this->upload->file_type = 'text/html';
		$this->assertTrue($this->upload->is_allowed_filetype(FALSE));
		$this->assertTrue($this->upload->is_allowed_filetype(TRUE));

		$this->upload->file_temp = realpath(PROJECT_BASE.'tests/mocks/uploads/ci_logo.gif');
		$this->upload->file_ext = '.gif';
		$this->upload->file_type = 'image/gif';
		$this->assertTrue($this->upload->is_allowed_filetype());
	}

	function test_is_allowed_filesize()
	{
		$this->upload->max_size = 100;
		$this->upload->file_size = 99;

		$this->assertTrue($this->upload->is_allowed_filesize());

		$this->upload->file_size = 101;
		$this->assertFalse($this->upload->is_allowed_filesize());
	}

	function test_is_allowed_dimensions()
	{
		$this->upload->file_type = 'text/plain';
		$this->assertTrue($this->upload->is_allowed_dimensions());

		$this->upload->file_type = 'image/gif';
		$this->upload->file_temp = realpath(PROJECT_BASE.'tests/mocks/uploads/ci_logo.gif');

		$this->upload->max_width = 10;
		$this->assertFalse($this->upload->is_allowed_dimensions());

		$this->upload->max_width = 170;
		$this->upload->max_height = 10;
		$this->assertFalse($this->upload->is_allowed_dimensions());

		$this->upload->max_height = 73;
		$this->assertTrue($this->upload->is_allowed_dimensions());
	}

	function test_validate_upload_path()
	{
		$this->upload->upload_path = '';
		$this->assertFalse($this->upload->validate_upload_path());

		$dir = 'uploads';
		$this->ci_vfs_mkdir($dir);
		$this->upload->upload_path = $this->ci_vfs_path($dir);
		$this->assertTrue($this->upload->validate_upload_path());
	}

	function test_get_extension()
	{
		$this->assertEquals('.txt', $this->upload->get_extension('hello.txt'));
		$this->assertEquals('.htaccess', $this->upload->get_extension('.htaccess'));
		$this->assertEquals('', $this->upload->get_extension('hello'));
	}

	function test_clean_file_name()
	{
		$this->assertEquals('hello.txt', $this->upload->clean_file_name('hello.txt'));
		$this->assertEquals('hello.txt', $this->upload->clean_file_name('%253chell>o.txt'));
	}

	function test_limit_filename_length()
	{
		$this->assertEquals('hello.txt', $this->upload->limit_filename_length('hello.txt', 10));
		$this->assertEquals('hello.txt', $this->upload->limit_filename_length('hello-world.txt', 9));
	}

	function test_do_xss_clean()
	{
		$dir = 'uploads';
		$file1 = 'file1.txt';
		$file2 = 'file2.txt';
		$file3 = 'file3.txt';
		$this->ci_vfs_create($file1, 'The billy goat was waiting for them.', $this->ci_vfs_root, $dir);
		$this->ci_vfs_create($file2, '', $this->ci_vfs_root, $dir);
		$this->ci_vfs_create($file3, '<script type="text/javascript">alert("Boo! said the billy goat")</script>', $this->ci_vfs_root, $dir);

		$this->upload->file_temp = $this->ci_vfs_path($file1, $dir);
		$this->assertTrue($this->upload->do_xss_clean());

		$this->upload->file_temp = $this->ci_vfs_path($file2, $dir);
		$this->assertFalse($this->upload->do_xss_clean());

		$this->upload->file_temp = $this->ci_vfs_path($file3, $dir);
		$this->assertFalse($this->upload->do_xss_clean());

		$this->upload->file_temp = realpath(PROJECT_BASE.'tests/mocks/uploads/ci_logo.gif');
		$this->assertTrue($this->upload->do_xss_clean());
	}

	function test_set_error()
	{
		$errors = array(
			'An error!'
		);

		$another = 'Another error!';

		$this->upload->set_error($errors);
		$this->assertEquals($errors, $this->upload->error_msg);

		$errors[] = $another;
		$this->upload->set_error($another);
		$this->assertEquals($errors, $this->upload->error_msg);
	}

	function test_display_errors()
	{
		$this->upload->error_msg[] = 'Error test';
		$this->assertEquals('<p>Error test</p>', $this->upload->display_errors());
	}

	function test_mimes_types()
	{
		$this->assertEquals('text/plain', $this->upload->mimes_types('txt'));
		$this->assertFalse($this->upload->mimes_types('foobar'));
	}

}