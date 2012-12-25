<?php

define('DWOO_DIRECTORY', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * main dwoo class, allows communication between the compiler, template and data classes
 *
 * <pre>
 * requirements :
 *  php 5.2.0 or above (might work below, it's a rough estimate)
 *  SPL and PCRE extensions (for php versions prior to 5.3.0)
 *  mbstring extension for some string manipulation plugins (especially if you intend to use UTF-8)
 * recommended :
 *  hash extension (for Dwoo_Template_String - minor performance boost)
 *
 * project created :
 *  2008-01-05
 * </pre>
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class Dwoo
{
	/**
	 * current version number
	 *
	 * @var string
	 */
	const VERSION = '1.1.1';

	/**
	 * unique number of this dwoo release
	 *
	 * this can be used by templates classes to check whether the compiled template
	 * has been compiled before this release or not, so that old templates are
	 * recompiled automatically when Dwoo is updated
	 */
	const RELEASE_TAG = 17;

	/**#@+
	 * constants that represents all plugin types
	 *
	 * these are bitwise-operation-safe values to allow multiple types
	 * on a single plugin
	 *
	 * @var int
	 */
	const CLASS_PLUGIN = 1;
	const FUNC_PLUGIN = 2;
	const NATIVE_PLUGIN = 4;
	const BLOCK_PLUGIN = 8;
	const COMPILABLE_PLUGIN = 16;
	const CUSTOM_PLUGIN = 32;
	const SMARTY_MODIFIER = 64;
	const SMARTY_BLOCK = 128;
	const SMARTY_FUNCTION = 256;
	const PROXY_PLUGIN = 512;
	const TEMPLATE_PLUGIN = 1024;
	/**#@-*/

	/**
	 * character set of the template, used by string manipulation plugins
	 *
	 * it must be lowercase, but setCharset() will take care of that
	 *
	 * @see setCharset
	 * @see getCharset
	 * @var string
	 */
	protected $charset = 'utf-8';

	/**
	 * global variables that are accessible through $dwoo.* in the templates
	 *
	 * default values include:
	 *
	 * $dwoo.version - current version number
	 * $dwoo.ad - a Powered by Dwoo link pointing to dwoo.org
	 * $dwoo.now - the current time
	 * $dwoo.template - the current template filename
	 * $dwoo.charset - the character set used by the template
	 *
	 * on top of that, foreach and other plugins can store special values in there,
	 * see their documentation for more details.
	 *
	 * @private
	 * @var array
	 */
	public $globals;

	/**
	 * directory where the compiled templates are stored
	 *
	 * defaults to DWOO_COMPILEDIR (= dwoo_dir/compiled by default)
	 *
	 * @var string
	 */
	protected $compileDir;

	/**
	 * directory where the cached templates are stored
	 *
	 * defaults to DWOO_CACHEDIR (= dwoo_dir/cache by default)
	 *
	 * @var string
	 */
	protected $cacheDir;

	/**
	 * defines how long (in seconds) the cached files must remain valid
	 *
	 * can be overriden on a per-template basis
	 *
	 * -1 = never delete
	 * 0 = disabled
	 * >0 = duration in seconds
	 *
	 * @var int
	 */
	protected $cacheTime = 0;

	/**
	 * security policy object
	 *
	 * @var Dwoo_Security_Policy
	 */
	protected $securityPolicy = null;

	/**
	 * stores the custom plugins callbacks
	 *
	 * @see addPlugin
	 * @see removePlugin
	 * @var array
	 */
	protected $plugins = array();

	/**
	 * stores the filter callbacks
	 *
	 * @see addFilter
	 * @see removeFilter
	 * @var array
	 */
	protected $filters = array();

	/**
	 * stores the resource types and associated
	 * classes / compiler classes
	 *
	 * @var array
	 */
	protected $resources = array
	(
		'file'		=>	array
		(
			'class'		=>	'Dwoo_Template_File',
			'compiler'	=>	null
		),
		'string'	=>	array
		(
			'class'		=>	'Dwoo_Template_String',
			'compiler'	=>	null
		)
	);

	/**
	 * the dwoo loader object used to load plugins by this dwoo instance
	 *
	 * @var Dwoo_ILoader
	 */
	protected $loader = null;

	/**
	 * currently rendered template, set to null when not-rendering
	 *
	 * @var Dwoo_ITemplate
	 */
	protected $template = null;

	/**
	 * stores the instances of the class plugins during template runtime
	 *
	 * @var array
	 */
	protected $runtimePlugins;

	/**
	 * stores the data during template runtime
	 *
	 * @var array
	 * @private
	 */
	public $data;

	/**
	 * stores the current scope during template runtime
	 *
	 * this should ideally not be accessed directly from outside template code
	 *
	 * @var mixed
	 * @private
	 */
	public $scope;

	/**
	 * stores the scope tree during template runtime
	 *
	 * @var array
	 */
	protected $scopeTree;

	/**
	 * stores the block plugins stack during template runtime
	 *
	 * @var array
	 */
	protected $stack;

	/**
	 * stores the current block plugin at the top of the stack during template runtime
	 *
	 * @var Dwoo_Block_Plugin
	 */
	protected $curBlock;

	/**
	 * stores the output buffer during template runtime
	 *
	 * @var string
	 */
	protected $buffer;

	/**
	 * stores plugin proxy
	 *
	 * @var Dwoo_IPluginProxy
	 */
	protected $pluginProxy;

 	/**
	 * constructor, sets the cache and compile dir to the default values if not provided
	 *
	 * @param string $compileDir path to the compiled directory, defaults to lib/compiled
	 * @param string $cacheDir path to the cache directory, defaults to lib/cache
	 */
	public function __construct($compileDir = null, $cacheDir = null)
	{
		if ($compileDir !== null) {
			$this->setCompileDir($compileDir);
		}
		if ($cacheDir !== null) {
			$this->setCacheDir($cacheDir);
		}
		$this->initGlobals();
	}

	/**
	 * resets some runtime variables to allow a cloned object to be used to render sub-templates
	 */
	public function __clone()
	{
		$this->template = null;
		unset($this->data);
	}

	/**
	 * outputs the template instead of returning it, this is basically a shortcut for get(*, *, *, true)
	 *
	 * @see get
	 * @param mixed $tpl template, can either be a Dwoo_ITemplate object (i.e. Dwoo_Template_File), a valid path to a template, or
	 * 					 a template as a string it is recommended to provide a Dwoo_ITemplate as it will probably make things faster,
	 * 					 especially if you render a template multiple times
	 * @param mixed $data the data to use, can either be a Dwoo_IDataProvider object (i.e. Dwoo_Data) or an associative array. if you're
	 * 					  rendering the template from cache, it can be left null
	 * @param Dwoo_ICompiler $compiler the compiler that must be used to compile the template, if left empty a default
	 * 								  Dwoo_Compiler will be used.
	 * @return string nothing or the template output if $output is true
	 */
	public function output($tpl, $data = array(), Dwoo_ICompiler $compiler = null)
	{
		return $this->get($tpl, $data, $compiler, true);
	}

	/**
	 * returns the given template rendered using the provided data and optional compiler
	 *
	 * @param mixed $tpl template, can either be a Dwoo_ITemplate object (i.e. Dwoo_Template_File), a valid path to a template, or
	 * 					 a template as a string it is recommended to provide a Dwoo_ITemplate as it will probably make things faster,
	 * 					 especially if you render a template multiple times
	 * @param mixed $data the data to use, can either be a Dwoo_IDataProvider object (i.e. Dwoo_Data) or an associative array. if you're
	 * 					  rendering the template from cache, it can be left null
	 * @param Dwoo_ICompiler $compiler the compiler that must be used to compile the template, if left empty a default
	 * 								  Dwoo_Compiler will be used.
	 * @param bool $output flag that defines whether the function returns the output of the template (false, default) or echoes it directly (true)
	 * @return string nothing or the template output if $output is true
	 */
	public function get($_tpl, $data = array(), $_compiler = null, $_output = false)
	{
		// a render call came from within a template, so we need a new dwoo instance in order to avoid breaking this one
		if ($this->template instanceof Dwoo_ITemplate) {
			$proxy = clone $this;
			return $proxy->get($_tpl, $data, $_compiler, $_output);
		}

		// auto-create template if required
		if ($_tpl instanceof Dwoo_ITemplate) {
			// valid, skip
		} elseif (is_string($_tpl) && file_exists($_tpl)) {
			$_tpl = new Dwoo_Template_File($_tpl);
		} else {
			throw new Dwoo_Exception('Dwoo->get/Dwoo->output\'s first argument must be a Dwoo_ITemplate (i.e. Dwoo_Template_File) or a valid path to a template file', E_USER_NOTICE);
		}

		// save the current template, enters render mode at the same time
		// if another rendering is requested it will be proxied to a new Dwoo instance
		$this->template = $_tpl;

		// load data
		if ($data instanceof Dwoo_IDataProvider) {
			$this->data = $data->getData();
		} elseif (is_array($data)) {
			$this->data = $data;
		} else {
			throw new Dwoo_Exception('Dwoo->get/Dwoo->output\'s data argument must be a Dwoo_IDataProvider object (i.e. Dwoo_Data) or an associative array', E_USER_NOTICE);
		}

		$this->globals['template'] = $_tpl->getName();
		$this->initRuntimeVars($_tpl);

		// try to get cached template
		$file = $_tpl->getCachedTemplate($this);
		$doCache = $file === true;
		$cacheLoaded = is_string($file);

		if ($cacheLoaded === true) {
			// cache is present, run it
			if ($_output === true) {
				include $file;
				$this->template = null;
			} else {
				ob_start();
				include $file;
				$this->template = null;
				return ob_get_clean();
			}
		} else {
			// no cache present
			if ($doCache === true) {
				$dynamicId = uniqid();
			}

			// render template
			$compiledTemplate = $_tpl->getCompiledTemplate($this, $_compiler);
			$out = include $compiledTemplate;

			// template returned false so it needs to be recompiled
			if ($out === false) {
				$_tpl->forceCompilation();
				$compiledTemplate = $_tpl->getCompiledTemplate($this, $_compiler);
				$out = include $compiledTemplate;
			}

			if ($doCache === true) {
				$out = preg_replace('/(<%|%>|<\?php|<\?|\?>)/', '<?php /*'.$dynamicId.'*/ echo \'$1\'; ?>', $out);
				if (!class_exists('Dwoo_plugin_dynamic', false)) {
					$this->getLoader()->loadPlugin('dynamic');
				}
				$out = Dwoo_Plugin_dynamic::unescape($out, $dynamicId, $compiledTemplate);
			}

			// process filters
			foreach ($this->filters as $filter) {
				if (is_array($filter) && $filter[0] instanceof Dwoo_Filter) {
					$out = call_user_func($filter, $out);
				} else {
					$out = call_user_func($filter, $this, $out);
				}
			}

			if ($doCache === true) {
				// building cache
				$file = $_tpl->cache($this, $out);

				// run it from the cache to be sure dynamics are rendered
				if ($_output === true) {
					include $file;
					// exit render mode
					$this->template = null;
				} else {
					ob_start();
					include $file;
					// exit render mode
					$this->template = null;
					return ob_get_clean();
				}
			} else {
				// no need to build cache
				// exit render mode
				$this->template = null;
				// output
				if ($_output === true) {
					echo $out;
				}
				return $out;
			}
		}
	}

	/**
	 * re-initializes the globals array before each template run
	 *
	 * this method is only callede once when the Dwoo object is created
	 */
	protected function initGlobals()
	{
		$this->globals = array
		(
			'version'	=>	self::VERSION,
			'ad'		=>	'<a href="http://dwoo.org/">Powered by Dwoo</a>',
			'now'		=>	$_SERVER['REQUEST_TIME'],
			'charset'	=>	$this->charset,
		);
	}

	/**
	 * re-initializes the runtime variables before each template run
	 *
	 * override this method to inject data in the globals array if needed, this
	 * method is called before each template execution
	 *
	 * @param Dwoo_ITemplate $tpl the template that is going to be rendered
	 */
	protected function initRuntimeVars(Dwoo_ITemplate $tpl)
	{
		$this->runtimePlugins = array();
		$this->scope =& $this->data;
		$this->scopeTree = array();
		$this->stack = array();
		$this->curBlock = null;
		$this->buffer = '';
	}

	/*
	 * --------- settings functions ---------
	 */

	/**
	 * adds a custom plugin that is not in one of the plugin directories
	 *
	 * @param string $name the plugin name to be used in the templates
	 * @param callback $callback the plugin callback, either a function name,
	 * 							 a class name or an array containing an object
	 * 							 or class name and a method name
	 * @param bool $compilable if set to true, the plugin is assumed to be compilable
	 */
	public function addPlugin($name, $callback, $compilable = false)
	{
		$compilable = $compilable ? self::COMPILABLE_PLUGIN : 0;
		if (is_array($callback)) {
			if (is_subclass_of(is_object($callback[0]) ? get_class($callback[0]) : $callback[0], 'Dwoo_Block_Plugin')) {
				$this->plugins[$name] = array('type'=>self::BLOCK_PLUGIN | $compilable, 'callback'=>$callback, 'class'=>(is_object($callback[0]) ? get_class($callback[0]) : $callback[0]));
			} else {
				$this->plugins[$name] = array('type'=>self::CLASS_PLUGIN | $compilable, 'callback'=>$callback, 'class'=>(is_object($callback[0]) ? get_class($callback[0]) : $callback[0]), 'function'=>$callback[1]);
			}
		} elseif (class_exists($callback, false)) {
			if (is_subclass_of($callback, 'Dwoo_Block_Plugin')) {
				$this->plugins[$name] = array('type'=>self::BLOCK_PLUGIN | $compilable, 'callback'=>$callback, 'class'=>$callback);
			} else {
				$this->plugins[$name] = array('type'=>self::CLASS_PLUGIN | $compilable, 'callback'=>$callback, 'class'=>$callback, 'function'=>'process');
			}
		} elseif (function_exists($callback)) {
			$this->plugins[$name] = array('type'=>self::FUNC_PLUGIN | $compilable, 'callback'=>$callback);
		} else {
			throw new Dwoo_Exception('Callback could not be processed correctly, please check that the function/class you used exists');
		}
	}

	/**
	 * removes a custom plugin
	 *
	 * @param string $name the plugin name
	 */
	public function removePlugin($name)
	{
		if (isset($this->plugins[$name])) {
			unset($this->plugins[$name]);
		}
	}

	/**
	 * adds a filter to this Dwoo instance, it will be used to filter the output of all the templates rendered by this instance
	 *
	 * @param mixed $callback a callback or a filter name if it is autoloaded from a plugin directory
	 * @param bool $autoload if true, the first parameter must be a filter name from one of the plugin directories
	 */
	public function addFilter($callback, $autoload = false)
	{
		if ($autoload) {
			$class = 'Dwoo_Filter_'.$callback;

			if (!class_exists($class, false) && !function_exists($class)) {
				try {
					$this->getLoader()->loadPlugin($callback);
				} catch (Dwoo_Exception $e) {
					if (strstr($callback, 'Dwoo_Filter_')) {
						throw new Dwoo_Exception('Wrong filter name : '.$callback.', the "Dwoo_Filter_" prefix should not be used, please only use "'.str_replace('Dwoo_Filter_', '', $callback).'"');
					} else {
						throw new Dwoo_Exception('Wrong filter name : '.$callback.', when using autoload the filter must be in one of your plugin dir as "name.php" containg a class or function named "Dwoo_Filter_name"');
					}
				}
			}

			if (class_exists($class, false)) {
				$callback = array(new $class($this), 'process');
			} elseif (function_exists($class)) {
				$callback = $class;
			} else {
				throw new Dwoo_Exception('Wrong filter name : '.$callback.', when using autoload the filter must be in one of your plugin dir as "name.php" containg a class or function named "Dwoo_Filter_name"');
			}

			$this->filters[] = $callback;
		} else {
			$this->filters[] = $callback;
		}
	}

	/**
	 * removes a filter
	 *
	 * @param mixed $callback callback or filter name if it was autoloaded
	 */
	public function removeFilter($callback)
	{
		if (($index = array_search('Dwoo_Filter_'.$callback, $this->filters, true)) !== false) {
			unset($this->filters[$index]);
		} elseif (($index = array_search($callback, $this->filters, true)) !== false) {
			unset($this->filters[$index]);
		} else	{
			$class = 'Dwoo_Filter_' . $callback;
			foreach ($this->filters as $index=>$filter) {
				if (is_array($filter) && $filter[0] instanceof $class) {
					unset($this->filters[$index]);
					break;
				}
			}
		}
	}

	/**
	 * adds a resource or overrides a default one
	 *
	 * @param string $name the resource name
	 * @param string $class the resource class (which must implement Dwoo_ITemplate)
	 * @param callback $compilerFactory the compiler factory callback, a function that must return a compiler instance used to compile this resource, if none is provided. by default it will produce a Dwoo_Compiler object
	 */
	public function addResource($name, $class, $compilerFactory = null)
	{
		if (strlen($name) < 2) {
			throw new Dwoo_Exception('Resource names must be at least two-character long to avoid conflicts with Windows paths');
		}

		if (!class_exists($class)) {
			throw new Dwoo_Exception('Resource class does not exist');
		}

		$interfaces = class_implements($class);
		if (in_array('Dwoo_ITemplate', $interfaces) === false) {
			throw new Dwoo_Exception('Resource class must implement Dwoo_ITemplate');
		}

		$this->resources[$name] = array('class'=>$class, 'compiler'=>$compilerFactory);
	}

	/**
	 * removes a custom resource
	 *
	 * @param string $name the resource name
	 */
	public function removeResource($name)
	{
		unset($this->resources[$name]);
		if ($name==='file') {
			$this->resources['file'] = array('class'=>'Dwoo_Template_File', 'compiler'=>null);
		}
	}

	/*
	 * --------- getters and setters ---------
	 */

	/**
	 * sets the loader object to use to load plugins
	 *
	 * @param Dwoo_ILoader $loader loader object
	 */
	public function setLoader(Dwoo_ILoader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * returns the current loader object or a default one if none is currently found
	 *
	 * @param Dwoo_ILoader
	 */
	public function getLoader()
	{
		if ($this->loader === null) {
			$this->loader = new Dwoo_Loader($this->getCompileDir());
		}

		return $this->loader;
	}

	/**
	 * returns the custom plugins loaded
	 *
	 * used by the Dwoo_ITemplate classes to pass the custom plugins to their Dwoo_ICompiler instance
	 *
	 * @return array
	 */
	public function getCustomPlugins()
	{
		return $this->plugins;
	}

	/**
	 * returns the cache directory with a trailing DIRECTORY_SEPARATOR
	 *
	 * @return string
	 */
	public function getCacheDir()
	{
		if ($this->cacheDir === null) {
			$this->setCacheDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);
		}

		return $this->cacheDir;
	}

	/**
	 * sets the cache directory and automatically appends a DIRECTORY_SEPARATOR
	 *
	 * @param string $dir the cache directory
	 */
	public function setCacheDir($dir)
	{
		$this->cacheDir = rtrim($dir, '/\\').DIRECTORY_SEPARATOR;
		if (is_writable($this->cacheDir) === false) {
			throw new Dwoo_Exception('The cache directory must be writable, chmod "'.$this->cacheDir.'" to make it writable');
		}
	}

	/**
	 * returns the compile directory with a trailing DIRECTORY_SEPARATOR
	 *
	 * @return string
	 */
	public function getCompileDir()
	{
		if ($this->compileDir === null) {
			$this->setCompileDir(dirname(__FILE__).DIRECTORY_SEPARATOR.'compiled'.DIRECTORY_SEPARATOR);
		}

		return $this->compileDir;
	}

	/**
	 * sets the compile directory and automatically appends a DIRECTORY_SEPARATOR
	 *
	 * @param string $dir the compile directory
	 */
	public function setCompileDir($dir)
	{
		$this->compileDir = rtrim($dir, '/\\').DIRECTORY_SEPARATOR;
		if (is_writable($this->compileDir) === false) {
			throw new Dwoo_Exception('The compile directory must be writable, chmod "'.$this->compileDir.'" to make it writable');
		}
	}

	/**
	 * returns the default cache time that is used with templates that do not have a cache time set
	 *
	 * @return int the duration in seconds
	 */
	public function getCacheTime()
	{
		return $this->cacheTime;
	}

	/**
	 * sets the default cache time to use with templates that do not have a cache time set
	 *
	 * @param int $seconds the duration in seconds
	 */
	public function setCacheTime($seconds)
	{
		$this->cacheTime = (int) $seconds;
	}

	/**
	 * returns the character set used by the string manipulation plugins
	 *
	 * the charset is automatically lowercased
	 *
	 * @return string
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * sets the character set used by the string manipulation plugins
	 *
	 * the charset will be automatically lowercased
	 *
	 * @param string $charset the character set
	 */
	public function setCharset($charset)
	{
		$this->charset = strtolower((string) $charset);
	}

	/**
	 * returns the current template being rendered, when applicable, or null
	 *
	 * @return Dwoo_ITemplate|null
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * sets the current template being rendered
	 *
	 * @param Dwoo_ITemplate $tpl template object
	 */
	public function setTemplate(Dwoo_ITemplate $tpl)
	{
		$this->template = $tpl;
	}

	/**
	 * sets the default compiler factory function for the given resource name
	 *
	 * a compiler factory must return a Dwoo_ICompiler object pre-configured to fit your needs
	 *
	 * @param string $resourceName the resource name (i.e. file, string)
	 * @param callback $compilerFactory the compiler factory callback
	 */
	public function setDefaultCompilerFactory($resourceName, $compilerFactory)
	{
		$this->resources[$resourceName]['compiler'] = $compilerFactory;
	}

	/**
	 * returns the default compiler factory function for the given resource name
	 *
	 * @param string $resourceName the resource name
	 * @return callback the compiler factory callback
	 */
	public function getDefaultCompilerFactory($resourceName)
	{
		return $this->resources[$resourceName]['compiler'];
	}

	/**
	 * sets the security policy object to enforce some php security settings
	 *
	 * use this if untrusted persons can modify templates
	 *
	 * @param Dwoo_Security_Policy $policy the security policy object
	 */
	public function setSecurityPolicy(Dwoo_Security_Policy $policy = null)
	{
		$this->securityPolicy = $policy;
	}

	/**
	 * returns the current security policy object or null by default
	 *
	 * @return Dwoo_Security_Policy|null the security policy object if any
	 */
	public function getSecurityPolicy()
	{
		return $this->securityPolicy;
	}

	/**
	 * sets the object that must be used as a plugin proxy when plugin can't be found
	 * by dwoo's loader
	 *
	 * @param Dwoo_IPluginProxy $pluginProxy the proxy object
	 */
	public function setPluginProxy(Dwoo_IPluginProxy $pluginProxy) {
		$this->pluginProxy = $pluginProxy;
	}

	/**
	 * returns the current plugin proxy object or null by default
	 *
	 * @param Dwoo_IPluginProxy|null the proxy object if any
	 */
	public function getPluginProxy() {
		return $this->pluginProxy;
	}

	/*
	 * --------- util functions ---------
	 */

	/**
	 * [util function] checks whether the given template is cached or not
	 *
	 * @param Dwoo_ITemplate $tpl the template object
	 * @return bool
	 */
	public function isCached(Dwoo_ITemplate $tpl)
	{
		return is_string($tpl->getCachedTemplate($this));
	}

	/**
	 * [util function] clears the cached templates if they are older than the given time
	 *
	 * @param int $olderThan minimum time (in seconds) required for a cached template to be cleared
	 * @return int the amount of templates cleared
	 */
	public function clearCache($olderThan=-1)
	{
		$cacheDirs = new RecursiveDirectoryIterator($this->getCacheDir());
		$cache = new RecursiveIteratorIterator($cacheDirs);
		$expired = time() - $olderThan;
		$count = 0;
		foreach ($cache as $file) {
			if ($cache->isDot() || $cache->isDir() || substr($file, -5) !== '.html') {
				continue;
			}
			if ($cache->getCTime() < $expired) {
				$count += unlink((string) $file) ? 1 : 0;
			}
		}
		return $count;
	}

	/**
	 * [util function] fetches a template object of the given resource
	 *
	 * @param string $resourceName the resource name (i.e. file, string)
	 * @param string $resourceId the resource identifier (i.e. file path)
	 * @param int $cacheTime the cache time setting for this resource
	 * @param string $cacheId the unique cache identifier
	 * @param string $compileId the unique compiler identifier
	 * @return Dwoo_ITemplate
	 */
	public function templateFactory($resourceName, $resourceId, $cacheTime = null, $cacheId = null, $compileId = null, Dwoo_ITemplate $parentTemplate = null)
	{
		if (isset($this->resources[$resourceName])) {
			// TODO could be changed to $this->resources[$resourceName]['class']::templateFactory(..) in 5.3 maybe
			return call_user_func(array($this->resources[$resourceName]['class'], 'templateFactory'), $this, $resourceId, $cacheTime, $cacheId, $compileId, $parentTemplate);
		} else {
			throw new Dwoo_Exception('Unknown resource type : '.$resourceName);
		}
	}

	/**
	 * [util function] checks if the input is an array or an iterator object, optionally it can also check if it's empty
	 *
	 * @param mixed $value the variable to check
	 * @param bool $checkIsEmpty if true, the function will also check if the array is empty,
	 * 								and return true only if it's not empty
	 * @return bool true if it's an array (and not empty) or false if it's not an array (or if it's empty)
	 */
	public function isArray($value, $checkIsEmpty=false)
	{
		if (is_array($value) === true) {
			if ($checkIsEmpty === false) {
				return true;
			} else {
				return count($value) > 0;
			}
		} elseif ($value instanceof Iterator) {
			if ($checkIsEmpty === false) {
				return true;
			} elseif ($value instanceof Countable) {
				return count($value) > 0;
			} else {
				$value->rewind();
				return $value->valid();
			}
		} elseif ($value instanceof ArrayAccess) {
			if ($checkIsEmpty === false) {
				return true;
			} elseif ($value instanceof Countable) {
				return count($value) > 0;
			} else {
				return $value->offsetExists(0);
			}
		}
		return false;
	}

	/**
	 * [util function] triggers a dwoo error
	 *
	 * @param string $message the error message
	 * @param int $level the error level, one of the PHP's E_* constants
	 */
	public function triggerError($message, $level=E_USER_NOTICE)
	{
		if (!($tplIdentifier = $this->template->getResourceIdentifier())) {
			$tplIdentifier = $this->template->getResourceName();
		}
		trigger_error('Dwoo error (in '.$tplIdentifier.') : '.$message, $level);
	}

	/*
	 * --------- runtime functions ---------
	 */

	/**
	 * [runtime function] adds a block to the block stack
	 *
	 * @param string $blockName the block name (without Dwoo_Plugin_ prefix)
	 * @param array $args the arguments to be passed to the block's init() function
	 * @return Dwoo_Block_Plugin the newly created block
	 */
	public function addStack($blockName, array $args=array())
	{
		if (isset($this->plugins[$blockName])) {
			$class = $this->plugins[$blockName]['class'];
		} else {
			$class = 'Dwoo_Plugin_'.$blockName;
		}

		if ($this->curBlock !== null) {
			$this->curBlock->buffer(ob_get_contents());
			ob_clean();
		} else {
			$this->buffer .= ob_get_contents();
			ob_clean();
		}

		$block = new $class($this);

		$cnt = count($args);
		if ($cnt===0) {
			$block->init();
		} elseif ($cnt===1) {
			$block->init($args[0]);
		} elseif ($cnt===2) {
			$block->init($args[0], $args[1]);
		} elseif ($cnt===3) {
			$block->init($args[0], $args[1], $args[2]);
		} elseif ($cnt===4) {
			$block->init($args[0], $args[1], $args[2], $args[3]);
		} else {
			call_user_func_array(array($block,'init'), $args);
		}

		$this->stack[] = $this->curBlock = $block;
		return $block;
	}

	/**
	 * [runtime function] removes the plugin at the top of the block stack
	 *
	 * calls the block buffer() function, followed by a call to end()
	 * and finally a call to process()
	 */
	public function delStack()
	{
		$args = func_get_args();

		$this->curBlock->buffer(ob_get_contents());
		ob_clean();

		$cnt = count($args);
		if ($cnt===0) {
			$this->curBlock->end();
		} elseif ($cnt===1) {
			$this->curBlock->end($args[0]);
		} elseif ($cnt===2) {
			$this->curBlock->end($args[0], $args[1]);
		} elseif ($cnt===3) {
			$this->curBlock->end($args[0], $args[1], $args[2]);
		} elseif ($cnt===4) {
			$this->curBlock->end($args[0], $args[1], $args[2], $args[3]);
		} else {
			call_user_func_array(array($this->curBlock, 'end'), $args);
		}

		$tmp = array_pop($this->stack);

		if (count($this->stack) > 0) {
			$this->curBlock = end($this->stack);
			$this->curBlock->buffer($tmp->process());
		} else {
			$this->curBlock = null;
			echo $tmp->process();
		}

		unset($tmp);
	}

	/**
	 * [runtime function] returns the parent block of the given block
	 *
	 * @param Dwoo_Block_Plugin $block
	 * @return Dwoo_Block_Plugin or false if the given block isn't in the stack
	 */
	public function getParentBlock(Dwoo_Block_Plugin $block)
	{
		$index = array_search($block, $this->stack, true);
		if ($index !== false && $index > 0) {
			return $this->stack[$index-1];
		}
		return false;
	}

	/**
	 * [runtime function] finds the closest block of the given type, starting at the top of the stack
	 *
	 * @param string $type the type of plugin you want to find
	 * @return Dwoo_Block_Plugin or false if no plugin of such type is in the stack
	 */
	public function findBlock($type)
	{
		if (isset($this->plugins[$type])) {
			$type = $this->plugins[$type]['class'];
		} else {
			$type = 'Dwoo_Plugin_'.str_replace('Dwoo_Plugin_', '', $type);
		}

		$keys = array_keys($this->stack);
		while (($key = array_pop($keys)) !== false) {
			if ($this->stack[$key] instanceof $type) {
				return $this->stack[$key];
			}
		}
		return false;
	}

	/**
	 * [runtime function] returns a Dwoo_Plugin of the given class
	 *
	 * this is so a single instance of every class plugin is created at each template run,
	 * allowing class plugins to have "per-template-run" static variables
	 *
	 * @private
	 * @param string $class the class name
	 * @return mixed an object of the given class
	 */
	public function getObjectPlugin($class)
	{
		if (isset($this->runtimePlugins[$class])) {
			return $this->runtimePlugins[$class];
		}
		return $this->runtimePlugins[$class] = new $class($this);
	}

	/**
	 * [runtime function] calls the process() method of the given class-plugin name
	 *
	 * @param string $plugName the class plugin name (without Dwoo_Plugin_ prefix)
	 * @param array $params an array of parameters to send to the process() method
	 * @return string the process() return value
	 */
	public function classCall($plugName, array $params = array())
	{
		$class = 'Dwoo_Plugin_'.$plugName;

		$plugin = $this->getObjectPlugin($class);

		$cnt = count($params);
		if ($cnt===0) {
			return $plugin->process();
		} elseif ($cnt===1) {
			return $plugin->process($params[0]);
		} elseif ($cnt===2) {
			return $plugin->process($params[0], $params[1]);
		} elseif ($cnt===3) {
			return $plugin->process($params[0], $params[1], $params[2]);
		} elseif ($cnt===4) {
			return $plugin->process($params[0], $params[1], $params[2], $params[3]);
		} else {
			return call_user_func_array(array($plugin, 'process'), $params);
		}
	}

	/**
	 * [runtime function] calls a php function
	 *
	 * @param string $callback the function to call
	 * @param array $params an array of parameters to send to the function
	 * @return mixed the return value of the called function
	 */
	public function arrayMap($callback, array $params)
	{
		if ($params[0] === $this) {
			$addThis = true;
			array_shift($params);
		}
		if ((is_array($params[0]) || ($params[0] instanceof Iterator && $params[0] instanceof ArrayAccess))) {
			if (empty($params[0])) {
				return $params[0];
			}

			// array map
			$out = array();
			$cnt = count($params);

			if (isset($addThis)) {
				array_unshift($params, $this);
				$items = $params[1];
				$keys = array_keys($items);

				if (is_string($callback) === false) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = call_user_func_array($callback, array(1=>$items[$i]) + $params);
					}
				} elseif ($cnt===1) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($this, $items[$i]);
					}
				} elseif ($cnt===2) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($this, $items[$i], $params[2]);
					}
				} elseif ($cnt===3) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($this, $items[$i], $params[2], $params[3]);
					}
				} else {
					while (($i = array_shift($keys)) !== null) {
						$out[] = call_user_func_array($callback, array(1=>$items[$i]) + $params);
					}
				}
			} else {
				$items = $params[0];
				$keys = array_keys($items);

				if (is_string($callback) === false) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = call_user_func_array($callback, array($items[$i]) + $params);
					}
				} elseif ($cnt===1) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($items[$i]);
					}
				} elseif ($cnt===2) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($items[$i], $params[1]);
					}
				} elseif ($cnt===3) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($items[$i], $params[1], $params[2]);
					}
				} elseif ($cnt===4) {
					while (($i = array_shift($keys)) !== null) {
						$out[] = $callback($items[$i], $params[1], $params[2], $params[3]);
					}
				} else {
					while (($i = array_shift($keys)) !== null) {
						$out[] = call_user_func_array($callback, array($items[$i]) + $params);
					}
				}
			}
			return $out;
		} else {
			return $params[0];
		}
	}

	/**
	 * [runtime function] reads a variable into the given data array
	 *
	 * @param string $varstr the variable string, using dwoo variable syntax (i.e. "var.subvar[subsubvar]->property")
	 * @param mixed $data the data array or object to read from
	 * @param bool $safeRead if true, the function will check whether the index exists to prevent any notices from being output
	 * @return mixed
	 */
	public function readVarInto($varstr, $data, $safeRead = false)
	{
		if ($data === null) {
			return null;
		}

		if (is_array($varstr) === false) {
			preg_match_all('#(\[|->|\.)?((?:[^.[\]-]|-(?!>))+)\]?#i', $varstr, $m);
		} else {
			$m = $varstr;
		}
		unset($varstr);

		while (list($k, $sep) = each($m[1])) {
			if ($sep === '.' || $sep === '[' || $sep === '') {
				if ((is_array($data) || $data instanceof ArrayAccess) && ($safeRead === false || isset($data[$m[2][$k]]))) {
					$data = $data[$m[2][$k]];
				} else {
					return null;
				}
			} else {
				if (is_object($data) && ($safeRead === false || isset($data->$m[2][$k]) || is_callable(array($data, '__get')))) {
					$data = $data->$m[2][$k];
				} else {
					return null;
				}
			}
		}

		return $data;
	}

	/**
	 * [runtime function] reads a variable into the parent scope
	 *
	 * @param int $parentLevels the amount of parent levels to go from the current scope
	 * @param string $varstr the variable string, using dwoo variable syntax (i.e. "var.subvar[subsubvar]->property")
	 * @return mixed
	 */
	public function readParentVar($parentLevels, $varstr = null)
	{
		$tree = $this->scopeTree;
		$cur = $this->data;

		while ($parentLevels--!==0) {
			array_pop($tree);
		}

		while (($i = array_shift($tree)) !== null) {
			if (is_object($cur)) {
				$cur = $cur->$i;
			} else {
				$cur = $cur[$i];
			}
		}

		if ($varstr!==null) {
			return $this->readVarInto($varstr, $cur);
		} else {
			return $cur;
		}
	}

	/**
	 * [runtime function] reads a variable into the current scope
	 *
	 * @param string $varstr the variable string, using dwoo variable syntax (i.e. "var.subvar[subsubvar]->property")
	 * @return mixed
	 */
	public function readVar($varstr)
	{
		if (is_array($varstr)===true) {
			$m = $varstr;
			unset($varstr);
		} else {
			if (strstr($varstr, '.') === false && strstr($varstr, '[') === false && strstr($varstr, '->') === false) {
				if ($varstr === 'dwoo') {
					return $this->globals;
				} elseif ($varstr === '__' || $varstr === '_root' ) {
					return $this->data;
					$varstr = substr($varstr, 6);
				} elseif ($varstr === '_' || $varstr === '_parent') {
					$varstr = '.'.$varstr;
					$tree = $this->scopeTree;
					$cur = $this->data;
					array_pop($tree);

					while (($i = array_shift($tree)) !== null) {
						if (is_object($cur)) {
							$cur = $cur->$i;
						} else {
							$cur = $cur[$i];
						}
					}

					return $cur;
				}

				$cur = $this->scope;

				if (isset($cur[$varstr])) {
					return $cur[$varstr];
				} else {
					return null;
				}
			}

			if (substr($varstr, 0, 1) === '.') {
				$varstr = 'dwoo'.$varstr;
			}

			preg_match_all('#(\[|->|\.)?((?:[^.[\]-]|-(?!>))+)\]?#i', $varstr, $m);
		}

		$i = $m[2][0];
		if ($i === 'dwoo') {
			$cur = $this->globals;
			array_shift($m[2]);
			array_shift($m[1]);
			switch ($m[2][0]) {

			case 'get':
				$cur = $_GET;
				break;
			case 'post':
				$cur = $_POST;
				break;
			case 'session':
				$cur = $_SESSION;
				break;
			case 'cookies':
			case 'cookie':
				$cur = $_COOKIE;
				break;
			case 'server':
				$cur = $_SERVER;
				break;
			case 'env':
				$cur = $_ENV;
				break;
			case 'request':
				$cur = $_REQUEST;
				break;
			case 'const':
				array_shift($m[2]);
				if (defined($m[2][0])) {
					return constant($m[2][0]);
				} else {
					return null;
				}

			}
			if ($cur !== $this->globals) {
				array_shift($m[2]);
				array_shift($m[1]);
			}
		} elseif ($i === '__' || $i === '_root') {
			$cur = $this->data;
			array_shift($m[2]);
			array_shift($m[1]);
		} elseif ($i === '_' || $i === '_parent') {
			$tree = $this->scopeTree;
			$cur = $this->data;

			while (true) {
				array_pop($tree);
				array_shift($m[2]);
				array_shift($m[1]);
				if (current($m[2]) === '_' || current($m[2]) === '_parent') {
					continue;
				}

				while (($i = array_shift($tree)) !== null) {
					if (is_object($cur)) {
						$cur = $cur->$i;
					} else {
						$cur = $cur[$i];
					}
				}
				break;
			}
		} else {
			$cur = $this->scope;
		}

		while (list($k, $sep) = each($m[1])) {
			if ($sep === '.' || $sep === '[' || $sep === '') {
				if ((is_array($cur) || $cur instanceof ArrayAccess) && isset($cur[$m[2][$k]])) {
					$cur = $cur[$m[2][$k]];
				} else {
					return null;
				}
			} elseif ($sep === '->') {
				if (is_object($cur)) {
					$cur = $cur->$m[2][$k];
				} else {
					return null;
				}
			} else {
				return null;
			}
		}

		return $cur;
	}

	/**
	 * [runtime function] assign the value to the given variable
	 *
	 * @param mixed $value the value to assign
	 * @param string $scope the variable string, using dwoo variable syntax (i.e. "var.subvar[subsubvar]->property")
	 * @return bool true if assigned correctly or false if a problem occured while parsing the var string
	 */
	public function assignInScope($value, $scope)
	{
		$tree =& $this->scopeTree;
		$data =& $this->data;

		if (!is_string($scope)) {
			return $this->triggerError('Assignments must be done into strings, ('.gettype($scope).') '.var_export($scope, true).' given', E_USER_ERROR);
		}
		if (strstr($scope, '.') === false && strstr($scope, '->') === false) {
			$this->scope[$scope] = $value;
		} else {
			// TODO handle _root/_parent scopes ?
			preg_match_all('#(\[|->|\.)?([^.[\]-]+)\]?#i', $scope, $m);

			$cur =& $this->scope;
			$last = array(array_pop($m[1]), array_pop($m[2]));

			while (list($k, $sep) = each($m[1])) {
				if ($sep === '.' || $sep === '[' || $sep === '') {
					if (is_array($cur) === false) {
						$cur = array();
					}
					$cur =& $cur[$m[2][$k]];
				} elseif ($sep === '->') {
					if (is_object($cur) === false) {
						$cur = new stdClass;
					}
					$cur =& $cur->$m[2][$k];
				} else {
					return false;
				}
			}

			if ($last[0] === '.' || $last[0] === '[' || $last[0] === '') {
				if (is_array($cur) === false) {
					$cur = array();
				}
				$cur[$last[1]] = $value;
			} elseif ($last[0] === '->') {
				if (is_object($cur) === false) {
					$cur = new stdClass;
				}
				$cur->$last[1] = $value;
			} else {
				return false;
			}
		}
	}

	/**
	 * [runtime function] sets the scope to the given scope string or array
	 *
	 * @param mixed $scope a string i.e. "level1.level2" or an array i.e. array("level1", "level2")
	 * @param bool $absolute if true, the scope is set from the top level scope and not from the current scope
	 * @return array the current scope tree
	 */
	public function setScope($scope, $absolute = false)
	{
		$old = $this->scopeTree;

		if (is_string($scope)===true) {
			$scope = explode('.', $scope);
		}

		if ($absolute===true) {
			$this->scope =& $this->data;
			$this->scopeTree = array();
		}

		while (($bit = array_shift($scope)) !== null) {
			if ($bit === '_' || $bit === '_parent') {
				array_pop($this->scopeTree);
				$this->scope =& $this->data;
				$cnt = count($this->scopeTree);
				for ($i=0;$i<$cnt;$i++)
					$this->scope =& $this->scope[$this->scopeTree[$i]];
			} elseif ($bit === '__' || $bit === '_root') {
				$this->scope =& $this->data;
				$this->scopeTree = array();
			} elseif (isset($this->scope[$bit])) {
				$this->scope =& $this->scope[$bit];
				$this->scopeTree[] = $bit;
			} else {
				unset($this->scope);
				$this->scope = null;
			}
		}

		return $old;
	}

	/**
	 * [runtime function] returns the entire data array
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * [runtime function] returns a reference to the current scope
	 *
	 * @return &mixed
	 */
	public function &getScope()
	{
		return $this->scope;
	}

	/**
	 * Redirects all calls to unexisting to plugin proxy.
	 *
	 * @param string Method name
	 * @param array  List of arguments
	 * @return mixed
	 */
	public function __call($method, $args) {
		$proxy = $this->getPluginProxy();
		if (!$proxy) {
			throw new Dwoo_Exception('Call to undefined method '.__CLASS__.'::'.$method.'()');
		}
		return call_user_func_array($proxy->getCallback($method), $args);
	}
}
