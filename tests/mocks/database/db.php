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
		include_once(BASEPATH.'database/DB.php');

		$this->config = $config;
	}

	public function set_config($group = 'default')
	{
		if ( ! isset($this->config[$group]))
		{
			throw new InvalidArgumentException('Group '.$group.' not exists');
		}

		if ( ! empty($this->config[$group]['dsn']))
		{
			$dsn = $this->config[$group]['dsn'];
		}
		else
		{
			$config = $this->config[$group];
			$dsn = $config['dbdriver'].'://'.$config['username'].':'.$config['password']
			       .'@'.$config['hostname'].'/'.$config['database'];

		}

		$params = array_slice($this->config[$group], 6);

		return $dsn.http_build_query($params);
	}
}