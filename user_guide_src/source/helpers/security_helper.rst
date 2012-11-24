###############
Security Helper
###############

The Security Helper file contains security related functions.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('security');

The following functions are available:

xss_clean()
===========

.. php:function:: xss_clean($str, $is_image = FALSE)

	:param	string	$str: Input data
	:param	bool	$is_image: Whether we're dealing with an image
	:returns:	string

Provides Cross Site Script Hack filtering.

This function is an alias for ``CI_Input::xss_clean()``. For more info,
please see the :doc:`Input Library <../libraries/input>` documentation.

sanitize_filename()
===================

.. php:function:: sanitize_filename($filename)

	:param	string	$filename: Filename
	:returns:	string

Provides protection against directory traversal.

This function is an alias for ``CI_Security::sanitize_filename()``.
For more info, please see the :doc:`Security Library <../libraries/security>`
documentation.

do_hash()
=========

.. php:function:: do_hash($str, $type = 'sha1')

	:param	string	$str: Input
	:param	string	$type: Algorithm
	:returns:	string

Permits you to create one way hashes suitable for encrypting
passwords. Will use SHA1 by default.

See `hash_algos() <http://php.net/function.hash_algos>`_
for a full list of supported algorithms.

Examples::

	$str = do_hash($str); // SHA1
	$str = do_hash($str, 'md5'); // MD5

.. note:: This function was formerly named ``dohash()``, which has been
	removed in favor of ``do_hash()``.

.. note:: This function is DEPRECATED. Use the native ``hash()`` instead.

strip_image_tags()
==================

.. php:function:: strip_image_tags($str)

	:param	string	$str: Input
	:returns:	string

This is a security function that will strip image tags from a string.
It leaves the image URL as plain text.

Example::

	$string = strip_image_tags($string);

This function is an alias for ``CI_Security::strip_image_tags()``. For
more info, please see the :doc:`Security Library <../libraries/security>`
documentation.

encode_php_tags()
=================

.. php:function:: encode_php_tags($str)

	:param	string	$str: Input
	:returns:	string

This is a security function that converts PHP tags to entities.

.. note: :php:func:`xss_clean()` does this automatically, if you use it.

Example::

	$string = encode_php_tags($string);