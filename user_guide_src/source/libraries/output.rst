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
automatically by CodeIgniter at the end of system execution. It is
possible, however, for you to manually intervene with the output if you
need to, using either of the two following functions:

$this->output->set_output();
=============================

Permits you to manually set the final output string. Usage example::

	$this->output->set_output($data);

.. important:: If you do set your output manually, it must be the last
	thing done in the function you call it from. For example, if you build a
	page in one of your controller functions, don't set the output until the
	end.

$this->output->set_content_type();
====================================

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

$this->output->get_content_type()
=================================

Returns the Content-Type HTTP header that's currently in use,
excluding the character set value.

	$mime = $this->output->get_content_type();

.. note:: If not set, the default return value is 'text/html'.

$this->output->get_header()
===========================

Gets the requested HTTP header value, if set.

If the header is not set, NULL will be returned.
If an empty value is passed to the method, it will return FALSE.

Example::

	$this->output->set_content_type('text/plain', 'UTF-8');
	echo $this->output->get_header('content-type');
	// Outputs: text/plain; charset=utf-8

.. note:: The header name is compared in a case-insensitive manner.

.. note:: Raw headers sent via PHP's native ``header()`` function are
	also detected.

$this->output->get_output()
===========================

Permits you to manually retrieve any output that has been sent for
storage in the output class. Usage example::

	$string = $this->output->get_output();

Note that data will only be retrievable from this function if it has
been previously sent to the output class by one of the CodeIgniter
functions like $this->load->view().

$this->output->append_output();
================================

Appends data onto the output string. Usage example::

	$this->output->append_output($data);

$this->output->set_header();
=============================

Permits you to manually set server headers, which the output class will
send for you when outputting the final rendered display. Example::

	$this->output->set_header("HTTP/1.0 200 OK");
	$this->output->set_header("HTTP/1.1 200 OK");
	$this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s', $last_update).' GMT');
	$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
	$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
	$this->output->set_header("Pragma: no-cache");

$this->output->set_status_header(code, 'text');
=================================================

Permits you to manually set a server status header. Example::

	$this->output->set_status_header('401');
	// Sets the header as:  Unauthorized

`See here <http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html>`_ for
a full list of headers.

.. note:: This method is an alias for :doc:`Common function <../general/common_functions>`
	``set_status_header()``.

$this->output->enable_profiler();
==================================

Permits you to enable/disable the
:doc:`Profiler <../general/profiling>`, which will display benchmark
and other data at the bottom of your pages for debugging and
optimization purposes.

To enable the profiler place the following function anywhere within your
:doc:`Controller <../general/controllers>` functions::

	$this->output->enable_profiler(TRUE);

When enabled a report will be generated and inserted at the bottom of
your pages.

To disable the profiler you will use::

	$this->output->enable_profiler(FALSE);

$this->output->set_profiler_sections();
=========================================

Permits you to enable/disable specific sections of the Profiler when
enabled. Please refer to the :doc:`Profiler <../general/profiling>`
documentation for further information.

$this->output->cache();
=======================

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

