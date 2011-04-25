<?php

require_once 'vfsStream/vfsStream.php';
require_once BASEPATH.'/core/Loader.php';

class Extended_Loader extends CI_Loader {
	
	/**
	 * Since we use paths to load up models, views, etc, we need the ability to
	 * mock up the file system so when core tests are run, we aren't mucking
	 * in the application directory.  this will give finer grained control over
	 * these tests.  So yeah, while this looks odd, I need to overwrite protected
	 * class vars in the loader.  So here we go...
	 *
	 * @covers CI_Loader::__construct()
	 */
	public function __construct()
	{
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('application'));
		
		$this->models_dir 	= vfsStream::newDirectory('models')->at(vfsStreamWrapper::getRoot());
		$this->libs_dir 	= vfsStream::newDirectory('libraries')->at(vfsStreamWrapper::getRoot());
		$this->helpers_dir 	= vfsStream::newDirectory('helpers')->at(vfsStreamWrapper::getRoot());
		$this->views_dir 	= vfsStream::newDirectory('views')->at(vfsStreamWrapper::getRoot());
		
		$this->_ci_ob_level  		= ob_get_level();
		$this->_ci_library_paths	= array(vfsStream::url('application').'/', BASEPATH);
		$this->_ci_helper_paths 	= array(vfsStream::url('application').'/', BASEPATH);
		$this->_ci_model_paths 		= array(vfsStream::url('application').'/');
		$this->_ci_view_paths 		= array(vfsStream::url('application').'/views/' => TRUE);
	}
}


class Loader_test extends CI_TestCase {
	
	private $ci_obj;
	
	public function set_up()
	{
		// Instantiate a new loader
		$this->load = new Extended_Loader();
		
		// mock up a ci instance
		$this->ci_obj = new StdClass;
		
		// Fix get_instance()
		$this->ci_instance($this->ci_obj);
	}

	// --------------------------------------------------------------------
	
	public function test_library()
	{
		$this->_setup_config_mock();
		
		// Test loading as an array.
		$this->assertNull($this->load->library(array('table')));
		$this->assertTrue(class_exists('CI_Table'), 'Table class exists');
		$this->assertAttributeInstanceOf('CI_Table', 'table', $this->ci_obj);
		
		// Test no lib given
		$this->assertEquals(FALSE, $this->load->library());
		
		// Test a string given to params
		$this->assertEquals(NULL, $this->load->library('table', ' '));
	}

	// --------------------------------------------------------------------

	public function test_load_library_in_application_dir()
	{
		$this->_setup_config_mock();
		
		$content = '<?php class Super_test_library {} ';
		
		$model = vfsStream::newFile('Super_test_library.php')->withContent($content)
														->at($this->load->libs_dir);
		
		$this->assertNull($this->load->library('super_test_library'));
		
		// Was the model class instantiated.
		$this->assertTrue(class_exists('Super_test_library'));		
	}
	
	// --------------------------------------------------------------------
	
	private function _setup_config_mock()
	{
		// Mock up a config object until we
		// figure out how to test the library configs
		$config = $this->getMock('CI_Config', NULL, array(), '', FALSE);
		$config->expects($this->any())
			   ->method('load')
			   ->will($this->returnValue(TRUE));
		
		// Add the mock to our stdClass
		$this->ci_instance_var('config', $config);
	}

	// --------------------------------------------------------------------

	public function test_non_existent_model()
	{
		$this->setExpectedException(
			'Exception',
			'CI Error: Unable to locate the model you have specified: ci_test_nonexistent_model.php'
			);
			
		$this->load->model('ci_test_nonexistent_model.php');
	}

	// --------------------------------------------------------------------
	
	/**
	 * @coverts CI_Loader::model
	 */
	public function test_models()
	{
		$this->ci_set_core_class('model', 'CI_Model');
		
		$content = '<?php class Unit_test_model extends CI_Model {} ';
		
		$model = vfsStream::newFile('unit_test_model.php')->withContent($content)
														->at($this->load->models_dir);
		
		$this->assertNull($this->load->model('unit_test_model'));
		
		// Was the model class instantiated.
		$this->assertTrue(class_exists('Unit_test_model'));
		
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
	 * @coverts CI_Loader::view
	 */
	public function test_load_view()
	{
		$this->ci_set_core_class('output', 'CI_Output');
		
		$content = 'This is my test page.  <?php echo $hello; ?>';
		$view = vfsStream::newFile('unit_test_view.php')->withContent($content)
														->at($this->load->views_dir);
		
		// Use the optional return parameter in this test, so the view is not
		// run through the output class.
		$this->assertEquals('This is my test page.  World!',
		$this->load->view('unit_test_view', array('hello' => "World!"), TRUE));
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * @coverts CI_Loader::view
	 */
	public function test_non_existent_view()
	{
		$this->setExpectedException(
			'Exception',
			'CI Error: Unable to load the requested file: ci_test_nonexistent_view.php'
			);
			
		$this->load->view('ci_test_nonexistent_view', array('foo' => 'bar'));
	}

	// --------------------------------------------------------------------

	public function test_file()
	{
		$content = 'Here is a test file, which we will load now.';
		$file = vfsStream::newFile('ci_test_mock_file.php')->withContent($content)
														   ->at($this->load->views_dir);
		
		// Just like load->view(), take the output class out of the mix here.
		$load = $this->load->file(vfsStream::url('application').'/views/ci_test_mock_file.php', 
								TRUE);
		
		$this->assertEquals($content, $load);
		
		$this->setExpectedException(
			'Exception',
			'CI Error: Unable to load the requested file: ci_test_file_not_exists'
			);
		
		$this->load->file('ci_test_file_not_exists', TRUE);
		
	}

	// --------------------------------------------------------------------
	
	public function test_vars()
	{
		$vars = array(
			'foo'	=> 'bar'
		);
		
		$this->assertNull($this->load->vars($vars));
		$this->assertNull($this->load->vars('foo', 'bar'));
	}

	// --------------------------------------------------------------------
	
	public function test_helper()
	{
		$this->assertEquals(NULL, $this->load->helper('array'));
		
		$this->setExpectedException(
			'Exception',
			'CI Error: Unable to load the requested file: helpers/bad_helper.php'
			);
		
		$this->load->helper('bad');
	}
	
	// --------------------------------------------------------------------

	public function test_loading_multiple_helpers()
	{
		$this->assertEquals(NULL, $this->load->helpers(array('file', 'array', 'string')));
	}
	
	// --------------------------------------------------------------------
	
	// public function testLanguage()
	// {
	// 	$this->assertEquals(NULL, $this->load->language('test'));
	// }	

	// --------------------------------------------------------------------

	public function test_load_config()
	{
		$this->_setup_config_mock();
		
		$this->assertNull($this->load->config('config', FALSE));
	}
	
	// --------------------------------------------------------------------

	public function test_load_bad_config()
	{
		$this->_setup_config_mock();
		
		$this->setExpectedException(
			'Exception',
			'CI Error: The configuration file foobar.php does not exist.'
			);
		
		$this->load->config('foobar', FALSE);
	}

	// --------------------------------------------------------------------
	
	
	
	
}