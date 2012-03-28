<?php

class DB_test extends CI_TestCase {

	// ------------------------------------------------------------------------

	public function test_db_invalid()
	{
		$db_config = new Mock_Database_DB(array(
			'undefined' => array(
				'dsn' => '',
				'hostname' => 'undefined',
				'username' => 'undefined',
				'password' => 'undefined',
				'database' => 'undefined',
				'dbdriver' => 'undefined',
			),
		));

		$this->setExpectedException('InvalidArgumentException', 'CI Error: Invalid DB driver');

		Mock_Database_DB::DB($db_config->set_dsn('undefined'), TRUE);
	}

	// ------------------------------------------------------------------------

	public function test_db_valid()
	{
		$db_config = new Mock_Database_DB(array(
			'mysql' => array(
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'travis',
				'password' => '',
				'database' => 'ci_test',
				'dbdriver' => 'mysql',
			),
		));

		$db = Mock_Database_DB::DB($db_config->set_dsn('mysql'), TRUE);

		$this->assertTrue($db instanceof CI_DB);
		$this->assertTrue($db instanceof CI_DB_Driver);
		$this->assertTrue($db instanceof CI_DB_mysql_driver);
	}
	
}