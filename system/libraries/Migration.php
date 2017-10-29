<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration Class
 *
 * All migrations should implement this, forces up() and down() and gives
 * access to the CI super-global.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Reactor Engineers
 * @link
 */
class CI_Migration {

	/**
	 * Whether the library is enabled
	 *
	 * @var bool
	 */
	protected $_migration_enabled = FALSE;

	/**
	 * Migration numbering type
	 *
	 * @var	bool
	 */
	protected $_migration_type = 'sequential';

	/**
	 * Path to migration classes
	 *
	 * @var string
	 */
	protected $_migration_path = NULL;

	/**
	 * Current migration version
	 *
	 * @var mixed
	 */
	protected $_migration_version = 0;

	/**
	 * Database table with migration info
	 *
	 * @var string
	 */
	protected $_migration_table = 'migrations';

	/**
	 * Whether to automatically run migrations
	 *
	 * @var	bool
	 */
	protected $_migration_auto_latest = FALSE;

	/**
	 * Migration basename regex
	 *
	 * @var string
	 */
	protected $_migration_regex;

	/**
	 * Error message
	 *
	 * @var string
	 */
	protected $_error_string = '';

	/**
	 * Initialize Migration Class
	 *
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($config = array())
	{
		// Only run this constructor on main library load
		if ( ! in_array(get_class($this), array('CI_Migration', config_item('subclass_prefix').'Migration'), TRUE))
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}

		log_message('info', 'Migrations Class Initialized');

		// Are they trying to use migrations while it is disabled?
		if ($this->_migration_enabled !== TRUE)
		{
			show_error('Migrations has been loaded but is disabled or set up incorrectly.');
		}

		// If not set, set it
		$this->_migration_path !== '' OR $this->_migration_path = APPPATH.'migrations/';

		// Add trailing slash if not set
		$this->_migration_path = rtrim($this->_migration_path, '/').'/';

		// Load migration language
		$this->lang->load('migration');

		// They'll probably be using dbforge
		$this->load->dbforge();

		// Make sure the migration table name was set.
		if (empty($this->_migration_table))
		{
			show_error('Migrations configuration file (migration.php) must have "migration_table" set.');
		}

		// Migration basename regex
		$this->_migration_regex = ($this->_migration_type === 'timestamp')
			? '/^\d{10}_(\w+)$/'
			: '/^\d{3}_(\w+)$/';

		// Make sure a valid migration numbering type was set.
		if ( ! in_array($this->_migration_type, array('sequential', 'timestamp')))
		{
			show_error('An invalid migration numbering type was specified: '.$this->_migration_type);
		}

		// If the migrations table is missing, make it
		if ( ! $this->db->table_exists($this->_migration_table))
		{
			$this->dbforge->add_field(array(
				'version' => array('type' => 'BIGINT', 'constraint' => 20),
			));

			$this->dbforge->create_table($this->_migration_table, TRUE);

			$this->db->insert($this->_migration_table, array('version' => 0));
		}

		// Do we auto migrate to the latest migration?
		if ($this->_migration_auto_latest === TRUE && ! $this->latest())
		{
			show_error($this->error_string());
		}
	}
	// --------------------------------------------------------------------

	/**
	 * Migrate UP method to a schema version
	 *
	 * Calls each migration step and run it if not already ran
	 *
	 * @param	string	$version	Optional Target schema version
	 * @return	array having status of each migration
	 */
	public function migrateup($version = '')
	{
		// Note: We use strings, so that timestamp versions work on 32-bit systems
		$current_version = $this->_get_version();

		$migrations = $this->find_migrations();
    if ($version != '')
    {
      $this->db->where("version",$version);
    }
    $query = $this->db->get($this->_migration_table);
    $old_migrations = array();
    foreach ($query->result() as $row)
    {
      $old_migrations[] = $row->version;
    }
    
    //$this->db->insert($this->_migration_table, array('version' => 0));
    $method = 'up';
		$pending = array();
		$output_array = array();
    if ($version == "")
    {
      foreach ($migrations as $number => $file)
      {
        if (!in_array($number,$old_migrations))
        {
          include_once($file);
          $class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

          // Validate the migration file structure
          if ( ! class_exists($class, FALSE))
          {
            $output_array[] = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
          }
          elseif ( ! is_callable(array($class, $method)))
          {
            $output_array[] = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
          }
          else
          {
            $pending[$number] = array($class, $method);
          }
        }
      }
    }
    else
    {
      if (array_key_exists($version,$migrations))
      {
        $number = $version;
        $file = $migrations[$number];
        if (!in_array($number,$old_migrations))
        {
          include_once($file);
          $class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

          // Validate the migration file structure
          if ( ! class_exists($class, FALSE))
          {
            $output_array[] = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
            //return FALSE;
          }
          elseif ( ! is_callable(array($class, $method)))
          {
            $output_array[] = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
          }
          else
          {
            $pending[$number] = array($class, $method);
          }
        }
      }
      else
      {
        $output_array[] =  sprintf($this->lang->line('migration_not_found'), $version);
      }
    }
    // Now just run the necessary migrations
    foreach ($pending as $number => $migration)
    {
      log_message('debug', 'Migrating '.$method.' from version '.$current_version.' to version '.$number);

      $migration[0] = new $migration[0];
      call_user_func($migration);
      $current_version = $number;
      $this->_add_version($current_version);
      $output_array[] =  'Version '.$number.' ran successfully!';
    }

		return $output_array;
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Migrate DOWN method to a schema version
	 *
	 * Calls each migration step and run it if not already ran
	 *
	 * @param	string	$version	Optional Target schema version
	 * @return	array having status of each migration
	 */
	public function migratedown($version = '')
	{
		// Note: We use strings, so that timestamp versions work on 32-bit systems
		$current_version = $this->_get_version();

		$migrations = $this->find_migrations();
    if ($version != '')
    {
      $this->db->where("version",$version);
    }
    $query = $this->db->get($this->_migration_table);
    $old_migrations = array();
    foreach ($query->result() as $row)
    {
      $old_migrations[] = $row->version;
    }
    
    //$this->db->insert($this->_migration_table, array('version' => 0));
    $method = 'down';
		$pending = array();
		$output_array = array();
    if ($version == "")
    {
      foreach ($migrations as $number => $file)
      {
        if (in_array($number,$old_migrations))
        {
          include_once($file);
          $class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

          // Validate the migration file structure
          if ( ! class_exists($class, FALSE))
          {
            $output_array[] = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
          }
          elseif ( ! is_callable(array($class, $method)))
          {
            $output_array[] = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
          }
          else
          {
            $pending[$number] = array($class, $method);
          }
        }
      }
    }
    else
    {
      if (array_key_exists($version,$migrations))
      {
        $number = $version;
        $file = $migrations[$number];
        if (!in_array($number,$old_migrations))
        {
          include_once($file);
          $class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

          // Validate the migration file structure
          if ( ! class_exists($class, FALSE))
          {
            $output_array[] = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
            //return FALSE;
          }
          elseif ( ! is_callable(array($class, $method)))
          {
            $output_array[] = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
          }
          else
          {
            $pending[$number] = array($class, $method);
          }
        }
      }
      else
      {
        $output_array[] =  sprintf($this->lang->line('migration_not_found'), $version);
      }
    }
    // Now just run the necessary migrations
    foreach ($pending as $number => $migration)
    {
      log_message('debug', 'Migrating '.$method.' from version '.$current_version.' to version '.$number);

      $migration[0] = new $migration[0];
      call_user_func($migration);
      $current_version = $number;
      $this->_del_version($current_version);
      $output_array[] =  'Version '.$number.' ran successfully!';
    }

		return $output_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the schema to the latest migration
	 *
	 * @return	mixed	Current version string on success, FALSE on failure
	 */
	public function latest()
	{
		$migrations = $this->find_migrations();

		if (empty($migrations))
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return FALSE;
		}

		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migration
		// filenames and proceed to the standard version migration
		return $this->version($this->_get_migration_number($last_migration));
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the schema to the migration version set in config
	 *
	 * @return	mixed	TRUE if no migrations are found, current version string on success, FALSE on failure
	 */
	public function current()
	{
		return $this->version($this->_migration_version);
	}

	// --------------------------------------------------------------------

	/**
	 * Error string
	 *
	 * @return	string	Error message returned as a string
	 */
	public function error_string()
	{
		return $this->_error_string;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves list of available migration scripts
	 *
	 * @return	array	list of migration file paths sorted by version
	 */
	public function find_migrations()
	{
		$migrations = array();
		// Load all *_*.php files in the migrations path
		foreach (glob($this->_migration_path.'*_*.php') as $file)
		{
			$name = basename($file, '.php');

      // Filter out non-migration files
			if (preg_match($this->_migration_regex, $name))
			{
				$number = $this->_get_migration_number($name);

				// There cannot be duplicate migration numbers
				if (isset($migrations[$number]))
				{
					$this->_error_string = sprintf($this->lang->line('migration_multiple_version'), $number);
					show_error($this->_error_string);
				}

				$migrations[$number] = $file;
			}
		}

		ksort($migrations);
		return $migrations;
	}

	// --------------------------------------------------------------------

	/**
	 * Extracts the migration number from a filename
	 *
	 * @param	string	$migration
	 * @return	string	Numeric portion of a migration filename
	 */
	protected function _get_migration_number($migration)
	{
		return sscanf($migration, '%[0-9]+', $number)
			? $number : '0';
	}

	// --------------------------------------------------------------------

	/**
	 * Extracts the migration class name from a filename
	 *
	 * @param	string	$migration
	 * @return	string	text portion of a migration filename
	 */
	protected function _get_migration_name($migration)
	{
		$parts = explode('_', $migration);
		array_shift($parts);
		return implode('_', $parts);
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @return	string	Current migration version
	 */
	protected function _get_version()
	{
		$row = $this->db->select('version')->get($this->_migration_table)->row();
		return $row ? $row->version : '0';
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @param	string	$migration	Migration reached
	 * @return	void
	 */
	protected function _update_version($migration)
	{
		$this->db->update($this->_migration_table, array(
			'version' => $migration
		));
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Insert the schema version
	 *
	 * @param	string	$migration	Migration reached
	 * @return	void
	 */
	protected function _add_version($migration)
	{
		$this->db->insert($this->_migration_table, array(
			'version' => $migration
		));
	}

	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * Delete the schema version
	 *
	 * @param	string	$migration	Migration reached
	 * @return	void
	 */
	protected function _del_version($migration)
	{
    $this->db->where("version",$migration);
		$this->db->delete($this->_migration_table);
	}

	// --------------------------------------------------------------------

	/**
	 * Enable the use of CI super-global
	 *
	 * @param	string	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}

}
