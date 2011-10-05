###############
Security Helper
###############

The Security Helper file contains security related functions.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('security');

The following functions are available:

xss_clean()
===========

Provides Cross Site Script Hack filtering. This function is an alias to
the one in the :doc:`Input class <../libraries/input>`. More info can
be found there.

sanitize_filename()
===================

Provides protection against directory traversal. This function is an
alias to the one in the :doc:`Security class <../libraries/security>`.
More info can be found there.

do_hash()
=========

Permits you to create SHA1 or MD5 one way hashes suitable for encrypting
passwords. Will create SHA1 by default. Examples

::

	$str = do_hash($str); // SHA1
	$str = do_hash($str, 'md5'); // MD5

.. note:: This function was formerly named dohash(), which has been
	deprecated in favor of `do_hash()`.

strip_image_tags()
==================

This is a security function that will strip image tags from a string. It
leaves the image URL as plain text.

::

	$string = strip_image_tags($string);

encode_php_tags()
=================

This is a security function that converts PHP tags to entities. Note: If
you use the XSS filtering function it does this automatically.

::

	$string = encode_php_tags($string);

