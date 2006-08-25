<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Loader Class
 * 
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		Rick Ellis
 * @category	Loader
 * @link		http://www.codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader {

	var $ob_level;
	var $cached_vars	= array();
	var $helpers		= array();
	var $plugins		= array();
	var $scripts		= array();
	var $languages		= array();
	var $view_path		= '';

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output
	 * buffering level
	 *
	 * @access	public
	 */
	function CI_Loader()
	{
		$this->view_path = APPPATH.'views/';
		$this->ob_level = ob_get_level();
				
		log_message('debug', "Loader Class Initialized");
	}
	// END CI_Loader()
	
	// --------------------------------------------------------------------
	
	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	any initialization parameters
	 * @return	void
	 */	
	function library($class, $param = FALSE)
	{		
		if ($class == '')
			return;
	
		$obj =& get_instance();
		$obj->_ci_initialize($class, $param);
		$obj->_ci_assign_to_models();
	}
	// END library()

	// --------------------------------------------------------------------
	
	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	any initialization parameters
	 * @return	void
	 */	
	function model($model, $name = '', $db_conn = FALSE)
	{		
		if ($model == '')
			return;
	
		$obj =& get_instance();
		$obj->_ci_load_model($model, $name, $db_conn);
	}
	// END library()
	
	// --------------------------------------------------------------------
	
	/**
	 * Database Loader
	 *
	 * @access	public
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	mixed
	 */	
	function database($db = '', $return = FALSE, $active_record = FALSE)
	{
		$obj =& get_instance();
	
		if ($return === TRUE)
		{
			return $obj->_ci_init_database($db, TRUE, $active_record);
		}
		else
		{
			$obj->_ci_init_database($db, FALSE, $active_record);
			$obj->_ci_assign_to_models();			
		}
	}
	// END database()
	
	// --------------------------------------------------------------------
	
	/**
	 * Scaffolding Loader
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function scaffolding($table = '')
	{		
		if ($table == FALSE)
		{
			show_error('You must include the name of the table you would like access when you initialize scaffolding');
		}
		
		$obj =& get_instance();
		$obj->_ci_init_scaffolding($table);
	}
	// END scaffolding()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load View
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to retun data so that
	 * a developer can process it in some way.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function view($view, $vars = array(), $return = FALSE)
	{
		return $this->_ci_load(array('view' => $view, 'vars' => $this->_ci_object_to_array($vars), 'return' => $return));
	}
	// END view()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load File
	 *
	 * This is a generic file loader
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function file($path, $return = FALSE)
	{
		return $this->_ci_load(array('path' => $path, 'return' => $return));
	}
	// END file()
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Variables
	 *
	 * Once variables are set they become availabe within
	 * the controller class and its "view" files.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function vars($vars = array())
	{
		$vars = $this->_ci_object_to_array($vars);
	
		if (is_array($vars) AND count($vars) > 0)
		{
			foreach ($vars as $key => $val)
			{
				$this->cached_vars[$key] = $val;
			}
		}
	}
	// END vars()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 *
	 * @access	public
	 * @param	mixed
	 * @return	void
	 */
	function helper($helpers = array())
	{
		if ( ! is_array($helpers))
		{
			$helpers = array($helpers);
		}
	
		foreach ($helpers as $helper)
		{
			if (isset($this->helpers[$helper]))
			{
				continue;
			}
		
			$helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');
		
			if ( ! file_exists(BASEPATH.'helpers/'.$helper.EXT))
			{
				show_error('Unable to load the requested file: helpers/'.$helper.EXT);
			}
			
			include_once(BASEPATH.'helpers/'.$helper.EXT);

			$this->helpers[$helper] = TRUE;
		}
		
		log_message('debug', 'Helpers loaded: '.implode(', ', $helpers));
	}
	// END helper()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helpers
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function helpers($helpers = array())
	{
		$this->helper($helpers);
	}
	// END helpers()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Plugin
	 *
	 * This function loads the specified plugin.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function plugin($plugins = array())
	{
		if ( ! is_array($plugins))
		{
			$plugins = array($plugins);
		}
	
		foreach ($plugins as $plugin)
		{
			if (isset($this->plugins[$plugin]))
			{
				continue;
			}
	
			$plugin = strtolower(str_replace(EXT, '', str_replace('_plugin.', '', $plugin)).'_pi');
		
			if ( ! file_exists(BASEPATH.'plugins/'.$plugin.EXT))
			{
				show_error('Unable to load the requested file: plugins/'.$plugin.EXT);
			}
			
			include_once(BASEPATH.'plugins/'.$plugin.EXT);	
			
			$this->plugins[$plugin] = TRUE;
		}
		
		log_message('debug', 'Plugins loaded: '.implode(', ', $plugins));
	}
	// END plugin()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Script
	 *
	 * This function loads the specified include file from the
	 * application/scripts/ folder
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function script($scripts = array())
	{
		if ( ! is_array($scripts))
		{
			$scripts = array($scripts);
		}
	
		foreach ($scripts as $script)
		{
			if (isset($this->scripts[$script]))
			{
				continue;
			}
	
			$script = strtolower(str_replace(EXT, '', $script));
		
			if ( ! file_exists(APPPATH.'scripts/'.$script.EXT))
			{
				show_error('Unable to load the requested script: scripts/'.$script.EXT);
			}
			
			include_once(APPPATH.'scripts/'.$script.EXT);
			
			$this->scripts[$script] = TRUE;
		}
		
		log_message('debug', 'Scripts loaded: '.implode(', ', $scripts));
	}
	// END script()
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Plugins
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function plugins($plugins = array())
	{
		$this->plugin($plugins);
	}
	// END plugins()
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads a language file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function language($file = '', $lang = '', $return = FALSE)
	{
		$obj =& get_instance();
		return $obj->lang->load($file, $lang, $return);
	}
	// END language()
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads a config file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function config($file = '')
	{
		$obj =& get_instance();
		$obj->config->load($file);
	}
	// END config()
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the Path to the "views" folder
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */
	function _ci_set_view_path($path)
	{
		$this->view_path = $path;
	}
	// END _ci_set_view_path()
	
	// --------------------------------------------------------------------
	
	/**
	 * Loader
	 *
	 * This function isn't called directly.  It's called from
	 * the two functions above.  It's used to load views and files
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_load($data)
	{	
		$OUT =& _load_class('CI_Output');

		// This allows anything loaded using $this->load (viwes, files, etc.)
		// to become accessible from within the Controller and Model functions.
		$obj =& get_instance();
		foreach ($obj->ci_is_loaded as $val)
		{
			if ( ! isset($this->$val))
			{
				$this->$val =& $obj->$val;
			}	
		}		
		
		// Set the default data variables
		foreach (array('view', 'vars', 'path', 'return') as $val)
		{
			$$val = ( ! isset($data[$val])) ? FALSE : $data[$val];
		}
		
		/*
		 * Extract and cached variables
		 *
		 * You can either set variables using the dedicated
		 * $this->load_vars() function or via the second
		 * parameter of this function. We'll
		 * merge the two types and cache them so that
		 * views that are embedded within other views
		 * can have access to these variables.
		 *
		 */	
		
		if (is_array($vars))
		{
			$this->cached_vars = array_merge($this->cached_vars, $vars);
		}
		extract($this->cached_vars);
				
		// Set the path to the requested file
		if ($path == '')
		{
			$ext = pathinfo($view, PATHINFO_EXTENSION);
			$file = ($ext == '') ? $view.EXT : $view;
			$path = $this->view_path.$file;
		}
		else
		{
			$x = explode('/', $path);
			$file = end($x);
		}

		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be 
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to 
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer, it won't be acurate.
		 *
		 */

		if ( ! file_exists($path))
		{
			show_error('Unable to load the requested file: '.$file);
		}

		ob_start();
		
		include($path);
		log_message('debug', 'File loaded: '.$path);
		
		// Return the file data if requested to
		if ($return === TRUE)
		{		
			$buffer = ob_get_contents();
			ob_end_clean(); 
			
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit templates (views) to be nested within
		 * other views, we need to flush the content back out whenever 
		 * we are beyond the first level of output buffering so that 
		 * it can be seen and included  properly by the first included 
		 * template and any subsequent ones. Oy!
		 *
		 */		
		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			$OUT->set_output(ob_get_contents());
			ob_end_clean();
		}
	}
	// END _load()
	
	// --------------------------------------------------------------------
	
	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems, 
	 * plugins, and helpers to be loaded automatically.
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_autoloader($autoload)
	{
		if ($autoload === FALSE)
		{
			return;
		}
	
		foreach (array('helper', 'plugin', 'script') as $type)
		{
			if (isset($autoload[$type]))
			{
				if ( ! is_array($autoload[$type]))
				{
					$autoload[$type] = array($autoload[$type]);
				}
			
				foreach ($autoload[$type] as $item)
				{
					$this->$type($item);
				}
			}
		}
	}
	// END _ci_autoloader()

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and convers the class variables to array key/vals
	 *
	 * @access	public
	 * @param	object
	 * @return	array
	 */
	function _ci_object_to_array($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}
		
		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			$array[$key] = $val;
		}
	
		return $array;
	}
	
}
// END Loader Class
?>