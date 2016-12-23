################
HTML Table Class
################

The Table Class provides functions that enable you to auto-generate HTML
tables from arrays or database result sets.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

*********************
Using the Table Class
*********************

Initializing the Class
======================

Like most other classes in CodeIgniter, the Table class is initialized
in your controller using the ``$this->load->library()`` method::

	$this->load->library('table');

Once loaded, the Table library object will be available using::

	$this->table

Examples
========

Here is an example showing how you can create a table from a
multi-dimensional array. Note that the first array index will become the
table heading (or you can set your own headings using the ``set_heading()``
method described in the function reference below).

::

	$this->load->library('table');

	$data = array(
		array('Name', 'Color', 'Size'),
		array('Fred', 'Blue', 'Small'),
		array('Mary', 'Red', 'Large'),
		array('John', 'Green', 'Medium')	
	);

	echo $this->table->generate($data);

Here is an example of a table created from a database query result. The
table class will automatically generate the headings based on the table
names (or you can set your own headings using the ``set_heading()``
method described in the class reference below).

::

	$this->load->library('table');

	$query = $this->db->query('SELECT * FROM my_table');

	echo $this->table->generate($query);

Here is an example showing how you might create a table using discrete
parameters::

	$this->load->library('table');

	$this->table->set_heading('Name', 'Color', 'Size');

	$this->table->add_row('Fred', 'Blue', 'Small');
	$this->table->add_row('Mary', 'Red', 'Large');
	$this->table->add_row('John', 'Green', 'Medium');

	echo $this->table->generate();

Here is the same example, except instead of individual parameters,
arrays are used::

	$this->load->library('table');

	$this->table->set_heading(array('Name', 'Color', 'Size'));

	$this->table->add_row(array('Fred', 'Blue', 'Small'));
	$this->table->add_row(array('Mary', 'Red', 'Large'));
	$this->table->add_row(array('John', 'Green', 'Medium'));

	echo $this->table->generate();

Changing the Look of Your Table
===============================

The Table Class permits you to set a table template with which you can
specify the design of your layout. Here is the template prototype::

	$template = array(
		'table_open'		=> '<table border="0" cellpadding="4" cellspacing="0">',

		'thead_open'		=> '<thead>',
		'thead_close'		=> '</thead>',

		'heading_row_start'	=> '<tr>',
		'heading_row_end'	=> '</tr>',
		'heading_cell_start'	=> '<th>',
		'heading_cell_end'	=> '</th>',

		'tbody_open'		=> '<tbody>',
		'tbody_close'		=> '</tbody>',

		'row_start'		=> '<tr>',
		'row_end'		=> '</tr>',
		'cell_start'		=> '<td>',
		'cell_end'		=> '</td>',

		'row_alt_start'		=> '<tr>',
		'row_alt_end'		=> '</tr>',
		'cell_alt_start'	=> '<td>',
		'cell_alt_end'		=> '</td>',

		'table_close'		=> '</table>'
	);

	$this->table->set_template($template);

.. note:: You'll notice there are two sets of "row" blocks in the
	template. These permit you to create alternating row colors or design
	elements that alternate with each iteration of the row data.

You are NOT required to submit a complete template. If you only need to
change parts of the layout you can simply submit those elements. In this
example, only the table opening tag is being changed::

	$template = array(
		'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
	);

	$this->table->set_template($template);
	
You can also set defaults for these in a config file.

***************
Class Reference
***************

