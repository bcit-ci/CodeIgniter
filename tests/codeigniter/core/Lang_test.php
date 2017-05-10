<?php

class Lang_test extends CI_TestCase {

	protected $lang;

	public function set_up()
	{
		$loader_cls = $this->ci_core_class('load');
		$this->ci_instance_var('load', new $loader_cls);
		$cls = $this->ci_core_class('lang');
		$this->lang = new $cls;
	}

	// --------------------------------------------------------------------

	public function test_load()
	{
		// Regular usage
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$this->assertTrue($this->lang->load('profiler', 'english'));
		$this->assertEquals('URI STRING', $this->lang->language['profiler_uri_string']);

		// Already loaded file
		$this->assertNull($this->lang->load('profiler', 'english'));

		// Unspecified language (defaults to english)
		$this->ci_vfs_clone('system/language/english/date_lang.php');
		$this->assertTrue($this->lang->load('date'));
		$this->assertEquals('Year', $this->lang->language['date_year']);

		// A language other than english
		$this->ci_vfs_clone('system/language/english/email_lang.php', 'system/language/german/');
		$this->assertTrue($this->lang->load('email', 'german'));
		$this->assertEquals('german', $this->lang->is_loaded['email_lang.php']);

		// Non-existent file
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested language file: language/english/nonexistent_lang.php'
		);
		$this->lang->load('nonexistent');
	}

	// --------------------------------------------------------------------

	public function test_non_alpha_idiom()
	{
		// Non-alpha idiom (should act the same as unspecified language)
		// test with existing file
		$this->ci_vfs_clone('system/language/english/number_lang.php');
		$this->ci_vfs_clone('system/language/english/number_lang.php', 'system/language/123funny/');
		$this->assertTrue($this->lang->load('number', '123funny'));
		$this->assertEquals('Bytes', $this->lang->language['bytes']);

		// test without existing file
		$this->ci_vfs_clone('system/language/english/email_lang.php');
		$this->assertTrue($this->lang->load('email', '456funny'));
		$this->assertEquals('You did not specify a SMTP hostname.', $this->lang->language['email_no_hostname']);
	}

	// --------------------------------------------------------------------

	public function test_multiple_file_load()
	{
		// Multiple files
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$files = array(
			0 => 'profiler',
			1 => 'nonexistent'
		);
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested language file: language/english/nonexistent_lang.php'
		);
		$this->lang->load($files, 'english');
	}

	// --------------------------------------------------------------------

	public function test_alternative_path_load()
	{
		// Alternative Path
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$this->assertTrue($this->lang->load('profiler', 'english', FALSE, TRUE, 'vfs://system/'));
	}

	// --------------------------------------------------------------------

	/**
	 * @depends	test_load
	 */
	public function test_line()
	{
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$this->lang->load('profiler', 'english');
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
		$this->assertFalse($this->lang->line('nonexistent_string'));
		$this->assertFalse($this->lang->line(NULL));
	}
}
