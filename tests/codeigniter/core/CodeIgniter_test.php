<?php

class CodeIgniter_test extends CI_TestCase {
	/**
	 * Prepare for test
	 */
	public function set_up()
	{
		// Create VFS tree
        $this->ci_vfs_setup();
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
	 * covers	CodeIgniter::instance
	 * covers	CodeIgniter::__construct
	 * covers	CodeIgniter::resolve_path
	 * covers	CodeIgniter::_get_class
	 * covers	CodeIgniter::_status_exit
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
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
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
		$this->assertEquals(array($this->ci_app_path, $this->ci_base_path), $CI->base_paths);
		$this->assertEquals(array($this->ci_app_path), $CI->app_paths);
	}

	/**
	 * Test instantiation without config.php
	 *
	 * covers  CodeIgniter::instance
	 */
	public function test_noconfig()
	{
		// Do we get a 503 if there is no config.php?
		$this->setExpectedException('RuntimeException', 'CI 503 Exit: The configuration file does not exist.');
		$this->assertNull(Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path));
	}

	/**
	 * Test package path autoloading
	 *
	 * covers  CodeIgniter::instance
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
		$this->_create_config($auto, 'autoload');

		// Create instance with environment
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path, $env);
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
	 * covers	CodeIgniter::instance
	 * covers	CodeIgniter::log_message
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
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
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
	 * covers	CodeIgniter::instance
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
		$this->_create_config($auto, 'autoload');

		// Create package path with subclass in VFS
		$class = 'Mock_Core_CodeIgniter';
		$subclass = $pre.$class;
		$content = '<?php class '.$subclass.' extends '.$class.' { }';
		$this->ci_vfs_create($subclass, $content, $this->ci_vfs_root, array($path, 'core'));

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);
		$this->assertInstanceOf('CodeIgniter', $CI);
		$this->assertInstanceOf($class, $CI);
		$this->assertInstanceOf($subclass, $CI);
	}

	/**
	 * Test loading core class
	 *
	 * covers	CodeIgniter::load_core_class
	 */
	public function test_load_core()
	{
		// Create main config file in VFS
		$pre = 'EXT_';
		$cfg = array(
			'subclass_prefix' => $pre,
			'log_threshold' => 0
		);
		$this->_create_config($cfg);

		// Create "core" class in VFS
		$name = 'Test';
		$class = 'CI_'.$name;
		$this->ci_vfs_create($name, '<?php class '.$class.' { }', $this->ci_base_root, 'core');

		// Create extension in VFS
		$ext = $pre.$name;
		$this->ci_vfs_create($ext, '<?php class '.$ext.' extends '.$class.' { }', $this->ci_app_root, 'core');

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Load class with object name
		$obj = 'mytest';
		$CI->load_core_class($name, $obj);

		// Was it loaded?
		$this->assertObjectHasAttribute($obj, $CI);
		$this->assertInstanceOf($class, $CI->$obj);
		$this->assertInstanceOf($ext, $CI->$obj);
	}

	/**
	 * Test calling a controller
	 *
	 * covers	CodeIgniter::call_controller
	 * covers	CodeIgniter::is_callable
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
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
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
	 * Test loading core base
	 *
	 * covers	CodeIgniter::_load_base
	 */
	public function test_load_base()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will alter names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Base__';

		// Set property names to be checked
		$marks = '_marked';
		$hooks = '_called';

		// Create core classes in VFS
		// The order here establishes the sequence that will be checked below
		$classes = array(
			'Benchmark' => array('methods' => array('mark($a)' => '$this->'.$marks.'[] = $a;')),
			'Config' => array('methods' => array('get($a, $b)' => 'return TRUE;')),
			'Hooks' => array('methods' => array('call_hook($a)' => '$this->'.$hooks.'[] = $a; return FALSE;')),
			'Loader' => array('obj' => 'load')
		);
		$names = $this->_create_core($pre, $prop, $classes);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Load base
		$CI->load_base($pre);

		// Was everything loaded in order?
		$this->_check_core_sequence($CI, $names, $prop);

		// Were the right benchmarks set in the right order?
		$this->assertObjectHasAttribute($marks, $CI->benchmark);
		$expect = array('total_execution_time_start', 'loading_time:_base_classes_start');
		$this->assertEquals($expect, $CI->benchmark->$marks);

		// Were the right hooks called?
		$this->assertObjectHasAttribute($hooks, $CI->hooks);
		$this->assertEquals(array('pre_system'), $CI->hooks->$hooks);
	}

	/**
	 * Test loading core routing
	 *
	 * covers	CodeIgniter::_load_routing
	 */
	public function test_load_routing()
	{
		global $routing;

		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Route__';

		// Set property names to check
		$hooks = '_called';
		$rtng = '_routing';
		$over = '_overrides';

		// Create core classes in VFS
		// The order here establishes the sequence that will be checked below
		// We include Hooks first, as it was already loaded and is called in routing
		$classes = array(
			'Hooks' => array('methods' => array('call_hook($a)' => '$this->'.$hooks.'[] = $a; return FALSE;')),
			'Utf8' => array(),
			'URI' => array(),
			'Output' => array('methods' => array('_display_cache()' => 'return FALSE;')),
			'Router' => array('methods' => array('_set_routing()' => '$this->'.$rtng.' = TRUE; return TRUE;',
				'_set_overrides($a)' => '$this->'.$over.' = $a;'))
		);
		$names = $this->_create_core($pre, $prop, $classes);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Define routing overrides
		$routing = array(
			'directory' => 'test_dir',
			'controller' => 'test_ctlr',
			'function' => 'test_func'
		);

		// Load routing
		$CI->load_routing($pre);

		// Was everything loaded in order?
		$this->_check_core_sequence($CI, $names, $prop);

		// Were the right hooks called?
		$this->assertObjectHasAttribute($hooks, $CI->hooks);
		$this->assertEquals(array('cache_override'), $CI->hooks->$hooks);

		// Did routing get set?
		$this->assertObjectHasAttribute($rtng, $CI->router);
		$this->assertTrue($CI->router->$rtng);

		// Did overrides get set?
		$this->assertObjectHasAttribute($over, $CI->router);
		$this->assertEquals($routing, $CI->router->$over);
	}

	/**
	 * Test caching
	 *
	 * covers	CodeIgniter::_load_routing
	 */
	public function test_load_cache()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Cache__';

		// Create core classes in VFS
		// This time, _display_cache will return TRUE as if it displayed the cache
		$classes = array(
			'Hooks' => array('methods' => array('call_hook($a)' => 'return FALSE;')),
			'Utf8' => array(),
			'URI' => array(),
			'Output' => array('methods' => array('_display_cache()' => 'return TRUE;')),
			'Router' => array('methods' => array('_set_routing()' => 'return TRUE;', '_set_overrides($a)' => ''))
		);
		$this->_create_core($pre, $prop, $classes);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Load routing and catch exit with no code or message
		$this->setExpectedException('RuntimeException', 'CI 0 Exit: ');
		$CI->load_routing($pre);
	}

	/**
	 * Test loading core support
	 *
	 * covers	CodeIgniter::_load_support
	 */
	public function test_load_support()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Support__';

		// Set property names to check
		$marks = '_marked';
		$auto = '_last';

		// Create core classes in VFS
		// The order here establishes the sequence that will be checked below
		// We include Benchmark and Loader first, as they were already loaded
		// and are called in routing
		$classes = array(
			'Benchmark' => array('methods' => array('mark($a)' => '$this->'.$marks.'[] = $a;')),
			'Loader' => array('obj' => 'load', 'methods' => array('_ci_autoloader()' =>
				'$this->'.$auto.' = (isset(CodeIgniter::instance()->lang));')),
			'Security' => array(),
			'Input' => array(),
			'Lang' => array()
		);
		$names = $this->_create_core($pre, $prop, $classes);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Load support
		$CI->load_support($pre);

		// Was everything loaded in order?
		$this->_check_core_sequence($CI, $names, $prop);

		// Were the right benchmarks set?
		$this->assertObjectHasAttribute($marks, $CI->benchmark);
		$this->assertEquals(array('loading_time:_base_classes_end'), $CI->benchmark->$marks);

		// Was ci_autoload called last?
		$this->assertObjectHasAttribute($auto, $CI->load);
		$this->assertTrue($CI->load->$auto);
	}

	/**
	 * Test running a controller
	 *
	 * covers	CodeIgniter::_run_controller
	 */
	public function test_run_controller()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Ctlr__';

		// Set property names to be checked
		$marks = '_marked';
		$hooks = '_called';
		$ran = '_ran';

		// Create controller in VFS
		$ctlr = 'TestRunCtlr';
		$class = strtolower($ctlr);
		$method = 'test';
		$content = '<?php class '.$ctlr.' { public function '.$method.'() { $this->'.$ran.' = TRUE; } }';
		$this->ci_vfs_create($class, $content, $this->ci_app_root, 'controllers');

		// Create route
		$route = array(
			$this->ci_app_path,
			'',
			$ctlr,
			$method
		);

		// Create core classes in VFS
		$this->_create_run_core($pre, $prop, $route, $marks, $hooks);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Run controller
		$CI->run_controller($pre);

		// Was the controller loaded and run?
		$this->assertObjectHasAttribute($class, $CI);
		$this->assertObjectHasAttribute($ran, $CI->$class);
		$this->assertTrue($CI->$class->$ran);

		// Was the routed object set correctly?
		$this->assertObjectHasAttribute('routed', $CI);
		$this->assertEquals($CI->routed, $CI->$class);

		// Were the right benchmarks set?
		$this->assertObjectHasAttribute($marks, $CI->benchmark);
		$expect = array('controller_execution_time_( '.$class.' / '.$method.' )_start');
		$this->assertEquals($expect, $CI->benchmark->$marks);

		// Were the right hooks called in the right order?
		$this->assertObjectHasAttribute($hooks, $CI->hooks);
		$this->assertEquals(array('pre_controller', 'post_controller_constructor'), $CI->hooks->$hooks);
	}

	/**
	 * Test a controller 404
	 *
	 * covers	CodeIgniter::_run_controller
	 */
	public function test_controller_404()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'C404__';

		// Create route for nonexistent controller
		$route = array(
			$this->ci_app_path,
			'',
			'NoController',
			'test'
		);

		// Create core classes in VFS
		$this->_create_run_core($pre, $prop, $route);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Run controller and catch 404
		$this->setExpectedException('RuntimeException', 'CI Error: 404');
		$CI->run_controller($pre);
	}

	/**
	 * Test a method 404
	 *
	 * covers	CodeIgniter::_run_controller
	 */
	public function test_method_404()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'M404__';

		// Create controller without method in VFS
		$ctlr = 'Test404Ctlr';
		$this->ci_vfs_create(strtolower($ctlr), '<?php class '.$ctlr.' { }', $this->ci_app_root, 'controllers');

		// Create route for nonexistent controller
		$route = array(
			$this->ci_app_path,
			'',
			$ctlr,
			'no_method'
		);

		// Create core classes in VFS
		$this->_create_run_core($pre, $prop, $route);

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Run controller and catch 404
		$this->setExpectedException('RuntimeException', 'CI Error: 404');
		$CI->run_controller($pre);
	}

	/**
	 * Test finalization and output
	 *
	 * covers	CodeIgniter::_finalize
	 */
	public function test_finalize()
	{
		// Create main config file in VFS
		$this->_create_config(array('log_threshold' => 0));

		// Set sequence property name and class prefix
		// Given a prefix, the mock will prefix names for us to avoid collisions
		// with actual core classes, since classes can't be unregistered.
		$prop = 'sequence';
		$pre = 'Final__';

		// Set property names to be checked
		$marks = '_marked';
		$hooks = '_called';
		$ran = '_ran';

		// Create route
		$class = 'final_ctlr';
		$method = 'final_method';
		$route = array(
			$this->ci_app_path,
			'',
			$class,
			$method
		);

		// Create core classes in VFS
		$this->_create_run_core($pre, $prop, $route, $marks, $hooks);

		// Create Output with display
		$this->_create_core($pre, $prop, 'Output', '', array('_display()' => '$this->'.$ran.' = TRUE;'));

		// Create instance
		$CI = Mock_Core_CodeIgniter::instance($this->ci_base_path, $this->ci_app_path);
		$this->assertNotNull($CI);

		// Finalize
		$CI->finalize($pre);

		// Was display run?
		$this->assertObjectHasAttribute($ran, $CI->output);
		$this->assertTrue($CI->output->$ran);

		// Were the right benchmarks set?
		$this->assertObjectHasAttribute($marks, $CI->benchmark);
		$expect = array('controller_execution_time_( '.$class.' / '.$method.' )_end');
		$this->assertEquals($expect, $CI->benchmark->$marks);

		// Were the right hooks called in the right order?
		$this->assertObjectHasAttribute($hooks, $CI->hooks);
		$this->assertEquals(array('post_controller', 'display_override', 'post_system'), $CI->hooks->$hooks);
	}

	/**
	 * Check if the classes were loaded correctly and in order
	 *
	 * @param	object	CI object
	 * @param	array	Classes array from _create_core
	 * @param	string	Sequence property name
	 * @return	void
	 */
	private function _check_core_sequence($CI, $classes, $prop)
	{
		// Check each class
		foreach ($classes as $obj => $class)
		{
			// Was it loaded under its name?
			$this->assertObjectHasAttribute($obj, $CI, $obj.' load failed');

			// Is it the right class?
			$this->assertInstanceOf($class, $CI->$obj, $obj.' is not a '.$class);

			// Did its sequence test pass?
			$this->assertTrue($CI->$obj->$prop, $obj.' sequence failed');
		}
	}

	/**
	 * Create core necessary to run a controller
	 *
	 * @param   string  Class prefix
	 * @param   string  Class sequence property name
	 * @param	array	Route stack
	 * @param	array	Optional benchmark tracking property name
	 * @param	array	Optional hook tracking property name
	 */
	private function _create_run_core($pre, $prop, $route, $marks = FALSE, $hooks = FALSE)
	{
		// Create Benchmark with optional mark tracking
		$code = $marks ? '$this->'.$marks.'[] = $a;' : '';
		$this->_create_core($pre, $prop, 'Benchmark', '', array('mark($a)' => $code));

		// Create Hooks with optional hook tracking
		$code = $hooks ? '$this->'.$hooks.'[] = $a; ' : '';
		$this->_create_core($pre, $prop, 'Hooks', '', array('call_hook($a)' => $code.'return FALSE;'));

		// Create Loader with a simple controller loader to instantiate our class
		// at the right time
		$this->_create_core($pre, $prop, 'Loader', '', array(
			'controller($a, $b, $c)' =>
				'$name = strtolower($a[2]); '.
				'$class = ucfirst($a[2]); '.
				'$file = $a[0].\'controllers/\'.$a[1].$name.\'.php\'; '.
				'if ( ! file_exists($file)) return FALSE; '.
				'include($file); '.
				'if ( ! class_exists($class)) return FALSE; '.
				'CodeIgniter::instance()->$name = new $class(); '.
				'return TRUE;'
		));

		// Create Router which will return our route stack
		// and the constants used in _run_controller and _finalize
		$this->_create_core($pre, $prop, 'Router', '',
			array('fetch_route()' => 'return '.var_export($route, TRUE).';'),
			array('SEG_CLASS' => 2, 'SEG_METHOD' => 3));
	}

	/**
	 * Create config file
	 *
	 * @param	array	Config array
	 * @param   string  File/array name
	 * @param   string  Optional subdirectory (for env)
	 * @return  void
	 */
	private function _create_config(array $config, $name = 'config', $sub = NULL)
	{
		// Generate config file content
		$content = '<?php $'.$name.' = '.var_export($config, TRUE).';';

		// Create file under subdirectory of app config dir
		$path = array('config');
		if ($sub)
		{
			$path[] = $sub;
		}
		$this->ci_vfs_create($name, $content, $this->ci_app_root, $path);
	}

	/**
	 * Create core class(es) in VFS
	 *
	 * @param   string  Class prefix
	 * @param   string  Class sequence property name
	 * @param   string  Class name or array of classes
	 * @param   string  Optional previous class in sequence
	 * @param   array   Optional class methods
	 * @param	array	Optional class constants
	 * @return  mixed   Class name or array of object/class pairs
	 */
	private function _create_core($pre, $prop, $name, $prev = FALSE, $methods = array(), $constants = array())
	{
		// Check for multiples
		if (is_array($name))
		{
			// Dispatch
			$cores = $name;
			$prev = FALSE;
			$classes = array();

			foreach ($cores as $name => $props)
			{
				// Get object name and methods and constants lists
				$obj = isset($props['obj']) ? $props['obj'] : strtolower($name);
				$methods = isset($props['methods']) ? $props['methods'] : array();
				$constants = isset($props['constants']) ? $props['constants'] : array();

				// Create core class and save object name for next in sequence
				$classes[$obj] = $this->_create_core($pre, $prop, $name, $prev, $methods, $constants);
				$prev = $obj;
			}

			// Return list of object and class names
			return $classes;
		}

		// Assemble mock class name and sequence test
		$mock = $pre.$name;
		$class = 'CI_'.$mock;
		$test = $prev ? 'isset(CodeIgniter::instance()->'.$prev.')' : 'TRUE';

		// Build class content
		$content = '<?php class '.$class.' { ';
		foreach ($constants as $const => $val)
		{
			$content .= 'const '.$const.' = '.$val.'; ';
		}
		$content .= 'public function __construct() { $this->'.$prop.' = '.$test.'; } ';
		foreach ($methods as $method => $body)
		{
			$content .= 'public function '.$method.' { '.$body.' } ';
		}
		$content .= '}';

		// Create content in core directory and return class name
		$this->ci_vfs_create($mock, $content, $this->ci_base_root, 'core');
		return $class;
	}
}

