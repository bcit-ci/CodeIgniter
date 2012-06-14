#########
URI Class
#########

The URI Class provides functions that help you retrieve information from
your URI strings. If you use URI routing, you can also retrieve
information about the re-routed segments.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

$this->uri->segment(n)
======================

Permits you to retrieve a specific segment. Where n is the segment
number you wish to retrieve. Segments are numbered from left to right.
For example, if your full URL is this::

	http://example.com/index.php/news/local/metro/crime_is_up

The segment numbers would be this:

#. news
#. local
#. metro
#. crime_is_up

By default the function returns NULL if the segment does not
exist. There is an optional second parameter that permits you to set
your own default value if the segment is missing. For example, this
would tell the function to return the number zero in the event of
failure::

	$product_id = $this->uri->segment(3, 0);

It helps avoid having to write code like this::

	if ($this->uri->segment(3) === FALSE)
	{
	    $product_id = 0;
	}
	else
	{
	    $product_id = $this->uri->segment(3);
	}

$this->uri->rsegment(n)
=======================

This function is identical to the previous one, except that it lets you
retrieve a specific segment from your re-routed URI in the event you are
using CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

$this->uri->slash_segment(n)
=============================

This function is almost identical to $this->uri->segment(), except it
adds a trailing and/or leading slash based on the second parameter. If
the parameter is not used, a trailing slash added. Examples::

	$this->uri->slash_segment(3);
	$this->uri->slash_segment(3, 'leading');
	$this->uri->slash_segment(3, 'both');

Returns:

#. segment/
#. /segment
#. /segment/

$this->uri->slash_rsegment(n)
==============================

This function is identical to the previous one, except that it lets you
add slashes a specific segment from your re-routed URI in the event you
are using CodeIgniter's :doc:`URI Routing <../general/routing>`
feature.

$this->uri->uri_to_assoc(n)
=============================

This function lets you turn URI segments into and associative array of
key/value pairs. Consider this URI::

	index.php/user/search/name/joe/location/UK/gender/male

Using this function you can turn the URI into an associative array with
this prototype::

	[array]
	(
	    'name' => 'joe'
	    'location'	=> 'UK'
	    'gender'	=> 'male'
	)

The first parameter of the function lets you set an offset. By default
it is set to 3 since your URI will normally contain a
controller/function in the first and second segments. Example::

	$array = $this->uri->uri_to_assoc(3);

	echo $array['name'];

The second parameter lets you set default key names, so that the array
returned by the function will always contain expected indexes, even if
missing from the URI. Example::

	$default = array('name', 'gender', 'location', 'type', 'sort');

	$array = $this->uri->uri_to_assoc(3, $default);

If the URI does not contain a value in your default, an array index will
be set to that name, with a value of FALSE.

Lastly, if a corresponding value is not found for a given key (if there
is an odd number of URI segments) the value will be set to FALSE
(boolean).

$this->uri->ruri_to_assoc(n)
==============================

This function is identical to the previous one, except that it creates
an associative array using the re-routed URI in the event you are using
CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

$this->uri->assoc_to_uri()
============================

Takes an associative array as input and generates a URI string from it.
The array keys will be included in the string. Example::

	$array = array('product' => 'shoes', 'size' => 'large', 'color' => 'red');

	$str = $this->uri->assoc_to_uri($array);

	// Produces: product/shoes/size/large/color/red

$this->uri->uri_string()
=========================

Returns a string with the complete URI. For example, if this is your
full URL::

	http://example.com/index.php/news/local/345

The function would return this::

	news/local/345

$this->uri->ruri_string()
==========================

This function is identical to the previous one, except that it returns
the re-routed URI in the event you are using CodeIgniter's :doc:`URI
Routing <../general/routing>` feature.

$this->uri->total_segments()
=============================

Returns the total number of segments.

$this->uri->total_rsegments()
==============================

This function is identical to the previous one, except that it returns
the total number of segments in your re-routed URI in the event you are
using CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

$this->uri->segment_array()
============================

Returns an array containing the URI segments. For example::

	$segs = $this->uri->segment_array();

	foreach ($segs as $segment)
	{
	    echo $segment;
	    echo '<br />';
	}

$this->uri->rsegment_array()
=============================

This function is identical to the previous one, except that it returns
the array of segments in your re-routed URI in the event you are using
CodeIgniter's :doc:`URI Routing <../general/routing>` feature.
