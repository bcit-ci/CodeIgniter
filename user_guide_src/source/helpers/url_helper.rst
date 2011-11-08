##########
URL Helper
##########

The URL Helper file contains functions that assist in working with URLs.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code

::

	$this->load->helper('url');

The following functions are available:

site_url()
==========

Returns your site URL, as specified in your config file. The index.php
file (or whatever you have set as your site index_page in your config
file) will be added to the URL, as will any URI segments you pass to the
function, and the url_suffix as set in your config file.

You are encouraged to use this function any time you need to generate a
local URL so that your pages become more portable in the event your URL
changes.

Segments can be optionally passed to the function as a string or an
array. Here is a string example

::

	echo site_url("news/local/123");

The above example would return something like:
http://example.com/index.php/news/local/123

Here is an example of segments passed as an array

::

	$segments = array('news', 'local', '123');
	echo site_url($segments);

base_url()
===========

Returns your site base URL, as specified in your config file. Example

::

	echo base_url();

This function returns the same thing as `site_url`, without the
index_page or url_suffix being appended.

Also like site_url, you can supply segments as a string or an array.
Here is a string example

::

	echo base_url("blog/post/123");

The above example would return something like:
http://example.com/blog/post/123

This is useful because unlike `site_url()`, you can supply a string to a
file, such as an image or stylesheet. For example

::

	echo base_url("images/icons/edit.png");

This would give you something like:
http://example.com/images/icons/edit.png

current_url()
=============

Returns the full URL (including segments) of the page being currently
viewed.

uri_string()
============

Returns the URI segments of any page that contains this function. For
example, if your URL was this

::

	http://some-site.com/blog/comments/123

The function would return

::

	/blog/comments/123

index_page()
============

Returns your site "index" page, as specified in your config file.
Example

::

	echo index_page();

anchor()
========

Creates a standard HTML anchor link based on your local site URL

::

	<a href="http://example.com">Click Here</a>

The tag has three optional parameters

::

	anchor(uri segments, text, attributes)

The first parameter can contain any segments you wish appended to the
URL. As with the site_url() function above, segments can be a string or
an array.

.. note:: If you are building links that are internal to your application
	do not include the base URL (http://...). This will be added automatically
	from the information specified in your config file. Include only the
	URI segments you wish appended to the URL.

The second segment is the text you would like the link to say. If you
leave it blank, the URL will be used.

The third parameter can contain a list of attributes you would like
added to the link. The attributes can be a simple string or an
associative array.

Here are some examples

::

	echo anchor('news/local/123', 'My News', 'title="News title"');

Would produce: <a href="http://example.com/index.php/news/local/123"
title="News title">My News</a>

::

	echo anchor('news/local/123', 'My News', array('title' => 'The best news!'));

Would produce: <a href="http://example.com/index.php/news/local/123"
title="The best news!">My News</a>

anchor_popup()
==============

Nearly identical to the anchor() function except that it opens the URL
in a new window. You can specify JavaScript window attributes in the
third parameter to control how the window is opened. If the third
parameter is not set it will simply open a new window with your own
browser settings. Here is an example with attributes

::

	$atts = array(               
		'width'      => '800',               
		'height'     => '600',               
		'scrollbars' => 'yes',               
		'status'     => 'yes',               
		'resizable'  => 'yes',               
		'screenx'    => '0',               
		'screeny'    => '0'             
	);

	echo anchor_popup('news/local/123', 'Click Me!', $atts);

Note: The above attributes are the function defaults so you only need to
set the ones that are different from what you need. If you want the
function to use all of its defaults simply pass an empty array in the
third parameter

::

	echo anchor_popup('news/local/123', 'Click Me!', array());

mailto()
========

Creates a standard HTML email link. Usage example

::

	echo mailto('me@my-site.com', 'Click Here to Contact Me');

As with the anchor() tab above, you can set attributes using the third
parameter.

safe_mailto()
=============

Identical to the above function except it writes an obfuscated version
of the mailto tag using ordinal numbers written with JavaScript to help
prevent the email address from being harvested by spam bots.

auto_link()
===========

Automatically turns URLs and email addresses contained in a string into
links. Example

::

	$string = auto_link($string);

The second parameter determines whether URLs and emails are converted or
just one or the other. Default behavior is both if the parameter is not
specified. Email links are encoded as safe_mailto() as shown above.

Converts only URLs

::

	$string = auto_link($string, 'url');

Converts only Email addresses

::

	$string = auto_link($string, 'email');

The third parameter determines whether links are shown in a new window.
The value can be TRUE or FALSE (boolean)

::

	$string = auto_link($string, 'both', TRUE);

url_title()
===========

Takes a string as input and creates a human-friendly URL string. This is
useful if, for example, you have a blog in which you'd like to use the
title of your entries in the URL. Example

::

	$title = "What's wrong with CSS?";
	$url_title = url_title($title);  // Produces:  Whats-wrong-with-CSS

The second parameter determines the word delimiter. By default dashes
are used. Options are: dash, or underscore

::

	$title = "What's wrong with CSS?";
	$url_title = url_title($title, 'underscore');  // Produces:  Whats_wrong_with_CSS

The third parameter determines whether or not lowercase characters are
forced. By default they are not. Options are boolean TRUE/FALSE

::

	$title = "What's wrong with CSS?";
	$url_title = url_title($title, 'underscore', TRUE);  // Produces:  whats_wrong_with_css

prep_url()
----------

This function will add http:// in the event that a scheme is missing
from a URL. Pass the URL string to the function like this

::

	$url = "example.com";
	$url = prep_url($url);

redirect()
==========

Does a "header redirect" to the URI specified. If you specify the full
site URL that link will be built, but for local links simply providing
the URI segments to the controller you want to direct to will create the
link. The function will build the URL based on your config file values.

The optional second parameter allows you to force a particular redirection
method. The available methods are "location" or "refresh", with location
being faster but less reliable on Windows servers. The default is "auto",
which will attempt to intelligently choose the method based on the server
environment.

The optional third parameter allows you to send a specific HTTP Response
Code - this could be used for example to create 301 redirects for search
engine purposes. The default Response Code is 302. The third parameter is
*only* available with 'location' redirects, and not 'refresh'. Examples::

	if ($logged_in == FALSE)
	{      
		redirect('/login/form/');
	}

	// with 301 redirect
	redirect('/article/13', 'location', 301);

.. note:: In order for this function to work it must be used before anything
	is outputted to the browser since it utilizes server headers.

.. note:: For very fine grained control over headers, you should use the
	`Output Library </libraries/output>` set_header() function.
