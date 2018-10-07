###############
Security Helper
###############

The Security Helper file contains security related functions.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('security');

Available Functions
===================

The following functions are available:


.. php:function:: xss_clean($str[, $is_image = FALSE])

	:param	string	$str: Input data
	:param	bool	$is_image: Whether we're dealing with an image
	:returns:	XSS-clean string
	:rtype:	string

	Provides Cross Site Script Hack filtering.

	This function is an alias for ``CI_Input::xss_clean()``. For more info,
	please see the :doc:`Input Library <../libraries/input>` documentation.

.. php:function:: sanitize_filename($filename)

	:param	string	$filename: Filename
	:returns:	Sanitized file name
	:rtype:	string

	Provides protection against directory traversal.

	This function is an alias for ``CI_Security::sanitize_filename()``.
	For more info, please see the :doc:`Security Library <../libraries/security>`
	documentation.

.. php:function:: strip_image_tags($str)

	:param	string	$str: Input string
	:returns:	The input string with no image tags
	:rtype:	string

	This is a security function that will strip image tags from a string.
	It leaves the image URL as plain text.

	Example::

		$string = strip_image_tags($string);

	This function is an alias for ``CI_Security::strip_image_tags()``. For
	more info, please see the :doc:`Security Library <../libraries/security>`
	documentation.


.. php:function:: encode_php_tags($str)

	:param	string	$str: Input string
	:returns:	Safely formatted string
	:rtype:	string

	This is a security function that converts PHP tags to entities.

	.. note:: :php:func:`xss_clean()` does this automatically, if you use it.

	Example::

		$string = encode_php_tags($string);
