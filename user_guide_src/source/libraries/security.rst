##############
Security Class
##############

The Security Class contains methods that help you create a secure
application, processing input data for security.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

*************
XSS Filtering
*************

CodeIgniter comes with a Cross Site Scripting Hack prevention filter
which can either run automatically to filter all POST and COOKIE data
that is encountered, or you can run it on a per item basis. By default
it does **not** run globally since it requires a bit of processing
overhead, and since you may not need it in all cases.

The XSS filter looks for commonly used techniques to trigger Javascript
or other types of code that attempt to hijack cookies or do other
malicious things. If anything disallowed is encountered it is rendered
safe by converting the data to character entities.

Note: This function should only be used to deal with data upon
submission. It's not something that should be used for general runtime
processing since it requires a fair amount of processing overhead.

To filter data through the XSS filter use the ``xss_clean()`` method::

	$data = $this->security->xss_clean($data);

If you want the filter to run automatically every time it encounters
POST or COOKIE data you can enable it by opening your
application/config/config.php file and setting this::

	$config['global_xss_filtering'] = TRUE;

.. note:: If you use the form validation class, it gives you the option of
	XSS filtering as well.

An optional second parameter, *is_image*, allows this function to be used
to test images for potential XSS attacks, useful for file upload
security. When this second parameter is set to TRUE, instead of
returning an altered string, the function returns TRUE if the image is
safe, and FALSE if it contained potentially malicious information that a
browser may attempt to execute.

::

	if ($this->security->xss_clean($file, TRUE) === FALSE)
	{
		// file failed the XSS test
	}

*********************************
Cross-site request forgery (CSRF)
*********************************

You can enable CSRF protection by altering your **application/config/config.php**
file in the following way::

	$config['csrf_protection'] = TRUE;

If you use the :doc:`form helper <../helpers/form_helper>`, then
:func:`form_open()` will automatically insert a hidden csrf field in
your forms. If not, then you can use ``get_csrf_token_name()``
and ``get_csrf_hash()``
::

	$csrf = array(
		'name' => $this->security->get_csrf_token_name(),
		'hash' => $this->security->get_csrf_hash()
	);

	...

	<input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

Tokens may be either regenerated on every submission (default) or
kept the same throughout the life of the CSRF cookie. The default
regeneration of tokens provides stricter security, but may result
in usability concerns as other tokens become invalid (back/forward
navigation, multiple tabs/windows, asynchronous actions, etc). You
may alter this behavior by editing the following config parameter

::

	$config['csrf_regeneration'] = TRUE;

Select URIs can be whitelisted from csrf protection (for example API
endpoints expecting externally POSTed content). You can add these URIs
by editing the 'csrf_exclude_uris' config parameter::

	$config['csrf_exclude_uris'] = array('api/person/add');

Regular expressions are also supported (case-insensitive)::

	$config['csrf_exclude_uris'] = array(
		'api/record/[0-9]+',
		'api/title/[a-z]+'
	);

***************
Class Reference
***************

.. class:: CI_Security

	.. method:: xss_clean($str[, $is_image = FALSE])

		:param	mixed	$str: Input string or an array of strings
		:returns:	XSS-clean data
		:rtype:	mixed

		Tries to remove XSS exploits from the input data and returns the cleaned string.
		If the optional second parameter is set to true, it will return boolean TRUE if the image is safe to use and FALSE if malicious data was detected in it.

	.. method:: sanitize_filename($str[, $relative_path = FALSE])

		:param	string	$str: File name/path
		:param	bool	$relative_path: Whether to preserve any directories in the file path
		:returns:	Sanitized file name/path
		:rtype:	string

		Tries to sanitize filenames in order to prevent directory traversal attempts
		and other security threats, which is particularly useful for files that were supplied via user input.
		::

			$filename = $this->security->sanitize_filename($this->input->post('filename'));

		If it is acceptable for the user input to include relative paths, e.g.
		*file/in/some/approved/folder.txt*, you can set the second optional parameter, ``$relative_path`` to TRUE.
		::

			$filename = $this->security->sanitize_filename($this->input->post('filename'), TRUE);

	.. method:: get_csrf_token_name()

		:returns:	CSRF token name
		:rtype:	string

		Returns the CSRF token name (the ``$config['csrf_token_name']`` value).

	.. method:: get_csrf_hash()

		:returns:	CSRF hash
		:rtype:	string

		Returns the CSRF hash value. Useful in combination with ``get_csrf_token_name()``
		for manually building forms or sending valid AJAX POST requests.

	.. method:: entity_decode($str[, $charset = NULL])

		:param	string	$str: Input string
		:param	string	$charset: Character set of the input string
		:returns:	Entity-decoded string
		:rtype:	string

		This method acts a lot like PHP's own native ``html_entity_decode()`` function in ENT_COMPAT mode, only
		it tries to detect HTML entities that don't end in a semicolon because some browsers allow that.

		If the ``$charset`` parameter is left empty, then your configured ``$config['charset']`` value will be used.

	.. method:: get_random_bytes($length)

		:param	int	$length: Output length
		:returns:	A binary stream of random bytes or FALSE on failure
		:rtype:	string

		A convenience method for getting proper random bytes via ``mcrypt_create_iv()``,
		``/dev/urandom`` or ``openssl_random_pseudo_bytes()`` (in that order), if one
		of them is available.

		Used for generating CSRF and XSS tokens.

		.. note:: The output is NOT guaranteed to be cryptographically secure,
			just the best attempt at that.