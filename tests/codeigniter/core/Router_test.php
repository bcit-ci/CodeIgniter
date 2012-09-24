<?php

class Router_test extends CI_TestCase {
	private $ci;

	/**
	 * Set up before each test
	 */
	public function set_up()
	{
		// Get instance object
		$this->ci = $this->ci_instance();

		// Create router
		$this->router = new Mock_Core_Router();
	}

	/**
	 * Test validating route
	 *
	 * @covers	CI_Router::validate_route
	 */
	public function test_validate_route()
	{
		// Mock loader with package paths
		$this->_mock_loader();
		$alt_dir = 'alternative';
		$alt_root = $this->ci_vfs_mkdir($alt_dir);
		$alt_path = $this->ci_vfs_path($alt_dir.'/');
		$this->ci->load->paths = array($alt_path, $this->ci_app_path);

		// Create controller in app path
		$dir = 'controllers';
		$ctlr = 'app_ctlr';
		$this->ci_vfs_create($ctlr, '', $this->ci_app_root, $dir);

		// Is the default function routed?
		$expect = array($this->ci_app_path, '', $ctlr, 'index');
		$this->assertEquals($expect, $this->router->validate_route($ctlr));

		// Create a controller in a subdirectory of the alternate path
		$sub = 'bass';
		$ctlr2 = 'sub_ctlr';
		$func2 = 'drop';
		$this->ci_vfs_create($ctlr2, '', $alt_root, array($dir, $sub));

		// Do the alternate path and subdirectory work?
		$expect = array($alt_path, $sub, $ctlr2, $func2);
		$this->assertEquals($expect, $this->router->validate_route(array($sub, $ctlr2, $func2)));

		// Set default controller with argument
		$def_ctlr = 'foo';
		$def_func = 'bar';
		$def_arg = 'baz';
		$this->ci_vfs_create($def_ctlr, '', $this->ci_app_root, $dir);
		$this->router->default_ctlr($def_ctlr.'/'.$def_func.'/'.$def_arg);

		// Do the default and argument work?
		$expect = array($this->ci_app_path, '', $def_ctlr, $def_func, $def_arg);
		$this->assertEquals($expect, $this->router->validate_route(''));

		// Set route
		$route3 = 'remap/request';
		$ctlr3 = 'real_ctlr';
		$func3 = 'action';
		$this->router->routes = array($route3 => $ctlr3.'/'.$func3);

		// Create actual controller
		$this->ci_vfs_create($ctlr3, '', $this->ci_app_root, $dir);

		// Does the request get remapped?
		$expect = array($this->ci_app_path, '', $ctlr3, $func3);
		$this->assertEquals($expect, $this->router->validate_route($route3));

		// Set route with number arg
		$rctlr = 'remap';
		$rfunc = 'request';
		$route4 = $rctlr.'/'.$rfunc;
		$ctlr4 = 'real_ctlr';
		$func4 = 'action';
		$this->router->routes = array($route4.'/(:num)' => $ctlr4.'/'.$func4.'/$1');

		// Create actual controller
		$this->ci_vfs_create($ctlr4, '', $this->ci_app_root, $dir);

		// Does the argument get passed?
		$arg4 = '42';
		$expect = array($this->ci_app_path, '', $ctlr4, $func4, $arg4);
		$this->assertEquals($expect, $this->router->validate_route($route4.'/'.$arg4));

		// Create remap controller
		$this->ci_vfs_create($rctlr, '', $this->ci_app_root, $dir);

		// Is the route ignored for a non-number?
		$arg = 'foo';
		$expect = array($this->ci_app_path, '', $rctlr, $rfunc, $arg);
		$this->assertEquals($expect, $this->router->validate_route($route4.'/'.$arg));

		// Does a non-existent route fail?
		$this->assertFalse($this->router->validate_route('non_ctlr'));
	}

