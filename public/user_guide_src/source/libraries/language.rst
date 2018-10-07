##############
Language Class
##############

The Language Class provides functions to retrieve language files and
lines of text for purposes of internationalization.

In your CodeIgniter **system** folder, you will find a **language** sub-directory
containing a set of language files for the **english** idiom.
The files in this directory (**system/language/english/**) define the regular messages,
error messages, and other generally output terms or expressions, for the different parts
of the CodeIgniter framework.

You can create or incorporate your own language files, as needed, in order to provide
application-specific error and other messages, or to provide translations of the core
messages into other languages. These translations or additional messages would go inside
your **application/language/** directory, with separate sub-directories for each idiom
(for instance, 'french' or 'german').

The CodeIgniter framework comes with a set of language files for the "english" idiom.
Additional approved translations for different idioms may be found in the
`CodeIgniter 3 Translations repositories <https://github.com/bcit-ci/codeigniter3-translations>`_.
Each repository deals with a single idiom.

When CodeIgniter loads language files, it will load the one in **system/language/**
first and will then look for an override in your **application/language/** directory.

.. note:: Each language should be stored in its own folder. For example,
	the English files are located at: system/language/english

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

***************************
Handling Multiple Languages
***************************

If you want to support multiple languages in your application, you would provide folders inside
your **application/language/** directory for each of them, and you would specify the default
language in your **application/config/config.php**.

The **application/language/english/** directory would contain any additional language files
needed by your application, for instance for error messages.

Each of the other idiom-specific directories would contain the core language files that you
obtained from the translations repositories, or that you translated yourself, as well as
any additional ones needed by your application.

You would store the language you are currently using, for instance in a session variable.

Sample Language Files
=====================

::

	system/
		language/
			english/
				...
				email_lang.php
				form_validation_lang.php
				...

	application/
		language/
			english/
				error_messages_lang.php
			french/
				...
				email_lang.php
				error_messages_lang.php
				form_validation_lang.php
				...

Example of switching languages
==============================

::

	$idiom = $this->session->get_userdata('language');
	$this->lang->load('error_messages', $idiom);
	$oops = $this->lang->line('message_key');

********************
Internationalization
********************

The Language class in CodeIgniter is meant to provide an easy and lightweight
way to support multiplelanguages in your application. It is not meant to be a
full implementation of what is commonly called `internationalization and localization
<https://en.wikipedia.org/wiki/Internationalization_and_localization>`_.

We use the term "idiom" to refer to a language using its common name,
rather than using any of the international standards, such as "en", "en-US",
or "en-CA-x-ca" for English and some of its variants.

.. note:: There is nothing to prevent you from using those abbreviations in your application!

************************
Using the Language Class
************************

Creating Language Files
=======================

Language files must be named with **_lang.php** as the filename extension.
For example, let's say you want to create a file containing error messages.
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

You can also load multiple language files at the same time by passing an array of language files as first parameter.
::

	$this->lang->load(array('filename1', 'filename2'));

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

***************
Class Reference
***************

.. php:class:: CI_Lang

	.. php:method:: load($langfile[, $idiom = ''[, $return = FALSE[, $add_suffix = TRUE[, $alt_path = '']]]])

		:param	mixed	$langfile: Language file to load or array with multiple files
		:param	string	$idiom: Language name (i.e. 'english')
		:param	bool	$return: Whether to return the loaded array of translations
		:param	bool	$add_suffix: Whether to add the '_lang' suffix to the language file name
		:param	string	$alt_path: An alternative path to look in for the language file
		:returns:	Array of language lines if $return is set to TRUE, otherwise void
		:rtype:	mixed

		Loads a language file.

	.. php:method:: line($line[, $log_errors = TRUE])

		:param	string	$line: Language line key name
		:param	bool	$log_errors: Whether to log an error if the line isn't found
		:returns:	Language line string or FALSE on failure
		:rtype:	string

		Fetches a single translation line from the already loaded language files,
		based on the line's name.