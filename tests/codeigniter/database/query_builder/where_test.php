<?php

class Where_test extends CI_TestCase {

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
	public function test_where_simple_key_value()
	{
		$job1 = $this->db->where('id', 1)->get('job')->row();

		$this->assertEquals('1', $job1->id);
		$this->assertEquals('Developer', $job1->name);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_custom_key_value()
	{
		$jobs = $this->db->where('id !=', 1)->get('job')->result_array();
		$this->assertEquals(3, count($jobs));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_associative_array()
	{
		$where = array('id >' => 2, 'name !=' => 'Accountant');
		$jobs = $this->db->where($where)->get('job')->result_array();

		$this->assertEquals(1, count($jobs));

		// Should be Musician
		$job = current($jobs);
		$this->assertEquals('Musician', $job['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_custom_string()
	{
		$where = "id > 2 AND name != 'Accountant'";
		$jobs = $this->db->where($where)->get('job')->result_array();

		$this->assertEquals(1, count($jobs));

		// Should be Musician
		$job = current($jobs);
		$this->assertEquals('Musician', $job['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_or()
	{
		$jobs = $this->db->where('name !=', 'Accountant')
							->or_where('id >', 3)
							->get('job')
							->result_array();

		$this->assertEquals(3, count($jobs));
		$this->assertEquals('Developer', $jobs[0]['name']);
		$this->assertEquals('Politician', $jobs[1]['name']);
		$this->assertEquals('Musician', $jobs[2]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_in()
	{
		$jobs = $this->db->where_in('name', array('Politician', 'Accountant'))
							->get('job')
							->result_array();

		$this->assertEquals(2, count($jobs));
		$this->assertEquals('Politician', $jobs[0]['name']);
		$this->assertEquals('Accountant', $jobs[1]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_where_not_in()
	{
		$jobs = $this->db->where_not_in('name', array('Politician', 'Accountant'))
							->get('job')
							->result_array();

		$this->assertEquals(2, count($jobs));
		$this->assertEquals('Developer', $jobs[0]['name']);
		$this->assertEquals('Musician', $jobs[1]['name']);
	}

	// ------------------------------------------------------------------------

	public function test_issue4093()
	{
		$input = 'bar and baz or qux';
		$sql = $this->db->where('foo', $input)->get_compiled_select('dummy');
		$this->assertEquals("'".$input."'", substr($sql, -20));
	}
}