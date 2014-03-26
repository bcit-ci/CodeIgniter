###########
Input Class
###########

The Input Class serves two purposes:

#. It pre-processes global input data for security.
#. It provides some helper methods for fetching input data and pre-processing it.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Security Filtering
==================

The security filtering method is called automatically when a new
:doc:`controller <../general/controllers>` is invoked. It does the
following:

-  If ``$config['allow_get_array']`` is FALSE (default is TRUE), destroys
   the global GET array.
-  Destroys all global variables in the event register_globals is
   turned on.
-  Filters the GET/POST/COOKIE array keys, permitting only alpha-numeric
   (and a few other) characters.
-  Provides XSS (Cross-site Scripting Hacks) filtering. This can be
   enabled globally, or upon request.
-  Standardizes newline characters to ``PHP_EOL`` (\\n in UNIX-based OSes,
   \\r\\n under Windows). This is configurable.

XSS Filtering
=============

The Input class has the ability to filter input automatically to prevent
cross-site scripting attacks. If you want the filter to run
automatically every time it encounters POST or COOKIE data you can
enable it by opening your *application/config/config.php* file and setting
this::

	$config['global_xss_filtering'] = TRUE;

Please refer to the :doc:`Security class <security>` documentation for
information on using XSS Filtering in your application.

Using POST, GET, COOKIE, or SERVER Data
=======================================

CodeIgniter comes with helper methods that let you fetch POST, GET,
COOKIE or SERVER items. The main advantage of using the provided
methods rather than fetching an item directly (``$_POST['something']``)
is that the methods will check to see if the item is set and return
NULL if not. This lets you conveniently use data without
having to test whether an item exists first. In other words, normally
you might do something like this::

	$something = isset($_POST['something']) ? $_POST['something'] : NULL;

With CodeIgniter's built in methods you can simply do this::

	$something = $this->input->post('something');

The main methods are:

-  ``$this->input->post()``
-  ``$this->input->get()``
-  ``$this->input->cookie()``
-  ``$this->input->server()``

Using the php://input stream
============================

If you want to utilize the PUT, DELETE, PATCH or other exotic request
methods, they can only be accessed via a special input stream, that
can only be read once. This isn't as easy as just reading from e.g.
the ``$_POST`` array, because it will always exist and you can try
and access multiple variables without caring that you might only have
one shot at all of the POST data.

CodeIgniter will take care of that for you, and you can access data
from the **php://input** stream at any time, just by calling the
``input_stream()`` method::

	$this->input->input_stream('key');

Similar to other methods such as ``get()`` and ``post()``, if the
requested data is not found, it will return NULL and you can also
decide whether to run the data through ``xss_clean()`` by passing
a boolean value as the second parameter::

	$this->input->input_stream('key', TRUE); // XSS Clean
	$this->input->input_stream('key', FALSE); // No XSS filter

.. note:: You can utilize ``method()`` in order to know if you're reading
	PUT, DELETE or PATCH data.

***************
Class Reference
***************

