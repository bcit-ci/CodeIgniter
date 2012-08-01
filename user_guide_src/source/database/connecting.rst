###########################
Connecting to your Database
###########################

There are two ways to connect to a database:

Automatically Connecting
========================

The "auto connect" feature will load and instantiate the database class
with every page load. To enable "auto connecting", add the word database
to the library array, as indicated in the following file:

application/config/autoload.php

Manually Connecting
===================

If only some of your pages require database connectivity you can
manually connect to your database by adding this line of code in any
function where it is needed, or in your class constructor to make the
database available globally in that class.

::

	$this->load->database();

If the above function does **not** contain any information in the first
parameter it will connect to the group specified in your database config
file. For most people, this is the preferred method of use.

Available Parameters
--------------------

#. The database connection values, passed either as an array or a DSN
   string.
#. TRUE/FALSE (boolean). Whether to return the connection ID (see
   Connecting to Multiple Databases below).
#. TRUE/FALSE (boolean). Whether to enable the Query Builder class. Set
   to TRUE by default.

Manually Connecting to a Database
---------------------------------

The first parameter of this function can **optionally** be used to
specify a particular database group from your config file, or you can
even submit connection values for a database that is not specified in
your config file. Examples:

To choose a specific group from your config file you can do this::

	$this->load->database('group_name');

Where group_name is the name of the connection group from your config
file.

To connect manually to a desired database you can pass an array of
values::

	$config['hostname'] = 'localhost';
	$config['username'] = 'myusername';
	$config['password'] = 'mypassword';
	$config['database'] = 'mydatabase';
	$config['dbdriver'] = 'mysqli';
	$config['dbprefix'] = '';
	$config['pconnect'] = FALSE;
	$config['db_debug'] = TRUE;
	$config['cache_on'] = FALSE;
	$config['cachedir'] = '';
	$config['char_set'] = 'utf8';
	$config['dbcollat'] = 'utf8_general_ci';
	$this->load->database($config);

For information on each of these values please see the :doc:`configuration
page <configuration>`.

.. note:: For the PDO driver, you should use the $config['dsn'] setting
	instead of 'hostname' and 'database':

	|
	| $config['dsn'] = 'mysql:host=localhost;dbname=mydatabase';

Or you can submit your database values as a Data Source Name. DSNs must
have this prototype::

	$dsn = 'dbdriver://username:password@hostname/database';  
	$this->load->database($dsn);

To override default config values when connecting with a DSN string, add
the config variables as a query string.

::

	$dsn = 'dbdriver://username:password@hostname/database?char_set=utf8&dbcollat=utf8_general_ci&cache_on=true&cachedir=/path/to/cache';  
	$this->load->database($dsn);

Connecting to Multiple Databases
================================

If you need to connect to more than one database simultaneously you can
do so as follows::

	$DB1 = $this->load->database('group_one', TRUE); 
	$DB2 = $this->load->database('group_two', TRUE);

Note: Change the words "group_one" and "group_two" to the specific
group names you are connecting to (or you can pass the connection values
as indicated above).

By setting the second parameter to TRUE (boolean) the function will
return the database object.

.. note:: When you connect this way, you will use your object name to issue
	commands rather than the syntax used throughout this guide. In other
	words, rather than issuing commands with:
	
	|
	| $this->db->query();
	| $this->db->result();
	| etc...
	|
	| You will instead use:
	|
	| $DB1->query();
	| $DB1->result();
	| etc...

.. note:: You don't need to create separate database configurations if you
	only need to use a different database on the same connection. You
	can switch to a different database when you need to, like this:

	| $this->db->db_select($database2_name);

Reconnecting / Keeping the Connection Alive
===========================================

If the database server's idle timeout is exceeded while you're doing
some heavy PHP lifting (processing an image, for instance), you should
consider pinging the server by using the reconnect() method before
sending further queries, which can gracefully keep the connection alive
or re-establish it.

::

	$this->db->reconnect();

Manually closing the Connection
===============================

While CodeIgniter intelligently takes care of closing your database
connections, you can explicitly close the connection.

::

	$this->db->close();