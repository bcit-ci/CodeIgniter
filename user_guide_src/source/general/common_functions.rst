################
Common Functions
################

CodeIgniter uses a few functions for its operation that are globally
defined, and are available to you at any point. These do not require
loading any libraries or helpers.

is_php('version_number')
==========================

is_php() determines of the PHP version being used is greater than the
supplied version_number.

::

	if (is_php('5.3.0'))
	{
	    $str = quoted_printable_encode($str);
	}

Returns boolean TRUE if the installed version of PHP is equal to or
greater than the supplied version number. Returns FALSE if the installed
version of PHP is lower than the supplied version number.

is_really_writable('path/to/file')
====================================

is_writable() returns TRUE on Windows servers when you really can't
write to the file as the OS reports to PHP as FALSE only if the
read-only attribute is marked. This function determines if a file is
actually writable by attempting to write to it first. Generally only
recommended on platforms where this information may be unreliable.

::

	if (is_really_writable('file.txt'))
	{
	    echo "I could write to this if I wanted to";
	}
	else
	{
	    echo "File is not writable";
	}

config_item('item_key')
=========================

The :doc:`Config library <../libraries/config>` is the preferred way of
accessing configuration information, however config_item() can be used
to retrieve single keys. See Config library documentation for more
information.

show_error('message'), show_404('page'), log_message('level', 'message')
========================================================================

These are each outlined on the :doc:`Error Handling <errors>` page.

set_status_header(code, 'text');
================================

Permits you to manually set a server status header. Example::

	set_status_header(401);
	// Sets the header as:  Unauthorized

`See here <http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html>`_ for
a full list of headers.

remove_invisible_characters($str)
===================================

This function prevents inserting null characters between ascii
characters, like Java\\0script.

html_escape($mixed)
====================

This function provides short cut for htmlspecialchars() function. It
accepts string and array. To prevent Cross Site Scripting (XSS), it is
very useful.
