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
		// Set our subclass prefix
		$this->subclass = 'Mock_Libraries_';
		$this->ci_set_config('subclass_prefix', $this->subclass);

		// Mock Loader->get_package_paths
		$paths = 'get_package_paths';
		$ldr = $this->getMockBuilder('CI_Loader')->setMethods(array($paths))->getMock();
		$ldr->expects($this->any())->method($paths)->will($this->returnValue(array(APPPATH, BASEPATH)));
		$this->ci_instance_var('load', $ldr);

		// Create mock driver library
		$this->name = 'Driver';
		$this->lib = new Mock_Libraries_Driver();
	}

	/**
	 * Test driver child loading
	 */
	public function test_load_driver()
	{
		// Create driver file
		$driver = 'basic';
		$file = $this->name.'_'.$driver;
		$class = 'CI_'.$file;
		$prop = 'called';
		$content = '<?php class '.$class.' extends CI_Driver { public $'.$prop.' = FALSE; '.
			'public function decorate($parent) { $this->'.$prop.' = TRUE; } }';
		$this->ci_vfs_create($file, $content, $this->ci_base_root, array('libraries', $this->name, 'drivers'));

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
	 */
	public function test_load_app_driver()
	{
		// Create driver file
		$driver = 'lowpack';
		$file = $this->name.'_'.$driver;
		$class = 'CI_'.$file;
		$content = '<?php class '.$class.' extends CI_Driver {  }';
		$this->ci_vfs_create($file, $content, $this->ci_app_root,
			array('libraries', $this->name, 'drivers'));

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
	 */
	public function test_load_driver_ext()
	{
		// Create base file
		$driver = 'extend';
		$base = $this->name.'_'.$driver;
		$baseclass = 'CI_'.$base;
		$content = '<?php class '.$baseclass.' extends CI_Driver {  }';
		$this->ci_vfs_create($base, $content, $this->ci_base_root, array('libraries', $this->name, 'drivers'));

		// Create driver file
		$class = $this->subclass.$base;
		$content = '<?php class '.$class.' extends '.$baseclass.' {  }';
		$this->ci_vfs_create($class, $content, $this->ci_app_root, array('libraries', $this->name, 'drivers'));

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
		$this->ci_vfs_create($class, $content, $this->ci_app_root, array('libraries', $this->name, 'drivers'));
		$this->lib->driver_list($driver);

		// Do we get an error when base class isn't found?
		$this->setExpectedException('RuntimeException', 'CI Error: Unable to load the requested class: CI_'.$base);
		$this->lib->load_driver($driver);
	}

	/**
	 * Test decorating driver with parent attributes
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

}
