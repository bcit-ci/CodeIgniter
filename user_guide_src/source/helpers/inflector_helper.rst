################
Inflector Helper
################

The Inflector Helper file contains functions that permits you to change
**English** words to plural, singular, camel case, etc.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('inflector');

Available Functions
===================

The following functions are available:


.. php:function:: singular($str)

	:param	string	$str: Input string
	:returns:	A singular word
	:rtype:	string

	Changes a plural word to singular. Example::

		echo singular('dogs'); // Prints 'dog'

.. php:function:: plural($str)

	:param	string	$str: Input string
	:returns:	A plural word
	:rtype:	string

	Changes a singular word to plural. Example::

		echo plural('dog'); // Prints 'dogs'

.. php:function:: camelize($str)

	:param	string	$str: Input string
	:returns:	Camelized string
	:rtype:	string

	Changes a string of words separated by spaces or underscores to camel
	case. Example::

		echo camelize('my_dog_spot'); // Prints 'myDogSpot'

.. php:function:: underscore($str)

	:param	string	$str: Input string
	:returns:	String containing underscores instead of spaces
	:rtype:	string

	Takes multiple words separated by spaces and underscores them.
	Example::

		echo underscore('my dog spot'); // Prints 'my_dog_spot'

.. php:function:: humanize($str[, $separator = '_'])

	:param	string	$str: Input string
	:param	string	$separator: Input separator
	:returns:	Humanized string
	:rtype:	string

	Takes multiple words separated by underscores and adds spaces between
	them. Each word is capitalized.

	Example::

		echo humanize('my_dog_spot'); // Prints 'My Dog Spot'

	To use dashes instead of underscores::

		echo humanize('my-dog-spot', '-'); // Prints 'My Dog Spot'

.. php:function:: is_countable($word)

	:param	string	$word: Input string
	:returns:	TRUE if the word is countable or FALSE if not
	:rtype:	bool

	Checks if the given word has a plural version. Example::

		is_countable('equipment'); // Returns FALSE