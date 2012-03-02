##########
Index Data
##########

$this->db->list_index()
=========================

Returns an array of objects containing index information.

.. note:: Not all databases provide meta-data.

Usage example::

	$indexes = $this->db->list_index('table_name');
	
	foreach ($indexes as $index)
	{
		echo $index->name;
		echo $index->column;
		echo $index->type;
		echo $index->comment;
	}

