##############
Security Class
##############

The Security Class contains methods that help you create a secure
application, processing input data for security.

XSS Filtering
=============

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

To filter data through the XSS filter use this function:

$this->security->xss_clean()
============================

Here is an usage example::

	$data = $this->security->xss_clean($data);

If you want the filter to run automatically every time it encounters
POST or COOKIE data you can enable it by opening your
application/config/config.php file and setting this::

	$config['global_xss_filtering'] = TRUE;

Note: If you use the form validation class, it gives you the option of
XSS filtering as well.

An optional second parameter, is_image, allows this function to be used
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

$this->security->sanitize_filename()
====================================

When accepting filenames from user input, it is best to sanitize them to
prevent directory traversal and other security related issues. To do so,
use the sanitize_filename() method of the Security class. Here is an
example::

	$filename = $this->security->sanitize_filename($this->input->post('filename'));

If it is acceptable for the user input to include relative paths, e.g.
file/in/some/approved/folder.txt, you can set the second optional
parameter, $relative_path to TRUE.

::

	$filename = $this->security->sanitize_filename($this->input->post('filename'), TRUE);

Cross-site request forgery (CSRF)
=================================

You can enable CSRF protection by opening your
application/config/config.php file and setting this::

	$config['csrf_protection'] = TRUE;

If you use the :doc:`form helper <../helpers/form_helper>`, then
``form_open()`` will automatically insert a hidden csrf field in
your forms. If not, then you can use ``csrf_get_token_name()``
and ``csrf_get_hash()``

::

	$csrf = array(
		'name' => $this->security->csrf_get_token_name(),
		'hash' => $this->security->csrf_get_hash()
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

$this->security->get_csrf_token_name()
======================================

Returns the CSRF token name, which is set by
``$config['csrf_token_name']``.

$this->security->get_csrf_hash()
================================

Returns the CSRF hash value. Useful in combination with
``get_csrf_token_name()`` for manually building forms or 
sending valid AJAX POST requests.