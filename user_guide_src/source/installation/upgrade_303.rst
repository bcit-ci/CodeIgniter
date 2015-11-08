#############################
Upgrading from 3.0.2 to 3.0.3
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Make sure your 'base_url' config value is not empty
===========================================================

When ``$config['base_url']`` is not set, CodeIgniter tries to automatically
detect what your website's base URL is. This is done purely for convenience
when you are starting development of a new application.

Auto-detection is never reliable and also has security implications, which
is why you should **always** have it manually configured!

One of the changes in CodeIgniter 3.0.3 is how this auto-detection works,
and more specifically it now falls back to the server's IP address instead
of the hostname requested by the client. Therefore, if you've ever relied
on auto-detection, it will change how your website works now.

In case you need to allow e.g. multiple domains, or both http:// and
https:// prefixes to be dynamically used depending on the request,
remember that *application/config/config.php* is still a PHP script, in
which you can create this logic with a few lines of code. For example::

	$allowed_domains = array('domain1.tld', 'domain2.tld');
	$default_domain  = 'domain1.tld';

	if (in_array($_SERVER['HTTP_HOST'], $allowed_domains, TRUE))
	{
		$domain = $_SERVER['HTTP_HOST'];
	}
	else
	{
		$domain = $default_domain;
	}

	if ( ! empty($_SERVER['HTTPS']))
	{
		$config['base_url'] = 'https://'.$domain;
	}
	else
	{
		$config['base_url'] = 'http://'.$domain;
	}
