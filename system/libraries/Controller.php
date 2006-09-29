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
 * Code Igniter Application Controller Class
 *
 * This class object is the the super class the every library in 
 * Code Igniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends CI_Base {

	var $_ci_models			= array();
	var $_ci_scaffolding	= FALSE;
	var $_ci_scaff_table	= FALSE;
	
	/**
	 * Constructor
	 *
	 * Loads the base classes needed to run CI, and runs the "autoload"
	 * routine which loads the systems specified in the "autoload" config file.
	 */
	function Controller()
	{	
		parent::CI_Base();
		
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be 
		// run as one big super object.
		$this->_ci_assign_core();
		
		// Load everything specified in the autoload.php file
		$this->load->_ci_autoloader($this->_ci_autoload());

		// This allows anything loaded using $this->load (viwes, files, etc.)
		// to become accessible from within the Controller class functions.
		foreach (get_object_vars($this) as $key => $var)
		{
			if (is_object($var))
			{
				$this->load->$key =& $this->$key;
			}
		}
		
		log_message('debug', "Controller Class Initialized");
	}
   	
	// --------------------------------------------------------------------

	/**
	 * Initialization Handler
	 *
	 * This function loads the requested class.
	 *
	 * @access	private
	 * @param 	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @return 	void
	 */
	function _ci_load_class($class, $params = NULL)
	{	
		// Prep the class name
		$class = strtolower(str_replace(EXT, '', $class));

		// Is this a class extension request?	
		if (substr($class, 0, 3) == 'my_')
		{
			$class = preg_replace("/my_(.+)/", "\\1", $class);
			$extend = TRUE;
		}
		else
		{
			$extend = FALSE;
		}
		
		// Does THIS file (Controller.php) contain an initialization
		// function that maps to the requested class?
		
		$method = '_ci_init_'.$class;
		
		if (method_exists($this, $method))
		{		
			if (is_null($params))
			{
				$this->$method();
			}
			else
			{
				$this->$method($params);
			}		
		
			// We're done...
			return TRUE;
		}
		
		// Are we extending one of the base classes?
		if ($extend == TRUE)
		{
			// Load the requested library from the main system/libraries folder
			if (file_exists(BASEPATH.'libraries/'.ucfirst($class).EXT))
			{
				include_once(BASEPATH.'libraries/'.ucfirst($class).EXT);
			}
			
			// Now look for a matching library
			foreach (array(ucfirst($class), $class) as $filename)
			{
				if (file_exists(APPPATH.'libraries/'.$filename.EXT))
				{
					include_once(APPPATH.'libraries/'.$filename.EXT);	
				}
			}
			
			return $this->_ci_init_class($filename, 'MY_', $params);
		}
		else
		{		
			// Lets search for the requested library file and load it.
			// For backward compatibility we'll test for filenames that are
			// both uppercase and lower.
			foreach (array(ucfirst($class), $class) as $filename)
			{
				for ($i = 1; $i < 3; $i++)
				{
					$path = ($i % 2) ? APPPATH : BASEPATH;
				
					if (file_exists($path.'libraries/'.$filename.EXT))
					{
						include_once($path.'libraries/'.$filename.EXT);
						return $this->_ci_init_class($filename, '', $params);
					}
				}
			}
		}
		
		// If we got this far we were unable to find the requested class
		log_message('error', "Unable to load the requested class: ".$class);
		show_error("Unable to load the class: ".$class);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	null
	 */
	function _ci_init_class($class, $prefix = '', $config = NULL)
	{
		// Is there an associated config file for this class?
		
		if ($config == NULL)
		{
			if (file_exists(APPPATH.'config/'.$class.EXT))
			{
				include_once(APPPATH.'config/'.$class.EXT);
			}
		}
		
		if ($prefix == '')
		{
			$name = ( ! class_exists($class)) ? 'CI_'.$class : $class;
		}
		else
		{
			$name = $prefix.ucfirst($class);
		}
		
		$remap = array(
						'Unit_test' 	=> 'unit'
						);
						
		$varname = ( ! isset($remap[$class])) ? $class : $remap[$class];
		
		// Instantiate the class
		if ($config !== NULL)
		{
			$this->$varname = new $name($config);
		}
		else
		{
			$this->$varname = new $name;
		}	
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Loads and instantiates the requested model class
	 *
	 * @access	private
	 * @param	string
	 * @return	array
	 */
	function _ci_init_model($model, $name = '', $db_conn = FALSE)
	{
		// Is the model in a sub-folder?
		// If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE)
		{
			$path = '';
		}
		else
		{
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}
	
		if ($name == '')
		{
			$name = $model;
		}
		
		$obj =& get_instance();
		if (in_array($name, $obj->_ci_models, TRUE))
		{
			return;
		}		
		
		if (isset($this->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
	
		$model = strtolower($model);
		
		if ( ! file_exists(APPPATH.'models/'.$path.$model.EXT))
		{
			show_error('Unable to locate the model you have specified: '.$model);
		}
				
		if ($db_conn !== FALSE)
		{
			if ($db_conn === TRUE)
				$db_conn = '';
		
			$this->_ci_init_database($db_conn, FALSE, TRUE);
		}
	
		if ( ! class_exists('Model'))
		{
			require_once(BASEPATH.'libraries/Model'.EXT);
		}

		require_once(APPPATH.'models/'.$path.$model.EXT);

		$model = ucfirst($model);
		$this->$name = new $model();
		$this->_ci_models[] = $name;
		$this->_ci_assign_to_models();
	}  	

	// --------------------------------------------------------------------

	/**
	 * Assign to Models
	 *
	 * Makes sure that anything loaded by the loader class (libraries, plugins, etc.)
	 * will be available to modles, if any exist.
	 *
	 * @access	public
	 * @param	object
	 * @return	array
	 */
	function _ci_assign_to_models()
	{
		$obj =& get_instance();
		if (count($obj->_ci_models) == 0)
		{
			return;
		}
		foreach ($obj->_ci_models as $model)
		{			
			$obj->$model->_assign_libraries();			
		}		
	}  	
  	
	// --------------------------------------------------------------------

	/**
	 * Auto-initialize Core Classes
	 *
	 * This initializes the core systems that are specified in the 
	 * libraries/autoload.php file, as well as the systems specified in
	 * the $autoload class array above.
	 *
	 * It returns the "autoload" array so we can pass it to the Loader 
	 * class since it needs to autoload plugins and helper files
	 *
	 * The config/autoload.php file contains an array that permits 
	 * sub-systems to be loaded automatically.
	 *
	 * @access	private
	 * @return	array
	 */
	function _ci_autoload()
	{
		include_once(APPPATH.'config/autoload'.EXT);
		
		if ( ! isset($autoload))
		{
			return FALSE;
		}
		
		if (count($autoload['config']) > 0)
		{
			foreach ($autoload['config'] as $key => $val)
			{
				$this->config->load($val);
			}
		}
		unset($autoload['config']);
		
		// A little tweak to remain backward compatible
		// The $autoload['core'] item was deprecated
		if ( ! isset($autoload['libraries']))
		{
			$autoload['libraries'] = $autoload['core'];
		
		}
		
		$exceptions = array('dbutil', 'dbexport');
		
		foreach ($autoload['libraries'] as $item)
		{
			if ( ! in_array($item, $exceptions, TRUE))
			{
				$this->_ci_load_class($item);
			}
			else
			{
				$this->_ci_init_dbextra($item);
			}
		}
		unset($autoload['libraries']);

		return $autoload;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Assign the core classes to the global $CI object
	 *
	 * By assigning all the classes instantiated by the front controller
	 * local class variables we enable everything to be accessible using
	 * $this->class->function()
	 *
	 * @access	private
	 * @return	void
	 */
	function _ci_assign_core()
	{
		foreach (array('Config', 'Input', 'Benchmark', 'URI', 'Output') as $val)
		{
			$class = strtolower($val);
			$this->$class =& _load_class($val);
		}
		
		$this->lang	=& _load_class('Language');
	
		// In PHP 4 the Controller class is a child of CI_Loader.
		// In PHP 5 we run it as its own class.
		if (floor(phpversion()) >= 5)
		{
			$this->load = new CI_Loader();
		}
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Initialize Scaffolding
	 *
	 * This initializing function works a bit different than the
	 * others. It doesn't load the class.  Instead, it simply
	 * sets a flag indicating that scaffolding is allowed to be
	 * used.  The actual scaffolding function below is
	 * called by the front controller based on whether the
	 * second segment of the URL matches the "secret" scaffolding
	 * word stored in the application/config/routes.php
	 *
	 * @access	private
	 * @param	string	the table to scaffold
	 * @return	void
	 */
	function _ci_init_scaffolding($table = FALSE)
	{
		if ($table === FALSE)
		{
			show_error('You must include the name of the table you would like access when you initialize scaffolding');
		}
		
		$this->_ci_scaffolding = TRUE;
		$this->_ci_scaff_table = $table;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Initialize Database
	 *
	 * @access	private
	 * @param	mixed	database connection values
	 * @param	bool	whether to return the object for multiple connections
	 * @return	void
	 */
	function _ci_init_database($params = '', $return = FALSE, $active_record = FALSE)
	{	
		if ($this->_ci_is_loaded('db') == TRUE AND $return == FALSE AND $active_record == FALSE)
		{
			return;
		}
	
		// Load the DB config file if needed
		if (is_string($params) AND strpos($params, '://') === FALSE)
		{
			include(APPPATH.'config/database'.EXT);
			
			$group = ($params == '') ? $active_group : $params;
			
			if ( ! isset($db[$group]))
			{
				show_error('You have specified an invalid database connection group: '.$group);
			}
			
			$params = $db[$group];
		}
		
		// No DB specified yet?  Beat them senseless...
		if ( ! isset($params['dbdriver']) OR $params['dbdriver'] == '')
		{
			show_error('You have not selected a database type to connect to.');
		}

		// Load the DB classes.  Note: Since the active record class is optional
		// we need to dynamically create a class that extends proper parent class 
		// based on whether we're using the active record class or not.
		// Kudos to Paul for discovering this clever use of eval()
		
		if ($active_record == TRUE)
		{
			$params['active_r'] = TRUE;
		}
		
		require_once(BASEPATH.'database/DB_driver'.EXT);

		if ( ! isset($params['active_r']) OR $params['active_r'] == TRUE) 
		{
			require_once(BASEPATH.'database/DB_active_rec'.EXT);
			
			if ( ! class_exists('CI_DB'))
			{
				eval('class CI_DB extends CI_DB_active_record { }');
			}
		}
		else
		{
			if ( ! class_exists('CI_DB'))
			{
				eval('class CI_DB extends CI_DB_driver { }');
			}
		}
				
		require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver'.EXT);

		// Instantiate the DB adapter
		$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
		$DB = new $driver($params);
		
		if ($return === TRUE)
		{
			return $DB;
		}
		
		$obj =& get_instance();
		$obj->db =& $DB;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Database Ancillary Classes
	 *
	 * @access	private
	 * @param	str		class name
	 * @return	void
	 */
	function _ci_init_dbextra($class)
	{
		if ( ! $this->_ci_is_loaded('db'))
		{
			$this->_ci_init_database();
		}
			
		if ($class == 'dbutil')
		{
			require_once(BASEPATH.'database/DB_utility'.EXT);
			require_once(BASEPATH.'database/drivers/'.$this->db->dbdriver.'/'.$this->db->dbdriver.'_utility'.EXT);
			$class = 'CI_DB_'.$this->db->dbdriver.'_utility';
			$this->dbutil = new $class();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns TRUE if a class is loaded, FALSE if not
	 *
	 * @access	public
	 * @param	string	 the class name
	 * @return	bool
	 */
	function _ci_is_loaded($class)
	{
		return ( ! isset($this->$class) OR ! is_object($this->$class)) ? FALSE : TRUE;
	}
  	  	
	// --------------------------------------------------------------------

	/**
	 * Scaffolding
	 *
	 * Initializes the scaffolding.
	 *
	 * @access	private
	 * @return	void
	 */
	function _ci_scaffolding()
	{
		if ($this->_ci_scaffolding === FALSE OR $this->_ci_scaff_table === FALSE)
		{
			show_404('Scaffolding unavailable');
		}
		
		if (class_exists('Scaffolding')) return;
			
		if ( ! in_array($this->uri->segment(3), array('add', 'insert', 'edit', 'update', 'view', 'delete', 'do_delete'), TRUE))
		{
			$method = 'view';
		}
		else
		{
			$method = $this->uri->segment(3);
		}
		
		$this->_ci_init_database("", FALSE, TRUE);
		$this->_ci_load_class('pagination');
		require_once(BASEPATH.'scaffolding/Scaffolding'.EXT);
		$this->scaff = new Scaffolding($this->_ci_scaff_table);
		$this->scaff->$method();
	}

}
// END _Controller class
?>