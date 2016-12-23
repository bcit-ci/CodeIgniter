####################
Query Helper Methods
####################

Information From Executing a Query
==================================

**$this->db->insert_id()**

The insert ID number when performing database inserts.

.. note:: If using the PDO driver with PostgreSQL, or using the Interbase
	driver, this function requires a $name parameter, which specifies the 
	appropriate sequence to check for the insert id.

**$this->db->affected_rows()**

Displays the number of affected rows, when doing "write" type queries
(insert, update, etc.).

.. note:: In MySQL "DELETE FROM TABLE" returns 0 affected rows. The database
	class has a small hack that allows it to return the correct number of
	affected rows. By default this hack is enabled but it can be turned off
	in the database driver file.

**$this->db->last_query()**

Returns the last query that was run (the query string, not the result).
Example::

	$str = $this->db->last_query();
	
	// Produces:  SELECT * FROM sometable....


.. note:: Disabling the **save_queries** setting in your database
	configuration will render this function useless.

Information About Your Database
===============================

**$this->db->count_all()**

Permits you to determine the number of rows in a particular table.
Submit the table name in the first parameter. Example::

	echo $this->db->count_all('my_table');
	
	// Produces an integer, like 25

**$this->db->platform()**

Outputs the database platform you are running (MySQL, MS SQL, Postgres,
etc...)::

	echo $this->db->platform();

**$this->db->version()**

Outputs the database version you are running::

	echo $this->db->version();

Making Your Queries Easier
==========================

**$this->db->insert_string()**

This function simplifies the process of writing database inserts. It
returns a correctly formatted SQL insert string. Example::

	$data = array('name' => $name, 'email' => $email, 'url' => $url);
	
	$str = $this->db->insert_string('table_name', $data);

The first parameter is the table name, the second is an associative
array with the data to be inserted. The above example produces::

	INSERT INTO table_name (name, email, url) VALUES ('Rick', 'rick@example.com', 'example.com')

.. note:: Values are automatically escaped, producing safer queries.

**$this->db->update_string()**

This function simplifies the process of writing database updates. It
returns a correctly formatted SQL update string. Example::

	$data = array('name' => $name, 'email' => $email, 'url' => $url);
	
	$where = "author_id = 1 AND status = 'active'";
	
	$str = $this->db->update_string('table_name', $data, $where);

The first parameter is the table name, the second is an associative
array with the data to be updated, and the third parameter is the
"where" clause. The above example produces::

	 UPDATE table_name SET name = 'Rick', email = 'rick@example.com', url = 'example.com' WHERE author_id = 1 AND status = 'active'

.. note:: Values are automatically escaped, producing safer queries.