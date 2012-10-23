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
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$this->assertTrue($this->lang->load('profiler', 'english'));
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}

	// --------------------------------------------------------------------

	public function test_load_with_unspecified_language()
	{
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$this->assertTrue($this->lang->load('profiler'));
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}

}