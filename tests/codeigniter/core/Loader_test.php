<?php

class Loader_test extends CodeIgniterTestCase {
	
	private $ci_obj;
	
	public function setUp()
	{
		// Instantiate a new loader
		$cls = $this->ci_core_class('load');
		$this->_loader = new $cls;
		
		// mock up a ci instance
		$this->ci_obj = new StdClass;
		
		// Fix get_instance()
		CodeIgniterTestCase::$test_instance =& $this;
		$this->ci_instance($this->ci_obj);
	}

	// --------------------------------------------------------------------
	
	public function testLibrary()
	{
		// Mock up a config object until we
		// figure out how to test the library configs
		$config = $this->getMock('CI_Config', NULL, array(), '', FALSE);
		$config->expects($this->any())
			   ->method('load')
			   ->will($this->returnValue(TRUE));
		
		// Add the mock to our stdClass
		$this->ci_set_instance_var('config', $config);
		
		// Test loading as an array.
		$this->assertEquals(NULL, $this->_loader->library(array('table')));
		$this->assertTrue(class_exists('CI_Table'), 'Table class exists');
		$this->assertAttributeInstanceOf('CI_Table', 'table', $this->ci_obj);
		
		// Test no lib given
		$this->assertEquals(FALSE, $this->_loader->library());
		
		// Test a string given to params
		$this->assertEquals(NULL, $this->_loader->library('table', ' '));
	}	
	
	// --------------------------------------------------------------------
	
	public function testModels()
	{
		// Test loading as an array.
		$this->assertEquals(NULL, $this->_loader->model(array('foobar')));
		
		// Test no model given
		$this->assertEquals(FALSE, $this->_loader->model(''));
		
		// Test a string given to params
		$this->assertEquals(NULL, $this->_loader->model('foobar', ' '));		
	}

	// --------------------------------------------------------------------
	
	public function testDatabase()
	{
		$this->assertEquals(NULL, $this->_loader->database());
		$this->assertEquals(NULL, $this->_loader->dbutil());		
	}

	// --------------------------------------------------------------------
	
	public function testView()
	{
		// I'm not entirely sure this is the proper way to handle this.
		// So, let's revist it, m'kay?
		try 
		{
			 $this->_loader->view('foo');
		}
		catch (Exception $expected)
		{
			return;
		}
	}

	// --------------------------------------------------------------------

	public function testFile()
	{
		// I'm not entirely sure this is the proper way to handle this.
		// So, let's revist it, m'kay?
		try 
		{
			 $this->_loader->file('foo');
		}
		catch (Exception $expected)
		{
			return;
		}		
	}

	// --------------------------------------------------------------------
	
	public function testVars()
	{
		$vars = array(
			'foo'	=> 'bar'
		);
		
		$this->assertEquals(NULL, $this->_loader->vars($vars));
		$this->assertEquals(NULL, $this->_loader->vars('foo', 'bar'));
	}

	// --------------------------------------------------------------------
	
	public function testHelper()
	{
		$this->assertEquals(NULL, $this->_loader->helper('array'));
		$this->assertEquals(NULL, $this->_loader->helper('bad'));
	}
	
	// --------------------------------------------------------------------

	public function testHelpers()
	{
		$this->assertEquals(NULL, $this->_loader->helpers(array('file', 'array', 'string')));
	}
	
	// --------------------------------------------------------------------
	
	// public function testLanguage()
	// {
	// 	$this->assertEquals(NULL, $this->_loader->language('test'));
	// }	

	// --------------------------------------------------------------------

	public function testLoadConfig()
	{
		$this->assertEquals(NULL, $this->_loader->config('config', FALSE, TRUE));
	}
	
	
	
}