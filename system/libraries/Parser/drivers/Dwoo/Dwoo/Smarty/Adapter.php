<?php

if (!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if (!defined('SMARTY_PHP_PASSTHRU')) {
	define('SMARTY_PHP_PASSTHRU',   0);
	define('SMARTY_PHP_QUOTE',      1);
	define('SMARTY_PHP_REMOVE',     2);
	define('SMARTY_PHP_ALLOW',      3);
}

if (class_exists('Dwoo_Compiler', false) === false) {
	require dirname(dirname(__FILE__)) . '/Compiler.php';
}

/**
 * a Smarty compatibility layer for Dwoo
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
class Dwoo_Smarty__Adapter extends Dwoo
{
	// magic get/set/call functions that handle unsupported features
	public function __set($p, $v)
	{
		if ($p==='scope') {
			$this->scope = $v;
			return;
		}
		if ($p==='data') {
			$this->data = $v;
			return;
		}
		if (array_key_exists($p, $this->compat['properties']) !== false) {
			if ($this->show_compat_errors) {
				$this->triggerError('Property '.$p.' is not available in the Dwoo_Smarty_Adapter, however it might be implemented in the future, check out http://wiki.dwoo.org/index.php/SmartySupport for more details.', E_USER_NOTICE);
			}
			$this->compat['properties'][$p] = $v;
		} else {
			if ($this->show_compat_errors) {
				$this->triggerError('Property '.$p.' is not available in the Dwoo_Smarty_Adapter, but it is not listed as such, so you might want to tell me about it at j.boggiano@seld.be', E_USER_NOTICE);
			}
		}
	}

	public function __get($p)
	{
		if (array_key_exists($p, $this->compat['properties']) !== false) {
			if ($this->show_compat_errors) {
				$this->triggerError('Property '.$p.' is not available in the Dwoo_Smarty_Adapter, however it might be implemented in the future, check out http://wiki.dwoo.org/index.php/SmartySupport for more details.', E_USER_NOTICE);
			}
			return $this->compat['properties'][$p];
		} else {
			if ($this->show_compat_errors) {
				$this->triggerError('Property '.$p.' is not available in the Dwoo_Smarty_Adapter, but it is not listed as such, so you might want to tell me about it at j.boggiano@seld.be', E_USER_NOTICE);
			}
		}
	}

	public function __call($m, $a)
	{
		if (method_exists($this->dataProvider, $m)) {
			call_user_func_array(array($this->dataProvider, $m), $a);
		} elseif ($this->show_compat_errors) {
			if (array_search($m, $this->compat['methods']) !== false) {
				$this->triggerError('Method '.$m.' is not available in the Dwoo_Smarty_Adapter, however it might be implemented in the future, check out http://wiki.dwoo.org/index.php/SmartySupport for more details.', E_USER_NOTICE);
			} else {
				$this->triggerError('Method '.$m.' is not available in the Dwoo_Smarty_Adapter, but it is not listed as such, so you might want to tell me about it at j.boggiano@seld.be', E_USER_NOTICE);
			}
		}
	}

	// list of unsupported properties and methods
	protected $compat = array
	(
		'methods' => array
		(
			'register_resource', 'unregister_resource', 'load_filter', 'clear_compiled_tpl',
			'clear_config', 'get_config_vars', 'config_load'
		),
		'properties' => array
		(
			'cache_handler_func' => null,
			'debugging' => false,
			'error_reporting' => null,
			'debugging_ctrl' => 'NONE',
			'request_vars_order' => 'EGPCS',
			'request_use_auto_globals' => true,
			'use_sub_dirs' => false,
			'autoload_filters' => array(),
			'default_template_handler_func' => '',
			'debug_tpl' => '',
			'cache_modified_check' => false,
			'default_modifiers' => array(),
			'default_resource_type' => 'file',
			'config_overwrite' => true,
			'config_booleanize' => true,
			'config_read_hidden' => false,
			'config_fix_newlines' => true,
			'config_class' => 'Config_File',
		),
	);

	// security vars
	public $security = false;
	public $trusted_dir = array();
	public $secure_dir = array();
	public $php_handling = SMARTY_PHP_PASSTHRU;
	public $security_settings = array
	(
		'PHP_HANDLING'    => false,
		'IF_FUNCS'        => array
		(
			'list', 'empty', 'count', 'sizeof',
			'in_array', 'is_array',
		),
		'INCLUDE_ANY'     => false,
		'PHP_TAGS'        => false,
		'MODIFIER_FUNCS'  => array(),
		'ALLOW_CONSTANTS'  => false
	);

	// paths
	public $template_dir = 'templates';
	public $compile_dir = 'templates_c';
	public $config_dir = 'configs';
	public $cache_dir = 'cache';
	public $plugins_dir = array();

	// misc options
	public $left_delimiter = '{';
	public $right_delimiter = '}';
	public $compile_check = true;
	public $force_compile = false;
	public $caching = 0;
	public $cache_lifetime = 3600;
	public $compile_id = null;
	public $compiler_file = null;
	public $compiler_class = null;

	// dwoo/smarty compat layer
	public $show_compat_errors = false;
	protected $dataProvider;
	protected $_filters = array('pre'=>array(), 'post'=>array(), 'output'=>array());
	protected static $tplCache = array();
	protected $compiler = null;

	public function __construct()
	{
		parent::__construct();
		$this->charset = 'iso-8859-1';
		$this->dataProvider = new Dwoo_Data();
		$this->compiler = new Dwoo_Compiler();
	}

	public function display($filename, $cacheId=null, $compileId=null)
	{
		$this->fetch($filename, $cacheId, $compileId, true);
	}

	public function fetch($filename, $cacheId=null, $compileId=null, $display=false)
	{
		$this->setCacheDir($this->cache_dir);
		$this->setCompileDir($this->compile_dir);

		if ($this->security) {
			$policy = new Dwoo_Security_Policy();
			$policy->addPhpFunction(array_merge($this->security_settings['IF_FUNCS'], $this->security_settings['MODIFIER_FUNCS']));

			$phpTags = $this->security_settings['PHP_HANDLING'] ? SMARTY_PHP_ALLOW : $this->php_handling;
			if ($this->security_settings['PHP_TAGS']) {
				$phpTags = SMARTY_PHP_ALLOW;
			}
			switch($phpTags) {
				case SMARTY_PHP_ALLOW:
				case SMARTY_PHP_PASSTHRU:
					$phpTags = Dwoo_Security_Policy::PHP_ALLOW;
					break;
				case SMARTY_PHP_QUOTE:
					$phpTags = Dwoo_Security_Policy::PHP_ENCODE;
					break;
				case SMARTY_PHP_REMOVE:
				default:
					$phpTags = Dwoo_Security_Policy::PHP_REMOVE;
					break;
			}
			$policy->setPhpHandling($phpTags);

			$policy->setConstantHandling($this->security_settings['ALLOW_CONSTANTS']);

			if ($this->security_settings['INCLUDE_ANY']) {
				$policy->allowDirectory(preg_replace('{^((?:[a-z]:)?[\\\\/]).*}i', '$1', __FILE__));
			} else {
				$policy->allowDirectory($this->secure_dir);
			}

			$this->setSecurityPolicy($policy);
		}

		if (!empty($this->plugins_dir)) {
			foreach ($this->plugins_dir as $dir) {
				$this->getLoader()->addDirectory(rtrim($dir, '\\/'));
			}
		}

		$tpl = $this->makeTemplate($filename, $cacheId, $compileId);
		if ($this->force_compile) {
			$tpl->forceCompilation();
		}

		if ($this->caching > 0) {
			$this->cacheTime = $this->cache_lifetime;
		} else {
			$this->cacheTime = 0;
		}

		if ($this->compiler_class !== null) {
			if ($this->compiler_file !== null && !class_exists($this->compiler_class, false)) {
				include $this->compiler_file;
			}
			$this->compiler = new $this->compiler_class;
		} else {
			$this->compiler->addPreProcessor('smarty_compat', true);
			$this->compiler->setLooseOpeningHandling(true);
		}

		$this->compiler->setDelimiters($this->left_delimiter, $this->right_delimiter);

		return $this->get($tpl, $this->dataProvider, $this->compiler, $display===true);
	}

	public function get($_tpl, $data = array(), $_compiler = null, $_output = false)
	{
		if ($_compiler === null) {
			$_compiler = $this->compiler;
		}
		return parent::get($_tpl, $data, $_compiler, $_output);
	}

	public function register_function($name, $callback, $cacheable=true, $cache_attrs=null)
	{
		if (isset($this->plugins[$name]) && $this->plugins[$name][0] !== self::SMARTY_FUNCTION) {
			throw new Dwoo_Exception('Multiple plugins of different types can not share the same name');
		}
		$this->plugins[$name] = array('type'=>self::SMARTY_FUNCTION, 'callback'=>$callback);
	}

	public function unregister_function($name)
	{
		unset($this->plugins[$name]);
	}

	public function register_block($name, $callback, $cacheable=true, $cache_attrs=null)
	{
		if (isset($this->plugins[$name]) && $this->plugins[$name][0] !== self::SMARTY_BLOCK) {
			throw new Dwoo_Exception('Multiple plugins of different types can not share the same name');
		}
		$this->plugins[$name] = array('type'=>self::SMARTY_BLOCK, 'callback'=>$callback);
	}

	public function unregister_block($name)
	{
		unset($this->plugins[$name]);
	}

	public function register_modifier($name, $callback)
	{
		if (isset($this->plugins[$name]) && $this->plugins[$name][0] !== self::SMARTY_MODIFIER) {
			throw new Dwoo_Exception('Multiple plugins of different types can not share the same name');
		}
		$this->plugins[$name] = array('type'=>self::SMARTY_MODIFIER, 'callback'=>$callback);
	}

	public function unregister_modifier($name)
	{
		unset($this->plugins[$name]);
	}

	public function register_prefilter($callback)
	{
		$processor = new Dwoo_SmartyProcessorAdapter($this->compiler);
		$processor->registerCallback($callback);
		$this->_filters['pre'][] = $processor;
		$this->compiler->addPreProcessor($processor);
	}

	public function unregister_prefilter($callback)
	{
		foreach ($this->_filters['pre'] as $index => $processor)
			if ($processor->callback === $callback) {
				$this->compiler->removePostProcessor($processor);
				unset($this->_filters['pre'][$index]);
			}
	}

	public function register_postfilter($callback)
	{
		$processor = new Dwoo_SmartyProcessorAdapter($this->compiler);
		$processor->registerCallback($callback);
		$this->_filters['post'][] = $processor;
		$this->compiler->addPostProcessor($processor);
	}

	public function unregister_postfilter($callback)
	{
		foreach ($this->_filters['post'] as $index => $processor)
			if ($processor->callback === $callback) {
				$this->compiler->removePostProcessor($processor);
				unset($this->_filters['post'][$index]);
			}
	}

	public function register_outputfilter($callback)
	{
		$filter = new Dwoo_SmartyFilterAdapter($this);
		$filter->registerCallback($callback);
		$this->_filters['output'][] = $filter;
		$this->addFilter($filter);
	}

	public function unregister_outputfilter($callback)
	{
		foreach ($this->_filters['output'] as $index => $filter)
			if ($filter->callback === $callback) {
				$this->removeOutputFilter($filter);
				unset($this->_filters['output'][$index]);
			}
	}

	function register_object($object, $object_impl, $allowed = array(), $smarty_args = false, $block_methods = array())
	{
		settype($allowed, 'array');
		settype($block_methods, 'array');
		settype($smarty_args, 'boolean');

		if (!empty($allowed) && $this->show_compat_errors) {
			$this->triggerError('You can register objects but can not restrict the method/properties used, this is PHP5, you have proper OOP access restrictions so use them.', E_USER_NOTICE);
		}

		if ($smarty_args) {
			$this->triggerError('You can register objects but methods will be called using method($arg1, $arg2, $arg3), not as an argument array like smarty did.', E_USER_NOTICE);
		}

		if (!empty($block_methods)) {
			$this->triggerError('You can register objects but can not use methods as being block methods, you have to build a plugin for that.', E_USER_NOTICE);
		}

		$this->dataProvider->assign($object, $object_impl);
	}

	function unregister_object($object)
	{
		$this->dataProvider->clear($object);
	}

	function get_registered_object($name) {
		$data = $this->dataProvider->getData();
		if (isset($data[$name]) && is_object($data[$name])) {
			return $data[$name];
		} else {
			trigger_error('Dwoo_Compiler: object "'.$name.'" was not registered or is not an object', E_USER_ERROR);
		}
	}

	public function template_exists($filename)
	{
		if (!is_array($this->template_dir)) {
			return file_exists($this->template_dir.DIRECTORY_SEPARATOR.$filename);
		} else {
			foreach ($this->template_dir as $tpl_dir) {
				if (file_exists($tpl_dir.DIRECTORY_SEPARATOR.$filename)) {
					return true;
				}
			}
			return false;
		}
	}

   	public function is_cached($tpl, $cacheId = null, $compileId = null)
   	{
   		return $this->isCached($this->makeTemplate($tpl, $cacheId, $compileId));
   	}

   	public function append_by_ref($var, &$value, $merge=false)
   	{
   		$this->dataProvider->appendByRef($var, $value, $merge);
   	}

	public function assign_by_ref($name, &$val)
	{
		$this->dataProvider->assignByRef($name, $val);
	}

   	public function clear_assign($var)
   	{
   		$this->dataProvider->clear($var);
   	}

   	public function clear_all_assign()
   	{
   		$this->dataProvider->clear();
   	}

	public function get_template_vars($name=null)
	{
		if ($this->show_compat_errors) {
			trigger_error('get_template_vars does not return values by reference, if you try to modify the data that way you should modify your code.', E_USER_NOTICE);
		}

		$data = $this->dataProvider->getData();
   		if ($name === null)
   			return $data;
   		elseif (isset($data[$name]))
   			return $data[$name];
   		return null;
   	}

   	public function clear_all_cache($olderThan = 0)
   	{
   		$this->clearCache($olderThan);
   	}

   	public function clear_cache($template, $cacheId = null, $compileId = null, $olderThan = 0)
   	{
   		$this->makeTemplate($template, $cacheId, $compileId)->clearCache($olderThan);
   	}

	public function trigger_error($error_msg, $error_type = E_USER_WARNING)
	{
		$this->triggerError($error_msg, $error_type);
	}

	protected function initGlobals()
	{
		parent::initGlobals();
		$this->globals['ldelim'] = '{';
		$this->globals['rdelim'] = '}';
	}

	protected function makeTemplate($file, $cacheId, $compileId)
	{
   		if ($compileId === null)
   			$compileId = $this->compile_id;

		$hash = bin2hex(md5($file.$cacheId.$compileId, true));
		if (!isset(self::$tplCache[$hash])) {
			// abs path
			if (substr($file, 0, 1) === '/' || substr($file, 1, 1) === ':') {
				self::$tplCache[$hash] = new Dwoo_Template_File($file, null, $cacheId, $compileId);
			} elseif (is_string($this->template_dir) || is_array($this->template_dir)) {
				self::$tplCache[$hash] = new Dwoo_Template_File($file, null, $cacheId, $compileId, $this->template_dir);
			} else {
				throw new Exception('Unable to load "'.$file.'", check the template_dir');
			}
		}
		return self::$tplCache[$hash];
	}

	public function triggerError($message, $level=E_USER_NOTICE)
	{
		if (is_object($this->template)) {
			return parent::triggerError($message, $level);
		}
		trigger_error('Dwoo error : '.$message, $level);
	}
}

class Dwoo_Smarty_Filter_Adapter extends Dwoo_Filter
{
	public $callback;

	public function process($input)
	{
		return call_user_func($this->callback, $input);
	}

	public function registerCallback($callback)
	{
		$this->callback = $callback;
	}
}

class Dwoo_Smarty_Processor_Adapter extends Dwoo_Processor
{
	public $callback;

	public function process($input)
	{
		return call_user_func($this->callback, $input);
	}

	public function registerCallback($callback)
	{
		$this->callback = $callback;
	}
}

// cloaks the adapter if possible with the smarty name to fool type-hinted plugins
if (class_exists('Smarty', false) === false)
{
	interface Smarty {}
	class Dwoo_Smarty_Adapter extends Dwoo_Smarty__Adapter implements Smarty {}
}
else
{
	class Dwoo_Smarty_Adapter extends Dwoo_Smarty__Adapter {}
}
