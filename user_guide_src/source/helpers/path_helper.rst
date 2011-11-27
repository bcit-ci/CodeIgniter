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

	$directory = '/etc/passwd'; 
	echo set_realpath($directory); // returns "/etc/passwd"  
	$non_existent_directory = '/path/to/nowhere'; 
	echo set_realpath($non_existent_directory, TRUE); // returns an error, as the path could not be resolved  
	echo set_realpath($non_existent_directory, FALSE); // returns "/path/to/nowhere"   


