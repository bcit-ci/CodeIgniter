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
		// Create VFS tree of loader locations
		$this->root = vfsStream::setup();
		$this->app_root = vfsStream::newDirectory('application')->at($this->root);
		$this->base_root = vfsStream::newDirectory('system')->at($this->root);

		// Get VFS app and base path URLs
		$this->app_path = vfsStream::url('application').'/';
		$this->base_path = vfsStream::url('system').'/';

		// Set loader paths with VFS URLs
		$this->_ci_ob_level  		= ob_get_level();
		$this->_ci_library_paths	= array($this->app_path, $this->base_path);
		$this->_ci_helper_paths 	= array($this->app_path, $this->base_path);
		$this->_ci_model_paths 		= array($this->app_path);
		$this->_ci_view_paths 		= array($this->app_path.'views/' => TRUE);
	}

}
