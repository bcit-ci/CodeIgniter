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

	/**
	 * @see ./mocks/schema/skeleton.php
	 */
	public function test_delete_join()
	{
		// Dummy join to delete politicians :D
		$this->db->where('job.name', 'Politician')
		->join('job', 'job.userId = user.id')
		->delete('user');

		$politicians = $this->db->where('id', 2)->get('user')->result_array();
		$this->assertEmpty($politicians);
	}

	public function test_delete_join_where(){		
		$this->db->join('job', 'job.userId = user.id')
		->where('job.name', 'Politician');

		$this->db->join('misc', 'misc.userId = user.id')		
		->where('misc.value', 'join1');

		$this->db->where('user.email', 'richard@world.com')
		->delete('user');
		
		$politician = $this->db->where('id', 2)->get('user');
		$this->assertNotEmpty($politician->result_array());


		$this->db->join('job', 'job.userId = user.id')
		->where('job.name', 'Politician');
		
		$this->db->join('misc', 'misc.userId = user.id')
		->where('misc.value', 'join1')
		->delete('user');

		$politician = $this->db->where('id', 2)->get('user');
		$this->assertEmpty($politician->result_array());
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
