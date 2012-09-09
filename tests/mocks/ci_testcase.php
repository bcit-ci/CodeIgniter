<?php

class CI_TestCase extends PHPUnit_Framework_TestCase {

	public $ci_vfs_root = NULL;
	public $ci_app_root = NULL;
	public $ci_base_root = NULL;
	public $ci_app_path = '';
	public $ci_base_path = '';

	protected $ci_instance;
	protected static $ci_test_instance;

	private $global_map = array(
		'benchmark'	=> 'bm',
		'config'	=> 'cfg',
		'hooks'		=> 'ext',
		'utf8'		=> 'uni',
		'router'	=> 'rtr',
		'output'	=> 'out',
		'security'	=> 'sec',
		'input'		=> 'in',
		'lang'		=> 'lang',
		'loader'	=> 'load',
		'model'		=> 'model',
		'controller'=> 'ctlr'
	);

	// --------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		$this->ci_instance = new stdClass();
	}

	// --------------------------------------------------------------------

	public function setUp()
	{
		if (method_exists($this, 'set_up'))
		{
			$this->set_up();
		}
	}

	// --------------------------------------------------------------------

	public function tearDown()
	{
		if (method_exists($this, 'tear_down'))
		{
			$this->tear_down();
		}
	}

	// --------------------------------------------------------------------

	public static function instance()
	{
		return self::$ci_test_instance;
	}

	// --------------------------------------------------------------------

	public function ci_set_config($key = '', $val = '')
	{
		// Add test config
		if ( ! isset($this->ci_instance->config))
		{
			$this->ci_instance->config = new CI_TestConfig();
		}

		// Empty key means just do setup above
		if ($key === '')
		{
			return;
		}

		if (is_array($key))
		{
			$this->ci_instance->config->config = $key;
		}
		else
		{
			$this->ci_instance->config->config[$key] = $val;
		}
	}

	// --------------------------------------------------------------------

	public function ci_get_config()
	{
		return isset($this->ci_instance->config) ? $this->ci_instance->config->config : array();
	}

	// --------------------------------------------------------------------

	public function ci_instance($obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance;
		}

		$this->ci_instance = $obj;
	}

	// --------------------------------------------------------------------

	public function ci_instance_var($name, $obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance->$name;
		}

		$this->ci_instance->$name =& $obj;
	}

	// --------------------------------------------------------------------

	/**
	 * Grab a core class
	 *
	 * Loads the correct core class without extensions
	 * and returns a reference to the class name in the
	 * globals array with the correct key. This way the
	 * test can modify the variable it assigns to and
	 * still maintain the global.
	 */
	public function &ci_core_class($name)
	{
		$name = strtolower($name);

		if (isset($this->global_map[$name]))
		{
			$class_name = ucfirst($name);
			$global_name = $this->global_map[$name];
		}
		elseif (in_array($name, $this->global_map))
		{
			$class_name = ucfirst(array_search($name, $this->global_map));
			$global_name = $name;
		}
		else
		{
			throw new Exception('Not a valid core class.');
		}

		if ( ! class_exists('CI_'.$class_name))
		{
			require_once BASEPATH.'core/'.$class_name.'.php';
		}

		$GLOBALS[strtoupper($global_name)] = 'CI_'.$class_name;
		return $GLOBALS[strtoupper($global_name)];
	}

	// --------------------------------------------------------------------

	// convenience function for global mocks
	public function ci_set_core_class($name, $obj)
	{
		$orig =& $this->ci_core_class($name);
		$orig = $obj;
	}

	// --------------------------------------------------------------------

	/**
	 * Create VFS root with system and application directories
	 *
	 * @return	void
	 */
	public function ci_vfs_setup()
	{
		// Create VFS tree
		$this->ci_vfs_root = vfsStream::setup();
		$this->ci_app_root = vfsStream::newDirectory('application')->at($this->ci_vfs_root);
		$this->ci_base_root = vfsStream::newDirectory('system')->at($this->ci_vfs_root);

		// Get VFS app and base path URLs
		$this->ci_app_path = vfsStream::url('application/');
		$this->ci_base_path = vfsStream::url('system/');
	}

	// --------------------------------------------------------------------

	/**
	 * Create VFS content
	 *
	 * @param	string  File name
	 * @param   string  File content
	 * @param   object  VFS directory object
	 * @param   string  Optional directory name
	 * @param   string  Optional subdirectory name
	 * @return  void
	 */
	public function ci_vfs_create($file, $content, $root = NULL, $dir = NULL, $sub = NULL)
	{
		// Check for array
		if (is_array($file))
		{
			foreach ($file as $name => $content)
			{
				$this->ci_vfs_create($name, $content, $root, $dir, $sub);
			}
			return;
		}

		// Assert .php extension
		if (strrpos($file, '.php') !== strlen($file) - 4)
		{
			$file .= '.php';
		}

		// Build content
		$tree = array($file => $content);

		// Check for directory
		if ($dir)
		{
			$dir_root = $root->getChild($dir);
			if ($dir_root)
			{
				// Directory exists - have sub?
				if ($sub)
				{
					// Check for sub
					$sub_root = $dir_root->getChild($sub);
					if ($sub_root)
					{
						// Exists - build under sub
						$root = $sub_root;
					}
					else
					{
						// None - build sub under dir
						$root = $dir_root;
						$tree = array($sub => $tree);
					}
				}
				else
				{
					// Build under dir
					$root = $dir_root;
				}
			}
			else
			{
				// Directory doesn't exist - have sub?
				if ($sub)
				{
					// Build content in sub
					$tree = array($sub => $tree);
				}

				// Build dir with content
				$tree = array($dir => $tree);
			}
		}

		// Create tree
		vfsStream::create($tree, $root);
	}

	// --------------------------------------------------------------------

	/**
	 * Helper to get a VFS URL path
	 *
	 * @return	string	Path URL
	 */
	public function ci_vfs_path($path)
	{
		// Remove leading slashes and return URL
		return vfsStream::url(ltrim($path, '/'));
	}

	// --------------------------------------------------------------------
	// Internals
	// --------------------------------------------------------------------

	/**
	 * Overwrite runBare
	 *
	 * PHPUnit instantiates the test classes before
	 * running them individually. So right before a test
	 * runs we set our instance. Normally this step would
	 * happen in setUp, but someone is bound to forget to
	 * call the parent method and debugging this is no fun.
	 */
	public function runBare()
	{
		self::$ci_test_instance = $this;
		parent::runBare();
	}

	// --------------------------------------------------------------------

	public function helper($name)
	{
		require_once(BASEPATH.'helpers/'.$name.'_helper.php');
	}

	// --------------------------------------------------------------------

	/**
	 * This overload is useful to create a stub, that need to have a specific method.
	 */
	public function __call($method, $args)
	{
		if ($this->{$method} instanceof Closure)
		{
			return call_user_func_array($this->{$method},$args);
		}
		else
		{
			return parent::__call($method, $args);
		}
	}

}
