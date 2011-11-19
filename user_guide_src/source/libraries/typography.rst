################
Typography Class
################

The Typography Class provides functions that help you format text.

Initializing the Class
======================

Like most other classes in CodeIgniter, the Typography class is
initialized in your controller using the $this->load->library function::

	$this->load->library('typography');

Once loaded, the Typography library object will be available using:
$this->typography

auto_typography()
==================

Formats text so that it is semantically and typographically correct
HTML. Takes a string as input and returns it with the following
formatting:

-  Surrounds paragraphs within <p></p> (looks for double line breaks to
   identify paragraphs).
-  Single line breaks are converted to <br />, except those that appear
   within <pre> tags.
-  Block level elements, like <div> tags, are not wrapped within
   paragraphs, but their contained text is if it contains paragraphs.
-  Quotes are converted to correctly facing curly quote entities, except
   those that appear within tags.
-  Apostrophes are converted to curly apostrophe entities.
-  Double dashes (either like -- this or like--this) are converted to
   em—dashes.
-  Three consecutive periods either preceding or following a word are
   converted to ellipsis…
-  Double spaces following sentences are converted to non-breaking
   spaces to mimic double spacing.

Usage example::

	$string = $this->typography->auto_typography($string);

Parameters
----------

There is one optional parameters that determines whether the parser
should reduce more then two consecutive line breaks down to two. Use
boolean TRUE or FALSE.

By default the parser does not reduce line breaks. In other words, if no
parameters are submitted, it is the same as doing this::

	$string = $this->typography->auto_typography($string, FALSE);

.. note:: Typographic formatting can be processor intensive,
	particularly if you have a lot of content being formatted. If you choose
	to use this function you may want to consider :doc:`caching <../general/caching>`
	your pages.

format_characters()
====================

This function is similar to the auto_typography function above, except
that it only does character conversion:

-  Quotes are converted to correctly facing curly quote entities, except
   those that appear within tags.
-  Apostrophes are converted to curly apostrophe entities.
-  Double dashes (either like -- this or like--this) are converted to
   em—dashes.
-  Three consecutive periods either preceding or following a word are
   converted to ellipsis…
-  Double spaces following sentences are converted to non-breaking
   spaces to mimic double spacing.

Usage example::

	$string = $this->typography->format_characters($string);

nl2br_except_pre()
====================

Converts newlines to <br /> tags unless they appear within <pre> tags.
This function is identical to the native PHP nl2br() function, except
that it ignores <pre> tags.

Usage example::

	$string = $this->typography->nl2br_except_pre($string);

protect_braced_quotes
=======================

When using the Typography library in conjunction with the Template
Parser library it can often be desirable to protect single and double
quotes within curly braces. To enable this, set the
protect_braced_quotes class property to TRUE.

Usage example::

	$this->load->library('typography');
	$this->typography->protect_braced_quotes = TRUE;

