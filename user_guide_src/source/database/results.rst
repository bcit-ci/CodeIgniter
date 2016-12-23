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

You can also pass a string to ``result()`` which represents a class to
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

	$row = $query->row();

	if (isset($row))
	{
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
	$row = $query->row(0, 'User');
	
	echo $row->name; // access attributes
	echo $row->reverse_name(); // or methods defined on the 'User' class

**row_array()**

Identical to the above ``row()`` method, except it returns an array.
Example::

	$query = $this->db->query("YOUR QUERY");

	$row = $query->row_array();

	if (isset($row))
	{
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
Custom Result Objects
*********************

You can have the results returned as an instance of a custom class instead
of a ``stdClass`` or array, as the ``result()`` and ``result_array()``
methods allow. This requires that the class is already loaded into memory.
The object will have all values returned from the database set as properties.
If these have been declared and are non-public then you should provide a
``__set()`` method to allow them to be set.

Example::

	class User {

		public $id;
		public $email;
		public $username;

		protected $last_login;

		public function last_login($format)
		{
			return $this->last_login->format($format);
		}

		public function __set($name, $value)
		{
			if ($name === 'last_login')
			{
				$this->last_login = DateTime::createFromFormat('U', $value);
			}
		}

		public function __get($name)
		{
			if (isset($this->$name))
			{
				return $this->$name;
			}
		}
	}

In addition to the two methods listed below, the following methods also can
take a class name to return the results as: ``first_row()``, ``last_row()``,
``next_row()``, and ``previous_row()``.

**custom_result_object()**

Returns the entire result set as an array of instances of the class requested.
The only parameter is the name of the class to instantiate.

Example::

	$query = $this->db->query("YOUR QUERY");

	$rows = $query->custom_result_object('User');

	foreach ($rows as $row)
	{
		echo $row->id;
		echo $row->email;
		echo $row->last_login('Y-m-d');
	}

**custom_row_object()**

Returns a single row from your query results. The first parameter is the row
number of the results. The second parameter is the class name to instantiate.

Example::

	$query = $this->db->query("YOUR QUERY");

	$row = $query->custom_row_object(0, 'User');

	if (isset($row))
	{
		echo $row->email;   // access attributes
		echo $row->last_login('Y-m-d');   // access class methods
	}

You can also use the ``row()`` method in exactly the same way.

Example::

	$row = $query->custom_row_object(0, 'User');

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

.. php:class:: CI_DB_result

	.. php:method:: result([$type = 'object'])

		:param	string	$type: Type of requested results - array, object, or class name
		:returns:	Array containing the fetched rows
		:rtype:	array

		A wrapper for the ``result_array()``, ``result_object()``
		and ``custom_result_object()`` methods.

		Usage: see `Result Arrays`_.

	.. php:method:: result_array()

		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is itself an associative array.

		Usage: see `Result Arrays`_.

	.. php:method:: result_object()

		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is an object of type ``stdClass``.

		Usage: see `Result Arrays`_.

	.. php:method:: custom_result_object($class_name)

		:param	string	$class_name: Class name for the resulting rows
		:returns:	Array containing the fetched rows
		:rtype:	array

		Returns the query results as an array of rows, where each
		row is an instance of the specified class.

	.. php:method:: row([$n = 0[, $type = 'object']])

		:param	int	$n: Index of the query results row to be returned
		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	mixed

		A wrapper for the ``row_array()``, ``row_object() and 
		``custom_row_object()`` methods.

		Usage: see `Result Rows`_.

	.. php:method:: unbuffered_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Next row from the result set or NULL if it doesn't exist
		:rtype:	mixed

		Fetches the next result row and returns it in the
		requested form.

		Usage: see `Result Rows`_.

	.. php:method:: row_array([$n = 0])

		:param	int	$n: Index of the query results row to be returned
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	array

		Returns the requested result row as an associative array.

		Usage: see `Result Rows`_.

	.. php:method:: row_object([$n = 0])

		:param	int	$n: Index of the query results row to be returned
                :returns:	The requested row or NULL if it doesn't exist
		:rtype:	stdClass

		Returns the requested result row as an object of type
		``stdClass``.

		Usage: see `Result Rows`_.

	.. php:method:: custom_row_object($n, $type)

		:param	int	$n: Index of the results row to return
		:param	string	$class_name: Class name for the resulting row
		:returns:	The requested row or NULL if it doesn't exist
		:rtype:	$type

		Returns the requested result row as an instance of the
		requested class.

	.. php:method:: data_seek([$n = 0])

		:param	int	$n: Index of the results row to be returned next
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Moves the internal results row pointer to the desired offset.

		Usage: see `Result Helper Methods`_.

	.. php:method:: set_row($key[, $value = NULL])

		:param	mixed	$key: Column name or array of key/value pairs
		:param	mixed	$value: Value to assign to the column, $key is a single field name
		:rtype:	void

		Assigns a value to a particular column.

	.. php:method:: next_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Next row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the next row from the result set.

	.. php:method:: previous_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Previous row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the previous row from the result set.

	.. php:method:: first_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	First row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the first row from the result set.

	.. php:method:: last_row([$type = 'object'])

		:param	string	$type: Type of the requested result - array, object, or class name
		:returns:	Last row of result set, or NULL if it doesn't exist
		:rtype:	mixed

		Returns the last row from the result set.

	.. php:method:: num_rows()

		:returns:	Number of rows in the result set
		:rtype:	int

		Returns the number of rows in the result set.

		Usage: see `Result Helper Methods`_.

	.. php:method:: num_fields()

		:returns:	Number of fields in the result set
		:rtype:	int

		Returns the number of fields in the result set.

		Usage: see `Result Helper Methods`_.

	.. php:method:: field_data()

		:returns:	Array containing field meta-data
		:rtype:	array

		Generates an array of ``stdClass`` objects containing
		field meta-data.

	.. php:method:: free_result()

		:rtype:	void

		Frees a result set.

		Usage: see `Result Helper Methods`_.

	.. php:method:: list_fields()

		:returns:	Array of column names
		:rtype:	array

		Returns an array containing the field names in the
		result set.
