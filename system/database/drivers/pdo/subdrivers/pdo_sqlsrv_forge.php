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
 * @since		Version 2.1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PDO SQLSRV Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_sqlsrv_forge extends CI_DB_pdo_forge {

	/**
	 * CREATE TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_create_table_if	= "IF NOT EXISTS (SELECT * FROM sysobjects WHERE ID = object_id(N'%s') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)\nCREATE TABLE";

	/**
	 * DROP TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_drop_table_if	= "IF EXISTS (SELECT * FROM sysobjects WHERE ID = object_id(N'%s') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)\nDROP TABLE";

	/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
	protected $_unsigned		= array(
		'TINYINT'	=> 'SMALLINT',
		'SMALLINT'	=> 'INT',
		'INT'		=> 'BIGINT',
		'REAL'		=> 'FLOAT'
	);

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _alter_table($alter_type, $table, $field)
	{
		if (in_array($alter_type, array('ADD', 'DROP'), TRUE))
		{
			return parent::_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE '.$this->db->escape_identifiers($table).' ALTER COLUMN ';
		$sqls = array();
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			$sqls[] = $sql.$this->_process_column($field[$i]);
		}

		return $sqls;
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
			case 'MEDIUMINT':
				$attributes['TYPE'] = 'INTEGER';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'INTEGER':
				$attributes['TYPE'] = 'INT';
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
			$field['auto_increment'] = ' IDENTITY(1,1)';
		}
	}

}

/* End of file pdo_sqlsrv_forge.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_sqlsrv_forge.php */