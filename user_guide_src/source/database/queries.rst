#######
Queries
#######

$this->db->query();
===================

To submit a query, use the following function::

	$this->db->query('YOUR QUERY HERE');

The query() function returns a database result **object** when "read"
type queries are run, which you can use to :doc:`show your
results <results>`. When "write" type queries are run it simply
returns TRUE or FALSE depending on success or failure. When retrieving
data you will typically assign the query to your own variable, like
this::

	$query = $this->db->query('YOUR QUERY HERE');

$this->db->simple_query();
===========================

This is a simplified version of the $this->db->query() method. It DOES
NOT return a database result set, nor does it set the query timer, or
compile bind data, or store your query for debugging. It simply lets you
submit a query. Most users will rarely use this function.

It returns whatever the database drivers' "execute" function returns.
That typically is TRUE/FALSE on success or failure for write type queries
such as INSERT, DELETE or UPDATE statements (which is what it really
should be used for) and a resource/object on success for queries with
fetchable results.

::

	if ($this->db->simple_query('YOUR QUERY'))
	{
		echo "Success!";
	}
	else
	{
		echo "Query failed!";
	}

.. note:: PostgreSQL's pg_exec() function always returns a resource on
	success, even for write type queries. So take that in mind if
	you're looking for a boolean value.

***************************************
Working with Database prefixes manually
***************************************

If you have configured a database prefix and would like to prepend it to
a table name for use in a native SQL query for example, then you can use
the following::

	$this->db->dbprefix('tablename'); // outputs prefix_tablename


If for any reason you would like to change the prefix programatically
without needing to create a new connection, you can use this method::

	$this->db->set_dbprefix('newprefix');
	$this->db->dbprefix('tablename'); // outputs newprefix_tablename


**********************
Protecting identifiers
**********************

In many databases it is advisable to protect table and field names - for
example with backticks in MySQL. **Query Builder queries are
automatically protected**, however if you need to manually protect an
identifier you can use::

	$this->db->protect_identifiers('table_name');


This function will also add a table prefix to your table, assuming you
have a prefix specified in your database config file. To enable the
prefixing set TRUE (boolen) via the second parameter::

	$this->db->protect_identifiers('table_name', TRUE);


****************
Escaping Queries
****************

It's a very good security practice to escape your data before submitting
it into your database. CodeIgniter has three methods that help you do
this:

#. **$this->db->escape()** This function determines the data type so
   that it can escape only string data. It also automatically adds
   single quotes around the data so you don't have to:
   ::

	$sql = "INSERT INTO table (title) VALUES(".$this->db->escape($title).")";

#. **$this->db->escape_str()** This function escapes the data passed to
   it, regardless of type. Most of the time you'll use the above
   function rather than this one. Use the function like this:
   ::

	$sql = "INSERT INTO table (title) VALUES('".$this->db->escape_str($title)."')";

#. **$this->db->escape_like_str()** This method should be used when
   strings are to be used in LIKE conditions so that LIKE wildcards
   ('%', '\_') in the string are also properly escaped.

::

	$search = '20% raise'; $sql = "SELECT id FROM table WHERE column LIKE '%".$this->db->escape_like_str($search)."%'";


**************
Query Bindings
**************

Bindings enable you to simplify your query syntax by letting the system
put the queries together for you. Consider the following example::

	$sql = "SELECT * FROM some_table WHERE id = ? AND status = ? AND author = ?";
	$this->db->query($sql, array(3, 'live', 'Rick'));

The question marks in the query are automatically replaced with the
values in the array in the second parameter of the query function.

The secondary benefit of using binds is that the values are
automatically escaped, producing safer queries. You don't have to
remember to manually escape data; the engine does it automatically for
you.

***************
Handling Errors
***************

$this->db->error();
===================

If you need to get the last error that has occured, the error() method
will return an array containing its code and message. Here's a quick
example::

	if ( ! $this->db->simple_query('SELECT `example_field` FROM `example_table`'))
	{
		$error = $this->db->error(); // Has keys 'code' and 'message'
	}

