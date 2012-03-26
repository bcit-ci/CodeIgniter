<?php
/**
 * TestCase class used for testing CodeIgniter application controllers. 
 */
class CI_Controller_TestCase extends PHPUnit_Framework_TestCase {
	
	/**
	 * Dispatches a request.
	 * 
	 * @param array
	 * @return void
	 */
	public function dispatch(array $routing = array())
	{
		$dispatcher =& load_class('Dispatcher', 'core');
		$dispatcher->dispatch($routing);
	}
	
}
