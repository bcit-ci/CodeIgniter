##############
Error Handling
##############

CodeIgniter lets you build error reporting into your applications using
the functions described below. In addition, it has an error logging
class that permits error and debugging messages to be saved as text
files.

.. note:: By default, CodeIgniter displays all PHP errors. You might
	wish to change this behavior once your development is complete. You'll
	find the error_reporting() function located at the top of your main
	index.php file. Disabling error reporting will NOT prevent log files
	from being written if there are errors.

Unlike most systems in CodeIgniter, the error functions are simple
procedural interfaces that are available globally throughout the
application. This approach permits error messages to get triggered
without having to worry about class/function scoping.

The following functions let you generate errors:

show_error()
============

.. php:function:: show_error($message, $status_code, $heading = 'An Error Was Encountered')

	:param	mixed	$message: Error message
	:param	int	$status_code: HTTP Response status code
	:param	string	$heading: Error page heading
	:returns:	void

This function will display the error message supplied to it using the
following error template::

	application/errors/error_general.php

The optional parameter ``$status_code`` determines what HTTP status
code should be sent with the error.

show_404()
==========

.. php:function:: show_404($page = '', $log_error = TRUE)

	:param	string	$page: URI string
	:param	bool	$log_error: Whether to log the error
	:returns:	void

This function will display the 404 error message supplied to it using
the following error template::

	application/errors/error_404.php

The function expects the string passed to it to be the file path to the
page that isn't found. Note that CodeIgniter automatically shows 404
messages if controllers are not found.

CodeIgniter automatically logs any ``show_404()`` calls. Setting the
optional second parameter to FALSE will skip logging.

log_message()
=============

.. php:function:: log_message($level = 'error', $message, $php_error = FALSE)

	:param	string	$level: Log level
	:param	string	$message: Message to log
	:param	bool	$php_error: Whether we're loggin a native PHP error message
	:returns:	void

This function lets you write messages to your log files. You must supply
one of three "levels" in the first parameter, indicating what type of
message it is (debug, error, info), with the message itself in the
second parameter.

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

#. Error Messages. These are actual errors, such as PHP errors or user
   errors.
#. Debug Messages. These are messages that assist in debugging. For
   example, if a class has been initialized, you could log this as
   debugging info.
#. Informational Messages. These are the lowest priority messages,
   simply giving information regarding some process. CodeIgniter doesn't
   natively generate any info messages but you may want to in your
   application.

.. note:: In order for the log file to actually be written, the *logs*
	directory must be writable. In addition, you must set the "threshold"
	for logging in *application/config/config.php*. You might, for example,
	only want error messages to be logged, and not the other two types.
	If you set it to zero logging will be disabled.