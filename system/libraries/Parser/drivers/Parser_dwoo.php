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
defined('BASEPATH') || exit('No direct script access allowed');

require_once BASEPATH . 'libraries/parser/drivers/Dwoo/dwooAutoload.php';

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_dwoo extends CI_Driver {

	/**
	* dwoo template instance
	*
	* @var object
	*/
	protected $dwoo;
	
	protected $dwoo_tpl_dir;
	
	private $ci;

	public function __construct()
	{
		parent::__construct();

		$this->ci = &get_instance();
		$this->dwoo = new Dwoo();
		
		// set the config items
		$this->dwoo->setCompileDir($this->dwoo_config['compile_dir']);	//APPPATH . "views/templates_c";
		$this->dwoo->setCacheDir($this->dwoo_config['cache_dir']);	//APPPATH . "views/templates_cache";
		$this->dwoo->dwoo_tpl_dir = dwoo_config['template_dir'];	//APPPATH . "views/templates";
		
		log_message('debug', "Dwoo Class Initialized");
	}
	
	public function parse($template, $data, $return = FALSE)
	{
		if (strpos($template, '/'
	
		if ($return)
		{
			return $this->dwoo->get($template, $data);
		}
		else
		{
			$this->dwoo->output($template, $data);
		}
	}
	
	public function parse_string($template, $data, $return = FALSE)
	{
		$tpl = new Dwoo_Template_String($template);
		
		if ($return)
		{
			return $this->dwoo->get($tpl, $data);
		}
		else
		{
			$this->dwoo->output($tpl, $data);
		}
	}
	
	/**
	* Magic Methods
	*
	* The following magic methods are defined so that they try to call dwoo first
	* and then they try to call the "parent" driver.
	*/
	
	public function __get($property)
	{
		if (array_key_exists($property, $this->dwoo_properties))
		{
			return $this->dwoo->{$property};
		}
		else
		{
			return parent::__get($property);	// we may be trying to access a "parent" property
		}
	}
	
	public function __call($method, $args = array())
	{
		if (array_key_exists($method, $this->dwoo_methods))
		{
			return call_user_func_array(array($this->dwoo, $method), $args);
		}
		else
		{
			return parent::__call($method, $args);
		}
		
	}

}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser.php */