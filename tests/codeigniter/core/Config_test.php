<?php

class Config_test extends CI_TestCase {
	/**
	 * Set up for next test
	 */
	public function set_up()
	{
		// Set predictable config values
		$ci = $this->ci_instance();
		$this->index_page = 'index.php';
		$this->base_url = 'http://example.com/';
		$this->subclass_prefix = 'MY_';
		$this->core_config = array(
			'index_page'		=> $this->index_page,
			'base_url'			=> $this->base_url,
			'subclass_prefix'	=> $this->subclass_prefix
		);
		$ci->_core_config = $this->core_config;

		// Set empty autoload.php contents
		$ci->_autoload = array();

		// Create VFS tree and set app path
		$this->ci_vfs_setup();
		$ci->app_paths = array($this->ci_app_path);

		// Create constants file in VFS
		// For the contructor test, we have it set a global to check
		$content = ($this->getName() == 'test_ctor') ? '<?php global $constants_ran; $constants_ran = TRUE;' : '';
		$this->ci_vfs_create('constants', $content, $this->ci_app_root, 'config');

		// Load config class
		$cls =& $this->ci_core_class('cfg');
		$this->config = new $cls;
	}

	/**
	 * Test constructor
	 *
	 * @covers	CI_Config::__construct
	 */
	public function test_ctor()
	{
		global $constants_ran;

		// Did our core config get picked up?
		$this->assertEquals($this->core_config, $this->config->config);

		// Were config paths initialized from the core?
		$this->assertEquals(array($this->ci_app_path), $this->config->_config_paths);

		// Was constants.php run?
		$this->assertTrue(isset($constants_ran));
		$this->assertTrue($constants_ran);
		unset($constants_ran);
	}

	/**
	 * Test item retrieval
	 *
	 * @covers  CI_Config::item
	 */
	public function test_item()
	{
		// Check configured item
		$this->assertEquals($this->base_url, $this->config->item('base_url'));

		// Does index work?
		$index = 'default';
		$item = 'good_item';
		$value = 'exists';
		$this->config->config[$index][$item] = $value;
		$this->assertEquals($value, $this->config->item($item, $index));

		// Does a missing item return FALSE?
		$this->assertFalse($this->config->item('no_good_item'));

		// Does a missing item under an index return FALSE?
		$this->assertFalse($this->config->item('no_good_item', 'bad_index'));

		// Does an item under a missing index return FALSE?
		$this->assertFalse($this->config->item('no_good_item', $index));
	}

	/**
	 * Test set item
	 *
	 * @covers  CI_Config::set_item
	 */
	public function test_set_item()
	{
		// Verify not set yet
		$item = 'not_yet_set';
		$this->assertFalse($this->config->item($item));

		// Set item
		$value = 'is set';
		$this->config->set_item($item, $value);

		// Was it set?
		$this->assertEquals($value, $this->config->item($item));
	}

	/**
	 * Test slash item
	 *
	 * @covers  CI_Config::slash_item
	 */
	public function test_slash_item()
	{
		// Do we get the base with a single slash?
		$this->assertEquals($this->base_url, $this->config->slash_item('base_url'));

		// Do we get the subclass with a slash?
		$this->assertEquals($this->subclass_prefix.'/', $this->config->slash_item('subclass_prefix'));

		// Do we get FALSE if it doesn't exist?
		$this->assertFalse($this->config->slash_item('no_good_item'));
	}

	/**
	 * Test site url
	 *
	 * @covers  CI_Config::site_url
	 */
	public function test_site_url()
	{
		// Do we get the right default?
		$this->assertEquals($this->base_url.$this->index_page, $this->config->site_url());

		// Save query string setting and override it and base url
		$q_string = $this->config->item('enable_query_strings');
		$this->config->set_item('enable_query_strings', FALSE);
		$this->config->set_item('base_url', '');

		// Do we get the default page with REST segments?
		$arg1 = 'test';
		$arg2 = '1';
		$this->assertEquals($this->index_page.'/'.$arg1, $this->config->site_url($arg1));
		$this->assertEquals($this->index_page.'/'.$arg1.'/'.$arg2, $this->config->site_url(array($arg1, $arg2)));

		// Restore query strings
		$this->config->set_item('enable_query_strings', TRUE);

		// Do we get the default page with query args?
		$this->assertEquals($this->index_page.'?'.$arg1, $this->config->site_url($arg1));
		$this->assertEquals($this->index_page.'?0='.$arg1.'&1='.$arg2, $this->config->site_url(array($arg1, $arg2)));

		// Restore base url
		$this->config->set_item('base_url', $this->base_url);

		// Do we get a full URL with default and query arg?
		$this->assertEquals($this->base_url.$this->index_page.'?'.$arg1, $this->config->site_url($arg1));

		// Restore original query setting
		$this->config->set_item('enable_query_strings', $q_string);
	}

