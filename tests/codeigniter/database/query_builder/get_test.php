<?php

class Get_test extends CI_TestCase {

	/**
	 * @var object Database/Query Builder holder
	 */
	protected $query_builder;

	public function set_up()
	{
		$config = Mock_Database_DB::config(DB_DRIVER);
		$connection = new Mock_Database_DB($config);
		$db = Mock_Database_DB::DB($connection->set_dsn(DB_DRIVER), TRUE);

		$this->ci_instance_var('db', $db);

		$loader = new Mock_Core_Loader();
		$loader->dbforge();

		$forge = $this->ci_instance->dbforge;

		Mock_Database_Schema_Skeleton::create_tables($forge);
		Mock_Database_Schema_Skeleton::create_data($db);

		$this->query_builder = $db;
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_get_simple()
	{
		$jobs = $this->query_builder->get('job')->result_array();
		
		// Dummy jobs contain 4 rows
		$this->assertCount(4, $jobs);

		// Check rows item
		$this->assertEquals('Developer', $jobs[0]['name']);
		$this->assertEquals('Politician', $jobs[1]['name']);
		$this->assertEquals('Accountant', $jobs[2]['name']);
		$this->assertEquals('Musician', $jobs[3]['name']);
	}
	
}