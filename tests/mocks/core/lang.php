<?php

class Mock_Core_Lang extends CI_Lang {

	public function line($line = '')
	{
		return FALSE;
	}

	public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		return;
	}

}