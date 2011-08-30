<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2006 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * CodeIgniter Driver Library Class
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver_Library extends CI_CoreShare {
	protected $valid_drivers	= array();
	protected $lib_name;

	/**
	 * Driver loader
	 *
	 * Loads a validated driver object and attaches it to the library
	 *
	 * @param	string	driver name
	 * @return	object	driver object
	 */
	public function load_driver($driver) {
		// Set parent library name first time
		if ( ! isset($this->lib_name)) {
			$this->lib_name = get_class($this);
		}

		// Get root instance
		$CI =& get_instance();

		// The class will be prefixed with the parent lib
		$driver_class = $this->lib_name.'_'.$driver;

		// Remove the CI_ prefix and lowercase
		$lib_name = ucfirst(strtolower(str_replace('CI_', '', $this->lib_name)));
		$driver_name = strtolower(str_replace('CI_', '', $driver_class));

		// Determine if driver is allowed
		if (!in_array($driver_name, array_map('strtolower', $this->valid_drivers))) {
			// The requested driver isn't valid!
			$msg = 'Invalid driver requested: '.$driver_class;
			throw new CI_ShowError($msg, '', 0, $msg);
		}

		// Check if driver is already defined
		if (!class_exists($driver_class)) {
			// Load driver as a library, but don't attach to CodeIgniter
			$this->_call_core($CI, '_load', 'library', $driver_name, '', NULL, $lib_name.'/drivers/');

			// See if the driver class was found
			if (!class_exists($driver_class)) {
				$msg = 'Unable to load the requested driver: '.$driver_class;
				throw new CI_ShowError($msg, '', 0, $msg);
			}
		}

		// Instantiate, attach, and return driver object
		$this->$driver = new $driver_class;
		$this->$driver->decorate($this);
		return $this->$driver;
	}

	/**
	 * Get magic method
	 *
	 * Loads a driver object when not found attached to the library
	 *
	 * @param	string	driver name
	 * @return void
	 */
	public function __get($driver) {
		// The first time a child is used it won't exist, so we instantiate it
		// subsequents calls will go straight to the proper child.
		$this->load_driver($driver);
	}
}
// END CI_Driver_Library CLASS

/**
 * CodeIgniter Driver Class
 *
 * This class enables you to create drivers for a Library based on the Driver Library.
 * It handles the drivers' access to the parent library
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver {
	protected $parent;

	private $methods = array();
	private $properties = array();

	private static $reflections = array();

	/**
	 * Decorate
	 *
	 * Decorates the child with the parent driver lib's methods and properties
	 *
	 * @param	object
	 * @return	void
	 */
	public function decorate($parent) {
		$this->parent = $parent;

		// Lock down attributes to what is defined in the class
		// and speed up references in magic methods
		$class_name = get_class($parent);

		if ( ! isset(self::$reflections[$class_name])) {
			$r = new ReflectionObject($parent);

			foreach ($r->getMethods() as $method) {
				if ($method->isPublic()) {
					$this->methods[] = $method->getName();
				}
			}

			foreach ($r->getProperties() as $prop) {
				if ($prop->isPublic()) {
					$this->properties[] = $prop->getName();
				}
			}

			self::$reflections[$class_name] = array($this->methods, $this->properties);
		}
		else {
			list($this->methods, $this->properties) = self::$reflections[$class_name];
		}
	}

	/**
	 * __call magic method
	 *
	 * Handles access to the parent driver library's methods
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($method, $args = array()) {
		if (in_array($method, $this->methods)) {
			return call_user_func_array(array($this->parent, $method), $args);
		}

		$trace = debug_backtrace();
		$CI =& get_instance();
		$CI->_exception_handler(E_ERROR, 'No such method \''.$method.'\'', $trace[1]['file'], $trace[1]['line']);
		exit;
	}

	/**
	 * __get magic method
	 *
	 * Handles reading of the parent driver library's properties
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function __get($var) {
		if (in_array($var, $this->properties)) {
			return $this->parent->$var;
		}
	}

	/**
	 * __set magic method
	 *
	 * Handles writing to the parent driver library's properties
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __set($var, $val) {
		if (in_array($var, $this->properties)) {
			$this->parent->$var = $val;
		}
	}
}
// END CI_Driver CLASS

/* End of file Driver.php */
/* Location: ./system/libraries/Driver.php */
