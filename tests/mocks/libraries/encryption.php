<?php

class Mock_Libraries_Encryption extends CI_Encryption {

	/**
	 * __get_params()
	 *
	 * Allows public calls to the otherwise protected _get_params().
	 */
	public function __get_params($params)
	{
		return $this->_get_params($params);
	}

	// --------------------------------------------------------------------

	/**
	 * get_key()
	 *
	 * Allows checking for key changes.
	 */
	public function get_key()
	{
		return $this->_key;
	}

	// --------------------------------------------------------------------

	/**
	 * __driver_get_handle()
	 *
	 * Allows checking for _mcrypt_get_handle(), _openssl_get_handle()
	 */
	public function __driver_get_handle($driver, $cipher, $mode)
	{
		return $this->{'_'.$driver.'_get_handle'}($cipher, $mode);
	}

}