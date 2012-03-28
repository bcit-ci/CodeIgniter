<?php

class DB_test extends CI_TestCase {

	public $db_config;

	public function set_up()
	{
		$this->db_config = new Mock_Database_DB(array(
			'mysql' => array(
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'travis',
				'password' => '',
				'database' => 'ci_test',
				'dbdriver' => 'mysql',
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
				'failover' => array(),
			),
		));
	}

	// ------------------------------------------------------------------------

	public function test_db_valid()
	{
		$db = DB($this->db_config->set_config('mysql'), TRUE);

		$this->assertTrue($db instanceof CI_DB);
		$this->assertTrue($db instanceof CI_DB_Driver);
		$this->assertTrue($db instanceof CI_DB_active_record);
		$this->assertTrue($db instanceof CI_DB_mysql_driver);
	}
	
}