##########
URL Helper
##########

The URL Helper file contains functions that assist in working with URLs.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('url');

Available Functions
===================

The following functions are available:

.. php:function:: site_url([$uri = ''[, $protocol = NULL]])

	:param	string	$uri: URI string
	:param	string	$protocol: Protocol, e.g. 'http' or 'https'
	:returns:	Site URL
	:rtype:	string

	Returns your site URL, as specified in your config file. The index.php
	file (or whatever you have set as your site **index_page** in your config
	file) will be added to the URL, as will any URI segments you pass to the
	function, plus the **url_suffix** as set in your config file.

	You are encouraged to use this function any time you need to generate a
	local URL so that your pages become more portable in the event your URL
	changes.

	Segments can be optionally passed to the function as a string or an
	array. Here is a string example::

		echo site_url('news/local/123');

	The above example would return something like:
	*http://example.com/index.php/news/local/123*

	Here is an example of segments passed as an array::

		$segments = array('news', 'local', '123');
		echo site_url($segments);

	This function is an alias for ``CI_Config::site_url()``. For more info,
	please see the :doc:`Config Library <../libraries/config>` documentation.

.. php:function:: base_url($uri = '', $protocol = NULL)

	:param	string	$uri: URI string
	:param	string	$protocol: Protocol, e.g. 'http' or 'https'
	:returns:	Base URL
	:rtype:	string

	Returns your site base URL, as specified in your config file. Example::

		echo base_url();

	This function returns the same thing as :php:func:`site_url()`, without
	the *index_page* or *url_suffix* being appended.

	Also like :php:func:`site_url()`, you can supply segments as a string or
	an array. Here is a string example::

		echo base_url("blog/post/123");

	The above example would return something like:
	*http://example.com/blog/post/123*

	This is useful because unlike :php:func:`site_url()`, you can supply a
	string to a file, such as an image or stylesheet. For example::

		echo base_url("images/icons/edit.png");

	This would give you something like:
	*http://example.com/images/icons/edit.png*

	This function is an alias for ``CI_Config::base_url()``. For more info,
	please see the :doc:`Config Library <../libraries/config>` documentation.

.. php:function:: current_url()

	:returns:	The current URL
	:rtype:	string

	Returns the full URL (including segments) of the page being currently
	viewed.

	.. note:: Calling this function is the same as doing this:
		|
		| site_url(uri_string());


.. php:function:: uri_string()

	:returns:	An URI string
	:rtype:	string

	Returns the URI segments of any page that contains this function.
	For example, if your URL was this::

		http://some-site.com/blog/comments/123

	The function would return::

		blog/comments/123

	This function is an alias for ``CI_Config::uri_string()``. For more info,
	please see the :doc:`Config Library <../libraries/config>` documentation.


.. php:function:: index_page()

	:returns:	'index_page' value
	:rtype:	mixed

	Returns your site **index_page**, as specified in your config file.
	Example::

		echo index_page();

