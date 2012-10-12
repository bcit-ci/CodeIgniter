############
Loader Class
############

Loader, as the name suggests, is used to load elements. These elements
can be libraries (classes) :doc:`View files <../general/views>`,
:doc:`Drivers <../general/drivers>`,
:doc:`Helpers <../general/helpers>`,
:doc:`Models <../general/models>`, or your own files.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

The following functions are available in this class:

$this->load->library('class_name', $config, 'object name')
===========================================================

This function is used to load core classes. Where class_name is the
name of the class you want to load. Note: We use the terms "class" and
"library" interchangeably.

For example, if you would like to send email with CodeIgniter, the first
step is to load the email class within your controller::

	$this->load->library('email');

Once loaded, the library will be ready for use, using
$this->email->*some_function*().

Library files can be stored in subdirectories within the main
"libraries" folder, or within your personal application/libraries
folder. To load a file located in a subdirectory, simply include the
path, relative to the "libraries" folder. For example, if you have file
located at::

	libraries/flavors/chocolate.php

You will load it using::

	$this->load->library('flavors/chocolate');

You may nest the file in as many subdirectories as you want.

Additionally, multiple libraries can be loaded at the same time by
passing an array of libraries to the load function.

::

	$this->load->library(array('email', 'table'));

Setting options
---------------

The second (optional) parameter allows you to optionally pass
configuration setting. You will typically pass these as an array::

	$config = array (
	                  'mailtype' => 'html',
	                  'charset'  => 'utf-8,
	                  'priority' => '1'
	               );

	$this->load->library('email', $config);

Config options can usually also be set via a config file. Each library
is explained in detail in its own page, so please read the information
regarding each one you would like to use.

Please take note, when multiple libraries are supplied in an array for
the first parameter, each will receive the same parameter information.

Assigning a Library to a different object name
----------------------------------------------

If the third (optional) parameter is blank, the library will usually be
assigned to an object with the same name as the library. For example, if
the library is named Calendar, it will be assigned to a variable named
$this->calendar.

If you prefer to set your own class names you can pass its value to the
third parameter::

	$this->load->library('calendar', '', 'my_calendar');

	// Calendar class is now accessed using:

	$this->my_calendar

Please take note, when multiple libraries are supplied in an array for
the first parameter, this parameter is discarded.

$this->load->driver('parent_name', $config, 'object name')
===========================================================

This function is used to load driver libraries. Where parent_name is the
name of the parent class you want to load.

As an example, if you would like to use sessions with CodeIgniter, the first
step is to load the session driver within your controller::

	$this->load->driver('session');

Once loaded, the library will be ready for use, using
$this->session->*some_function*().

Driver files must be stored in a subdirectory within the main
"libraries" folder, or within your personal application/libraries
folder. The subdirectory must match the parent class name. Read the
:doc:`Drivers <../general/drivers>` description for details.

Additionally, multiple driver libraries can be loaded at the same time by
passing an array of drivers to the load function.

::

	$this->load->driver(array('session', 'cache'));

Setting options
---------------

The second (optional) parameter allows you to optionally pass
configuration settings. You will typically pass these as an array::

	$config = array (
	                  'sess_driver' => 'cookie',
	                  'sess_encrypt_cookie'  => true,
	                  'encryption_key' => 'mysecretkey'
	               );

	$this->load->driver('session', $config);

Config options can usually also be set via a config file. Each library
is explained in detail in its own page, so please read the information
regarding each one you would like to use.

Assigning a Driver to a different object name
----------------------------------------------

If the third (optional) parameter is blank, the library will be assigned
to an object with the same name as the parent class. For example, if
the library is named Session, it will be assigned to a variable named
$this->session.

If you prefer to set your own class names you can pass its value to the
third parameter::

	$this->load->library('session', '', 'my_session');

	// Session class is now accessed using:

	$this->my_session

.. note:: Driver libraries may also be loaded with the library() method,
	but it is faster to use driver()

$this->load->view('file_name', $data, true/false)
==================================================

This function is used to load your View files. If you haven't read the
:doc:`Views <../general/views>` section of the user guide it is
recommended that you do since it shows you how this function is
typically used.

The first parameter is required. It is the name of the view file you
would like to load. Note: The .php file extension does not need to be
specified unless you use something other than .php.

The second **optional** parameter can take an associative array or an
object as input, which it runs through the PHP
`extract <http://www.php.net/extract>`_ function to convert to variables
that can be used in your view files. Again, read the
:doc:`Views <../general/views>` page to learn how this might be useful.

