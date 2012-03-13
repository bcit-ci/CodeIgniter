<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Interbase/Firebird Utility Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_interbase_utility extends CI_DB_utility {

	/**
	 * List databases
	 *
	 * I don't believe you can do a database listing with Firebird
	 * since each database is its own file.  I suppose we could
	 * try reading a directory looking for Firebird files, but
	 * that doesn't seem like a terribly good idea
	 *
	 * @return	bool
	 */
	public function _list_databases()
	{
		if ($this->db_debug)
		{
			return $this->db->display_error('db_unsuported_feature');
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize table query
	 *
	 * Is optimization even supported in Interbase/Firebird?
	 *
	 * @param	string	the table name
	 * @return	object
	 */
	public function _optimize_table($table)
	{
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Repair table query
	 *
	 * Table repairs are not supported in Interbase/Firebird
	 *
	 * @param	string	the table name
	 * @return	object
	 */
	public function _repair_table($table)
	{
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Interbase/Firebird Export
	 *
	 * @param	string	$filename
	 * @return	mixed
	 */
	public function backup($filename)
	{
		if ($service = ibase_service_attach($this->db->hostname, $this->db->username, $this->db->password))
		{
			$res = ibase_backup($service, $this->db->database, $filename.'.fbk');
			
			//Close the service connection	
			ibase_service_detach($service);
			
			return $res;
		}
		else
		{
			return FALSE;
		}
	}
}

/* End of file interbase_utility.php */
/* Location: ./system/database/drivers/interbase/interbase_utility.php */