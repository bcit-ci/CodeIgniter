#################
Typography Helper
#################

The Typography Helper file contains functions that help your format text
in semantically relevant ways.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('typography');

The following functions are available:

auto_typography()
=================

.. php:function:: auto_typography($str, $reduce_linebreaks = FALSE)

	:param	string	$str: Input string
	:param	bool	$reduce_linebreaks: Whether to reduce multiple instances of double newlines to two
	:returns:	string

Formats text so that it is semantically and typographically correct
HTML.

This function is an alias for ``CI_Typography::auto_typography``.
For more info, please see the :doc:`Typography Library
<../libraries/typography>` documentation.

Usage example::

	$string = auto_typography($string);

.. note:: Typographic formatting can be processor intensive, particularly if
	you have a lot of content being formatted. If you choose to use this
	function you may want to consider `caching <../general/caching>` your
	pages.

nl2br_except_pre()
==================

.. php:function:: nl2br_except_pre($str)

	:param	string	$str: Input string
	:returns:	string

Converts newlines to <br /> tags unless they appear within <pre> tags.
This function is identical to the native PHP ``nl2br()`` function,
except that it ignores <pre> tags.

Usage example::

	$string = nl2br_except_pre($string);

entity_decode()
===============

.. php:function:: entity_decode($str, $charset = NULL)

	:param	string	$str: Input string
	:param	string	$charset: Character set
	:returns:	string

This function is an alias for ``CI_Security::entity_decode()``.
Fore more info, please see the :doc:`Security Library
<../libraries/security>` documentation.