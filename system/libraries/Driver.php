<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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
#[AllowDynamicProperties]
class CI_Driver_Library {

	/**
	 * Array of drivers that are available to use with the driver class
	 *
	 * @var array
	 */
	protected $valid_drivers = array();

	/**
	 * Name of the current class - usually the driver class
	 *
	 * @var string
	 */
	protected $lib_name;

	/**
	 * Get magic method
	 *
	 * The first time a child is used it won't exist, so we instantiate it
	 * subsequents calls will go straight to the proper child.
	 *
	 * @param	string	Child class name
	 * @return	object	Child class
	 */
	public function __get($child)
	{
		// Try to load the driver
		return $this->load_driver($child);
	}

	/**
	 * Load driver
	 *
	 * Separate load_driver call to support explicit driver load by library or user
	 *
	 * @param	string	Driver name (w/o parent prefix)
	 * @return	object	Child class
	 */
	public function load_driver($child)
	{
		// Get CodeIgniter instance and subclass prefix
		$prefix = config_item('subclass_prefix');

		if ( ! isset($this->lib_name))
		{
			// Get library name without any prefix
			$this->lib_name = str_replace(array('CI_', $prefix), '', get_class($this));
		}

		// The child will be prefixed with the parent lib
		$child_name = $this->lib_name.'_'.$child;

		// See if requested child is a valid driver
		if ( ! in_array($child, $this->valid_drivers))
		{
			// The requested driver isn't valid!
			$msg = 'Invalid driver requested: '.$child_name;
			log_message('error', $msg);
			show_error($msg);
		}

		// Get package paths and filename case variations to search
		$CI = get_instance();
		$paths = $CI->load->get_package_paths(TRUE);

		// Is there an extension?
		$class_name = $prefix.$child_name;
		$found = class_exists($class_name, FALSE);
		if ( ! $found)
		{
			// Check for subclass file
			foreach ($paths as $path)
			{
				// Does the file exist?
				$file = $path.'libraries/'.$this->lib_name.'/drivers/'.$prefix.$child_name.'.php';
				if (file_exists($file))
				{
					// Yes - require base class from BASEPATH
					$basepath = BASEPATH.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
					if ( ! file_exists($basepath))
					{
						$msg = 'Unable to load the requested class: CI_'.$child_name;
						log_message('error', $msg);
						show_error($msg);
					}

					// Include both sources and mark found
					include_once($basepath);
					include_once($file);
					$found = TRUE;
					break;
				}
			}
		}

		// Do we need to search for the class?
		if ( ! $found)
		{
			// Use standard class name
			$class_name = 'CI_'.$child_name;
			if ( ! class_exists($class_name, FALSE))
			{
				// Check package paths
				foreach ($paths as $path)
				{
					// Does the file exist?
					$file = $path.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
					if (file_exists($file))
					{
						// Include source
						include_once($file);
						break;
					}
				}
			}
		}

		// Did we finally find the class?
		if ( ! class_exists($class_name, FALSE))
		{
			if (class_exists($child_name, FALSE))
			{
				$class_name = $child_name;
			}
			else
			{
				$msg = 'Unable to load the requested driver: '.$class_name;
				log_message('error', $msg);
				show_error($msg);
			}
		}

		// Instantiate, decorate and add child
		$obj = new $class_name();
		$obj->decorate($this);
		$this->$child = $obj;
		return $this->$child;
	}

}

// --------------------------------------------------------------------------

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

	/**
	 * Instance of the parent class
	 *
	 * @var object
	 */
	protected $_parent;

	/**
	 * List of methods in the parent class
	 *
	 * @var array
	 */
	protected $_methods = array();

	/**
	 * List of properties in the parent class
	 *
	 * @var array
	 */
	protected $_properties = array();

	/**
	 * Array of methods and properties for the parent class(es)
	 *
	 * @static
	 * @var	array
	 */
	protected static $_reflections = array();

	/**
	 * Decorate
	 *
	 * Decorates the child with the parent driver lib's methods and properties
	 *
	 * @param	object
	 * @return	void
	 */
	public function decorate($parent)
	{
		$this->_parent = $parent;

		// Lock down attributes to what is defined in the class
		// and speed up references in magic methods

		$class_name = get_class($parent);

		if ( ! isset(self::$_reflections[$class_name]))
		{
			$r = new ReflectionObject($parent);

			foreach ($r->getMethods() as $method)
			{
				if ($method->isPublic())
				{
					$this->_methods[] = $method->getName();
				}
			}

			foreach ($r->getProperties() as $prop)
			{
				if ($prop->isPublic())
				{
					$this->_properties[] = $prop->getName();
				}
			}

			self::$_reflections[$class_name] = array($this->_methods, $this->_properties);
		}
		else
		{
			list($this->_methods, $this->_properties) = self::$_reflections[$class_name];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __call magic method
	 *
	 * Handles access to the parent driver library's methods
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($method, $args = array())
	{
		if (in_array($method, $this->_methods))
		{
			return call_user_func_array(array($this->_parent, $method), $args);
		}

		throw new BadMethodCallException('No such method: '.$method.'()');
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic method
	 *
	 * Handles reading of the parent driver library's properties
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function __get($var)
	{
		if (in_array($var, $this->_properties))
		{
			return $this->_parent->$var;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __set magic method
	 *
	 * Handles writing to the parent driver library's properties
	 *
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __set($var, $val)
	{
		if (in_array($var, $this->_properties))
		{
			$this->_parent->$var = $val;
		}
	}

}
