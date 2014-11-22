##############
Selecting Data
##############

The following functions allow you to build SQL **SELECT** statements.

$this->db->get()
================

Runs the selection query and returns the result. Can be used by itself
to retrieve all records from a table::

	$query = $this->db->get('mytable');  // Produces: SELECT * FROM mytable

The second and third parameters enable you to set a limit and offset
clause::

	$query = $this->db->get('mytable', 10, 20);
	// Produces: SELECT * FROM mytable LIMIT 20, 10 (in MySQL. Other databases have slightly different syntax)

You'll notice that the above function is assigned to a variable named
$query, which can be used to show the results::

	$query = $this->db->get('mytable');

	foreach ($query->result() as $row)
	{
		echo $row->title;
	}

Please visit the :doc:`result functions <../database/results>` page for a full
discussion regarding result generation.

$this->db->get_compiled_select()
================================

Compiles the selection query just like `$this->db->get()`_ but does not *run*
the query. This method simply returns the SQL query as a string.

Example::

	$sql = $this->db->get_compiled_select('mytable');
	echo $sql;

	// Produces string: SELECT * FROM mytable

The second parameter enables you to set whether or not the query builder query
will be reset (by default it will be reset, just like when using `$this->db->get()`)::

	echo $this->db->limit(10,20)->get_compiled_select('mytable', FALSE);
	// Produces string: SELECT * FROM mytable LIMIT 20, 10
	// (in MySQL. Other databases have slightly different syntax)

	echo $this->db->select('title, content, date')->get_compiled_select();

	// Produces string: SELECT title, content, date FROM mytable LIMIT 20, 10

The key thing to notice in the above example is that the second query did not
utilize `$this->db->from()`_ and did not pass a table name into the first
parameter. The reason for this outcome is because the query has not been
executed using `$this->db->get()`_ which resets values or reset directly
using `$this->db->reset_query()`_.


$this->db->get_where()
======================

Identical to the above function except that it permits you to add a
"where" clause in the second parameter, instead of using the db->where()
function::

	$query = $this->db->get_where('mytable', array('id' => $id), $limit, $offset);

Please read the about the where function below for more information.

.. note:: get_where() was formerly known as getwhere(), which has been removed

$this->db->select()
===================

Permits you to write the SELECT portion of your query::

	$this->db->select('title, content, date');
	$query = $this->db->get('mytable');  // Produces: SELECT title, content, date FROM mytable


.. note:: If you are selecting all (\*) from a table you do not need to
	use this function. When omitted, CodeIgniter assumes you wish to SELECT *

$this->db->select() accepts an optional second parameter. If you set it
to FALSE, CodeIgniter will not try to protect your field or table names
with backticks. This is useful if you need a compound select statement.

::

	$this->db->select('(SELECT SUM(payments.amount) FROM payments WHERE payments.invoice_id=4') AS amount_paid', FALSE);
	$query = $this->db->get('mytable');


$this->db->select_max()
=======================

Writes a "SELECT MAX(field)" portion for your query. You can optionally
include a second parameter to rename the resulting field.

::

	$this->db->select_max('age');
	$query = $this->db->get('members');  // Produces: SELECT MAX(age) as age FROM members

	$this->db->select_max('age', 'member_age');
	$query = $this->db->get('members'); // Produces: SELECT MAX(age) as member_age FROM members


$this->db->select_min()
=======================

Writes a "SELECT MIN(field)" portion for your query. As with
select_max(), You can optionally include a second parameter to rename
the resulting field.

::

	$this->db->select_min('age');
	$query = $this->db->get('members'); // Produces: SELECT MIN(age) as age FROM members


$this->db->select_avg()
=======================

Writes a "SELECT AVG(field)" portion for your query. As with
select_max(), You can optionally include a second parameter to rename
the resulting field.

::

	$this->db->select_avg('age');
	$query = $this->db->get('members'); // Produces: SELECT AVG(age) as age FROM members


$this->db->select_sum()
=======================

Writes a "SELECT SUM(field)" portion for your query. As with
select_max(), You can optionally include a second parameter to rename
the resulting field.

::

	$this->db->select_sum('age');
	$query = $this->db->get('members'); // Produces: SELECT SUM(age) as age FROM members


$this->db->from()
=================

Permits you to write the FROM portion of your query::

	$this->db->select('title, content, date');
	$this->db->from('mytable');
	$query = $this->db->get();  // Produces: SELECT title, content, date FROM mytable

.. note:: As shown earlier, the FROM portion of your query can be specified
	in the $this->db->get() function, so use whichever method you prefer.

$this->db->join()
=================

