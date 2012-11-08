#############
Smiley Helper
#############

The Smiley Helper file contains functions that let you manage smileys
(emoticons).

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('smiley');

Overview
========

The Smiley helper has a renderer that takes plain text simileys, like
:-) and turns them into a image representation, like |smile!|

It also lets you display a set of smiley images that when clicked will
be inserted into a form field. For example, if you have a blog that
allows user commenting you can show the smileys next to the comment
form. Your users can click a desired smiley and with the help of some
JavaScript it will be placed into the form field.

Clickable Smileys Tutorial
==========================

Here is an example demonstrating how you might create a set of clickable
smileys next to a form field. This example requires that you first
download and install the smiley images, then create a controller and the
View as described.

.. important:: Before you begin, please `download the smiley images
	<http://codeigniter.com/download_files/smileys.zip>`_
	and put them in a publicly accessible place on your server.
	This helper also assumes you have the smiley replacement array
	located at `application/config/smileys.php`

The Controller
--------------

In your `application/controllers/` folder, create a file called
smileys.php and place the code below in it.

.. important:: Change the URL in the :php:func:`get_clickable_smileys()`
	function below so that it points to your smiley folder.

You'll notice that in addition to the smiley helper, we are also using
the :doc:`Table Class <../libraries/table>`::

	<?php

	class Smileys extends CI_Controller {

		public function index()
		{
			$this->load->helper('smiley');
			$this->load->library('table');

			$image_array = get_clickable_smileys('http://example.com/images/smileys/', 'comments');
			$col_array = $this->table->make_columns($image_array, 8);

			$data['smiley_table'] = $this->table->generate($col_array);
			$this->load->view('smiley_view', $data);
		}

	}

In your `application/views/` folder, create a file called `smiley_view.php`
and place this code in it::

	<html>
		<head>
			<title>Smileys</title>
			<?php echo smiley_js(); ?>
		</head>
		<body>
			<form name="blog">
				<textarea name="comments" id="comments" cols="40" rows="4"></textarea>
			</form>
			<p>Click to insert a smiley!</p>
			<?php echo $smiley_table; ?> </body> </html>
			When you have created the above controller and view, load it by visiting http://www.example.com/index.php/smileys/
		</body>
	</html>

Field Aliases
-------------

When making changes to a view it can be inconvenient to have the field
id in the controller. To work around this, you can give your smiley
links a generic name that will be tied to a specific id in your view.

::

	$image_array = get_smiley_links("http://example.com/images/smileys/", "comment_textarea_alias");

To map the alias to the field id, pass them both into the
:php:func:`smiley_js()` function::

	$image_array = smiley_js("comment_textarea_alias", "comments");

get_clickable_smileys()
=======================

.. php:function:: get_clickable_smileys($image_url, $alias = '', $smileys = NULL)

	:param	string	$image_url: URL path to the smileys directory
	:param	string	$alias: Field alias
	:returns:	array

Returns an array containing your smiley images wrapped in a clickable
link. You must supply the URL to your smiley folder and a field id or
field alias.

Example::

	$image_array = get_smiley_links("http://example.com/images/smileys/", "comment");

smiley_js()
===========

.. php:function:: smiley_js($alias = '', $field_id = '', $inline = TRUE)

	:param	string	$alias: Field alias
	:param	string	$field_id: Field ID
	:param	bool	$inline: Whether we're inserting an inline smiley

Generates the JavaScript that allows the images to be clicked and
inserted into a form field. If you supplied an alias instead of an id
when generating your smiley links, you need to pass the alias and
corresponding form id into the function. This function is designed to be
placed into the <head> area of your web page.

Example::

	<?php echo smiley_js(); ?>

parse_smileys()
===============

.. php:function:: parse_smileys($str = '', $image_url = '', $smileys = NULL)

	:param	string	$str: Text containing smiley codes
	:param	string	$image_url: URL path to the smileys directory
	:param	array	$smileys: An array of smileys
	:returns:	string

Takes a string of text as input and replaces any contained plain text
smileys into the image equivalent. The first parameter must contain your
string, the second must contain the URL to your smiley folder

Example::

	$str = 'Here are some simileys: :-)  ;-)';
	$str = parse_smileys($str, "http://example.com/images/smileys/");
	echo $str;


.. |smile!| image:: ../images/smile.gif