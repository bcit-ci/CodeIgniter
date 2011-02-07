<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2006 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

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

	private $_migration_enabled = FALSE;
	private $_migration_path = NULL;
	private $_migration_version = 0;

	public $error = '';

	function __construct($config = array())
	{
		# Only run this constructor on main library load
		if (get_parent_class($this) !== FALSE)
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_' . $key} = $val;
		}

		log_message('debug', 'Migrations class initialized');

		// Are they trying to use migrations while it is disabled?
		if ($this->_migration_enabled !== TRUE)
		{
			show_error('Migrations has been loaded but is disabled or set up incorrectly.');
		}

		// If not set, set it
		$this->_migration_path == '' OR $this->_migration_path = APPPATH . 'migrations/';

		// Add trailing slash if not set
		$this->_migration_path = rtrim($this->_migration_path, '/').'/';

		// They'll probably be using dbforge
		$this->load->dbforge();

		// If the migrations table is missing, make it
		if ( ! $this->db->table_exists('migrations'))
		{
			$this->dbforge->add_field(array(
				'version' => array('type' => 'INT', 'constraint' => 3),
			));

			$this->dbforge->create_table('migrations', TRUE);

			$this->db->insert('migrations', array('version' => 0));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 *
	 * @access	public
	 * @param $version integer	Target schema version
	 * @return	mixed	TRUE if already latest, FALSE if failed, int if upgraded
	 */
	function version($target_version)
	{
		$start = $current_version = $this->_get_version();
		$stop = $target_version;

		if ($target_version > $current_version)
		{
			// Moving Up
			++$start;
			++$stop;
			$step = 1;
		}

		else
		{
			// Moving Down
			$step = -1;
		}

		$method = $step === 1 ? 'up' : 'down';
		$migrations = array();

		// We now prepare to actually DO the migrations
		// But first let's make sure that everything is the way it should be
		for ($i = $start; $i != $stop; $i += $step)
		{
			$f = glob(sprintf($this->_migration_path . '%03d_*.php', $i));

			// Only one migration per step is permitted
			if (count($f) > 1)
			{
				$this->error = sprintf($this->lang->line('multiple_migration_version'), $i);
				return FALSE;
			}

			// Migration step not found
			if (count($f) == 0)
			{
				// If trying to migrate up to a version greater than the last
				// existing one, migrate to the last one.
				if ($step == 1)
				{
					break;
				}

				// If trying to migrate down but we're missing a step,
				// something must definitely be wrong.
				$this->error = sprintf($this->lang->line('migration_not_found'), $i);
				return FALSE;
			}

			$file = basename($f[0]);
			$name = basename($f[0], '.php');

			// Filename validations
			if (preg_match('/^\d{3}_(\w+)$/', $name, $match))
			{
				$match[1] = strtolower($match[1]);

				// Cannot repeat a migration at different steps
				if (in_array($match[1], $migrations))
				{
					$this->error = sprintf($this->lang->line('multiple_migrations_name'), $match[1]);
					return FALSE;
				}

				include $f[0];
				$class = 'Migration_' . ucfirst($match[1]);

				if ( ! class_exists($class))
				{
					$this->error = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
					return FALSE;
				}

				if ( ! is_callable(array($class, 'up')) || ! is_callable(array($class, 'down')))
				{
					$this->error = sprintf($this->lang->line('wrong_migration_interface'), $class);
					return FALSE;
				}

				$migrations[] = $match[1];
			}
			else
			{
				$this->error = sprintf($this->lang->line('invalid_migration_filename'), $file);
				return FALSE;
			}
		}

		$this->log('Current schema version: ' . $current_version);

		$version = $i + ($step == 1 ? -1 : 0);

		// If there is nothing to do so quit
		if ($migrations === array())
		{
			return TRUE;
		}

		$this->log('Moving ' . $method . ' to version ' . $version);

		// Loop through the migrations
		foreach ($migrations AS $migration)
		{
			// Run the migration class
			$class = 'Migration_' . ucfirst(strtolower($migration));
			call_user_func(array(new $class, $method));

			$current_version += $step;
			$this->_update_version($current_version);
		}

		$this->log('All done. Schema is at version '.$current_version);

		return $current_version;
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the latest migration
	 *
	 * @access	public
	 * @return	mixed	true if already latest, false if failed, int if upgraded
	 */
	public function latest()
	{
		if ( ! $migrations = $this->find_migrations())
		{
			throw new Exception('no_migrations_found');
			return false;
		}

		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migration
		// filenames and procceed to the standard version migration
		$last_version = intval(substr($last_migration, 0, 3));
		return $this->version($last_version);
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the migration version set in config
	 *
	 * @access	public
	 * @return	mixed	true if already current, false if failed, int if upgraded
	 */
	public function current()
	{
		$version = $this->_migration_version;
		return $this->version($version);
	}

	// --------------------------------------------------------------------

	/**
	 * Set's the schema to the latest migration
	 *
	 * @access	public
	 * @return	mixed	true if already latest, false if failed, int if upgraded
	 */

	protected static function find_migrations()
	{
		// Load all *_*.php files in the migrations path
		$files = glob($this->_migration_path . '*_*.php');
		$file_count = count($files);

		for ($i = 0; $i < $file_count; $i++)
		{
			// Mark wrongly formatted files as false for later filtering
			$name = basename($files[$i], '.php');
			if ( ! preg_match('/^\d{3}_(\w+)$/', $name))
			{
				$files[$i] = FALSE;
			}
		}

		sort($files);

		return $files;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @access	private
	 * @return	integer	Current Schema version
	 */
	private function _get_version()
	{
		$row = $this->db->get('migrations')->row();
		return $row ? $row->version : 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @access	private
	 * @param $migrations integer	Schema version reached
	 * @return	void					Outputs a report of the migration
	 */
	private function _update_version($migrations)
	{
		return $this->db->update('migrations', array(
			'version' => $migrations
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @access	private
	 * @param $migrations integer	Schema version reached
	 * @return	void					Outputs a report of the migration
	 */
	private function log($text)
	{
		echo $text.'<br/>';
	}

	// --------------------------------------------------------------------

	/**
	 * Enable the use of CI super-global
	 *
	 * @access	public
	 * @param $var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}
}