	/**
	 * Test system url
	 *
	 * @covers  CI_Config::system_url
	 */
	public function test_system_url()
	{
		// Do we get the system folder under the base URL?
		$this->assertEquals($this->base_url.'system/', $this->config->system_url());
	}

	/**
	 * Test loading a config file
	 *
	 * @covers	CI_Config::load
	 */
	public function test_load()
	{
		// Create config files in VFS
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
		$this->ci_vfs_create($file1, '<?php $config = '.var_export($cfg1, TRUE).';', $this->ci_app_root, 'config');
		$this->ci_vfs_create($file2, '<?php $config = '.var_export($cfg2, TRUE).';', $this->ci_app_root, 'config');

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

	/**
	 * Test getting config file contents
	 *
	 * @covers	CI_Config::get
	 */
	public function test_get()
	{
		// Create config file
		$file = 'myconfig.php';
		$name = 'fish';
		$cfg = array(
			'one' => 'this one has a little star',
			'two' => 'this one has a little car',
			'red' => 'some are glad',
			'blue' => 'some are sad'
		);
		$this->ci_vfs_create($file, '<?php $'.$name.' = '.var_export($cfg, TRUE).';', $this->ci_app_root, 'config');

		// Do we get the contents?
		$this->assertEquals($cfg, $this->config->get($file, $name));

		// Do we get the filename if the array doesn't exist?
		$this->assertEquals($this->ci_app_path.'config/'.$file, $this->config->get($file, 'not_a_var'));

		// Do we get FALSE if the file doesn't exist?
		$this->assertFalse($this->config->get('not_a_file', $name));

		// Create a file to merge
		$cfg2 = array(
			'red' => 'some are very, very bad',
			'say' => 'what a lot of fish there are'
		);
		$dir = 'package';
		$content = '<?php $'.$name.' = '.var_export($cfg2, TRUE).';';
		$this->ci_vfs_create($file, $content, $this->ci_vfs_root, array($dir, 'config'));

		// Add config path
		array_push($this->config->_config_paths, $this->ci_vfs_path($dir.'/'));

		// Do we get the merged config?
		$expect = array(
			'one' => $cfg['one'],
			'two' => $cfg['two'],
			'red' => $cfg2['red'],
			'blue' => $cfg['blue'],
			'say' => $cfg2['say']
		);
		$this->assertEquals($expect, $this->config->get($file, $name));
	}

	/**
	 * Test getting config file contents with extras
	 *
	 * @covers	CI_Config::get_ext
	 */
	public function test_get_ext()
	{
		// Create config file
		$file = 'aconfig.php';
		$name = 'config';
		$cfg = array(
			'int' => 42,
			'bool' => true,
			'string' => 'I do not know. Go ask your dad.',
			'array' => array('one', 'two', 'three')
		);
		$var1 = 'sam';
		$val1 = 'I am.';
		$var2 = 'dr';
		$val2 = 'Seuss';
		$content = '<?php $'.$name.' = '.var_export($cfg, TRUE).'; '.
			'$'.$var1.' = '.var_export($val1, TRUE).'; '.
			'$'.$var2.' = '.var_export($val2, TRUE).';';
		$this->ci_vfs_create($file, $content, $this->ci_app_root, 'config');

		// Do we get the contents?
		$extras = NULL;
		$this->assertEquals($cfg, $this->config->get_ext($file, $name, $extras));
		$this->assertTrue(is_array($extras));
		$this->assertArrayHasKey($var1, $extras);
		$this->assertEquals($val1, $extras[$var1]);
		$this->assertArrayHasKey($var2, $extras);
		$this->assertEquals($val2, $extras[$var2]);
	}
}

