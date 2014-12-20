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

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

**********************
Application "Packages"
**********************

An application package allows for the easy distribution of complete sets
of resources in a single directory, complete with its own libraries,
models, helpers, config, and language files. It is recommended that
these packages be placed in the application/third_party directory. Below
is a sample map of an package directory.

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
package path via the ``add_package_path()`` method.

Package view files
------------------

By Default, package view files paths are set when ``add_package_path()``
is called. View paths are looped through, and once a match is
encountered that view is loaded.

In this instance, it is possible for view naming collisions within
packages to occur, and possibly the incorrect package being loaded. To
ensure against this, set an optional second parameter of FALSE when
calling ``add_package_path()``.

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

***************
Class Reference
***************

.. class:: CI_Loader

	.. method:: library($library[, $params = NULL[, $object_name = NULL]])

		:param	mixed	$library: Library name as a string or an array with multiple libraries
		:param	array	$params: Optional array of parameters to pass to the loaded library's constructor
		:param	string	$object_name: Optional object name to assign the library to
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		This method is used to load core classes.

		.. note:: We use the terms "class" and "library" interchangeably.

		For example, if you would like to send email with CodeIgniter, the first
		step is to load the email class within your controller::

			$this->load->library('email');

		Once loaded, the library will be ready for use, using ``$this->email``.

		Library files can be stored in subdirectories within the main
		"libraries" directory, or within your personal *application/libraries*
		directory. To load a file located in a subdirectory, simply include the
		path, relative to the "libraries" directory. For example, if you have
		file located at::

			libraries/flavors/Chocolate.php

		You will load it using::

			$this->load->library('flavors/chocolate');

		You may nest the file in as many subdirectories as you want.

		Additionally, multiple libraries can be loaded at the same time by
		passing an array of libraries to the load method.
		::

			$this->load->library(array('email', 'table'));

		**Setting options**

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

		**Assigning a Library to a different object name**

		If the third (optional) parameter is blank, the library will usually be
		assigned to an object with the same name as the library. For example, if
		the library is named Calendar, it will be assigned to a variable named
		``$this->calendar``.

		If you prefer to set your own class names you can pass its value to the
		third parameter::

			$this->load->library('calendar', NULL, 'my_calendar');

			// Calendar class is now accessed using:
			$this->my_calendar

		Please take note, when multiple libraries are supplied in an array for
		the first parameter, this parameter is discarded.

	.. method:: driver($library[, $params = NULL[, $object_name]])

		:param	mixed	$library: Library name as a string or an array with multiple libraries
		:param	array	$params: Optional array of parameters to pass to the loaded library's constructor
		:param	string	$object_name: Optional object name to assign the library to
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		This method is used to load driver libraries, acts very much like the
		``library()`` method.

		As an example, if you would like to use sessions with CodeIgniter, the first
		step is to load the session driver within your controller::

			$this->load->driver('session');

		Once loaded, the library will be ready for use, using ``$this->session``.

		Driver files must be stored in a subdirectory within the main
		"libraries" directory, or within your personal *application/libraries*
		directory. The subdirectory must match the parent class name. Read the
		:doc:`Drivers <../general/drivers>` description for details.

		Additionally, multiple driver libraries can be loaded at the same time by
		passing an array of drivers to the load method.
		::

			$this->load->driver(array('session', 'cache'));

		**Setting options**

		The second (optional) parameter allows you to optionally pass
		configuration settings. You will typically pass these as an array::

			$config = array(
				'sess_driver' => 'cookie',
				'sess_encrypt_cookie'  => true,
				'encryption_key' => 'mysecretkey'
			);

			$this->load->driver('session', $config);

		Config options can usually also be set via a config file. Each library
		is explained in detail in its own page, so please read the information
		regarding each one you would like to use.

		**Assigning a Driver to a different object name**

		If the third (optional) parameter is blank, the library will be assigned
		to an object with the same name as the parent class. For example, if
		the library is named Session, it will be assigned to a variable named
		``$this->session``.

		If you prefer to set your own class names you can pass its value to the
		third parameter::

			$this->load->library('session', '', 'my_session');

			// Session class is now accessed using:
			$this->my_session

	.. method:: view($view[, $vars = array()[, return = FALSE]])

		:param	string	$view: View name
		:param	array	$vars: An associative array of variables
		:param	bool	$return: Whether to return the loaded view
		:returns:	View content string if $return is set to TRUE, otherwise CI_Loader instance (method chaining)
		:rtype:	mixed

		This method is used to load your View files. If you haven't read the
		:doc:`Views <../general/views>` section of the user guide it is
		recommended that you do since it shows you how this method is
		typically used.

		The first parameter is required. It is the name of the view file you
		would like to load.

		.. note:: The .php file extension does not need to be specified unless
			you use something other than .php.

		The second **optional** parameter can take an associative array or an
		object as input, which it runs through the PHP
		`extract() <http://www.php.net/extract>`_ function to convert to variables
		that can be used in your view files. Again, read the
		:doc:`Views <../general/views>` page to learn how this might be useful.

		The third **optional** parameter lets you change the behavior of the
		method so that it returns data as a string rather than sending it to
		your browser. This can be useful if you want to process the data in some
		way. If you set the parameter to TRUE (boolean) it will return data. The
		default behavior is FALSE, which sends it to your browser. Remember to
		assign it to a variable if you want the data returned::

			$string = $this->load->view('myfile', '', TRUE);

	.. method:: vars($vars[, $val = ''])

		:param	mixed	$vars: An array of variables or a single variable name
		:param	mixed	$val: Optional variable value
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		This method takes an associative array as input and generates
		variables using the PHP `extract() <http://www.php.net/extract>`_
		function. This method produces the same result as using the second
		parameter of the ``$this->load->view()`` method above. The reason you
		might want to use this method independently is if you would like to
		set some global variables in the constructor of your controller and have
		them become available in any view file loaded from any method. You can
		have multiple calls to this method. The data get cached and merged
		into one array for conversion to variables.

	.. method:: get_var($key)

		:param	string	$key: Variable name key
		:returns:	Value if key is found, NULL if not
		:rtype:	mixed

		This method checks the associative array of variables available to
		your views. This is useful if for any reason a var is set in a library
		or another controller method using ``$this->load->vars()``.

	.. method:: get_vars()

		:returns:	An array of all assigned view variables
		:rtype:	array

		This method retrieves all variables available to your views.

	.. method:: clear_vars()

		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		Clears cached view variables.

	.. method:: model($model[, $name = ''[, $db_conn = FALSE]])

		:param	mixed	$model: Model name or an array containing multiple models
		:param	string	$name: Optional object name to assign the model to
		:param	string	$db_conn: Optional database configuration group to load
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		::

			$this->load->model('model_name');


		If your model is located in a subdirectory, include the relative path
		from your models directory. For example, if you have a model located at
		*application/models/blog/Queries.php* you'll load it using::

			$this->load->model('blog/queries');

		If you would like your model assigned to a different object name you can
		specify it via the second parameter of the loading method::

			$this->load->model('model_name', 'fubar');
			$this->fubar->method();

	.. method:: database([$params = ''[, $return = FALSE[, $query_builder = NULL]]])

		:param	mixed	$params: Database group name or configuration options
		:param	bool	$return: Whether to return the loaded database object
		:param	bool	$query_builder: Whether to load the Query Builder
		:returns:	Loaded CI_DB instance or FALSE on failure if $return is set to TRUE, otherwise CI_Loader instance (method chaining)
		:rtype:	mixed

		This method lets you load the database class. The two parameters are
		**optional**. Please see the :doc:`database <../database/index>`
		section for more info.

	.. method:: dbforge([$db = NULL[, $return = FALSE]])

		:param	object	$db: Database object
		:param	bool	$return: Whether to return the Database Forge instance
		:returns:	Loaded CI_DB_forge instance if $return is set to TRUE, otherwise CI_Loader instance (method chaining)
		:rtype:	mixed

		Loads the :doc:`Database Forge <../database/forge>` class, please refer
		to that manual for more info.

	.. method:: dbutil([$db = NULL[, $return = FALSE]])

		:param	object	$db: Database object
		:param	bool	$return: Whether to return the Database Utilities instance
		:returns:	Loaded CI_DB_utility instance if $return is set to TRUE, otherwise CI_Loader instance (method chaining)
		:rtype:	mixed

		Loads the :doc:`Database Utilities <../database/utilities>` class, please
		refer to that manual for more info.

	.. method:: helper($helpers)

		:param	mixed	$helpers: Helper name as a string or an array containing multiple helpers
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		This method loads helper files, where file_name is the name of the
		file, without the _helper.php extension.

	.. method:: file($path[, $return = FALSE])

		:param	string	$path: File path
		:param	bool	$return: Whether to return the loaded file
		:returns:	File contents if $return is set to TRUE, otherwise CI_Loader instance (method chaining)
		:rtype:	mixed

		This is a generic file loading method. Supply the filepath and name in
		the first parameter and it will open and read the file. By default the
		data is sent to your browser, just like a View file, but if you set the
		second parameter to boolean TRUE it will instead return the data as a
		string.

	.. method:: language($files[, $lang = ''])

		:param	mixed	$files: Language file name or an array of multiple language files
		:param	string	$lang: Language name
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		This method is an alias of the :doc:`language loading
		method <language>`: ``$this->lang->load()``.

	.. method:: config($file[, $use_sections = FALSE[, $fail_gracefully = FALSE]])

		:param	string	$file: Configuration file name
		:param	bool	$use_sections: Whether configuration values should be loaded into their own section
		:param	bool	$fail_gracefully: Whether to just return FALSE in case of failure
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		This method is an alias of the :doc:`config file loading
		method <config>`: ``$this->config->load()``

	.. method:: is_loaded($class)

		:param	string	$class: Class name
		:returns:	Singleton property name if found, FALSE if not
		:rtype:	mixed

		Allows you to check if a class has already been loaded or not.

		.. note:: The word "class" here refers to libraries and drivers.

		If the requested class has been loaded, the method returns its assigned
		name in the CI Super-object and FALSE if it's not::

			$this->load->library('form_validation');
			$this->load->is_loaded('Form_validation');	// returns 'form_validation'

			$this->load->is_loaded('Nonexistent_library');	// returns FALSE

		.. important:: If you have more than one instance of a class (assigned to
			different properties), then the first one will be returned.

		::

			$this->load->library('form_validation', $config, 'fv');
			$this->load->library('form_validation');

			$this->load->is_loaded('Form_validation');	// returns 'fv'

	.. method:: add_package_path($path[, $view_cascade = TRUE])

		:param	string	$path: Path to add
		:param	bool	$view_cascade: Whether to use cascading views
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		Adding a package path instructs the Loader class to prepend a given path
		for subsequent requests for resources. As an example, the "Foo Bar"
		application package above has a library named Foo_bar.php. In our
		controller, we'd do the following::

			$this->load->add_package_path(APPPATH.'third_party/foo_bar/')
				->library('foo_bar');

	.. method:: remove_package_path([$path = ''])

		:param	string	$path: Path to remove
		:returns:	CI_Loader instance (method chaining)
		:rtype:	CI_Loader

		When your controller is finished using resources from an application
		package, and particularly if you have other application packages you
		want to work with, you may wish to remove the package path so the Loader
		no longer looks in that directory for resources. To remove the last path
		added, simply call the method with no parameters.

		Or to remove a specific package path, specify the same path previously
		given to ``add_package_path()`` for a package.::

			$this->load->remove_package_path(APPPATH.'third_party/foo_bar/');

	.. method:: get_package_paths([$include_base = TRUE])

		:param	bool	$include_base: Whether to include BASEPATH
		:returns:	An array of package paths
		:rtype:	array

		Returns all currently available package paths.