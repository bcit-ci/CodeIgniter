<?php

class Mock_Core_Log extends CI_Log {
	/**
	 * Get protected property
	 *
	 * @return	mixed	Property value or NULL
	 */
	public function get_config($name)
	{
		return isset($this->$name) ? $this->$name : NULL;
	}
}

