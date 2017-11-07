##############
CAPTCHA Helper
##############

The CAPTCHA Helper file contains functions that assist in creating
CAPTCHA images.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('captcha');

Using the CAPTCHA helper
========================

Once loaded you can generate a CAPTCHA like this::

	$vals = array(
		'word'		=> 'Random word',
		'img_path'	=> './captcha/',
		'img_url'	=> 'http://example.com/captcha/',
		'font_path'	=> './path/to/fonts/texb.ttf',
		'img_width'	=> '150',
		'img_height'	=> 30,
		'expiration'	=> 7200,
		'word_length'	=> 8,
		'font_size'	=> 16,
		'img_id'	=> 'Imageid',
		'pool'		=> '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

		// White background and border, black text and red grid
		'colors'	=> array(
			'background' => array(255, 255, 255),
			'border' => array(255, 255, 255),
			'text' => array(0, 0, 0),
			'grid' => array(255, 40, 40)
		)
	);

	$cap = create_captcha($vals);
	echo $cap['image'];

-  The captcha function requires the GD image library.
-  The **img_path** and **img_url** are both required if you want to write images to disk.
   To create ``data:image/png;base64`` images, simply omit these options.
-  If a **word** is not supplied, the function will generate a random
   ASCII string. You might put together your own word library that you
   can draw randomly from.
-  If you do not specify a path to a TRUE TYPE font, the native ugly GD
   font will be used.
-  The "captcha" directory must be writable
-  The **expiration** (in seconds) signifies how long an image will remain
   in the captcha folder before it will be deleted. The default is two
   hours.
-  **word_length** defaults to 8, **pool** defaults to '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
-  **font_size** defaults to 16, the native GD font has a size limit. Specify a "true type" font for bigger sizes.
-  The **img_id** will be set as the "id" of the captcha image.
-  If any of the **colors** values is missing, it will be replaced by the default.

Adding a Database
-----------------

In order for the captcha function to prevent someone from submitting,
you will need to add the information returned from ``create_captcha()``
to your database. Then, when the data from the form is submitted by
the user you will need to verify that the data exists in the database
and has not expired.

Here is a table prototype::

	CREATE TABLE captcha (  
		captcha_id bigint(13) unsigned NOT NULL auto_increment,  
		captcha_time int(10) unsigned NOT NULL,  
		ip_address varchar(45) NOT NULL,  
		word varchar(20) NOT NULL,  
		PRIMARY KEY `captcha_id` (`captcha_id`),  
		KEY `word` (`word`)
	);

Here is an example of usage with a database. On the page where the
CAPTCHA will be shown you'll have something like this::

	$this->load->helper('captcha');

	$cap = create_captcha($vals);
	$data = array(     
		'captcha_time'	=> $cap['time'],     
		'ip_address'	=> $this->input->ip_address(),     
		'word'		=> $cap['word']     
	);

	$query = $this->db->insert_string('captcha', $data);
	$this->db->query($query);

	echo 'Submit the word you see below:';
	echo $cap['image'];
	echo '<input type="text" name="captcha" value="" />';

Then, on the page that accepts the submission you'll have something like
this::

	// First, delete old captchas
	$expiration = time() - 7200; // Two hour limit
	$this->db->where('captcha_time < ', $expiration)
		->delete('captcha');

	// Then see if a captcha exists:
	$sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
	$binds = array($_POST['captcha'], $this->input->ip_address(), $expiration);
	$query = $this->db->query($sql, $binds);
	$row = $query->row();

	if ($row->count == 0)
	{     
		echo 'You must submit the word that appears in the image.';
	}

Available Functions
===================

The following functions are available:

.. php:function:: create_captcha([$data = ''[, $img_path = ''[, $img_url = ''[, $font_path = '']]]])

	:param	array	$data: Array of data for the CAPTCHA
	:param	string	$img_path: Path to create the image in (DEPRECATED)
	:param	string	$img_url: URL to the CAPTCHA image folder (DEPRECATED)
	:param	string	$font_path: Server path to font (DEPRECATED)
	:returns:	array('word' => $word, 'time' => $now, 'image' => $img)
	:rtype:	array

	Takes an array of information to generate the CAPTCHA as input and
	creates the image to your specifications, returning an array of
	associative data about the image.

	::

		array(
			'image'	=> IMAGE TAG
			'time'	=> TIMESTAMP (in microtime)
			'word'	=> CAPTCHA WORD
		)

	The **image** is the actual image tag::

		<img src="data:image/png;base64,RHVtbXkgZXhhbXBsZQ==" width="140" height="50" />

	The **time** is the micro timestamp used as the image name without the
	file extension. It will be a number like this: 1139612155.3422

	The **word** is the word that appears in the captcha image, which if not
	supplied to the function, will be a random string.

	.. note:: Usage of the ``$img_path``, ``$img_url`` and ``$font_path``
		parameters is DEPRECATED. Provide them in the ``$data`` array
		instead.
