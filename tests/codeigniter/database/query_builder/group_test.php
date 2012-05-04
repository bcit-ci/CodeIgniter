<?php

class Group_test extends CI_TestCase {

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
	public function test_group_by()
	{
		$jobs = $this->db->select('job.name as job_name, job.id as job_id')
							  ->from('job')
							  ->group_by('job_name HAVING SUM(job_id) > 2')
		                      ->get()
		                      ->result_array();
		
		// Check the result
		$this->assertEquals(2, count($jobs));
		$this->assertEquals('Accountant', $jobs[0]['job_name']);
		$this->assertEquals('Musician', $jobs[1]['job_name']);
	}
	
}