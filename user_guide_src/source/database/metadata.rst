#################
Database Metadata
#################

**************
Table MetaData
**************

These functions let you fetch table information.

List the Tables in Your Database
================================

**$this->db->list_tables();**

Returns an array containing the names of all the tables in the database
you are currently connected to. Example::

	$tables = $this->db->list_tables();
	
	foreach ($tables as $table)
	{
		echo $table;
	}


Determine If a Table Exists
===========================

**$this->db->table_exists();**

Sometimes it's helpful to know whether a particular table exists before
running an operation on it. Returns a boolean TRUE/FALSE. Usage example::

	if ($this->db->table_exists('table_name'))
	{
		// some code...
	}

.. note:: Replace *table_name* with the name of the table you are looking for.


**************
Field MetaData
**************

List the Fields in a Table
==========================

**$this->db->list_fields()**

Returns an array containing the field names. This query can be called
two ways:

1. You can supply the table name and call it from the $this->db->
object::

	$fields = $this->db->list_fields('table_name');
	
	foreach ($fields as $field)
	{
		echo $field;
	}

2. You can gather the field names associated with any query you run by
calling the function from your query result object::

	$query = $this->db->query('SELECT * FROM some_table');
	
	foreach ($query->list_fields() as $field)
	{
		echo $field;
	}


Determine If a Field is Present in a Table
==========================================

**$this->db->field_exists()**

Sometimes it's helpful to know whether a particular field exists before
performing an action. Returns a boolean TRUE/FALSE. Usage example::

	if ($this->db->field_exists('field_name', 'table_name'))
	{
		// some code...
	}

.. note:: Replace *field_name* with the name of the column you are looking
	for, and replace *table_name* with the name of the table you are
	looking for.


Retrieve Field Metadata
=======================

**$this->db->field_data()**

Returns an array of objects containing field information.

Sometimes it's helpful to gather the field names or other metadata, like
the column type, max length, etc.

.. note:: Not all databases provide meta-data.

Usage example::

	$fields = $this->db->field_data('table_name');
	
	foreach ($fields as $field)
	{
		echo $field->name;
		echo $field->type;
		echo $field->max_length;
		echo $field->primary_key;
	}

If you have run a query already you can use the result object instead of
supplying the table name::

	$query = $this->db->query("YOUR QUERY");
	$fields = $query->field_data();

The following data is available from this function if supported by your
database:

-  name - column name
-  max_length - maximum length of the column
-  primary_key - 1 if the column is a primary key
-  type - the type of the column
