<?php

class Mock_Database_DB {

	/**
	 * @var array DB configuration
	 */
	private $config = array();

	/**
	 * @var string DB driver name
	 */
	private static $dbdriver = '';

	/**
	 * @var string DB sub-driver name
	 */
	private static $subdriver = '';

	/**
	 * Prepare database configuration skeleton
	 *
	 * @param  array 	DB configuration to set
	 * @return void
	 */
	public function __construct($config = array())
	{
		$this->config = $config;
	}

	/**
	 * Build DSN connection string for DB driver instantiate process
	 *
	 * @param 	string 	Group name
	 * @return 	string 	DSN Connection string
	 */
	public function set_dsn($group = 'default')
	{
		if ( ! isset($this->config[$group]))
		{
			throw new InvalidArgumentException('Group '.$group.' not exists');
		}

		self::$dbdriver = $this->config[$group]['dbdriver'];
		if (isset($this->config[$group]['subdriver']))
		{
			self::$subdriver = $this->config[$group]['subdriver'];
		}

		$params = array(
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => FALSE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'autoinit' => TRUE,
			'stricton' => FALSE,
		);

		$config = array_merge($this->config[$group], $params);
		$dsnstring = empty($config['dsn']) ? FALSE : $config['dsn'];
		$subdriver = empty($config['subdriver']) ? FALSE: $config['subdriver'];
		$failover = empty($config['failover']) ? FALSE : $config['failover'];

		$dsn = $config['dbdriver'].'://'.$config['username'].':'.$config['password']
					.'@'.$config['hostname'].'/'.$config['database'];

		// Build the parameter
		$other_params = array_slice($config, 6);
		if ($dsnstring) $other_params['dsn'] = $dsnstring;
		if ($subdriver) $other_params['subdriver'] = $subdriver;
		if ($failover) $other_params['failover'] = $failover;

		return $dsn.'?'.http_build_query($other_params);
	}

	/**
	 * Return a database config array
	 *
	 * @see 	./config
	 * @param	string	Driver based configuration
	 * @return	array
	 */
	public static function config($driver)
	{
		$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
		return include($dir.'config'.DIRECTORY_SEPARATOR.$driver.'.php');
	}

	/**
	 * Main DB method wrapper
	 *
	 * @param 	string	Group or DSN string
	 * @param 	bool
	 * @return 	object
	 */
	public static function DB($group, $query_builder = FALSE)
	{
		// Create dummy driver and builder files to "load" - the mocks have
		// already triggered autoloading of the real files
		$case = CI_TestCase::instance();
		$driver = self::$dbdriver;
		$subdriver = self::$subdriver;
		$case->ci_vfs_create(array(
			'DB_driver.php' => '',
			'DB_result.php' => '',
			'DB_forge.php' => '',
			'DB_query_builder.php' => ''
		), '', $case->ci_base_root, 'database');
		if (file_exists(SYSTEM_PATH.'database/drivers/'.$driver.'/'.$driver.'_driver.php'))
		{
			$case->ci_vfs_create(array(
				$driver.'_driver.php' => '',
				$driver.'_result.php' => '',
				$driver.'_forge.php' => ''
			), '', $case->ci_base_root, 'database/drivers/'.$driver);
		}
		if ($subdriver)
		{
			$case->ci_vfs_create(array(
				$driver.'_'.$subdriver.'_driver.php' => '',
				$driver.'_'.$subdriver.'_forge.php' => ''
			), '', $case->ci_base_root, 'database/drivers/'.$driver.'/subdrivers');
		}

		include_once(SYSTEM_PATH.'database/DB.php');

		try
		{
			$db = DB($group, $query_builder);
		}
		catch (Exception $e)
		{
			throw new InvalidArgumentException($e->getMessage());
		}

		return $db;
	}

}