Permits you to write the JOIN portion of your query::

	$this->db->select('*');
	$this->db->from('blogs');
	$this->db->join('comments', 'comments.id = blogs.id');
	$query = $this->db->get();

	// Produces:
	// SELECT * FROM blogs JOIN comments ON comments.id = blogs.id

Multiple function calls can be made if you need several joins in one
query.

If you need a specific type of JOIN you can specify it via the third
parameter of the function. Options are: left, right, outer, inner, left
outer, and right outer.

::

	$this->db->join('comments', 'comments.id = blogs.id', 'left');
	// Produces: LEFT JOIN comments ON comments.id = blogs.id

$this->db->where()
==================

This function enables you to set **WHERE** clauses using one of four
methods:

.. note:: All values passed to this function are escaped automatically,
	producing safer queries.

#. **Simple key/value method:**

	::

		$this->db->where('name', $name); // Produces: WHERE name = 'Joe'

	Notice that the equal sign is added for you.

	If you use multiple function calls they will be chained together with
	AND between them:

	::

		$this->db->where('name', $name);
		$this->db->where('title', $title);
		$this->db->where('status', $status);
		// WHERE name = 'Joe' AND title = 'boss' AND status = 'active'

#. **Custom key/value method:**
	You can include an operator in the first parameter in order to
	control the comparison:

	::

		$this->db->where('name !=', $name);
		$this->db->where('id <', $id); // Produces: WHERE name != 'Joe' AND id < 45

#. **Associative array method:**

	::

		$array = array('name' => $name, 'title' => $title, 'status' => $status);
		$this->db->where($array);
		// Produces: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'

	You can include your own operators using this method as well:

	::

		$array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);
		$this->db->where($array);

#. **Custom string:**
	You can write your own clauses manually::

		$where = "name='Joe' AND status='boss' OR status='active'";
		$this->db->where($where);


$this->db->where() accepts an optional third parameter. If you set it to
FALSE, CodeIgniter will not try to protect your field or table names
with backticks.

::

	$this->db->where('MATCH (field) AGAINST ("value")', NULL, FALSE);


$this->db->or_where()
=====================

This function is identical to the one above, except that multiple
instances are joined by OR::

	$this->db->where('name !=', $name);
	$this->db->or_where('id >', $id);  // Produces: WHERE name != 'Joe' OR id > 50

.. note:: or_where() was formerly known as orwhere(), which has been
	removed.

$this->db->where_in()
=====================

Generates a WHERE field IN ('item', 'item') SQL query joined with AND if
appropriate

::

	$names = array('Frank', 'Todd', 'James');
	$this->db->where_in('username', $names);
	// Produces: WHERE username IN ('Frank', 'Todd', 'James')


$this->db->or_where_in()
========================

Generates a WHERE field IN ('item', 'item') SQL query joined with OR if
appropriate

::

	$names = array('Frank', 'Todd', 'James');
	$this->db->or_where_in('username', $names);
	// Produces: OR username IN ('Frank', 'Todd', 'James')


$this->db->where_not_in()
=========================

Generates a WHERE field NOT IN ('item', 'item') SQL query joined with
AND if appropriate

::

	$names = array('Frank', 'Todd', 'James');
	$this->db->where_not_in('username', $names);
	// Produces: WHERE username NOT IN ('Frank', 'Todd', 'James')


$this->db->or_where_not_in()
============================

Generates a WHERE field NOT IN ('item', 'item') SQL query joined with OR
if appropriate

::

	$names = array('Frank', 'Todd', 'James');
	$this->db->or_where_not_in('username', $names);
	// Produces: OR username NOT IN ('Frank', 'Todd', 'James')


$this->db->like()
=================

This method enables you to generate **LIKE** clauses, useful for doing
searches.

.. note:: All values passed to this method are escaped automatically.

#. **Simple key/value method:**

	::

		$this->db->like('title', 'match');
		// Produces: WHERE `title` LIKE '%match%' ESCAPE '!'

	If you use multiple method calls they will be chained together with
	AND between them::

		$this->db->like('title', 'match');
		$this->db->like('body', 'match');
		// WHERE `title` LIKE '%match%' ESCAPE '!' AND  `body` LIKE '%match% ESCAPE '!'

	If you want to control where the wildcard (%) is placed, you can use
	an optional third argument. Your options are 'before', 'after' and
	'both' (which is the default).

	::

		$this->db->like('title', 'match', 'before');	// Produces: WHERE `title` LIKE '%match' ESCAPE '!'
		$this->db->like('title', 'match', 'after');	// Produces: WHERE `title` LIKE 'match%' ESCAPE '!'
		$this->db->like('title', 'match', 'both');	// Produces: WHERE `title` LIKE '%match%' ESCAPE '!'

