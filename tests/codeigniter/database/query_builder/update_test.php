<?php

class Update_test extends CI_TestCase {

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
	public function test_update()
	{
		// Check initial record
		$job1 = $this->db->where('id', 1)->get('job')->row();
		$this->assertEquals('Developer', $job1->name);

		// Do the update
		$this->db->where('id', 1)->update('job', array('name' => 'Programmer'));

		// Check updated record
		$job1 = $this->db->where('id', 1)->get('job')->row();
		$this->assertEquals('Programmer', $job1->name);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_update_with_set()
	{
		// Check initial record
		$job1 = $this->db->where('id', 4)->get('job')->row();
		$this->assertEquals('Musician', $job1->name);

		// Do the update
		$this->db->set('name', 'Vocalist');
		$this->db->update('job', NULL, 'id = 4');

		// Check updated record
		$job1 = $this->db->where('id', 4)->get('job')->row();
		$this->assertEquals('Vocalist', $job1->name);
	}

}