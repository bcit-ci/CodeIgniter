<?php

class Config_test extends CI_TestCase {

	public function set_up()
	{
		$cls =& $this->ci_core_class('cfg');

		// set predictable config values
		$this->ci_set_config(array(
			'index_page'		=> 'index.php',
			'base_url'			=> 'http://example.com/',
			'subclass_prefix'	=> 'MY_'
		));

		$this->config = new $cls;
	}

	// --------------------------------------------------------------------

	public function test_item()
	{
		$this->assertEquals('http://example.com/', $this->config->item('base_url'));

		// Bad Config value
		$this->assertFalse($this->config->item('no_good_item'));

		// Index
		$this->assertFalse($this->config->item('no_good_item', 'bad_index'));
		$this->assertFalse($this->config->item('no_good_item', 'default'));
	}

	// --------------------------------------------------------------------

	public function test_set_item()
	{
		$this->assertFalse($this->config->item('not_yet_set'));

		$this->config->set_item('not_yet_set', 'is set');

		$this->assertEquals('is set', $this->config->item('not_yet_set'));
	}

	// --------------------------------------------------------------------

	public function test_slash_item()
	{
		// Bad Config value
		$this->assertFalse($this->config->slash_item('no_good_item'));

		$this->assertEquals('http://example.com/', $this->config->slash_item('base_url'));

		$this->assertEquals('MY_/', $this->config->slash_item('subclass_prefix'));
	}

	// --------------------------------------------------------------------

	public function test_site_url()
	{
		$this->assertEquals('http://example.com/index.php', $this->config->site_url());

		$base_url = $this->config->item('base_url');

		$this->config->set_item('base_url', '');

		$q_string = $this->config->item('enable_query_strings');

		$this->config->set_item('enable_query_strings', FALSE);

		$this->assertEquals('index.php/test', $this->config->site_url('test'));
		$this->assertEquals('index.php/test/1', $this->config->site_url(array('test', '1')));

		$this->config->set_item('enable_query_strings', TRUE);

		$this->assertEquals('index.php?test', $this->config->site_url('test'));
		$this->assertEquals('index.php?0=test&1=1', $this->config->site_url(array('test', '1')));

		$this->config->set_item('base_url', $base_url);

		$this->assertEquals('http://example.com/index.php?test', $this->config->site_url('test'));

		// back to home base
		$this->config->set_item('enable_query_strings', $q_string);
	}

	// --------------------------------------------------------------------

	public function test_system_url()
	{
		$this->assertEquals('http://example.com/system/', $this->config->system_url());
	}

}