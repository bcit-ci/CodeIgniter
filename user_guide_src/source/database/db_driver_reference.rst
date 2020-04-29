###################
DB Driver Reference
###################

This is the platform-independent base DB implementation class.
This class will not be called directly. Rather, the adapter
class for the specific database will extend and instantiate it.

The how-to material for this has been split over several articles.
This article is intended to be a reference for them.

.. important:: Not all methods are supported by all database drivers,
	some of them may fail (and return FALSE) if the underlying
	driver does not support them.

.. php:class:: CI_DB_driver

	.. php:method:: initialize()

		:rtype:	void
		:throws:	RuntimeException	In case of failure

		Initialize database settings, establish a connection to
		the database.

	.. php:method:: db_connect($persistent = TRUE)

		:param	bool	$persistent: Whether to establish a persistent connection or a regular one
		:returns:	Database connection resource/object or FALSE on failure
		:rtype:	mixed

		Establish a connection with the database.

		.. note:: The returned value depends on the underlying
			driver in use. For example, a ``mysqli`` instance
			will be returned with the 'mysqli' driver.

	.. php:method:: db_pconnect()

		:returns:	Database connection resource/object or FALSE on failure
		:rtype:	mixed

		Establish a persistent connection with the database.

		.. note:: This method is just an alias for ``db_connect(TRUE)``.

	.. php:method:: reconnect()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Keep / reestablish the database connection if no queries
		have been sent for a length of time exceeding the
		server's idle timeout.

	.. php:method:: db_select([$database = ''])

		:param	string	$database: Database name
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Select / switch the current database.

	.. php:method:: platform()

		:returns:	Platform name
		:rtype:	string

		The name of the platform in use (mysql, mssql, etc...).

	.. php:method:: version()

		:returns:	The version of the database being used
		:rtype:	string

		Database version number.

	.. php:method:: query($sql[, $binds = FALSE[, $return_object = NULL]])

		:param	string	$sql: The SQL statement to execute
		:param	array	$binds: An array of binding data
		:param	bool	$return_object: Whether to return a result object or not
		:returns:	TRUE for successful "write-type" queries, CI_DB_result instance (method chaining) on "query" success, FALSE on failure
		:rtype:	mixed

		Execute an SQL query.

		Accepts an SQL string as input and returns a result object
		upon successful execution of a "read" type query.

		Returns:

		   - Boolean TRUE upon successful execution of a "write type" queries
		   - Boolean FALSE upon failure
		   - ``CI_DB_result`` object for "read type" queries

		.. note: If 'db_debug' setting is set to TRUE, an error
			page will be displayed instead of returning FALSE
			on failures and script execution will stop.

	.. php:method:: simple_query($sql)

		:param	string	$sql: The SQL statement to execute
		:returns:	Whatever the underlying driver's "query" function returns
		:rtype:	mixed

		A simplified version of the ``query()`` method, appropriate
		for use when you don't need to get a result object or to
		just send a query to the database and not care for the result.

	.. php:method:: affected_rows()

		:returns:	Number of rows affected
		:rtype:	int

		Returns the number of rows *changed* by the last executed query.

		Useful for checking how much rows were created, updated or deleted
		during the last executed query.

	.. php:method:: trans_strict([$mode = TRUE])

		:param	bool	$mode: Strict mode flag
		:rtype:	void

		Enable/disable transaction "strict" mode.

		When strict mode is enabled, if you are running multiple
		groups of transactions and one group fails, all subsequent
		groups will be rolled back.

		If strict mode is disabled, each group is treated
		autonomously, meaning a failure of one group will not
		affect any others.

	.. php:method:: trans_off()

		:rtype:	void

		Disables transactions at run-time.

	.. php:method:: trans_start([$test_mode = FALSE])

		:param	bool	$test_mode: Test mode flag
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Start a transaction.

	.. php:method:: trans_complete()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Complete Transaction.

	.. php:method:: trans_status()

                :returns:	TRUE if the transaction succeeded, FALSE if it failed
		:rtype:	bool

		Lets you retrieve the transaction status flag to
		determine if it has failed.
		
	.. php:method:: trans_active()

		:returns:	TRUE if a transaction is active, FALSE if not
		:rtype:	bool

		Determines if a transaction is currently active.

	.. php:method:: compile_binds($sql, $binds)

		:param	string	$sql: The SQL statement 
		:param	array	$binds: An array of binding data
		:returns:	The updated SQL statement
		:rtype:	string

		Compiles an SQL query with the bind values passed for it.

	.. php:method:: is_write_type($sql)

		:param	string	$sql: The SQL statement 
		:returns:	TRUE if the SQL statement is of "write type", FALSE if not
		:rtype:	bool

		Determines if a query is of a "write" type (such as
		INSERT, UPDATE, DELETE) or "read" type (i.e. SELECT).

	.. php:method:: elapsed_time([$decimals = 6])

		:param	int	$decimals: The number of decimal places
		:returns:	The aggregate query elapsed time, in microseconds
		:rtype:	string

		Calculate the aggregate query elapsed time.

	.. php:method:: total_queries()

		:returns:	The total number of queries executed
		:rtype:	int

		Returns the total number of queries that have been
		executed so far.

	.. php:method:: last_query()

		:returns:	The last query executed
		:rtype:	string

		Returns the last query that was executed.

	.. php:method:: escape($str)

		:param	mixed	$str: The value to escape, or an array of multiple ones
		:returns:	The escaped value(s)
		:rtype:	mixed

		Escapes input data based on type, including boolean and
		NULLs.

	.. php:method:: escape_str($str[, $like = FALSE])

		:param	mixed	$str: A string value or array of multiple ones
		:param	bool	$like: Whether or not the string will be used in a LIKE condition
		:returns:	The escaped string(s)
		:rtype:	mixed

		Escapes string values.

		.. warning:: The returned strings do NOT include quotes
			around them.

	.. php:method:: escape_like_str($str)

		:param	mixed	$str: A string value or array of multiple ones
		:returns:	The escaped string(s)
		:rtype:	mixed

		Escape LIKE strings.

		Similar to ``escape_str()``, but will also escape the ``%``
		and ``_`` wildcard characters, so that they don't cause
		false-positives in LIKE conditions.

		.. important:: The ``escape_like_str()`` method uses '!' (exclamation mark)
			to escape special characters for *LIKE* conditions. Because this
			method escapes partial strings that you would wrap in quotes
			yourself, it cannot automatically add the ``ESCAPE '!'``
			condition for you, and so you'll have to manually do that.


	.. php:method:: primary($table)

		:param	string	$table: Table name
		:returns:	The primary key name, FALSE if none
		:rtype:	string

		Retrieves the primary key of a table.

		.. note:: If the database platform does not support primary
			key detection, the first column name may be assumed
			as the primary key.

	.. php:method:: count_all([$table = ''])

		:param	string	$table: Table name
		:returns:	Row count for the specified table
		:rtype:	int

		Returns the total number of rows in a table, or 0 if no
		table was provided.

	.. php:method:: list_tables([$constrain_by_prefix = FALSE])

		:param	bool	$constrain_by_prefix: TRUE to match table names by the configured dbprefix
		:returns:	Array of table names or FALSE on failure
		:rtype:	array

		Gets a list of the tables in the current database.

	.. php:method:: table_exists($table_name)

		:param	string	$table_name: The table name
		:returns:	TRUE if that table exists, FALSE if not
		:rtype:	bool

		Determine if a particular table exists.

	.. php:method:: list_fields($table)

		:param	string	$table: The table name
		:returns:	Array of field names or FALSE on failure
		:rtype:	array

		Gets a list of the field names in a table.

	.. php:method:: field_exists($field_name, $table_name)

		:param	string	$table_name: The table name
		:param	string	$field_name: The field name
		:returns:	TRUE if that field exists in that table, FALSE if not
		:rtype:	bool

		Determine if a particular field exists.

	.. php:method:: field_data($table)

		:param	string	$table: The table name
		:returns:	Array of field data items or FALSE on failure
		:rtype:	array

		Gets a list containing field data about a table.

	.. php:method:: escape_identifiers($item, $split = TRUE)

		:param	mixed	$item: The item or array of items to escape
		:param	bool	$split: Whether to split identifiers when a dot is encountered
		:returns:	The input item(s), escaped
		:rtype:	mixed

		Escape SQL identifiers, such as column, table and names.

	.. php:method:: insert_string($table, $data)

		:param	string	$table: The target table
		:param	array	$data: An associative array of key/value pairs
		:returns:	The SQL INSERT statement, as a string
		:rtype:	string

		Generate an INSERT statement string.

	.. php:method:: update_string($table, $data, $where)

		:param	string	$table: The target table
		:param	array	$data: An associative array of key/value pairs
		:param	mixed	$where: The WHERE statement conditions
		:returns:	The SQL UPDATE statement, as a string
		:rtype:	string

		Generate an UPDATE statement string.

	.. php:method:: call_function($function)

		:param	string	$function: Function name
		:returns:	The function result
		:rtype:	string

		Runs a native PHP function , using a platform agnostic
		wrapper.

	.. php:method:: cache_set_path([$path = ''])

		:param	string	$path: Path to the cache directory
		:rtype:	void

		Sets the directory path to use for caching storage.

	.. php:method:: cache_on()

		:returns:	TRUE if caching is on, FALSE if not
		:rtype:	bool

		Enable database results caching.

	.. php:method:: cache_off()

		:returns:	TRUE if caching is on, FALSE if not
		:rtype:	bool

		Disable database results caching.

	.. php:method:: cache_delete([$segment_one = ''[, $segment_two = '']])

		:param	string	$segment_one: First URI segment
		:param	string	$segment_two: Second URI segment
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Delete the cache files associated with a particular URI.

	.. php:method:: cache_delete_all()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Delete all cache files.

	.. php:method:: close()

		:rtype:	void

		Close the DB Connection.

	.. php:method:: display_error([$error = ''[, $swap = ''[, $native = FALSE]]])

		:param	string	$error: The error message
		:param	string	$swap: Any "swap" values
		:param	bool	$native: Whether to localize the message
		:rtype:	void

		:returns:	Displays the DB error screensends the application/views/errors/error_db.php template
                :rtype:	string

		Display an error message and stop script execution.

		The message is displayed using the
		*application/views/errors/error_db.php* template.

	.. php:method:: protect_identifiers($item[, $prefix_single = FALSE[, $protect_identifiers = NULL[, $field_exists = TRUE]]])

		:param	string	$item: The item to work with
		:param	bool	$prefix_single: Whether to apply the dbprefix even if the input item is a single identifier
		:param	bool	$protect_identifiers: Whether to quote identifiers
		:param	bool	$field_exists: Whether the supplied item contains a field name or not
		:returns:	The modified item
		:rtype:	string

		Takes a column or table name (optionally with an alias)
		and applies the configured *dbprefix* to it.

		Some logic is necessary in order to deal with
		column names that include the path. 

		Consider a query like this::

			SELECT * FROM hostname.database.table.column AS c FROM hostname.database.table

		Or a query with aliasing::

			SELECT m.member_id, m.member_name FROM members AS m

		Since the column name can include up to four segments
		(host, DB, table, column) or also have an alias prefix,
		we need to do a bit of work to figure this out and
		insert the table prefix (if it exists) in the proper
		position, and escape only the correct identifiers.

		This method is used extensively by the Query Builder class.
