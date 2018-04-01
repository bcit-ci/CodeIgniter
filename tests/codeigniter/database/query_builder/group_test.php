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
		$jobs = $this->db->select('name')
					->from('job')
					->group_by('name')
					->get()
					->result_array();

		$this->assertEquals(4, count($jobs));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_having_by()
	{
		$jobs = $this->db->select('name')
					->from('job')
					->group_by('name')
					->having('SUM(id) > 2')
					->get()
					->result_array();

		$this->assertEquals(2, count($jobs));
	}

}