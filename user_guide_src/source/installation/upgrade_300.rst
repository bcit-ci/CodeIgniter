#############################
Upgrading from 2.2.x to 3.0.x
#############################

Before performing an update you should take your site offline by replacing the index.php file with a static one.

*************************************
Step 1: Update your CodeIgniter files
*************************************

**Replace** all files and directories in your *system/* directory and
replace your index.php file. If any modifications were made to your
index.php they will need to be made fresh in this new one.

.. important:: You have to delete the old *system/* directory first and
	then put the new one in its place. A simple copy-paste may cause
	issues.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

**************************************
Step 2: Update your classes file names
**************************************

Starting with CodeIgniter 3.0, all class filenames (libraries, drivers, controllers
and models) must be named in a Ucfirst-like manner or in other words - they must
start with a capital letter.

For example, if you have the following library file:

	application/libraries/mylibrary.php

... then you'll have to rename it to:

	application/libraries/Mylibrary.php

The same goes for driver libraries and extensions and/or overrides of CodeIgniter's
own libraries and core classes.

	application/libraries/MY_email.php  
	application/core/MY_log.php

The above files should respectively be renamed to the following:

	application/libraries/MY_Email.php  
	application/core/MY_Log.php

Controllers:

	application/controllers/welcome.php	->	application/controllers/Welcome.php

Models:

	application/models/misc_model.php	->	application/models/Misc_model.php

Please note that this DOES NOT affect directories, configuration files, views,
helpers, hooks and anything else - it is only applied to classes.

You must now follow just one simple rule - class names in Ucfirst and everything else
in lowercase.

********************************
Step 3: Replace config/mimes.php
********************************

This config file has been updated to contain more user mime-types, please copy
it to *application/config/mimes.php*.

**************************************************************
Step 4: Remove $autoload['core'] from your config/autoload.php
**************************************************************

Use of the ``$autoload['core']`` config array has been deprecated as of CodeIgniter 1.4.1 and is now removed.
Move any entries that you might have listed there to ``$autoload['libraries']`` instead.

***************************************************
Step 5: Move your Log class overrides or extensions
***************************************************

The Log Class is considered as a "core" class and is now located in the
**system/core/** directory. Therefore, in order for your Log class overrides
or extensions to work, you need to move them to **application/core/**:

	application/libraries/Log.php -> application/core/Log.php  
	application/libraries/MY_Log.php -> application/core/MY_Log.php

*****************************************
Step 6: Update your Session library usage
*****************************************

The :doc:`Session Library </libraries/sessions>` has been completely
re-written in CodeIgniter 3 and now comes with a bunch of new features,
but that also means that there are changes that you should make ...

Most notably, the library now uses separate storage drivers instead of
always relying on (encrypted) cookies.
In fact, cookies as storage have now been removed and you must always use
some kind of server-side storage engine, with the file-system being the
default option.

The Session Class now utilizes PHP's own mechanisms for building custom
session handlers, which also means that your session data is now
accessible via the ``$_SESSION`` superglobal (though, we've kept the
possibility to use it as "userdata", like you've done until now).

A few configuration options have been removed and a few have been added.
You should really read the whole :doc:`Session library manual
</libraries/sessions>` for the details, but here's a short list of changes
that you should make:

  - Set your ``$config['sess_driver']`` value

    It will default to 'files', unless you've previously used
    ``$config['sess_use_database']``, in which case it will be set to
    'database'.

  - Set a ``$config['sess_save_path']`` value

    For the 'database' driver, a fallback to ``$config['sess_table_name']``
    is in place, but otherwise requires you to read the manual for the
    specific driver of your choice.

  - Update your ``ci_sessions`` table ('database' driver only)

    The table structure has changed a bit, and more specifically:

      - ``session_id`` field is renamed to ``id``
      - ``user_agent`` field is dropped
      - ``user_data`` field is renamed to ``data`` and under MySQL is now of type BLOB
      - ``last_activity`` field is renamed to ``timestamp``

    This is accompanied by a slight change in the table indexes too, so
    please read the manual about the `Session Database Driver
    <../libraries/sessions.html#database-driver>`_ for more information.

    .. important:: Only MySQL and PostgreSQL are officially supported
    	now. Other databases may still work, but due to lack of advisory
    	locking features, they are unsafe for concurrent requests and
    	you should consider using another driver instead.

  - Remove ``$config['sess_match_useragent']``

    The user-agent string is input supplied by the user's browser, or in
    other words: client side input. As such, it is an ineffective feature
    and hence why it has been removed.

  - Remove ``$config['sess_encrypt_cookie']``

    As already noted, the library no longer uses cookies as a storage
    mechanism, which renders this option useless.

  - Remove ``$config['sess_expire_on_close']``

    This option is still usable, but only for backwards compatibility
    purposes and it should be otherwise removed. The same effect is
    achieved by setting ``$config['sess_expiration']`` to 0.

  - Check "flashdata" for collisions with "userdata"

    Flashdata is now just regular "userdata", only marked for deletion on
    the next request. In other words: you can't have both "userdata" and
    "flashdata" with the same name, because it's the same thing.

  - Check usage of session metadata

    Previously, you could access the 'session_id', 'ip_address',
    'user_agent' and 'last_activity' metadata items as userdata.
    This is no longer possible, and you should read the notes about
    `Session Metadata <../libraries/sessions.html#accessing-session-metadata>`_
    if your application relies on those values.

  - Check ``unset_userdata()`` usage

    Previously, this method used to accept an associative array of
    ``'key' => 'dummy value'`` pairs for unsetting multiple keys. That
    however makes no sense and you now have to pass *only* the keys, as
    the elements of an array.

    ::

    	// Old
    	$this->session->unset_userdata(array('item' => '', 'item2' => ''));

    	// New
    	$this->session->unset_userdata(array('item', 'item2'));

Finally, if you have written a Session extension, you must now move it to
the *application/libraries/Session/* directory, although chances are that
it will now also have to be re-factored.

***************************************
Step 7: Update your config/database.php
***************************************

Due to 3.0.0's renaming of Active Record to Query Builder, inside your
**config/database.php**, you will need to rename the ``$active_record``
variable to ``$query_builder``::

	$active_group = 'default';
	// $active_record = TRUE;
	$query_builder = TRUE;

************************************
Step 8: Replace your error templates
************************************

In CodeIgniter 3.0, the error templates are now considered as views and have been moved to the
*application/views/errors* directory.

Furthermore, we've added support for CLI error templates in plain-text format that unlike HTML,
is suitable for the command line. This of course requires another level of separation.

It is safe to move your old templates from *application/errors* to *application/views/errors/html*,
but you'll have to copy the new *application/views/errors/cli* directory from the CodeIgniter archive.

******************************************
Step 9: Update your config/routes.php file
******************************************

Routes containing :any
======================

Historically, CodeIgniter has always provided the **:any** wildcard in
routing, with the intention of providing a way to match any character
**within** an URI segment.

However, the **:any** wildcard is actually just an alias for a regular
expression and used to be executed in that manner as **.+**. This is
considered a bug, as it also matches the / (forward slash) character, which
is the URI segment delimiter and that was never the intention.

In CodeIgniter 3, the **:any** wildcard will now represent **[^/]+**, so
that it will not match a forward slash.

There are certainly many developers that have utilized this bug as an actual
feature. If you're one of them and want to match a forward slash, please use
the **.+** regular expression::

	(.+)	// matches ANYTHING
	(:any)	// matches any character, except for '/'

Directories and 'default_controller', '404_override'
====================================================

As you should know, the ``$route['default_controller']`` and
``$route['404_override']`` settings accept not only a controller name, but
also *controller/method* pairs. However, a bug in the routing logic has
made it possible for some users to use that as *directory/controller*
instead.

As already said, this behavior was incidental and was never intended, nor
documented. If you've relied on it, your application will break with
CodeIgniter 3.0.

Another notable change in version 3 is that 'default_controller' and
'404_override' are now applied *per directory*. To explain what this means,
let's take the following example::

	$route['default_controller'] = 'main';

Now, assuming that your website is located at *example.com*, you already
know that if a user visits ``http://example.com/``, the above setting will
cause your 'Main' controller to be loaded.

However, what happens if you have an *application/controllers/admin/*
directory and the user visits ``http://example.com/admin/``?
In CodeIgniter 3, the router will look for a 'Main' controller under the
admin/ directory as well. If not found, a Not Found (404) will be triggered.

The same rule applies to the '404_override' setting.

*************************************************************************
Step 10: Many functions now return NULL instead of FALSE on missing items
*************************************************************************

Many methods and functions now return NULL instead of FALSE when the required items don't exist:

 - :doc:`Common functions <../general/common_functions>`

   - config_item()

 - :doc:`Config Class <../libraries/config>`

   - config->item()
   - config->slash_item()

 - :doc:`Input Class <../libraries/input>`

   - input->get()
   - input->post()
   - input->get_post()
   - input->cookie()
   - input->server()
   - input->input_stream()
   - input->get_request_header()

 - :doc:`Session Class <../libraries/sessions>`

   - session->userdata()
   - session->flashdata()

 - :doc:`URI Class <../libraries/uri>`

   - uri->segment()
   - uri->rsegment()

 - :doc:`Array Helper <../helpers/array_helper>`

   - element()
   - elements()

*******************************
Step 11: Usage of XSS filtering
*******************************

Many functions in CodeIgniter allow you to use its XSS filtering feature
on demand by passing a boolean parameter. The default value of that
parameter used to be boolean FALSE, but it is now changed to NULL and it
will be dynamically determined by your ``$config['global_xss_filtering']``
value.

If you used to manually pass a boolean value for the ``$xss_filter``
parameter or if you've always had ``$config['global_xss_filtering']`` set
to FALSE, then this change doesn't concern you.

Otherwise however, please review your usage of the following functions:

 - :doc:`Input Library <../libraries/input>`

   - input->get()
   - input->post()
   - input->get_post()
   - input->cookie()
   - input->server()
   - input->input_stream()

 - :doc:`Cookie Helper <../helpers/cookie_helper>` :php:func:`get_cookie()`

.. important:: Another related change is that the ``$_GET``, ``$_POST``,
	``$_COOKIE`` and ``$_SERVER`` superglobals are no longer
	automatically overwritten when global XSS filtering is turned on.

*************************************************
Step 12: Check for potential XSS issues with URIs
*************************************************

The :doc:`URI Library <../libraries/uri>` used to automatically convert
a certain set of "programmatic characters" to HTML entities when they
are encountered in a URI segment.

This was aimed at providing some automatic XSS protection, in addition
to the ``$config['permitted_uri_chars']`` setting, but has proven to be
problematic and is now removed in CodeIgniter 3.0.

If your application has relied on this feature, you should update it to
filter URI segments through ``$this->security->xss_clean()`` whenever you
output them.

****************************************************************
Step 13: Check for usage of the 'xss_clean' Form validation rule
****************************************************************

A largely unknown rule about XSS cleaning is that it should *only be
applied to output*, as opposed to input data.

We've made that mistake ourselves with our automatic and global XSS cleaning
feature (see previous step about XSS above), so now in an effort to discourage that
practice, we're also removing 'xss_clean' from the officially supported
list of :doc:`form validation <../libraries/form_validation>` rules.

Because the :doc:`Form Validation library <../libraries/form_validation>`
generally validates *input* data, the 'xss_clean' rule simply doesn't
belong in it.

If you really, really need to apply that rule, you should now also load the
:doc:`Security Helper <../helpers/security_helper>`, which contains
``xss_clean()`` as a regular function and therefore can be also used as
a validation rule.

********************************************************
Step 14: Update usage of Input Class's get_post() method
********************************************************

Previously, the :doc:`Input Class <../libraries/input>` method ``get_post()``
was searching first in POST data, then in GET data. This method has been
modified so that it searches in GET then in POST, as its name suggests.

A method has been added, ``post_get()``, which searches in POST then in GET, as
``get_post()`` was doing before.

********************************************************************
Step 15: Update usage of Directory Helper's directory_map() function
********************************************************************

In the resulting array, directories now end with a trailing directory
separator (i.e. a slash, usually).

*************************************************************
Step 16: Update usage of Database Forge's drop_table() method
*************************************************************

Up until now, ``drop_table()`` added an IF EXISTS clause by default or it didn't work
at all with some drivers. In CodeIgniter 3.0, the IF EXISTS condition is no longer added
by default and has an optional second parameter that allows that instead and is set to
FALSE by default.

If your application relies on IF EXISTS, you'll have to change its usage.

::

	// Now produces just DROP TABLE `table_name`
	$this->dbforge->drop_table('table_name');

	// Produces DROP TABLE IF EXISTS `table_name`
	$this->dbforge->drop_table('table_name', TRUE);

.. note:: The given example uses MySQL-specific syntax, but it should work across
	all drivers with the exception of ODBC.

***********************************************************
Step 17: Change usage of Email library with multiple emails
***********************************************************

The :doc:`Email Library <../libraries/email>` will automatically clear the
set parameters after successfully sending emails. To override this behaviour,
pass FALSE as the first parameter in the ``send()`` method:

::

	if ($this->email->send(FALSE))
 	{
 		// Parameters won't be cleared
 	}

***************************************************
Step 18: Update your Form_validation language lines
***************************************************

Two improvements have been made to the :doc:`Form Validation Library
<../libraries/form_validation>`'s :doc:`language <../libraries/language>`
files and error messages format:

 - :doc:`Language Library <../libraries/language>` line keys now must be
   prefixed with **form_validation_** in order to avoid collisions::

	// Old
	$lang['rule'] = ...

	// New
	$lang['form_validation_rule'] = ...

 - The error messages format has been changed to use named parameters, to
   allow more flexibility than what `sprintf()` offers::

	// Old
	'The %s field does not match the %s field.'

	// New
	'The {field} field does not match the {param} field.'

.. note:: The old formatting still works, but the non-prefixed line keys
	are DEPRECATED and scheduled for removal in CodeIgniter 3.1+.
	Therefore you're encouraged to update its usage sooner rather than
	later.

************************************************************
Step 19: Make sure your 'base_url' config value is not empty
************************************************************

When ``$config['base_url']`` is not set, CodeIgniter tries to automatically
detect what your website's base URL is. This is done purely for convenience
when you are starting development of a new application.

Auto-detection is never reliable and also has security implications, which
is why you should **always** have it manually configured!

One of the changes in CodeIgniter 3.0.3 is how this auto-detection works,
and more specifically it now falls back to the server's IP address instead
of the hostname requested by the client. Therefore, if you've ever relied
on auto-detection, it will change how your website works now.

In case you need to allow e.g. multiple domains, or both http:// and
https:// prefixes to be dynamically used depending on the request,
remember that *application/config/config.php* is still a PHP script, in
which you can create this logic with a few lines of code. For example::

	$allowed_domains = array('domain1.tld', 'domain2.tld');
	$default_domain  = 'domain1.tld';

	if (in_array($_SERVER['HTTP_HOST'], $allowed_domains, TRUE))
	{
		$domain = $_SERVER['HTTP_HOST'];
	}
	else
	{
		$domain = $default_domain;
	}

	if ( ! empty($_SERVER['HTTPS']))
	{
		$config['base_url'] = 'https://'.$domain;
	}
	else
	{
		$config['base_url'] = 'http://'.$domain;
	}


****************************************************************
Step 20: Remove usage of (previously) deprecated functionalities
****************************************************************

In addition to the ``$autoload['core']`` configuration setting, there's a
number of other functionalities that have been removed in CodeIgniter 3.0.0:

The SHA1 library
================

The previously deprecated SHA1 library has been removed, alter your code to use PHP's native
``sha1()`` function to generate a SHA1 hash.

Additionally, the ``sha1()`` method in the :doc:`Encrypt Library <../libraries/encrypt>` has been removed.

The EXT constant
================

Usage of the ``EXT`` constant has been deprecated since dropping support for PHP 4. There's no
longer a need to maintain different filename extensions and in this new CodeIgniter version,
the ``EXT`` constant has been removed. Use just '.php' instead.

Smiley helper
=============

The *Smiley Helper* is a legacy feature from EllisLab's ExpressionEngine product.
However, it is too specific for a general purpose framework like CodeIgniter
and as such it is now deprecated.

Also, the previously deprecated ``js_insert_smiley()`` (since version 1.7.2) is now removed.

The Encrypt library
===================

Following numerous vulnerability reports, the :doc:`Encrypt Library <../libraries/encrypt>` has
been deprecated and a new, :doc:`Encryption Library <../libraries/encryption>` is added to take
its place.

The new library requires either the `MCrypt extension <https://secure.php.net/mcrypt>`_ (and /dev/urandom
availability) or PHP 5.3.3 and the `OpenSSL extension <https://secure.php.net/openssl>`_.
While this might be rather inconvenient, it is a requirement that allows us to have properly
implemented cryptographic functions.

.. note:: The :doc:`Encrypt Library <../libraries/encrypt>` is still available for the purpose
	of keeping backwards compatibility.

.. important:: You are strongly encouraged to switch to the new :doc:`Encryption Library
	<../libraries/encryption>` as soon as possible!

The Cart library
================

The *Cart Library*, similarly to the *Smiley Helper* is too specific for
CodeIgniter. It is now deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: The library is still available, but you're strongly encouraged to
	remove its usage sooner rather than later.

Database drivers 'mysql', 'sqlite', 'mssql', 'pdo/dblib'
========================================================

The **mysql** driver utilizes the old 'mysql' PHP extension, known for its aging code base and
many low-level problems. The extension is deprecated as of PHP 5.5 and CodeIgniter deprecates
it in version 3.0, switching the default configured MySQL driver to **mysqli**.

Please use either the 'mysqli' or 'pdo/mysql' drivers for MySQL. The old 'mysql' driver will be
removed at some point in the future.

The **sqlite**, **mssql** and **pdo/dblib** (also known as pdo/mssql or pdo/sybase) drivers
all depend on PHP extensions that for different reasons no longer exist since PHP 5.3.

Therefore we are now deprecating these drivers as we will have to remove them in one of the next
CodeIgniter versions. You should use the more advanced, **sqlite3**, **sqlsrv** or **pdo/sqlsrv**
drivers respectively.

.. note:: These drivers are still available, but you're strongly encouraged to switch to other ones
	sooner rather than later.

Security helper do_hash()
=========================

:doc:`Security Helper <../helpers/security_helper>` function ``do_hash()`` is now just an alias for
PHP's native ``hash()`` function. It is deprecated and scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove its usage sooner
	rather than later.

The $config['global_xss_filtering'] setting
===========================================

As already explained above, XSS filtering should not be done on input data,
but on output instead. Therefore, the ``$config['global_xss_filtering']``,
which automatically filters *input* data, is considered a bad practice and
is now deprecated.

Instead, you should manually escape any user-provided data via the
:php:func:`xss_clean()` function when you need to output it, or use a
library like `HTML Purifier <http://htmlpurifier.org/>`_ that does that
for you.

.. note:: The setting is still available, but you're strongly encouraged to
	remove its usage sooner rather than later.

File helper read_file()
=======================

:doc:`File Helper <../helpers/file_helper>` function ``read_file()`` is now just an alias for
PHP's native ``file_get_contents()`` function. It is deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove its usage sooner
	rather than later.

String helper repeater()
========================

:doc:`String Helper <../helpers/string_helper>` function ``repeater()`` is now just an alias for
PHP's native ``str_repeat()`` function. It is deprecated and scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove its usage sooner
	rather than later.

String helper trim_slashes()
============================

:doc:`String Helper <../helpers/string_helper>` function ``trim_slashes()`` is now just an alias
for PHP's native ``trim()`` function (with a slash passed as its second argument). It is deprecated and
scheduled for removal in CodeIgniter 3.1+.

.. note:: This function is still available, but you're strongly encouraged to remove its usage sooner
	rather than later.

Form helper form_prep()
=======================

:doc:`Form Helper <../helpers/form_helper>` function ``form_prep()`` is now
just an alias for :doc:`common function </general/common_functions>`
:func:`html_escape()`. It is deprecated and will be removed in the future.

Please use :php:func:`html_escape()` instead.

.. note:: This function is still available, but you're strongly encouraged
	to remove its usage sooner rather than later.

Email helper functions
======================

The *Email Helper* only has two functions:

 - ``valid_email()``
 - ``send_email()``

Both of them are now aliases for PHP's native ``filter_var()`` and ``mail()`` functions, respectively.
Therefore, the *Email Helper* altogether is being deprecated and is scheduled for removal in CodeIgniter 3.1+.

.. note:: These functions are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

Date helper standard_date()
===========================

:doc:`Date Helper <../helpers/date_helper>` function ``standard_date()`` is being deprecated due
to the availability of native PHP `constants <https://secure.php.net/manual/en/class.datetime.php#datetime.constants.types>`_,
which when combined with ``date()`` provide the same functionality. Furthermore, they have the
exact same names as the ones supported by ``standard_date()``. Here are examples of how to replace
its usage:

::

	// Old way
	standard_date(); // defaults to standard_date('DATE_RFC822', now());

	// Replacement
	date(DATE_RFC822, now());

	// Old way
	standard_date('DATE_ATOM', $time);

	// Replacement
	date(DATE_ATOM, $time);

.. note:: This function is still available, but you're strongly encouraged to remove its usage sooner
	rather than later as it is scheduled for removal in CodeIgniter 3.1+.

HTML helpers nbs(), br()
========================

:doc:`HTML Helper <../helpers/html_helper>` functions ``nbs()`` and ``br()`` are just aliases
for the native ``str_repeat()`` function used with ``&nbsp;`` and ``<br >`` respectively.

Because there's no point in just aliasing native PHP functions, they are now deprecated and
scheduled for removal in CodeIgniter 3.1+.

.. note:: These functions are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

Pagination library 'anchor_class' setting
=========================================

The :doc:`Pagination Library <../libraries/pagination>` now supports adding pretty much any HTML
attribute to your anchors via the 'attributes' configuration setting. This includes passing the
'class' attribute and using the separate 'anchor_class' setting no longer makes sense.
As a result of that, the 'anchor_class' setting is now deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This setting is still available, but you're strongly encouraged to remove its usage sooner
	rather than later.

String helper random_string() types 'unique' and 'encrypt'
==========================================================

When using the :doc:`String Helper <../helpers/string_helper>` function :php:func:`random_string()`,
you should no longer pass the **unique** and **encrypt** randomization types. They are only
aliases for **md5** and **sha1** respectively and are now deprecated and scheduled for removal
in CodeIgniter 3.1+.

.. note:: These options are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

URL helper url_title() separators 'dash' and 'underscore'
=========================================================

When using the :doc:`URL Helper <../helpers/url_helper>` function :php:func:`url_title()`, you
should no longer pass **dash** or **underscore** as the word separator. This function will
now accept any character and you should just pass the chosen character directly, so you
should write '-' instead of 'dash' and '_' instead of 'underscore'.

**dash** and **underscore** now act as aliases and are deprecated and scheduled for removal
in CodeIgniter 3.1+.

.. note:: These options are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

Session Library method all_userdata()
=====================================

As seen in the :doc:`Change Log <../changelog>`, :doc:`Session Library <../libraries/sessions>`
method ``userdata()`` now allows you to fetch all userdata by simply omitting its parameter::

	$this->session->userdata();

This makes the ``all_userdata()`` method redudant and therefore it is now just an alias for
``userdata()`` with the above shown usage and is being deprecated and scheduled for removal
in CodeIgniter 3.1+.

.. note:: This method is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

Database Forge method add_column() with an AFTER clause
=======================================================

If you have used the **third parameter** for :doc:`Database Forge <../database/forge>` method
``add_column()`` to add a field for an AFTER clause, then you should change its usage.

That third parameter has been deprecated and scheduled for removal in CodeIgniter 3.1+.

You should now put AFTER clause field names in the field definition array instead::

	// Old usage:
	$field = array(
		'new_field' => array('type' => 'TEXT')
	);

	$this->dbforge->add_column('table_name', $field, 'another_field');

	// New usage:
	$field = array(
		'new_field' => array('type' => 'TEXT', 'after' => 'another_field')
	);

	$this->dbforge->add_column('table_name', $field);

.. note:: The parameter is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

.. note:: This is for MySQL and CUBRID databases only! Other drivers don't support this
	clause and will silently ignore it.

URI Routing methods fetch_directory(), fetch_class(), fetch_method()
====================================================================

With properties ``CI_Router::$directory``, ``CI_Router::$class`` and ``CI_Router::$method``
being public and their respective ``fetch_*()`` no longer doing anything else to just return
the properties - it doesn't make sense to keep them.

Those are all internal, undocumented methods, but we've opted to deprecate them for now
in order to maintain backwards-compatibility just in case. If some of you have utilized them,
then you can now just access the properties instead::

	$this->router->directory;
	$this->router->class;
	$this->router->method;

.. note:: Those methods are still available, but you're strongly encouraged to remove their usage
	sooner rather than later.

Input library method is_cli_request()
=====================================

Calls to the ``CI_Input::is_cli_request()`` method are necessary at many places
in the CodeIgniter internals and this is often before the :doc:`Input Library
<../libraries/input>` is loaded. Because of that, it is being replaced by a common
function named :php:func:`is_cli()` and this method is now just an alias.

The new function is both available at all times for you to use and shorter to type.

::

	// Old
	$this->input->is_cli_request();

	// New
	is_cli();

``CI_Input::is_cli_request()`` is now now deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This method is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

Config library method system_url()
==================================

Usage of ``CI_Config::system_url()`` encourages insecure coding practices.
Namely, your CodeIgniter *system/* directory shouldn't be publicly accessible
from a security point of view.

Because of this, this method is now deprecated and scheduled for removal in
CodeIgniter 3.1+.

.. note:: This method is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

The Javascript library
======================

The *Javascript Library* has always had an 'experimental' status and was
never really useful, nor a proper solution.

It is now deprecated and scheduled for removal in CodeIgniter 3.1+.

.. note:: This library is still available, but you're strongly encouraged to remove its usage
	sooner rather than later.

Form Validation method prep_for_form()
======================================

The :doc:`Form Validation Library <../libraries/form_validation>` has a
``prep_for_form()`` method, which is/can also be used as a rule in
``set_rules()`` to automatically perform HTML encoding on input data.

Automatically encoding input (instead of output) data is a bad practice in
the first place, and CodeIgniter and PHP itself offer other alternatives
to this method anyway.
For example, :doc:`Form Helper <../helpers/form_helper>` functions will
automatically perform HTML escaping when necessary.

Therefore, the *prep_for_form* method/rule is pretty much useless and is now
deprecated and scheduled for removal in 3.1+.

.. note:: The method is still available, but you're strongly encouraged to
	remove its usage sooner rather than later.

***********************************************************
Step 21: Check your usage of Text helper highlight_phrase()
***********************************************************

The default HTML tag used by :doc:`Text Helper <../helpers/text_helper>` function
:func:`highlight_phrase()` has been changed from ``<strong>`` to the new HTML5
tag ``<mark>``.

Unless you've used your own highlighting tags, this might cause trouble
for your visitors who use older web browsers such as Internet Explorer 8.
We therefore suggest that you add the following code to your CSS files
in order to avoid backwards compatibility with old browsers::

	mark {
		background: #ff0;
		color: #000;
	};
