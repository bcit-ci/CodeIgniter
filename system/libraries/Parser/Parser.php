<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser extends CI_Driver_Library {

	/**
	 * Config array for template paths, to be used by children drivers
	 *
	 * @var array
	 */
	public $template_config = array();

	/**
	 * Valid parser drivers
	 *
	 * @var array
	 */
	protected $valid_drivers = array(
		'scorch',
		'smarty',
		'dwoo',
		'raintpl',
		'twig',
		'mustache'
	);

	/**
	 * Reference to the driver
	 *
	 * @var mixed
	 */
	protected $driver;
	
	/**
	 * Reference to Codeigniter Instance
	 *
	 * @var object
	 */
	private $ci;


	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct($config = array())
	{
		$this->ci = &get_instance();
		
		if ($this->ci->config->load('parser', TRUE, TRUE))
		{
			$default_config = $this->ci->config->item('parser');
		}
		
		if (count($config) > 0)
		{
			$default_config = array_merge($default_config, $config);
		}
		
		// if user added some drivers, then we need put them in the valid_drivers array
		// to get loaded
		$this->valid_drivers = array_merge($this->valid_drivers, $default_config['valid_drivers']);
		
		// set template config items
		$this->template_config['template_dir']	= ($default_config['template_dir']) ? $default_config['template_dir'] : APPPATH.'views/templates/';
		$this->template_config['cache_dir']		= ($default_config['cache_dir']) ? $default_config['cache_dir'] : APPPATH.'cache/';
		$this->template_config['compile_dir']	= ($default_config['compile_dir']) ? $default_config['compile_dir'] : APPPATH.'views/templates/compile/';
		
		// set the driver
		$this->driver = $this->load_driver(($default_config['driver']) ? $default_config['driver'] : 'scorch');	// make sure we have a driver to load
	}
	
	/**
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param. Returns the loaded view
	 * as string
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data = array(), $return = FALSE)
	{
		return $this->{$this->driver}->parse($template, $data);
	}
	

	// --------------------------------------------------------------------

	/**
	 * Parse a String
	 *
	 * Parses pseudo-variables contained in the specified string,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($template, $data, $return = FALSE)
	{
		return $this->{$this->driver}->parse_string($template, $data, $return);
	}
	
	/**
	* __call magic method
	*
	* Any call to the parser driver will default to calling the specified adapter
	*
	* @param	string
	* @param	array
	* @return	mixed
	*/
	public function __call($method, $args = array())
	{
		return call_user_func_array(array($this->{$this->driver}, $method), $args);
	}
}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser/Parser.php */