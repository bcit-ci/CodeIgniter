<?php

class Mock_Database_DB {

	private $config = array();
	
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

	public function set_dsn($group = 'default')
	{
		if ( ! isset($this->config[$group]))
		{
			throw new InvalidArgumentException('Group '.$group.' not exists');
		}

		$params = array(
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => TRUE,
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'autoinit' => TRUE,
			'stricton' => FALSE,
			'failover' => array()
		);

		$config = array_merge($this->config[$group], $params);

		if ( ! empty($config['dsn']))
		{
			$dsn = $config['dsn'];
		}
		else
		{
			$dsn = $config['dbdriver'].'://'.$config['username'].':'.$config['password']
			       .'@'.$config['hostname'].'/'.$config['database'];

		}

		$other_params = array_slice($config, 6);

		return $dsn.http_build_query($other_params);
	}

	public static function DB($group, $query_builder = FALSE)
	{
		include_once(BASEPATH.'database/DB.php');

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