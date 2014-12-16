###################
DB Driver Reference
###################

This is the platform-independent base DB implementation class.
This class will not be called directly. Rather, the adapter
class for the specific database will extend and instantiate it.

The how-to material for this has been split over several articles.
This article is intended to be a reference for them.

.. class:: CI_DB_driver

	.. method:: initialize()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	boolean

		Initialize Database Settings; 
                establish a connection to the database.

	.. method:: db_pconnect()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	boolean

		Establish a persistent database connection

	.. method:: reconnect()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	boolean

		Keep / reestablish the db connection if no queries have been
                sent for a length of time exceeding the server's idle timeout.

                This is only available in drivers that support it.

	.. method:: db_select()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	boolean

		Select database

	.. method:: db_set_charset($charset)

		:param	string	$charset: Character set name
		:returns:	TRUE on success, FALSE on failure
		:rtype:	boolean

		Set client character set

	.. method:: platform()

		:returns:	Platform name
		:rtype:	string

		The name of the platform in use (mysql, mssql, etc...)

	.. method:: version()

		:returns:	The version of the database being used.
		:rtype:	string

		Database version number

	.. method:: query($sql[, $binds = FALSE[, $return_object = NULL]]])

		:param	string	$sql: The SQL statement to execute
		:param	array	$binds: An array of binding data
		:param	bool	$return_object: Character set name
		:returns:	True on "update" success, DB_result object on "query" success, FALSE on failure
		:rtype:	mixed

		Execute the query

                Accepts an SQL string as input and returns a result object 
                upon
                successful execution of a "read" type query. Returns boolean 
                TRUE
                upon successful execution of a "write" type query. 
                Returns boolean
                FALSE upon failure, and if the $db_debug variable is set 
                to TRUE
                will raise an error.

	.. method:: load_rdriver()

		:returns:	The DB_result object appropriate for the driver in use
		:rtype:	DB_result

		Load the result drivers

	.. method:: simple_query($sql)

		:param	string	$sql: The SQL statement to execute
		:returns:	True on "update" success, DB_result object on "query" success, FALSE on failure
		:rtype:	mixed

		Simple Query

                This is a simplified version of the query() function. Internally
                we only use it when running transaction commands since they do
                not require all the features of the main query() function.

	.. method:: trans_off()

		:rtype:	void

		Disable Transactions.

                This permits transactions to be disabled at run-time.

	.. method:: trans_strict([$mode = TRUE])

		:param	boolean	$mode: TRUE for strict mode
		:rtype:	void

		Enable/disable Transaction Strict Mode.

                When strict mode is enabled, if you are running multiple 
                groups of
                transactions, if one group fails all groups will be rolled back.
                If strict mode is disabled, each group is treated autonomously, 
                meaning
                a failure of one group will not affect any others.

	.. method:: trans_start([$test_mode = FALSE[)

		:param	boolean	$test_mode: TRUE for testing mode
		:rtype:	void

		Start Transaction.

	.. method:: trans_complete()

		:rtype:	void

		Complete Transaction.

	.. method:: trans_status()

                :returns:   TRUE if the transaction succeeded, FALSE if it failed
		:rtype:	boolean

		Lets you retrieve the transaction flag to determine if it
                has failed.

	.. method:: compile_binds($sql, $binds)

		:param	string	$sql: The SQL statement 
		:param	array	$binds: An array of binding data
		:returns:	The updated SQL statement
		:rtype:	string

		Compile Bindings

	.. method:: is_write_type($sql)

		:param	string	$sql: The SQL statement 
		:returns:	TRUE if the SQL statement is a "write" type of statement
		:rtype:	boolean

		Determines if a query is a "write" type.

	.. method:: elapsed_time([$decimals = 6])

		:param	int	$decimals: The number of decimal places
		:returns:	The aggregate query elapsed time, in microseconds
		:rtype:	string

		Calculate the aggregate query elapsed time.

	.. method:: total_queries()

		:returns:	The total number of queries
		:rtype:	int

		Returns the total number of queries.

	.. method:: last_query()

		:returns:	The last query executed
		:rtype:	string

		Returns the last query that was executed.

	.. method:: escape($str)

		:param	string	$str: The string to work with
		:returns:	The escaped string
		:rtype:	mixed

		"Smart" Escape String.

                Escapes data based on type.
                Sets boolean and null types

	.. method:: escape_str($str[, $like = FALSE])

		:param	mixed	$str: The string or array of strings to work with
                :param	boolean	$like: Whether or not the string will be used in a LIKE condition
		:returns:	The escaped string(s)
		:rtype:	mixed

		Escape String.

	.. method:: escape_like_str($str)

		:param	mixed	$str: The string or array of strings to work with
                :returns:	The escaped string(s)
		:rtype:	mixed

		Escape LIKE String.
                
                Calls the individual driver for platform
                specific escaping for LIKE conditions.

	.. method:: primary([$table = ''])

		:param	string	$table: The table name
                :returns:	The primary key name, FALSE if none
		:rtype:	mixed

		Primary.
                
                Retrieves the primary key. It assumes that the row in the first
                position is the primary key.

	.. method:: count_all([$table = ''])

		:param	string	$table: The table name
                :returns:	Record count for specified table
		:rtype:	int

		"Count All" query.
                
                Generates a platform-specific query string that counts 
                all records in
                the specified database.

	.. method:: list_tables([$constrain_by_prefix = FALSE])

		:param	boolean	$constrain_by_prefix: TRUE to constrain the tables considered
                :returns:	Array of table names, FALSE if the operation is unsupported
		:rtype:	mixed

		Returns an array of table names.

	.. method:: table_exists($table_name)

		:param	string	$table_name: The table name
                :returns:	TRUE if that table exists
		:rtype:	boolean

		Determine if a particular table exists.

	.. method:: list_fields([$table = ''])

		:param	string	$table: The table name
                :returns:	Array of field names, FALSE if the table doesn't exist or the operation is un-supported
		:rtype:	mixed

		Fetch Field Names.

	.. method:: field_exists($field_name, $table_name)

		:param	string	$table_name: The table name
                :param	string	$field_name: The field name
                :returns:	TRUE if that field exists in that table
		:rtype:	boolean

		Determine if a particular field exists.

	.. method:: field_data([$table = ''])

		:param	string	$table: The table name
                :returns:	Object with field data, FALSE if no table given
		:rtype:	mixed

		Returns an object with field data.

	.. method:: escape_identifiers($item)

		:param	mixed	$item: The item or array of items to escape
                :returns:	The item(s), escaped
		:rtype:	mixed

		Escape the SQL Identifiers.
                
                This function escapes column and table names

	.. method:: insert_string($table, $data)

		:param	string	$table: The table upon which the query will be performed
                :param	array	$data: an associative array of data key/values
                :returns:	The SQL insert string
		:rtype:	string

		Generate an insert string.

	.. method:: update_string($table, $data, $where)

		:param	string	$table: The table upon which the query will be performed
                :param	array	$data: an associative array of data key/values
                :param	mixed	$where: the "where" statement
                :returns:	The SQL update string
		:rtype:	string

		Generate an update string.

	.. method:: call_function($function)

		:param	string	$function: Function name
                :returns:	The function result
		:rtype:	string

		Enables a native PHP function to be run, using a platform 
                agnostic wrapper.

	.. method:: cache_set_path([$path = ''])

		:param	string	$path: the path to the cache directory
                :rtype:	void

		Set Cache Directory Path.

	.. method:: cache_on()

                :returns:	cache_on value
                :rtype:	boolean

		Enable Query Caching.

	.. method:: cache_off()

                :returns:	cache_on value
                :rtype:	boolean

		Disable Query Caching.

	.. method:: cache_delete([$segment_one = ''[, $segment_two = '']])

                :param	string	$segment_one: first URI segment
                :param	string	$segment_two: second URI segment
                :returns:	TRUE on success, FALSE on failure
                :rtype:	boolean

		Delete the cache files associated with a particular URI

	.. method:: cache_delete_all()

                :returns:	TRUE on success, FALSE on failure
                :rtype:	boolean

		Delete All cache files

	.. method:: close()

                :rtype:	void

		Close DB Connection

	.. method:: display_error([$error = ''[, $swap = ''[, $native = FALSE]]])

                :param	string	$error: the error message
                :param	string	$swap: any "swap" values
                :param	boolean	$native: whether to localize the message
                :returns:   sends the application/views/errors/error_db.php template
                :rtype:	string

		Display an error message

	.. method:: protect_identifiers($item[, $prefix_single = FALSE[, $protect_identifiers = NULL[, $field_exists = TRUE]]])

                :param	string	$item: the item
                :param	boolean	$prefix_single: whether to use a single prefix
                :param	boolean	$protect_identifiers: whether to protect identifiers
                :param	boolean	$field_exists: whether the supplied item does not contain a field name.
                :returns:   the modified item
                :rtype:	string

		Protect Identifiers.
                
                This function is used extensively by the Query Builder class, 
                and by
                a couple functions in this class.
                It takes a column or table name (optionally with an alias) 
                and inserts
                the table prefix onto it. Some logic is necessary in order 
                to deal with
                column names that include the path. Consider a query like this::
                
                        SELECT * FROM hostname.database.table.column AS c FROM hostname.database.table
                
                Or a query with aliasing::
                
                        SELECT m.member_id, m.member_name FROM members AS m
                
                Since the column name can include up to four segments 
                (host, DB, table, column)
                or also have an alias prefix, we need to do a bit of work 
                to figure this out and
                insert the table prefix (if it exists) in the proper position, 
                and escape only
                the correct identifiers.