.. php:class:: CI_Table

	.. attribute:: $function = NULL

		Allows you to specify a native PHP function or a valid function array object to be applied to all cell data.
		::

			$this->load->library('table');

			$this->table->set_heading('Name', 'Color', 'Size');
			$this->table->add_row('Fred', '<strong>Blue</strong>', 'Small');

			$this->table->function = 'htmlspecialchars';
			echo $this->table->generate();

		In the above example, all cell data would be ran through PHP's :php:func:`htmlspecialchars()` function, resulting in::

			<td>Fred</td><td>&lt;strong&gt;Blue&lt;/strong&gt;</td><td>Small</td>

	.. php:method:: generate([$table_data = NULL])

		:param	mixed	$table_data: Data to populate the table rows with
		:returns:	HTML table
		:rtype:	string

		Returns a string containing the generated table. Accepts an optional parameter which can be an array or a database result object.

	.. php:method:: set_caption($caption)

		:param	string	$caption: Table caption
		:returns:	CI_Table instance (method chaining)
		:rtype:	CI_Table

		Permits you to add a caption to the table.
		::

			$this->table->set_caption('Colors');

	.. php:method:: set_heading([$args = array()[, ...]])

		:param	mixed	$args: An array or multiple strings containing the table column titles
		:returns:	CI_Table instance (method chaining)
		:rtype:	CI_Table

		Permits you to set the table heading. You can submit an array or discrete params::

			$this->table->set_heading('Name', 'Color', 'Size');

			$this->table->set_heading(array('Name', 'Color', 'Size'));

	.. php:method:: add_row([$args = array()[, ...]])

		:param	mixed	$args: An array or multiple strings containing the row values
		:returns:	CI_Table instance (method chaining)
		:rtype:	CI_Table

		Permits you to add a row to your table. You can submit an array or discrete params::

			$this->table->add_row('Blue', 'Red', 'Green');

			$this->table->add_row(array('Blue', 'Red', 'Green'));

		If you would like to set an individual cell's tag attributes, you can use an associative array for that cell.
		The associative key **data** defines the cell's data. Any other key => val pairs are added as key='val' attributes to the tag::

			$cell = array('data' => 'Blue', 'class' => 'highlight', 'colspan' => 2);
			$this->table->add_row($cell, 'Red', 'Green');

			// generates
			// <td class='highlight' colspan='2'>Blue</td><td>Red</td><td>Green</td>

	.. php:method:: make_columns([$array = array()[, $col_limit = 0]])

		:param	array	$array: An array containing multiple rows' data
		:param	int	$col_limit: Count of columns in the table
		:returns:	An array of HTML table columns
		:rtype:	array

		This method takes a one-dimensional array as input and creates a multi-dimensional array with a depth equal to the number of columns desired.
		This allows a single array with many elements to be displayed in a table that has a fixed column count. Consider this example::

			$list = array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve');

			$new_list = $this->table->make_columns($list, 3);

			$this->table->generate($new_list);

			// Generates a table with this prototype

			<table border="0" cellpadding="4" cellspacing="0">
			<tr>
			<td>one</td><td>two</td><td>three</td>
			</tr><tr>
			<td>four</td><td>five</td><td>six</td>
			</tr><tr>
			<td>seven</td><td>eight</td><td>nine</td>
			</tr><tr>
			<td>ten</td><td>eleven</td><td>twelve</td></tr>
			</table>


	.. php:method:: set_template($template)

		:param	array	$template: An associative array containing template values
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Permits you to set your template. You can submit a full or partial template.
		::

			$template = array(
				'table_open'  => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
			);
		
			$this->table->set_template($template);

	.. php:method:: set_empty($value)

		:param	mixed	$value: Value to put in empty cells
		:returns:	CI_Table instance (method chaining)
		:rtype:	CI_Table

		Lets you set a default value for use in any table cells that are empty.
		You might, for example, set a non-breaking space::

			$this->table->set_empty("&nbsp;");

	.. php:method:: clear()

		:returns:	CI_Table instance (method chaining)
		:rtype:	CI_Table

		Lets you clear the table heading, row data and caption. If
		you need to show multiple tables with different data you
		should to call this method after each table has been
		generated to clear the previous table information.

		Example ::

			$this->load->library('table');

			$this->table->set_caption('Preferences');
			$this->table->set_heading('Name', 'Color', 'Size');
			$this->table->add_row('Fred', 'Blue', 'Small');
			$this->table->add_row('Mary', 'Red', 'Large');
			$this->table->add_row('John', 'Green', 'Medium');

			echo $this->table->generate();

			$this->table->clear();

			$this->table->set_caption('Shipping');
			$this->table->set_heading('Name', 'Day', 'Delivery');
			$this->table->add_row('Fred', 'Wednesday', 'Express');
			$this->table->add_row('Mary', 'Monday', 'Air');
			$this->table->add_row('John', 'Saturday', 'Overnight');

			echo $this->table->generate();
