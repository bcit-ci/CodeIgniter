###########
Text Helper
###########

The Text Helper file contains functions that assist in working with
text.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('text');

Available Functions
===================

The following functions are available:

.. function:: word_limiter($str[, $limit = 100[, $end_char = '&#8230;']])

	:param	string	$str: Input string
	:param	int	$limit: Limit
	:param	string	$end_char: End character (usually an ellipsis)
	:returns:	Word-limited string
	:rtype:	string

	Truncates a string to the number of *words* specified. Example::

		$string = "Here is a nice text string consisting of eleven words.";
		$string = word_limiter($string, 4);
		// Returns:  Here is a nice

	The third parameter is an optional suffix added to the string. By
	default it adds an ellipsis.


.. function:: character_limiter($str[, $n = 500[, $end_char = '&#8230;']])

	:param	string	$str: Input string
	:param	int	$n: Number of characters
	:param	string	$end_char: End character (usually an ellipsis)
	:returns:	Character-limited string
	:rtype:	string

	Truncates a string to the number of *characters* specified. It
	maintains the integrity of words so the character count may be slightly
	more or less than what you specify.

	Example::

		$string = "Here is a nice text string consisting of eleven words.";
		$string = character_limiter($string, 20);
		// Returns:  Here is a nice text string

	The third parameter is an optional suffix added to the string, if
	undeclared this helper uses an ellipsis.

	.. note:: If you need to truncate to an exact number of characters please
		see the :func:`ellipsize()` function below.

.. function:: ascii_to_entities($str)

	:param	string	$str: Input string
	:returns:	A string with ASCII values converted to entities
	:rtype:	string

	Converts ASCII values to character entities, including high ASCII and MS
	Word characters that can cause problems when used in a web page, so that
	they can be shown consistently regardless of browser settings or stored
	reliably in a database. There is some dependence on your server's
	supported character sets, so it may not be 100% reliable in all cases,
	but for the most part it should correctly identify characters outside
	the normal range (like accented characters).

	Example::

		$string = ascii_to_entities($string);

.. function::entities_to_ascii($str[, $all = TRUE])

	:param	string	$str: Input string
	:param	bool	$all: Whether to convert unsafe entities as well
	:returns:	A string with HTML entities converted to ASCII characters
	:rtype:	string

	This function does the opposite of :func:`ascii_to_entities()`.
	It turns character entities back into ASCII.

.. function:: convert_accented_characters($str)

	:param	string	$str: Input string
	:returns:	A string with accented characters converted
	:rtype:	string

	Transliterates high ASCII characters to low ASCII equivalents. Useful
	when non-English characters need to be used where only standard ASCII
	characters are safely used, for instance, in URLs.

	Example::

		$string = convert_accented_characters($string);

	.. note:: This function uses a companion config file
		`application/config/foreign_chars.php` to define the to and
		from array for transliteration.

.. function:: word_censor($str, $censored[, $replacement = ''])

	:param	string	$str: Input string
	:param	array	$censored: List of bad words to censor
	:param	string	$replacement: What to replace bad words with
	:returns:	Censored string
	:rtype:	string

	Enables you to censor words within a text string. The first parameter
	will contain the original string. The second will contain an array of
	words which you disallow. The third (optional) parameter can contain
	a replacement value for the words. If not specified they are replaced
	with pound signs: ####.

	Example::

		$disallowed = array('darn', 'shucks', 'golly', 'phooey');
		$string = word_censor($string, $disallowed, 'Beep!');

.. function:: highlight_code($str)

	:param	string	$str: Input string
	:returns:	String with code highlighted via HTML
	:rtype:	string

	Colorizes a string of code (PHP, HTML, etc.). Example::

		$string = highlight_code($string);

	The function uses PHP's ``highlight_string()`` function, so the
	colors used are the ones specified in your php.ini file.


.. function:: highlight_phrase($str, $phrase[, $tag_open = '<mark>'[, $tag_close = '</mark>']])

	:param	string	$str: Input string
	:param	string	$phrase: Phrase to highlight
	:param	string	$tag_open: Opening tag used for the highlight
	:param	string	$tag_close: Closing tag for the highlight
	:returns:	String with a phrase highlighted via HTML
	:rtype:	string

	Will highlight a phrase within a text string. The first parameter will
	contain the original string, the second will contain the phrase you wish
	to highlight. The third and fourth parameters will contain the
	opening/closing HTML tags you would like the phrase wrapped in.

	Example::

		$string = "Here is a nice text string about nothing in particular.";
		echo highlight_phrase($string, "nice text", '<span style="color:#990000;">', '</span>');

	The above code prints::

		Here is a <span style="color:#990000;">nice text</span> string about nothing in particular.

	.. note:: This function used to use the ``<strong>`` tag by default. Older browsers
		might not support the new HTML5 mark tag, so it is recommended that you
		insert the following CSS code into your stylesheet if you need to support
		such browsers::

			mark {
				background: #ff0;
				color: #000;
			};

.. function:: word_wrap($str[, $charlim = 76])

	:param	string	$str: Input string
	:param	int	$charlim: Character limit
	:returns:	Word-wrapped string
	:rtype:	string

	Wraps text at the specified *character* count while maintaining
	complete words.

	Example::

		$string = "Here is a simple string of text that will help us demonstrate this function.";
		echo word_wrap($string, 25);

		// Would produce:  Here is a simple string of text that will help us demonstrate this function

.. function:: ellipsize($str, $max_length[, $position = 1[, $ellipsis = '&hellip;']])

	:param	string	$str: Input string
	:param	int	$max_length: String length limit
	:param	mixed	$position: Position to split at (int or float)
	:param	string	$ellipsis: What to use as the ellipsis character
	:returns:	Ellipsized string
	:rtype:	string

	This function will strip tags from a string, split it at a defined
	maximum length, and insert an ellipsis.

	The first parameter is the string to ellipsize, the second is the number
	of characters in the final string. The third parameter is where in the
	string the ellipsis should appear from 0 - 1, left to right. For
	example. a value of 1 will place the ellipsis at the right of the
	string, .5 in the middle, and 0 at the left.

	An optional forth parameter is the kind of ellipsis. By default,
	&hellip; will be inserted.

	Example::

		$str = 'this_string_is_entirely_too_long_and_might_break_my_design.jpg';
		echo ellipsize($str, 32, .5);

	Produces::

		this_string_is_e&hellip;ak_my_design.jpg