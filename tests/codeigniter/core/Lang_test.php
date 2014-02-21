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

		// Non-alpha idiom (should act the same as unspecified language)
		$this->ci_vfs_clone('system/language/english/number_lang.php');
		$this->assertTrue($this->lang->load('number'));
		$this->assertEquals('Bytes', $this->lang->language['bytes']);

		// Non-existent file
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested language file: language/english/nonexistent_lang.php'
		);
		$this->lang->load('nonexistent');

		// File with no language content
		$this->ci_vfs_create('foo_lang.php', '<?php $foo = 0;', $this->ci_app_root, 'language/english');
		$this->assertNull($this->lang->load('foo'));

		// Non-array $lang
		$this->ci_vfs_create('bar_lang.php', '<?php $lang = FALSE;', $this->ci_app_root, 'language/english');
		$this->assertNull($this->lang->load('bar'));

		// Non-array / no content, with return = TRUE
		$this->assertEquals(array(), $this->lang->load('bar'));

		// With an alt_path, return = TRUE & suffix included
		$this->ci_vfs_mkdir('fubar/language/english', $this->ci_app_root);
		$this->ci_vfs_create('fubar_lang.php', '<?php $lang = array(\'fubar_test\' => \'Test\');', $this->ci_app_root.'/fubar/language/english');
		$this->assertEquals(
			array('fubar_test' => 'Test'),
			$this->lang->load('fubar_lang', 'english', TRUE, TRUE, $this->ci_app_root.'fubar')
		);
		$this->assertEquals(
			array('fubar_test' => 'Test'),
			$this->lang->load('fubar_lang', 'english', TRUE, FALSE, $this->ci_app_root.'fubar')
		);
		$this->assertEquals(
			array('fubar_test' => 'Test'),
			$this->lang->load('fubar_lang.php', 'english', TRUE, FALSE, $this->ci_app_root.'fubar')
		);
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