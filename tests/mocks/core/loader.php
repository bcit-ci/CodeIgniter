<?php

class Mock_Core_Loader extends CI_Loader {

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