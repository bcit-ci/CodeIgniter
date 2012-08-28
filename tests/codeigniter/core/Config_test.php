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

	// --------------------------------------------------------------------

	public function test_load()
	{
		// Create VFS tree of application config files
		$file1 = 'test.php';
		$file2 = 'secttest';
		$key1 = 'testconfig';
		$val1 = 'my_value';
		$cfg1 = array(
			$key1 => $val1
		);
		$cfg2 = array(
			'one' => 'prime',
			'two' => 2,
			'three' => true
		);
		$tree = array(
			'application' => array(
				'config' => array(
					$file1 => '<?php $config = '.var_export($cfg1, TRUE).';',
					$file2.'.php' => '<?php $config = '.var_export($cfg2, TRUE).';'
				)
			)
		);
		$root = vfsStream::setup('root', NULL, $tree);

		// Set config path with VFS URL
		$this->config->_config_paths = array(vfsStream::url('application').'/');

		// Test regular load
		$this->assertTrue($this->config->load($file1));
		$this->assertEquals($val1, $this->config->item($key1));

		// Test section load
		$this->assertTrue($this->config->load($file2, TRUE));
		$this->assertEquals($cfg2, $this->config->item($file2));

		// Test graceful fail
		$this->assertFalse($this->config->load('not_config_file', FALSE, TRUE));

		// Test regular fail
		$file3 = 'absentia';
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: The configuration file '.$file3.'.php does not exist.'
		);
		$this->assertNull($this->config->load($file3));
	}

}
