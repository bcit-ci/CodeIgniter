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
 * PDO IBM DB2 Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_ibm_forge extends CI_DB_pdo_forge {

	/**
	 * RENAME TABLE IF statement
	 *
	 * @var	string
	 */
	protected $_rename_table	= 'RENAME TABLE %s TO %s';

	/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
	protected $_unsigned		= array(
		'SMALLINT'	=> 'INTEGER',
		'INT'		=> 'BIGINT',
		'INTEGER'	=> 'BIGINT'
	);

	/**
	 * DEFAULT value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
	protected $_default		= FALSE;

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
		if ($alter_type === 'CHANGE')
		{
			$alter_type = 'MODIFY';
		}

		return parent::_alter_table($alter_type, $table, $field);
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
	 * Field attribute UNIQUE
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_unique(&$attributes, &$field)
	{
		if ( ! empty($attributes['UNIQUE']) && $attributes['UNIQUE'] === TRUE)
		{
			$field['unique'] = ' UNIQUE';

			// UNIQUE must be used with NOT NULL
			$field['null'] = ' NOT NULL';
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
		// Not supported
	}

}

/* End of file pdo_ibm_forge.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_ibm_forge.php */