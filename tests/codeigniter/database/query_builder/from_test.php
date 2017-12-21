<?php

class From_test extends CI_TestCase {

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
	public function test_from_simple()
	{
		$jobs = $this->db->from('job')
					->get()
					->result_array();

		$this->assertCount(4, $jobs);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_from_with_where()
	{
		$job1 = $this->db->from('job')
					->where('id', 1)
					->get()
					->row();

		$this->assertEquals('1', $job1->id);
		$this->assertEquals('Developer', $job1->name);
		$this->assertEquals('Awesome job, but sometimes makes you bored', $job1->description);
	}

}
