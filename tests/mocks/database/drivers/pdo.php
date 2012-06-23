<?php

class Mock_Database_Drivers_PDO extends Mock_Database_DB_Driver {

	/**
	 * Instantiate the database driver
	 *
	 * @param	string	DB Driver class name
	 * @param	array	DB configuration to set
	 * @return	void
	 */
	public function __construct($config = array())
	{
		if ( ! empty($config))
		{
			$class = 'CI_'.$config['pdodriver'].'_PDO_driver';
		}
	
		parent::__construct($class, $config);
	}
}