The third **optional** parameter lets you change the behavior of the
function so that it returns data as a string rather than sending it to
your browser. This can be useful if you want to process the data in some
way. If you set the parameter to true (boolean) it will return data. The
default behavior is false, which sends it to your browser. Remember to
assign it to a variable if you want the data returned::

	$string = $this->load->view('myfile', '', true);

$this->load->model('model_name');
==================================

::

	$this->load->model('model_name');


If your model is located in a sub-folder, include the relative path from
your models folder. For example, if you have a model located at
application/models/blog/queries.php you'll load it using::

	$this->load->model('blog/queries');


If you would like your model assigned to a different object name you can
specify it via the second parameter of the loading function::

	$this->load->model('model_name', 'fubar');

	$this->fubar->function();

$this->load->database('options', true/false)
============================================

This function lets you load the database class. The two parameters are
**optional**. Please see the :doc:`database <../database/index>`
section for more info.

$this->load->vars($array)
=========================

This function takes an associative array as input and generates
variables using the PHP `extract <http://www.php.net/extract>`_
function. This function produces the same result as using the second
parameter of the $this->load->view() function above. The reason you
might want to use this function independently is if you would like to
set some global variables in the constructor of your controller and have
them become available in any view file loaded from any function. You can
have multiple calls to this function. The data get cached and merged
into one array for conversion to variables.

$this->load->get_var($key)
===========================

This function checks the associative array of variables available to
your views. This is useful if for any reason a var is set in a library
or another controller method using $this->load->vars().

$this->load->get_vars()
===========================

This function retrieves all variables available to
your views.

$this->load->helper('file_name')
=================================

This function loads helper files, where file_name is the name of the
file, without the _helper.php extension.

$this->load->file('filepath/filename', true/false)
==================================================

This is a generic file loading function. Supply the filepath and name in
the first parameter and it will open and read the file. By default the
data is sent to your browser, just like a View file, but if you set the
second parameter to true (boolean) it will instead return the data as a
string.

$this->load->language('file_name')
===================================

This function is an alias of the :doc:`language loading
function <language>`: $this->lang->load()

$this->load->config('file_name')
=================================

This function is an alias of the :doc:`config file loading
function <config>`: $this->config->load()

Application "Packages"
======================

An application package allows for the easy distribution of complete sets
of resources in a single directory, complete with its own libraries,
models, helpers, config, and language files. It is recommended that
these packages be placed in the application/third_party folder. Below
is a sample map of an package directory

Sample Package "Foo Bar" Directory Map
======================================

The following is an example of a directory for an application package
named "Foo Bar".

::

	/application/third_party/foo_bar

	config/
	helpers/
	language/
	libraries/
	models/

Whatever the purpose of the "Foo Bar" application package, it has its
own config files, helpers, language files, libraries, and models. To use
these resources in your controllers, you first need to tell the Loader
that you are going to be loading resources from a package, by adding the
package path.

$this->load->add_package_path()
---------------------------------

Adding a package path instructs the Loader class to prepend a given path
for subsequent requests for resources. As an example, the "Foo Bar"
application package above has a library named Foo_bar.php. In our
controller, we'd do the following::

	$this->load->add_package_path(APPPATH.'third_party/foo_bar/');
	$this->load->library('foo_bar');

$this->load->remove_package_path()
------------------------------------

When your controller is finished using resources from an application
package, and particularly if you have other application packages you
want to work with, you may wish to remove the package path so the Loader
no longer looks in that folder for resources. To remove the last path
added, simply call the method with no parameters.

$this->load->remove_package_path()
------------------------------------

Or to remove a specific package path, specify the same path previously
given to add_package_path() for a package.::

	$this->load->remove_package_path(APPPATH.'third_party/foo_bar/');

Package view files
------------------

By Default, package view files paths are set when add_package_path()
is called. View paths are looped through, and once a match is
encountered that view is loaded.

In this instance, it is possible for view naming collisions within
packages to occur, and possibly the incorrect package being loaded. To
ensure against this, set an optional second parameter of FALSE when
calling add_package_path().

::

	$this->load->add_package_path(APPPATH.'my_app', FALSE);
	$this->load->view('my_app_index'); // Loads
	$this->load->view('welcome_message'); // Will not load the default welcome_message b/c the second param to add_package_path is FALSE

	// Reset things
	$this->load->remove_package_path(APPPATH.'my_app');

	// Again without the second parameter:
	$this->load->add_package_path(APPPATH.'my_app');
	$this->load->view('my_app_index'); // Loads
	$this->load->view('welcome_message'); // Loads
