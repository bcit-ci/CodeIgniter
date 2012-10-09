<?php

class Mock_Core_Loader extends CI_Loader {

	/**
	 * Since we use paths to load up models, views, etc, we need the ability to
	 * mock up the file system so when core tests are run, we aren't mucking
     * in the application directory. This will give finer grained control over
	 * these tests. Also, by mocking the system directory, we eliminate dependency
	 * on any other classes so errors in libraries, helpers, etc. don't give false
	 * negatives for the actual loading process. So yeah, while this looks odd,
	 * I need to overwrite protected class vars in the loader. So here we go...
	 *
	 * @covers CI_Loader::__construct()
	 */
	public function __construct()
	{
		// Get VFS paths from test case
		$test = CI_TestCase::instance();
		$this->_ci_base_path = $test->ci_base_path;
		$this->_ci_app_path = $test->ci_app_path;
		$this->_ci_view_path = $test->ci_view_path;

		// Run parent constructor
		parent::__construct();
	}

	/**
	 * Give public access to _ci_autoloader for testing
	 */
	public function autoload()
	{
		$this->_ci_autoloader();
	}

}