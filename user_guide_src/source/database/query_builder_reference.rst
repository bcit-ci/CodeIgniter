#################
Query Builder API
#################

.. note:: This page provides a complete API reference for the Query Builder
        class. For information on how to use the class, see the
        `Query Builder Class <query_builder.html>`_ page.

***************
Class Reference
***************

.. class:: CI_DB_query_builder

	.. method:: count_all_results($table = '')

		:param	string	$table: Table name to query
		:returns:	Number of rows in the query result
		:rtype:	int

		Generates a platform-specific query string that counts 
                all records returned by an Query Builder query.

	.. method:: dbprefix($table = '')

		:param	string	$table: The table name to work with
		:returns:	The modified table name
		:rtype:	string

		Prepends a database prefix if one exists in configuration

	.. method:: delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)

		:param	mixed	$table: The table(s) to delete from; string or array
		:param	string	$where: The where clause
		:param	string	$limit: The limit clause
		:param	boolean	$reset_data: TRUE to reset the query "write" clause
		:returns:	DB_query_builder instance, FALSE on failure
		:rtype:	mixed

		Compiles a delete string and runs the query

	.. method:: distinct($val = TRUE)

		:param	boolean	$val: Desired value of the "distinct" flag
		:returns:	DB_query_driver instance
		:rtype:	object

		Sets a flag which tells the query string compiler to add DISTINCT

	.. method:: empty_table($table = '')

		:param	string	$table: Name of table to empty
		:returns:	DB_driver instance
		:rtype:	object

		Compiles a delete string and runs "DELETE FROM table"

	.. method:: flush_cache()

		:rtype:	void

		Empties the QB cache

	.. method:: from($from)

		:param	mixed	$from: Can be a string or array
		:returns:	DB_query_builder instance
		:rtype:	object

		Generates the FROM portion of the query

	.. method:: get($table = '', $limit = NULL, $offset = NULL)

		:param	string	$table: The table to query
		:param	string	$limit: The limit clause
		:param	string	$offset: The offset clause
		:returns:	DB_result
		:rtype:	object

		Compiles the select statement based on the other functions 
                called and runs the query

	.. method:: get_compiled_delete($table = '', $reset = TRUE)

		:param	string	$table: Name of the table to delete from
		:param	boolean	$reset: TRUE: reset QB values; FALSE: leave QB values alone
		:returns:	The SQL string
		:rtype:	string

		Compiles a delete query string and returns the sql

	.. method:: get_compiled_insert($table = '', $reset = TRUE)

		:param	string	$table: Name of the table to insert into
		:param	boolean	$reset: TRUE: reset QB values; FALSE: leave QB values alone
		:returns:	The SQL string
		:rtype:	string

		Compiles an insert query string and returns the sql

	.. method:: get_compiled_select($table = '', $reset = TRUE)

		:param	string	$table: Name of the table to select from
		:param	boolean	$reset: TRUE: reset QB values; FALSE: leave QB values alone
		:returns:	The SQL string
		:rtype:	string

		Compiles a select query string and returns the sql

	.. method:: get_compiled_update($table = '', $reset = TRUE)

		:param	string	$table: Name of the table to update
		:param	boolean	$reset: TRUE: reset QB values; FALSE: leave QB values alone
		:returns:	The SQL string
		:rtype:	string

		Compiles an update query string and returns the sql

	.. method:: get_where($table = '', $where = NULL, $limit = NULL, $offset = NULL)

		:param	mixed	$table: The table(s) to delete from; string or array
		:param	string	$where: The where clause
		:param	int	$limit: Number of records to return
		:param	int	$offset: Number of records to skip
		:returns:	DB_result
		:rtype:	object

		Allows the where clause, limit and offset to be added directly

	.. method:: group_by($by, $escape = NULL)

		:param	mixed	$by: Field(s) to group by; string or array
		:returns:	DB_query_builder instance
		:rtype:	object

		Adds a GROUPBY clause to the query

	.. method:: group_end()

		:returns:	DB_query_builder instance
		:rtype:	object

		Ends a query group

	.. method:: group_start($not = '', $type = 'AND ')

		:param	string	$not: (Internal use only)
		:param	string	$type: (Internal use only)
		:returns:	DB_query_builder instance
		:rtype:	object

		Starts a query group.

	.. method:: having($key, $value = NULL, $escape = NULL)

		:param	string	$key: Key (string) or associative array of values
		:param	string	$value: Value sought if the key is a string
		:param	string	$escape: TRUE to escape the content
		:returns:	DB_query_builder instance
		:rtype:	object

		Separates multiple calls with 'AND'.

	.. method:: insert($table = '', $set = NULL, $escape = NULL)

		:param	string	$table: The table to insert data into
		:param	array	$set: An associative array of insert values
		:param	boolean	$table: Whether to escape values and identifiers
		:returns:	DB_result
		:rtype:	object

		Compiles an insert string and runs the query

	.. method:: insert_batch($table = '', $set = NULL, $escape = NULL)

		:param	string	$table: The table to insert data into
		:param	array	$set: An associative array of insert values
		:param	boolean	$escape: Whether to escape values and identifiers
		:returns:	Number of rows inserted or FALSE on failure
		:rtype:	mixed

		Compiles batch insert strings and runs the queries

	.. method:: join($table, $cond, $type = '', $escape = NULL)

		:param	string	$table: Name of the table being joined
		:param	string	$cond: The JOIN condition
		:param	string	$type: The JOIN type
		:param	boolean	$escape: Whether to escape values and identifiers
		:returns:	DB_query_builder instance
		:rtype:	object

		Generates the JOIN portion of the query

	.. method:: like($field, $match = '', $side = 'both', $escape = NULL)

		:param	string	$field: Name of field to compare
		:param	string	$match: Text portion to match
		:param	string	$side: Position of a match
		:param	boolean	$escape: Whether to escape values and identifiers
		:returns:	DB_query_builder instance
		:rtype:	object

		Generates a %LIKE% portion of the query.
                Separates multiple calls with 'AND'.

	.. method:: limit($value, $offset = FALSE)

		:param	mixed	$value: Number of rows to limit the results to, NULL for no limit
		:param	mixed	$offset: Number of rows to skip, FALSE if no offset used
		:returns:	DB_query_builder instance
		:rtype:	object

		Specify a limit and offset for the query

	.. method:: x

		:param	string	$x: x
		:returns:	x
		:rtype:	x

		x

	.. method:: x

		:param	string	$x: x
		:returns:	x
		:rtype:	x

		x

	.. method:: x

		:param	string	$x: x
		:returns:	x
		:rtype:	x

		x

