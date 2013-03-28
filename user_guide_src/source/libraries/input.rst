###########
Input Class
###########

The Input Class serves two purposes:

#. It pre-processes global input data for security.
#. It provides some helper methods for fetching input data and pre-processing it.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

Security Filtering
==================

The security filtering method is called automatically when a new
:doc:`controller <../general/controllers>` is invoked. It does the
following:

-  If $config['allow_get_array'] is FALSE (default is TRUE), destroys
   the global GET array.
-  Destroys all global variables in the event register_globals is
   turned on.
-  Filters the GET/POST/COOKIE array keys, permitting only alpha-numeric
   (and a few other) characters.
-  Provides XSS (Cross-site Scripting Hacks) filtering. This can be
   enabled globally, or upon request.
-  Standardizes newline characters to \\n(In Windows \\r\\n)

XSS Filtering
=============

The Input class has the ability to filter input automatically to prevent
cross-site scripting attacks. If you want the filter to run
automatically every time it encounters POST or COOKIE data you can
enable it by opening your application/config/config.php file and setting
this::

	$config['global_xss_filtering'] = TRUE;

Please refer to the :doc:`Security class <security>` documentation for
information on using XSS Filtering in your application.

Using POST, GET, COOKIE, or SERVER Data
=======================================

CodeIgniter comes with four helper methods that let you fetch POST, GET,
COOKIE or SERVER items. The main advantage of using the provided
methods rather than fetching an item directly (``$_POST['something']``)
is that the methods will check to see if the item is set and return
NULL if not. This lets you conveniently use data without
having to test whether an item exists first. In other words, normally
you might do something like this::

	$something = isset($_POST['something']) ? $_POST['something'] : NULL;

With CodeIgniter's built in methods you can simply do this::

	$something = $this->input->post('something');

The four methods are:

-  $this->input->post()
-  $this->input->get()
-  $this->input->cookie()
-  $this->input->server()

$this->input->post()
====================

The first parameter will contain the name of the POST item you are
looking for::

	$this->input->post('some_data');

The method returns NULL if the item you are attempting to retrieve
does not exist.

The second optional parameter lets you run the data through the XSS
filter. It's enabled by setting the second parameter to boolean TRUE;

::

	$this->input->post('some_data', TRUE);

To return an array of all POST items call without any parameters.

To return all POST items and pass them through the XSS filter set the
first parameter NULL while setting the second parameter to boolean;

The method returns NULL if there are no items in the POST.

::

	$this->input->post(NULL, TRUE); // returns all POST items with XSS filter
	$this->input->post(); // returns all POST items without XSS filter

$this->input->get()
===================

This method is identical to the post method, only it fetches get data
::

	$this->input->get('some_data', TRUE);

To return an array of all GET items call without any parameters.

To return all GET items and pass them through the XSS filter set the
first parameter NULL while setting the second parameter to boolean;

The method returns NULL if there are no items in the GET.

::

	$this->input->get(NULL, TRUE); // returns all GET items with XSS filter
	$this->input->get(); // returns all GET items without XSS filtering


$this->input->get_post()
========================

This method will search through both the post and get streams for
data, looking first in post, and then in get::

	$this->input->get_post('some_data', TRUE);

$this->input->cookie()
======================

This method is identical to the post method, only it fetches cookie data
::

	$this->input->cookie('some_cookie');
	$this->input->cookie('some_cookie, TRUE); // with XSS filter


$this->input->server()
======================

This method is identical to the above methods, only it fetches server
server data::

	$this->input->server('some_data');

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

Similar to the methods above, if the requested data is not found, it
will return NULL and you can also decide whether to run the data
through ``xss_clean()`` by passing a boolean value as the second
parameter::

	$this->input->input_stream('key', TRUE); // XSS Clean
	$this->input->input_stream('key', FALSE); // No XSS filter

.. note:: You can utilize method() in order to know if you're reading
	PUT, DELETE or PATCH data.

$this->input->set_cookie()
==========================

Sets a cookie containing the values you specify. There are two ways to
pass information to this method so that a cookie can be set: Array
Method, and Discrete Parameters:

Array Method
^^^^^^^^^^^^

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

**Notes:**

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

Discrete Parameters
^^^^^^^^^^^^^^^^^^^

If you prefer, you can set the cookie by passing data using individual
parameters::

	$this->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure);


$this->input->ip_address()
==========================

Returns the IP address for the current user. If the IP address is not
valid, the method will return an IP of: 0.0.0.0

::

	echo $this->input->ip_address();

$this->input->valid_ip($ip)
===========================

Takes an IP address as input and returns TRUE or FALSE (boolean) if it
is valid or not.

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

$this->input->user_agent()
==========================

Returns the user agent (web browser) being used by the current user.
Returns FALSE if it's not available.

::

	echo $this->input->user_agent();

See the :doc:`User Agent Class <user_agent>` for methods which extract
information from the user agent string.

$this->input->request_headers()
===============================

Useful if running in a non-Apache environment where
`apache_request_headers() <http://php.net/apache_request_headers>`_
will not be supported. Returns an array of headers.

::

	$headers = $this->input->request_headers();

$this->input->get_request_header()
==================================

Returns a single member of the request headers array.

::

	$this->input->get_request_header('some-header', TRUE);

$this->input->is_ajax_request()
===============================

Checks to see if the HTTP_X_REQUESTED_WITH server header has been
set, and returns a boolean response.

$this->input->is_cli_request()
==============================

Checks to see if the STDIN constant is set, which is a failsafe way to
see if PHP is being run on the command line.

::

	$this->input->is_cli_request()

$this->input->method()
======================

Returns the $_SERVER['REQUEST_METHOD'], optional set uppercase or lowercase (default lowercase).

::

	echo $this->input->method(TRUE); // Outputs: POST
	echo $this->input->method(FALSE); // Outputs: post
	echo $this->input->method(); // Outputs: post