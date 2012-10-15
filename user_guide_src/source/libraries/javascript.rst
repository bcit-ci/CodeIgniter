.. note:: This driver is experimental. Its feature set and
	implementation may change in future releases.

################
Javascript Class
################

CodeIgniter provides a library to help you with certain common functions
that you may want to use with Javascript. Please note that CodeIgniter
does not require the jQuery library to run, and that any scripting
library will work equally well. The jQuery library is simply presented
as a convenience if you choose to use it.

Initializing the Class
======================

To initialize the Javascript class manually in your controller
constructor, use the $this->load->library function. Currently, the only
available library is jQuery, which will automatically be loaded like
this::

	$this->load->library('javascript');

The Javascript class also accepts parameters, js_library_driver
(string) default 'jquery' and autoload (bool) default TRUE. You may
override the defaults if you wish by sending an associative array::

	$this->load->library('javascript', array('js_library_driver' => 'scripto', 'autoload' => FALSE));

Again, presently only 'jquery' is available. You may wish to set
autoload to FALSE, though, if you do not want the jQuery library to
automatically include a script tag for the main jQuery script file. This
is useful if you are loading it from a location outside of CodeIgniter,
or already have the script tag in your markup.

Once loaded, the jQuery library object will be available using:
$this->javascript

Setup and Configuration
=======================

Set these variables in your view
--------------------------------

As a Javascript library, your files must be available to your
application.

As Javascript is a client side language, the library must be able to
write content into your final output. This generally means a view.
You'll need to include the following variables in the <head> sections of
your output.

::

	<?php echo $library_src;?>
	<?php echo $script_head;?>


$library_src, is where the actual library file will be loaded, as well
as any subsequent plugin script calls; $script_head is where specific
events, functions and other commands will be rendered.

Set the path to the librarys with config items
----------------------------------------------

There are some configuration items in Javascript library. These can
either be set in application/config.php, within its own
config/javascript.php file, or within any controller usings the
set_item() function.

An image to be used as an "ajax loader", or progress indicator. Without
one, the simple text message of "loading" will appear when Ajax calls
need to be made.

::

	$config['javascript_location'] = 'http://localhost/codeigniter/themes/js/jquery/';
	$config['javascript_ajax_img'] = 'images/ajax-loader.gif';

If you keep your files in the same directories they were downloaded
from, then you need not set this configuration items.

The jQuery Class
================

To initialize the jQuery class manually in your controller constructor,
use the $this->load->library function::

	$this->load->library('javascript/jquery');

You may send an optional parameter to determine whether or not a script
tag for the main jQuery file will be automatically included when loading
the library. It will be created by default. To prevent this, load the
library as follows::

	$this->load->library('javascript/jquery', FALSE);

Once loaded, the jQuery library object will be available using:
$this->jquery

jQuery Events
=============

Events are set using the following syntax.

::

	$this->jquery->event('element_path', code_to_run());


In the above example:

-  "event" is any of blur, change, click, dblclick, error, focus, hover,
   keydown, keyup, load, mousedown, mouseup, mouseover, mouseup, resize,
   scroll, or unload.
-  "element_path" is any valid `jQuery
   selector <http://docs.jquery.com/Selectors>`_. Due to jQuery's unique
   selector syntax, this is usually an element id, or CSS selector. For
   example "#notice_area" would effect <div id="notice_area">, and
   "#content a.notice" would effect all anchors with a class of "notice"
   in the div with id "content".
-  "code_to_run()" is script your write yourself, or an action such as
   an effect from the jQuery library below.

Effects
=======

The query library supports a powerful
`Effects <http://docs.jquery.com/Effects>`_ repertoire. Before an effect
can be used, it must be loaded::

	$this->jquery->effect([optional path] plugin name); // for example $this->jquery->effect('bounce');


hide() / show()
---------------

Each of this functions will affect the visibility of an item on your
page. hide() will set an item invisible, show() will reveal it.

