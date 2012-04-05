<?php

class Select_test extends CI_TestCase {

	/**
	 * @var object Database/Query Builder holder
	 */
	protected $query_builder;

	public function set_up()
	{
		$db = Mock_Database_Schema_Skeleton::init(DB_DRIVER);

		Mock_Database_Schema_Skeleton::create_tables();
		Mock_Database_Schema_Skeleton::create_data();

		$this->query_builder = $db;
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_select_only_one_collumn()
	{
		$jobs_name = $this->query_builder->select('name')
		                                  ->get('job')
		                                  ->result_array();
		
		// Check rows item
		$this->assertArrayHasKey('name',$jobs_name[0]);
		$this->assertFalse(array_key_exists('id', $jobs_name[0]));
		$this->assertFalse(array_key_exists('description', $jobs_name[0]));
	}
	
}