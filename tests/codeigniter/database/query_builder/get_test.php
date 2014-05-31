<?php

class Get_test extends CI_TestCase {

	/**
	 * @var object Database/Query Builder holder
	 */
	protected $db;

	public function set_up()
	{
		$this->db = Mock_Database_Schema_Skeleton::init(DB_DRIVER);

		Mock_Database_Schema_Skeleton::create_tables();
		Mock_Database_Schema_Skeleton::create_data();
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_get_simple()
	{
		$jobs = $this->db->get('job')->result_array();

		// Dummy jobs contain 4 rows
		$this->assertCount(4, $jobs);

		// Check rows item
		$this->assertEquals('Developer', $jobs[0]['name']);
		$this->assertEquals('Politician', $jobs[1]['name']);
		$this->assertEquals('Accountant', $jobs[2]['name']);
		$this->assertEquals('Musician', $jobs[3]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_get_where()
	{
		$job1 = $this->db->get_where('job', array('id' => 1))->result_array();

		// Dummy jobs contain 1 rows
		$this->assertCount(1, $job1);

		// Check rows item
		$this->assertEquals('Developer', $job1[0]['name']);
	}

}