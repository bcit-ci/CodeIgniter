####################
The CodeIgniter Core
####################

All of the components that are required to run a CodeIgniter application
comprise the core. These pieces are all loaded before control is passed
to your Controller, and several of them help determine which Controller
even gets loaded (or none, in the case of caching). But, at the very center
of the core is the CodeIgniter object.

With the advent of HMVC in CodeIgniter, the Controller could no longer be
the singleton to which all other loaded objects were attached. After all,
HMVC allows you to load more than one Controller. So, that became the role
of the CodeIgniter object, which also encapsulates the bootstrapping process
for your application.

The CodeIgniter class is lightweight, providing a few base-level services and
a run() function to launch your application. Nonetheless, it acts as the
central point of coordination for all the other core classes, Libraries,
Models, and Controllers your application needs in order to do its job.

How do I find the CodeIgniter object?
=====================================

Gaining access to the CodeIgniter object is very easy. From within any
Controller, just reference $this->CI. To access the Config object, you use
$this->CI->config, for the Loader it's $this->CI->load, etc. 

If you are faimiliar with older versions of CodeIgniter, you know that
everything you loaded was directly attached to your Controller and accessible
through $this. You can still access all the loaded components in the same way,
even though they are actually attached to the CodeIgniter object now. So, if
you have an application full of $this->config and $this->load, or if you are
just in the habit of using CodeIgniter that way, don't worry - it still works.
However, do be aware that the newer $this->CI->*object* notation is ever so
slightly faster than the old way.

Views also have access to the CodeIgniter object through $this->CI, even though
they are not actually objects themselves, due to the context in which they are
loaded. Models, on the other hand, do not automatically have $this->CI defined,
but that is easily remedied if you wish.

In the constructor of your Model or custom Library, or anywhere else you need it,
you can get a reference to the CodeIgniter object with get_instance(). Inside an
object, just assign it like so::

	$this->CI = get_instance();

Then you will be able to access CodeIgniter the same way as you can in your
Controller. You can also just assign the return of get_instance() to a local
variable if that is more convenient. Because PHP objects are always handled
by reference, the =& operator is not necessary.

What does the CodeIgniter object do?
====================================

The CodeIgniter object establishes the base framework that is the essence of
CodeIgniter, providing the resources your Controller needs to run. It starts by
reading your config.php and autoload.php files out of the application config/
directory. These two files are critical to setting up that framework,
addressing important decisions such as where to look for core classes, class
extensions, and other config files, as well as whether or not to log messages
along the way. Starting immediately with these files allows even the
CodeIgniter class itself to be extended based on the configured subclass
prefix, and potentially from an autoloaded package path.

CodeIgniter then begins loading all of the necessary core classes through its
own load_core_class() function. During this process, it also calls
:doc:`Hooks <hooks>` to support developer customizations and records
:doc:`Benchmarks <../libraries/benchmark>` for profiling. The specific
sequence of core classes, hooks, and benchmarks is detailed on the
:doc:`Application Flow <../overview/appflow>` page.

Because log message generation is such a common task across all parts of the
system, the CodeIgniter object also provides the log_message() function, which
handles loading the Log class when and if it is needed. If logging is disabled
in your application, CodeIgniter never actually loads Log.

Finally, the CodeIgniter class provides the service of calling Controller
functions through its call_controller() method. Whether it's the Controller
routed by the request URL, or a sub-Controller being called in an
:doc:`HMVC <../overview/hmvc>` operation, this function handles validating the
requested call, invoking the Controller's _remap function if available, and
triggering an appropriate error if there is a failure. As a complement to
call_controller(), the is_callable() function determines whether or not a
class method can be called.

.. _extending-codeigniter:

Extending the CodeIgniter object
================================

If you need to change the behavior of CodeIgniter, you can
:ref:`extend <core-extensions>` the CodeIgniter class, just like the rest of
the core classes. However, if you choose to do so, you must exercise great
caution in order to keep from breaking your application. Remember that most
of the base services provided by this class are critical to getting the core
up and running. Changing them may be fatal.

Probably the most likely reason for extending CodeIgniter is inserting extra
operations during the bootstrapping process. Perhaps you need something to
happen at a point where there isn't already a Hook to leverage for your
purposes. The run() function that handles these operations is structured to
make this scenario relatively easy. It is broken down into the following
subroutines so you can target the part of the process you need to change:

- _load_base() Loads the base-level core classes:

  - Benchmark
  - Config
  - Hooks
  - Loader

  Records two benchmarks:

  - total_execution_time_start
  - loading_time:_base_classes_start

  And calls one hook:

  - pre_system

- _load_routing() Loads the routing core classes:

  - Utf8
  - URI
  - Output
  - Router

  Sets the routing, calls one hook:

  - cache_override

  And calls Output to display the cache, if applicable, in which case
  the application exits

- _load_support() Loads the security and support core classes:

  - Security
  - Input
  - Lang

  Calls Loader to perform configured autoloading, and records one benchmark:

  - loading_time:_base_classes_end

- _run_controller() Loads and runs the routed controller. It records one benchmark:

  - controller_execution_time_( class / method )_start

  And calls two hooks:

  - pre_controller
  - post_controller_constructor

.. note:: The sequence in which these functions run, and indeed the sequence in
	which the core classes are loaded, is very important due to
	interdependencies between the classes. It is *very strongly* recommended
	that you **DO NOT CHANGE** this order of operations.

After run() has completed (or exited), one more function is called from the
CodeIgniter destructor:

- _finalize() Finalizes the application run. It records one benchmark:

  - controller_execution_time_( class / method )_end

  Calls three hooks:

  - post_controller
  - display_override
  - post_system

  And calls Output to send the final output to the browser

Because it is possible for _finalize() to be run when not all of the core
classes have been loaded, it contains safety checks to see if it is possible
to execute all of these steps. Your extension must perform the same kind of
checks if you overload _finalize().

