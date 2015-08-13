###############
Language Helper
###############

The Language Helper file contains functions that assist in working with
language files.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('language');

Available Functions
===================

The following functions are available:


.. php:function:: lang($line[, $for = ''[, $attributes = array()]])

 	:param	string	$line: Language line key
 	:param	string	$for: HTML "for" attribute (ID of the element we're creating a label for)
 	:param	array	$attributes: Any additional HTML attributes
 	:returns:	HTML-formatted language line label
	:rtype:	string

	This function returns a line of text from a loaded language file with
	simplified syntax that may be more desirable for view files than
	``CI_Lang::line()``.

	Example::

		echo lang('language_key', 'form_item_id', array('class' => 'myClass'));
		// Outputs: <label for="form_item_id" class="myClass">Language line</label>