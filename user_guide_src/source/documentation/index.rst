#################################
Writing CodeIgniter Documentation
#################################

CodeIgniter uses Sphinx to generate its documentation in a variety of formats,
using reStructuredText to handle the formatting.  If you are familiar with
Markdown or Textile, you will quickly grasp reStructuredText.  The focus is
on readability and user friendliness.
While they can be quite technical, we always write for humans!

A local table of contents should always be included, like the one below.
It is created automatically by inserting the following:

::

	.. contents::
		:local:

	.. raw:: html

  	<div class="custom-index container"></div>

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

The <div> that is inserted as raw HTML is a hook for the documentation's
JavaScript to dynamically add links to any function and method definitions
contained in the current page.

**************
Tools Required
**************

To see the rendered HTML, ePub, PDF, etc., you will need to install Sphinx
along with the PHP domain extension for Sphinx.  The underlying requirement
is to have Python installed.  Lastly, you will install the CI Lexer for
Pygments, so that code blocks can be properly highlighted.

.. code-block:: bash

	easy_install "sphinx==1.2.3"
	easy_install sphinxcontrib-phpdomain

Then follow the directions in the README file in the :samp:`cilexer` folder
inside the documentation repository to install the CI Lexer.



*****************************************
Page and Section Headings and Subheadings
*****************************************

Headings not only provide order and sections within a page, but they also
are used to automatically build both the page and document table of contents.
Headings are formed by using certain characters as underlines for a bit of
text.  Major headings, like page titles and section headings also use
overlines.  Other headings just use underlines, with the following hierarchy::

	# with overline for page titles
	* with overline for major sections
	= for subsections
	- for subsubsections
	^ for subsubsubsections
	" for subsubsubsubsections (!)

The :download:`TextMate ELDocs Bundle <./ELDocs.tmbundle.zip>` can help you
create these with the following tab triggers::

	title->

		##########
		Page Title
		##########

	sec->

		*************
		Major Section
		*************

	sub->

		Subsection
		==========

	sss->

		SubSubSection
		-------------

	ssss->

		SubSubSubSection
		^^^^^^^^^^^^^^^^

	sssss->

		SubSubSubSubSection (!)
		"""""""""""""""""""""""




********************
Method Documentation
********************

When documenting class methods for third party developers, Sphinx provides
directives to assist and keep things simple.  
For example, consider the following ReST:

.. code-block:: rst

	.. class:: Some_class

		.. method:: some_method ( $foo [, $bar [, $bat]])

			This function will perform some action. The ``$bar`` array must contain
			a something and something else, and along with ``$bat`` is an optional
			parameter.

			:param int $foo: the foo id to do something in
			:param mixed $bar: A data array that must contain a something and something else
			:param bool $bat: whether or not to do something
			:returns: FALSE on failure, TRUE if successful
			:rtype: bool

			::

				$this->load->library('some_class');

				$bar = array(
					'something'		=> 'Here is this parameter!',
					'something_else'	=> 42
				);

				$bat = $this->some_class->should_do_something();

				if ($this->some_class->some_method(4, $bar, $bat) === FALSE)
				{
					show_error('An Error Occurred Doing Some Method');
				}

			.. note:: Here is something that you should be aware of when using some_method().
					For real.

			See also :meth:`Some_class::should_do_something`


		.. method:: should_do_something()

			:returns: Whether or not something should be done
			:rtype: bool


It creates the following display:

.. class:: Some_class


	.. method:: some_method ( $foo [, $bar [, $bat]])

		This function will perform some action. The ``$bar`` array must contain
		a something and something else, and along with ``$bat`` is an optional
		parameter.

		:param int $foo: the foo id to do something in
		:param mixed $bar: A data array that must contain a something and something else
		:param bool $bat: whether or not to do something
		:returns: FALSE on failure, TRUE if successful
		:rtype: bool

		::

			$this->load->library('some_class');

			$bar = array(
				'something'		=> 'Here is this parameter!',
				'something_else'	=> 42
			);

			$bat = $this->some_class->should_do_something();

			if ($this->some_class->some_method(4, $bar, $bat) === FALSE)
			{
				show_error('An Error Occurred Doing Some Method');
			}

		.. note:: Here is something that you should be aware of when using some_method().
				For real.

		See also :meth:`Some_class::should_do_something`


	.. method:: should_do_something()

		:returns: Whether or not something should be done
		:rtype: bool