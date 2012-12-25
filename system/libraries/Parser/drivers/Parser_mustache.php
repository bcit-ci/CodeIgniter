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

require_once BASEPATH . 'libraries/parser/drivers/Mustache/Autoloader.php';
Mustache_Autoloader::register();

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_mustache extends CI_Driver {

	/**
	* mustache template instance
	*
	* @var object
	*/
	protected $mustache;
	
	protected $mustache_methods;
	
	protected $mustache_properties;
	
	protected $mustache_config;
	
	protected $mustache_loader_fs;	// mustache loaders
	
	protected $mustache_loader_str;
	
	private $ci;

	public function __construct()
	{
		parent::__construct();

		$this->ci = &get_instance();
		
		$this->mustache_config		= $this->default_config['mustache'];	// get from "parent" parser class
		$this->mustache				= new Mustache_Engine($this->mustache_config);	// set the config
		$this->mustache_loader_fs	= new Mustache_Loader_FilesystemLoader($this->mustache_config['template_dir']);
		$this->mustache_loader_str	= new Mustache_Loader_StringLoader();
		
		// lets cache all of the methods and properties for mustache to use in magic methods
		$r = new ReflectionObject($this->mustache);

		foreach ($r->getMethods() as $method)
		{
			if ($method->isPublic())
			{
				// mustache has a LOT of methods, so we'll store the name as a key so that we can
				// use array_key_exists which is constant time and can be much faster than in_array
				// basically using the php array as a c++ unordered_set
				$this->mustache_methods[$method->getName()] = NULL;
			}
		}

		foreach ($r->getProperties() as $prop)
		{
			if ($prop->isPublic())
			{
				$this->mustache_properties[$prop->getName()] = NULL;
			}
		}
		
		log_message('debug', "mustache Class Initialized");
	}
	
	public function parse($template, $data, $return = FALSE)
	{
		$this->mustache->setLoader($this->mustache_loader_fs);

		if ($return)
		{
			return $this->mustache->render($template, $data);
		}
		else
		{
			echo $this->mustache->render($template, $data);
		}
	}
	
	public function parse_string($template, $data, $return = FALSE)
	{
		$this->mustache->setLoader($this->mustache_loader_str);
		
		if ($return)
		{
			return $this->mustache->render($template, $data);
		}
		else
		{
			echo $this->mustache->render($template, $data);
		}
	}
	
	/**
	* Magic Methods
	*
	* The following magic methods are defined so that they try to call mustache first
	* and then they try to call the "parent" driver.
	*/
	
	public function __get($property)
	{
		if (array_key_exists($property, $this->mustache_properties))
		{
			return $this->mustache->{$property};
		}
		else
		{
			return parent::__get($property);	// we may be trying to access a "parent" property
		}
	}
	
	public function __call($method, $args = array())
	{
		if (array_key_exists($method, $this->mustache_methods))
		{
			return call_user_func_array(array($this->mustache, $method), $args);
		}
		else
		{
			return parent::__call($method, $args);
		}
		
	}

}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser.php */