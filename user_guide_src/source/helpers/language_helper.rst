###############
Language Helper
###############

The Language Helper file contains functions that assist in working with
language files.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('language');

The following functions are available:

lang('language line', 'element id')
===================================

This function returns a line of text from a loaded language file with
simplified syntax that may be more desirable for view files than calling
`$this->lang->line()`. The optional second parameter will also output a
form label for you. Example

::

	echo lang('language_key', 'form_item_id');
	// becomes <label for="form_item_id">language_key</label>

lang_format('language line', 'placeholder value(s)', 'placeholder string')
===================================

This function returns a line of text from a loaded language file with
simplified syntax that may be more desirable for view files than calling
`$this->lang->line()`. The optional second and third parameters allows you
to replace placeholders in the line of text with actual values. Example

::

	// $lang['language_key'] = 'You have ? new messages.';
	echo lang_format('language_key', 10);
	// outputs: You have 10 new messages.

	// $lang['language_key'] = 'Are you sure you want to remove %s from %s ?';
	echo lang_format('language_key', array('Item 1', 'Category 1'), '%s');
	// outputs: Are you sure you want to remove Item 1 from Category 1 ?

