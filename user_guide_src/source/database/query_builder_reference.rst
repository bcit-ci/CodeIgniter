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

