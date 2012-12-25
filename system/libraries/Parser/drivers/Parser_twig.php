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

require_once BASEPATH . 'libraries/parser/drivers/Twig/Autoloader.php';
Twig_Autoloader::register();

/**
 * Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/parser.html
 */
class CI_Parser_twig extends CI_Driver {

	public $twig_loader_fs;
	
	public $twig_loader_str;

	/**
	* twig template instance
	*
	* @var object
	*/
	protected $twig;

	private $ci;

	public function __construct()
	{
		parent::__construct();

		$this->ci = &get_instance();
		
		$twig_config = array();
		$template_config = $this->template_config;	// get template_config from "parent"
		
		if ($this->ci->config->load('twig', TRUE, TRUE))
		{
			$twig_config = $this->ci->config->item('twig');
		}
		
		if ($twig_config['cache'])
		{
			$twig_config['cache'] = $template_config['compile_dir'];
		}
		
		$this->twig				= new Twig_Environment(NULL, $twig_config);	// set the config
		$this->twig_loader_fs	= new Twig_Loader_Filesystem($template_config['template_dir']);
		$this->twig_loader_str	= new Twig_Loader_String();
		
		log_message('debug', "Twig Class Initialized");
	}
	
	public function parse($template, $data, $return = FALSE)
	{
		$this->twig->setLoader($this->twig_loader_fs);

		if ($return)
		{
			return $this->twig->render($template, $data);
		}
		else
		{
			$this->twig->display($template, $data);
		}
	}
	
	public function parse_string($template, $data, $return = FALSE)
	{
		$this->twig->setLoader($this->twig_loader_str);
		
		if ($return)
		{
			return $this->twig->render($template, $data);
		}
		else
		{
			$this->twig->display($template, $data);
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
/* Location: ./system/libraries/Parser/drivers/Parser_twig.php */