#################
Typography Helper
#################

The Typography Helper file contains functions that help your format text
in semantically relevant ways.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('typography');

The following functions are available:

auto_typography()
=================

Formats text so that it is semantically and typographically correct
HTML. Please see the :doc:`Typography Class <../libraries/typography>`
for more info.

Usage example::

	$string = auto_typography($string);

.. note:: Typographic formatting can be processor intensive, particularly if
	you have a lot of content being formatted. If you choose to use this
	function you may want to consider `caching </general/caching>` your pages.

nl2br_except_pre()
==================

Converts newlines to <br /> tags unless they appear within <pre> tags.
This function is identical to the native PHP nl2br() function, except
that it ignores <pre> tags.

Usage example

::

	$string = nl2br_except_pre($string);

