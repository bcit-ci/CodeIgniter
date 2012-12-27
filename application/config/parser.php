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
| Driver
| -------------------------------------------------------------------------
| The driver to be loaded. Similar to the database config file where you
| select which db driver to be used. The default is Codeigniter's Scorch parser.
| The adpater name must be all lower case.
| If you add any new drivers, then you must set the driver here to access
| your driver with this syntax:
| $this->parser->my_new_driver_func()
| instead of:
| $this->parser->my_new_driver->some_func()
*/

$config['driver'] = 'dummy';

/*
| -------------------------------------------------------------------------
| Valid drivers
| -------------------------------------------------------------------------
| If you add any new drivers, you need to put them in this array.
| e.g. array('tpl_engine_1', 'tpl_engine_2');
|
*/

$config['valid_drivers'] = array('dummy');

/*
| -------------------------------------------------------------------------
| File Directories
| -------------------------------------------------------------------------
| Some default directories for the parser drivers. These variables don't apply
| to every driver. For example, CI's Scorch driver uses CI views for its
| templates.
|
| 'template_dir'	= base directory to the stored templates
| 'cache_dir'		= directory to the cached templates (this directory has to be writable) if 
|	the chosen driver uses caching
| 'compile_dir'		= directory to the compiled templates. (this directory needs to be writable)
|
*/

$config['template_dir']	= APPPATH.'views/templates/';
$config['cache_dir']	= APPPATH.'cache/';
$config['compile_dir']	= APPPATH.'views/templates/compile/';

/* End of file profiler.php */
/* Location: ./application/config/parser.php */