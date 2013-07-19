###########
URI Routing
###########

Typically there is a one-to-one relationship between a URL string and
its corresponding controller class/method. The segments in a URI
normally follow this pattern::

	example.com/class/function/id/

In some instances, however, you may want to remap this relationship so
that a different class/method can be called instead of the one
corresponding to the URL.

For example, let's say you want your URLs to have this prototype::

	example.com/product/1/
	example.com/product/2/
	example.com/product/3/
	example.com/product/4/

Normally the second segment of the URL is reserved for the method
name, but in the example above it instead has a product ID. To
overcome this, CodeIgniter allows you to remap the URI handler.

Setting your own routing rules
==============================

Routing rules are defined in your *application/config/routes.php* file.
In it you'll see an array called ``$route`` that permits you to specify
your own routing criteria. Routes can either be specified using wildcards
or Regular Expressions.

Wildcards
=========

A typical wildcard route might look something like this::

	$route['product/:num'] = 'catalog/product_lookup';

In a route, the array key contains the URI to be matched, while the
array value contains the destination it should be re-routed to. In the
above example, if the literal word "product" is found in the first
segment of the URL, and a number is found in the second segment, the
"catalog" class and the "product_lookup" method are instead used.

You can match literal values or you can use two wildcard types:

**(:num)** will match a segment containing only numbers.
**(:any)** will match a segment containing any character (except for '/', which is the segment delimiter).

.. note:: Wildcards are actually aliases for regular expressions, with
	**:any** being translated to **[^/]+** and **:num** to **[0-9]+**,
	respectively.

.. note:: Routes will run in the order they are defined. Higher routes
	will always take precedence over lower ones.

.. note:: Route rules are not filters! Setting a rule of e.g.
	'foo/bar/(:num)' will not prevent controller *Foo* and method
	*bar* to be called with a non-numeric value if that is a valid
	route.

Examples
========

Here are a few routing examples::

	$route['journals'] = 'blogs';

A URL containing the word "journals" in the first segment will be
remapped to the "blogs" class.

::

	$route['blog/joe'] = 'blogs/users/34';

A URL containing the segments blog/joe will be remapped to the "blogs"
class and the "users" method. The ID will be set to "34".

::

	$route['product/(:any)'] = 'catalog/product_lookup';

A URL with "product" as the first segment, and anything in the second
will be remapped to the "catalog" class and the "product_lookup"
method.

::

	$route['product/(:num)'] = 'catalog/product_lookup_by_id/$1';

A URL with "product" as the first segment, and a number in the second
will be remapped to the "catalog" class and the
"product_lookup_by_id" method passing in the match as a variable to
the method.

.. important:: Do not use leading/trailing slashes.

Regular Expressions
===================

If you prefer you can use regular expressions to define your routing
rules. Any valid regular expression is allowed, as are back-references.

.. note:: If you use back-references you must use the dollar syntax
	rather than the double backslash syntax.

A typical RegEx route might look something like this::

	$route['products/([a-z]+)/(\d+)'] = '$1/id_$2';

In the above example, a URI similar to products/shirts/123 would instead
call the "shirts" controller class and the "id_123" method.

With regular expressions, you can also catch a segment containing a
forward slash ('/'), which would usually represent the delimiter between
multiple segments.
For example, if a user accesses a password protected area of your web
application and you wish to be able to redirect them back to the same
page after they log in, you may find this example useful::

	$route['login/(.+)'] = 'auth/login/$1';

That will call the "auth" controller class and its ``login()`` method,
passing everything contained in the URI after *login/* as a parameter.

For those of you who don't know regular expressions and want to learn
more about them, `regular-expressions.info <http://www.regular-expressions.info/>`
might be a good starting point.

.. note:: You can also mix and match wildcards with regular expressions.

Callbacks
=========

If you are using PHP >= 5.3 you can use callbacks in place of the normal
routing rules to process the back-references. Example::

	$route['products/([a-zA-Z]+)/edit/(\d+)'] = function ($product_type, $id)
	{
		return 'catalog/product_edit/' . strtolower($product_type) . '/' . $id;
	};

Reserved Routes
===============

There are three reserved routes::

	$route['default_controller'] = 'welcome';

This route indicates which controller class should be loaded if the URI
contains no data, which will be the case when people load your root URL.
In the above example, the "welcome" class would be loaded. You are
encouraged to always have a default route otherwise a 404 page will
appear by default.

::

	$route['404_override'] = '';

This route indicates which controller class should be loaded if the
requested controller is not found. It will override the default 404
error page. It won't affect to the ``show_404()`` function, which will
continue loading the default *error_404.php* file at
*application/views/errors/error_404.php*.


::

	$route['translate_uri_dashes'] = FALSE;

As evident by the boolean value, this is not exactly a route. This
option enables you to automatically replace dashes ('-') with
underscores in the controller and method URI segments, thus saving you
additional route entries if you need to do that.
This is required, because the dash isn't a valid class or method name
character and would cause a fatal error if you try to use it.

.. important:: The reserved routes must come before any wildcard or
	regular expression routes.