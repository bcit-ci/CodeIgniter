<?php

class Mock_Core_Router extends CI_Router {

	/**
	 * Get the default controller
	 */
	public function default_ctlr($default = NULL)
	{
		if ($default)
		{
			$this->default_controller = $default;
			return;
		}
		return $this->default_controller;
	}

}
