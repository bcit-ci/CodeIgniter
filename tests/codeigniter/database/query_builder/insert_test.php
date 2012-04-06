<?php

class Insert_test extends CI_TestCase {

	/**
	 * @var object Database/Query Builder holder
	 * @see ./mocks/schema/skeleton.php
	 */
	protected $db;

	public function set_up()
	{
		$this->db = Mock_Database_Schema_Skeleton::init(DB_DRIVER);

		Mock_Database_Schema_Skeleton::create_tables();

		// Truncate the current datas
		$this->db->truncate('job');
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_insert()
	{
		$job_data = array('id' => 1, 'name' => 'Grocery Sales', 'description' => 'Discount!');
		
		// Do normal insert
		$this->assertTrue($this->db->insert('job', $job_data));

		$job_1 = $this->db->get('job')->row();

		// Check the result
		$this->assertEquals('Grocery Sales', $job_1->name);

	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_insert_batch()
	{
		$job_datas = array(
			array('id' => 2, 'name' => 'Commedian', 'description' => 'Theres something in your teeth'), 
			array('id' => 3, 'name' => 'Cab Driver', 'description' => 'Iam yellow'),
		);
		
		// Do insert batch
		$this->assertTrue($this->db->insert_batch('job', $job_datas));

		$job_2 = $this->db->get_where('job', array('id' => 2))->row();

		// Check the result
		$this->assertEquals('Commedian', $job_2->name);
	}
	
}