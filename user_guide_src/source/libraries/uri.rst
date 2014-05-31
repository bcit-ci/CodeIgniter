#########
URI Class
#########

The URI Class provides methods that help you retrieve information from
your URI strings. If you use URI routing, you can also retrieve
information about the re-routed segments.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

***************
Class Reference
***************

.. class:: CI_URI

	.. method:: segment($n[, $no_result = NULL])

		:param	int	$n: Segment index number
		:param	mixed	$no_result: What to return if the searched segment is not found
		:returns:	Segment value or $no_result value if not found
		:rtype:	mixed

		Permits you to retrieve a specific segment. Where n is the segment
		number you wish to retrieve. Segments are numbered from left to right.
		For example, if your full URL is this::

			http://example.com/index.php/news/local/metro/crime_is_up

		The segment numbers would be this:

		#. news
		#. local
		#. metro
		#. crime_is_up

		The optional second parameter defaults to NULL and allows you to set the return value
		of this method when the requested URI segment is missing.
		For example, this would tell the method to return the number zero in the event of failure::

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

	.. method:: rsegment($n[, $no_result = NULL])

		:param	int	$n: Segment index number
		:param	mixed	$no_result: What to return if the searched segment is not found
		:returns:	Routed segment value or $no_result value if not found
		:rtype:	mixed

		This method is identical to ``segment()``, except that it lets you retrieve
		a specific segment from your re-routed URI in the event you are
		using CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

	.. method:: slash_segment($n[, $where = 'trailing'])

		:param	int	$n: Segment index number
		:param	string	$where: Where to add the slash ('trailing' or 'leading')
		:returns:	Segment value, prepended/suffixed with a forward slash, or a slash if not found
		:rtype:	string

		This method is almost identical to ``segment()``, except it
		adds a trailing and/or leading slash based on the second parameter.
		If the parameter is not used, a trailing slash added. Examples::

			$this->uri->slash_segment(3);
			$this->uri->slash_segment(3, 'leading');
			$this->uri->slash_segment(3, 'both');

		Returns:

		#. segment/
		#. /segment
		#. /segment/

	.. method:: slash_rsegment($n[, $where = 'trailing'])

		:param	int	$n: Segment index number
		:param	string	$where: Where to add the slash ('trailing' or 'leading')
		:returns:	Routed segment value, prepended/suffixed with a forward slash, or a slash if not found
		:rtype:	string

		This method is identical to ``slash_segment()``, except that it lets you
		add slashes a specific segment from your re-routed URI in the event you
		are using CodeIgniter's :doc:`URI Routing <../general/routing>`
		feature.

	.. method:: uri_to_assoc([$n = 3[, $default = array()]])

		:param	int	$n: Segment index number
		:param	array	$default: Default values
		:returns:	Associative URI segments array
		:rtype:	array

		This method lets you turn URI segments into and associative array of
		key/value pairs. Consider this URI::

			index.php/user/search/name/joe/location/UK/gender/male

		Using this method you can turn the URI into an associative array with
		this prototype::

			[array]
			(
				'name'		=> 'joe'
				'location'	=> 'UK'
				'gender'	=> 'male'
			)

		The first parameter lets you set an offset, which defaults to 3 since your
		URI will normally contain a controller/method pair in the first and second segments.
		Example::

			$array = $this->uri->uri_to_assoc(3);
			echo $array['name'];

		The second parameter lets you set default key names, so that the array
		returned will always contain expected indexes, even if missing from the URI.
		Example::

			$default = array('name', 'gender', 'location', 'type', 'sort');
			$array = $this->uri->uri_to_assoc(3, $default);

		If the URI does not contain a value in your default, an array index will
		be set to that name, with a value of NULL.

		Lastly, if a corresponding value is not found for a given key (if there
		is an odd number of URI segments) the value will be set to NULL.

	.. method:: ruri_to_assoc([$n = 3[, $default = array()]])

		:param	int	$n: Segment index number
		:param	array	$default: Default values
		:returns:	Associative routed URI segments array
		:rtype:	array

		This method is identical to ``uri_to_assoc()``, except that it creates
		an associative array using the re-routed URI in the event you are using
		CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

	.. method:: assoc_to_uri($array)

		:param	array	$array: Input array of key/value pairs
		:returns:	URI string
		:rtype:	string

		Takes an associative array as input and generates a URI string from it.
		The array keys will be included in the string. Example::

			$array = array('product' => 'shoes', 'size' => 'large', 'color' => 'red');
			$str = $this->uri->assoc_to_uri($array);

			// Produces: product/shoes/size/large/color/red

	.. method:: uri_string()

		:returns:	URI string
		:rtype:	string

		Returns a string with the complete URI. For example, if this is your full URL::

			http://example.com/index.php/news/local/345

		The method would return this::

			news/local/345

	.. method:: ruri_string()

		:returns:	Routed URI string
		:rtype:	string

		This method is identical to ``uri_string()``, except that it returns
		the re-routed URI in the event you are using CodeIgniter's :doc:`URI
		Routing <../general/routing>` feature.

	.. method:: total_segments()

		:returns:	Count of URI segments
		:rtype:	int

		Returns the total number of segments.

	.. method:: total_rsegments()

		:returns:	Count of routed URI segments
		:rtype:	int

		This method is identical to ``total_segments()``, except that it returns
		the total number of segments in your re-routed URI in the event you are
		using CodeIgniter's :doc:`URI Routing <../general/routing>` feature.

	.. method:: segment_array()

		:returns:	URI segments array
		:rtype:	array

		Returns an array containing the URI segments. For example::

			$segs = $this->uri->segment_array();

			foreach ($segs as $segment)
			{
				echo $segment;
				echo '<br />';
			}

	.. method:: rsegment_array()

		:returns:	Routed URI segments array
		:rtype:	array

		This method is identical to ``segment_array()``, except that it returns
		the array of segments in your re-routed URI in the event you are using
		CodeIgniter's :doc:`URI Routing <../general/routing>` feature.