.. php:function:: anchor($uri = '', $title = '', $attributes = '')

	:param	string	$uri: URI string
	:param	string	$title: Anchor title
	:param	mixed	$attributes: HTML attributes
	:returns:	HTML hyperlink (anchor tag)
	:rtype:	string

	Creates a standard HTML anchor link based on your local site URL.

	The first parameter can contain any segments you wish appended to the
	URL. As with the :php:func:`site_url()` function above, segments can
	be a string or an array.

	.. note:: If you are building links that are internal to your application
		do not include the base URL (http&#58;//...). This will be added
		automatically from the information specified in your config file.
		Include only the URI segments you wish appended to the URL.

	The second segment is the text you would like the link to say. If you
	leave it blank, the URL will be used.

	The third parameter can contain a list of attributes you would like
	added to the link. The attributes can be a simple string or an
	associative array.

	Here are some examples::

		echo anchor('news/local/123', 'My News', 'title="News title"');
		// Prints: <a href="http://example.com/index.php/news/local/123" title="News title">My News</a>

		echo anchor('news/local/123', 'My News', array('title' => 'The best news!'));
		// Prints: <a href="http://example.com/index.php/news/local/123" title="The best news!">My News</a>

		echo anchor('', 'Click here');
		// Prints: <a href="http://example.com">Click Here</a>


.. php:function:: anchor_popup($uri = '', $title = '', $attributes = FALSE)

	:param	string	$uri: URI string
	:param	string	$title: Anchor title
	:param	mixed	$attributes: HTML attributes
	:returns:	Pop-up hyperlink
	:rtype:	string

	Nearly identical to the :php:func:`anchor()` function except that it
	opens the URL in a new window. You can specify JavaScript window
	attributes in the third parameter to control how the window is opened.
	If the third parameter is not set it will simply open a new window with
	your own browser settings.

	Here is an example with attributes::

		$atts = array(
			'width'       => 800,
			'height'      => 600,
			'scrollbars'  => 'yes',
			'status'      => 'yes',
			'resizable'   => 'yes',
			'screenx'     => 0,
			'screeny'     => 0,
			'window_name' => '_blank'
		);

		echo anchor_popup('news/local/123', 'Click Me!', $atts);

	.. note:: The above attributes are the function defaults so you only need to
		set the ones that are different from what you need. If you want the
		function to use all of its defaults simply pass an empty array in the
		third parameter:
		|
		| echo anchor_popup('news/local/123', 'Click Me!', array());

	.. note:: The **window_name** is not really an attribute, but an argument to
		the JavaScript `window.open() <http://www.w3schools.com/jsref/met_win_open.asp>`
		method, which accepts either a window name or a window target.

	.. note:: Any other attribute than the listed above will be parsed as an
		HTML attribute to the anchor tag.


.. php:function:: mailto($email, $title = '', $attributes = '')

	:param	string	$email: E-mail address
	:param	string	$title: Anchor title
	:param	mixed	$attributes: HTML attributes
	:returns:	A "mail to" hyperlink
	:rtype:	string

	Creates a standard HTML e-mail link. Usage example::

		echo mailto('me@my-site.com', 'Click Here to Contact Me');

	As with the :php:func:`anchor()` tab above, you can set attributes using the
	third parameter::

		$attributes = array('title' => 'Mail me');
		echo mailto('me@my-site.com', 'Contact Me', $attributes);

.. php:function:: safe_mailto($email, $title = '', $attributes = '')

	:param	string	$email: E-mail address
	:param	string	$title: Anchor title
	:param	mixed	$attributes: HTML attributes
	:returns:	A spam-safe "mail to" hyperlink
	:rtype:	string

	Identical to the :php:func:`mailto()` function except it writes an obfuscated
	version of the *mailto* tag using ordinal numbers written with JavaScript to
	help prevent the e-mail address from being harvested by spam bots.

.. php:function:: auto_link($str, $type = 'both', $popup = FALSE)

	:param	string	$str: Input string
	:param	string	$type: Link type ('email', 'url' or 'both')
	:param	bool	$popup: Whether to create popup links
	:returns:	Linkified string
	:rtype:	string

	Automatically turns URLs and e-mail addresses contained in a string into
	links. Example::

		$string = auto_link($string);

	The second parameter determines whether URLs and e-mails are converted or
	just one or the other. Default behavior is both if the parameter is not
	specified. E-mail links are encoded as :php:func:`safe_mailto()` as shown
	above.

	Converts only URLs::

		$string = auto_link($string, 'url');

	Converts only e-mail addresses::

		$string = auto_link($string, 'email');

	The third parameter determines whether links are shown in a new window.
	The value can be TRUE or FALSE (boolean)::

		$string = auto_link($string, 'both', TRUE);


.. php:function:: url_title($str, $separator = '-', $lowercase = FALSE)

	:param	string	$str: Input string
	:param	string	$separator: Word separator
	:param	bool	$lowercase: Whether to transform the output string to lower-case
	:returns:	URL-formatted string
	:rtype:	string

	Takes a string as input and creates a human-friendly URL string. This is
	useful if, for example, you have a blog in which you'd like to use the
	title of your entries in the URL. Example::

		$title = "What's wrong with CSS?";
		$url_title = url_title($title);
		// Produces: Whats-wrong-with-CSS

	The second parameter determines the word delimiter. By default dashes
	are used. Preferred options are: **-** (dash) or **_** (underscore)

	Example::

		$title = "What's wrong with CSS?";
		$url_title = url_title($title, 'underscore');
		// Produces: Whats_wrong_with_CSS

	.. note:: Old usage of 'dash' and 'underscore' as the second parameter
		is DEPRECATED.

	The third parameter determines whether or not lowercase characters are
	forced. By default they are not. Options are boolean TRUE/FALSE.

	Example::

		$title = "What's wrong with CSS?";
		$url_title = url_title($title, 'underscore', TRUE);
		// Produces: whats_wrong_with_css


.. php:function:: prep_url($str = '')

	:param	string	$str: URL string
	:returns:	Protocol-prefixed URL string
	:rtype:	string

	This function will add http&#58;// in the event that a protocol prefix
	is missing from a URL.

	Pass the URL string to the function like this::

		$url = prep_url('example.com');


.. php:function:: redirect($uri = '', $method = 'auto', $code = NULL)

	:param	string	$uri: URI string
	:param	string	$method: Redirect method ('auto', 'location' or 'refresh')
	:param	string	$code: HTTP Response code (usually 302 or 303)
	:rtype:	void

	Does a "header redirect" to the URI specified. If you specify the full
	site URL that link will be built, but for local links simply providing
	the URI segments to the controller you want to direct to will create the
	link. The function will build the URL based on your config file values.

	The optional second parameter allows you to force a particular redirection
	method. The available methods are **auto**, **location** and **refresh**,
	with location being faster but less reliable on IIS servers.
	The default is **auto**, which will attempt to intelligently choose the
	method based on the server environment.

	The optional third parameter allows you to send a specific HTTP Response
	Code - this could be used for example to create 301 redirects for search
	engine purposes. The default Response Code is 302. The third parameter is
	*only* available with **location** redirects, and not *refresh*. Examples::

		if ($logged_in == FALSE)
		{      
			redirect('/login/form/');
		}

		// with 301 redirect
		redirect('/article/13', 'location', 301);

	.. note:: In order for this function to work it must be used before anything
		is outputted to the browser since it utilizes server headers.

	.. note:: For very fine grained control over headers, you should use the
		:doc:`Output Library </libraries/output>` ``set_header()`` method.

	.. note:: To IIS users: if you hide the `Server` HTTP header, the *auto*
		method won't detect IIS, in that case it is advised you explicitly
		use the **refresh** method.

	.. note:: When the **location** method is used, an HTTP status code of 303
		will *automatically* be selected when the page is currently accessed
		via POST and HTTP/1.1 is used.

	.. important:: This function will terminate script execution.
