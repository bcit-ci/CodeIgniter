<?php

class Escape_test extends CI_TestCase {

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
	public function test_escape_like_percent_sign()
	{
		$string = $this->db->escape_like_str('\%foo');
		$this->db->select('value');
		$this->db->from('misc');
		$this->db->like('key', $string, 'after');
		$res = $this->db->get();

		// Check the result
		$this->assertEquals(1, count($res->result_array()));
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_escape_like_backslash_sign()
	{
		$string = $this->db->escape_like_str('\\');
		$res = $this->db->select('value')->from('misc')->like('key', $string, 'after')->get();
		$this->db->select('value');
		$this->db->from('misc');
		$this->db->like('key', $string, 'after');
		$res = $this->db->get();

		// Check the result
		$this->assertEquals(2, count($res->result_array()));
	}

}