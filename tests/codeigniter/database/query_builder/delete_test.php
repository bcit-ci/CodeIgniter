<?php

class Delete_test extends CI_TestCase {

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
	public function test_delete()
	{
		// Check initial record
		$job1 = $this->db->where('id', 1)->get('job')->row();

		$this->assertEquals('Developer', $job1->name);

		// Do the delete
		$this->db->delete('job', array('id' => 1));

		// Check the record
		$job1 = $this->db->where('id', 1)->get('job');

		$this->assertEmpty($job1->result_array());
	}

	public function test_delete_join(){		
		$this->db->join('user', 'user.id = device.userId')->where('user.name', 'Derek Jones');
		$this->db->where('device.uuid', 'e0101111d38bde8e6740011221af335301010333')->delete('device');
		
		$device = $this->db->where('uuid', 'e0101111d38bde8e6740011221af335301010333')->get('device');
		$this->assertNotEmpty($device->result_array());

		$this->db->join('user', 'user.id = device.userId')->where('user.name', 'Derek Jones');
		$this->db->where('device.name', 'iPhone 5')->delete('device');
		
		$device = $this->db->where('userId', 1)->get('device');
		$this->assertEquals(count($device->result_array()), 1);
	}

	// ------------------------------------------------------------------------

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_delete_several_tables()
	{
		// Check initial record
		$user4 = $this->db->where('id', 4)->get('user')->row();
		$job4 = $this->db->where('id', 4)->get('job')->row();

		$this->assertEquals('Musician', $job4->name);
		$this->assertEquals('Chris Martin', $user4->name);

		// Do the delete
		$this->db->delete(array('job', 'user'), array('id' => 4));

		// Check the record
		$job4 = $this->db->where('id', 4)->get('job');
		$user4 = $this->db->where('id', 4)->get('user');

		$this->assertEmpty($job4->result_array());
		$this->assertEmpty($user4->result_array());
	}

}
