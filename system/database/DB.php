<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Initialize the database
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
function &DB($params = '', $active_record_override = FALSE)
{
	// Load the DB config file if a DSN string wasn't passed
	if (is_string($params) AND strpos($params, '://') === FALSE)
	{
		include(APPPATH.'config/database'.EXT);
		
		if ( ! isset($db) OR count($db) == 0)
		{
			show_error('No database connection settings were found in the database config file.');
		}
		
		if ($params != '')
		{
			$active_group = $params;
		}
		
		if ( ! isset($active_group) OR ! isset($db[$active_group]))
		{
			show_error('You have specified an invalid database connection group.');
		}
		
		$params = $db[$active_group];
	}
	elseif (is_string($params))
	{
		
		/* parse the URL from the DSN string
		*  Database settings can be passed as discreet
	 	*  parameters or as a data source name in the first
	 	*  parameter. DSNs must have this prototype:
	 	*  $dsn = 'driver://username:password@hostname/database';
		*/
	
		if (($dns = @parse_url($params)) === FALSE)
		{
			show_error('Invalid DB Connection String');
		}
		
		$params = array(
							'dbdriver'	=> $dns['scheme'],
							'hostname'	=> (isset($dns['host'])) ? rawurldecode($dns['host']) : '',
							'username'	=> (isset($dns['user'])) ? rawurldecode($dns['user']) : '',
							'password'	=> (isset($dns['pass'])) ? rawurldecode($dns['pass']) : '',
							'database'	=> (isset($dns['path'])) ? rawurldecode(substr($dns['path'], 1)) : ''
						);
		
		// were additional config items set?
		if (isset($dns['query']))
		{
			parse_str($dns['query'], $extra);

			foreach($extra as $key => $val)
			{
				// booleans please
				if (strtoupper($val) == "TRUE")
				{
					$val = TRUE;
				}
				elseif (strtoupper($val) == "FALSE")
				{
					$val = FALSE;
				}

				$params[$key] = $val;
			}
		}
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
	
	if ($active_record_override == TRUE)
	{
		$active_record = TRUE;
	}
	
	require_once(BASEPATH.'database/DB_driver'.EXT);

	if ( ! isset($active_record) OR $active_record == TRUE)
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
	
	if ($DB->autoinit == TRUE)
	{
		$DB->initialize();
	}
	
	return $DB;
}	



/* End of file DB.php */
/* Location: ./system/database/DB.php */