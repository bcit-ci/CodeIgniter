####################################
Hooks - Extending the Framework Core
####################################

CodeIgniter's Hooks feature provides a means to tap into and modify the
inner workings of the framework without hacking the core files. When
CodeIgniter runs it follows a specific execution process, diagramed in
the :doc:`Application Flow <../overview/appflow>` page. There may be
instances, however, where you'd like to cause some action to take place
at a particular stage in the execution process. For example, you might
want to run a script right before your controllers get loaded, or right
after, or you might want to trigger one of your own scripts in some
other location.

Enabling Hooks
==============

The hooks feature can be globally enabled/disabled by setting the
following item in the **application/config/config.php** file::

	$config['enable_hooks'] = TRUE;

Defining a Hook
===============

Hooks are defined in the **application/config/hooks.php** file.
Each hook is specified as an array with this prototype::

	$hook['pre_controller'] = array(
		'class'    => 'MyClass',
		'function' => 'Myfunction',
		'filename' => 'Myclass.php',
		'filepath' => 'hooks',
		'params'   => array('beer', 'wine', 'snacks')
	);

**Notes:**

The array index correlates to the name of the particular hook point you
want to use. In the above example the hook point is pre_controller. A
list of hook points is found below. The following items should be
defined in your associative hook array:

-  **class** The name of the class you wish to invoke. If you prefer to
   use a procedural function instead of a class, leave this item blank.
-  **function** The function (or method) name you wish to call.
-  **filename** The file name containing your class/function.
-  **filepath** The name of the directory containing your script.
   Note:
   Your script must be located in a directory INSIDE your *application/*
   directory, so the file path is relative to that directory. For example,
   if your script is located in *application/hooks/*, you will simply use
   'hooks' as your filepath. If your script is located in
   *application/hooks/utilities/* you will use 'hooks/utilities' as your
   filepath. No trailing slash.
-  **params** Any parameters you wish to pass to your script. This item
   is optional.

If you're running PHP 5.3+, you can also use lambda/anoymous functions
(or closures) as hooks, with a simpler syntax::

	$hook['post_controller'] = function()
	{
		/* do something here */
	};

Multiple Calls to the Same Hook
===============================

If want to use the same hook point with more than one script, simply
make your array declaration multi-dimensional, like this::

	$hook['pre_controller'][] = array(
		'class'    => 'MyClass',
		'function' => 'MyMethod',
		'filename' => 'Myclass.php',
		'filepath' => 'hooks',
		'params'   => array('beer', 'wine', 'snacks')
	);

	$hook['pre_controller'][] = array(
		'class'    => 'MyOtherClass',
		'function' => 'MyOtherMethod',
		'filename' => 'Myotherclass.php',
		'filepath' => 'hooks',
		'params'   => array('red', 'yellow', 'blue')
	);

Notice the brackets after each array index::

	$hook['pre_controller'][]

This permits you to have the same hook point with multiple scripts. The
order you define your array will be the execution order.

Hook Points
===========

The following is a list of available hook points.

-  **pre_system**
   Called very early during system execution. Only the benchmark and
   hooks class have been loaded at this point. No routing or other
   processes have happened.
-  **pre_controller**
   Called immediately prior to any of your controllers being called.
   All base classes, routing, and security checks have been done.
-  **post_controller_constructor**
   Called immediately after your controller is instantiated, but prior
   to any method calls happening.
-  **post_controller**
   Called immediately after your controller is fully executed.
-  **display_override**
   Overrides the ``_display()`` method, used to send the finalized page
   to the web browser at the end of system execution. This permits you
   to use your own display methodology. Note that you will need to
   reference the CI superobject with ``$this->CI =& get_instance()`` and
   then the finalized data will be available by calling
   ``$this->CI->output->get_output()``.
-  **cache_override**
   Enables you to call your own method instead of the ``_display_cache()``
   method in the :doc:`Output Library <../libraries/output>`. This permits
   you to use your own cache display mechanism.
-  **post_system**
   Called after the final rendered page is sent to the browser, at the
   end of system execution after the finalized data is sent to the
   browser.