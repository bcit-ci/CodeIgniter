#####################
Template Parser Class
#####################

The Template Parser Class enables you to parse pseudo-variables
contained within your view files. It can parse simple variables or
variable tag pairs. If you've never used a template engine,
pseudo-variables look like this::

	<html>
	<head>
	<title>{blog_title}</title>
	</head>
	<body>

	<h3>{blog_heading}</h3>

	{blog_entries}
	<h5>{title}</h5>
	<p>{body}</p>
	{/blog_entries}
	</body>
	</html>

These variables are not actual PHP variables, but rather plain text
representations that allow you to eliminate PHP from your templates
(view files).

.. note:: CodeIgniter does **not** require you to use this class since
	using pure PHP in your view pages lets them run a little faster.
	However, some developers prefer to use a template engine if they work
	with designers who they feel would find some confusion working with PHP.

.. important:: The Template Parser Class is **not** a full-blown
	template parsing solution. We've kept it very lean on purpose in order
	to maintain maximum performance.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Initializing the Class
======================

Like most other classes in CodeIgniter, the Parser class is initialized
in your controller using the $this->load->library function::

	$this->load->library('parser');

Once loaded, the Parser library object will be available using:
$this->parser

Parsing templates
=================

You can use the ``parse()`` method to parse (or render) simple templates, like this::

	$data = array(
	            'blog_title' => 'My Blog Title',
	            'blog_heading' => 'My Blog Heading'
	            );

	$this->parser->parse('blog_template', $data);

The first parameter contains the name of the :doc:`view
file <../general/views>` (in this example the file would be called
blog_template.php), and the second parameter contains an associative
array of data to be replaced in the template. In the above example, the
template would contain two variables: {blog_title} and {blog_heading}

There is no need to "echo" or do something with the data returned by
$this->parser->parse(). It is automatically passed to the output class
to be sent to the browser. However, if you do want the data returned
instead of sent to the output class you can pass TRUE (boolean) to the
third parameter::

	$string = $this->parser->parse('blog_template', $data, TRUE);

Variable Pairs
==============

The above example code allows simple variables to be replaced. What if
you would like an entire block of variables to be repeated, with each
iteration containing new values? Consider the template example we showed
at the top of the page::

	<html>
	<head>
	<title>{blog_title}</title>
	</head>
	<body>

	<h3>{blog_heading}</h3>

	{blog_entries}
	<h5>{title}</h5>
	<p>{body}</p>
	{/blog_entries}
	</body>
	</html>

In the above code you'll notice a pair of variables: {blog_entries}
data... {/blog_entries}. In a case like this, the entire chunk of data
between these pairs would be repeated multiple times, corresponding to
the number of rows in a result.

Parsing variable pairs is done using the identical code shown above to
parse single variables, except, you will add a multi-dimensional array
corresponding to your variable pair data. Consider this example::

	$this->load->library('parser');

	$data = array(
	              'blog_title'   => 'My Blog Title',
	              'blog_heading' => 'My Blog Heading',
	              'blog_entries' => array(
	                                      array('title' => 'Title 1', 'body' => 'Body 1'),
	                                      array('title' => 'Title 2', 'body' => 'Body 2'),
	                                      array('title' => 'Title 3', 'body' => 'Body 3'),
	                                      array('title' => 'Title 4', 'body' => 'Body 4'),
	                                      array('title' => 'Title 5', 'body' => 'Body 5')
	                                      )
	            );

	$this->parser->parse('blog_template', $data);

If your "pair" data is coming from a database result, which is already a
multi-dimensional array, you can simply use the database result_array()
function::

	$query = $this->db->query("SELECT * FROM blog");

	$this->load->library('parser');

	$data = array(
	              'blog_title'   => 'My Blog Title',
	              'blog_heading' => 'My Blog Heading',
	              'blog_entries' => $query->result_array()
	            );

	$this->parser->parse('blog_template', $data);

***************
Class Reference
***************

.. class:: CI_Parser

	.. method:: parse($template, $data[, $return = FALSE])

		:param	string	$template: Path to view file
		:param	array	$data: Variable data
		:param	bool	$return: Whether to only return the parsed template
		:returns:	Parsed template string
		:rtype:	string

		Parses a template from the provided path and variables.

	.. method:: parse_string($template, $data[, $return = FALSE])

		:param	string	$template: Path to view file
		:param	array	$data: Variable data
		:param	bool	$return: Whether to only return the parsed template
		:returns:	Parsed template string
		:rtype:	string

		This method works exactly like ``parse()``, only it accepts the template as a
		string instead of loading a view file.

	.. method:: set_delimiters([$l = '{'[, $r = '}']])

		:param	string	$l: Left delimiter
		:param	string	$r: Right delimiter
		:rtype: void

		Sets the delimiters (opening and closing) for a value "tag" in a template.