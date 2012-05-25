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

		$this->pre = (strpos(DB_DRIVER, 'pgsql') === FALSE) ? '`' : '"';
		$this->esc = (strpos(DB_DRIVER, 'mysql') === FALSE) ? '!' : '';
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_escape_like_percent_sign()
	{
		$string = $this->db->escape_like_str('\%foo');

		$sql = "SELECT {$this->pre}value{$this->pre} FROM {$this->pre}misc{$this->pre} WHERE {$this->pre}key{$this->pre} LIKE '$string%' ESCAPE '$this->esc';";

		$res = $this->db->query($sql)->result_array();
		
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

		$sql = "SELECT {$this->pre}value{$this->pre} FROM {$this->pre}misc{$this->pre} WHERE {$this->pre}key{$this->pre} LIKE '$string%' ESCAPE '$this->esc';";

		$res = $this->db->query($sql)->result_array();
		
		// Check the result
		$this->assertEquals(2, count($res->result_array()));
	}

}