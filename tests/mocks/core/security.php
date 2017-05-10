<?php

class Mock_Core_Security extends CI_Security {

	public function csrf_set_cookie()
	{
		// We cannot set cookie in CLI mode, so for csrf test, who rely on $_COOKIE,
		// we superseded set_cookie with directly set the cookie variable,
		// @see : ./tests/codeigniter/core/Security_test.php, line 8
		return $this;
	}

	// Override inaccessible protected properties
	public function __get($property)
	{
		return isset($this->{'_'.$property}) ? $this->{'_'.$property} : NULL;
	}

	public function remove_evil_attributes($str, $is_image)
	{
		return $this->_remove_evil_attributes($str, $is_image);
	}

	// Override inaccessible protected method
	public function __call($method, $params)
	{
		if (is_callable(array($this, '_'.$method)))
		{
			return call_user_func_array(array($this, '_'.$method), $params);
		}

		throw new BadMethodCallException('Method '.$method.' was not found');
	}

}
