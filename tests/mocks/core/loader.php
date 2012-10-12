<?php

class Mock_Core_Loader extends CI_Loader {

	/**
	 * Give public access to _ci_autoloader for testing
	 */
	public function autoload()
	{
		$this->_ci_autoloader();
	}

}
