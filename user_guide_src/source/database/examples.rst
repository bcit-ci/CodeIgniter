##################################
Database Quick Start: Example Code
##################################

The following page contains example code showing how the database class
is used. For complete details please read the individual pages
describing each function.

Initializing the Database Class
===============================

The following code loads and initializes the database class based on
your :doc:`configuration <configuration>` settings::

	$this->load->database();

Once loaded the class is ready to be used as described below.

Note: If all your pages require database access you can connect
automatically. See the :doc:`connecting <connecting>` page for details.

Standard Query With Multiple Results (Object Version)
=====================================================

::

	$query = $this->db->query('SELECT name, title, email FROM my_table');
	
	foreach ($query->result() as $row)
	{
		echo $row->title;
		echo $row->name;
		echo $row->email;
	}
	
	echo 'Total Results: ' . $query->num_rows();

The above result() function returns an array of **objects**. Example:
$row->title

Standard Query With Multiple Results (Array Version)
====================================================

::

	$query = $this->db->query('SELECT name, title, email FROM my_table');
	
	foreach ($query->result_array() as $row)
	{
		echo $row['title'];
		echo $row['name'];
		echo $row['email'];
	}

The above result_array() function returns an array of standard array
indexes. Example: $row['title']

Testing for Results
===================

If you run queries that might **not** produce a result, you are
encouraged to test for a result first using the num_rows() function::

	$query = $this->db->query("YOUR QUERY");
	if ($query->num_rows() > 0)
	{
		foreach ($query->result() as $row)
		{
			echo $row->title;
			echo $row->name;
			echo $row->body;
		}
	}

Standard Query With Single Result
=================================

::

	$query = $this->db->query('SELECT name FROM my_table LIMIT 1'); 
	$row = $query->row();
	echo $row->name;

The above row() function returns an **object**. Example: $row->name

Standard Query With Single Result (Array version)
=================================================

::

	$query = $this->db->query('SELECT name FROM my_table LIMIT 1');
	$row = $query->row_array();
	echo $row['name'];

The above row_array() function returns an **array**. Example:
$row['name']

Standard Insert
===============

::

	$sql = "INSERT INTO mytable (title, name) VALUES (".$this->db->escape($title).", ".$this->db->escape($name).")";
	$this->db->query($sql);
	echo $this->db->affected_rows();

Query Builder Query
===================

The :doc:`Query Builder Pattern <query_builder>` gives you a simplified
means of retrieving data::

	$query = $this->db->get('table_name');
	
	foreach ($query->result() as $row)
	{
		echo $row->title;
	}

The above get() function retrieves all the results from the supplied
table. The :doc:`Query Builder <query_builder>` class contains a full
compliment of functions for working with data.

Query Builder Insert
====================

::

	$data = array(
		'title' => $title,
		'name' => $name,
		'date' => $date
	);
	
	$this->db->insert('mytable', $data);  // Produces: INSERT INTO mytable (title, name, date) VALUES ('{$title}', '{$name}', '{$date}')

