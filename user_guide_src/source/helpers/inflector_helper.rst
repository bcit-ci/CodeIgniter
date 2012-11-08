################
Inflector Helper
################

The Inflector Helper file contains functions that permits you to change
words to plural, singular, camel case, etc.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('inflector');

The following functions are available:

singular()
==========

.. php:function:: singular($str)

	:param	string	$str: Input string
	:returns:	string

Changes a plural word to singular. Example::

	echo singular('dogs'); // Prints 'dog'

plural()
========

.. php:function:: plural($str)

	:param	string	$str: Input string
	:returns:	string

Changes a singular word to plural. Example::

	echo plural('dog'); // Prints 'dogs'

camelize()
==========

.. php:function:: camelize($str)

	:param	string	$str: Input string
	:returns:	string

Changes a string of words separated by spaces or underscores to camel
case. Example::

	echo camelize('my_dog_spot'); // Prints 'myDogSpot'

underscore()
============

.. php:function:: camelize($str)

	:param	string	$str: Input string
	:returns:	string

Takes multiple words separated by spaces and underscores them.
Example::

	echo underscore('my dog spot'); // Prints 'my_dog_spot'

humanize()
==========

.. php:function:: camelize($str)

	:param	string	$str: Input string
	:param	string	$separator: Input separator
	:returns:	string

Takes multiple words separated by underscores and adds spaces between
them. Each word is capitalized.

Example::

	echo humanize('my_dog_spot'); // Prints 'My Dog Spot'

To use dashes instead of underscores::

	echo humanize('my-dog-spot', '-'); // Prints 'My Dog Spot'

is_countable()
==============

.. php:function:: is_countable($word)

	:param	string	$word: Input string
	:returns:	bool

Checks if the given word has a plural version. Example::

	is_countable('equipment'); // Returns FALSE