	/**
	 * Test default routing
	 *
	 * @covers	CI_Router::_set_routing
	 */
	public function test_default_routing()
	{
		// Load mock Config
		$this->ci_set_config();

		// Mock up URI
		$this->_mock_uri();

		// Mock loader with package paths
		$this->_mock_loader();
		$this->ci->load->paths = array($this->ci_app_path);

		// Set up routes config with default
		$stack = array('main', 'handler');
		$default = implode('/', $stack);
		$routes = array('default_controller' => $default);
		$this->ci->config->to_get($routes);

		// Create default controller
		$this->ci_vfs_create($stack[0], '', $this->ci_app_root, 'controllers');

		// Set routing
		$this->router->_set_routing();

		// Did the default get set?
		$this->assertEquals($default, $this->router->default_ctlr());

		// Did the right URI methods get called?
		$expect = array('_fetch_uri_string', '_remove_url_suffix', '_explode_segments', '_reindex_segments');
		$this->assertObjectHasAttribute('called', $this->ci->uri);
		$this->assertEquals($expect, $this->ci->uri->called);

		// Did the route stack get set correctly?
		$expect = array($this->ci_app_path, '', $stack[0], $stack[1]);
		$this->assertEquals($expect, $this->router->fetch_route());

		// Are the routed segments correct?
		$this->assertEquals($stack, $this->ci->uri->rsegments);
	}

	/**
	 * Test query string routing
	 *
	 * @covers	CI_Router::_set_routing
	 */
	public function test_query_routing()
	{
		// Load mock Config with triggers
		$ctrigger = 'ctlr';
		$dtrigger = 'dir';
		$ftrigger = 'func';
		$config = array(
			'enable_query_strings' => TRUE,
			'controller_trigger' => $ctrigger,
			'directory_trigger' => $dtrigger,
			'function_trigger' => $ftrigger
		);
		$this->ci_set_config($config);

		// Mock up URI
		$this->_mock_uri();

		// Mock loader with package paths
		$this->_mock_loader();
		$this->ci->load->paths = array($this->ci_app_path);

		// Set up routes config
		$routes = array('default_controller' => '');
		$this->ci->config->to_get($routes);

		// Set query args
		$dir = 'qdir';
		$ctlr = 'query_ctlr';
		$func = 'get';
		$oldget = $_GET;
		$_GET = array($dtrigger => $dir, $ctrigger => $ctlr, $ftrigger => $func);

		// Create controller
		$this->ci_vfs_create($ctlr, '', $this->ci_app_root, array('controllers', $dir));

		// Set routing
		$this->router->_set_routing();

		// Did the route stack get set?
		$expect = array($this->ci_app_path, $dir.'/', $ctlr, $func);
		$this->assertEquals($expect, $this->router->fetch_route());

		// Reset _GET
		$_GET = $oldget;
	}

	/**
	 * Test route remapping
	 *
	 * @covers	CI_Router::_set_routing
	 */
	public function test_remap_routing()
	{
		// Load mock Config
		$this->ci_set_config();

		// Mock up URI
		$this->_mock_uri();

		// Mock loader with package paths
		$this->_mock_loader();
		$this->ci->load->paths = array($this->ci_app_path);

		// Set up routes config
		$route = 'main/path';
		$arg = 'switch';
		$ctlr = 'other';
		$func = 'handler';
		$routes = array(
			'default_controller' => '',
			$route.'/(:any)' => $ctlr.'/'.$func.'/$1',
		);
		$this->ci->config->to_get($routes);

		// Create controller and set segments
		$this->ci_vfs_create($ctlr, '', $this->ci_app_root, 'controllers');
		$this->ci->uri->segments = array($ctlr, $func, $arg);

		// Set routing
		$this->router->_set_routing();

		// Did the route stack get set correctly?
		$expect = array($this->ci_app_path, '', $ctlr, $func, $arg);
		$this->assertEquals($expect, $this->router->fetch_route());
	}

	/**
	 * Test getting error route
	 *
	 * @covers	CI_Router::get_error_route
	 */
	public function test_error_route()
	{
		// Mock loader with package paths
		$this->_mock_loader();
		$this->ci->load->paths = array($this->ci_app_path);

		// Do we get FALSE with no error route?
		$this->assertFalse($this->router->get_error_route('error_override'));

		// Set up error override
		$dir = 'controllers';
		$ectlr = 'error_ctlr';
		$efunc = 'error_func';
		$eroute = 'error_override';
		$this->ci_vfs_create($ectlr, '', $this->ci_app_root, $dir);
		$this->router->routes = array($eroute => $ectlr.'/'.$efunc);

		// Do we get the error route when set?
		$expect = array($this->ci_app_path, '', $ectlr, $efunc);
		$this->assertEquals($expect, $this->router->get_error_route($eroute));

		// Set up 404 override
		$ctlr = 'e404_ctlr';
		$func = 'e404_func';
		$route = '404_override';
		$this->ci_vfs_create($ctlr, '', $this->ci_app_root, $dir);
		$this->router->routes = array($route => $ctlr.'/'.$func);

		// Do we get the error route when set?
		$expect = array($this->ci_app_path, '', $ctlr, $func);
		$this->assertEquals($expect, $this->router->get_error_route($route));
	}

