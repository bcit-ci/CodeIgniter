<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/*
| -------------------------------------------------------------------------
| Twig Config Items
| -------------------------------------------------------------------------
| Below are the twig config items as defined at http://twig.sensiolabs.org/doc/api.html
| However some config items are CI specific. Here is the meaning of the CI
| specific items:
| 'cache'	= set to TRUE if you want to use caching with twig. If true
| then cache path will be the compile_dir as defined in ./application/config/parser.php
| 
*/

$config['debug']				= FALSE;
$config['charset']				= 'utf-8';
$config['base_template_class']	= 'Twig_Template';
$config['strict_variables']		=  FALSE;
$config['autoescape']			= 'html';
$config['cache']				= FALSE;
$config['auto_reload']			= NULL;
$config['optimizations']		= -1;

/* End of file profiler.php */
/* Location: ./application/config/twig.php */