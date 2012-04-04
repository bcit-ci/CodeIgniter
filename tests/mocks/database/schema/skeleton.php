<?php

class Mock_Database_Schema_Skeleton {
	
	/**
	 * Create the dummy tables
	 *
	 * @return void
	 */
	public static function create_tables($forge)
	{
		// Job Table
		$forge->add_field(array(
			'id' => array(
				'type' => 'INTEGER',
				'constraint' => 3,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 40,
			),
			'description' => array(
				'type' => 'TEXT',
			),
		));
		$forge->add_key('id', TRUE);
		$res = $forge->create_table('job');
		var_dump($res);
	}

	/**
	 * Create the dummy datas
	 *
	 * @return void
	 */
	public static function create_data($db)
	{
		// Job Data
		$data = array(
			'job' => array(
				array('id' => 1, 'name' => 'Developer', 'description' => 'Awesome job, but sometimes makes you bored'), 
				array('id' => 2, 'name' => 'Politician', 'description' => 'This is not really a job'),
    			array('id' => 3, 'name' => 'Accountant', 'description' => 'Boring job, but you will get free snack at lunch'),
			    array('id' => 4, 'name' => 'Musician', 'description' => 'Only Coldplay can actually called Musician'),
			),
		);

		foreach ($data as $table => $dummy_data) 
		{
			$db->truncate($table);

			foreach ($dummy_data as $single_dummy_data)
			{
				$db->insert($table, $single_dummy_data); 
			}
		}
	}
}