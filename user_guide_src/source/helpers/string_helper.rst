#############
String Helper
#############

The String Helper file contains functions that assist in working with
strings.

.. important:: Please note that these functions are NOT intended, nor
	suitable to be used for any kind of security-related logic.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('string');

Available Functions
===================

The following functions are available:

.. php:function:: random_string([$type = 'alnum'[, $len = 8]])

	:param	string	$type: Randomization type
	:param	int	$len: Output string length
	:returns:	A random string
	:rtype:	string

	Generates a random string based on the type and length you specify.
	Useful for creating passwords or generating random hashes.

	The first parameter specifies the type of string, the second parameter
	specifies the length. The following choices are available:

	-  **alpha**: A string with lower and uppercase letters only.
	-  **alnum**: Alpha-numeric string with lower and uppercase characters.
	-  **basic**: A random number based on ``mt_rand()``.
	-  **numeric**: Numeric string.
	-  **nozero**: Numeric string with no zeros.
	-  **md5**: An encrypted random number based on ``md5()`` (fixed length of 32).
	-  **sha1**: An encrypted random number based on ``sha1()`` (fixed length of 40).

	Usage example::

		echo random_string('alnum', 16);

	.. note:: Usage of the *unique* and *encrypt* types is DEPRECATED. They
		are just aliases for *md5* and *sha1* respectively.

.. php:function:: increment_string($str[, $separator = '_'[, $first = 1]])

	:param	string	$str: Input string
	:param	string	$separator: Separator to append a duplicate number with
	:param	int	$first: Starting number
	:returns:	An incremented string
	:rtype:	string

	Increments a string by appending a number to it or increasing the
	number. Useful for creating "copies" or a file or duplicating database
	content which has unique titles or slugs.

	Usage example::

		echo increment_string('file', '_'); // "file_1"
		echo increment_string('file', '-', 2); // "file-2"
		echo increment_string('file_4'); // "file_5"


.. php:function:: alternator($args)

	:param	mixed	$args: A variable number of arguments
	:returns:	Alternated string(s)
	:rtype:	mixed

	Allows two or more items to be alternated between, when cycling through
	a loop. Example::

		for ($i = 0; $i < 10; $i++)
		{     
			echo alternator('string one', 'string two');
		}

	You can add as many parameters as you want, and with each iteration of
	your loop the next item will be returned.

	::

		for ($i = 0; $i < 10; $i++)
		{     
			echo alternator('one', 'two', 'three', 'four', 'five');
		}

	.. note:: To use multiple separate calls to this function simply call the
		function with no arguments to re-initialize.

.. php:function:: reduce_double_slashes($str)

	:param	string	$str: Input string
	:returns:	A string with normalized slashes
	:rtype:	string

	Converts double slashes in a string to a single slash, except those
	found in URL protocol prefixes (e.g. http&#58;//).

	Example::

		$string = "http://example.com//index.php";
		echo reduce_double_slashes($string); // results in "http://example.com/index.php"

.. php:function:: strip_slashes($data)

	:param	mixed	$data: Input string or an array of strings
	:returns:	String(s) with stripped slashes
	:rtype:	mixed

	Removes any slashes from an array of strings.

	Example::

		$str = array(
			'question'  => 'Is your name O\'reilly?',
			'answer' => 'No, my name is O\'connor.'
		);

		$str = strip_slashes($str);

	The above will return the following array::

		array(
			'question'  => "Is your name O'reilly?",
			'answer' => "No, my name is O'connor."
		);

	.. note:: For historical reasons, this function will also accept
		and handle string inputs. This however makes it just an
		alias for ``stripslashes()``.

.. php:function:: reduce_multiples($str[, $character = ''[, $trim = FALSE]])

	:param	string	$str: Text to search in
	:param	string	$character: Character to reduce
	:param	bool	$trim: Whether to also trim the specified character
	:returns:	Reduced string
	:rtype:	string

	Reduces multiple instances of a particular character occurring directly
	after each other. Example::

		$string = "Fred, Bill,, Joe, Jimmy";
		$string = reduce_multiples($string,","); //results in "Fred, Bill, Joe, Jimmy"

	If the third parameter is set to TRUE it will remove occurrences of the
	character at the beginning and the end of the string. Example::

		$string = ",Fred, Bill,, Joe, Jimmy,";
		$string = reduce_multiples($string, ", ", TRUE); //results in "Fred, Bill, Joe, Jimmy"

.. php:function:: quotes_to_entities($str)

	:param	string	$str: Input string
	:returns:	String with quotes converted to HTML entities
	:rtype:	string

	Converts single and double quotes in a string to the corresponding HTML
	entities. Example::

		$string = "Joe's \"dinner\"";
		$string = quotes_to_entities($string); //results in "Joe&#39;s &quot;dinner&quot;"


.. php:function:: strip_quotes($str)

	:param	string	$str: Input string
	:returns:	String with quotes stripped
	:rtype:	string

	Removes single and double quotes from a string. Example::

		$string = "Joe's \"dinner\"";
		$string = strip_quotes($string); //results in "Joes dinner"
