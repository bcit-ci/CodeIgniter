<?php

class Mock_Core_Output extends CI_Output {

	public function get_mime_type()
	{
		return $this->mime_type;
	}

	public function get_profiler_sections()
	{
		return $this->_profiler_sections;
	}

	public function set_cache_header($arg1, $arg2)
	{
		// Do nothing
	}

}
