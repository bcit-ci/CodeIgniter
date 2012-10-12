<?php

class Loader_test extends CI_TestCase {

	private $ci_obj;

	public function set_up()
	{
		// Instantiate a new loader
		$this->load = new Mock_Core_Loader();

		// Get CI instance
		$this->ci_obj = $this->ci_instance();

		// Set subclass prefix
		$this->ci_set_config('subclass_prefix', 'MY_');
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::library
	 */
	public function test_library()
	{
		// Create libraries directory with test library
		$lib = 'unit_test_lib';
		$class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create($lib, '<?php class '.$class.' { }', $this->ci_base_root, 'libraries');

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
	 * @covers CI_Loader::library
	 */
	public function test_library_config()
	{
		// Create libraries directory with test library
		$lib = 'unit_test_config_lib';
		$class = 'CI_'.ucfirst($lib);
		$content = '<?php class '.$class.' { public function __construct($params) { $this->config = $params; } }';
		$this->ci_vfs_create($lib, $content, $this->ci_base_root, 'libraries');

		// Create config file
		$cfg = array(
			'foo' => 'bar',
			'bar' => 'baz',
			'baz' => false
		);
		$this->ci_vfs_create($lib, '<?php $config = '.var_export($cfg, TRUE).';', $this->ci_app_root, 'config');

		// Test object name and config
		$obj = 'testy';
		$this->assertNull($this->load->library($lib, NULL, $obj));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);
		$this->assertEquals($cfg, $this->ci_obj->$obj->config);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::library
	 */
	public function test_load_library_in_application_dir()
	{
		// Create libraries directory in app path with test library
		$lib = 'super_test_library';
		$class = ucfirst($lib);
		$this->ci_vfs_create($lib, '<?php class '.$class.' { }', $this->ci_app_root, 'libraries');

		// Load library
		$this->assertNull($this->load->library($lib));

		// Was the model class instantiated.
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::driver
	 */
	public function test_driver()
	{
		// Create libraries directory with test driver
		$driver = 'unit_test_driver';
		$dir = ucfirst($driver);
		$class = 'CI_'.$dir;
		$content = '<?php class '.$class.' { } ';
		$this->ci_vfs_create($driver, $content, $this->ci_base_root, 'libraries/'.$dir);

		// Test loading as an array.
		$this->assertNull($this->load->driver(array($driver)));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $driver, $this->ci_obj);

		// Test loading as a library with a name
		$obj = 'testdrive';
		$this->assertNull($this->load->library($driver, NULL, $obj));
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);

		// Test no driver given
		$this->assertFalse($this->load->driver());

		// Test a string given to params
		$this->assertNull($this->load->driver($driver, ' '));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::model
	 */
	public function test_non_existent_model()
	{
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to locate the model you have specified: ci_test_nonexistent_model.php'
		);

		$this->load->model('ci_test_nonexistent_model.php');
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::model
	 */
	public function test_models()
	{
		$this->ci_set_core_class('model', 'CI_Model');

		// Create models directory with test model
		$model = 'unit_test_model';
		$class = ucfirst($model);
		$content = '<?php class '.$class.' extends CI_Model {} ';
		$this->ci_vfs_create($model, $content, $this->ci_app_root, 'models');

		// Load model
		$this->assertNull($this->load->model($model));

		// Was the model class instantiated.
		$this->assertTrue(class_exists($class));

		// Test no model given
		$this->assertNull($this->load->model(''));
	}

	// --------------------------------------------------------------------

	// public function testDatabase()
	// {
	// 	$this->assertEquals(NULL, $this->load->database());
	// 	$this->assertEquals(NULL, $this->load->dbutil());
	// }

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::view
	 */
	public function test_load_view()
	{
		$this->ci_set_core_class('output', 'CI_Output');

		// Create views directory with test view
		$view = 'unit_test_view';
		$content = 'This is my test page.  <?php echo $hello; ?>';
		$this->ci_vfs_create($view, $content, $this->ci_app_root, 'views');

		// Use the optional return parameter in this test, so the view is not
		// run through the output class.
		$out = $this->load->view($view, array('hello' => "World!"), TRUE);
		$this->assertEquals('This is my test page.  World!', $out);
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::view
	 */
	public function test_non_existent_view()
	{
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: ci_test_nonexistent_view.php'
		);

		$this->load->view('ci_test_nonexistent_view', array('foo' => 'bar'));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::file
	 */
	public function test_file()
	{
		// Create views directory with test file
		$dir = 'views';
		$file = 'ci_test_mock_file';
		$content = 'Here is a test file, which we will load now.';
		$this->ci_vfs_create($file, $content, $this->ci_app_root, $dir);

		// Just like load->view(), take the output class out of the mix here.
		$out = $this->load->file(APPPATH.$dir.'/'.$file.'.php', TRUE);
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
	 * @covers CI_Loader::vars
	 */
	public function test_vars()
	{
		$this->assertNull($this->load->vars(array('foo' => 'bar')));
		$this->assertNull($this->load->vars('foo', 'bar'));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::helper
	 */
	public function test_helper()
	{
		// Create helper directory in app path with test helper
		$helper = 'test';
		$func = '_my_helper_test_func';
		$content = '<?php function '.$func.'() { return true; } ';
		$this->ci_vfs_create($helper.'_helper', $content, $this->ci_app_root, 'helpers');

		// Load helper
		$this->assertEquals(NULL, $this->load->helper($helper));
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
	 * @covers CI_Loader::helper
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

	// public function testLanguage()
	// {
	// 	$this->assertEquals(NULL, $this->load->language('test'));
	// }

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::add_package_path
	 * @covers CI_Loader::get_package_paths
	 * @covers CI_Loader::remove_package_path
	 */
	public function test_packages()
	{
		// Create third-party directory in app path with model
		$dir = 'third-party';
		$lib = 'unit_test_package';
		$class = 'CI_'.ucfirst($lib);
		$content = '<?php class '.$class.' { } ';
		$this->ci_vfs_create($lib, $content, $this->ci_app_root, $dir);

		// Test failed load without path
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested class: '.$lib
		);
		$this->load->library($lib);

		// Clear exception and get paths
		$this->setExpectedException(NULL);
		$paths = $this->load->get_package_paths(TRUE);

		// Add path and verify
		$path = APPPATH.$dir;
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
	 * @covers CI_Loader::config
	 */
	public function test_load_config()
	{
		$this->assertNull($this->load->config('config', FALSE));
	}

	// --------------------------------------------------------------------

	/**
	 * @covers CI_Loader::_ci_autoloader
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
		$this->ci_vfs_create($lib, '<?php class '.$lib_class.' { }', $this->ci_base_root, 'libraries');

		// Create test driver
		$drv = 'autodrv';
		$subdir = ucfirst($drv);
		$drv_class = 'CI_'.$subdir;
		$this->ci_vfs_create($drv, '<?php class '.$drv_class.' { }', $this->ci_base_root, 'libraries/'.$subdir);

		// Create package directory in app path with model
		$dir = 'testdir';
		$path = APPPATH.$dir.'/';
		$model = 'automod';
		$mod_class = ucfirst($model);
		$this->ci_vfs_create($model, '<?php class '.$mod_class.' { }', $this->ci_app_root, $dir.'/models');

		// Create autoloader config
		$cfg = array(
			'packages' => array($path),
			'helper' => array($helper),
			'libraries' => array($lib),
			'drivers' => array($drv),
			'model' => array($model),
			'config' => array()
		);
		$this->ci_vfs_create('autoload', '<?php $autoload = '.var_export($cfg, TRUE).';', $this->ci_app_root, 'config');

		// Run autoloader
		$this->load->autoload();

		// Verify path
		$this->assertContains($path, $this->load->get_package_paths());

		// Verify helper
		$this->assertTrue(function_exists($hlp_func), $hlp_func.' does not exist');

		// Verify library
		$this->assertTrue(class_exists($lib_class), $lib_class.' does not exist');
		$this->assertAttributeInstanceOf($lib_class, $lib, $this->ci_obj);

		// Verify driver
		$this->assertTrue(class_exists($drv_class), $drv_class.' does not exist');
		$this->assertAttributeInstanceOf($drv_class, $drv, $this->ci_obj);

		// Verify model
		$this->assertTrue(class_exists($mod_class), $mod_class.' does not exist');
		$this->assertAttributeInstanceOf($mod_class, $model, $this->ci_obj);
	}

}