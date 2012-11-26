##############
Language Class
##############

The Language Class provides functions to retrieve language files and
lines of text for purposes of internationalization.

In your CodeIgniter system folder you'll find one called language
containing sets of language files. You can create your own language
files as needed in order to display error and other messages in other
languages.

Language files are typically stored in your **system/language/** directory.
Alternately you can create a directory called language inside your
application folder and store them there. CodeIgniter will always load the
one in **system/language/** first and will then look for an override in
your **application/language/** directory.

.. note:: Each language should be stored in its own folder. For example,
	the English files are located at: system/language/english

Creating Language Files
=======================

Language files must be named with **_lang.php** as the file extension. For
example, let's say you want to create a file containing error messages.
You might name it: error_lang.php

Within the file you will assign each line of text to an array called
``$lang`` with this prototype::

	$lang['language_key'] = 'The actual message to be shown';

.. note:: It's a good practice to use a common prefix for all messages
	in a given file to avoid collisions with similarly named items in other
	files. For example, if you are creating error messages you might prefix
	them with error\_

::

	$lang['error_email_missing'] = 'You must submit an email address';
	$lang['error_url_missing'] = 'You must submit a URL';
	$lang['error_username_missing'] = 'You must submit a username';

Loading A Language File
=======================

In order to fetch a line from a particular file you must load the file
first. Loading a language file is done with the following code::

	$this->lang->load('filename', 'language');

Where filename is the name of the file you wish to load (without the
file extension), and language is the language set containing it (ie,
english). If the second parameter is missing, the default language set
in your **application/config/config.php** file will be used.

.. note:: The *language* parameter can only consist of letters.

Fetching a Line of Text
=======================

Once your desired language file is loaded you can access any line of
text using this function::

	$this->lang->line('language_key');

Where *language_key* is the array key corresponding to the line you wish
to show.

You can optionally pass FALSE as the second argument of that method to
disable error logging, in case you're not sure if the line exists::

	$this->lang->line('misc_key', FALSE);

.. note:: This method simply returns the line. It does not echo it.

Using language lines as form labels
-----------------------------------

This feature has been deprecated from the language library and moved to
the :php:func:`lang()` function of the :doc:`Language Helper
<../helpers/language_helper>`.

Auto-loading Languages
======================

If you find that you need a particular language globally throughout your
application, you can tell CodeIgniter to :doc:`auto-load
<../general/autoloader>` it during system initialization. This is done
by opening the **application/config/autoload.php** file and adding the
language(s) to the autoload array.