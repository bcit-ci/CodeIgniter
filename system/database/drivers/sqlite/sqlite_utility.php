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
 * @since		Version 1.0
 * @filesource
 */

/**
 * SQLite Utility Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite_utility extends CI_DB_utility {

	/**
	 * List databases
	 *
	 * I don't believe you can do a database listing with SQLite
	 * since each database is its own file. I suppose we could
	 * try reading a directory looking for SQLite files, but
	 * that doesn't seem like a terribly good idea
	 *
	 * @return	bool
	 */
	public function _list_databases()
	{
		return ($this->db_debug) ? $this->db->display_error('db_unsuported_feature') : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Optimize table query
	 *
	 * @param	string	the table name
	 * @return	bool
	 */
	public function _optimize_table($table)
	{
		// Not supported
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Repair table query
	 *
	 * @param	string	the table name
	 * @return	bool
	 */
	public function _repair_table($table)
	{
		// Not supported
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * SQLite Export
	 *
	 * @param	array	Preferences
	 * @return	mixed
	 */
	public function _backup($params = array())
	{
		// Currently unsupported
		return $this->db->display_error('db_unsuported_feature');
	}

}

/* End of file sqlite_utility.php */
/* Location: ./system/database/drivers/sqlite/sqlite_utility.php */