::

	$this->jquery->hide(target, optional speed, optional extra information);
	$this->jquery->show(target, optional speed, optional extra information);


-  "target" will be any valid jQuery selector or selectors.
-  "speed" is optional, and is set to either slow, normal, fast, or
   alternatively a number of milliseconds.
-  "extra information" is optional, and could include a callback, or
   other additional information.

toggle()
--------

toggle() will change the visibility of an item to the opposite of its
current state, hiding visible elements, and revealing hidden ones.

::

	$this->jquery->toggle(target);


-  "target" will be any valid jQuery selector or selectors.

animate()
---------

::

	 $this->jquery->animate(target, parameters, optional speed, optional extra information);


-  "target" will be any valid jQuery selector or selectors.
-  "parameters" in jQuery would generally include a series of CSS
   properties that you wish to change.
-  "speed" is optional, and is set to either slow, normal, fast, or
   alternatively a number of milliseconds.
-  "extra information" is optional, and could include a callback, or
   other additional information.

For a full summary, see
`http://docs.jquery.com/Effects/animate <http://docs.jquery.com/Effects/animate>`_

Here is an example of an animate() called on a div with an id of "note",
and triggered by a click using the jQuery library's click() event.

::

	$params = array(
	'height' => 80,
	'width' => '50%',
	'marginLeft' => 125
	);
	$this->jquery->click('#trigger', $this->jquery->animate('#note', $params, 'normal'));

fadeIn() / fadeOut()
--------------------

::

	$this->jquery->fadeIn(target,  optional speed, optional extra information);
	$this->jquery->fadeOut(target,  optional speed, optional extra information);


-  "target" will be any valid jQuery selector or selectors.
-  "speed" is optional, and is set to either slow, normal, fast, or
   alternatively a number of milliseconds.
-  "extra information" is optional, and could include a callback, or
   other additional information.

toggleClass()
-------------

This function will add or remove a CSS class to its target.

::

	$this->jquery->toggleClass(target, class)


-  "target" will be any valid jQuery selector or selectors.
-  "class" is any CSS classname. Note that this class must be defined
   and available in a CSS that is already loaded.

fadeIn() / fadeOut()
--------------------

These effects cause an element(s) to disappear or reappear over time.

::

	$this->jquery->fadeIn(target,  optional speed, optional extra information);
	$this->jquery->fadeOut(target,  optional speed, optional extra information);


-  "target" will be any valid jQuery selector or selectors.
-  "speed" is optional, and is set to either slow, normal, fast, or
   alternatively a number of milliseconds.
-  "extra information" is optional, and could include a callback, or
   other additional information.

slideUp() / slideDown() / slideToggle()
---------------------------------------

These effects cause an element(s) to slide.

::

	$this->jquery->slideUp(target,  optional speed, optional extra information);
	$this->jquery->slideDown(target,  optional speed, optional extra information);
	$this->jquery->slideToggle(target,  optional speed, optional extra information);


-  "target" will be any valid jQuery selector or selectors.
-  "speed" is optional, and is set to either slow, normal, fast, or
   alternatively a number of milliseconds.
-  "extra information" is optional, and could include a callback, or
   other additional information.

Plugins
=======

Some select jQuery plugins are made available using this library.

corner()
--------

Used to add distinct corners to page elements. For full details see
`http://www.malsup.com/jquery/corner/ <http://www.malsup.com/jquery/corner/>`_

::

	$this->jquery->corner(target, corner_style);


-  "target" will be any valid jQuery selector or selectors.
-  "corner_style" is optional, and can be set to any valid style such
   as round, sharp, bevel, bite, dog, etc. Individual corners can be set
   by following the style with a space and using "tl" (top left), "tr"
   (top right), "bl" (bottom left), or "br" (bottom right).

::

	$this->jquery->corner("#note", "cool tl br");


tablesorter()
-------------

description to come

modal()
-------

description to come

calendar()
----------

description to come
