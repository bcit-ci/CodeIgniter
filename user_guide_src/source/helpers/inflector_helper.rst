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

.. php:function:: ordinal_format($number)

	:param	int	$number: non-negative natural number to be converted
    	:returns:	Ordinal numeral for given number or original value on failure
    	:rtype:	string

    	Returns the ordinal numeral (1st, 2nd, 3rd etc.) for a
    	non-negative natural number. If the input is not a natural number
    	greater than 0, the function will return the original value. Examples::

		echo ordinal_format(1); // Returns 1st
		echo ordinal_format(3); // Returns 3rd
		echo ordinal_format(21); // Returns 21st
		echo ordinal_format(102); // Returns 102nd
		echo ordinal_format(-5); // Invalid input, will return -5
