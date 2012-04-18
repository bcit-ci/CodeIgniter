###########
File Helper
###########

The File Helper file contains functions that assist in working with files.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('file');

The following functions are available:

read_file('path')
=================

Returns the data contained in the file specified in the path. Example

::

	$string = read_file('./path/to/file.php');

The path can be a relative or full server path. Returns FALSE (boolean) on failure.

.. note:: The path is relative to your main site index.php file, NOT your
	controller or view files. CodeIgniter uses a front controller so paths
	are always relative to the main site index.

If your server is running an `open_basedir` restriction this function might not work if you are trying to access a file above the calling script.

write_file('path', $data)
=========================

Writes data to the file specified in the path. If the file does not exist the function will create it. Example

::

	$data = 'Some file data';
	if ( ! write_file('./path/to/file.php', $data))
	{     
		echo 'Unable to write the file';
	}
	else
	{     
		echo 'File written!';
	}

You can optionally set the write mode via the third parameter

::

	write_file('./path/to/file.php', $data, 'r+');

The default mode is wb. Please see the `PHP user guide <http://php.net/fopen>`_ for mode options.

Note: In order for this function to write data to a file its file permissions must be set such that it is writable (666, 777, etc.). If the file does not already exist, the directory containing it must be writable.

.. note:: The path is relative to your main site index.php file, NOT your
	controller or view files. CodeIgniter uses a front controller so paths
	are always relative to the main site index.

delete_files('path')
====================

Deletes ALL files contained in the supplied path. Example

::

	delete_files('./path/to/directory/');

If the second parameter is set to true, any directories contained within the supplied root path will be deleted as well. Example

::

	delete_files('./path/to/directory/', TRUE);

.. note:: The files must be writable or owned by the system in order to be deleted.

get_filenames('path/to/directory/')
===================================

Takes a server path as input and returns an array containing the names of all files contained within it. The file path can optionally be added to the file names by setting the second parameter to TRUE.

get_dir_file_info('path/to/directory/', $top_level_only = TRUE)
===============================================================

Reads the specified directory and builds an array containing the filenames, filesize, dates, and permissions. Sub-folders contained within the specified path are only read if forced by sending the second parameter, $top_level_only to FALSE, as this can be an intensive operation.

get_file_info('path/to/file', $file_information)
================================================

Given a file and path, returns the name, path, size, date modified. Second parameter allows you to explicitly declare what information you want returned; options are: `name`, `server_path`, `size`, `date`, `readable`, `writable`, `executable`, `fileperms`. Returns FALSE if the file cannot be found.

.. note:: The "writable" uses the PHP function is_writable() which is known
	to have issues on the IIS webserver. Consider using fileperms instead,
	which returns information from PHP's fileperms() function.

get_mime_by_extension('file')
=============================

Translates a file extension into a mime type based on config/mimes.php. Returns FALSE if it can't determine the type, or open the mime config file.

::

	$file = "somefile.png";
	echo $file . ' is has a mime type of ' . get_mime_by_extension($file);


.. note:: This is not an accurate way of determining file mime types, and
	is here strictly as a convenience. It should not be used for security.

symbolic_permissions($perms)
============================

Takes numeric permissions (such as is returned by `fileperms()` and returns standard symbolic notation of file permissions.

::

	echo symbolic_permissions(fileperms('./index.php'));  // -rw-r--r--

octal_permissions($perms)
=========================

Takes numeric permissions (such as is returned by fileperms() and returns a three character octal notation of file permissions.

::

	echo octal_permissions(fileperms('./index.php'));  // 644

