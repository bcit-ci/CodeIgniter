<?php

/**
 * Mock library to add testing features to Session driver library
 */
class Mock_Libraries_Session extends CI_Session {
	/**
	 * Simulate new page load
	 */
	public function reload()
	{
		$this->_flashdata_sweep();
		$this->_flashdata_mark();
		$this->_tempdata_sweep();
	}
}

/**
 * Mock cookie driver to overload cookie setting
 */
class Mock_Libraries_Session_cookie extends CI_Session_cookie {
	/**
	 * Overload _setcookie to manage $_COOKIE values, since actual cookies can't be set in unit testing
	 */
	protected function _setcookie($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = FALSE, $httponly = FALSE)
	{
		if (empty($value) OR $expire <= time())
		{
			unset($_COOKIE[$name]);
		}
		else
		{
			$_COOKIE[$name] = $value;
		}
	}
}