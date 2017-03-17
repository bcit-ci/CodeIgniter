####################
Database Forge Class
####################

The Database Forge Class contains methods that help you manage your
database.

.. contents:: Table of Contents
    :depth: 3

****************************
Initializing the Forge Class
****************************

.. important:: In order to initialize the Forge class, your database
	driver must already be running, since the forge class relies on it.

Load the Forge Class as follows::

	$this->load->dbforge()

You can also pass another database object to the DB Forge loader, in case
the database you want to manage isn't the default one::

	$this->myforge = $this->load->dbforge($this->other_db, TRUE);

In the above example, we're passing a custom database object as the first
parameter and then tell it to return the dbforge object, instead of
assigning it directly to ``$this->dbforge``.

.. note:: Both of the parameters can be used individually, just pass an empty
	value as the first one if you wish to skip it.

Once initialized you will access the methods using the ``$this->dbforge``
object::

	$this->dbforge->some_method();

*******************************
Creating and Dropping Databases
*******************************

**$this->dbforge->create_database('db_name')**

Permits you to create the database specified in the first parameter.
Returns TRUE/FALSE based on success or failure::

	if ($this->dbforge->create_database('my_db'))
	{
		echo 'Database created!';
	}

**$this->dbforge->drop_database('db_name')**

Permits you to drop the database specified in the first parameter.
Returns TRUE/FALSE based on success or failure::

	if ($this->dbforge->drop_database('my_db'))
	{
		echo 'Database deleted!';
	}


****************************
Creating and Dropping Tables
****************************

There are several things you may wish to do when creating tables. Add
fields, add keys to the table, alter columns. CodeIgniter provides a
mechanism for this.

Adding fields
=============

Fields are created via an associative array. Within the array you must
include a 'type' key that relates to the datatype of the field. For
example, INT, VARCHAR, TEXT, etc. Many datatypes (for example VARCHAR)
also require a 'constraint' key.

::

	$fields = array(
		'users' => array(
			'type' => 'VARCHAR',
			'constraint' => '100',
		),
	);
	// will translate to "users VARCHAR(100)" when the field is added.


Additionally, the following key/values can be used:

-  unsigned/true : to generate "UNSIGNED" in the field definition.
-  default/value : to generate a default value in the field definition.
-  null/true : to generate "NULL" in the field definition. Without this,
   the field will default to "NOT NULL".
-  auto_increment/true : generates an auto_increment flag on the
   field. Note that the field type must be a type that supports this,
   such as integer.
-  unique/true : to generate a unique key for the field definition.

::

	$fields = array(
		'blog_id' => array(
			'type' => 'INT',
			'constraint' => 5,
			'unsigned' => TRUE,
			'auto_increment' => TRUE
		),
		'blog_title' => array(
			'type' => 'VARCHAR',
			'constraint' => '100',
			'unique' => TRUE,
		),
		'blog_author' => array(
			'type' =>'VARCHAR',
			'constraint' => '100',
			'default' => 'King of Town',
		),
		'blog_description' => array(
			'type' => 'TEXT',
			'null' => TRUE,
		),
	);


After the fields have been defined, they can be added using
``$this->dbforge->add_field($fields);`` followed by a call to the
``create_table()`` method.

**$this->dbforge->add_field()**

The add fields method will accept the above array.


Passing strings as fields
-------------------------

If you know exactly how you want a field to be created, you can pass the
string into the field definitions with add_field()

::

	$this->dbforge->add_field("label varchar(100) NOT NULL DEFAULT 'default label'");


.. note:: Passing raw strings as fields cannot be followed by ``add_key()`` calls on those fields.

.. note:: Multiple calls to add_field() are cumulative.

Creating an id field
--------------------

There is a special exception for creating id fields. A field with type
id will automatically be assigned as an INT(9) auto_incrementing
Primary Key.

::

	$this->dbforge->add_field('id');
	// gives id INT(9) NOT NULL AUTO_INCREMENT


Adding Keys
===========

Generally speaking, you'll want your table to have Keys. This is
accomplished with $this->dbforge->add_key('field'). An optional second
parameter set to TRUE will make it a primary key. Note that add_key()
must be followed by a call to create_table().

Multiple column non-primary keys must be sent as an array. Sample output
below is for MySQL.

::

	$this->dbforge->add_key('blog_id', TRUE);
	// gives PRIMARY KEY `blog_id` (`blog_id`)

	$this->dbforge->add_key('blog_id', TRUE);
	$this->dbforge->add_key('site_id', TRUE);
	// gives PRIMARY KEY `blog_id_site_id` (`blog_id`, `site_id`)

	$this->dbforge->add_key('blog_name');
	// gives KEY `blog_name` (`blog_name`)

	$this->dbforge->add_key(array('blog_name', 'blog_label'));
	// gives KEY `blog_name_blog_label` (`blog_name`, `blog_label`)


Creating a table
================

After fields and keys have been declared, you can create a new table
with

::

	$this->dbforge->create_table('table_name');
	// gives CREATE TABLE table_name


An optional second parameter set to TRUE adds an "IF NOT EXISTS" clause
into the definition

::

	$this->dbforge->create_table('table_name', TRUE);
	// gives CREATE TABLE IF NOT EXISTS table_name

