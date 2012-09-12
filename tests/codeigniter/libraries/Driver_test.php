<?php

/**
 * Driver library base class unit test
 */
class Driver_test extends CI_TestCase {
	/**
	 * Set up test framework
	 */
	public function set_up()
	{
		// Create VFS directories
		$this->root = vfsStream::setup();
		$this->base_root = vfsStream::newDirectory('system')->at($this->root);
		$this->app_root = vfsStream::newDirectory('application')->at($this->root);
		$this->base_path = vfsStream::url('system/');
		$this->app_path = vfsStream::url('application/');
	}

	/**
	 * Test driver child loading
	 *
	 * @covers  CI_Driver_Library::load_driver
	 */
	public function test_load_driver()
	{
		// Create mock driver library
		$this->_mock_library();

		// Create driver file
		$driver = 'basic';
		$file = $this->name.'_'.$driver;
		$class = 'CI_'.$file;
		$prop = 'called';
		$content = '<?php class '.$class.' extends CI_Driver { public $'.$prop.' = FALSE; '.
			'public function decorate($parent) { $this->'.$prop.' = TRUE; } }';
		$this->_create_content($file, $content, $this->base_root, 'libraries/'.$this->name.'/drivers');

		// Make driver valid
		$this->lib->driver_list($driver);

		// Load driver
		$this->assertNotNull($this->lib->load_driver($driver));

		// Did lib name get set?
		$this->assertEquals($this->name, $this->lib->get_name());

		// Was driver loaded?
		$this->assertObjectHasAttribute($driver, $this->lib);
		$this->assertAttributeInstanceOf($class, $driver, $this->lib);
		$this->assertAttributeInstanceOf('CI_Driver', $driver, $this->lib);

		// Was decorate called?
		$this->assertObjectHasAttribute($prop, $this->lib->$driver);
		$this->assertTrue($this->lib->$driver->$prop);

		// Do we get an error for an invalid driver?
		$driver = 'unlisted';
		$this->setExpectedException('RuntimeException', 'CI Error: Invalid driver requested: '.$this->name.'_'.$driver);
		$this->lib->load_driver($driver);
	}

	/**
	 * Test loading lowercase from app path
	 *
	 * @covers  CI_Driver_Library::load_driver
	 */
	public function test_load_app_driver()
	{
		// Create mock driver library
		$this->_mock_library();

		// Create driver file
		$driver = 'lowpack';
		$file = $this->name.'_'.$driver;
		$class = 'CI_'.$file;
		$content = '<?php class '.$class.' extends CI_Driver {  }';
		$this->_create_content(strtolower($file), $content, $this->app_root, 'libraries/'.$this->name.'/drivers');

		// Make valid list
		$nodriver = 'absent';
		$this->lib->driver_list(array($driver, $nodriver));

		// Load driver
		$this->assertNotNull($this->lib->load_driver($driver));

		// Was driver loaded?
		$this->assertObjectHasAttribute($driver, $this->lib);
		$this->assertAttributeInstanceOf($class, $driver, $this->lib);
		$this->assertAttributeInstanceOf('CI_Driver', $driver, $this->lib);

		// Do we get an error for a non-existent driver?
		$this->setExpectedException('RuntimeException', 'CI Error: Unable to load the requested driver: CI_'.
			$this->name.'_'.$nodriver);
		$this->lib->load_driver($nodriver);
	}

	/**
	 * Test loading driver extension
	 *
	 * @covers  CI_Driver_Library::load_driver
	 */
	public function test_load_driver_ext()
	{
		// Create mock driver library
		$this->_mock_library();

		// Create base file
		$driver = 'extend';
		$base = $this->name.'_'.$driver;
		$baseclass = 'CI_'.$base;
		$content = '<?php class '.$baseclass.' extends CI_Driver {  }';
		$this->_create_content($base, $content, $this->base_root, 'libraries/'.$this->name.'/drivers');

		// Create driver file
		$class = $this->subclass.$base;
		$content = '<?php class '.$class.' extends '.$baseclass.' {  }';
		$this->_create_content($class, $content, $this->app_root, 'libraries/'.$this->name.'/drivers');

		// Make valid list
		$this->lib->driver_list($driver);

		// Load driver
		$this->assertNotNull($this->lib->load_driver($driver));

		// Was driver loaded?
		$this->assertObjectHasAttribute($driver, $this->lib);
		$this->assertAttributeInstanceOf($class, $driver, $this->lib);
		$this->assertAttributeInstanceOf($baseclass, $driver, $this->lib);
		$this->assertAttributeInstanceOf('CI_Driver', $driver, $this->lib);

		// Create driver extension without base
		$driver = 'baseless';
		$base = $this->name.'_'.$driver;
		$class = $this->subclass.$base;
		$content = '<?php class '.$class.' extends CI_Driver {  }';
		$this->_create_content($class, $content, $this->app_root, 'libraries/'.$this->name.'/drivers');
		$this->lib->driver_list($driver);

		// Do we get an error when base class isn't found?
		$this->setExpectedException('RuntimeException', 'CI Error: Unable to load the requested class: CI_'.$base);
		$this->lib->load_driver($driver);
	}

