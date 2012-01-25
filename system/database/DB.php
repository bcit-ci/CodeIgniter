<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
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

/**
 * Initialize the database
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 * @param 	string
 * @param 	bool	Determines if active record should be used or not
 */
function &DB($params = '', $active_record_override = NULL)
{
	// Load the DB config file if a DSN string wasn't passed
	if (is_string($params) AND strpos($params, '://') === FALSE)
	{
		// Is the config file in the environment folder?
		if (( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php'))
			&& ! file_exists($file_path = APPPATH.'config/database.php'))
		{
			show_error('The configuration file database.php does not exist.');
		}

		include($file_path);

		if ( ! isset($db) OR count($db) === 0)
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

		// Post-process the configuration, for PDO
		if ($params['dbdriver'] == 'pdo')
		{
			// Hostname generally would have this prototype
			// $db['hostname'] = 'pdodriver:host(/Server(/DSN))=hostname(/DSN);';
			// We need to get the prefix (pdodriver used by PDO).
			$pdodriver = '';
			$dsn = $params['hostname'];

			if (($fragments = explode(':', $dsn)) && count($fragments) >= 1)
			{
				$pdodriver = strtolower(current($fragments));
			}

			// Add these two variable into our params
			$params['pdodriver'] = $pdodriver;
			$params['dsn'] = $dsn;
		}
	}
	elseif (is_string($params))
	{

		/* parse the URL from the DSN string
		 *  Database settings can be passed as discreet
		 *  parameters or as a data source name in the first
		 *  parameter. DSNs must have this prototype:
		 *  $dsn = 'driver://username:password@hostname/database';
		 */
		if (($dsn = @parse_url($params)) === FALSE)
		{
			show_error('Invalid DB Connection String');
		}

		$params = array(
				'dbdriver' => $dsn['scheme'],
				'hostname' => (isset($dsn['host'])) ? rawurldecode($dsn['host']) : '',
				'port'     => (isset($dsn['port'])) ? rawurldecode($dsn['port']) : '',
				'username' => (isset($dsn['user'])) ? rawurldecode($dsn['user']) : '',
				'password' => (isset($dsn['pass'])) ? rawurldecode($dsn['pass']) : '',
				'database' => (isset($dsn['path'])) ? rawurldecode(substr($dsn['path'], 1)) : ''
			);

		// were additional config items set?
		if (isset($dsn['query']))
		{
			parse_str($dsn['query'], $extra);

			foreach ($extra as $key => $val)
			{
				// booleans please
				if (strtoupper($val) === 'TRUE')
				{
					$val = TRUE;
				}
				elseif (strtoupper($val) === 'FALSE')
				{
					$val = FALSE;
				}

				$params[$key] = $val;
			}
		}

		// Post-process the configuration, for PDO
		// Assuming that the following DSN string format is used:
		// $dsn = 'pdo://username:password@hostname:port/database?pdodriver=pgsql';
		if ( $params['dbdriver'] == 'pdo')
		{
			// Invalid DSN, display an error
			if ( ! array_key_exists('pdodriver', $params))
			{
				show_error('Invalid DB Connection String for PDO');
			}

			$params['pdodriver'] = strtolower($params['pdodriver']);
			$params['dsn'] = $params['pdodriver'].':';

			// Add hostname to the DSN for databases that need it
			if ( ! empty($params['hostname']) && in_array($params['pdodriver'], array('informix', 'mysql', 'pgsql', 'sybase', 'mssql', 'dblib', 'cubrid')))
			{
			    $params['dsn'] .= 'host='.$params['hostname'].';';
			}

			// Add a port to the DSN for databases that can use it
			if ( ! empty($params['port']) && in_array($params['pdodriver'], array('informix', 'mysql', 'pgsql', 'ibm', 'cubrid')))
			{
			    $params['dsn'] .= 'port='.$params['port'].';';
			}
		}
	}

	// No DB specified yet? Beat them senseless...
	if ( ! isset($params['dbdriver']) OR $params['dbdriver'] == '')
	{
		show_error('You have not selected a database type to connect to.');
	}

	// Post-process the configuration, for PDO
	if ($params['dbdriver'] == 'pdo')
	{
	    // Add the database name to the DSN, if needed
	    if (stripos($params['dsn'], 'dbname') === FALSE 
	       && in_array($params['pdodriver'], array('4d', 'pgsql', 'mysql', 'firebird', 'sybase', 'mssql', 'dblib', 'cubrid')))
	    {
	        $params['dsn'] .= 'dbname='.$params['database'].';';
	    }
	    elseif (stripos($params['dsn'], 'database') === FALSE && in_array($params['pdodriver'], array('ibm', 'sqlsrv')))
	    {
	    	if (stripos($params['dsn'], 'dsn') === FALSE)
	    	{
		        $params['dsn'] .= 'database='.$params['database'].';';
	    	}
	    }
	    elseif ($params['pdodriver'] === 'sqlite' && $params['dsn'] === 'sqlite:')
	    {
	        if ($params['database'] !== ':memory')
	        {
	            if ( ! file_exists($params['database']))
	            {
	                show_error('Invalid DB Connection string for PDO SQLite');
	            }

	            $params['dsn'] .= (strpos($params['database'], DIRECTORY_SEPARATOR) !== 0) ? DIRECTORY_SEPARATOR : '';
	        }

	        $params['dsn'] .= $params['database'];
	    }

	    // Add charset to the DSN, if needed
	    if (in_array($params['pdodriver'], array('4d', 'mysql', 'sybase', 'mssql', 'dblib', 'oci'))
	       && array_key_exists('char_set', $params))
	    {
	        $params['dsn'] .= 'charset='.$params['char_set'].';';
	    }
	}

	// Load the DB classes. Note: Since the active record class is optional
	// we need to dynamically create a class that extends proper parent class
	// based on whether we're using the active record class or not.
	if ($active_record_override !== NULL)
	{
		$active_record = $active_record_override;
	}

	require_once(BASEPATH.'database/DB_driver.php');

	if ( ! isset($active_record) OR $active_record == TRUE)
	{
		require_once(BASEPATH.'database/DB_active_rec.php');
		if ( ! class_exists('CI_DB'))
		{
			class CI_DB extends CI_DB_active_record { }
		}
	}
	elseif ( ! class_exists('CI_DB'))
	{
		class CI_DB extends CI_DB_driver { }
	}

	require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php');

	// Instantiate the DB adapter
	$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
	$DB = new $driver($params);

	if ($DB->autoinit == TRUE)
	{
		$DB->initialize();
	}

	if (isset($params['stricton']) && $params['stricton'] == TRUE)
	{
		$DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
	}

	return $DB;
}

/* End of file DB.php */
/* Location: ./system/database/DB.php */