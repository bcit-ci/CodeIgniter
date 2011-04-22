<?php

class Lang_test extends CI_TestCase {
	
	protected $lang;
	
	public function set_up()
	{
		$cls = $this->ci_core_class('lang');
		$this->lang = new $cls;
	}
	
	// --------------------------------------------------------------------
	
	public function test_load()
	{
		// get_config needs work
		$this->markTestIncomplete('get_config needs work');
		//$this->assertTrue($this->lang->load('profiler'));
	}
	
	// --------------------------------------------------------------------

	public function test_line()
	{
		$this->markTestIncomplete('get_config needs work');
		
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}
	
}