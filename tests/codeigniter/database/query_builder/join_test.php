<?php

class Join_test extends CI_TestCase {

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
	public function test_join_simple()
	{
		$job_user = $this->db->select('job.id as job_id, job.name as job_name, user.id as user_id, user.name as user_name')
							->from('job')
							->join('user', 'user.id = job.id')
							->get()
							->result_array();

		// Check the result
		$this->assertEquals('1', $job_user[0]['job_id']);
		$this->assertEquals('1', $job_user[0]['user_id']);
		$this->assertEquals('Derek Jones', $job_user[0]['user_name']);
		$this->assertEquals('Developer', $job_user[0]['job_name']);
	}
	
}