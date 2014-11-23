#############
Updating Data
#############

$this->db->update()
===================

Generates an update string and runs the query based on the data you
supply. You can pass an **array** or an **object** to the function. Here
is an example using an array::

	$data = array(
		'title' => $title,
		'name' => $name,
		'date' => $date
	);

	$this->db->where('id', $id);
	$this->db->update('mytable', $data);
	// Produces: // UPDATE mytable  // SET title = '{$title}', name = '{$name}', date = '{$date}' // WHERE id = $id

Or you can supply an object::

	/*
	class Myclass {
		public $title = 'My Title';
		public $content = 'My Content';
		public $date = 'My Date';
	}
	*/

	$object = new Myclass;
	$this->db->where('id', $id);
	$this->db->update('mytable', $object);
	// Produces: // UPDATE mytable  // SET title = '{$title}', name = '{$name}', date = '{$date}' // WHERE id = $id

.. note:: All values are escaped automatically producing safer queries.

You'll notice the use of the $this->db->where() function, enabling you
to set the WHERE clause. You can optionally pass this information
directly into the update function as a string::

	$this->db->update('mytable', $data, "id = 4");

Or as an array::

	$this->db->update('mytable', $data, array('id' => $id));

You may also use the $this->db->set() function described above when
performing updates.


$this->db->update_batch()
=========================

Generates an update string based on the data you supply, and runs the query.
You can either pass an **array** or an **object** to the function.
Here is an example using an array::

	$data = array(
	   array(
	      'title' => 'My title' ,
	      'name' => 'My Name 2' ,
	      'date' => 'My date 2'
	   ),
	   array(
	      'title' => 'Another title' ,
	      'name' => 'Another Name 2' ,
	      'date' => 'Another date 2'
	   )
	);

	$this->db->update_batch('mytable', $data, 'title');

	// Produces:
	// UPDATE `mytable` SET `name` = CASE
	// WHEN `title` = 'My title' THEN 'My Name 2'
	// WHEN `title` = 'Another title' THEN 'Another Name 2'
	// ELSE `name` END,
	// `date` = CASE
	// WHEN `title` = 'My title' THEN 'My date 2'
	// WHEN `title` = 'Another title' THEN 'Another date 2'
	// ELSE `date` END
	// WHERE `title` IN ('My title','Another title')

The first parameter will contain the table name, the second is an associative
array of values, the third parameter is the where key.

.. note:: All values are escaped automatically producing safer queries.

.. note:: ``affected_rows()`` won't give you proper results with this method,
	due to the very nature of how it works. Instead, ``update_batch()``
	returns the number of rows affected.

$this->db->get_compiled_update()
================================

This works exactly the same way as ``$this->db->get_compiled_insert()`` except
that it produces an UPDATE SQL string instead of an INSERT SQL string.

For more information view documentation for `$this->db->get_compiled_insert()`.

.. note:: This method doesn't work for batched updates.

