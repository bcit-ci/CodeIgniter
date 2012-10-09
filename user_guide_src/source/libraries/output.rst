############
Output Class
############

The Output class is a small class with one main function: To send the
finalized web page to the requesting browser. It is also responsible for
:doc:`caching <../general/caching>` your web pages, if you use that
feature.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

Under normal circumstances you won't even notice the Output class since
it works transparently without your intervention. For example, when you
use the :doc:`Loader <../libraries/loader>` class to load a view file,
it's automatically passed to the Output class, which will be called
automatically by CodeIgniter at the end of system execution.

Stacked Output
==============

One key feature of output manipulation is the ability to divide your
application's output into layers, or a "stack". These layers can be added
and removed, but if left in place, they get collapsed into a single string
when the output is sent to the browser. Each additional layer gets appended
to the output after the previous (lower) layer. This feature allows you to
establish insertion points in your output for adding more data later, or to
easily capture output from an operation (like loading a View) and use it as
needed. One example might be generating the content of an email to be sent.
The following functions let you manage the Output stack if you want to use
that feature:

stack_push('text')
*******************

Adds a new layer to the output stack and returns the new stack level. A string
may be passed as the initial value for the new layer. Subsequent calls to set
or append output will operate on the new layer. For example::

	$this->output->stack_push('Nested output');

stack_level()
*************

Returns the 1-based index of the current stack level, which is effectively the
current number of levels in the stack. Usage example::

	$level = $this->output->stack_level();

stack_pop()
***********

Removes the current (highest) level of the output stack and returns its
contents as a string. The bottom level of the stack can never be removed. If
stack_pop() is called on a one-level stack, the level is emptied and its
contents are returned, but the stack remains at one (empty) level.
Usage example::

	$contents = $this->output->stack_pop();

Manipulating Output
===================

Whether or not you're using additional output layers, you can use the following
functions to handle output if you need to do more than just accumulate Views:

set_output('text', level)
*************************

Permits you to manually set the contents of a specific output level or the
final output string. With only one parameter, the argument will be set as
the contents of the current stack level. Example::

	$this->output->set_output($data);

If you pass a numeric (1-based) stack level as a second parameter, the first
parameter is set as the contents of the specified level. If the level number
given is greater than the current stack level, the content is set to the
highest level instead. Example::

	$this->output->set_output($data, 2);

If you pass boolean TRUE as the second parameter, the entire stack will be
replaced with a single-level stack containing the data provided. Example::

	$this->output->set_output($data, TRUE);

.. important:: If you do set your output manually, it will overwrite any
	output already set or accumulated at the effected level(s)
	(e.g. - by loading Views).

append_output('text', level)
****************************

Appends data onto the current or specified output level. As with set_output(),
if the specified level is invalid, the current level is used. Usage examples::

	$this->output->append_output($data);
	$this->output->append_output($data, 3);

get_output(level)
*****************

Permits you to manually retrieve any output that has been sent for
storage in the output class. For example::

	$string = $this->output->get_output();

If you are using stack levels, you can retrieve the output stored at a specific
level. Example::

	$string = $this->output->get_output(2);

You may also pass boolean TRUE as an argument to retrieve
the entire stack, collapsed as it will be sent to the browser::

	$string = $this->output->get_output(TRUE);

Note that data will only be retrievable from this function if it has
been previously sent to the output class by loading a View or calling
set_output() and/or append_output().

Handling Headers
================

These function allow you to manage the headers that will be delivered with
your output:

set_content_type(type)
**********************

Permits you to set the mime-type of your page so you can serve JSON
data, JPEG's, XML, etc easily.

::

	$this->output
	    ->set_content_type('application/json')
	    ->set_output(json_encode(array('foo' => 'bar')));

	$this->output
	    ->set_content_type('jpeg') // You could also use ".jpeg" which will have the full stop removed before looking in config/mimes.php
	    ->set_output(file_get_contents('files/something.jpg'));

.. important:: Make sure any non-mime string you pass to this method
	exists in config/mimes.php or it will have no effect.

You can also set the character set of the document, by passing a second argument::

	$this->output->set_content_type('css', 'utf-8');

get_content_type()
******************

Returns the Content-Type HTTP header that's currently in use.

	$mime = $this->output->get_content_type();

.. note:: If not set, the default return value is 'text/html'.

set_header('content')
*********************

Permits you to manually set server headers, which the output class will
send for you when outputting the final rendered display. Example::

	$this->output->set_header("HTTP/1.0 200 OK");
	$this->output->set_header("HTTP/1.1 200 OK");
	$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', $last_update).' GMT');
	$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
	$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
	$this->output->set_header("Pragma: no-cache");

set_status_header(code, 'text')
*******************************

Permits you to manually set a server status header. Example::

	$this->output->set_status_header('401');
	// Sets the header as:  Unauthorized

`See here <http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html>`_ for
a full list of headers.

.. note:: This method is an alias for :doc:`Common function <../general/common_funtions.rst>`
	``set_status_header()``.

Profiling
=========

These functions help integrate :doc:`Profiling <../general/profiling>` with
Output:

enable_profiler(enable)
***********************

Permits you to enable/disable the Profiler, which will display benchmark and
other data at the bottom of your pages for debugging and optimization purposes.

To enable the profiler place the following function anywhere within your
:doc:`Controller <../general/controllers>` functions::

	$this->output->enable_profiler(TRUE);

When enabled a report will be generated and inserted at the bottom of
your pages.

To disable the profiler you will use::

	$this->output->enable_profiler(FALSE);

set_profiler_sections()
***********************

Permits you to enable/disable specific sections of the Profiler when enabled.

Content Caching
===============

The CodeIgniter output library also controls caching. For more
information, please see the :doc:`caching
documentation <../general/caching>`.

Parsing Execution Variables
===========================

CodeIgniter will parse the pseudo-variables {elapsed_time} and
{memory_usage} in your output by default. To disable this, set the
$parse_exec_vars class property to FALSE in your controller.
::

	$this->output->parse_exec_vars = FALSE;