	/**
	 * Test decorating driver with parent attributes
	 *
	 * @covers	CI_Driver::decorate
	 */
	public function test_decorate()
	{
		// Create parent with a method and property to access
		$pclass = 'Test_Parent';
		$prop = 'parent_prop';
		$value = 'initial';
		$method = 'parent_func';
		$return = 'func return';
		$code = 'class '.$pclass.' { public $'.$prop.' = \''.$value.'\'; '.
			'public function '.$method.'() { return \''.$return.'\'; } }';
		eval($code);
		$parent = new $pclass();

		// Create child driver to decorate
		$class = 'Test_Driver';
		eval('class '.$class.' extends CI_Driver {  }');
		$child = new $class();

		// Decorate child
		$child->decorate($parent);

		// Do we get the initial parent property value?
		$this->assertEquals($value, $child->$prop);

		// Can we change the parent property?
		$newval = 'changed';
		$child->$prop = $newval;
		$this->assertEquals($newval, $parent->$prop);

		// Do we get back the updated value?
		$this->assertEquals($newval, $child->$prop);

		// Can we call the parent method?
		$this->assertEquals($return, $child->$method());
	}

	/**
	 * Create mock driver library
	 */
	private function _mock_library()
	{
		// Create instance object
		$ci = new StdClass();

		// Mock up Config to return subclass prefix
		$cfg = 'Drv_Mock_Config';
		if ( ! class_exists($cfg))
		{
			$code = 'class '.$cfg.' { public $config = array(); '.
				'public function item($key) { return isset($this->config[$key]) ? $this->config[$key] : FALSE; } }';
			eval($code);
		}
		$ci->config = new $cfg();
		$this->subclass = 'Mock_Libraries_';
		$ci->config->config['subclass_prefix'] = $this->subclass;

		// Mock up Loader to return package paths
		$load = 'Drv_Mock_Loader';
		if ( ! class_exists($load))
		{
			$code = 'class '.$load.' { public $paths = array(); '.
				'public function get_package_paths($all) { return $this->paths; } }';
			eval($code);
		}
		$ci->load = new $load();
		$ci->load->paths = array($this->app_path, $this->base_path);

		// Set instance
		$this->ci_instance($ci);

		// Create mock driver library
		$this->name = 'Driver';
		$this->lib = new Mock_Libraries_Driver();
	}

	/**
	 * Create file (and subdirectories) in VFS tree
	 *
	 * @param	string	File name (w/o .php)
	 * @param	string	File content
	 * @param	object	VFS root to create under
	 * @param	string	Subdirectory path
	 * @return	void
	 */
	private function _create_content($file, $content, $root, $path = '')
	{
		// Initialize tree with file
		$tree = array($file.'.php' => $content);

		// Get subdirectories
		$subs = explode('/', $path);
		while (($dir = array_shift($subs)))
		{
			// See if subdir exists under current root
			$dir_root = $root->getChild($dir);
			if ($dir_root)
			{
				// Yes - recurse into subdir
				$root = $dir_root;
			}
			else
			{
				// No - put subdir back and quit
				array_unshift($subs, $dir);
				break;
			}
		}

		// Create any remaining subdirectories
		foreach (array_reverse($subs) as $dir)
		{
			// Wrap content in subdirectory for creation
			$tree = array($dir => $tree);
		}

		// Create tree
		vfsStream::create($tree, $root);
	}
}

