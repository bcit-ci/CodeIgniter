########
Security
########

This page describes some "best practices" regarding web security, and
details CodeIgniter's internal security features.

URI Security
============

CodeIgniter is fairly restrictive regarding which characters it allows
in your URI strings in order to help minimize the possibility that
malicious data can be passed to your application. URIs may only contain
the following:

-  Alpha-numeric text (latin characters only)
-  Tilde: ~
-  Percent sign: %
-  Period: .
-  Colon: :
-  Underscore: \_
-  Dash: -
-  Space

Register_globals
=================

During system initialization all global variables are unset, except
those found in the ``$_GET``, ``$_POST``, and ``$_COOKIE`` arrays.
The unsetting routine is effectively the same as
*register_globals = off*.

display_errors
==============

In production environments, it is typically desirable to "disable" PHP's
error reporting by setting the internal *display_errors* flag to a value
of 0. This disables native PHP errors from being rendered as output,
which may potentially contain sensitive information.

Setting CodeIgniter's **ENVIRONMENT** constant in index.php to a value of
**\'production\'** will turn off these errors. In development mode, it is
recommended that a value of 'development' is used. More information
about differentiating between environments can be found on the
:doc:`Handling Environments <environments>` page.

magic_quotes_runtime
====================

The *magic_quotes_runtime* directive is turned off during system
initialization so that you don't have to remove slashes when retrieving
data from your database.

**************
Best Practices
**************

Before accepting any data into your application, whether it be POST data
from a form submission, COOKIE data, URI data, XML-RPC data, or even
data from the SERVER array, you are encouraged to practice this three
step approach:

#. Filter the data as if it were tainted.
#. Validate the data to ensure it conforms to the correct type, length,
   size, etc. (sometimes this step can replace step one)
#. Escape the data before submitting it into your database.

CodeIgniter provides the following functions to assist in this process:

XSS Filtering
=============

CodeIgniter comes with a Cross Site Scripting filter. This filter
looks for commonly used techniques to embed malicious JavaScript into
your data, or other types of code that attempt to hijack cookies or
do other malicious things. The XSS Filter is described
:doc:`here <../libraries/security>`.

Validate the data
=================

CodeIgniter has a :doc:`Form Validation Library
<../libraries/form_validation>` that assists you in
validating, filtering, and prepping your data.

Escape all data before database insertion
=========================================

Never insert information into your database without escaping it.
Please see the section that discusses :doc:`database queries
<../database/queries>` for more information.

Hide your files
===============

Another good security practice is to only leave your *index.php*
and "assets" (e.g. .js, css and image files) under your server's
*webroot* directory (most commonly named "htdocs/"). These are
the only files that you would need to be accessible from the web.

Allowing your visitors to see anything else would potentially
allow them to access sensitive data, execute scripts, etc.

If you're not allowed to do that, you can try using a .htaccess
file to restrict access to those resources.

CodeIgniter will have an index.html file in all of its
directories in an attempt to hide some of this data, but have
it in mind that this is not enough to prevent a serious
attacker.