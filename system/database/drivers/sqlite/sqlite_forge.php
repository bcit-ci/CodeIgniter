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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SQLite Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite_forge extends CI_DB_forge {

	/**
	 * CREATE TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_create_table_if	= FALSE;

	/**
	 * UNSIGNED support
	 *
	 * @var	bool|array
	 */
	protected $_unsigned		= FALSE;

	/**
	 * NULL value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_null		= 'NULL';

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @param	string	$db_name	(ignored)
	 * @return	bool
	 */
	public function create_database($db_name = '')
	{
		// In SQLite, a database is created when you connect to the database.
		// We'll return TRUE so that an error isn't generated
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @param	string	$db_name	(ignored)
	 * @return	bool
	 */
	public function drop_database($db_name = '')
	{
		if ( ! @file_exists($this->db->database) OR ! @unlink($this->db->database))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unable_to_drop') : FALSE;
		}
		elseif ( ! empty($this->db->data_cache['db_names']))
		{
			$key = array_search(strtolower($this->db->database), array_map('strtolower', $this->db->data_cache['db_names']), TRUE);
			if ($key !== FALSE)
			{
				unset($this->db->data_cache['db_names'][$key]);
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @todo	implement drop_column(), modify_column()
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _alter_table($alter_type, $table, $field)
	{
		if ($alter_type === 'DROP' OR $alter_type === 'CHANGE')
		{
			// drop_column():
			//	BEGIN TRANSACTION;
			//	CREATE TEMPORARY TABLE t1_backup(a,b);
			//	INSERT INTO t1_backup SELECT a,b FROM t1;
			//	DROP TABLE t1;
			//	CREATE TABLE t1(a,b);
			//	INSERT INTO t1 SELECT a,b FROM t1_backup;
			//	DROP TABLE t1_backup;
			//	COMMIT;

			return FALSE;
		}

		return parent::_alter_table($alter_type, $table, $field);
	}

	// --------------------------------------------------------------------

	/**
	 * Process column
	 *
	 * @param	array	$field
	 * @return	string
	 */
	protected function _process_column($field)
	{
		return $this->db->escape_identifiers($field['name'])
			.' '.$field['type']
			.$field['auto_increment']
			.$field['null']
			.$field['unique']
			.$field['default'];
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute TYPE
	 *
	 * Performs a data type mapping between different databases.
	 *
	 * @param	array	&$attributes
	 * @return	void
	 */
	protected function _attr_type(&$attributes)
	{
		switch (strtoupper($attributes['TYPE']))
		{
			case 'ENUM':
			case 'SET':
				$attributes['TYPE'] = 'TEXT';
				return;
			default: return;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute AUTO_INCREMENT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_auto_increment(&$attributes, &$field)
	{
		if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos($field['type'], 'int') !== FALSE)
		{
			$field['type'] = 'INTEGER PRIMARY KEY';
			$field['default'] = '';
			$field['null'] = '';
			$field['unique'] = '';
			$field['auto_increment'] = ' AUTOINCREMENT';

			$this->primary_keys = array();
		}
	}

}

/* End of file sqlite_forge.php */
/* Location: ./system/database/drivers/sqlite/sqlite_forge.php */