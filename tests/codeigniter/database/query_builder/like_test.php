<?php

class Like_test extends CI_TestCase {

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
	public function test_like()
	{
		$job1 = $this->db->like('name', 'veloper')
							->get('job')
							->row();

		// Check the result
		$this->assertEquals('1', $job1->id);
		$this->assertEquals('Developer', $job1->name);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_or_like()
	{
		$jobs = $this->db->like('name', 'ian')
							->or_like('name', 'veloper')
							->get('job')
							->result_array();

		// Check the result
		$this->assertCount(3, $jobs);
		$this->assertEquals('Developer', $jobs[0]['name']);
		$this->assertEquals('Politician', $jobs[1]['name']);
		$this->assertEquals('Musician', $jobs[2]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_not_like()
	{
		$jobs = $this->db->not_like('name', 'veloper')
							->get('job')
							->result_array();

		// Check the result
		$this->assertCount(3, $jobs);
		$this->assertEquals('Politician', $jobs[0]['name']);
		$this->assertEquals('Accountant', $jobs[1]['name']);
		$this->assertEquals('Musician', $jobs[2]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_or_not_like()
	{
		$jobs = $this->db->like('name', 'an')
							->or_not_like('name', 'veloper')
							->get('job')
							->result_array();

		// Check the result
		$this->assertCount(3, $jobs);
		$this->assertEquals('Politician', $jobs[0]['name']);
		$this->assertEquals('Accountant', $jobs[1]['name']);
		$this->assertEquals('Musician', $jobs[2]['name']);
	}

	// ------------------------------------------------------------------------

	/**
	 * GitHub issue #273
	 *
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_like_spaces_and_tabs()
	{
		$spaces = $this->db->like('value', '   ')->get('misc')->result_array();
		$tabs = $this->db->like('value', "\t")->get('misc')->result_array();

		$this->assertCount(1, $spaces);
		$this->assertCount(1, $tabs);
	}

	/**
	 * GitHub issue #5462
	 *
	 * @see ./mocks/schema/skeleton.php
	 *
	 * @dataProvider like_set_side_provider
	 */
	public function test_like_set_side($str, $side, $expected_name)
	{
		$actual = $this->db->like('name', $str, $side)->get('job')->result_array();
		$this->assertCount(1, $actual);
		$this->assertEquals($expected_name, $actual[0]['name']);
	}

	public function like_set_side_provider()
	{
		return array(
			array('Developer', 'none', 'Developer'),
			array('tician', 'before', 'Politician'),
			array('Accou', 'after', 'Accountant'),
			array('usicia', 'both', 'Musician'),
		);
	}
}
