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
 * PDO PostgreSQL Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_pgsql_forge extends CI_DB_pdo_forge {

	/**
	 * DROP TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_drop_table_if	= 'DROP TABLE IF EXISTS';

	/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
	protected $_unsigned		= array(
		'INT2'		=> 'INTEGER',
		'SMALLINT'	=> 'INTEGER',
		'INT'		=> 'BIGINT',
		'INT4'		=> 'BIGINT',
		'INTEGER'	=> 'BIGINT',
		'INT8'		=> 'NUMERIC',
		'BIGINT'	=> 'NUMERIC',
		'REAL'		=> 'DOUBLE PRECISION',
		'FLOAT'		=> 'DOUBLE PRECISION'
	);

	/**
	 * NULL value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_null		= 'NULL';

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	object	&$db	Database object
	 * @return	void
	 */
	public function __construct(&$db)
	{
		parent::__construct($db);

		if (version_compare($this->db->version(), '9.0', '>'))
		{
			$this->create_table_if = 'CREATE TABLE IF NOT EXISTS';
		}
	}

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
		if (in_array($alter_type, array('DROP', 'ADD'), TRUE))
		{
			return parent::_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE '.$this->db->escape_identifiers($table);
		$sqls = array();
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				return FALSE;
			}

			if (version_compare($this->db->version(), '8', '>=') && isset($field[$i]['type']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' TYPE '.$field[$i]['type'].$field[$i]['length'];
			}

			if ( ! empty($field[$i]['default']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' SET DEFAULT '.$field[$i]['default'];
			}

			if (isset($field[$i]['null']))
			{
				$sqls[] = $sql.' ALTER COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.($field[$i]['null'] === TRUE ? ' DROP NOT NULL' : ' SET NOT NULL');
			}

			if ( ! empty($field[$i]['new_name']))
			{
				$sqls[] = $sql.' RENAME COLUMN '.$this->db->escape_identifiers($field[$i]['name'])
					.' TO '.$this->db->escape_identifiers($field[$i]['new_name']);
			}
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
		// Reset field lenghts for data types that don't support it
		if (isset($attributes['CONSTRAINT']) && stripos($attributes['TYPE'], 'int') !== FALSE)
		{
			$attributes['CONSTRAINT'] = NULL;
		}

		switch (strtoupper($attributes['TYPE']))
		{
			case 'TINYINT':
				$attributes['TYPE'] = 'SMALLINT';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'MEDIUMINT':
				$attributes['TYPE'] = 'INTEGER';
				$attributes['UNSIGNED'] = FALSE;
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
		if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE)
		{
			$field['type'] = ($field['type'] === 'NUMERIC')
						? 'BIGSERIAL'
						: 'SERIAL';
		}
	}

}

/* End of file pdo_pgsql_forge.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_pgsql_forge.php */