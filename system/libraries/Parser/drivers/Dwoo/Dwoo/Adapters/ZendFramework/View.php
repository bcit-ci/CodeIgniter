<?php

/**
 * Dwoo adapter for ZendFramework
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the
 * use of this software.
 *
 * @author	   Denis Arh <denis@arh.cc>
 * @author     Stephan Wentz <stephan@wentz.it>
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class Dwoo_Adapters_ZendFramework_View extends Zend_View_Abstract
{
	/**
	 * @var Dwoo
	 */
	protected $_engine = null;

	/**
	 * @var Dwoo_Data
	 */
	protected $_dataProvider = null;

	/**
	 * @var Dwoo_Compiler
	 */
	protected $_compiler = null;
	
	/**
	 * Changing Filter's scope to play nicely
	 *
	 * @var array
	 */
	protected $_filter = array();
	

	/**
	 * @var string
	 */
	protected $_templateFileClass = 'Dwoo_Template_File';

	/**
	 * @var array
	 */
	protected $_templateFileSettings = array();

	/**
	 * @var Dwoo_IPluginProxy
	 */
	protected $_pluginProxy = null;

	/**
	 * Constructor method.
	 * See setOptions for $opt details
	 *
	 * @see setOptions
	 * @param array|Zend_Config List of options or Zend_Config instance
	 */
	public function __construct($opt = array())
	{
		
		if (is_array($opt)) {
			$this->setOptions($opt);
		} elseif ($opt instanceof Zend_Config) {
			$this->setConfig($opt);
		}

		$this->init();
	}

	/**
	 * Set object state from options array
	 *  - engine        = engine class name|engine object|array of options for engine
	 *  - dataProvider  = data provider class name|data provider object|array of options for data provider
	 *  - compiler      = compiler class name|compiler object|array of options for compiler
	 *  - templateFile  = 
	 *
	 *  Array of options:
	 *  - type class name or object for engine, dataProvider or compiler
	 *  - any set* method (compileDir for setCompileDir ...)
	 *
	 * @param  array $options
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setOptions(array $opt = array())
	{
		// BC checks
		// TODO remove in 1.1
		if (isset($opt['compileDir']) || isset($opt['compile_dir'])) {
			trigger_error('Dwoo ZF Adapter: the compile dir should be set in the $options[\'engine\'][\'compileDir\'] value the adapter settings', E_USER_WARNING);
		}

		if (isset($opt['cacheDir']) || isset($opt['cache_dir'])) {
			trigger_error('Dwoo ZF Adapter: the cache dir should be set in the $options[\'engine\'][\'cacheDir\'] value the adapter settings', E_USER_WARNING);
		}
		// end BC

		// Making sure that everything is loaded.		
		$classes = array('engine', 'dataProvider', 'compiler');
		
		// Setting options to Dwoo objects...
		foreach ($opt as $type => $settings) {			
			if (!method_exists($this, 'set' . $type)) {
				throw new Dwoo_Exception("Unknown type $type");
			}

			if (is_string($settings) || is_object($settings)) {
				call_user_func(array($this, 'set' . $type), $settings);
			} elseif (is_array($settings)) {
				// Set requested class
				if (array_key_exists('type', $settings)) {
					call_user_func(array($this, 'set' . $type), $settings['type']);
				}
				
				if (in_array($type, $classes)) {
					// Call get so that the class is initialized
					$rel = call_user_func(array($this, 'get' . $type));
	
					// Call set*() methods so that all the settings are set.
					foreach ($settings as $method => $value) {
						if (method_exists($rel, 'set' . $method)) {
							call_user_func(array($rel, 'set' . $method), $value);
						}
					}
				} elseif ('templateFile' == $type) {
					// Remember the settings for the templateFile
					$this->_templateFileSettings = $settings;
				}
			}
		}
	}

  /**
   * Set object state from Zend_Config object
   *
   * @param  Zend_Config $config
   * @return Dwoo_Adapters_ZendFramework_View
   */
	public function setConfig(Zend_Config $config)
	{
		return $this->setOptions($config->toArray());
	}

	/**
	 * Called before template rendering
	 *
	 * Binds plugin proxy to the Dwoo.
	 *
	 * @see Dwoo_Adapters_ZendFramework_View::getPluginProxy();
	 * @see Dwoo::setPluginProxy();
	 */
	protected function preRender()
 	{
		$this->getEngine()->setPluginProxy($this->getPluginProxy());
	}

	/**
	 * Wraper for Dwoo_Data::__set()
	 * allows to assign variables using the object syntax
	 *
	 * @see Dwoo_Data::__set()
	 * @param string $name the variable name
	 * @param string $value the value to assign to it
	 */
 	public function __set($name, $value)
 	{
 		$this->getDataProvider()->__set($name, $value);
 	}

 	/**
	 * Sraper for Dwoo_Data::__get() allows to read variables using the object
	 * syntax
	 *
	 * @see Dwoo_Data::__get()
	 * @param string $name the variable name
	 * @return mixed
	 */
 	public function __get($name)
 	{
		 return $this->getDataProvider()->__get($name);
 	}

 	/**
	 * Wraper for Dwoo_Data::__isset()
	 * supports calls to isset($dwooData->var)
	 *
	 * @see Dwoo_Data::__isset()
	 * @param string $name the variable name
	 */
 	public function __isset($name)
 	{
 		return $this->getDataProvider()->__isset($name);
 	}

	/**
	 * Wraper for Dwoo_Data::_unset()
	 * supports unsetting variables using the object syntax
	 *
	 * @see Dwoo_Data::__unset()
	 * @param string $name the variable name
	 */
	public function __unset($name)
	{
		$this->getDataProvider()->__unset($name);
	}
	
	/**
	 * Catches clone request and clones data provider
	 */
	public function __clone() {
		$this->setDataProvider(clone $this->getDataProvider());
	}

	/**
	 * Returns plugin proxy interface
	 *
	 * @return Dwoo_IPluginProxy
	 */
	public function getPluginProxy()
	{
		if (!$this->_pluginProxy) {
			$this->_pluginProxy = new Dwoo_Adapters_ZendFramework_PluginProxy($this);
		}

		return $this->_pluginProxy;
	}

	/**
	 * Sets plugin proxy
	 *
	 * @param Dwoo_IPluginProxy
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setPluginProxy(Dwoo_IPluginProxy $pluginProxy)
	{
		$this->_pluginProxy = $pluginProxy;
		return $this;
	}

	/**
	 * Sets template engine
	 *
	 * @param string|Dwoo Object or name of the class
	 */
	public function setEngine($engine)
	{
		// if param given as an object
		if ($engine instanceof Dwoo) {
			$this->_engine = $engine;
		}
		//
		elseif (is_subclass_of($engine, 'Dwoo') || 'Dwoo' === $engine) {
			$this->_engine = new $engine();
		}
		else {
			throw new Dwoo_Exception("Custom engine must be a subclass of Dwoo");
		}
	}

	/**
	 * Return the Dwoo template engine object
	 *
	 * @return Dwoo
	 */
	public function getEngine()
	{
		if (null === $this->_engine) {
			$this->_engine = new Dwoo();
		}

		return $this->_engine;
	}

	/**
	 * Sets Dwoo data object
	 *
	 * @param string|Dwoo_Data Object or name of the class
	 */
	public function setDataProvider($data)
	{
		if ($data instanceof Dwoo_IDataProvider) {
			$this->_dataProvider = $data;
		}
		elseif (is_subclass_of($data, 'Dwoo_Data') || 'Dwoo_Data' == $data) {
			$this->_dataProvider = new $data();
		}
		else {
			throw new Dwoo_Exception("Custom data provider must be a subclass of Dwoo_Data or instance of Dwoo_IDataProvider");
		}
	}

	/**
	 * Return the Dwoo data object
	 *
	 * @return Dwoo_Data
	 */
	public function getDataProvider()
	{
		if (null === $this->_dataProvider) {
			$this->_dataProvider = new Dwoo_Data;
		}

		return $this->_dataProvider;
	}


	/**
	 * Sets Dwoo compiler
	 *
	 * @param string|Dwoo_Compiler Object or name of the class
	 */
	public function setCompiler($compiler)
	{

		// if param given as an object
		if ($compiler instanceof Dwoo_ICompiler) {
			$this->_compiler = $compiler;
		}
		// if param given as a string
		elseif (is_subclass_of($compiler, 'Dwoo_Compiler') || 'Dwoo_Compiler' == $compiler) {
			$this->_compiler = new $compiler;
		}
		else {
			throw new Dwoo_Exception("Custom compiler must be a subclass of Dwoo_Compiler or instance of Dwoo_ICompiler");
		}
	}

	/**
	 * Return the Dwoo compiler object
	 *
	 * @return Dwoo_Data
	 */
	public function getCompiler()
	{	
		if (null === $this->_compiler) {	
			$this->_compiler = Dwoo_Compiler::compilerFactory();
		}

		return $this->_compiler;
	}
	
	/**
	 * Initializes Dwoo_ITemplate type of class and sets properties from _templateFileSettings
	 * 
	 * @param  string Template location
	 * @return Dwoo_ITemplate
	 */
	public function getTemplateFile($template) {
		$templateFileClass = $this->_templateFileClass;		

		$dwooTemplateFile = new $templateFileClass($template);
		
		if (!($dwooTemplateFile instanceof Dwoo_ITemplate)) {
			throw new Dwoo_Exception("Custom templateFile class must be a subclass of Dwoo_ITemplate");
		}

		foreach ($this->_templateFileSettings as $method => $value) {
			if (method_exists($dwooTemplateFile, 'set' . $method)) {
				call_user_func(array($dwooTemplateFile, 'set' . $method), $value);
			}
		}
		
		return $dwooTemplateFile;
	}

	/**
	 * Dwoo_ITemplate type of class
	 *   
	 * @param string Name of the class
	 * @return void
	 */
	public function setTemplateFile($tempateFileClass) {
		$this->_templateFileClass = $tempateFileClass;
	}

	/**
	 * Passes data to Dwoo_Data object
	 *
	 * @see Dwoo_Data::assign()
	 * @param array|string $name
	 * @param mixed $val
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function assign($name, $val = null)
	{
		$this->getDataProvider()->assign($name, $val);
		return $this;
	}
	
	/**
	 * Return list of all assigned variables
	 *
	 * @return array	
	 */	
	public function getVars()
	{
		return $this->_dataProvider->getData();
	}

	/**
	 * Clear all assigned variables
	 *
	 * Clears all variables assigned to Zend_View either via {@link assign()} or
	 * property overloading ({@link __get()}/{@link __set()}).
	 *
	 * @return void
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function clearVars()
	{
		$this->getDataProvider()->clear();
		return $this;
	}

	/**
	 * Wraper for parent's render method so preRender method
	 * can be called (that will bind the plugin proxy to the
	 * engine.
	 *
	 * @see Zend_View_Abstract::render
	 * @return string The script output.
	 */
	public function render($name)
	{
		$this->preRender();
		return parent::render($name);
	}

	/**
	 * Processes a view script and outputs it. Output is then
	 * passed through filters.
	 *
	 * @param string $name The script script name to process.
	 * @return string The script output.
	 */
	public function _run()
	{
		echo $this->_engine->get(
			$this->getTemplateFile(func_get_arg(0)),
			$this->getDataProvider(),
			$this->getCompiler()
		);
	}

	/**
	 * Add plugin path
	 *
	 * @param string $dir Directory
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function addPluginDir($dir)
	{
		$this->getEngine()->getLoader()->addDirectory($dir);
		return $this;
	}

	/**
	 * Set compile path
	 *
	 * @param string $dir Directory
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setCompileDir($dir)
	{
		$this->getEngine()->setCompileDir($dir);
		return $this;
	}

	/**
	 * Set cache path
	 *
	 * @param string $dir Directory
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setCacheDir($dir)
	{
		$this->getEngine()->setCacheDir($dir);
		return $this;
	}

	/**
	 * Set cache lifetime
	 *
	 * @param string $seconds Lifetime in seconds
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setCacheLifetime($seconds)
	{
		$this->getEngine()->setCacheTime($seconds);
		return $this;
	}

	/**
	 * Set charset
	 *
	 * @param string $charset
	 * @return Dwoo_Adapters_ZendFramework_View
	 */
	public function setCharset($charset)
	{
		$this->_engine->setCharset($charset);
		return $this;
	}
}