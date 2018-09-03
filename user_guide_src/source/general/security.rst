########
Security
########

This page describes some "best practices" regarding web security, and
details CodeIgniter's internal security features.

.. note:: If you came here looking for a security contact, please refer to
	our `Contribution Guide <../contributing/index>`.

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
================

During system initialization all global variables that are found to exist
in the ``$_GET``, ``$_POST``, ``$_REQUEST`` and ``$_COOKIE`` are unset.

The unsetting routine is effectively the same as *register_globals = off*.

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

#. Validate the data to ensure it conforms to the correct type, length,
   size, etc.
#. Filter the data as if it were tainted.
#. Escape the data before submitting it into your database or outputting
   it to a browser.

CodeIgniter provides the following functions and tips to assist you
in this process:

XSS Filtering
=============

CodeIgniter comes with a Cross Site Scripting filter. This filter
looks for commonly used techniques to embed malicious JavaScript into
your data, or other types of code that attempt to hijack cookies or
do other malicious things. The XSS Filter is described
:doc:`here <../libraries/security>`.

.. note:: XSS filtering should *only be performed on output*. Filtering
	input data may modify the data in undesirable ways, including
	stripping special characters from passwords, which reduces
	security instead of improving it.

CSRF protection
===============

CSRF stands for Cross-Site Request Forgery, which is the process of an
attacker tricking their victim into unknowingly submitting a request.

CodeIgniter provides CSRF protection out of the box, which will get
automatically triggered for every non-GET HTTP request, but also needs
you to create your submit forms in a certain way. This is explained in
the :doc:`Security Library <../libraries/security>` documentation.

Password handling
=================

It is *critical* that you handle passwords in your application properly.

Unfortunately, many developers don't know how to do that, and the web is
full of outdated or otherwise wrongful advices, which doesn't help.

We would like to give you a list of combined do's and don'ts to help you
with that. Please read below.

-  DO NOT store passwords in plain-text format.

   Always **hash** your passwords.

-  DO NOT use Base64 or similar encoding for storing passwords.

   This is as good as storing them in plain-text. Really. Do **hashing**,
   not *encoding*.

   Encoding, and encryption too, are two-way processes. Passwords are
   secrets that must only be known to their owner, and thus must work
   only in one direction. Hashing does that - there's *no* un-hashing or
   de-hashing, but there is decoding and decryption.

-  DO NOT use weak or broken hashing algorithms like MD5 or SHA1.

   These algorithms are old, proven to be flawed, and not designed for
   password hashing in the first place.

   Also, DON'T invent your own algorithms.

   Only use strong password hashing algorithms like BCrypt, which is used
   in PHP's own `Password Hashing <https://secure.php.net/password>`_ functions.

   Please use them, even if you're not running PHP 5.5+, CodeIgniter
   provides them for you.

-  DO NOT ever display or send a password in plain-text format!

   Even to the password's owner, if you need a "Forgotten password"
   feature, just randomly generate a new, one-time (this is also important)
   password and send that instead.

-  DO NOT put unnecessary limits on your users' passwords.

   If you're using a hashing algorithm other than BCrypt (which has a limit
   of 72 characters), you should set a relatively high limit on password
   lengths in order to mitigate DoS attacks - say, 1024 characters.

   Other than that however, there's no point in forcing a rule that a
   password can only be up to a number of characters, or that it can't
   contain a certain set of special characters.

   Not only does this **reduce** security instead of improving it, but
   there's literally no reason to do it. No technical limitations and
   no (practical) storage constraints apply once you've hashed them, none!

Validate input data
===================

CodeIgniter has a :doc:`Form Validation Library
<../libraries/form_validation>` that assists you in
validating, filtering, and prepping your data.

Even if that doesn't work for your use case however, be sure to always
validate and sanitize all input data. For example, if you expect a numeric
string for an input variable, you can check for that with ``is_numeric()``
or ``ctype_digit()``. Always try to narrow down your checks to a certain
pattern.

Have it in mind that this includes not only ``$_POST`` and ``$_GET``
variables, but also cookies, the user-agent string and basically
*all data that is not created directly by your own code*.


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
