<?php

class Mock_Core_URI extends CI_URI {

	protected function _is_cli_request()
	{
		return FALSE;
	}

}