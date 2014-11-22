##############
Inserting Data
##############

$this->db->insert()
===================

Generates an insert string based on the data you supply, and runs the
query. You can either pass an **array** or an **object** to the
function. Here is an example using an array::

	$data = array(
		'title' => 'My title',
		'name' => 'My Name',
		'date' => 'My date'
	);

	$this->db->insert('mytable', $data);
	// Produces: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')

The first parameter will contain the table name, the second is an
associative array of values.

Here is an example using an object::

	/*
	class Myclass {
		public $title = 'My Title';
		public $content = 'My Content';
		public $date = 'My Date';
	}
	*/

	$object = new Myclass;
	$this->db->insert('mytable', $object);
	// Produces: INSERT INTO mytable (title, content, date) VALUES ('My Title', 'My Content', 'My Date')

The first parameter will contain the table name, the second is an
object.

.. note:: All values are escaped automatically producing safer queries.

$this->db->get_compiled_insert()
================================
Compiles the insertion query just like `$this->db->insert()`_ but does not
*run* the query. This method simply returns the SQL query as a string.

Example::

	$data = array(
		'title' => 'My title',
		'name'  => 'My Name',
		'date'  => 'My date'
	);

	$sql = $this->db->set($data)->get_compiled_insert('mytable');
	echo $sql;

	// Produces string: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')

The second parameter enables you to set whether or not the query builder query
will be reset (by default it will be--just like `$this->db->insert()`_)::

	echo $this->db->set('title', 'My Title')->get_compiled_insert('mytable', FALSE);

	// Produces string: INSERT INTO mytable (title) VALUES ('My Title')

	echo $this->db->set('content', 'My Content')->get_compiled_insert();

	// Produces string: INSERT INTO mytable (title, content) VALUES ('My Title', 'My Content')

The key thing to notice in the above example is that the second query did not
utlize `$this->db->from()` nor did it pass a table name into the first
parameter. The reason this worked is because the query has not been executed
using `$this->db->insert()` which resets values or reset directly using
`$this->db->reset_query()`.

.. note:: This method doesn't work for batched inserts.

$this->db->insert_batch()
=========================

Generates an insert string based on the data you supply, and runs the
query. You can either pass an **array** or an **object** to the
function. Here is an example using an array::

	$data = array(
		array(
			'title' => 'My title',
			'name' => 'My Name',
			'date' => 'My date'
		),
		array(
			'title' => 'Another title',
			'name' => 'Another Name',
			'date' => 'Another date'
		)
	);

	$this->db->insert_batch('mytable', $data);
	// Produces: INSERT INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date'),  ('Another title', 'Another name', 'Another date')

The first parameter will contain the table name, the second is an
associative array of values.

.. note:: All values are escaped automatically producing safer queries.

$this->db->replace()
====================

This method executes a REPLACE statement, which is basically the SQL
standard for (optional) DELETE + INSERT, using *PRIMARY* and *UNIQUE*
keys as the determining factor.
In our case, it will save you from the need to implement complex
logics with different combinations of  ``select()``, ``update()``,
``delete()`` and ``insert()`` calls.

Example::

	$data = array(
		'title' => 'My title',
		'name'  => 'My Name',
		'date'  => 'My date'
	);

	$this->db->replace('table', $data);

	// Executes: REPLACE INTO mytable (title, name, date) VALUES ('My title', 'My name', 'My date')

In the above example, if we assume that the *title* field is our primary
key, then if a row containing 'My title' as the *title* value, that row
will be deleted with our new row data replacing it.

Usage of the ``set()`` method is also allowed and all fields are
automatically escaped, just like with ``insert()``.

$this->db->set()
================

This function enables you to set values for inserts or updates.

**It can be used instead of passing a data array directly to the insert
or update functions:**

::

	$this->db->set('name', $name);
	$this->db->insert('mytable');  // Produces: INSERT INTO mytable (name) VALUES ('{$name}')

If you use multiple function called they will be assembled properly
based on whether you are doing an insert or an update::

	$this->db->set('name', $name);
	$this->db->set('title', $title);
	$this->db->set('status', $status);
	$this->db->insert('mytable');

**set()** will also accept an optional third parameter ($escape), that
will prevent data from being escaped if set to FALSE. To illustrate the
difference, here is set() used both with and without the escape
parameter.

::

	$this->db->set('field', 'field+1', FALSE);
	$this->db->insert('mytable'); // gives INSERT INTO mytable (field) VALUES (field+1)
	$this->db->set('field', 'field+1');
	$this->db->insert('mytable'); // gives INSERT INTO mytable (field) VALUES ('field+1')


You can also pass an associative array to this function::

	$array = array(
		'name' => $name,
		'title' => $title,
		'status' => $status
	);

	$this->db->set($array);
	$this->db->insert('mytable');

Or an object::

	/*
	class Myclass {
		public $title = 'My Title';
		public $content = 'My Content';
		public $date = 'My Date';
	}
	*/

	$object = new Myclass;
	$this->db->set($object);
	$this->db->insert('mytable');

