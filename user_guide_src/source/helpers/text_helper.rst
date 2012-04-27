###########
Text Helper
###########

The Text Helper file contains functions that assist in working with
text.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('text');

The following functions are available:

word_limiter()
==============

Truncates a string to the number of **words** specified. Example::

	$string = "Here is a nice text string consisting of eleven words.";
	$string = word_limiter($string, 4);
	// Returns:  Here is a nice…

The third parameter is an optional suffix added to the string. By
default it adds an ellipsis.

character_limiter()
===================

Truncates a string to the number of **characters** specified. It
maintains the integrity of words so the character count may be slightly
more or less then what you specify. Example

::

	$string = "Here is a nice text string consisting of eleven words.";
	$string = character_limiter($string, 20);
	// Returns:  Here is a nice text string…

The third parameter is an optional suffix added to the string, if
undeclared this helper uses an ellipsis.

**Note:** If you need to truncate to an exact number of characters please see
the :ref:`ellipsize` function below.

ascii_to_entities()
===================

Converts ASCII values to character entities, including high ASCII and MS
Word characters that can cause problems when used in a web page, so that
they can be shown consistently regardless of browser settings or stored
reliably in a database. There is some dependence on your server's
supported character sets, so it may not be 100% reliable in all cases,
but for the most part it should correctly identify characters outside
the normal range (like accented characters). Example

::

	$string = ascii_to_entities($string);

entities_to_ascii()
===================

This function does the opposite of the previous one; it turns character
entities back into ASCII.

convert_accented_characters()
=============================

Transliterates high ASCII characters to low ASCII equivalents, useful
when non-English characters need to be used where only standard ASCII
characters are safely used, for instance, in URLs.

::

	$string = convert_accented_characters($string);

This function uses a companion config file
`application/config/foreign_chars.php` to define the to and from array
for transliteration.

word_censor()
=============

Enables you to censor words within a text string. The first parameter
will contain the original string. The second will contain an array of
words which you disallow. The third (optional) parameter can contain a
replacement value for the words. If not specified they are replaced with
pound signs: ####. Example

::

	$disallowed = array('darn', 'shucks', 'golly', 'phooey');
	$string = word_censor($string, $disallowed, 'Beep!');

highlight_code()
================

Colorizes a string of code (PHP, HTML, etc.). Example::

	$string = highlight_code($string);

The function uses PHP's highlight_string() function, so the colors used
are the ones specified in your php.ini file.

highlight_phrase()
==================

Will highlight a phrase within a text string. The first parameter will
contain the original string, the second will contain the phrase you wish
to highlight. The third and fourth parameters will contain the
opening/closing HTML tags you would like the phrase wrapped in. Example

::

	$string = "Here is a nice text string about nothing in particular.";
	$string = highlight_phrase($string, "nice text", '<span style="color:#990000">', '</span>');

The above text returns:

Here is a nice text string about nothing in particular.

word_wrap()
===========

Wraps text at the specified **character** count while maintaining
complete words. Example

::

	$string = "Here is a simple string of text that will help us demonstrate this function.";
	echo word_wrap($string, 25);

	// Would produce:  Here is a simple string of text that will help us demonstrate this function

.. _ellipsize:

ellipsize()
===========

This function will strip tags from a string, split it at a defined
maximum length, and insert an ellipsis.

The first parameter is the string to ellipsize, the second is the number
of characters in the final string. The third parameter is where in the
string the ellipsis should appear from 0 - 1, left to right. For
example. a value of 1 will place the ellipsis at the right of the
string, .5 in the middle, and 0 at the left.

An optional forth parameter is the kind of ellipsis. By default,
&hellip; will be inserted.

::

	$str = 'this_string_is_entirely_too_long_and_might_break_my_design.jpg';
	echo ellipsize($str, 32, .5);

Produces:

::

	this_string_is_e&hellip;ak_my_design.jpg