.. class:: CI_Input

	.. method:: post([$index = NULL[, $xss_clean = NULL]])

		:param	string	$index: POST parameter name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	$_POST if no parameters supplied, otherwise the POST value if found or NULL if not
		:rtype:	mixed

		The first parameter will contain the name of the POST item you are
		looking for::

			$this->input->post('some_data');

		The method returns NULL if the item you are attempting to retrieve
		does not exist.

		The second optional parameter lets you run the data through the XSS
		filter. It's enabled by setting the second parameter to boolean TRUE
		or by setting your ``$config['global_xss_filtering']`` to TRUE.
		::

			$this->input->post('some_data', TRUE);

		To return an array of all POST items call without any parameters.

		To return all POST items and pass them through the XSS filter set the
		first parameter NULL while setting the second parameter to boolean TRUE.
		::

			$this->input->post(NULL, TRUE); // returns all POST items with XSS filter
			$this->input->post(NULL, FALSE); // returns all POST items without XSS filter

	.. method:: get([$index = NULL[, $xss_clean = NULL]])

		:param	string	$index: GET parameter name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	$_GET if no parameters supplied, otherwise the GET value if found or NULL if not
		:rtype:	mixed

		This method is identical to ``post()``, only it fetches GET data.
		::

			$this->input->get('some_data', TRUE);

		To return an array of all GET items call without any parameters.

		To return all GET items and pass them through the XSS filter set the
		first parameter NULL while setting the second parameter to boolean TRUE.
		::

			$this->input->get(NULL, TRUE); // returns all GET items with XSS filter
			$this->input->get(NULL, FALSE); // returns all GET items without XSS filtering

	.. method:: post_get($index[, $xss_clean = NULL])

		:param	string	$index: POST/GET parameter name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	POST/GET value if found, NULL if not
		:rtype:	mixed

		This method works pretty much the same way as ``post()`` and ``get()``,
		only combined. It will search through both POST and GET streams for data,
		looking in POST first, and then in GET::

			$this->input->post_get('some_data', TRUE);

	.. method:: get_post($index[, $xss_clean = NULL])

		:param	string	$index: GET/POST parameter name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	GET/POST value if found, NULL if not
		:rtype:	mixed

		This method works the same way as ``post_get()`` only it looks for GET
		data first.

			$this->input->get_post('some_data', TRUE);

		.. note:: This method used to act EXACTLY like ``post_get()``, but it's
			behavior has changed in CodeIgniter 3.0.

	.. method:: cookie([$index = NULL[, $xss_clean = NULL]])

		:param	string	$index: COOKIE parameter name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	$_COOKIE if no parameters supplied, otherwise the COOKIE value if found or NULL if not
		:rtype:	mixed

		This method is identical to ``post()`` and ``get()``, only it fetches cookie
		data::

			$this->input->cookie('some_cookie');
			$this->input->cookie('some_cookie, TRUE); // with XSS filter

	.. method:: server($index[, $xss_clean = NULL])

		:param	string	$index: Value name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	$_SERVER item value if found, NULL if not
		:rtype:	mixed

		This method is identical to the ``post()``, ``get()`` and ``cookie()``
		methods, only it fetches server data (``$_SERVER``)::

			$this->input->server('some_data');

	.. method:: input_stream([$index = NULL[, $xss_clean = NULL]])

		:param	string	$index: Key name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	Input stream array if no parameters supplied, otherwise the specified value if found or NULL if not
		:rtype:	mixed

		This method is identical to ``get()``, ``post()`` and ``cookie()``,
		only it fetches the *php://input* stream data.

	.. method:: set_cookie($name = ''[, $value = ''[, $expire = ''[, $domain = ''[, $path = '/'[, $prefix = ''[, $secure = FALSE[, $httponly = FALSE]]]]]]])

		:param	mixed	$name: Cookie name or an array of parameters
		:param	string	$value: Cookie value
		:param	int	$expire: Cookie expiration time in seconds
		:param	string	$domain: Cookie domain
		:param	string	$path: Cookie path
		:param	string	$prefix: Cookie name prefix
		:param	bool	$secure: Whether to only transfer the cookie through HTTPS
		:param	bool	$httponly: Whether to only make the cookie accessible for HTTP requests (no JavaScript)
		:rtype:	void


		Sets a cookie containing the values you specify. There are two ways to
		pass information to this method so that a cookie can be set: Array
		Method, and Discrete Parameters:

		**Array Method**

		Using this method, an associative array is passed to the first
		parameter::

			$cookie = array(
				'name'   => 'The Cookie Name',
				'value'  => 'The Value',
				'expire' => '86500',
				'domain' => '.some-domain.com',
				'path'   => '/',
				'prefix' => 'myprefix_',
				'secure' => TRUE
			);

			$this->input->set_cookie($cookie);

		**Notes**

		Only the name and value are required. To delete a cookie set it with the
		expiration blank.

		The expiration is set in **seconds**, which will be added to the current
		time. Do not include the time, but rather only the number of seconds
		from *now* that you wish the cookie to be valid. If the expiration is
		set to zero the cookie will only last as long as the browser is open.

		For site-wide cookies regardless of how your site is requested, add your
		URL to the **domain** starting with a period, like this:
		.your-domain.com

		The path is usually not needed since the method sets a root path.

		The prefix is only needed if you need to avoid name collisions with
		other identically named cookies for your server.

		The secure boolean is only needed if you want to make it a secure cookie
		by setting it to TRUE.

		**Discrete Parameters**

		If you prefer, you can set the cookie by passing data using individual
		parameters::

			$this->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure);

	.. method:: ip_address()

		:returns:	Visitor's IP address or '0.0.0.0' if not valid
		:rtype:	string

		Returns the IP address for the current user. If the IP address is not
		valid, the method will return '0.0.0.0'::

			echo $this->input->ip_address();

		.. important:: This method takes into account the ``$config['proxy_ips']``
			setting and will return the reported HTTP_X_FORWARDED_FOR,
			HTTP_CLIENT_IP, HTTP_X_CLIENT_IP or HTTP_X_CLUSTER_CLIENT_IP
			address for the allowed IP addresses.

	.. method:: valid_ip($ip[, $which = ''])

		:param	string	$ip: IP address
		:param	string	$which: IP protocol ('ipv4' or 'ipv6')
		:returns:	TRUE if the address is valid, FALSE if not
		:rtype:	bool

		Takes an IP address as input and returns TRUE or FALSE (boolean) depending
		on whether it is valid or not.

		.. note:: The $this->input->ip_address() method above automatically
			validates the IP address.

		::

			if ( ! $this->input->valid_ip($ip))
			{
				echo 'Not Valid';
			}
			else
			{
				echo 'Valid';
			}

		Accepts an optional second string parameter of 'ipv4' or 'ipv6' to specify
		an IP format. The default checks for both formats.

	.. method:: user_agent([$xss_clean = NULL])

		:returns:	User agent string or NULL if not set
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:rtype:	mixed

		Returns the user agent string (web browser) being used by the current user,
		or NULL if it's not available.
		::

			echo $this->input->user_agent();

		See the :doc:`User Agent Class <user_agent>` for methods which extract
		information from the user agent string.

	.. method:: request_headers([$xss_clean = FALSE])

		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	An array of HTTP request headers
		:rtype:	array

		Returns an array of HTTP request headers.
		Useful if running in a non-Apache environment where
		`apache_request_headers() <http://php.net/apache_request_headers>`_
		will not be supported.
		::

			$headers = $this->input->request_headers();

	.. method:: get_request_header($index[, $xss_clean = FALSE])

		:param	string	$index: HTTP request header name
		:param	bool	$xss_clean: Whether to apply XSS filtering
		:returns:	An HTTP request header or NULL if not found
		:rtype:	string

		Returns a single member of the request headers array or NULL
		if the searched header is not found.
		::

			$this->input->get_request_header('some-header', TRUE);

	.. method:: is_ajax_request()

		:returns:	TRUE if it is an Ajax request, FALSE if not
		:rtype:	bool

		Checks to see if the HTTP_X_REQUESTED_WITH server header has been
		set, and returns boolean TRUE if it is or FALSE if not.

	.. method:: is_cli_request()

		:returns:	TRUE if it is a CLI request, FALSE if not
		:rtype:	bool

		Checks to see if the application was run from the command-line
		interface.

		.. note:: This method checks both the PHP SAPI name currently in use
			and if the ``STDIN`` constant is defined, which is usually a
			failsafe way to see if PHP is being run via the command line.

		::

			$this->input->is_cli_request()

		.. note:: This method is DEPRECATED and is now just an alias for the
			:func:`is_cli()` function.

	.. method:: method([$upper = FALSE])

		:param	bool	$upper: Whether to return the request method name in upper or lower case
		:returns:	HTTP request method
		:rtype:	string

		Returns the ``$_SERVER['REQUEST_METHOD']``, with the option to set it
		in uppercase or lowercase.
		::

			echo $this->input->method(TRUE); // Outputs: POST
			echo $this->input->method(FALSE); // Outputs: post
			echo $this->input->method(); // Outputs: post