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

	var $ci_is_loaded		= array();
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
		foreach ($this->ci_is_loaded as $val)
		{
			$this->load->$val =& $this->$val;
		}
			
		log_message('debug', "Controller Class Initialized");
	}
  	// END Controller()
  	
	// --------------------------------------------------------------------

	/**
	 * Initialization Handler
	 *
	 * Looks for the existence of a handler method and calls it
	 *
	 * @access	private
	 * @param 	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @return 	void
	 */
	function _ci_initialize($what, $params = FALSE)
	{		
		$method = '_ci_init_'.strtolower(str_replace(EXT, '', $what));

		if ( ! method_exists($this, $method))
		{
			$method = substr($method, 4);
		
			if ( ! file_exists(APPPATH.'init/'.$method.EXT))
			{
				if ( ! file_exists(BASEPATH.'init/'.$method.EXT))
				{
					log_message('error', "Unable to load the requested class: ".$what);
					show_error("Unable to load the class: ".$what);
				}
				
				include(BASEPATH.'init/'.$method.EXT);
			}
			else
			{
				include(APPPATH.'init/'.$method.EXT);
			}
		}
		else
		{
			if ($params === FALSE)
			{
				$this->$method();
			}
			else
			{
				$this->$method($params);
			}
		}
	}
  	// END _ci_initialize()
  	
	// --------------------------------------------------------------------

	/**
	 * Loads and instantiates the requested model class
	 *
	 * @access	private
	 * @param	string
	 * @return	array
	 */
	function _ci_load_model($model, $name = '', $db_conn = FALSE)
	{
		if ($name == '')
		{
			$name = $model;
		}
		
		$obj =& get_instance();
		if (in_array($name, $obj->_ci_models))
		{
			return;
		}		
		
		if (isset($this->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
	
		$model = strtolower($model);
		
		if ( ! file_exists(APPPATH.'models/'.$model.EXT))
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

		require_once(APPPATH.'models/'.$model.EXT);

		$model = ucfirst($model);
		$this->$name = new $model();
		$this->_ci_models[] = $name;
		$this->_ci_assign_to_models();
	}
	// END _ci_load_model()
  	

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
	// END _ci_assign_to_models()
  	
  	
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
		
		if ( ! is_array($autoload['core']))
		{
			$autoload['core'] = array($autoload['core']);
		}
		
		foreach ($autoload['core'] as $item)
		{
			$this->_ci_initialize($item);
		}
		
		return $autoload;
	}
  	// END _ci_autoload()
  	
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
			$this->$class =& _load_class('CI_'.$val);
			$this->ci_is_loaded[] = $class;
		}
		
		$this->lang	=& _load_class('CI_Language');
		$this->ci_is_loaded[] = 'lang';
	
		// In PHP 4 the Controller class is a child of CI_Loader.
		// In PHP 5 we run it as its own class.
		if (floor(phpversion()) >= 5)
		{
			$this->load = new CI_Loader();
		}
		
		$this->ci_is_loaded[] = 'load';
	}
  	// END _ci_assign_core()
  	
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
  	// END _ci_init_scaffolding()
  	
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
		
		require_once(BASEPATH.'drivers/DB_driver'.EXT);

		if ( ! isset($params['active_r']) OR $params['active_r'] == TRUE) 
		{
			require_once(BASEPATH.'drivers/DB_active_record'.EXT);
			
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
		
		require_once(BASEPATH.'drivers/DB_'.$params['dbdriver'].EXT);

		// Instantiate the DB adapter
		$driver = 'CI_DB_'. $params['dbdriver'];
		$DB = new $driver($params);
		
		if ($return === TRUE)
		{
			return $DB;
		}
		
		$obj =& get_instance();
		$obj->ci_is_loaded[] = 'db';
		$obj->db =& $DB;
	}
  	// END _ci_init_database()
  	
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
		return ( ! in_array($class, $this->ci_is_loaded)) ? FALSE : TRUE;
	}
  	// END _ci_is_loaded()
  	
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
			
		if ( ! in_array($this->uri->segment(3), array('add', 'insert', 'edit', 'update', 'view', 'delete', 'do_delete')))
		{
			$method = 'view';
		}
		else
		{
			$method = $this->uri->segment(3);
		}
		
		$this->_ci_init_database("", FALSE, TRUE);
		
		$this->_ci_initialize('pagination');
		require_once(BASEPATH.'scaffolding/Scaffolding'.EXT);
		$this->scaff = new Scaffolding($this->_ci_scaff_table);
		$this->scaff->$method();
	}
	// END _ci_scaffolding()

}
// END _Controller class
?>