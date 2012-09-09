<?php

class Loader_test extends CI_TestCase {

	private $ci_obj;

	public function set_up()
	{
		// Create VFS tree
		$this->ci_vfs_setup();

		// Add view path to VFS
		$this->ci_view_root = vfsStream::newDirectory('views')->at($this->ci_vfs_root);
		$this->ci_view_path = vfsStream::url('views/');

		// Determine if we need core support methods
		if (strpos($this->getName(), 'controller') !== FALSE) {
			// Create CI mockup
			$class = 'Mock_Loader_CI';
			if ( ! class_exists($class))
			{
				$code = 'class '.$class.' { '.
					'public function call_controller($class, $method, $args, $name, $return) '.
						'{ $this->_ctlr_ran = $method; return $return ? $this->_ctlr_result : TRUE; } '.
					'public function get_controller_output(&$out, $class, $method, $args, $name) '.
						'{ $this->_ctlr_ran = $method; $out = $this->_ctlr_output; return TRUE; } '.
				'}';
				eval($code);
			}
			$this->ci_obj = new $class();

			// Initialize support flags
			$this->_ctlr_ran = FALSE; 
			$this->_ctlr_result = TRUE;
			$this->_ctlr_output = '';

			// Create router mockup
			$class = 'Mock_Loader_Router';
			if ( ! class_exists($class))
			{
				$code = 'class '.$class.' { const SEG_METHOD = 4; '.
					'public function validate_route($route) { return $this->_valid_route; } }';
				eval($code);
			}
			$this->ci_obj->router = new $class();

			// Initialize route to return
			$this->ci_obj->router->_valid_route = array();
		}
		else {
			// Just get the default instance
			$this->ci_obj = $this->ci_instance();
		}

		// Set up CI instance with empty autoload.php contents and path sources
		$this->ci_obj->_autoload = array();
		$this->ci_obj->base_paths = array($this->ci_app_path, $this->ci_base_path);
		$this->ci_obj->app_paths = array($this->ci_app_path);
		$this->ci_instance($this->ci_obj);

		// Set up config
		$this->subclass = 'MY_';
		$this->ci_set_config('subclass_prefix', $this->subclass);

		// Instantiate a new loader
		$this->load = new Mock_Core_Loader();
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::__construct
	 * @covers	CI_Loader::get_package_paths
	 */
	public function test_ctor()
	{
		// Did we get paths from the core?
		$this->assertEquals($this->ci_obj->base_paths, $this->load->get_package_paths(TRUE));
		$this->assertEquals($this->ci_obj->app_paths, $this->load->get_package_paths());
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::library
	 */
	public function test_library()
	{
		// Create libraries directory with test library
		$lib = 'unit_test_lib';
		$class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create($lib, $this->_empty($class), $this->ci_base_root, 'libraries');

		// Test loading as an array.
		$this->assertNull($this->load->library(array($lib)));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);

		// Test no lib given
		$this->assertFalse($this->load->library());

		// Test a string given to params
		$this->assertNull($this->load->library($lib, ' '));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::library
	 */
	public function test_library_config()
	{
		// Create libraries directory with capitalized test library in subdir
		$sub = 'mylibs';
		$lib = ucfirst('unit_test_config_lib');
		$class = 'CI_'.$lib;
		$content = '<?php class '.$class.
			' { public function __construct($params = NULL) { $this->config = $params; } } ';
		$this->ci_vfs_create($lib, $content, $this->ci_base_root, 'libraries', $sub);

		// Create config to be loaded
		// For isolation, we just set the contents in CI_TestConfig to be retrieved
		$cfg = array(
			'foo' => 'bar',
			'bar' => 'baz',
			'baz' => false
		);
		$this->ci_obj->config->to_get($cfg);

		// Load library with object name
		$obj = 'testy';
		$this->assertNull($this->load->library($sub.'/'.$lib, NULL, $obj));

		// Was the class instantiated?
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertObjectHasAttribute($obj, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);

		// Did the config get set?
		$this->assertObjectHasAttribute('config', $this->ci_obj->$obj);
		$this->assertEquals($cfg, $this->ci_obj->$obj->config);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::library
	 */
	public function test_load_library_in_application_dir()
	{
		// Create libraries directory in app path with test library
		$lib = 'super_test_library';
		$class = ucfirst($lib);
		$this->ci_vfs_create($lib, $this->_empty($class), $this->ci_app_root, 'libraries');

		// Load library
		$this->assertNull($this->load->library($lib));

		// Was the class instantiated?
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertObjectHasAttribute($lib, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::library
	 */
	public function test_library_subclass()
	{
		// Create libraries directory in base path with test library
		$lib = 'sub_test_library';
		$class = ucfirst($lib);
		$this->ci_vfs_create($class, $this->_empty($class), $this->ci_base_root, 'libraries');

		// Create library subclass in app path libraries directory
		$content = $this->_empty($this->subclass.$class, $class);
		$this->ci_vfs_create($this->subclass.$lib, $content, $this->ci_app_root, 'libraries');

		// Load library
		$this->assertNull($this->load->library($lib));

		// Was the class instantiated?
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertTrue(class_exists($this->subclass.$class), $this->subclass.$class.' does not exist');
		$this->assertObjectHasAttribute($lib, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);
		$this->assertAttributeInstanceOf($this->subclass.$class, $lib, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::driver
	 */
	public function test_driver()
	{
		// Create libraries directory with test driver
		$driver = 'unit_test_driver';
		$dir = ucfirst($driver);
		$class = 'CI_'.$dir;
		$this->ci_vfs_create($driver, $this->_empty($class), $this->ci_base_root, 'libraries', $dir);

		// Test loading as an array.
		$this->assertNull($this->load->driver(array($driver)));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertObjectHasAttribute($driver, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $driver, $this->ci_obj);

		// Test loading as a library with a name
		$obj = 'testdrive';
		$this->assertNull($this->load->library($driver, NULL, $obj));
		$this->assertObjectHasAttribute($obj, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);

		// Test no driver given
		$this->assertFalse($this->load->driver());

		// Test a string given to params
		$this->assertNull($this->load->driver($driver, ' '));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::model
	 */
	public function test_model()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('model');

		// Create models directory with test model
		$model = 'unit_test_model';
		$base = 'CI_Model';
		$class = ucfirst($model);
		$this->ci_vfs_create($model, $this->_empty($class, $base), $this->ci_app_root, 'models');

		// Load model as array
		$this->assertNull($this->load->model(array($model)));

		// Was the model class instantiated?
		$this->assertTrue(class_exists($class));
		$this->assertObjectHasAttribute($model, $this->ci_obj);
		$this->assertAttributeInstanceOf($base, $model, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $model, $this->ci_obj);

		// Test no model given
		$this->assertNull($this->load->model(''));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::model
	 */
	public function test_model_subdir()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('model');

		// Create models subdirectory with test model
		$model = 'test_sub_model';
		$base = 'CI_Model';
		$class = ucfirst($model);
		$subdir = 'cars';
		$this->ci_vfs_create($model, $this->_empty($class, $base), $this->ci_app_root, 'models', $subdir);

		// Load model
		$name = 'testors';
		$this->assertNull($this->load->model($subdir.'/'.$model, $name));

		// Was the model class instantiated?
		$this->assertTrue(class_exists($class));
		$this->assertObjectHasAttribute($name, $this->ci_obj);
		$this->assertAttributeInstanceOf($base, $name, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $name, $this->ci_obj);

		// Test name conflict
		$obj = 'conflict';
		$this->ci_obj->$obj = new StdClass();
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: The model name you are loading is the name of a resource that is already being used: '.$obj
		);
		$this->load->model('not_real', $obj);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::model
	 */
	public function test_non_existent_model()
	{
		// Set expected exception
		$model = 'ci_test_nonexistent_model.php';
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to locate the model you have specified: '.$model
		);

		// Try to load model
		$this->load->model($model);
	}

	// --------------------------------------------------------------------

	// public function testDatabase()
	// {
	// 	$this->assertEquals(NULL, $this->load->database());
	// 	$this->assertEquals(NULL, $this->load->dbutil());
	// }

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::view
	 */
	public function test_load_view()
	{
		// Create views directory with test view
		$view = 'unit_test_view';
		$this->ci_vfs_create($view, 'This is my test page.  <?php echo $hello; ?>', $this->ci_app_root, 'views');

		// Use the optional return parameter in this test, so the view is not
		// run through the output class.
		$out = $this->load->view($view, array('hello' => 'World!'), TRUE);
		$this->assertEquals('This is my test page.  World!', $out);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::view
	 */
	public function test_non_existent_view()
	{
		// Set expected exception
		$view = 'ci_test_nonexistent_view';
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: '.$view.'.php'
		);

		// Try to load the view
		$this->load->view($view, array('foo' => 'bar'));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::controller
	 */
	public function test_controller()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('ctlr');

		// Create controller in VFS
		$class = 'TestCtlr';
		$method = 'test_handler';
		$name = strtolower($class);
		$this->ci_vfs_create($name, $this->_empty($class), $this->ci_app_root, 'controllers');

		// Create route stack to return
		$this->ci_obj->router->_valid_route = array(
			$this->ci_app_path,
			'',
			$class,
			$method
		);

		// Did the controller get loaded?
		$this->assertTrue($this->load->controller($class.'/'.$method));
		$this->assertObjectHasAttribute($name, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $name, $this->ci_obj);

		// Did the correct method get called?
		$this->assertEquals($method, $this->ci_obj->_ctlr_ran);

		// Do we get FALSE for an empty route?
		$this->assertFalse($this->load->controller(''));

		// Do we get FALSE for an invalid stack?
		$this->assertFalse($this->load->controller(array('foo', 'bar', 'baz')));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::controller
	 */
	public function test_controller_result()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('ctlr');

		// Create controller in subdirectory of VFS
		$subdir = 'special';
		$class = 'TestResultCtlr';
		$method = 'result_handler';
		$this->ci_vfs_create(strtolower($class), $this->_empty($class), $this->ci_app_root, 'controllers', $subdir);

		// Create route stack to pass
		$route = array(
			$this->ci_app_path,
			$subdir.'/',
			$class,
			$method
		);

		// Set object name and method result
		$name = 'flock';
		$result = 'I ran (so far away)';
		$this->ci_obj->_ctlr_result = $result;

		// Was the result returned?
		$this->assertEquals($result, $this->load->controller($route, $name, TRUE));

		// Did the controller get loaded?
		$this->assertObjectHasAttribute($name, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $name, $this->ci_obj);

		// Did the correct method get called?
		$this->assertEquals($method, $this->ci_obj->_ctlr_ran);

		// Does it skip the call if we tell it to?
		$route[3] = 'not_a_handler';
		$this->ci_obj->_ctlr_ran = FALSE;
		$this->assertTrue($this->load->controller($route, $name, FALSE));
		$this->assertFalse($this->ci_obj->_ctlr_ran);

		// Do we get NULL as a result for an empty route?
		$this->assertNull($this->load->controller('', NULL, TRUE));

		// Do we get NULL as a result for an invalid stack?
		$this->assertNull($this->load->controller(array('foo', 'bar', 'baz'), NULL, TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::controller
	 */
	public function test_controller_output()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('ctlr');

		// Create controller in VFS
		$class = 'TestOutCtlr';
		$method = 'output_handler';
		$name = strtolower($class);
		$this->ci_vfs_create($name, $this->_empty($class), $this->ci_app_root, 'controllers');

		// Create route stack to pass
		$route = array(
			$this->ci_app_path,
			'',
			$class,
			$method
		);

		// Create output to generate
		$expected = 'Don\'t panic.';
		$this->ci_obj->_ctlr_output = $expected;

		// Did the controller get loaded?
		$this->assertTrue($this->load->controller_output($out, $route));
		$this->assertObjectHasAttribute($name, $this->ci_obj);
		$this->assertAttributeInstanceOf($class, $name, $this->ci_obj);

		// Did the correct method get called?
		$this->assertEquals($method, $this->ci_obj->_ctlr_ran);

		// Was the output returned?
		$this->assertEquals($expected, $out);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::file
	 */
	public function test_file()
	{
		// Create subdirectory with test file
		$dir = 'views';
		$file = 'ci_test_mock_file';
		$content = 'Here is a test file, which we will load now.';
		$this->ci_vfs_create($file, $content, $this->ci_app_root, $dir);

		// Just like load->view(), take the output class out of the mix here.
		$out = $this->load->file($this->ci_app_path.$dir.'/'.$file.'.php', TRUE);
		$this->assertEquals($content, $out);

		// Test non-existent file
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: ci_test_file_not_exists'
		);

		$this->load->file('ci_test_file_not_exists', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::vars
	 */
	public function test_vars()
	{
		$this->assertNull($this->load->vars(array('foo' => 'bar')));
		$this->assertNull($this->load->vars('foo', 'bar'));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::helper
	 */
	public function test_helper()
	{
		// Create helper directory in app path with test helper
		$helper = 'test';
		$func = '_my_helper_test_func';
		$content = '<?php function '.$func.'() { return true; } ';
		$this->ci_vfs_create($helper.'_helper', $content, $this->ci_app_root, 'helpers');

		// Load helper
		$this->assertNull($this->load->helper($helper));
		$this->assertTrue(function_exists($func), $func.' does not exist');

		// Test non-existent helper
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: helpers/bad_helper.php'
		);

		$this->load->helper('bad');
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::helper
	 */
	public function test_loading_multiple_helpers()
	{
		// Create helper directory in base path with test helpers
		$helpers = array();
		$funcs = array();
		$files = array();
		for ($i = 1; $i <= 3; ++$i) {
			$helper = 'test'.$i;
			$helpers[] = $helper;
			$func = '_my_helper_test_func'.$i;
			$funcs[] = $func;
			$files[$helper.'_helper'] = '<?php function '.$func.'() { return true; } ';
		}
		$this->ci_vfs_create($files, NULL, $this->ci_base_root, 'helpers');

		// Load helpers
		$this->assertEquals(NULL, $this->load->helpers($helpers));

		// Verify helper existence
		foreach ($funcs as $func) {
			$this->assertTrue(function_exists($func), $func.' does not exist');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::language
	 */
	public function test_language()
	{
		// Create mock Lang class with load stub
		$class = 'Mock_Load_Lang';
		$prop = '_file';
		eval('class '.$class.' { public function load($file, $lang) { $this->'.$prop.' = $file; } }');
		$this->ci_instance_var('lang', new $class());

		// Does the right file get loaded?
		$file = 'test';
		$this->assertNull($this->load->language($file));
		$this->assertObjectHasAttribute($prop, $this->ci_obj->lang);
		$this->assertEquals($file, $this->ci_obj->lang->$prop);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::add_package_path
	 * @covers	CI_Loader::get_package_paths
	 * @covers	CI_Loader::remove_package_path
	 */
	public function test_packages()
	{
		// Create third-party directory in app path with model
		$dir = 'third-party';
		$lib = 'unit_test_package';
		$class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create($lib, $this->_empty($class), $this->ci_app_root, $dir);

		// Test failed load without path
		$this->setExpectedException('RuntimeException', 'CI Error: Unable to load the requested class: '.$lib);
		$this->load->library($lib);

		// Clear exception and get paths
		$this->setExpectedException(NULL);
		$paths = $this->load->get_package_paths(TRUE);

		// Add path and verify
		$path = $this->ci_app_path.$dir;
		$this->assertNull($this->load->add_package_path($path));
		$this->assertContains($path, $this->load->get_package_paths(TRUE));

		// Test successful load
		$this->assertNull($this->load->library($lib));
		$this->assertTrue(class_exists($class), $class.' does not exist');

		// Remove path and verify restored paths
		$this->assertNull($this->load->remove_package_path($path));
		$this->assertEquals($paths, $this->load->get_package_paths(TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::config
	 */
	public function test_load_config()
	{
		$this->assertNull($this->load->config('config', FALSE));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers	CI_Loader::_ci_autoloader
	 */
	public function test_autoloader()
	{
		// Create helper directory in app path with test helper
		$helper = 'autohelp';
		$hlp_func = '_autohelp_test_func';
		$content = '<?php function '.$hlp_func.'() { return true; } ';
		$this->ci_vfs_create($helper.'_helper', $content, $this->ci_app_root, 'helpers');

		// Create libraries directory in base path with test library
		$lib = 'autolib';
		$lib_class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create($lib, $this->_empty($lib_class), $this->ci_base_root, 'libraries');

		// Create libraries subdirectory with test driver
		$drv = 'autodrv';
		$subdir = ucfirst($drv);
		$drv_class = 'CI_'.$subdir;
		$this->ci_vfs_create($drv, $this->_empty($drv_class), $this->ci_base_root, 'libraries', $subdir);

		// Create package directory in app path with model
		$dir = 'testdir';
		$path = $this->ci_app_path.$dir.'/';
		$model = 'automod';
		$mod_class = ucfirst($model);
		$this->ci_vfs_create($model, $this->_empty($mod_class), $this->ci_app_root, $dir, 'models');

		// Autoload path since autoloaded packages are handled during bootstrapping
		$this->load->add_package_path($path);

		// Create autoloader config
		$cfg = array(
			'helper' => array($helper),
			'libraries' => array($lib),
			'drivers' => array($drv),
			'model' => array($model),
		);
		$this->ci_obj->_autoload = $cfg;

		// Run autoloader
		$this->load->autoload();

		// Verify path
		$this->assertContains($path, $this->load->get_package_paths());

		// Verify helper
		$this->assertTrue(function_exists($hlp_func), $hlp_func.' does not exist');

		// Verify library
		$this->assertTrue(class_exists($lib_class), $lib_class.' does not exist');
		$this->assertObjectHasAttribute($lib, $this->ci_obj);
		$this->assertAttributeInstanceOf($lib_class, $lib, $this->ci_obj);

		// Verify driver
		$this->assertTrue(class_exists($drv_class), $drv_class.' does not exist');
		$this->assertObjectHasAttribute($drv, $this->ci_obj);
		$this->assertAttributeInstanceOf($drv_class, $drv, $this->ci_obj);

		// Verify model
		$this->assertTrue(class_exists($mod_class), $mod_class.' does not exist');
		$this->assertObjectHasAttribute($model, $this->ci_obj);
		$this->assertAttributeInstanceOf($mod_class, $model, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	private function _empty($class, $base = '')
	{
		if ( ! empty($base))
		{
			$class .= ' extends '.$base;
		}
		return '<?php class '.$class.' { } ';
	}

}
