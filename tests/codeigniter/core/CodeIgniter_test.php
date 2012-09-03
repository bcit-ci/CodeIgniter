<?php

class CodeIgniter_test extends CI_TestCase {
	/**
	 * Prepare for test
	 */
	public function set_up()
	{
		// Create VFS tree
		$this->root = vfsStream::setup();
		$this->app_root = vfsStream::newDirectory('application')->at($this->root);
		$this->base_root = vfsStream::newDirectory('system')->at($this->root);

		// Get VFS app and base path URLs
		$this->app_path = vfsStream::url('application').'/';
		$this->base_path = vfsStream::url('system').'/';
	}

	/**
	 * Clean up after test
	 */
	public function tear_down()
	{
		// Clean up for next test
		Mock_Core_CodeIgniter::reset();
	}

	/**
	 * Test CodeIgniter instance singleton
	 *
	 * @covers	CodeIgniter::instance
	 */
	public function test_instance()
	{
		// Create main config file
		$log_cfg = array(
			'log_path' => 'test_logs',
			'log_threshold' => 0,
			'log_date_format' => 'Y-m-d'
		);
		$pre = 'MY_';
		$cfg = array_merge(array('subclass_prefix' => $pre), $log_cfg);
		$this->_create_config($cfg);

		// Instantiate CodeIgniter
		$CI = Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path);
		$this->assertNotNull($CI);
		$this->assertInstanceOf('CodeIgniter', $CI);

		// Does a subsequent call give us the same object?
		$CI2 = Mock_Core_CodeIgniter::instance();
		$this->assertEquals($CI, $CI2);

		// Were our config items registered?
		$this->assertEquals($pre, $CI->subclass_prefix);
		$this->assertEquals($log_cfg['log_threshold'], $CI->log_threshold);
		$this->assertEquals($log_cfg, $CI->_log_config);
		$this->assertEquals($cfg, $CI->_core_config);

		// Were base and app paths set correctly?
		$this->assertEquals(array($this->app_path, $this->base_path), $CI->base_paths);
		$this->assertEquals(array($this->app_path), $CI->app_paths);
	}

	/**
	 * Test instantiation without config.php
	 *
	 * @covers  CodeIgniter::instance
	 */
	public function test_noconfig()
	{
		// Do we get a 503 if there is no config.php?
		$this->setExpectedException('RuntimeException', 'CI 503 Exit: The configuration file does not exist.');
		$this->assertNull(Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path));
	}

	/**
	 * Test package path autoloading
	 *
	 * @covers  CodeIgniter::instance
	 */
	public function test_auto_package()
	{
		// Create main config file
		$cfg = array(
			'subclass_prefix' => 'MY_',
			'log_threshold' => 0
		);
		$this->_create_config($cfg);

		// Create autoload config in environment path
		$env = 'test';
		$path = 'test_path/';
		$auto = array(
			'packages' => array($path)
		);
		$tree = array($env => array('autoload.php' => '<?php $autoload = '.var_export($auto, TRUE).';'));
		vfsStream::create($tree, $this->app_root->getChild('config'));

		// Create instance with environment
		$CI = Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path, $env);
		$this->assertNotNull($CI);

		// Was autoload found?
		$this->assertEquals($auto, $CI->_autoload);

		// Was package path loaded?
		$this->assertContains($path, $CI->base_paths);
		$this->assertContains($path, $CI->app_paths);
	}

	/**
	 * Test $assign_to_config
	 *
	 * @covers	CodeIgniter::instance
	 */
	public function test_assign_config()
	{
		global $assign_to_config;

		// Create main config file
		$cfg = array(
			'base_url' => '',
			'index_page' => 'index.php',
			'language' => 'english',
			'subclass_prefix' => 'MY_',
			'log_threshold' => 0
		);
		$this->_create_config($cfg);

		// Create assign_to_config
		$assign_to_config = array(
			'base_url' => 'www.unittest.com',
			'index_page' => 'welcome.php',
			'log_threshold' => 2,
			'custom_item' => false
		);

		// Instantiate CodeIgniter
		$CI = Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path);
		$this->assertNotNull($CI);

		// Did the overrides apply?
		$cmp = array(
			'base_url' => $assign_to_config['base_url'],
			'index_page' => $assign_to_config['index_page'],
			'language' => $cfg['language'],
			'subclass_prefix' => $cfg['subclass_prefix'],
			'log_threshold' => $assign_to_config['log_threshold'],
			'custom_item' => $assign_to_config['custom_item']
		);
		$this->assertEquals($cmp, $CI->_core_config);
		$this->assertEquals($cfg['subclass_prefix'], $CI->subclass_prefix);
		$this->assertEquals($assign_to_config['log_threshold'], $CI->log_threshold);

		// Did we get a log message (sice the override raised the threshold)?
		$this->assertEquals('CodeIgniter Class Initialized', $CI->_log);
	}

	/**
	 * Test instance subclass
	 *
	 * @covers	CodeIgniter::instance
	 */
	public function test_sub_instance()
	{
		// Create main config file in VFS
		$pre = 'SUB_';
		$cfg = array(
			'subclass_prefix' => $pre,
			'log_threshold' => 0
		);
		$this->_create_config($cfg);

		// Create autoload config in VFS
		$path = 'custom';
		$auto = array('packages' => array(vfsStream::url($path)));
		$content = '<?php $autoload = '.var_export($auto, TRUE).';';
		vfsStream::newFile('autoload.php')->withContent($content)->at($this->app_root->getChild('config'));

		// Create package path with subclass in VFS
		$class = 'Mock_Core_CodeIgniter';
		$subclass = $pre.$class;
		$content = '<?php class '.$subclass.' extends '.$class.' { }';
		$tree = array($path => array('core' => array($subclass.'.php' => $content)));
		vfsStream::create($tree, $this->root);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path);
		$this->assertNotNull($CI);
		$this->assertInstanceOf('CodeIgniter', $CI);
		$this->assertInstanceOf($class, $CI);
		$this->assertInstanceOf($subclass, $CI);
	}

	/**
	 * Test calling a controller
	 *
	 * @covers	CodeIgniter::call_controller
	 */
	public function test_call_controller()
	{
		// Create main config file
		$cfg = array(
			'subclass_prefix' => 'MY_',
			'log_threshold' => 1
		);
		$this->_create_config($cfg);

		// Instantiate CodeIgniter
		$CI = Mock_Core_CodeIgniter::instance($this->base_path, $this->app_path);
		$this->assertNotNull($CI);

		// Create controller
		$class = 'Test_ctlr';
		$name = 'testctlr';
		$method = 'test';
		$msg = 'Test ran';
		$code = 'class '.$class.' extends CI_Controller { public $message; '.
			'public function '.$method.'() { $this->message = \''.$msg.'\'; } }';
		eval($code);
		$CI->$name = new $class();

		// Call method
		$this->assertTrue($CI->call_controller($class, $method, array(), $name));
		$this->assertEquals($msg, $CI->$name->message);
	}

	/**
	 * Create main config.php file
	 *
	 * @param	array	$config array
	 */
	private function _create_config(array $config, $name = 'config')
	{
		// Build tree of config file in config directory
		$tree = array('config' => array($name.'.php' => '<?php $'.$name.' = '.var_export($config, TRUE).';'));

		// Create tree under application/
		vfsStream::create($tree, $this->app_root);
	}
}

