##############
Error Handling
##############

In CodeIgniter, it is encouraged to use PHP exceptions to perform
error handling. CodeIgniter provides an error template page to
display error messages to end users. You will need to invoke Exceptions
class to render the error template. In addition, CodeIgniter has an error
logging class that permits error and debugging messages to be saved
as text files.

.. note:: By default, CodeIgniter displays all PHP errors. You might
	wish to change this behavior once your development is complete. You'll
	find the error_reporting() function located at the top of your main
	index.php file. Disabling error reporting will NOT prevent log files
	from being written if there are errors.

Unlike most systems in CodeIgniter, the error functions are simple
procedural interfaces that are available globally throughout the
application. This approach permits error messages to get triggered
without having to worry about class/function scoping.

Example of rendering general error page::

	$_error =& load_class('Exceptions', 'core');
	echo $_error->show_error($heading, $message, 'error_general', $status_code);

This above code will display the error message supplied to it using
the error template appropriate to your execution::

	application/views/errors/html/error_general.php

or::

	application/views/errors/cli/error_general.php

The following functions let you generate errors:

.. php:function:: show_404($page = '', $log_error = TRUE)

	:param	string	$page: URI string
	:param	bool	$log_error: Whether to log the error
	:rtype:	void

	This function will display the 404 error message supplied to it
	using the error template appropriate to your execution::

		application/views/errors/html/error_404.php

	or:

		application/views/errors/cli/error_404.php

	The function expects the string passed to it to be the file path to
	the page that isn't found. The exit status code will be set to
	``EXIT_UNKNOWN_FILE``.
	Note that CodeIgniter automatically shows 404 messages if
	controllers are not found.

	CodeIgniter automatically logs any ``show_404()`` calls. Setting the
	optional second parameter to FALSE will skip logging.

.. php:function:: log_message($level, $message, $php_error = FALSE)

	:param	string	$level: Log level: 'error', 'debug' or 'info'
	:param	string	$message: Message to log
	:param	bool	$php_error: Whether we're logging a native PHP error message
	:rtype:	void

	This function lets you write messages to your log files. You must
	supply one of three "levels" in the first parameter, indicating what
	type of message it is (debug, error, info), with the message itself
	in the second parameter.

	Example::

		if ($some_var == '')
		{
			log_message('error', 'Some variable did not contain a value.');
		}
		else
		{
			log_message('debug', 'Some variable was correctly set');
		}

		log_message('info', 'The purpose of some variable is to provide some value.');

	There are three message types:

	#. Error Messages. These are actual errors, such as PHP errors or
	   user errors.
	#. Debug Messages. These are messages that assist in debugging. For
	   example, if a class has been initialized, you could log this as
	   debugging info.
	#. Informational Messages. These are the lowest priority messages,
	   simply giving information regarding some process.

	.. note:: In order for the log file to actually be written, the
		*logs/* directory must be writable. In addition, you must
		set the "threshold" for logging in
		*application/config/config.php*. You might, for example,
		only want error messages to be logged, and not the other
		two types. If you set it to zero logging will be disabled.