#. **Associative array method:**

	::

		$array = array('title' => $match, 'page1' => $match, 'page2' => $match);
		$this->db->like($array);
		// WHERE `title` LIKE '%match%' ESCAPE '!' AND  `page1` LIKE '%match%' ESCAPE '!' AND  `page2` LIKE '%match%' ESCAPE '!'


$this->db->or_like()
====================

This method is identical to the one above, except that multiple
instances are joined by OR::

	$this->db->like('title', 'match'); $this->db->or_like('body', $match);
	// WHERE `title` LIKE '%match%' ESCAPE '!' OR  `body` LIKE '%match%' ESCAPE '!'

.. note:: ``or_like()`` was formerly known as ``orlike()``, which has been removed.

$this->db->not_like()
=====================

This method is identical to ``like()``, except that it generates
NOT LIKE statements::

	$this->db->not_like('title', 'match');	// WHERE `title` NOT LIKE '%match% ESCAPE '!'

$this->db->or_not_like()
========================

This method is identical to ``not_like()``, except that multiple
instances are joined by OR::

	$this->db->like('title', 'match');
	$this->db->or_not_like('body', 'match');
	// WHERE `title` LIKE '%match% OR  `body` NOT LIKE '%match%' ESCAPE '!'

$this->db->group_by()
=====================

Permits you to write the GROUP BY portion of your query::

	$this->db->group_by("title"); // Produces: GROUP BY title

You can also pass an array of multiple values as well::

	$this->db->group_by(array("title", "date"));  // Produces: GROUP BY title, date

.. note:: group_by() was formerly known as groupby(), which has been
	removed.

$this->db->distinct()
=====================

Adds the "DISTINCT" keyword to a query

::

	$this->db->distinct();
	$this->db->get('table'); // Produces: SELECT DISTINCT * FROM table


$this->db->having()
===================

Permits you to write the HAVING portion of your query. There are 2
possible syntaxes, 1 argument or 2::

	$this->db->having('user_id = 45');  // Produces: HAVING user_id = 45
	$this->db->having('user_id',  45);  // Produces: HAVING user_id = 45

You can also pass an array of multiple values as well::

	$this->db->having(array('title =' => 'My Title', 'id <' => $id));
	// Produces: HAVING title = 'My Title', id < 45


If you are using a database that CodeIgniter escapes queries for, you
can prevent escaping content by passing an optional third argument, and
setting it to FALSE.

::

	$this->db->having('user_id',  45);  // Produces: HAVING `user_id` = 45 in some databases such as MySQL
	$this->db->having('user_id',  45, FALSE);  // Produces: HAVING user_id = 45


$this->db->or_having()
======================

Identical to having(), only separates multiple clauses with "OR".

$this->db->order_by()
=====================

Lets you set an ORDER BY clause.

The first parameter contains the name of the column you would like to order by.

The second parameter lets you set the direction of the result.
Options are **ASC**, **DESC** AND **RANDOM**.

::

	$this->db->order_by('title', 'DESC');
	// Produces: ORDER BY `title` DESC

You can also pass your own string in the first parameter::

	$this->db->order_by('title DESC, name ASC');
	// Produces: ORDER BY `title` DESC, `name` ASC

Or multiple function calls can be made if you need multiple fields.

::

	$this->db->order_by('title', 'DESC');
	$this->db->order_by('name', 'ASC');
	// Produces: ORDER BY `title` DESC, `name` ASC

If you choose the **RANDOM** direction option, then the first parameters will
be ignored, unless you specify a numeric seed value.

::

	$this->db->order_by('title', 'RANDOM');
	// Produces: ORDER BY RAND()

	$this->db->order_by(42, 'RANDOM');
	// Produces: ORDER BY RAND(42)

.. note:: order_by() was formerly known as orderby(), which has been
	removed.

.. note:: Random ordering is not currently supported in Oracle and
	will default to ASC instead.

$this->db->limit()
==================

Lets you limit the number of rows you would like returned by the query::

	$this->db->limit(10);  // Produces: LIMIT 10

The second parameter lets you set a result offset.

::

	$this->db->limit(10, 20);  // Produces: LIMIT 20, 10 (in MySQL.  Other databases have slightly different syntax)

$this->db->count_all_results()
==============================

Permits you to determine the number of rows in a particular Active
Record query. Queries will accept Query Builder restrictors such as
where(), or_where(), like(), or_like(), etc. Example::

	echo $this->db->count_all_results('my_table');  // Produces an integer, like 25
	$this->db->like('title', 'match');
	$this->db->from('my_table');
	echo $this->db->count_all_results(); // Produces an integer, like 17

$this->db->count_all()
======================

Permits you to determine the number of rows in a particular table.
Submit the table name in the first parameter. Example::

	echo $this->db->count_all('my_table');  // Produces an integer, like 25
