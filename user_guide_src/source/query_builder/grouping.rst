##############
Query grouping
##############

Query grouping allows you to create groups of WHERE clauses by enclosing them in parentheses. This will allow
you to create queries with complex WHERE clauses. Nested groups are supported. Example::

	$this->db->select('*')->from('my_table')
		->group_start()
			->where('a', 'a')
			->or_group_start()
				->where('b', 'b')
				->where('c', 'c')
			->group_end()
		->group_end()
		->where('d', 'd')
	->get();

	// Generates:
	// SELECT * FROM (`my_table`) WHERE ( `a` = 'a' OR ( `b` = 'b' AND `c` = 'c' ) ) AND `d` = 'd'

.. note:: groups need to be balanced, make sure every group_start() is matched by a group_end().

$this->db->group_start()
========================

Starts a new group by adding an opening parenthesis to the WHERE clause of the query.

$this->db->or_group_start()
===========================

Starts a new group by adding an opening parenthesis to the WHERE clause of the query, prefixing it with 'OR'.

$this->db->not_group_start()
============================

Starts a new group by adding an opening parenthesis to the WHERE clause of the query, prefixing it with 'NOT'.

$this->db->or_not_group_start()
===============================

Starts a new group by adding an opening parenthesis to the WHERE clause of the query, prefixing it with 'OR NOT'.

$this->db->group_end()
======================

Ends the current group by adding an closing parenthesis to the WHERE clause of the query.

