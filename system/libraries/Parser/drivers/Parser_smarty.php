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

require_once BASEPATH . 'libraries/parser/drivers/smarty/Smarty.class.php';

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_smarty extends CI_Driver {

	/**
	* Smarty template instance
	*
	* @var object
	*/
	protected $smarty;
	
	private $ci;

	public function __construct()
	{
		parent::__construct();

		$this->ci = &get_instance();
		$this->smarty = new Smarty();
		$template_config = $this->template_config;	// get template_config from "parent"
		
		// set the config items
		$this->smarty->setCompileDir($template_config['compile_dir']);
		$this->smarty->setTemplateDir($template_config['template_dir']);
		$this->smarty->setCacheDir($template_config['cache_dir']);

		log_message('debug', "Smarty Class Initialized");
	}
	
	public function parse($template, $data, $return = FALSE)
	{
		foreach ($data as $key => $value)
		{
			$this->smarty->assign($key, $value);
		}
		
		if ($return)
		{
			return $this->smarty->fetch($template);
		}
		else
		{
			$this->smarty->display($template);
		}
	}
	
	public function parse_string($template, $data, $return = FALSE)
	{
		foreach ($data as $key => $value)
		{
			$this->smarty->assign($key, $value);
		}
		
		if ($return)
		{
			return $this->smarty->fetch('string:'.$template);
		}
		else
		{
			$this->smarty->display('string:'.$template);
		}
	}
	
	/**
	 * Magic Methods
	 *
	 * The following magic method are defined so that they try to call twig first
	 * and then they try to call the "parent" driver.
	 */
	public function __call($method, $args = array())
	{
		if (method_exists($this->twig, $method))
		{
			return call_user_func_array(array($this->twig, $method), $args);
		}
		else
		{
			return parent::__call($method, $args);
		}
		
	}
}

/* End of file Parser.php */
/* Location: ./system/libraries/Parser/drivers/Parser_smarty.php */