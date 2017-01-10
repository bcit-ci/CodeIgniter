<?php

class Loader_test extends CI_TestCase {

	private $ci_obj;

	public function set_up()
	{
		// Instantiate a new loader
		$loader = $this->ci_core_class('loader');
		$this->load = new $loader();

		// Get CI instance
		$this->ci_obj = $this->ci_instance();

		// Set subclass prefix
		$this->prefix = 'MY_';
		$this->ci_set_config('subclass_prefix', $this->prefix);
	}

	// --------------------------------------------------------------------

	public function test_library()
	{
		// Test getting CI_Loader object
		$this->assertInstanceOf('CI_Loader', $this->load->library(NULL));

		// Create library in VFS
		$lib = 'unit_test_lib';
		$class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create(ucfirst($lib), '<?php class '.$class.' { }', $this->ci_base_root, 'libraries');

		// Test is_loaded fail
		$this->assertFalse($this->load->is_loaded(ucfirst($lib)));

		// Test loading as an array.
		$this->assertInstanceOf('CI_Loader', $this->load->library(array($lib)));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);

		// Create library in VFS
		$lib = array('unit_test_lib' => 'unit_test_lib');

		// Test loading as an array (int).
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
		$this->assertTrue(class_exists($class), $class.' does not exist');

		// Test a string given to params
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib, ' '));

		// test non existent lib
		$lib = 'non_existent_test_lib';

		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested class: '.ucfirst($lib)
		);
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
	}

	// --------------------------------------------------------------------

	public function test_bad_library()
	{
		$lib = 'bad_test_lib';
		$this->ci_vfs_create(ucfirst($lib), '', $this->ci_app_root, 'libraries');
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Non-existent class: '.ucfirst($lib)
		);
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
	}

	// --------------------------------------------------------------------

	public function test_library_extension()
	{
		// Create library and extension in VFS
		$name = 'ext_test_lib';
		$lib = ucfirst($name);
		$class = 'CI_'.$lib;
		$ext = $this->prefix.$lib;
		$this->ci_vfs_create($lib, '<?php class '.$class.' { }', $this->ci_base_root, 'libraries');
		$this->ci_vfs_create($ext, '<?php class '.$ext.' extends '.$class.' { }', $this->ci_app_root, 'libraries');

		// Test loading with extension
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertTrue(class_exists($ext), $ext.' does not exist');
		$this->assertAttributeInstanceOf($class, $name, $this->ci_obj);
		$this->assertAttributeInstanceOf($ext, $name, $this->ci_obj);

		// Test reloading with object name
		$obj = 'exttest';
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib, NULL, $obj));
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);
		$this->assertAttributeInstanceOf($ext, $obj, $this->ci_obj);

		// Test reloading
		unset($this->ci_obj->$name);
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
		$this->assertObjectNotHasAttribute($name, $this->ci_obj);

		// Create baseless library
		$name = 'ext_baseless_lib';
		$lib = ucfirst($name);
		$class = $this->prefix.$lib;
		$this->ci_vfs_create($class, '<?php class '.$class.' { }', $this->ci_app_root, 'libraries');

		// Test missing base class
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested class: '.$lib
		);
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
	}

	// --------------------------------------------------------------------

	public function test_library_config()
	{
		// Create library in VFS
		$lib = 'unit_test_config_lib';
		$class = 'CI_'.ucfirst($lib);
		$content = '<?php class '.$class.' { public function __construct($params) { $this->config = $params; } }';
		$this->ci_vfs_create(ucfirst($lib), $content, $this->ci_base_root, 'libraries');

		// Create config file
		$cfg = array(
			'foo' => 'bar',
			'bar' => 'baz',
			'baz' => false
		);
		$this->ci_vfs_create($lib, '<?php $config = '.var_export($cfg, TRUE).';', $this->ci_app_root, 'config');

		// Test object name and config
		$obj = 'testy';
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib, NULL, $obj));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);
		$this->assertEquals($cfg, $this->ci_obj->$obj->config);

		// Test is_loaded
		$this->assertEquals($obj, $this->load->is_loaded(ucfirst($lib)));

		// Test to load another class with the same object name
		$lib = 'another_test_lib';
		$class = ucfirst($lib);
		$this->ci_vfs_create(ucfirst($lib), '<?php class '.$class.' { }', $this->ci_app_root, 'libraries');
		$this->setExpectedException(
			'RuntimeException',
			"CI Error: Resource '".$obj."' already exists and is not a ".$class." instance."
		);
		$this->load->library($lib, NULL, $obj);
	}

	// --------------------------------------------------------------------

	public function test_load_library_in_application_dir()
	{
		// Create library in VFS
		$lib = 'super_test_library';
		$class = ucfirst($lib);
		$this->ci_vfs_create(ucfirst($lib), '<?php class '.$class.' { }', $this->ci_app_root, 'libraries');

		// Load library
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));

		// Was the model class instantiated.
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $lib, $this->ci_obj);
	}

	// --------------------------------------------------------------------

	public function test_driver()
	{
		// Call the autoloader, to include system/libraries/Driver.php
		class_exists('CI_Driver_Library', TRUE);

		// Create driver in VFS
		$driver = 'unit_test_driver';
		$dir = ucfirst($driver);
		$class = 'CI_'.$dir;
		$content = '<?php class '.$class.' { } ';
		$this->ci_vfs_create(ucfirst($driver), $content, $this->ci_base_root, 'libraries/'.$dir);

		// Test loading as an array.
		$this->assertInstanceOf('CI_Loader', $this->load->driver(array($driver)));
		$this->assertTrue(class_exists($class), $class.' does not exist');
		$this->assertAttributeInstanceOf($class, $driver, $this->ci_obj);

		// Test loading as a library with a name
		$obj = 'testdrive';
		$this->assertInstanceOf('CI_Loader', $this->load->library($driver, NULL, $obj));
		$this->assertAttributeInstanceOf($class, $obj, $this->ci_obj);

		// Test a string given to params
		$this->assertInstanceOf('CI_Loader', $this->load->driver($driver, ' '));
	}

	// --------------------------------------------------------------------

	public function test_models()
	{
		$this->ci_set_core_class('model', 'CI_Model');

		// Create model in VFS
		$model = 'Unit_test_model';
		$content = '<?php class '.$model.' extends CI_Model {} ';
		$this->ci_vfs_create($model, $content, $this->ci_app_root, 'models');

		// Load model
		$this->assertInstanceOf('CI_Loader', $this->load->model($model));

		// Was the model class instantiated.
		$this->assertTrue(class_exists($model));
		$this->assertObjectHasAttribute($model, $this->ci_obj);

		// Test no model given
		$this->assertInstanceOf('CI_Loader', $this->load->model(''));
	}

	// --------------------------------------------------------------------

	public function test_model_subdir()
	{
		// Make sure base class is loaded - we'll test _ci_include later
		$this->ci_core_class('model');

		// Create modelin VFS
		$model = 'Test_sub_model';
		$base = 'CI_Model';
		$subdir = 'cars';
		$this->ci_vfs_create($model, '<?php class '.$model.' extends '.$base.' { }', $this->ci_app_root,
			array('models', $subdir));

		// Load model
		$name = 'testors';
		$this->assertInstanceOf('CI_Loader', $this->load->model($subdir.'/'.$model, $name));

		// Was the model class instantiated?
		$this->assertTrue(class_exists($model));
		$this->assertObjectHasAttribute($name, $this->ci_obj);
		$this->assertAttributeInstanceOf($base, $name, $this->ci_obj);
		$this->assertAttributeInstanceOf($model, $name, $this->ci_obj);

		// Test name conflict
		$obj = 'conflict';
		$this->ci_obj->$obj = new stdClass();
		$this->setExpectedException(
			'RuntimeException',
			'The model name you are loading is the name of a resource that is already being used: '.$obj
		);
		$this->load->model('not_real', $obj);
	}

	// --------------------------------------------------------------------

	public function test_non_existent_model()
	{
		$this->setExpectedException(
			'RuntimeException',
			'Unable to locate the model you have specified: Ci_test_nonexistent_model.php'
		);

		$this->load->model('ci_test_nonexistent_model.php');
	}

	// --------------------------------------------------------------------

	// public function testDatabase()
	// {
	// 	$this->assertInstanceOf('CI_Loader', $this->load->database());
	// 	$this->assertInstanceOf('CI_Loader', $this->load->dbutil());
	// }

	// --------------------------------------------------------------------

	public function test_load_view()
	{
		// Create view in VFS
		$view = 'unit_test_view';
		$var = 'hello';
		$value = 'World!';
		$content = 'This is my test page.  ';
		$this->ci_vfs_create($view, $content.'<?php echo $'.$var.';', $this->ci_app_root, 'views');

		// Test returning view
		$out = $this->load->view($view, array($var => $value), TRUE);
		$this->assertEquals($content.$value, $out);

		// Mock output class
		$output = $this->getMockBuilder('CI_Output')->setMethods(array('append_output'))->getMock();
		$output->expects($this->once())->method('append_output')->with($content.$value);
		$this->ci_instance_var('output', $output);

		// Test view output and $vars as an object
		$vars = new stdClass();
		$vars->$var = $value;
		$this->assertInstanceOf('CI_Loader', $this->load->view($view, $vars));
	}

	// --------------------------------------------------------------------

	public function test_non_existent_view()
	{
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: ci_test_nonexistent_view.php'
		);

		$this->load->view('ci_test_nonexistent_view', array('foo' => 'bar'));
	}

	// --------------------------------------------------------------------

	public function test_file()
	{
		// Create view in VFS
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

	public function test_vars()
	{
		$key1 = 'foo';
		$val1 = 'bar';
		$key2 = 'boo';
		$val2 = 'hoo';
		$this->assertInstanceOf('CI_Loader', $this->load->vars(array($key1 => $val1)));
		$this->assertInstanceOf('CI_Loader', $this->load->vars($key2, $val2));
		$this->assertEquals($val1, $this->load->get_var($key1));
		$this->assertEquals(array($key1 => $val1, $key2 => $val2), $this->load->get_vars());
	}

	// --------------------------------------------------------------------

	public function test_clear_vars()
	{
		$key1 = 'foo';
		$val1 = 'bar';
		$key2 = 'boo';
		$val2 = 'hoo';
		$this->assertInstanceOf('CI_Loader', $this->load->vars(array($key1 => $val1)));
		$this->assertInstanceOf('CI_Loader', $this->load->vars($key2, $val2));
		$this->assertEquals($val1, $this->load->get_var($key1));
		$this->assertEquals(array($key1 => $val1, $key2 => $val2), $this->load->get_vars());

		$this->assertInstanceOf('CI_Loader', $this->load->clear_vars());
		$this->assertEquals('', $this->load->get_var($key1));
		$this->assertEquals('', $this->load->get_var($key2));
	}

	// --------------------------------------------------------------------

	public function test_helper()
	{
		// Create helper in VFS
		$helper = 'test';
		$func = '_my_helper_test_func';
		$content = '<?php function '.$func.'() { return TRUE; } ';
		$this->ci_vfs_create($helper.'_helper', $content, $this->ci_base_root, 'helpers');

		// Create helper extension
		$exfunc = '_my_extension_func';
		$content = '<?php function '.$exfunc.'() { return TRUE; } ';
		$this->ci_vfs_create($this->prefix.$helper.'_helper', $content, $this->ci_app_root, 'helpers');

		// Load helper
		$this->assertInstanceOf('CI_Loader', $this->load->helper($helper));
		$this->assertTrue(function_exists($func), $func.' does not exist');
		$this->assertTrue(function_exists($exfunc), $exfunc.' does not exist');

		// Create baseless extension
		$ext = 'bad_ext';
		$this->ci_vfs_create($this->prefix.$ext.'_helper', '', $this->ci_app_root, 'helpers');

		// Test bad extension
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: helpers/'.$ext.'_helper.php'
		);
		$this->load->helper($ext);
	}

	// --------------------------------------------------------------------

	public function test_non_existent_helper()
	{
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested file: helpers/bad_helper.php'
		);
		$this->load->helper('bad');
	}

	// --------------------------------------------------------------------

	public function test_loading_multiple_helpers()
	{
		// Create helpers in VFS
		$helpers = array();
		$funcs = array();
		$files = array();
		for ($i = 1; $i <= 3; ++$i) {
			$helper = 'test'.$i;
			$helpers[] = $helper;
			$func = '_my_helper_test_func'.$i;
			$funcs[] = $func;
			$files[$helper.'_helper'] = '<?php function '.$func.'() { return TRUE; } ';
		}
		$this->ci_vfs_create($files, NULL, $this->ci_base_root, 'helpers');

		// Load helpers
		$this->assertInstanceOf('CI_Loader', $this->load->helpers($helpers));

		// Verify helper existence
		foreach ($funcs as $func) {
			$this->assertTrue(function_exists($func), $func.' does not exist');
		}
	}

	// --------------------------------------------------------------------

	public function test_language()
	{
		// Mock lang class and test load call
		$file = 'test';
		$lang = $this->getMockBuilder('CI_Lang')->setMethods(array('load'))->getMock();
		$lang->expects($this->once())->method('load')->with($file);
		$this->ci_instance_var('lang', $lang);
		$this->assertInstanceOf('CI_Loader', $this->load->language($file));
	}

	// --------------------------------------------------------------------

	public function test_packages()
	{
		// Create model in VFS package path
		$dir = 'third-party';
		$lib = 'unit_test_package';
		$class = ucfirst($lib);
		$this->ci_vfs_create(ucfirst($lib), '<?php class '.$class.' { }', $this->ci_app_root, array($dir, 'libraries'));

		// Get paths
		$paths = $this->load->get_package_paths(TRUE);

		// Test failed load without path
		$this->setExpectedException(
			'RuntimeException',
			'CI Error: Unable to load the requested class: '.ucfirst($lib)
		);
		$this->load->library($lib);

		// Add path and verify
		$path = APPPATH.$dir.'/';
		$this->assertInstanceOf('CI_Loader', $this->load->add_package_path($path));
		$this->assertContains($path, $this->load->get_package_paths(TRUE));

		// Test successful load
		$this->assertInstanceOf('CI_Loader', $this->load->library($lib));
		$this->assertTrue(class_exists($class), $class.' does not exist');

		// Add another path
		$path2 = APPPATH.'another/';
		$this->assertInstanceOf('CI_Loader', $this->load->add_package_path($path2));
		$this->assertContains($path2, $this->load->get_package_paths(TRUE));

		// Remove last path
		$this->assertInstanceOf('CI_Loader', $this->load->remove_package_path());
		$this->assertNotContains($path2, $this->load->get_package_paths(TRUE));

		// Remove path and verify restored paths
		$this->assertInstanceOf('CI_Loader', $this->load->remove_package_path($path));
		$this->assertEquals($paths, $this->load->get_package_paths(TRUE));
	}

	// --------------------------------------------------------------------

	public function test_remove_package_path()
	{
		$dir = 'third-party';
		$path = APPPATH.$dir.'/';
		$path2 = APPPATH.'another/';
		$paths = $this->load->get_package_paths(TRUE);

		$this->assertInstanceOf('CI_Loader', $this->load->add_package_path($path));
		$this->assertInstanceOf('CI_Loader', $this->load->remove_package_path($path));
		$this->assertEquals($paths, $this->load->get_package_paths(TRUE));

		$this->assertInstanceOf('CI_Loader', $this->load->add_package_path($path2));
		$this->assertInstanceOf('CI_Loader', $this->load->remove_package_path());
		$this->assertNotContains($path2, $this->load->get_package_paths(TRUE));
	}

	// --------------------------------------------------------------------

	public function test_load_config()
	{
		$cfg = 'someconfig';
		$this->assertTrue($this->load->config($cfg, FALSE));
		$this->assertContains($cfg, $this->ci_obj->config->loaded);
	}

	// --------------------------------------------------------------------

	public function test_initialize()
	{
		// Create helper in VFS
		$helper = 'autohelp';
		$hlp_func = '_autohelp_test_func';
		$content = '<?php function '.$hlp_func.'() { return TRUE; }';
		$this->ci_vfs_create($helper.'_helper', $content, $this->ci_app_root, 'helpers');

		// Create library in VFS
		$lib = 'autolib';
		$lib_class = 'CI_'.ucfirst($lib);
		$this->ci_vfs_create(ucfirst($lib), '<?php class '.$lib_class.' { }', $this->ci_base_root, 'libraries');

		// Create driver in VFS
		$drv = 'autodrv';
		$subdir = ucfirst($drv);
		$drv_class = 'CI_'.$subdir;
		$this->ci_vfs_create(ucfirst($drv), '<?php class '.$drv_class.' { }', $this->ci_base_root, array('libraries', $subdir));

		// Create model in VFS package path
		$dir = 'testdir';
		$path = APPPATH.$dir.'/';
		$model = 'Automod';
		$this->ci_vfs_create($model, '<?php class '.$model.' { }', $this->ci_app_root, array($dir, 'models'));

		// Create autoloader config
		$cfg = array(
			'packages' => array($path),
			'helper' => array($helper),
			'libraries' => array($lib),
			'drivers' => array($drv),
			'model' => array($model),
			'config' => array('config1', 'config2')
		);
		$this->ci_vfs_create('autoload', '<?php $autoload = '.var_export($cfg, TRUE).';', $this->ci_app_root, 'config');

		$this->load->initialize();

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
		$this->assertTrue(class_exists($model), $model.' does not exist');
		$this->assertAttributeInstanceOf($model, $model, $this->ci_obj);

		// Verify config calls
		$this->assertEquals($cfg['config'], $this->ci_obj->config->loaded);
	}
}
