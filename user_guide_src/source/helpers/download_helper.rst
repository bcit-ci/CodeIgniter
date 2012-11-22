###############
Download Helper
###############

The Download Helper lets you download data to your desktop.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('download');

The following functions are available:

force_download()
================

.. php:function:: force_download($filename = '', $data = '', $set_mime = FALSE)

	:param	string	$filename: Filename
	:param	mixed	$data: File contents
	:param	bool	$set_mime: Whether to try to send the actual MIME type
	:returns:	void

Generates server headers which force data to be downloaded to your
desktop. Useful with file downloads. The first parameter is the **name
you want the downloaded file to be named**, the second parameter is the
file data.

If you set the second parameter to NULL and ``$filename`` is an existing, readable
file path, then its content will be read instead.

If you set the third parameter to boolean TRUE, then the actual file MIME type
(based on the filename extension) will be sent, so that if your browser has a
handler for that type - it can use it.

Example::

	$data = 'Here is some text!';
	$name = 'mytext.txt';
	force_download($name, $data);

If you want to download an existing file from your server you'll need to
do the following::

	// Contents of photo.jpg will be automatically read
	force_download('/path/to/photo.jpg', NULL);