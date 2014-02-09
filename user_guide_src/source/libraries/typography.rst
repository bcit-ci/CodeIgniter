################
Typography Class
################

The Typography Class provides methods that help you format text.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

**************************
Using the Typography Class
**************************

Initializing the Class
======================

Like most other classes in CodeIgniter, the Typography class is
initialized in your controller using the ``$this->load->library()`` method::

	$this->load->library('typography');

Once loaded, the Typography library object will be available using::

	$this->typography

***************
Class Reference
***************

.. class:: CI_Typography

	.. attribute:: $protect_braced_quotes = FALSE

		When using the Typography library in conjunction with the :doc:`Template Parser library <parser>`
		it can often be desirable to protect single and double quotes within curly braces.
		To enable this, set the ``protect_braced_quotes`` class property to TRUE.

		Usage example::

			$this->load->library('typography');
			$this->typography->protect_braced_quotes = TRUE;

	.. method auto_typography($str[, $reduce_linebreaks = FALSE])

		:param	string	$str: Input string
		:param	bool	$reduce_linebreaks: Whether to reduce consequitive linebreaks
		:returns:	HTML typography-safe string
		:rtype:	string

		Formats text so that it is semantically and typographically correct HTML.
		Takes a string as input and returns it with the following formatting:

		 -  Surrounds paragraphs within <p></p> (looks for double line breaks to identify paragraphs).
		 -  Single line breaks are converted to <br />, except those that appear within <pre> tags.
		 -  Block level elements, like <div> tags, are not wrapped within paragraphs, but their contained text is if it contains paragraphs.
		 -  Quotes are converted to correctly facing curly quote entities, except those that appear within tags.
		 -  Apostrophes are converted to curly apostrophe entities.
		 -  Double dashes (either like -- this or like--this) are converted to em—dashes.
		 -  Three consecutive periods either preceding or following a word are converted to ellipsis (…).
		 -  Double spaces following sentences are converted to non-breaking spaces to mimic double spacing.

		Usage example::

			$string = $this->typography->auto_typography($string);

		There is one optional parameter that determines whether the parser should reduce more than two consecutive line breaks down to two.
		Pass boolean TRUE to enable reducing line breaks::

			$string = $this->typography->auto_typography($string, TRUE);

		.. note:: Typographic formatting can be processor intensive, particularly if you have a lot of content being formatted.
			If you choose to use this method you may want to consider :doc:`caching <../general/caching>` your pages.

	.. method:: format_characters($str)

		:param	string	$str: Input string
		:returns:	Formatted string
		:rtype:	string

		This method is similar to ``auto_typography()`` above, except that it only does character conversion:

		 -  Quotes are converted to correctly facing curly quote entities, except those that appear within tags.
		 -  Apostrophes are converted to curly apostrophe entities.
		 -  Double dashes (either like -- this or like--this) are converted to em—dashes.
		 -  Three consecutive periods either preceding or following a word are converted to ellipsis (…).
		 -  Double spaces following sentences are converted to non-breaking spaces to mimic double spacing.

		Usage example::

			$string = $this->typography->format_characters($string);

	.. method:: nl2br_except_pre($str)

		:param	string	$str: Input string
		:returns:	Formatted string
		:rtype:	string

		Converts newlines to <br /> tags unless they appear within <pre> tags.
		This method is identical to the native PHP :php:func:`nl2br()` function, except that it ignores <pre> tags.

		Usage example::

			$string = $this->typography->nl2br_except_pre($string);