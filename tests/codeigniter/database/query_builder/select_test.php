<?php

class Select_test extends CI_TestCase {

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
	public function test_select_only_one_collumn()
	{
		$jobs_name = $this->db->select('name')
		                      ->get('job')
		                      ->result_array();

		// Check rows item
		$this->assertArrayHasKey('name',$jobs_name[0]);
		$this->assertFalse(array_key_exists('id', $jobs_name[0]));
		$this->assertFalse(array_key_exists('description', $jobs_name[0]));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_select_min()
	{
		$job_min = $this->db->select_min('id')
		                    ->get('job')
		                    ->row();

		// Minimum id was 1
		$this->assertEquals('1', $job_min->id);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_select_max()
	{
		$job_max = $this->db->select_max('id')
		                    ->get('job')
		                    ->row();

		// Maximum id was 4
		$this->assertEquals('4', $job_max->id);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_select_avg()
	{
		$job_avg = $this->db->select_avg('id')
		                    ->get('job')
		                    ->row();

		// Average should be 2.5
		$this->assertEquals('2.5', $job_avg->id);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_select_sum()
	{
		$job_sum = $this->db->select_sum('id')
		                    ->get('job')
		                    ->row();

		// Sum of ids should be 10
		$this->assertEquals('10', $job_sum->id);
	}

}