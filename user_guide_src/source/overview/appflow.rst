######################
Application Flow Chart
######################

The following graphic illustrates how data flows throughout the system:

|CodeIgniter application flow|

#. The index.php serves as the front controller, initializing the base
   resources needed to run CodeIgniter.
#. The Router examines the HTTP request to determine what should be done
   with it.
#. If a cache file exists, it is sent directly to the browser, bypassing
   the normal system execution.
#. Security. Before the application controller is loaded, the HTTP
   request and any user submitted data is filtered for security.
#. The Controller loads the model, helpers, and any other resources
   needed to process the specific request. It may also load and run
   sub-Controllers to help handle the task at hand.
#. The finalized View is rendered then sent to the web browser to be
   seen. If caching is enabled, the view is cached first so that on
   subsequent requests it can be served.

For those who want to fully understand the details of the CodeIgniter
application lifecycle, and especially those who wish to modify CodeIgniter's
behavior through :doc:`extensions <../general/core_classes>` and/or
:doc:`hooks <../general/hooks>`, the entire sequence follows:

#. Assess environment, paths, and overrides in index.php and set path constants
#. Define CI version
#. Read config.php and autoload.php files from the application path
#. Apply $assign_to_config overrides if present
#. Autoload package paths
#. Load CodeIgniter extension class if present
#. Instantiate the :doc:`CodeIgniter <../general/codeigniter>` object
#. Register the exception handler
#. Disable magic quotes for PHP < 5.4
#. Load :doc:`Benchmark <../libraries/benchmark>` class
#. *Mark total execution start time*
#. *Mark base class loading start time*
#. Load :doc:`Config <../libraries/config>` class and pass the core config items established during
   bootloading (including $assign_to_config overrides)
#. Read constants.php file(s) from all the application/package paths
#. Autoload config files
#. Load :doc:`Hooks <../general/hooks>` class
#. *Call pre-system hook*
#. Load :doc:`Loader <../libraries/loader>` class and pass the base and application path lists with
   autoloaded package paths applied
#. Load **Utf8** class
#. Load :doc:`URI <../libraries/uri>` class
#. Load :doc:`Output <../libraries/output>` class (to be prepared for 404 output)
#. Load :doc:`Router <../general/routing>` class, set routing, and apply $routing overrides
#. *Call cache-override hook*, and if not overridden, check for cache
#. If a valid cache is found, send it to Output and jump to the
   display-override hook below
#. Load :doc:`Security <../libraries/security>` class
#. Load :doc:`Input <../libraries/input>` class
#. Load :doc:`Lang <../libraries/language>` class
#. Autoload helpers, languages, libraries, drivers, controllers, and models
   (in that order, and don't run controllers)
#. *Mark base class loading end time*
#. *Call pre-controller hook*
#. *Mark controller execution start time*
#. Load the routed controller (or 404 if not found)
#. *Call post-controller-constructor hook*
#. Call routed controller method (or _remap) (or 404 if not found)
#. **THE** :doc:`CONTROLLER <../general/controllers>` **RUNS**
#. *Mark controller execution end time*
#. *Call post-controller hook*
#. *Call display-override hook*, and if not overridden, display output
#. *Call post-system hook*

.. |CodeIgniter application flow| image:: ../images/appflowchart.gif