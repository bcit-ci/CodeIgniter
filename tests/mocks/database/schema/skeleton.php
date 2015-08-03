<?php

class Mock_Database_Schema_Skeleton {

	/**
	 * @var object Database Holder
	 */
	public static $db;

	/**
	 * @var object Forge Holder
	 */
	public static $forge;

	/**
	 * @var object Driver Holder
	 */
	public static $driver;

	/**
	 * Initialize both database and forge components
	 */
	public static function init($driver)
	{
		if (empty(self::$db) && empty(self::$forge))
		{
			// E_DEPRECATED notices thrown by mysql_connect(), mysql_pconnect()
			// on PHP 5.5+ cause the tests to fail
			if ($driver === 'mysql' && version_compare(PHP_VERSION, '5.5', '>='))
			{
				error_reporting(E_ALL & ~E_DEPRECATED);
			}

			$config = Mock_Database_DB::config($driver);
			$connection = new Mock_Database_DB($config);
			$db = Mock_Database_DB::DB($connection->set_dsn($driver), TRUE);

			CI_TestCase::instance()->ci_instance_var('db', $db);

			$loader = new CI_Loader();
			$loader->dbforge();
			$forge = CI_TestCase::instance()->ci_instance_var('dbforge');

			self::$db = $db;
			self::$forge = $forge;
			self::$driver = $driver;
		}

		return self::$db;
	}

	/**
	 * Create the dummy tables
	 *
	 * @return void
	 */
	public static function create_tables()
	{
		// User Table
		self::$forge->add_field(array(
			'id' => array(
				'type' => 'INTEGER',
				'constraint' => 3
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 40
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => 100
			),
			'country' => array(
				'type' => 'VARCHAR',
				'constraint' => 40
			)
		));
		self::$forge->add_key('id', TRUE);
		self::$forge->create_table('user', TRUE);

		// Job Table
		self::$forge->add_field(array(
			'id' => array(
				'type' => 'INTEGER',
				'constraint' => 3
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 40
			),
			'description' => array(
				'type' => 'TEXT'
			)
		));
		self::$forge->add_key('id', TRUE);
		self::$forge->create_table('job', TRUE);

		// Misc Table
		self::$forge->add_field(array(
			'id' => array(
				'type' => 'INTEGER',
				'constraint' => 3
			),
			'key' => array(
				'type' => 'VARCHAR',
				'constraint' => 40
			),
			'value' => array(
				'type' => 'TEXT'
			)
		));
		self::$forge->add_key('id', TRUE);
		self::$forge->create_table('misc', TRUE);
	}

	/**
	 * Create the dummy datas
	 *
	 * @return void
	 */
	public static function create_data()
	{
		// Job Data
		$data = array(
			'user' => array(
				array('id' => 1, 'name' => 'Derek Jones', 'email' => 'derek@world.com', 'country' => 'US'),
				array('id' => 2, 'name' => 'Ahmadinejad', 'email' => 'ahmadinejad@world.com', 'country' => 'Iran'),
				array('id' => 3, 'name' => 'Richard A Causey', 'email' => 'richard@world.com', 'country' => 'US'),
				array('id' => 4, 'name' => 'Chris Martin', 'email' => 'chris@world.com', 'country' => 'UK')
			),
			'job' => array(
				array('id' => 1, 'name' => 'Developer', 'description' => 'Awesome job, but sometimes makes you bored'),
				array('id' => 2, 'name' => 'Politician', 'description' => 'This is not really a job'),
				array('id' => 3, 'name' => 'Accountant', 'description' => 'Boring job, but you will get free snack at lunch'),
				array('id' => 4, 'name' => 'Musician', 'description' => 'Only Coldplay can actually called Musician')
			),
			'misc' => array(
				array('id' => 1, 'key' => '\\xxxfoo456', 'value' => 'Entry with \\xxx'),
				array('id' => 2, 'key' => '\\%foo456', 'value' => 'Entry with \\%'),
				array('id' => 3, 'key' => 'spaces and tabs', 'value' => ' One  two   three	tab')
			)
		);

		foreach ($data as $table => $dummy_data)
		{
			self::$db->truncate($table);

			foreach ($dummy_data as $single_dummy_data)
			{
				self::$db->insert($table, $single_dummy_data);
			}
		}
	}

}
