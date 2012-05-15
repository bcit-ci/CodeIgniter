<?php

class Mock_Core_Security extends CI_Security {
	
	public function csrf_set_cookie()
	{
		return $this;
	}

	// Overide inaccesible protected properties
	public function __get($property)
	{
		return isset($this->{'_'.$property}) ? $this->{'_'.$property} : NULL;
	}

	// Overide inaccesible protected method
	public function __call($method, $params)
	{
		if (is_callable(array($this, '_'.$method)))
		{
			return call_user_func_array(array($this, '_'.$method), $params);
		}

		throw new BadMethodCallException('Method '.$method.' was not found');
	}

}