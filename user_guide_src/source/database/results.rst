########################
Generating Query Results
########################

There are several ways to generate query results:

.. contents::
    :local:
    :depth: 2

*************
Result Arrays
*************

**result()**

This method returns the query result as an array of **objects**, or
**an empty array** on failure. Typically you'll use this in a foreach
loop, like this::

	$query = $this->db->query("YOUR QUERY");
	
	foreach ($query->result() as $row)
	{
		echo $row->title;
		echo $row->name;
		echo $row->body;
	}

The above method is an alias of ``result_object()``.

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
		echo $user->name; // access attributes
		echo $user->reverse_name(); // or methods defined on the 'User' class
	}

**result_array()**

This method returns the query result as a pure array, or an empty
array when no result is produced. Typically you'll use this in a foreach
loop, like this::

	$query = $this->db->query("YOUR QUERY");
	
	foreach ($query->result_array() as $row)
	{
		echo $row['title'];
		echo $row['name'];
		echo $row['body'];
	}

***********
Result Rows
***********

**row()**

This method returns a single result row. If your query has more than
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
	
	echo $row->name; // access attributes
	echo $row->reverse_name(); // or methods defined on the 'User' class

**row_array()**

Identical to the above ``row()`` method, except it returns an array.
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

.. note:: All the methods above will load the whole result into memory
	(prefetching). Use ``unbuffered_row()`` for processing large
	result sets.

**unbuffered_row()**

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

*********************
Result Helper Methods
*********************

**num_rows()**

The number of rows returned by the query. Note: In this example, $query
is the variable that the query result object is assigned to::

	$query = $this->db->query('SELECT * FROM my_table');
	
	echo $query->num_rows();

.. note:: Not all database drivers have a native way of getting the total
	number of rows for a result set. When this is the case, all of
	the data is prefetched and ``count()`` is manually called on the
	resulting array in order to achieve the same result.
	
**num_fields()**

The number of FIELDS (columns) returned by the query. Make sure to call
the method using your query result object::

	$query = $this->db->query('SELECT * FROM my_table');
	
	echo $query->num_fields();

**free_result()**

It frees the memory associated with the result and deletes the result
resource ID. Normally PHP frees its memory automatically at the end of
script execution. However, if you are running a lot of queries in a
particular script you might want to free the result after each query
result has been generated in order to cut down on memory consumption.

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

**data_seek()**

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

***************
Class Reference
***************

.. class:: CI_DB_result

	.. method:: result([$type = 'object'])

		:param	string	$type: Type of requested results - array, object, or class name
		:returns:	Array containing the fetched rows
		:rtype:	array

		A wrapper for the ``result_array()``, ``result_object()``
		and ``custom_result_object()`` methods.

		Usage: see `Result Arrays`_.

	.. method:: result_array()

		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is itself an associative array.

		Usage: see `Result Arrays`_.

	.. method:: result_object()

		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is an object of type ``stdClass``.

		Usage: see `Result Arrays`_.

	.. method:: custom_result_object($class_name)

		:param	string	$class_name: Class name for the resulting rows
		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is an instance of the specified class.

	.. method:: row([$n = 0[, $type = 'object']])

		:param	int	$n: Index of the query results row to be returned
		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	mixed

		A wrapper for the ``row_array()``, ``row_object() and 
		``custom_row_object()`` methods.

		Usage: see `Result Rows`_.

	.. method:: unbuffered_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Next row from the result set or NULL if it doesn't exist
		:rtype:	mixed

		Fetches the next result row and returns it in the
		requested form.

		Usage: see `Result Rows`_.

	.. method:: row_array([$n = 0])

		:param	int	$n: Index of the query results row to be returned
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	array

		Returns the requested result row as an associative array.

		Usage: see `Result Rows`_.

	.. method:: row_object([$n = 0])

		:param	int	$n: Index of the query results row to be returned
                :returns:	The requested row or NULL if it doesn't exist
		:rtype:	stdClass

		Returns the requested result row as an object of type
		``stdClass``.

		Usage: see `Result Rows`_.

	.. method:: custom_row_object($n, $type)

		:param	int	$n: Index of the results row to return
		:param	string	$class_name: Class name for the resulting row
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	$type

		Returns the requested result row as an instance of the
		requested class.

	.. method:: data_seek([$n = 0])

		:param	int	$n: Index of the results row to be returned next
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Moves the internal results row pointer to the desired offset.

		Usage: see `Result Helper Methods`_.

	.. method:: set_row($key[, $value = NULL])

		:param	mixed	$key: Column name or array of key/value pairs
		:param	mixed	$value: Value to assign to the column, $key is a single field name
		:rtype:	void

		Assigns a value to a particular column.

	.. method:: next_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Next row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the next row from the result set.

	.. method:: previous_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Previous row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the previous row from the result set.

	.. method:: first_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	First row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the first row from the result set.

	.. method:: last_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Last row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the last row from the result set.

	.. method:: num_rows()

		:returns:	Number of rows in the result set
		:rtype:	int

		Returns the number of rows in the result set.

		Usage: see `Result Helper Methods`_.

	.. method:: num_fields()

		:returns:	Number of fields in the result set
		:rtype:	int

		Returns the number of fields in the result set.

		Usage: see `Result Helper Methods`_.

	.. method:: field_data()

		:returns:	Array containing field meta-data
		:rtype:	array

		Generates an array of ``stdClass`` objects containing
		field meta-data.

	.. method:: free_result()

		:rtype:	void

		Frees a result set.

		Usage: see `Result Helper Methods`_.

	.. method:: list_fields()

		:returns:	Array of column names
		:rtype:	array

		Returns an array containing the field names in the
		result set.