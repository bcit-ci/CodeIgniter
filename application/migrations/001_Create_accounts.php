<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_accounts extends	CI_Migration {
	
	function up() 
	{	
		if ( ! $this->db->table_exists('accounts'))
		{
			// Setup Keys
			$this->dbforge->add_key('id', TRUE);
			
			$this->dbforge->add_field(array(
				'id' => array('type' => 'INT', 'constraint' => 5, 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'company_name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'first_name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'last_name' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => FALSE),
				'phone' => array('type' => 'TEXT', 'null' => FALSE),
				'email' => array('type' => 'TEXT', 'null' => FALSE),
				'address' => array('type' => 'TEXT', 'null' => FALSE),
				'Last_Update' => array('type' => 'DATETIME', 'null' => FALSE)
			));
			
			$this->dbforge->add_field("Created_At TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
			$this->dbforge->create_table('accounts', TRUE);
		}
	}

	function down() 
	{
		$this->dbforge->drop_table('accounts');
	}
}
