##################
Benchmarking Class
##################

CodeIgniter has a Benchmarking class that is always active, enabling the
time difference between any two marked points to be calculated.

.. note:: This class is initialized automatically by the system so there
	is no need to do it manually.

In addition, the benchmark is always started the moment the framework is
invoked, and ended by the output class right before sending the final
view to the browser, enabling a very accurate timing of the entire
system execution to be shown.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

*************************
Using the Benchmark Class
*************************

The Benchmark class can be used within your
:doc:`controllers </general/controllers>`,
:doc:`views </general/views>`, or your :doc:`models </general/models>`.
The process for usage is this:

#. Mark a start point
#. Mark an end point
#. Run the "elapsed time" function to view the results

Here's an example using real code::

	$this->benchmark->mark('code_start');

	// Some code happens here

	$this->benchmark->mark('code_end');

	echo $this->benchmark->elapsed_time('code_start', 'code_end');

.. note:: The words "code_start" and "code_end" are arbitrary. They
	are simply words used to set two markers. You can use any words you
	want, and you can set multiple sets of markers. Consider this example::

		$this->benchmark->mark('dog');

		// Some code happens here

		$this->benchmark->mark('cat');

		// More code happens here

		$this->benchmark->mark('bird');

		echo $this->benchmark->elapsed_time('dog', 'cat');
		echo $this->benchmark->elapsed_time('cat', 'bird');
		echo $this->benchmark->elapsed_time('dog', 'bird');


Profiling Your Benchmark Points
===============================

If you want your benchmark data to be available to the
:doc:`Profiler </general/profiling>` all of your marked points must
be set up in pairs, and each mark point name must end with _start and
_end. Each pair of points must otherwise be named identically. Example::

	$this->benchmark->mark('my_mark_start');

	// Some code happens here...

	$this->benchmark->mark('my_mark_end');

	$this->benchmark->mark('another_mark_start');

	// Some more code happens here...

	$this->benchmark->mark('another_mark_end');

Please read the :doc:`Profiler page </general/profiling>` for more
information.

Displaying Total Execution Time
===============================

If you would like to display the total elapsed time from the moment
CodeIgniter starts to the moment the final output is sent to the
browser, simply place this in one of your view templates::

	<?php echo $this->benchmark->elapsed_time();?>

You'll notice that it's the same function used in the examples above to
calculate the time between two point, except you are **not** using any
parameters. When the parameters are absent, CodeIgniter does not stop
the benchmark until right before the final output is sent to the
browser. It doesn't matter where you use the function call, the timer
will continue to run until the very end.

An alternate way to show your elapsed time in your view files is to use
this pseudo-variable, if you prefer not to use the pure PHP::

	{elapsed_time}

.. note:: If you want to benchmark anything within your controller
	functions you must set your own start/end points.

Displaying Memory Consumption
=============================

If your PHP installation is configured with --enable-memory-limit, you
can display the amount of memory consumed by the entire system using the
following code in one of your view file::

	<?php echo $this->benchmark->memory_usage();?>

.. note:: This function can only be used in your view files. The consumption
	will reflect the total memory used by the entire app.

An alternate way to show your memory usage in your view files is to use
this pseudo-variable, if you prefer not to use the pure PHP::

	{memory_usage}


***************
Class Reference
***************

.. class:: CI_Benchmark

	.. method:: mark($name)

		:param	string	$name: the name you wish to assign to your marker
		:rtype:	void

		Sets a benchmark marker.

	.. method:: elapsed_time([$point1 = ''[, $point2 = ''[, $decimals = 4]]])

		:param	string	$point1: a particular marked point
		:param	string	$point2: a particular marked point
		:param	int	$decimals: number of decimal places for precision
		:returns:	Elapsed time
		:rtype:	string

		Calculates and returns the time difference between two marked points.

		If the first parameter is empty this function instead returns the
		``{elapsed_time}`` pseudo-variable. This permits the full system
		execution time to be shown in a template. The output class will
		swap the real value for this variable.


	.. method:: memory_usage()

		:returns:	Memory usage info
		:rtype:	string

		Simply returns the ``{memory_usage}`` marker.

		This permits it to be put it anywhere in a template without the memory
		being calculated until the end. The :doc:`Output Class <output>` will
		swap the real value for this variable.