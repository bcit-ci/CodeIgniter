<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class CIPHPUnitTestRequest
{
	protected $testCase;

	/**
	 * @var CIPHPUnitTestSuperGlobal
	 */
	protected $superGlobal;

	/**
	 * @var CIPHPUnitTestRouter
	 */
	protected $router;

	/**
	 * @var callable[] callable called post controller constructor
	 */
	protected $callables = [];
	
	/**
	 * @var callable[] callable called pre controller constructor
	 */
	protected $callablePreConstructors = [];

	protected $enableHooks = false;
	
	/**
	 * @var CI_Hooks
	 */
	protected $hooks;

	public function __construct(PHPUnit_Framework_TestCase $testCase)
	{
		$this->testCase = $testCase;
		$this->superGlobal = new CIPHPUnitTestSuperGlobal();
		$this->router = new CIPHPUnitTestRouter();
	}

	/**
	 * Set HTTP request header
	 * 
	 * @param string $name  header name
	 * @param string $value value
	 */
	public function setHeader($name, $value)
	{
		$this->superGlobal->set_SERVER_HttpHeader($name, $value);
	}

	/**
	 * Set (and Reset) callable
	 * 
	 * @param callable $callable function to run after controller instantiation
	 */
	public function setCallable(callable $callable)
	{
		$this->callables = [];
		$this->callables[] = $callable;
	}

	/**
	 * Add callable
	 * 
	 * @param callable $callable function to run after controller instantiation
	 */
	public function addCallable(callable $callable)
	{
		$this->callables[] = $callable;
	}

	/**
	 * Set (and Reset) callable pre constructor
	 * 
	 * @param callable $callable function to run before controller instantiation
	 */
	public function setCallablePreConstructor(callable $callable)
	{
		$this->callablePreConstructors = [];
		$this->callablePreConstructors[] = $callable;
	}

	/**
	 * Add callable pre constructor
	 * 
	 * @param callable $callable function to run before controller instantiation
	 */
	public function addCallablePreConstructor(callable $callable)
	{
		$this->callablePreConstructors[] = $callable;
	}

	/**
	 * Enable Hooks for Controllres
	 * This enables only pre_controller, post_controller_constructor, post_controller
	 */
	public function enableHooks()
	{
		$this->enableHooks = true;
		$this->hooks =& load_class('Hooks', 'core');
	}

	/**
	 * Request to Controller
	 *
	 * @param string       $http_method HTTP method
	 * @param array|string $argv        array of controller,method,arg|uri
	 * @param array|string $params      POST params/GET params|raw_input_stream
	 */
	public function request($http_method, $argv, $params = [])
	{
		if (is_string($argv))
		{
			$argv = ltrim($argv, '/');
		}

		// Set super globals
		$_SERVER['REQUEST_METHOD'] = $http_method;
		$this->superGlobal->set_GET($argv, $params);
		$this->superGlobal->set_POST($params);
		$this->superGlobal->set_SERVER_REQUEST_URI($argv);

		try {
			if (is_array($argv))
			{
				return $this->callControllerMethod(
					$http_method, $argv, $params
				);
			}
			else
			{
				return $this->requestUri($http_method, $argv, $params);
			}
		}
		// redirect()
		catch (CIPHPUnitTestRedirectException $e)
		{
			if ($e->getCode() === 0)
			{
				set_status_header(200);
			}
			else
			{
				set_status_header($e->getCode());
			}
			$CI =& get_instance();
			$CI->output->_status['redirect'] = $e->getMessage();
		}
		// show_404()
		catch (CIPHPUnitTestShow404Exception $e)
		{
			$this->processError($e);
			return $e->getMessage();
		}
		// show_error()
		catch (CIPHPUnitTestShowErrorException $e)
		{
			$this->processError($e);
			return $e->getMessage();
		}
	}

	protected function processError(Exception $e)
	{
		set_status_header($e->getCode());
	}

	/**
	 * Call Controller Method
	 *
	 * @param string       $http_method    HTTP method
	 * @param array        $argv           controller, method [, arg1, ...]
	 * @param array|string $request_params POST params/GET params|raw_input_stream
	 */
	protected function callControllerMethod($http_method, $argv, $request_params)
	{
		$_SERVER['argv'] = array_merge(['index.php'], $argv);

		$class  = ucfirst($argv[0]);
		$method = $argv[1];

		// Remove controller and method
		array_shift($argv);
		array_shift($argv);

//		$request = [
//			'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
//			'class' => $class,
//			'method' => $method,
//			'params' => $argv,
//			'$_GET' => $_GET,
//			'$_POST' => $_POST,
//		];
//		var_dump($request, $_SERVER['argv']);

		// Reset CodeIgniter instance state
		reset_instance();

		$this->setRawInputStream($request_params);

		// 404 checking
		if (! class_exists($class) || ! method_exists($class, $method))
		{
			// If 404, CodeIgniter instance is not created yet. So create it here
			// Because we need CI->output->_status to store info
			$CI =& get_instance();
			if ($CI instanceof CIPHPUnitTestNullCodeIgniter)
			{
				CIPHPUnitTest::createCodeIgniterInstance();
			}

			show_404($class.'::'.$method . '() is not found');
		}

		$params = $argv;

		return $this->createAndCallController($class, $method, $params, false);
	}

	/**
	 * Request to URI
	 *
	 * @param string       $http_method    HTTP method
	 * @param string       $uri            URI string
	 * @param array|string $request_params POST params/GET params|raw_input_stream
	 */
	protected function requestUri($http_method, $uri, $request_params)
	{
		$_SERVER['argv'] = ['index.php', $uri];

		// Force cli mode because if not, it changes URI (and RTR) behavior
		$cli = is_cli();
		set_is_cli(TRUE);

		// Reset CodeIgniter instance state
		reset_instance();

		$this->setRawInputStream($request_params);

		// Get route
		list($class, $method, $params) = $this->router->getRoute();

		// Restore cli mode
		set_is_cli($cli);

//		$request = [
//			'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
//			'class' => $class,
//			'method' => $method,
//			'params' => $params,
//			'$_GET' => $_GET,
//			'$_POST' => $_POST,
//		];
//		var_dump($request, $_SERVER['argv']);

		return $this->createAndCallController($class, $method, $params);
	}

	protected function callHook($hook)
	{
		if ($this->enableHooks)
		{
			return $this->hooks->call_hook($hook);
		}

		return false;
	}

	protected function setRawInputStream($string)
	{
		if (is_string($string))
		{
			$INPUT =& load_class('Input', 'core');
			CIPHPUnitTestReflection::setPrivateProperty(
				$INPUT,
				'_raw_input_stream',
				$string
			);
		}
	}

	protected function createAndCallController($class, $method, $params, $call_display = true)
	{
		ob_start();

		$this->callHook('pre_controller');

		// Run callablePreConstructor
		if ($this->callablePreConstructors !== [])
		{
			foreach ($this->callablePreConstructors as $callable)
			{
				$callable();
			}
		}

		// Create controller
		if (CIPHPUnitTest::wiredesignzHmvcInstalled())
		{
			new CI();
		}
		$controller = new $class;
		$CI =& get_instance();

		// Set CodeIgniter instance to TestCase
		$this->testCase->setCI($CI);

		// Set default response code 200
		set_status_header(200);
		// Run callable
		if ($this->callables !== [])
		{
			foreach ($this->callables as $callable)
			{
				$callable($CI);
			}
		}

		$this->callHook('post_controller_constructor');

		// Call controller method
		call_user_func_array([$controller, $method], $params);

		$this->callHook('post_controller');

		if ($call_display && $this->callHook('display_override') === false)
		{
			$CI->output->_display();
		}

		$output = ob_get_clean();

		if ($output == '')
		{
			$output = $CI->output->get_output();
		}

		return $output;
	}

	/**
	 * Get HTTP Status Code Info
	 * 
	 * @return array ['code' => code, 'text' => text]
	 * @throws LogicException
	 */
	public function getStatus()
	{
		$CI =& get_instance();
		if (! isset($CI->output->_status))
		{
			throw new LogicException('Status code is not set. You must call $this->request() first');
		}

		return $CI->output->_status;
	}
}
