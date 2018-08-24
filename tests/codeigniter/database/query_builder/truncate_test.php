<?php

class Truncate_test extends CI_TestCase {

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
	public function test_truncate()
	{
		// Check initial record
		$jobs = $this->db->get('job')->result_array();
		$this->assertCount(4, $jobs);

		// Do the empty
		$this->db->truncate('job');

		// Check the record
		$jobs = $this->db->get('job');
		$this->assertEmpty($jobs->result_array());
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_truncate_with_from()
	{
		// Check initial record
		$users = $this->db->get('user')->result_array();
		$this->assertCount(4, $users);

		// Do the empty
		$this->db->from('user')->truncate();

		// Check the record
		$users = $this->db->get('user');
		$this->assertEmpty($users->result_array());
	}

}
