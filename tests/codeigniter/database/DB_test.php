<?php

class DB_test extends CI_TestCase {

	public function test_db_invalid()
	{
		$connection = new Mock_Database_DB(array(
			'undefined' => array(
				'dsn' => '',
				'hostname' => 'undefined',
				'username' => 'undefined',
				'password' => 'undefined',
				'database' => 'undefined',
				'dbdriver' => 'undefined',
			),
		));

		$this->setExpectedException('RuntimeException', 'CI Error: Invalid DB driver');

		Mock_Database_DB::DB($connection->set_dsn('undefined'), TRUE);
	}

	// ------------------------------------------------------------------------

	public function test_db_valid()
	{
		$config = Mock_Database_DB::config(DB_DRIVER);
		$connection = new Mock_Database_DB($config);

		// E_DEPRECATED notices thrown by mysql_connect(), mysql_pconnect()
		// on PHP 5.5+ cause the tests to fail
		if (DB_DRIVER === 'mysql' && version_compare(PHP_VERSION, '5.5', '>='))
		{
			error_reporting(E_ALL & ~E_DEPRECATED);
		}

		$db = Mock_Database_DB::DB($connection->set_dsn(DB_DRIVER), TRUE);

		$this->assertTrue($db instanceof CI_DB);
		$this->assertTrue($db instanceof CI_DB_Driver);
	}

	// ------------------------------------------------------------------------

/*
	This test is unusable, because whoever wrote it apparently thought that
	an E_WARNING should equal an Exception and based the whole test suite
	around that bogus assumption.

	public function test_db_failover()
	{
		$config = Mock_Database_DB::config(DB_DRIVER);
		$connection = new Mock_Database_DB($config);
		$db = Mock_Database_DB::DB($connection->set_dsn(DB_DRIVER.'_failover'), TRUE);

		$this->assertTrue($db instanceof CI_DB);
		$this->assertTrue($db instanceof CI_DB_Driver);
	}
*/

}