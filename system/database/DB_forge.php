<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * Database Utility Class
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_forge {
	protected $CI;
	public $fields			= array();
	public $keys			= array();
	public $primary_keys	= array();
	public $db_char_set		= '';

	/**
	 * Constructor
	 *
	 * Grabs the CI super object instance so we can access it.
	 */
	public function __construct() {
		// Assign the main database object to $this->CI->db
		$this->CI =& get_instance();
		$this->CI->log_message('debug', 'Database Forge Class Initialized');
	}

	/**
	 * Create database
	 *
	 * @param	string	the database name
	 * @return	bool
	 */
	public function create_database($db_name) {
		$sql = $this->_create_database($db_name);

		if (is_bool($sql)) {
			return $sql;
		}

		return $this->CI->db->query($sql);
	}

	/**
	 * Drop database
	 *
	 * @param	string	the database name
	 * @return	bool
	 */
	public function drop_database($db_name) {
		$sql = $this->_drop_database($db_name);

		if (is_bool($sql)) {
			return $sql;
		}

		return $this->CI->db->query($sql);
	}

	/**
	 * Add Key
	 *
	 * @param	string	key
	 * @param	string	type
	 * @return	void
	 */
	public function add_key($key = '', $primary = FALSE) {
		if (is_array($key)) {
			foreach ($key as $one) {
				$this->add_key($one, $primary);
			}

			return;
		}

		if ($key == '') {
			throw new CI_ShowError('Key information is required for that operation.');
		}

		if ($primary === TRUE) {
			$this->primary_keys[] = $key;
		}
		else {
			$this->keys[] = $key;
		}
	}

	/**
	 * Add Field
	 *
	 * @param	string	collation
	 * @return	void
	 */
	public function add_field($field = '') {
		if ($field == '') {
			throw new CI_ShowError('Field information is required.');
		}

		if (is_string($field)) {
			if ($field == 'id') {
				$this->add_field(array('id' => array(
					'type' => 'INT',
					'constraint' => 9,
					'auto_increment' => TRUE
				)));
				$this->add_key('id', TRUE);
			}
			else {
				if (strpos($field, ' ') === FALSE) {
					throw new CI_ShowError('Field information is required for that operation.');
				}

				$this->fields[] = $field;
			}
		}

		if (is_array($field)) {
			$this->fields = array_merge($this->fields, $field);
		}
	}

	/**
	 * Create Table
	 *
	 * @param	string	the table name
	 * @return	bool
	 */
	public function create_table($table = '', $if_not_exists = FALSE) {
		if ($table == '') {
			throw new CI_ShowError('A table name is required for that operation.');
		}

		if (count($this->fields) == 0) {
			throw new CI_ShowError('Field information is required.');
		}

		$sql = $this->_create_table($this->CI->db->dbprefix.$table, $this->fields, $this->primary_keys, $this->keys,
			$if_not_exists);

		$this->_reset();
		return $this->CI->db->query($sql);
	}

	/**
	 * Drop Table
	 *
	 * @param	string	the table name
	 * @return	bool
	 */
	public function drop_table($table_name) {
		$sql = $this->_drop_table($this->CI->db->dbprefix.$table_name);

		if (is_bool($sql)) {
			return $sql;
		}

		return $this->CI->db->query($sql);
	}

	/**
	 * Rename Table
	 *
	 * @param	string	the old table name
	 * @param	string	the new table name
	 * @return	bool
	 */
	public function rename_table($table_name, $new_table_name) {
		if ($table_name == '' OR $new_table_name == '') {
			throw new CI_ShowError('A table name is required for that operation.');
		}

		$sql = $this->_rename_table($this->CI->db->dbprefix.$table_name, $this->CI->db->dbprefix.$new_table_name);
		return $this->CI->db->query($sql);
	}

	/**
	 * Column Add
	 *
	 * @param	string	the table name
	 * @param	string	the column name
	 * @param	string	the column definition
	 * @return	bool
	 */
	public function add_column($table = '', $field = array(), $after_field = '') {
		if ($table == '') {
			throw new CI_ShowError('A table name is required for that operation.');
		}

		// add field info into field array, but we can only do one at a time
		// so we cycle through
		foreach ($field as $k => $v) {
			$this->add_field(array($k => $field[$k]));

			if (count($this->fields) == 0) {
				throw new CI_ShowError('Field information is required.');
			}

			$sql = $this->_alter_table('ADD', $this->CI->db->dbprefix.$table, $this->fields, $after_field);

			$this->_reset();

			if ($this->CI->db->query($sql) === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Column Drop
	 *
	 * @param	string	the table name
	 * @param	string	the column name
	 * @return	bool
	 */
	public function drop_column($table = '', $column_name = '') {
		if ($table == '') {
			throw new CI_ShowError('A table name is required for that operation.');
		}

		if ($column_name == '') {
			throw new CI_ShowError('A column name is required for that operation.');
		}

		$sql = $this->_alter_table('DROP', $this->CI->db->dbprefix.$table, $column_name);

		return $this->CI->db->query($sql);
	}

	/**
	 * Column Modify
	 *
	 * @param	string	the table name
	 * @param	string	the column name
	 * @param	string	the column definition
	 * @return	bool
	 */
	public function modify_column($table = '', $field = array()) {
		if ($table == '') {
			throw new CI_ShowError('A table name is required for that operation.');
		}

		// add field info into field array, but we can only do one at a time
		// so we cycle through
		foreach ($field as $k => $v) {
			// If no name provided, use the current name
			if ( ! isset($field[$k]['name'])) {
				$field[$k]['name'] = $k;
			}

			$this->add_field(array($k => $field[$k]));

			if (count($this->fields) == 0) {
				throw new CI_ShowError('Field information is required.');
			}

			$sql = $this->_alter_table('CHANGE', $this->CI->db->dbprefix.$table, $this->fields);

			$this->_reset();

			if ($this->CI->db->query($sql) === FALSE) {
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Reset
	 *
	 * Resets table creation vars
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function _reset() {
		$this->fields		= array();
		$this->keys			= array();
		$this->primary_keys	= array();
	}
}

/* End of file DB_forge.php */
/* Location: ./system/database/DB_forge.php */
