################
Common Functions
################

CodeIgniter uses a few functions for its operation that are globally
defined, and are available to you at any point. These do not require
loading any libraries or helpers.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

.. php:function:: is_php($version)

	:param	string	$version: Version number
	:returns:	TRUE if the running PHP version is at least the one specified or FALSE if not
	:rtype:	bool

	Determines if the PHP version being used is greater than the
	supplied version number.

	Example::

		if (is_php('5.5'))
		{
			echo json_last_error_msg();
		}

	Returns boolean TRUE if the installed version of PHP is equal to or
	greater than the supplied version number. Returns FALSE if the installed
	version of PHP is lower than the supplied version number.

.. php:function:: is_really_writable($file)

	:param	string	$file: File path
	:returns:	TRUE if the path is writable, FALSE if not
	:rtype:	bool

	``is_writable()`` returns TRUE on Windows servers when you really can't
	write to the file as the OS reports to PHP as FALSE only if the
	read-only attribute is marked.

	This function determines if a file is actually writable by attempting
	to write to it first. Generally only recommended on platforms where
	this information may be unreliable.

	Example::

		if (is_really_writable('file.txt'))
		{
			echo "I could write to this if I wanted to";
		}
		else
		{
			echo "File is not writable";
		}

	.. note:: See also `PHP bug #54709 <https://bugs.php.net/bug.php?id=54709>`_ for more info.

.. php:function:: config_item($key)

	:param	string	$key: Config item key
	:returns:	Configuration key value or NULL if not found
	:rtype:	mixed

	The :doc:`Config Library <../libraries/config>` is the preferred way of
	accessing configuration information, however ``config_item()`` can be used
	to retrieve single keys. See :doc:`Config Library <../libraries/config>`
	documentation for more information.

.. :noindex: function:: show_error($message, $status_code[, $heading = 'An Error Was Encountered'])

	:param	mixed	$message: Error message
	:param	int	$status_code: HTTP Response status code
	:param	string	$heading: Error page heading
	:rtype:	void

	This function calls ``CI_Exception::show_error()``. For more info,
	please see the :doc:`Error Handling <errors>` documentation.

.. :noindex: function:: show_404([$page = ''[, $log_error = TRUE]])

	:param	string	$page: URI string
	:param	bool	$log_error: Whether to log the error
	:rtype:	void

	This function calls ``CI_Exception::show_404()``. For more info,
	please see the :doc:`Error Handling <errors>` documentation.

.. :noindex: function:: log_message($level, $message)

	:param	string	$level: Log level: 'error', 'debug' or 'info'
	:param	string	$message: Message to log
	:rtype:	void

	This function is an alias for ``CI_Log::write_log()``. For more info,
	please see the :doc:`Error Handling <errors>` documentation.

.. php:function:: set_status_header($code[, $text = ''])

	:param	int	$code: HTTP Response status code
	:param	string	$text: A custom message to set with the status code
	:rtype:	void

	Permits you to manually set a server status header. Example::

		set_status_header(401);
		// Sets the header as:  Unauthorized

	`See here <http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html>`_ for
	a full list of headers.

.. php:function:: remove_invisible_characters($str[, $url_encoded = TRUE])

	:param	string	$str: Input string
	:param	bool	$url_encoded: Whether to remove URL-encoded characters as well
	:returns:	Sanitized string
	:rtype:	string

	This function prevents inserting NULL characters between ASCII
	characters, like Java\\0script.

	Example::

		remove_invisible_characters('Java\\0script');
		// Returns: 'Javascript'

.. php:function:: html_escape($var)

	:param	mixed	$var: Variable to escape (string or array)
	:returns:	HTML escaped string(s)
	:rtype:	mixed

	This function acts as an alias for PHP's native ``htmlspecialchars()``
	function, with the advantage of being able to accept an array of strings.

	It is useful in preventing Cross Site Scripting (XSS).

.. php:function:: get_mimes()

	:returns:	An associative array of file types
	:rtype:	array

	This function returns a *reference* to the MIMEs array from
	*application/config/mimes.php*.

.. php:function:: is_https()

	:returns:	TRUE if currently using HTTP-over-SSL, FALSE if not
	:rtype:	bool

	Returns TRUE if a secure (HTTPS) connection is used and FALSE
	in any other case (including non-HTTP requests).

.. php:function:: is_cli()

	:returns:	TRUE if currently running under CLI, FALSE otherwise
	:rtype:	bool

	Returns TRUE if the application is run through the command line
	and FALSE if not.

	.. note:: This function checks both if the ``PHP_SAPI`` value is 'cli'
		or if the ``STDIN`` constant is defined.

.. php:function:: function_usable($function_name)

	:param	string	$function_name: Function name
	:returns:	TRUE if the function can be used, FALSE if not
	:rtype:	bool

	Returns TRUE if a function exists and is usable, FALSE otherwise.

	This function runs a ``function_exists()`` check and if the
	`Suhosin extension <http://www.hardened-php.net/suhosin/>` is loaded,
	checks if it doesn't disable the function being checked.

	It is useful if you want to check for the availability of functions
	such as ``eval()`` and ``exec()``, which are dangerous and might be
	disabled on servers with highly restrictive security policies.

	.. note:: This function was introduced because Suhosin terminated
		script execution, but this turned out to be a bug. A fix
		has been available for some time (version 0.9.34), but is
		unfortunately not released yet.
