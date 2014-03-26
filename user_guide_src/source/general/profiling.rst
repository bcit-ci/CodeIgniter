##########################
Profiling Your Application
##########################

The Profiler Class will display benchmark results, queries you have run,
and ``$_POST`` data at the bottom of your pages. This information can be
useful during development in order to help with debugging and
optimization.

Initializing the Class
======================

.. important:: This class does NOT need to be initialized. It is loaded
	automatically by the :doc:`Output Library <../libraries/output>`
	if profiling is enabled as shown below.

Enabling the Profiler
=====================

To enable the profiler place the following line anywhere within your
:doc:`Controller <controllers>` methods::

	$this->output->enable_profiler(TRUE);

When enabled a report will be generated and inserted at the bottom of
your pages.

To disable the profiler you will use::

	$this->output->enable_profiler(FALSE);

Setting Benchmark Points
========================

In order for the Profiler to compile and display your benchmark data you
must name your mark points using specific syntax.

Please read the information on setting Benchmark points in the
:doc:`Benchmark Library <../libraries/benchmark>` page.

Enabling and Disabling Profiler Sections
========================================

Each section of Profiler data can be enabled or disabled by setting a
corresponding config variable to TRUE or FALSE. This can be done one of
two ways. First, you can set application wide defaults with the
*application/config/profiler.php* config file.

Example::

	$config['config']          = FALSE;
	$config['queries']         = FALSE;

In your controllers, you can override the defaults and config file
values by calling the ``set_profiler_sections()`` method of the
:doc:`Output Library <../libraries/output>`::

	$sections = array(
		'config'  => TRUE,
		'queries' => TRUE
	);

	$this->output->set_profiler_sections($sections);

Available sections and the array key used to access them are described
in the table below.

======================= =================================================================== ========
Key                     Description                                                         Default
======================= =================================================================== ========
**benchmarks**          Elapsed time of Benchmark points and total execution time           TRUE
**config**              CodeIgniter Config variables                                        TRUE
**controller_info**     The Controller class and method requested                           TRUE
**get**                 Any GET data passed in the request                                  TRUE
**http_headers**        The HTTP headers for the current request                            TRUE
**memory_usage**        Amount of memory consumed by the current request, in bytes          TRUE
**post**                Any POST data passed in the request                                 TRUE
**queries**             Listing of all database queries executed, including execution time  TRUE
**uri_string**          The URI of the current request                                      TRUE
**session_data**        Data stored in the current session                                  TRUE
**query_toggle_count**  The number of queries after which the query block will default to   25
                        hidden.
======================= =================================================================== ========

.. note:: Disabling the **save_queries** setting in your database configuration
	will also effectively disable profiling for database queries and render
	the 'queries' setting above useless.