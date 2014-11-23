.. _ar-caching:

#####################
Query Builder Caching
#####################

While not "true" caching, Query Builder enables you to save (or "cache")
certain parts of your queries for reuse at a later point in your
script's execution. Normally, when an Query Builder call is completed,
all stored information is reset for the next call. With caching, you can
prevent this reset, and reuse information easily.

Cached calls are cumulative. If you make 2 cached select() calls, and
then 2 uncached select() calls, this will result in 4 select() calls.
There are three Caching functions available:

$this->db->start_cache()
========================

This function must be called to begin caching. All Query Builder queries
of the correct type (see below for supported queries) are stored for
later use.

$this->db->stop_cache()
=======================

This function can be called to stop caching.

$this->db->flush_cache()
========================

This function deletes all items from the Query Builder cache.

Here's a usage example::

	$this->db->start_cache();
	$this->db->select('field1');
	$this->db->stop_cache();
	$this->db->get('tablename');
	//Generates: SELECT `field1` FROM (`tablename`)

	$this->db->select('field2');
	$this->db->get('tablename');
	//Generates:  SELECT `field1`, `field2` FROM (`tablename`)

	$this->db->flush_cache();
	$this->db->select('field2');
	$this->db->get('tablename');
	//Generates:  SELECT `field2` FROM (`tablename`)


.. note:: The following statements can be cached: select, from, join,
	where, like, group_by, having, order_by, set


$this->db->reset_query()
========================

Resetting Query Builder allows you to start fresh with your query without
executing it first using a method like $this->db->get() or $this->db->insert().
Just like the methods that execute a query, this will *not* reset items you've
cached using `Query Builder Caching`_.

This is useful in situations where you are using Query Builder to generate SQL
(ex. ``$this->db->get_compiled_select()``) but then choose to, for instance,
run the query::

	// Note that the second parameter of the get_compiled_select method is FALSE
	$sql = $this->db->select(array('field1','field2'))
					->where('field3',5)
					->get_compiled_select('mytable', FALSE);

	// ...
	// Do something crazy with the SQL code... like add it to a cron script for
	// later execution or something...
	// ...

	$data = $this->db->get()->result_array();

	// Would execute and return an array of results of the following query:
	// SELECT field1, field1 from mytable where field3 = 5;

.. note:: Double calls to ``get_compiled_select()`` while you're using the
	Query Builder Caching functionality and NOT resetting your queries
	will results in the cache being merged twice. That in turn will
	i.e. if you're caching a ``select()`` - select the same field twice.