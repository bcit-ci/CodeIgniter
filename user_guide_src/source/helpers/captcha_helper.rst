##############
CAPTCHA Helper
##############

The CAPTCHA Helper file contains functions that assist in creating
CAPTCHA images.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code
::

	$this->load->helper('captcha');

The following functions are available:

create_captcha()
================

.. php:function:: function create_captcha($data = '')

	:param	array	$data: Array of data for the CAPTCHA
	:returns:	$word: Generatad string of CAPTCHA

Takes an array of information to generate the CAPTCHA as input and
creates the image to your specifications, returning a string of
generated CAPTCHA.


Using the CAPTCHA helper
------------------------

Once loaded you can generate a captcha like this::

	$prefs = array(
		'word'			=> 'Random word',
		'font_path'		=> './path/to/fonts/texb.ttf',
		'img_width'		=> '100',
		'img_height'		=> 30,
		'random_str_length' 	=> 5,
		'border' 		=> FALSE		
	);

	$word = create_captcha_stream($prefs);
	$this->session->set_flashdata('word', $word);

-  The captcha function requires the GD image library.
-  All parameters are optional.
-  If a **word** is not supplied, the function will generate a random
   ASCII string. You might put together your own word library that you
   can draw randomly from.
-  If you do not specify a path to a TRUE TYPE font, the native ugly GD
   font will be used.

To create an image you need to add <img> tag, with path to your captcha
controller, to view file::

<img src=" http://yoursite.com/captcha_controller " />

Now when generating captcha into the session will be recorded string that displayed on the image.


Check the entered data::

	$captcha = trim($this->input->post('captcha')); // string that you get from form submit
	$word = $this->session->flashdata('word'); 	// string in session that generatad on image

	if ($word == $captcha)
	{
		echo "Login sucsessfull";
	}
	else
	{
		echo "Bad login";
	}

