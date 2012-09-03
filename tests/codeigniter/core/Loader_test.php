<?php

class Loader_test extends CI_TestCase {

	private $ci_obj;

	public function set_up()
	{
		// Create VFS tree of loader locations
		$this->root = vfsStream::setup();
		$this->base_root = vfsStream::newDirectory('system')->at($this->root);
		$this->app_root = vfsStream::newDirectory('application')->at($this->root);
		$this->view_root = vfsStream::newDirectory('views')->at($this->root);

		// Get VFS path URLs
		$this->base_path = vfsStream::url('system').'/';
		$this->app_path = vfsStream::url('application').'/';
		$this->view_path = vfsStream::url('views').'/';

		// Set up config
		$this->ci_set_config('subclass_prefix', 'MY_');

		// Get CI instance and set empty autoload.php contents and path sources
		$this->ci_obj = $this->ci_instance();
		$this->ci_obj->_autoload = array();
		$this->ci_obj->base_paths = array($this->app_path, $this->base_path);
		$this->ci_obj->app_paths = array($this->app_path);

		// Instantiate a new loader
		$this->load = new Mock_Core_Loader();
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
		$this->_create_content('libraries', $lib, '<?php class '.$class.' { } ', NULL, TRUE);

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
		$content = '<?php class '.$class.
			' { public function __construct($params = NULL) { $this->config = $params; } } ';
		$this->_create_content('libraries', $lib, $content, NULL, TRUE);
		
		// Create config to be loaded
		// For isolation, we just set the contents in CI_TestConfig to be retrieved
		$cfg = array(
			'foo' => 'bar',
			'bar' => 'baz',
			'baz' => false
		);
		$this->ci_obj->config->to_get = $cfg;
		
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
		$this->_create_content('libraries', $lib, '<?php class '.$class.' {} ');

		// Load library
		$this->assertNull($this->load->library($lib));

		// Was the model class instantiated?
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
		$this->_create_content('libraries', $driver, '<?php class '.$class.' { } ', $dir, TRUE);
		
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
		$this->_create_content('models', $model, '<?php class '.$class.' extends CI_Model {} ');

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
		$this->_create_content('views', $view, 'This is my test page.  <?php echo $hello; ?>');

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
		$this->_create_content($dir, $file, $content);

		// Just like load->view(), take the output class out of the mix here.
		$out = $this->load->file($this->app_path.$dir.'/'.$file.'.php', TRUE);
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
		$this->_create_content('helpers', $helper.'_helper', '<?php function '.$func.'() { return true; } ');
		
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
		$this->_create_content('helpers', $files, NULL, NULL, TRUE);
		
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
		$this->_create_content($dir, $lib, '<?php class '.$class.' { } ');
		
		// Test failed load without path
		$this->setExpectedException('RuntimeException', 'CI Error: Unable to load the requested class: '.$lib);
		$this->load->library($lib);
		
		// Clear exception and get paths
		$this->setExpectedException(NULL);
		$paths = $this->load->get_package_paths(TRUE);
		
		// Add path and verify
		$path = $this->app_path.$dir;
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
		$this->_create_content('helpers', $helper.'_helper', '<?php function '.$hlp_func.'() { return true; } ');

		// Create libraries directory in base path with test library
		$lib = 'autolib';
		$lib_class = 'CI_'.ucfirst($lib);
		$this->_create_content('libraries', $lib, '<?php class '.$lib_class.' { } ', NULL, TRUE);

		// Create libraries subdirectory with test driver
		// Since libraries/ now exists, we have to look it up and
		// add the subdir directly instead of using _create_content
		$drv = 'autodrv';
		$subdir = ucfirst($drv);
		$drv_class = 'CI_'.$subdir;
		$tree = array(
			$subdir => array($drv.'.php' => '<?php class '.$drv_class.' { } ')
		);
		vfsStream::create($tree, $this->base_root->getChild('libraries'));

		// Create package directory in app path with model
		$dir = 'testdir';
		$path = $this->app_path.$dir.'/';
		$model = 'automod';
		$mod_class = ucfirst($model);
		$this->_create_content($dir, $model, '<?php class '.$mod_class.' { } ', 'models');

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
		$this->assertAttributeInstanceOf($lib_class, $lib, $this->ci_obj);

		// Verify driver
		$this->assertTrue(class_exists($drv_class), $drv_class.' does not exist');
		$this->assertAttributeInstanceOf($drv_class, $drv, $this->ci_obj);

		// Verify model
		$this->assertTrue(class_exists($mod_class), $mod_class.' does not exist');
		$this->assertAttributeInstanceOf($mod_class, $model, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	private function _create_content($dir, $file, $content, $sub = NULL, $base = FALSE)
	{
		// Create structure containing directory
		$tree = array($dir => array());

		// Check for subdirectory
		if ($sub) {
			// Add subdirectory to tree and get reference
			$tree[$dir][$sub] = array();
			$leaf =& $tree[$dir][$sub];
		}
		else {
			// Get reference to main directory
			$leaf =& $tree[$dir];
		}

		// Check for multiple files
		if (is_array($file)) {
			// Add multiple files to directory
			foreach ($file as $name => $data) {
				$leaf[$name.'.php'] = $data;
			}
		}
		else {
			// Add single file with content
			$leaf[$file.'.php'] = $content;
		}

		// Create structure under app or base path
		vfsStream::create($tree, $base ? $this->base_root : $this->app_root);
	}

}
