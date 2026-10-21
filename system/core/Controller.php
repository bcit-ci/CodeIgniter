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

// Polyfill for pre-8.2 PHP
if (PHP_VERSION_ID >= 80000 && class_exists('Attribute', false) && !class_exists('AllowDynamicProperties', false)) {
	#[Attribute(Attribute::TARGET_CLASS)]
	class AllowDynamicProperties {}
}

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/general/controllers.html
 */
#[AllowDynamicProperties]
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * CI_Loader
	 *
	 * @var	CI_Loader
	 */
	public $load;

	/**
	 * CI_Benchmark
	 *
	 * @var	CI_Benchmark
	 */
	public $benchmark;

	/**
	 * CI_Hooks
	 *
	 * @var	CI_Hooks
	 */
	public $hooks;

	/**
	 * CI_Config
	 *
	 * @var	CI_Config
	 */
	public $config;

	/**
	 * CI_Log
	 *
	 * @var	CI_Log
	 */
	public $log;

	/**
	 * CI_Utf8
	 *
	 * @var	CI_Utf8
	 */
	public $utf8;

	/**
	 * CI_URI
	 *
	 * @var	CI_URI
	 */
	public $uri;

	/**
	 * CI_Exceptions
	 *
	 * @var	CI_Exceptions
	 */
	public $exceptions;

	/**
	 * CI_Router
	 *
	 * @var	CI_Router
	 */
	public $router;

	/**
	 * CI_Output
	 *
	 * @var	CI_Output
	 */
	public $output;

	/**
	 * CI_Security
	 *
	 * @var	CI_Security
	 */
	public $security;

	/**
	 * CI_Input
	 *
	 * @var	CI_Input
	 */
	public $input;

	/**
	 * CI_Lang
	 *
	 * @var	CI_Lang
	 */
	public $lang;

	/**
	 * CI_DB
	 *
	 * @var	CI_DB
	 */
	public $db;

	/**
	 * CI_Session
	 *
	 * @var	CI_Session
	 */
	public $session;

	/**
	 * CI_Encryption
	 *
	 * @var	CI_Encryption
	 */
	public $encryption;

	/**
	 * CI_Language
	 *
	 * @var	CI_Language
	 */
	public $language;

	/**
	 * CI_Email
	 *
	 * @var	CI_Email
	 */
	public $sendmail;

	/**
	 * Loop library
	 *
	 * @var	object
	 */
	public $loop;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

	// --------------------------------------------------------------------

	/**
	 * Allow dynamic properties
	 *
	 * @param	string	$name	Property name
	 * @param	mixed	$value	Property value
	 * @return	void
	 */
	public function __set($name, $value)
	{
		$this->$name = $value;
	}

}
