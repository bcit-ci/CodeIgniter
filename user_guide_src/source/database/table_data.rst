##########
Table Data
##########

These functions let you fetch table information.

$this->db->list_tables();
==========================

Returns an array containing the names of all the tables in the database
you are currently connected to. Example::

	$tables = $this->db->list_tables();
	
	foreach ($tables as $table)
	{
		echo $table;
	}

$this->db->table_exists();
===========================

Sometimes it's helpful to know whether a particular table exists before
running an operation on it. Returns a boolean TRUE/FALSE. Usage example::

	if ($this->db->table_exists('table_name'))
	{
		// some code...
	}

.. note:: Replace *table_name* with the name of the table you are looking for.
