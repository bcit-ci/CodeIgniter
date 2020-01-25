####################
File Uploading Class
####################

CodeIgniter's File Uploading Class permits files to be uploaded. You can
set various preferences, restricting the type and size of the files.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

***********
The Process
***********

Uploading a file involves the following general process:

-  An upload form is displayed, allowing a user to select a file and
   upload it.
-  When the form is submitted, the file is uploaded to the destination
   you specify.
-  Along the way, the file is validated to make sure it is allowed to be
   uploaded based on the preferences you set.
-  Once uploaded, the user will be shown a success message.

To demonstrate this process here is brief tutorial. Afterward you'll
find reference information.

Creating the Upload Form
========================

Using a text editor, create a form called upload_form.php. In it, place
this code and save it to your **application/views/** directory::

	<html lang="en">
	<head>
	<title>Upload Form</title>
	</head>
	<body>

	<?php echo $error;?>

	<?php echo form_open_multipart('upload/do_upload');?>

	<input type="file" name="userfile" size="20" />

	<br /><br />

	<input type="submit" value="upload" />

	</form>

	</body>
	</html>

You'll notice we are using a form helper to create the opening form tag.
File uploads require a multipart form, so the helper creates the proper
syntax for you. You'll also notice we have an $error variable. This is
so we can show error messages in the event the user does something
wrong.

The Success Page
================

Using a text editor, create a form called upload_success.php. In it,
place this code and save it to your **application/views/** directory::

	<html lang="en">
	<head>
	<title>Upload Form</title>
	</head>
	<body>

	<h3>Your file was successfully uploaded!</h3>

	<ul>
	<?php foreach ($upload_data as $item => $value):?>
	<li><?php echo $item;?>: <?php echo $value;?></li>
	<?php endforeach; ?>
	</ul>

	<p><?php echo anchor('upload', 'Upload Another File!'); ?></p>

	</body>
	</html>

The Controller
==============

Using a text editor, create a controller called Upload.php. In it, place
this code and save it to your **application/controllers/** directory::

	<?php

	class Upload extends CI_Controller {

		public function __construct()
		{
			parent::__construct();
			$this->load->helper(array('form', 'url'));
		}

		public function index()
		{
			$this->load->view('upload_form', array('error' => ' ' ));
		}

		public function do_upload()
		{
			$config['upload_path']		= './uploads/';
			$config['allowed_types']	= 'gif|jpg|png';
			$config['max_size']		= 100;
			$config['max_width']		= 1024;
			$config['max_height']		= 768;

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('userfile'))
			{
				$error = array('error' => $this->upload->display_errors());

				$this->load->view('upload_form', $error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());

				$this->load->view('upload_success', $data);
			}
		}
	}
	?>

The Upload Directory
====================

You'll need a destination directory for your uploaded images. Create a
directory at the root of your CodeIgniter installation called uploads
and set its file permissions to 777.

Try it!
=======

To try your form, visit your site using a URL similar to this one::

	example.com/index.php/upload/

You should see an upload form. Try uploading an image file (either a
jpg, gif, or png). If the path in your controller is correct it should
work.

***************
Reference Guide
***************

Initializing the Upload Class
=============================

Like most other classes in CodeIgniter, the Upload class is initialized
in your controller using the ``$this->load->library()`` method::

	$this->load->library('upload');

Once the Upload class is loaded, the object will be available using:
$this->upload

Setting Preferences
===================

Similar to other libraries, you'll control what is allowed to be upload
based on your preferences. In the controller you built above you set the
following preferences::

	$config['upload_path'] = './uploads/';
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']	= '100';
	$config['max_width'] = '1024';
	$config['max_height'] = '768';

	$this->load->library('upload', $config);

	// Alternately you can set preferences by calling the ``initialize()`` method. Useful if you auto-load the class:
	$this->upload->initialize($config);

The above preferences should be fairly self-explanatory. Below is a
table describing all available preferences.

Preferences
===========

The following preferences are available. The default value indicates
what will be used if you do not specify that preference.

============================ ================= ======================= ======================================================================
Preference                   Default Value     Options                 Description
============================ ================= ======================= ======================================================================
**upload_path**              None              None                    The path to the directory where the upload should be placed. The
                                                                       directory must be writable and the path can be absolute or relative.
**allowed_types**            None              None                    The mime types corresponding to the types of files you allow to be
                                                                       uploaded. Usually the file extension can be used as the mime type.
                                                                       Can be either an array or a pipe-separated string.
**file_name**                None              Desired file name       If set CodeIgniter will rename the uploaded file to this name. The
                                                                       extension provided in the file name must also be an allowed file type.
                                                                       If no extension is provided in the original file_name will be used.
**file_ext_tolower**         FALSE             TRUE/FALSE (boolean)    If set to TRUE, the file extension will be forced to lower case
**overwrite**                FALSE             TRUE/FALSE (boolean)    If set to true, if a file with the same name as the one you are
                                                                       uploading exists, it will be overwritten. If set to false, a number will
                                                                       be appended to the filename if another with the same name exists.
**max_size**                 0                 None                    The maximum size (in kilobytes) that the file can be. Set to zero for no
                                                                       limit. Note: Most PHP installations have their own limit, as specified
                                                                       in the php.ini file. Usually 2 MB (or 2048 KB) by default.
**max_width**                0                 None                    The maximum width (in pixels) that the image can be. Set to zero for no
                                                                       limit.
**max_height**               0                 None                    The maximum height (in pixels) that the image can be. Set to zero for no
                                                                       limit.
**min_width**                0                 None                    The minimum width (in pixels) that the image can be. Set to zero for no
                                                                       limit.
**min_height**               0                 None                    The minimum height (in pixels) that the image can be. Set to zero for no
                                                                       limit.
**max_filename**             0                 None                    The maximum length that a file name can be. Set to zero for no limit.
**max_filename_increment**   100               None                    When overwrite is set to FALSE, use this to set the maximum filename
                                                                       increment for CodeIgniter to append to the filename.
**encrypt_name**             FALSE             TRUE/FALSE (boolean)    If set to TRUE the file name will be converted to a random encrypted
                                                                       string. This can be useful if you would like the file saved with a name
                                                                       that can not be discerned by the person uploading it.
**remove_spaces**            TRUE              TRUE/FALSE (boolean)    If set to TRUE, any spaces in the file name will be converted to
                                                                       underscores. This is recommended.
**detect_mime**              TRUE              TRUE/FALSE (boolean)    If set to TRUE, a server side detection of the file type will be
                                                                       performed to avoid code injection attacks. DO NOT disable this option
                                                                       unless you have no other option as that would cause a security risk.
**mod_mime_fix**             TRUE              TRUE/FALSE (boolean)    If set to TRUE, multiple filename extensions will be suffixed with an
                                                                       underscore in order to avoid triggering `Apache mod_mime
                                                                       <https://httpd.apache.org/docs/2.0/mod/mod_mime.html#multipleext>`_.
                                                                       DO NOT turn off this option if your upload directory is public, as this
                                                                       is a security risk.
============================ ================= ======================= ======================================================================

Setting preferences in a config file
====================================

If you prefer not to set preferences using the above method, you can
instead put them into a config file. Simply create a new file called the
upload.php, add the $config array in that file. Then save the file in:
**config/upload.php** and it will be used automatically. You will NOT
need to use the ``$this->upload->initialize()`` method if you save your
preferences in a config file.

***************
Class Reference
***************

.. php:class:: CI_Upload

	.. php:method:: initialize([array $config = array()[, $reset = TRUE]])

		:param	array	$config: Preferences
		:param	bool	$reset: Whether to reset preferences (that are not provided in $config) to their defaults
		:returns:	CI_Upload instance (method chaining)
		:rtype:	CI_Upload

	.. php:method:: do_upload([$field = 'userfile'])

		:param	string	$field: Name of the form field
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Performs the upload based on the preferences you've set.

		.. note:: By default the upload routine expects the file to come from
			a form field called userfile, and the form must be of type
			"multipart".

		::

			<form method="post" action="some_action" enctype="multipart/form-data" />

		If you would like to set your own field name simply pass its value to
		the ``do_upload()`` method::

			$field_name = "some_field_name";
			$this->upload->do_upload($field_name);

	.. php:method:: display_errors([$open = '<p>'[, $close = '</p>']])

		:param	string	$open: Opening markup
		:param	string	$close: Closing markup
		:returns:	Formatted error message(s)
		:rtype:	string

		Retrieves any error messages if the ``do_upload()`` method returned
		false. The method does not echo automatically, it returns the data so
		you can assign it however you need.

		**Formatting Errors**

			By default the above method wraps any errors within <p> tags. You can
			set your own delimiters like this::

				$this->upload->display_errors('<p>', '</p>');


	.. php:method:: data([$index = NULL])

		:param	string	$data: Element to return instead of the full array
		:returns:	Information about the uploaded file
		:rtype:	mixed

		This is a helper method that returns an array containing all of the
		data related to the file you uploaded. Here is the array prototype::

			Array
			(
				[file_name]	=> mypic.jpg
				[file_type]	=> image/jpeg
				[file_path]	=> /path/to/your/upload/
				[full_path]	=> /path/to/your/upload/jpg.jpg
				[raw_name]	=> mypic
				[orig_name]	=> mypic.jpg
				[client_name]	=> mypic.jpg
				[file_ext]	=> .jpg
				[file_size]	=> 22.2
				[is_image]	=> 1
				[image_width]	=> 800
				[image_height]	=> 600
				[image_type]	=> jpeg
				[image_size_str] => width="800" height="200"
			)

		To return one element from the array::

			$this->upload->data('file_name');	// Returns: mypic.jpg

		Here's a table explaining the above-displayed array items:

		================ ====================================================================================================
		Item             Description
		================ ====================================================================================================
		file_name        Name of the file that was uploaded, including the filename extension
		file_type        File MIME type identifier
		file_path        Absolute server path to the file
		full_path        Absolute server path, including the file name
		raw_name         File name, without the extension
		orig_name        Original file name. This is only useful if you use the encrypted name option.
		client_name      File name supplied by the client user agent, but possibly sanitized
		file_ext         Filename extension, period included
		file_size        File size in kilobytes
		is_image         Whether the file is an image or not. 1 = image. 0 = not.
		image_width      Image width
		image_height     Image height
		image_type       Image type (usually the file name extension without the period)
		image_size_str   A string containing the width and height (useful to put into an image tag)
		================ ====================================================================================================
