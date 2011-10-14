#############
Cookie Helper
#############

The Cookie Helper file contains functions that assist in working with
cookies.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('cookie');

The following functions are available:

set_cookie()
============

This helper function gives you view file friendly syntax to set browser
cookies. Refer to the :doc:`Input class <../libraries/input>` for a
description of use, as this function is an alias to
`$this->input->set_cookie()`.

.. php:method:: set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE)

	:param string 	$name: the name of the cookie
	:param string 	$value: the value of the cookie
	:param string 	$expire: the number of seconds until expiration
	:param string 	$domain: the cookie domain.  Usually:  .yourdomain.com
	:param string 	$path: the cookie path
	:param string 	$prefix: the cookie prefix
	:param boolean	$secure: secure cookie or not.
	:returns: void

get_cookie()
============

This helper function gives you view file friendly syntax to get browser
cookies. Refer to the :doc:`Input class <../libraries/input>` for a
description of use, as this function is an alias to `$this->input->cookie()`.

.. php:method:: get_cookie($index = '', $xss_clean = FALSE)

	:param string 	$index: the name of the cookie
	:param boolean	$xss_clean: If the resulting value should be xss_cleaned or not
	:returns: mixed

delete_cookie()
===============

Lets you delete a cookie. Unless you've set a custom path or other
values, only the name of the cookie is needed

.. php:method:: delete_cookie($name = '', $domain = '', $path = '/', $prefix = '')

	:param string 	$name: the name of the cookie
	:param string 	$domain: cookie domain (ususally .example.com)
	:param string 	$path: cookie path
	:param string 	$prefix: cookie prefix
	:returns: void

::

	delete_cookie("name");

This function is otherwise identical to ``set_cookie()``, except that it
does not have the value and expiration parameters. You can submit an
array of values in the first parameter or you can set discrete
parameters.

::

	delete_cookie($name, $domain, $path, $prefix)