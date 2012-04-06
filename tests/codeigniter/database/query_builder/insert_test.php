<?php

class Insert_test extends CI_TestCase {

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
	public function test_insert()
	{
		$job_data = array('name' => 'Grocery Sales', 'description' => 'Discount!');
		
		// Do normal insert
		$this->assertTrue($this->db->insert('job', $job_data));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_insert_batch()
	{
		$job_datas = array(
			array('name' => 'Commedian', 'description' => 'Theres something in your teeth'), 
			array('name' => 'Cab Driver', 'description' => 'Iam yellow'),
		);
		
		// Do insert batch
		$this->assertTrue($this->db->insert_batch('job', $job_datas));
	}
	
}