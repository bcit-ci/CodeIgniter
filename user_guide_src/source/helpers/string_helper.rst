#############
String Helper
#############

The String Helper file contains functions that assist in working with
strings.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('string');

The following functions are available:

random_string()
===============

Generates a random string based on the type and length you specify.
Useful for creating passwords or generating random hashes.

The first parameter specifies the type of string, the second parameter
specifies the length. The following choices are available:

alpha, alunum, numeric, nozero, unique, md5, encrypt and sha1

-  **alpha**: A string with lower and uppercase letters only.
-  **alnum**: Alpha-numeric string with lower and uppercase characters.
-  **numeric**: Numeric string.
-  **nozero**: Numeric string with no zeros.
-  **unique**: Encrypted with MD5 and uniqid(). Note: The length
   parameter is not available for this type. Returns a fixed length 32
   character string.
-  **sha1**: An encrypted random number based on do_hash() from the
   :doc:`security helper <security_helper>`.

Usage example

::

	echo random_string('alnum', 16);

increment_string()
==================

Increments a string by appending a number to it or increasing the
number. Useful for creating "copies" or a file or duplicating database
content which has unique titles or slugs.

Usage example

::

	echo increment_string('file', '_'); // "file_1"
	echo increment_string('file', '-', 2); // "file-2"
	echo increment_string('file_4'); // "file_5"

alternator()
============

Allows two or more items to be alternated between, when cycling through
a loop. Example

::

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

repeater()
==========

Generates repeating copies of the data you submit. Example

::

	$string = "\n"; echo repeater($string, 30);

The above would generate 30 newlines.

reduce_double_slashes()
=======================

Converts double slashes in a string to a single slash, except those
found in http://. Example

::

	$string = "http://example.com//index.php";
	echo reduce_double_slashes($string); // results in "http://example.com/index.php"

trim_slashes()
==============

Removes any leading/trailing slashes from a string. Example

::

	$string = "/this/that/theother/";
	echo trim_slashes($string); // results in this/that/theother


reduce_multiples()
==================

Reduces multiple instances of a particular character occuring directly
after each other. Example::

	$string = "Fred, Bill,, Joe, Jimmy";
	$string = reduce_multiples($string,","); //results in "Fred, Bill, Joe, Jimmy"

The function accepts the following parameters:

::

	reduce_multiples(string: text to search in, string: character to reduce, boolean: whether to remove the character from the front and end of the string)

The first parameter contains the string in which you want to reduce the
multiplies. The second parameter contains the character you want to have
reduced. The third parameter is FALSE by default; if set to TRUE it will
remove occurences of the character at the beginning and the end of the
string. Example:

::

	$string = ",Fred, Bill,, Joe, Jimmy,";
	$string = reduce_multiples($string, ", ", TRUE); //results in "Fred, Bill, Joe, Jimmy"


quotes_to_entities()
====================

Converts single and double quotes in a string to the corresponding HTML
entities. Example

::

	$string = "Joe's \"dinner\"";
	$string = quotes_to_entities($string); //results in "Joe&#39;s &quot;dinner&quot;"

strip_quotes()
==============

Removes single and double quotes from a string. Example::

	$string = "Joe's \"dinner\"";
	$string = strip_quotes($string); //results in "Joes dinner"

