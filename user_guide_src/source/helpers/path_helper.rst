###########
Path Helper
###########

The Path Helper file contains functions that permits you to work with
file paths on the server.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('path');

The following functions are available:

set_realpath()
==============

Checks to see if the path exists. This function will return a server
path without symbolic links or relative directory structures. An
optional second argument will cause an error to be triggered if the path
cannot be resolved.

::

	$file = '/etc/php5/apache2/php.ini';
	echo set_realpath($file); // returns "/etc/php5/apache2/php.ini"

	$non_existent_file = '/path/to/non-exist-file.txt';
	echo set_realpath($non_existent_file, TRUE);	// shows an error, as the path cannot be resolved
	echo set_realpath($non_existent_file, FALSE);	// returns "/path/to/non-exist-file.txt"

	$directory = '/etc/php5';
	echo set_realpath($directory);	// returns "/etc/php5/"
	
	$non_existent_directory = '/path/to/nowhere';
	echo set_realpath($non_existent_directory, TRUE);	// shows an error, as the path cannot be resolved
	echo set_realpath($non_existent_directory, FALSE);	// returns "/path/to/nowhere"
