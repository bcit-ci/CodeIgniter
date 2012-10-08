################
Migrations Class
################

Migrations are a convenient way for you to alter your database in a 
structured and organized manner. You could edit fragments of SQL by hand 
but you would then be responsible for telling other developers that they 
need to go and run them. You would also have to keep track of which changes 
need to be run against the production machines next time you deploy.

The database table **migration** tracks which migrations have already been 
run so all you have to do is update your application files and 
call **$this->migrate->current()** to work out which migrations should be run. 
The current version is found in **config/migration.php**.

******************
Create a Migration
******************

.. note:: Each Migration is run in numerical order forward or backwards 
	depending on the method taken. Use a prefix of 3 numbers followed by an 
	underscore for the filename of your migration.
	
This will be the first migration for a new site which has a blog. All 
migrations go in the folder **application/migrations/** and have names such 
as: **001_add_blog.php**.::

	defined('BASEPATH') OR exit('No direct script access allowed');

	class Migration_Add_blog extends CI_Migration {

		public function up()
		{
			$this->dbforge->add_field(array(
				'blog_id' => array(
					'type' => 'INT',
					'constraint' => 5,
					'unsigned' => TRUE,
					'auto_increment' => TRUE
				),
				'blog_title' => array(
					'type' => 'VARCHAR',
					'constraint' => '100',
				),
				'blog_description' => array(
					'type' => 'TEXT',
					'null' => TRUE,
				),
			));
			
			$this->dbforge->create_table('blog');
		}

		public function down()
		{
			$this->dbforge->drop_table('blog');
		}

Then in **application/config/migration.php** set **$config['migration_version'] = 1;**.

*************
Usage Example
*************

In this example some simple code is placed in **application/controllers/migrate.php** 
to update the schema.::

	$this->load->library('migration');

	if ( ! $this->migration->current())
	{
		show_error($this->migration->error_string());
	}

******************
Function Reference
******************

There are five available methods for the Migration class:

-  $this->migration->current();
-  $this->migration->error_string();
-  $this->migration->find_migrations();
-  $this->migration->latest();
-  $this->migration->version();

$this->migration->current()
============================

The current migration is whatever is set for **$config['migration_version']** in 
**application/config/migration.php**.

$this->migration->error_string()
=================================

This returns a string of errors while performing a migration.

$this->migration->find_migrations()
====================================

An array of migration filenames are returned that are found in the **migration_path** 
property.

$this->migration->latest()
===========================

This works much the same way as current() but instead of looking for 
the **$config['migration_version']** the Migration class will use the very 
newest migration found in the filesystem.

$this->migration->version()
============================

Version can be used to roll back changes or step forwards programmatically to 
specific versions. It works just like current but ignores **$config['migration_version']**.::

	$this->load->library('migration');

	$this->migration->version(5);

*********************
Migration Preferences
*********************

The following is a table of all the config options for migrations.

========================== ====================== ============= =============================================
Preference                 Default                Options       Description
========================== ====================== ============= =============================================
**migration_enabled**      FALSE                  TRUE / FALSE  Enable or disable migrations.
**migration_path**         APPPATH.'migrations/'  None          The path to your migrations folder.
**migration_version**      0                      None          The current version your database should use.
**migration_table**        migrations             None          The table name for storing the shema
                                                                version number.
**migration_auto_latest**  FALSE                  TRUE / FALSE  Enable or disable automatically 
                                                                running migrations.
========================== ====================== ============= =============================================
