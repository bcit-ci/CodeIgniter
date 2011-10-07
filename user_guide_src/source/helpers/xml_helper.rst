##########
XML Helper
##########

The XML Helper file contains functions that assist in working with XML
data.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('xml');

The following functions are available:

xml_convert()
=====================

Takes a string as input and converts the following reserved XML
characters to entities:

- Ampersands: &
- Less then and greater than characters: < >
- Single and double quotes: ' "
- Dashes: -

This function ignores ampersands if they are part of existing character
entities. Example

::

	$string = xml_convert($string);

