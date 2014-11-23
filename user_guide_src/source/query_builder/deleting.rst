#############
Deleting Data
#############

$this->db->delete()
===================

Generates a delete SQL string and runs the query.

::

	$this->db->delete('mytable', array('id' => $id));  // Produces: // DELETE FROM mytable  // WHERE id = $id

The first parameter is the table name, the second is the where clause.
You can also use the where() or or_where() functions instead of passing
the data to the second parameter of the function::

	$this->db->where('id', $id);
	$this->db->delete('mytable');

	// Produces:
	// DELETE FROM mytable
	// WHERE id = $id


An array of table names can be passed into delete() if you would like to
delete data from more than 1 table.

::

	$tables = array('table1', 'table2', 'table3');
	$this->db->where('id', '5');
	$this->db->delete($tables);


If you want to delete all data from a table, you can use the truncate()
function, or empty_table().

$this->db->empty_table()
========================

Generates a delete SQL string and runs the
query.::

	  $this->db->empty_table('mytable'); // Produces: DELETE FROM mytable


$this->db->truncate()
=====================

Generates a truncate SQL string and runs the query.

::

	$this->db->from('mytable');
	$this->db->truncate();

	// or

	$this->db->truncate('mytable');

	// Produce:
	// TRUNCATE mytable

.. note:: If the TRUNCATE command isn't available, truncate() will
	execute as "DELETE FROM table".

$this->db->get_compiled_delete()
================================
This works exactly the same way as ``$this->db->get_compiled_insert()`` except
that it produces a DELETE SQL string instead of an INSERT SQL string.

For more information view documentation for `$this->db->get_compiled_insert()`_.

