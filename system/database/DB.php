<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Initialize the database
 *
 * @category	Database
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/database/
 */
function &DB($params = '', $active_record = FALSE)
{
	// Load the DB config file if a DSN string wasn't passed
	if (is_string($params) AND strpos($params, '://') === FALSE)
	{
		include(APPPATH.'config/database'.EXT);
		
		$group = ($params == '') ? $active_group : $params;
		
		if ( ! isset($db[$group]))
		{
			show_error('You have specified an invalid database connection group: '.$group);
		}
		
		$params = $db[$group];
	}
	
	// No DB specified yet?  Beat them senseless...
	if ( ! isset($params['dbdriver']) OR $params['dbdriver'] == '')
	{
		show_error('You have not selected a database type to connect to.');
	}

	// Load the DB classes.  Note: Since the active record class is optional
	// we need to dynamically create a class that extends proper parent class
	// based on whether we're using the active record class or not.
	// Kudos to Paul for discovering this clever use of eval()
	
	if ($active_record == TRUE)
	{
		$params['active_r'] = TRUE;
	}
	
	require_once(BASEPATH.'database/DB_driver'.EXT);

	if ( ! isset($params['active_r']) OR $params['active_r'] == TRUE)
	{
		require_once(BASEPATH.'database/DB_active_rec'.EXT);
		
		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_active_record { }');
		}
	}
	else
	{
		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_driver { }');
		}
	}
			
	require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver'.EXT);

	// Instantiate the DB adapter
	$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
	$DB =& new $driver($params);	
	return $DB;
}	


?>