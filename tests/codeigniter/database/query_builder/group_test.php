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

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_having_in()
	{
		$jobs = $this->db->select('name')
			->from('job')
			->group_by('name')
			->having_in('SUM(id)', array(1, 2, 5))
			->get()
			->result_array();

		$this->assertEquals(2, count($jobs));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_or_having_in()
	{
		$jobs = $this->db->select('name')
			->from('job')
			->group_by('name')
			->or_having_in('SUM(id)', array(1, 5))
			->or_having_in('SUM(id)', array(2, 6))
			->get()
			->result_array();

		$this->assertEquals(2, count($jobs));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_having_not_in()
	{
		$jobs = $this->db->select('name')
			->from('job')
			->group_by('name')
			->having_not_in('SUM(id)', array(3, 6))
			->get()
			->result_array();

		$this->assertEquals(3, count($jobs));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_or_having_not_in()
	{
		$jobs = $this->db->select('name')
			->from('job')
			->group_by('name')
			->or_having_not_in('SUM(id)', array(1, 2, 3))
			->or_having_not_in('SUM(id)', array(1, 3, 4))
			->get()
			->result_array();

		$this->assertEquals(2, count($jobs));
	}
}
