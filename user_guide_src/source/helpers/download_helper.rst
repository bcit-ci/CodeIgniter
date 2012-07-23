###############
Download Helper
###############

The Download Helper lets you download data to your desktop.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('download');

The following functions are available:

force_download('filename', 'data')
==================================

Generates server headers which force data to be downloaded to your
desktop. Useful with file downloads. The first parameter is the **name
you want the downloaded file to be named**, the second parameter is the
file data. Example

::

	$data = 'Here is some text!';
	$name = 'mytext.txt';
	force_download($name, $data);

If you want to download an existing file from your server you'll need to
read the file into a string

::

	$data = file_get_contents("/path/to/photo.jpg"); // Read the file's contents
	$name = 'myphoto.jpg';
	force_download($name, $data);