	/**
	 * Test set/fetch methods
	 *
	 * @covers	CI_Router::set_path
	 * @covers	CI_Router::fetch_path
	 * @covers	CI_Router::set_directory
	 * @covers	CI_Router::fetch_directory
	 * @covers	CI_Router::set_class
	 * @covers	CI_Router::fetch_class
	 * @covers	CI_Router::set_method
	 * @covers	CI_Router::fetch_method
	 * @covers	CI_Router::fetch_route
	 */
	public function test_set_fetch()
	{
		// Set/fetch path
		$path = $this->ci_vfs_path('application/');
		$this->router->set_path($path);
		$this->assertEquals($path, $this->router->fetch_path());

		// Set/fetch directory - should add slash
		$directory = 'custom';
		$this->router->set_directory($directory);
		$this->assertEquals($directory.'/', $this->router->fetch_directory());

		// Set/fetch class
		$class = 'my_ctlr';
		$this->router->set_class($class);
		$this->assertEquals($class, $this->router->fetch_class());

		// Set/fetch method
		$method = 'unusual';
		$this->router->set_method($method);
		$this->assertEquals($method, $this->router->fetch_method());

		// Do we get all the parts in the stack?
		$expect = array($path, $directory.'/', $class, $method);
		$this->assertEquals($expect, $this->router->fetch_route());
	}

	/**
	 * Test setting overrides
	 *
	 * @covers	CI_Router::_set_overrides
	 */
	public function test_set_overrides()
	{
		// Load mock Config
		$this->ci_set_config();

		// Mock up URI
		$this->_mock_uri();

		// Mock loader with package paths
		$this->_mock_loader();
		$alt_dir = 'alternative';
		$alt_root = $this->ci_vfs_mkdir($alt_dir);
		$alt_path = $this->ci_vfs_path($alt_dir.'/');
		$this->ci->load->paths = array($alt_path, $this->ci_app_path);

		// Set up routes config
		$routes = array('default_controller' => '');
		$this->ci->config->to_get($routes);

		// Create controller and set segments
		$ctlr = 'orig_ctlr';
		$func = 'orig_func';
		$this->ci_vfs_create($ctlr, '', $this->ci_app_root, 'controllers');
		$this->ci->uri->segments = array($ctlr, $func);

		// Set routing
		$this->router->_set_routing();

		// Did the route stack get set correctly?
		$expect = array($this->ci_app_path, '', $ctlr, $func);
		$this->assertEquals($expect, $this->router->fetch_route());

		// Set overrides
		$opath = $alt_path;
		$odir = 'new_dir';
		$octlr = 'new_ctlr';
		$ofunc = 'new_func';
		$override = array(
			'path' => $opath,
			'directory' => $odir,
			'controller' => $octlr,
			'function' => $ofunc
		);
		$this->router->_set_overrides($override);

		// Did the route stack get updated?
		$expect = array($opath, $odir.'/', $octlr, $ofunc);
		$this->assertEquals($expect, $this->router->fetch_route());
	}

	/**
	 * Mock up URI
	 */
	private function _mock_uri()
	{
		// Create mock URI class
		$class = 'Router_URI';
		if ( ! class_exists($class))
		{
			$code = 'class '.$class.' { public $segments = array(); public $rsegments = array(); '.
				'public function _filter_uri($arg1) { return $arg1; } '.
				'public function _fetch_uri_string() { $this->called[] = __FUNCTION__; } '.
				'public function _remove_url_suffix() { $this->called[] = __FUNCTION__; } '.
				'public function _explode_segments() { $this->called[] = __FUNCTION__; } '.
				'public function _reindex_segments() { $this->called[] = __FUNCTION__; } '.
			'}';
			eval($code);
		}

		// Attach mock URI
		$this->ci->uri = new $class();
	}

	/**
	 * Mock up Loader
	 */
	private function _mock_loader()
	{
		// Create VFS tree
		$this->ci_vfs_setup();

		// Create mock Loader class
		$class = 'Router_Loader';
		if ( ! class_exists($class))
		{
			eval('class '.$class.' { public $paths; public function get_package_paths() { return $this->paths; } }');
		}
		$this->ci->load = new $class();
	}
}

