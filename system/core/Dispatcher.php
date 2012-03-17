<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Dispatcher {

	protected $_class;
	protected $_method;

	/**
	 * Dispatch the request to the correct class and method. Routing options may
	 * be given here to override the options already parsed by the router class.
	 *
	 * @param array
	 * @return void
	 */
	public function dispatch(array $routing = array())
	{
		global $RTR;
		 
		// set any routing overrides
		if ( ! empty($routing))
		{
			$RTR->_set_overrides($routing);
		}

		$this->_load_controllers();
		$this->_security_check();
		$this->_pre_dispatch();
		$this->_dispatch();
		$this->_post_dispatch();
	}

	/**
	 * Load the controllers needed to dispatch the request.
	 *
	 * @return void
	 */
	protected function _load_controllers()
	{
		global $CFG, $RTR, $BM;
		 
		if (file_exists(APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php'))
		{
			require APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php';
		}

		// Load the local application controller
		// Note: The Router class automatically validates the controller path using the router->_validate_request().
		// If this include fails it means that the default controller in the Routes.php file is not resolving to something valid.
		if ( ! file_exists(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().'.php'))
		{
			show_error('Unable to load your default controller. Please make sure the controller specified in your Routes.php file is valid.');
		}

		include(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().'.php');

		// Set a mark point for benchmarking
		$BM->mark('loading_time:_base_classes_end');
	}

	/**
	 * Performs a security check before entering the pre-dispatch phase.
	 *
	 * None of the functions in the app controller or the loader class can be
	 * called via the URI, nor can controller functions that begin with an
	 * underscore.
	 *
	 * @return void
	 */
	protected function _security_check()
	{
		global $RTR;
		 
		$this->_class = $RTR->fetch_class();
		$this->_method = $RTR->fetch_method();

		if ( ! class_exists($this->_class)
		OR strpos($this->_method, '_') === 0
		OR in_array(strtolower($this->_method), array_map('strtolower', get_class_methods('CI_Controller')))
		)
		{
			if ( ! empty($RTR->routes['404_override']))
			{
				$x = explode('/', $RTR->routes['404_override'], 2);
				$this->_class = $x[0];
				$this->_method = (isset($x[1]) ? $x[1] : 'index');
				if ( ! class_exists($this->_class))
				{
					if ( ! file_exists(APPPATH.'controllers/'.$this->_class.'.php'))
					{
						show_404("{$this->_class}/{$this->_method}");
					}

					include_once(APPPATH.'controllers/'.$this->_class.'.php');
				}
			}
			else
			{
				show_404("{$this->_class}/{$this->_method}");
			}
		}
	}

	/**
	 * Initial setup and call hooks before dispatching the request.
	 *
	 * @return void
	 */
	protected function _pre_dispatch()
	{
		global $EXT, $BM;
		 
		$EXT->_call_hook('pre_controller');

		// Mark a start point so we can benchmark the controller
		$BM->mark('controller_execution_time_( '.$this->_class.' / '.$this->_method.' )_start');
		$GLOBALS['CI'] = new $this->_class();

		$EXT->_call_hook('post_controller_constructor');
	}

	/**
	 * Perform the actual dispatch by calling the method.
	 *
	 * @return void
	 */
	protected function _dispatch()
	{
		global $CI, $URI, $RTR, $BM;
		 
		// Is there a "remap" function? If so, we call it instead
		if (method_exists($CI, '_remap'))
		{
			$CI->_remap($this->_method, array_slice($URI->rsegments, 2));
		}
		else
		{
			// is_callable() returns TRUE on some versions of PHP 5 for private and protected
			// methods, so we'll use this workaround for consistent behavior
			if ( ! in_array(strtolower($this->_method), array_map('strtolower', get_class_methods($CI))))
			{
				// Check and see if we are using a 404 override and use it.
				if ( ! empty($RTR->routes['404_override']))
				{
					$x = explode('/', $RTR->routes['404_override'], 2);
					$this->_class = $x[0];
					$this->_method = (isset($x[1]) ? $x[1] : 'index');
					if ( ! class_exists($this->_class))
					{
						if ( ! file_exists(APPPATH.'controllers/'.$this->_class.'.php'))
						{
							show_404("{$this->_class}/{$this->_method}");
						}

						include_once(APPPATH.'controllers/'.$this->_class.'.php');
						unset($CI);
						$CI = new $this->_class();
					}
				}
				else
				{
					show_404("{$this->_class}/{$this->_method}");
				}
			}

			// Call the requested method.
			// Any URI segments present (besides the class/function) will be passed to the method for convenience
			call_user_func_array(array(&$CI, $this->_method), array_slice($URI->rsegments, 2));
		}

		// Mark a benchmark end point
		$BM->mark('controller_execution_time_( '.$this->_class.' / '.$this->_method.' )_end');
	}

	/**
	 * Release resources and call hooks after dispatching the request.
	 */
	protected function _post_dispatch()
	{
		global $EXT, $OUT, $CI;
		 
		$EXT->_call_hook('post_controller');
		if ($EXT->_call_hook('display_override') === FALSE)
		{
			$OUT->_display();
		}
		$EXT->_call_hook('post_system');

		if (class_exists('CI_DB') && isset($CI->db))
		{
			$CI->db->close();
		}
	}
}
