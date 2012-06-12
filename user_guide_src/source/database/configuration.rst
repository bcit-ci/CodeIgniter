######################
Database Configuration
######################

CodeIgniter has a config file that lets you store your database
connection values (username, password, database name, etc.). The config
file is located at application/config/database.php. You can also set
database connection values for specific
:doc:`environments <../libraries/config>` by placing **database.php**
it the respective environment config folder.

The config settings are stored in a multi-dimensional array with this
prototype::

	$db['default']['hostname'] = "localhost";
	$db['default']['username'] = "root";
	$db['default']['password'] = "";
	$db['default']['database'] = "database_name";
	$db['default']['dbdriver'] = "mysql";
	$db['default']['dbprefix'] = "";
	$db['default']['pconnect'] = TRUE;
	$db['default']['db_debug'] = FALSE;
	$db['default']['cache_on'] = FALSE;
	$db['default']['cachedir'] =  "";
	$db['default']['char_set'] = "utf8";
	$db['default']['dbcollat'] = "utf8_general_ci";
	$db['default']['swap_pre'] = "";
	$db['default']['autoinit'] = TRUE;
	$db['default']['stricton'] = FALSE;

If you use PDO as your dbdriver, you can specify the full DSN string describe a connection to the database like this::

	$db['default']['dsn'] = 'pgsql:host=localhost;port=5432;dbname=database_name';

You can also specify failovers for the situation when the main connection cannot connect for some reason.
These failovers can be specified by setting the failover for a connection like this::

	$db['default']['failover'] = array(
			array(
				'hostname' => 'localhost1',
				'username' => '',
				'password' => '',
				'database' => '',
				'dbdriver' => 'mysql',
				'dbprefix' => '',
				'pconnect' => TRUE,
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'cachedir' => '',
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'swap_pre' => '',
				'autoinit' => TRUE,
				'stricton' => FALSE
			),
			array(
				'hostname' => 'localhost2',
				'username' => '',
				'password' => '',
				'database' => '',
				'dbdriver' => 'mysql',
				'dbprefix' => '',
				'pconnect' => TRUE,
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'cachedir' => '',
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'swap_pre' => '',
				'autoinit' => TRUE,
				'stricton' => FALSE
			)
		);

You can specify as many failovers as you like.

The reason we use a multi-dimensional array rather than a more simple
one is to permit you to optionally store multiple sets of connection
values. If, for example, you run multiple environments (development,
production, test, etc.) under a single installation, you can set up a
connection group for each, then switch between groups as needed. For
example, to set up a "test" environment you would do this::

	$db['test']['hostname'] = "localhost";
	$db['test']['username'] = "root";
	$db['test']['password'] = "";
	$db['test']['database'] = "database_name";
	$db['test']['dbdriver'] = "mysql";
	$db['test']['dbprefix'] = "";
	$db['test']['pconnect'] = TRUE;
	$db['test']['db_debug'] = FALSE;
	$db['test']['cache_on'] = FALSE;
	$db['test']['cachedir'] =  "";
	$db['test']['char_set'] = "utf8";
	$db['test']['dbcollat'] = "utf8_general_ci";
	$db['test']['swap_pre'] = "";
	$db['test']['autoinit'] = TRUE;
	$db['test']['stricton'] = FALSE;

Then, to globally tell the system to use that group you would set this
variable located in the config file::

	$active_group = "test";

Note: The name "test" is arbitrary. It can be anything you want. By
default we've used the word "default" for the primary connection, but it
too can be renamed to something more relevant to your project.

Query Builder
-------------

The :doc:`Query Builder Class <query_builder>` is globally enabled or
disabled by setting the $query_builder variable in the database
configuration file to TRUE/FALSE (boolean). If you are not using the
query builder class, setting it to FALSE will utilize fewer resources
when the database classes are initialized.

::

	$query_builder = TRUE;

.. note:: that some CodeIgniter classes such as Sessions require Active
	Records be enabled to access certain functionality.

Explanation of Values:
----------------------

======================  ==================================================================================================
 Name Config             Description
======================  ==================================================================================================
**hostname** 		The hostname of your database server. Often this is "localhost".
**username**		The username used to connect to the database.
**password**		The password used to connect to the database.
**database**		The name of the database you want to connect to.
**dbdriver**		The database type. ie: mysql, postgre, odbc, etc. Must be specified in lower case.
**dbprefix**		An optional table prefix which will added to the table name when running :doc:
			`Query Builder <query_builder>` queries. This permits multiple CodeIgniter installations
			to share one database.
**pconnect**		TRUE/FALSE (boolean) - Whether to use a persistent connection.
**db_debug**		TRUE/FALSE (boolean) - Whether database errors should be displayed.
**cache_on**		TRUE/FALSE (boolean) - Whether database query caching is enabled,
			see also :doc:`Database Caching Class <caching>`.
**cachedir**		The absolute server path to your database query cache directory.
**char_set**		The character set used in communicating with the database.
**dbcollat**		The character collation used in communicating with the database

			.. note:: For MySQL and MySQLi databases, this setting is only used
				as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
				(and in table creation queries made with DB Forge). There is an
				incompatibility in PHP with mysql_real_escape_string() which can
				make your site vulnerable to SQL injection if you are using a
				multi-byte character set and are running versions lower than these.
				Sites using Latin-1 or UTF-8 database character set and collation are
				unaffected.

**swap_pre**		A default table prefix that should be swapped with dbprefix. This is useful for distributed
			applications where you might run manually written queries, and need the prefix to still be
			customizable by the end user.
**autoinit**		Whether or not to automatically connect to the database when the library loads. If set to false,
			the connection will take place prior to executing the first query.
**stricton**		TRUE/FALSE (boolean) - Whether to force "Strict Mode" connections, good for ensuring strict SQL
			while developing an application.
**port**		The database port number. To use this value you have to add a line to the database config array.
			::
			
				$db['default']['port'] =  5432;
======================  ==================================================================================================

.. note:: Depending on what database platform you are using (MySQL, PostgreSQL,
	etc.) not all values will be needed. For example, when using SQLite you
	will not need to supply a username or password, and the database name
	will be the path to your database file. The information above assumes
	you are using MySQL.
