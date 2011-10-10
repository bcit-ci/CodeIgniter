###########
Controllers
###########

Controllers are the heart of your application, as they determine how
HTTP requests should be handled.

.. contents:: Page Contents

What is a Controller?
=====================

**A Controller is simply a class file that is named in a way that can be
associated with a URI.**

Consider this URI::

	example.com/index.php/blog/

In the above example, CodeIgniter would attempt to find a controller
named blog.php and load it.

**When a controller's name matches the first segment of a URI, it will
be loaded.**

Let's try it: Hello World!
==========================

Let's create a simple controller so you can see it in action. Using your
text editor, create a file called blog.php, and put the following code
in it::

	<?php
	class Blog extends CI_Controller {

		public function index()
		{
			echo 'Hello World!';
		}
	}
	?>

Then save the file to your application/controllers/ folder.

Now visit the your site using a URL similar to this::

	example.com/index.php/blog/

If you did it right, you should see Hello World!.

Note: Class names must start with an uppercase letter. In other words,
this is valid::

	<?php
	class Blog extends CI_Controller {

	}
	?>
	

This is **not** valid::

	<?php
	class blog extends CI_Controller {

	}
	?>

Also, always make sure your controller extends the parent controller
class so that it can inherit all its functions.

Functions
=========

In the above example the function name is index(). The "index" function
is always loaded by default if the **second segment** of the URI is
empty. Another way to show your "Hello World" message would be this::

	example.com/index.php/blog/index/

**The second segment of the URI determines which function in the
controller gets called.**

Let's try it. Add a new function to your controller::

	<?php
	class Blog extends CI_Controller {

		public function index()
		{
			echo 'Hello World!';
		}

		public function comments()
		{
			echo 'Look at this!';
		}
	}
	?>

Now load the following URL to see the comment function::

	example.com/index.php/blog/comments/

You should see your new message.

Passing URI Segments to your Functions
======================================

If your URI contains more then two segments they will be passed to your
function as parameters.

For example, lets say you have a URI like this::

	example.com/index.php/products/shoes/sandals/123

Your function will be passed URI segments 3 and 4 ("sandals" and "123")::

	<?php
	class Products extends CI_Controller {

	    public function shoes($sandals, $id)
	    {
	        echo $sandals;
	        echo $id;
	    }
	}
	?>

.. important:: If you are using the :doc:`URI Routing <routing>`
	feature, the segments passed to your function will be the re-routed
	ones.

Defining a Default Controller
=============================

CodeIgniter can be told to load a default controller when a URI is not
present, as will be the case when only your site root URL is requested.
To specify a default controller, open your **application/config/routes.php**
file and set this variable::

	$route['default_controller'] = 'Blog';

Where Blog is the name of the controller class you want used. If you now
load your main index.php file without specifying any URI segments you'll
see your Hello World message by default.

Remapping Function Calls
========================

As noted above, the second segment of the URI typically determines which
function in the controller gets called. CodeIgniter permits you to
override this behavior through the use of the _remap() function::

	public function _remap()
	{
	    // Some code here...
	}

.. important:: If your controller contains a function named _remap(),
	it will **always** get called regardless of what your URI contains. It
	overrides the normal behavior in which the URI determines which function
	is called, allowing you to define your own function routing rules.

The overridden function call (typically the second segment of the URI)
will be passed as a parameter to the _remap() function::

	public function _remap($method)
	{
	    if ($method == 'some_method')
	    {
	        $this->$method();
	    }
	    else
	    {
	        $this->default_method();
	    }
	}

Any extra segments after the method name are passed into _remap() as an
optional second parameter. This array can be used in combination with
PHP's `call_user_func_array <http://php.net/call_user_func_array>`_
to emulate CodeIgniter's default behavior.

::

	public function _remap($method, $params = array())
	{
	    $method = 'process_'.$method;
	    if (method_exists($this, $method))
	    {
	        return call_user_func_array(array($this, $method), $params);
	    }
	    show_404();
	}

Processing Output
=================

CodeIgniter has an output class that takes care of sending your final
rendered data to the web browser automatically. More information on this
can be found in the :doc:`Views <views>` and :doc:`Output class <../libraries/output>` pages. In some cases, however, you
might want to post-process the finalized data in some way and send it to
the browser yourself. CodeIgniter permits you to add a function named
_output() to your controller that will receive the finalized output
data.

.. important:: If your controller contains a function named _output(),
	it will **always** be called by the output class instead of echoing the
	finalized data directly. The first parameter of the function will
	contain the finalized output.

Here is an example::

	public function _output($output)
	{
	    echo $output;
	}

.. note:: Please note that your _output() function will receive the data in its
	finalized state. Benchmark and memory usage data will be rendered, cache
	files written (if you have caching enabled), and headers will be sent
	(if you use that :doc:`feature <../libraries/output>`) before it is
	handed off to the _output() function.
	To have your controller's output cached properly, its _output() method
	can use::

		if ($this->output->cache_expiration > 0)
		{
		    $this->output->_write_cache($output);
		}

	If you are using this feature the page execution timer and memory usage
	stats might not be perfectly accurate since they will not take into
	acccount any further processing you do. For an alternate way to control
	output *before* any of the final processing is done, please see the
	available methods in the :doc:`Output Class <../libraries/output>`.

Private Functions
=================

In some cases you may want certain functions hidden from public access.
To make a function private, simply add an underscore as the name prefix
and it will not be served via a URL request. For example, if you were to
have a function like this::

	private function _utility()
	{
	  // some code
	}

Trying to access it via the URL, like this, will not work::

	example.com/index.php/blog/_utility/

Organizing Your Controllers into Sub-folders
============================================

If you are building a large application you might find it convenient to
organize your controllers into sub-folders. CodeIgniter permits you to
do this.

Simply create folders within your application/controllers directory and
place your controller classes within them.

.. note:: When using this feature the first segment of your URI must
	specify the folder. For example, lets say you have a controller located
	here::

		application/controllers/products/shoes.php

	To call the above controller your URI will look something like this::

		example.com/index.php/products/shoes/show/123

Each of your sub-folders may contain a default controller which will be
called if the URL contains only the sub-folder. Simply name your default
controller as specified in your application/config/routes.php file

CodeIgniter also permits you to remap your URIs using its :doc:`URI
Routing <routing>` feature.

Class Constructors
==================

If you intend to use a constructor in any of your Controllers, you
**MUST** place the following line of code in it::

	parent::__construct();

The reason this line is necessary is because your local constructor will
be overriding the one in the parent controller class so we need to
manually call it.

::

	<?php
	class Blog extends CI_Controller {

	       public function __construct()
	       {
	            parent::__construct();
	            // Your own constructor code
	       }
	}
	?>

Constructors are useful if you need to set some default values, or run a
default process when your class is instantiated. Constructors can't
return a value, but they can do some default work.

Reserved Function Names
=======================

Since your controller classes will extend the main application
controller you must be careful not to name your functions identically to
the ones used by that class, otherwise your local functions will
override them. See :doc:`Reserved Names <reserved_names>` for a full
list.

That's it!
==========

That, in a nutshell, is all there is to know about controllers.