You could also pass optional table attributes, such as MySQL's ``ENGINE``::

	$attributes = array('ENGINE' => 'InnoDB');
	$this->dbforge->create_table('table_name', FALSE, $attributes);
	// produces: CREATE TABLE `table_name` (...) ENGINE = InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

.. note:: Unless you specify the ``CHARACTER SET`` and/or ``COLLATE`` attributes,
	``create_table()`` will always add them with your configured *char_set*
	and *dbcollat* values, as long as they are not empty (MySQL only).


Dropping a table
================

Execute a DROP TABLE statement and optionally add an IF EXISTS clause.

::

	// Produces: DROP TABLE table_name
	$this->dbforge->drop_table('table_name');

	// Produces: DROP TABLE IF EXISTS table_name
	$this->dbforge->drop_table('table_name',TRUE);


Renaming a table
================

Executes a TABLE rename

::

	$this->dbforge->rename_table('old_table_name', 'new_table_name');
	// gives ALTER TABLE old_table_name RENAME TO new_table_name


****************
Modifying Tables
****************

Adding a Column to a Table
==========================

**$this->dbforge->add_column()**

The ``add_column()`` method is used to modify an existing table. It
accepts the same field array as above, and can be used for an unlimited
number of additional fields.

::

	$fields = array(
		'preferences' => array('type' => 'TEXT')
	);
	$this->dbforge->add_column('table_name', $fields);
	// Executes: ALTER TABLE table_name ADD preferences TEXT

If you are using MySQL or CUBIRD, then you can take advantage of their
AFTER and FIRST clauses to position the new column.

Examples::

	// Will place the new column after the `another_field` column:
	$fields = array(
		'preferences' => array('type' => 'TEXT', 'after' => 'another_field')
	);

	// Will place the new column at the start of the table definition:
	$fields = array(
		'preferences' => array('type' => 'TEXT', 'first' => TRUE)
	);


Dropping a Column From a Table
==============================

**$this->dbforge->drop_column()**

Used to remove a column from a table.

::

	$this->dbforge->drop_column('table_name', 'column_to_drop');



Modifying a Column in a Table
=============================

**$this->dbforge->modify_column()**

The usage of this method is identical to ``add_column()``, except it
alters an existing column rather than adding a new one. In order to
change the name you can add a "name" key into the field defining array.

::

	$fields = array(
		'old_name' => array(
			'name' => 'new_name',
			'type' => 'TEXT',
		),
	);
	$this->dbforge->modify_column('table_name', $fields);
	// gives ALTER TABLE table_name CHANGE old_name new_name TEXT


***************
Class Reference
***************

.. php:class:: CI_DB_forge

	.. php:method:: add_column($table[, $field = array()[, $_after = NULL]])

		:param	string	$table: Table name to add the column to
		:param	array	$field: Column definition(s)
		:param	string	$_after: Column for AFTER clause (deprecated)
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Adds a column to a table. Usage:  See `Adding a Column to a Table`_.

	.. php:method:: add_field($field)

		:param	array	$field: Field definition to add
		:returns:	CI_DB_forge instance (method chaining)
		:rtype:	CI_DB_forge

                Adds a field to the set that will be used to create a table. Usage:  See `Adding fields`_.

	.. php:method:: add_key($key[, $primary = FALSE])

		:param	array	$key: Name of a key field
		:param	bool	$primary: Set to TRUE if it should be a primary key or a regular one
		:returns:	CI_DB_forge instance (method chaining)
		:rtype:	CI_DB_forge

		Adds a key to the set that will be used to create a table. Usage:  See `Adding Keys`_.

	.. php:method:: create_database($db_name)

		:param	string	$db_name: Name of the database to create
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Creates a new database. Usage:  See `Creating and Dropping Databases`_.

	.. php:method:: create_table($table[, $if_not_exists = FALSE[, array $attributes = array()]])

		:param	string	$table: Name of the table to create
		:param	string	$if_not_exists: Set to TRUE to add an 'IF NOT EXISTS' clause
		:param	string	$attributes: An associative array of table attributes
		:returns:  TRUE on success, FALSE on failure
		:rtype:	bool

		Creates a new table. Usage:  See `Creating a table`_.

	.. php:method:: drop_column($table, $column_name)

		:param	string	$table: Table name
		:param	array	$column_name: The column name to drop
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Drops a column from a table. Usage:  See `Dropping a Column From a Table`_.

	.. php:method:: drop_database($db_name)

		:param	string	$db_name: Name of the database to drop
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Drops a database. Usage:  See `Creating and Dropping Databases`_.

	.. php:method:: drop_table($table_name[, $if_exists = FALSE])

		:param	string	$table: Name of the table to drop
		:param	string	$if_exists: Set to TRUE to add an 'IF EXISTS' clause
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Drops a table. Usage:  See `Dropping a table`_.

	.. php:method:: modify_column($table, $field)

		:param	string	$table: Table name
		:param	array	$field: Column definition(s)
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Modifies a table column. Usage:  See `Modifying a Column in a Table`_.

	.. php:method:: rename_table($table_name, $new_table_name)

		:param	string	$table: Current of the table
		:param	string	$new_table_name: New name of the table
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Renames a table. Usage:  See `Renaming a table`_.
