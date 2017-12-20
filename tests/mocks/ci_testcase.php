<?php

class CI_TestCase extends PHPUnit_Framework_TestCase {

	public $ci_vfs_root;
	public $ci_app_root;
	public $ci_base_root;
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
		'model'		=> 'model'
	);

	// --------------------------------------------------------------------

	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		$this->ci_instance = new stdClass();
	}

	// --------------------------------------------------------------------

	public function setUp()
	{
		// Setup VFS with base directories
		$this->ci_vfs_root = vfsStream::setup();
		$this->ci_app_root = vfsStream::newDirectory('application')->at($this->ci_vfs_root);
		$this->ci_base_root = vfsStream::newDirectory('system')->at($this->ci_vfs_root);
		$this->ci_view_root = vfsStream::newDirectory('views')->at($this->ci_app_root);

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
			require_once SYSTEM_PATH.'core/'.$class_name.'.php';
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

	/**
	 * Create VFS directory
	 *
	 * @param	string	Directory name
	 * @param	object	Optional root to create in
	 * @return	object	New directory object
	 */
	public function ci_vfs_mkdir($name, $root = NULL)
	{
		// Check for root
		if ( ! $root)
		{
			$root = $this->ci_vfs_root;
		}

		// Return new directory object
		return vfsStream::newDirectory($name)->at($root);
	}

	// --------------------------------------------------------------------

	/**
	 * Create VFS content
	 *
	 * @param	string	File name
	 * @param	string	File content
	 * @param	object	VFS directory object
	 * @param	mixed	Optional subdirectory path or array of subs
	 * @return	void
	 */
	public function ci_vfs_create($file, $content = '', $root = NULL, $path = NULL)
	{
		// Check for array
		if (is_array($file))
		{
			foreach ($file as $name => $content)
			{
				$this->ci_vfs_create($name, $content, $root, $path);
			}
			return;
		}

		// Assert .php extension if none given
		if (pathinfo($file, PATHINFO_EXTENSION) == '')
		{
			$file .= '.php';
		}

		// Build content
		$tree = array($file => $content);

		// Check for path
		$subs = array();
		if ($path)
		{
			// Explode if not array
			$subs = is_array($path) ? $path : explode('/', trim($path, '/'));
		}

		// Check for root
		if ( ! $root)
		{
			// Use base VFS root
			$root = $this->ci_vfs_root;
		}

		// Handle subdirectories
		while (($dir = array_shift($subs)))
		{
			// See if subdir exists under current root
			$dir_root = $root->getChild($dir);
			if ($dir_root)
			{
			   	// Yes - recurse into subdir
				$root = $dir_root;
			}
			else
			{
				// No - put subdirectory back and quit
				array_unshift($subs, $dir);
				break;
			}
		}

		// Create any remaining subdirectories
		if ($subs)
		{
			foreach (array_reverse($subs) as $dir)
			{
				// Wrap content in subdirectory for creation
				$tree = array($dir => $tree);
			}
		}

		// Create tree
		vfsStream::create($tree, $root);
	}

	// --------------------------------------------------------------------

	/**
	 * Clone a real file into VFS
	 *
	 * @param	string	Path from base directory
	 * @return	bool	TRUE on success, otherwise FALSE
	 */
	public function ci_vfs_clone($path, $dest='')
	{
		// Check for array
		if (is_array($path))
		{
			foreach ($path as $file)
			{
				$this->ci_vfs_clone($file, $dest);
			}
			return;
		}

		// Get real file contents
		$content = file_get_contents(PROJECT_BASE.$path);
		if ($content === FALSE)
		{
			// Couldn't find file to clone
			return FALSE;
		}

		if (empty($dest))
		{
			$dest = dirname($path);
		}

		$this->ci_vfs_create(basename($path), $content, NULL, $dest);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Helper to get a VFS URL path
	 *
	 * @param	string	Path
	 * @param	string	Optional base path
	 * @return	string	Path URL
	 */
	public function ci_vfs_path($path, $base = '')
	{
		// Check for base path
		if ($base)
		{
			// Prepend to path
			$path = rtrim($base, '/').'/'.ltrim($path, '/');

			// Is it already in URL form?
			if (strpos($path, '://') !== FALSE)
			{
				// Done - return path
				return $path;
			}
		}

		// Trim leading slash and return URL
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
		require_once(SYSTEM_PATH.'helpers/'.$name.'_helper.php');
	}

	// --------------------------------------------------------------------

	public function lang($name)
	{
		require(SYSTEM_PATH.'language/english/'.$name.'_lang.php');
		return $lang;
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

		return parent::__call($method, $args);
	}

}
