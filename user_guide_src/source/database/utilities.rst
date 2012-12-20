######################
Database Utility Class
######################

The Database Utility Class contains methods that help you manage your
database.

.. contents:: Table of Contents


******************
Function Reference
******************

Initializing the Utility Class
==============================

.. important:: In order to initialize the Utility class, your database
	driver must already be running, since the utilities class relies on it.

Load the Utility Class as follows::

	$this->load->dbutil()

You can also pass another database object to the DB Utility loader, in case
the database you want to manage isn't the default one::

	$this->myutil = $this->load->dbutil($this->other_db, TRUE);

In the above example, we're passing a custom database object as the first
parameter and then tell it to return the dbutil object, instead of
assigning it directly to ``$this->dbutil``.

.. note:: Both of the parameters can be used individually, just pass an empty
	value as the first one if you wish to skip it.

Once initialized you will access the methods using the ``$this->dbutil``
object::

	$this->dbutil->some_method()

$this->dbutil->list_databases();
================================

Returns an array of database names::

	$dbs = $this->dbutil->list_databases();

	foreach ($dbs as $db)
	{
 		echo $db;
	}

$this->dbutil->database_exists();
=================================

Sometimes it's helpful to know whether a particular database exists.
Returns a boolean TRUE/FALSE. Usage example::

	if ($this->dbutil->database_exists('database_name'))
	{
		// some code...
	}

.. note:: Replace *database_name* with the name of the table you are
	looking for. This method is case sensitive.

$this->dbutil->optimize_table('table_name');
============================================

Permits you to optimize a table using the table name specified in the
first parameter. Returns TRUE/FALSE based on success or failure::

	if ($this->dbutil->optimize_table('table_name'))
	{
		echo 'Success!';
	}

.. note:: Not all database platforms support table optimization. It is
	mostly for use with MySQL.

$this->dbutil->repair_table('table_name');
==========================================

Permits you to repair a table using the table name specified in the
first parameter. Returns TRUE/FALSE based on success or failure::

	if ($this->dbutil->repair_table('table_name'))
	{
		echo 'Success!';
	}

.. note:: Not all database platforms support table repairs.

$this->dbutil->optimize_database();
====================================

Permits you to optimize the database your DB class is currently
connected to. Returns an array containing the DB status messages or
FALSE on failure.

::

	$result = $this->dbutil->optimize_database();

	if ($result !== FALSE)
	{
		print_r($result);
	}

.. note:: Not all database platforms support table optimization. It
	it is mostly for use with MySQL.

$this->dbutil->csv_from_result($db_result);
===========================================

Permits you to generate a CSV file from a query result. The first
parameter of the method must contain the result object from your
query. Example::

	$this->load->dbutil();

	$query = $this->db->query("SELECT * FROM mytable");

	echo $this->dbutil->csv_from_result($query);

The second, third, and fourth parameters allow you to set the delimiter
newline, and enclosure characters respectively. By default commas are
used as the delimiter, "\n" is used as a new line, and a double-quote
is used as the enclosure. Example::

	$delimiter = ",";
	$newline = "\r\n";
	$enclosure = '"';

	echo $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);

.. important:: This method will NOT write the CSV file for you. It
	simply creates the CSV layout. If you need to write the file
	use the :doc:`File Helper <../helpers/file_helper>`.

$this->dbutil->xml_from_result($db_result);
===========================================

Permits you to generate an XML file from a query result. The first
parameter expects a query result object, the second may contain an
optional array of config parameters. Example::

	$this->load->dbutil();

	$query = $this->db->query("SELECT * FROM mytable");

	$config = array (
		'root'		=> 'root',
		'element'	=> 'element',
		'newline'	=> "\n",
		'tab'		=> "\t"
	);

	echo $this->dbutil->xml_from_result($query, $config);

.. important:: This method will NOT write the XML file for you. It
	simply creates the XML layout. If you need to write the file
	use the :doc:`File Helper <../helpers/file_helper>`.

$this->dbutil->backup();
========================

Permits you to backup your full database or individual tables. The
backup data can be compressed in either Zip or Gzip format.

.. note:: This feature is only available for MySQL and Interbase/Firebird databases.

.. note:: For Interbase/Firebird databases, the backup file name is the only parameter.

		Eg. $this->dbutil->backup('db_backup_filename');

.. note:: Due to the limited execution time and memory available to PHP,
	backing up very large databases may not be possible. If your database is
	very large you might need to backup directly from your SQL server via
	the command line, or have your server admin do it for you if you do not
	have root privileges.

Usage Example
-------------

::

	// Load the DB utility class
	$this->load->dbutil();

	// Backup your entire database and assign it to a variable
	$backup =& $this->dbutil->backup();

	// Load the file helper and write the file to your server
	$this->load->helper('file');
	write_file('/path/to/mybackup.gz', $backup);

	// Load the download helper and send the file to your desktop
	$this->load->helper('download');
	force_download('mybackup.gz', $backup);

Setting Backup Preferences
--------------------------

Backup preferences are set by submitting an array of values to the first
parameter of the ``backup()`` method. Example::

	$prefs = array(
		'tables'	=> array('table1', 'table2'),	// Array of tables to backup.
		'ignore'	=> array(),			// List of tables to omit from the backup
		'format'	=> 'txt',			// gzip, zip, txt
		'filename'	=> 'mybackup.sql',		// File name - NEEDED ONLY WITH ZIP FILES
		'add_drop'	=> TRUE,			// Whether to add DROP TABLE statements to backup file
		'add_insert'	=> TRUE,			// Whether to add INSERT data to backup file
		'newline'	=> "\n"				// Newline character used in backup file
	);

	$this->dbutil->backup($prefs);

Description of Backup Preferences
---------------------------------

======================= ======================= ======================= ========================================================================
Preference              Default Value           Options                 Description
======================= ======================= ======================= ========================================================================
**tables**               empty array             None                    An array of tables you want backed up. If left blank all tables will be
                                                                         exported.
**ignore**               empty array             None                    An array of tables you want the backup routine to ignore.
**format**               gzip                    gzip, zip, txt          The file format of the export file.
**filename**             the current date/time   None                    The name of the backed-up file. The name is needed only if you are using
                                                                         zip compression.
**add_drop**             TRUE                    TRUE/FALSE              Whether to include DROP TABLE statements in your SQL export file.
**add_insert**           TRUE                    TRUE/FALSE              Whether to include INSERT statements in your SQL export file.
**newline**              "\\n"                   "\\n", "\\r", "\\r\\n"  Type of newline to use in your SQL export file.
**foreign_key_checks**   TRUE                    TRUE/FALSE              Whether output should keep foreign key checks enabled.
======================= ======================= ======================= ========================================================================