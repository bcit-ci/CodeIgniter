<?php

class Join_test extends CI_TestCase {

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
	public function test_join_simple()
	{
		$job_user = $this->db->select('job.id as job_id, job.name as job_name, user.id as user_id, user.name as user_name')
							->from('job')
							->join('user', 'user.id = job.id')
							->get()
							->result_array();

		// Check the result
		$this->assertEquals('1', $job_user[0]['job_id']);
		$this->assertEquals('1', $job_user[0]['user_id']);
		$this->assertEquals('Derek Jones', $job_user[0]['user_name']);
		$this->assertEquals('Developer', $job_user[0]['job_name']);
	}

	// ------------------------------------------------------------------------

	public function test_join_escape_multiple_conditions()
	{
		// We just need a valid query produced, not one that makes sense
		$fields = array($this->db->protect_identifiers('table1.field1'), $this->db->protect_identifiers('table2.field2'));

		$expected = 'SELECT '.implode(', ', $fields)
				."\nFROM ".$this->db->escape_identifiers('table1')
				."\nLEFT JOIN ".$this->db->escape_identifiers('table2').' ON '.implode(' = ', $fields)
				.' AND '.$fields[0]." = 'foo' AND ".$fields[1].' = 0';

		$result = $this->db->select('table1.field1, table2.field2')
				->from('table1')
				->join('table2', "table1.field1 = table2.field2 AND table1.field1 = 'foo' AND table2.field2 = 0", 'LEFT')
				->get_compiled_select();

		$this->assertEquals($expected, $result);
	}

}