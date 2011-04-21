<?php

class Lang_test extends CI_TestCase {
	
	protected $lang;
	
	public function setUp()
	{
		$cls = $this->ci_core_class('lang');
		$this->lang = new $cls;
	}
	
	// --------------------------------------------------------------------
	
	public function testLoad()
	{
		// get_config needs work
		$this->markTestIncomplete('get_config needs work');
		//$this->assertTrue($this->lang->load('profiler'));
	}
	
	// --------------------------------------------------------------------

	public function testLine()
	{
		$this->markTestIncomplete('get_config needs work');
		
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}
	
}