########################
Generating Query Results
########################

There are several ways to generate query results:

result()
========

This function returns the query result as an array of **objects**, or
**an empty array** on failure. Typically you'll use this in a foreach
loop, like this::

	$query = $this->db->query("YOUR QUERY");
	
	foreach ($query->result() as $row)
	{
		echo $row->title;
		echo $row->name;
		echo $row->body;
	}

The above function is an alias of result_object().

If you run queries that might **not** produce a result, you are
encouraged to test the result first::

	$query = $this->db->query("YOUR QUERY");
	
	if ($query->num_rows() > 0)
	{
		foreach ($query->result() as $row)
		{
			echo $row->title;
			echo $row->name;
			echo $row->body;
		}
	}

You can also pass a string to result() which represents a class to
instantiate for each result object (note: this class must be loaded)

::

	$query = $this->db->query("SELECT * FROM users;");

	foreach ($query->result('User') as $user)
	{
	   echo $user->name; // call attributes
	   echo $user->reverse_name(); // or methods defined on the 'User' class
	}

result_array()
===============

This function returns the query result as a pure array, or an empty
array when no result is produced. Typically you'll use this in a foreach
loop, like this::

	$query = $this->db->query("YOUR QUERY");
	
	foreach ($query->result_array() as $row)
	{
		echo $row['title'];
		echo $row['name'];
		echo $row['body'];
	}

row()
=====

This function returns a single result row. If your query has more than
one row, it returns only the first row. The result is returned as an
**object**. Here's a usage example::

	$query = $this->db->query("YOUR QUERY");
	
	if ($query->num_rows() > 0)
	{
		$row = $query->row();
		
		echo $row->title;
		echo $row->name;
		echo $row->body;
	}

If you want a specific row returned you can submit the row number as a
digit in the first parameter::

	$row = $query->row(5);

You can also add a second String parameter, which is the name of a class
to instantiate the row with::

	$query = $this->db->query("SELECT * FROM users LIMIT 1;");
	$query->row(0, 'User');
	
	echo $row->name; // call attributes
	echo $row->reverse_name(); // or methods defined on the 'User' class

row_array()
===========

Identical to the above row() function, except it returns an array.
Example::

	$query = $this->db->query("YOUR QUERY");
	
	if ($query->num_rows() > 0)
	{
		$row = $query->row_array();
		
		echo $row['title'];
		echo $row['name'];
		echo $row['body'];
	}

If you want a specific row returned you can submit the row number as a
digit in the first parameter::

	$row = $query->row_array(5);

In addition, you can walk forward/backwards/first/last through your
results using these variations:

	| **$row = $query->first_row()**
	| **$row = $query->last_row()**
	| **$row = $query->next_row()**
	| **$row = $query->previous_row()**

By default they return an object unless you put the word "array" in the
parameter:

	| **$row = $query->first_row('array')**
	| **$row = $query->last_row('array')**
	| **$row = $query->next_row('array')**
	| **$row = $query->previous_row('array')**

.. note:: all the functions above will load the whole result into memory (prefetching) use unbuffered_row() for processing large result sets.

unbuffered_row()
================

This method returns a single result row without prefetching the whole
result in memory as ``row()`` does. If your query has more than one row,
it returns the current row and moves the internal data pointer ahead. 

::

	$query = $this->db->query("YOUR QUERY");
	
	while ($row = $query->unbuffered_row())
	{	
		echo $row->title;
		echo $row->name;
		echo $row->body;
	}

You can optionally pass 'object' (default) or 'array' in order to specify
the returned value's type::

	$query->unbuffered_row();		// object
	$query->unbuffered_row('object');	// object
	$query->unbuffered_row('array');	// associative array

***********************
Result Helper Functions
***********************

$query->num_rows()
==================

The number of rows returned by the query. Note: In this example, $query
is the variable that the query result object is assigned to::

	$query = $this->db->query('SELECT * FROM my_table');
	
	echo $query->num_rows();

.. note::
	Not all database drivers have a native way of getting the total
	number of rows for a result set. When this is the case, all of
	the data is prefetched and count() is manually called on the
	resulting array in order to achieve the same functionality.
	
$query->num_fields()
====================

The number of FIELDS (columns) returned by the query. Make sure to call
the function using your query result object::

	$query = $this->db->query('SELECT * FROM my_table');
	
	echo $query->num_fields();

$query->free_result()
=====================

It frees the memory associated with the result and deletes the result
resource ID. Normally PHP frees its memory automatically at the end of
script execution. However, if you are running a lot of queries in a
particular script you might want to free the result after each query
result has been generated in order to cut down on memory consumptions.
Example::

	$query = $this->db->query('SELECT title FROM my_table');
	
	foreach ($query->result() as $row)
	{
		echo $row->title;
	}
	$query->free_result();  // The $query result object will no longer be available

	$query2 = $this->db->query('SELECT name FROM some_table');

	$row = $query2->row();
	echo $row->name;
	$query2->free_result(); // The $query2 result object will no longer be available

data_seek()
===========

This method sets the internal pointer for the next result row to be
fetched. It is only useful in combination with ``unbuffered_row()``.

It accepts a positive integer value, which defaults to 0 and returns
TRUE on success or FALSE on failure.

::

	$query = $this->db->query('SELECT `field_name` FROM `table_name`');
	$query->data_seek(5); // Skip the first 5 rows
	$row = $query->unbuffered_row();

.. note:: Not all database drivers support this feature and will return FALSE.
	Most notably - you won't be able to use it with PDO.