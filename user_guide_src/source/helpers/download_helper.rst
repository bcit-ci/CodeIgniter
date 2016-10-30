###############
Download Helper
###############

The Download Helper lets you download data to your desktop.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('download');

Available Functions
===================

The following functions are available:


.. php:function:: force_download([$filename = ''[, $data = ''[, $set_mime = FALSE]]])

	:param	mixed	$filename: Filename
	:param	mixed	$data: File contents
	:param	bool	$set_mime: Whether to try to send the actual MIME type
	:rtype:	void

	Generates server headers which force data to be downloaded to your
	desktop. Useful with file downloads. The first parameter is the **name
	you want the downloaded file to be named**, the second parameter is the
	file data.

	If you set the second parameter to NULL and ``$filename`` is an existing, readable
	file path, then its content will be read instead. You may also set ``$filename``
	as an array containing a single entry, the key being the existing, readable file path
	and the value as the downloading file name.

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

	If you want to download an existing file from your server, but change the name
	of the actual file sent to browser, you will need this::

		// Contents of photo.jpg will be automatically read and sent as my-photo.jpg
		force_download(array('/path/to/photo.jpg' => 'my-photo.jpg'), NULL);