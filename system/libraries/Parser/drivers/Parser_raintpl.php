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

require_once BASEPATH . 'libraries/parser/drivers/raintpl/rain.tpl.class.php';

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_raintpl extends CI_Driver {

	/**
	* raintpl template instance
	*
	* @var object
	*/
	protected $raintpl;
	
	protected $raintpl_methods;
	
	protected $raintpl_properties;
	
	protected $raintpl_config;
	
	private $ci;

	public function __construct()
	{
		parent::__construct();

		$this->ci = &get_instance();
		$this->raintpl = new RainTPL();
		$this->raintpl_config = $this->default_config['raintpl'];	// get from "parent" parser class
		
		// lets cache all of the methods and properties for raintpl to use in magic methods
		$r = new ReflectionObject($this->raintpl);

		foreach ($r->getMethods() as $method)
		{
			if ($method->isPublic())
			{
				$this->raintpl_methods[$method->getName()] = NULL;
			}
		}

		foreach ($r->getProperties() as $prop)
		{
			if ($prop->isPublic())
			{
				$this->raintpl_properties[$prop->getName()] = NULL;
			}
		}
		
		// set the config items
		RainTPL::configure('tpl_dir', $this->raintpl_config['template_dir']);
		RainTPL::configure('cache_dir', $this->raintpl_config['cache_dir']);
		RainTPL::configure('base_url', $this->raintpl_config['base_url']);
		RainTPL::configure('tpl_ext', $this->raintpl_config['tpl_ext']);
		RainTPL::configure('path_replace', $this->raintpl_config['path_replace']);
		RainTPL::configure('path_replace_list', $this->raintpl_config['path_replace_list']);
		RainTPL::configure('black_list', $this->raintpl_config['black_list']);
		RainTPL::configure('check_template_update', $this->raintpl_config['check_template_update']);
		RainTPL::configure('php_enabled', $this->raintpl_config['php_enabled']);
		
		log_message('debug', "raintpl Class Initialized");
	}
	
	public function parse($template, $data, $return = FALSE)
	{
		foreach ($data as $key => $value)
		{
			$this->raintpl->assign($key, $value);
		}

		return $this->raintpl->draw($template, $return);
	}
	
	public function parse_string($template, $data, $return = FALSE){return FALSE;}	// no such way to do so in current version of raintpl
	
	/**
	* Magic Methods
	*
	* The following magic methods are defined so that they try to call raintpl first
	* and then they try to call the "parent" driver.
	*/
	
	public function __get($property)
	{
		if (array_key_exists($property, $this->raintpl_properties))
		{
			return $this->raintpl->{$property};
		}
		else
		{
			return parent::__get($property);	// we may be trying to access a "parent" property
		}
	}
	
	public function __call($method, $args = array())
	{
		if (array_key_exists($method, $this->raintpl_methods))
		{
			return call_user_func_array(array($this->raintpl, $method), $args);
		}
		else
		{
			return parent::__call($method, $args);
		}
		
	}

}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser.php */