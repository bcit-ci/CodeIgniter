######################
Database Configuration
######################

CodeIgniter has a config file that lets you store your database
connection values (username, password, database name, etc.). The config
file is located at application/config/database.php. You can also set
database connection values for specific
:doc:`environments <../libraries/config>` by placing **database.php**
in the respective environment config folder.

The config settings are stored in a multi-dimensional array with this
prototype::

	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'database_name',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => TRUE,
		'db_debug' => TRUE,
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array()
	);

Some database drivers (such as PDO, PostgreSQL, Oracle, ODBC) might
require a full DSN string to be provided. If that is the case, you
should use the 'dsn' configuration setting, as if you're using the
driver's underlying native PHP extension, like this::

	// PDO
	$db['default']['dsn'] = 'pgsql:host=localhost;port=5432;dbname=database_name';

	// Oracle
	$db['default']['dsn'] = '//localhost/XE';

.. note:: If you do not specify a DSN string for a driver that requires it, CodeIgniter
	will try to build it with the rest of the provided settings.

.. note:: If you provide a DSN string and it is missing some valid settings (e.g. the
	database character set), which are present in the rest of the configuration
	fields, CodeIgniter will append them.

You can also specify failovers for the situation when the main connection cannot connect for some reason.
These failovers can be specified by setting the failover for a connection like this::

	$db['default']['failover'] = array(
			array(
				'hostname' => 'localhost1',
				'username' => '',
				'password' => '',
				'database' => '',
				'dbdriver' => 'mysqli',
				'dbprefix' => '',
				'pconnect' => TRUE,
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'cachedir' => '',
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'swap_pre' => '',
				'encrypt' => FALSE,
				'compress' => FALSE,
				'stricton' => FALSE
			),
			array(
				'hostname' => 'localhost2',
				'username' => '',
				'password' => '',
				'database' => '',
				'dbdriver' => 'mysqli',
				'dbprefix' => '',
				'pconnect' => TRUE,
				'db_debug' => TRUE,
				'cache_on' => FALSE,
				'cachedir' => '',
				'char_set' => 'utf8',
				'dbcollat' => 'utf8_general_ci',
				'swap_pre' => '',
				'encrypt' => FALSE,
				'compress' => FALSE,
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

	$db['test'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'database_name',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => TRUE,
		'db_debug' => TRUE,
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'compress' => FALSE,
		'encrypt' => FALSE,
		'stricton' => FALSE,
		'failover' => array()
	);

Then, to globally tell the system to use that group you would set this
variable located in the config file::

	$active_group = 'test';

.. note:: The name 'test' is arbitrary. It can be anything you want. By
	default we've used the word "default" for the primary connection,
	but it too can be renamed to something more relevant to your project.

Query Builder
-------------

The :doc:`Query Builder Class <query_builder>` is globally enabled or
disabled by setting the $query_builder variable in the database
configuration file to TRUE/FALSE (boolean). The default setting is TRUE.
If you are not using the
query builder class, setting it to FALSE will utilize fewer resources
when the database classes are initialized.

::

	$query_builder = TRUE;

.. note:: that some CodeIgniter classes such as Sessions require Query
	Builder to be enabled to access certain functionality.

Explanation of Values:
----------------------

======================  ===========================================================================================================
 Name Config             Description
======================  ===========================================================================================================
**dsn**			The DSN connect string (an all-in-one configuration sequence).
**hostname** 		The hostname of your database server. Often this is 'localhost'.
**username**		The username used to connect to the database.
**password**		The password used to connect to the database.
**database**		The name of the database you want to connect to.
**dbdriver**		The database type. ie: mysqli, postgre, odbc, etc. Must be specified in lower case.
**dbprefix**		An optional table prefix which will added to the table name when running
			:doc:`Query Builder <query_builder>` queries. This permits multiple CodeIgniter
			installations to share one database.
**pconnect**		TRUE/FALSE (boolean) - Whether to use a persistent connection.
**db_debug**		TRUE/FALSE (boolean) - Whether database errors should be displayed.
**cache_on**		TRUE/FALSE (boolean) - Whether database query caching is enabled,
			see also :doc:`Database Caching Class <caching>`.
**cachedir**		The absolute server path to your database query cache directory.
**char_set**		The character set used in communicating with the database.
**dbcollat**		The character collation used in communicating with the database

			.. note:: Only used in the 'mysql' and 'mysqli' drivers.

**swap_pre**		A default table prefix that should be swapped with dbprefix. This is useful for distributed
			applications where you might run manually written queries, and need the prefix to still be
			customizable by the end user.
**schema**		The database schema, defaults to 'public'. Used by PostgreSQL and ODBC drivers.
**encrypt**		Whether or not to use an encrypted connection.

			  - 'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
			  - 'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
			  
			    - 'ssl_key'    - Path to the private key file
			    - 'ssl_cert'   - Path to the public key certificate file
			    - 'ssl_ca'     - Path to the certificate authority file
			    - 'ssl_capath' - Path to a directory containing trusted CA certificates in PEM format
			    - 'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
			    - 'ssl_verify' - TRUE/FALSE; Whether to verify the server certificate or not ('mysqli' only)

**compress**		Whether or not to use client compression (MySQL only).
**stricton**		TRUE/FALSE (boolean) - Whether to force "Strict Mode" connections, good for ensuring strict SQL
			while developing an application.
**port**		The database port number. To use this value you have to add a line to the database config array.
			::

				$db['default']['port'] = 5432;

======================  ===========================================================================================================

.. note:: Depending on what database platform you are using (MySQL, PostgreSQL,
	etc.) not all values will be needed. For example, when using SQLite you
	will not need to supply a username or password, and the database name
	will be the path to your database file. The information above assumes
	you are using MySQL.
