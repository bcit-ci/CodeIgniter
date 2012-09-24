# CodeIgniter System Core #

### Introduction:

As of CodeIgniter 3.0, the core has been adapted to include HMVC support
and a number of related features. HMVC stands for _Heirarchical Model
View Controller_, and in the simplest terms, this means you can call other
controllers from the controller routed by the request.

### Subcontrollers:

The first change that goes with this feature is that the Loader class now
offers a controller() method for loading sub-controllers. This method
accepts a URI-type string to identify what method of which controller to
run, much like you would find in your browser's address bar.

Any routes defined in your routes.php configuration file are applied to the
controller path, and the default "index" method is added if a method name is
not identified. Here are some examples of handing off control to a sub-controller:

	$this->load->controller('subhandler');			// Loads and calls Subhandler->index()
	$this->load->controller('handle/task');			// Loads and calls Handle->task()
	$this->load->controller('weirdness', 'normal');	// Loads Weirdness as 'normal' and calls index()
	$this->load->controller('notyet', '', FALSE);	// Loads Notyet, but does not call a method

In addition, controllers may be loaded out of the application directory or any
paths added with the Loader add_package_path() method. Another new feature is
that package paths may be located in the PHP include directories, if you have
access to them. Paths relative to includes are resolved at the time they are added.

### CodeIgniter Object:

In order to allow more than one controller to be loaded at a time, the heart of
the system had to be separated from the Controller class. As a result, we now
have the CodeIgniter class, from which we create the single, central core of
the application. This is the object to which all other loaded objects are
attached, including controllers.

Now, from inside a controller, you won't be able to tell the difference.
Everything you are used to finding under $this - the core class objects,
libraries you've lodaed, etc. - are all still accessible in exactly the
same way. However, there are a few subtle differences. In reality, your
controller now has a $this->CI member pointing to the CodeIgniter core,
and anything not directly attached to your controller is automatically
searched for there instead. This means it is now infinitessimally slower
to call $this->load (or whatever) than $this->CI->load. With some of the
other performance enhancements in the core, you probably couldn't measure
the difference at runtime, but calling $this->CI is a good practice to adopt
for the future.

### Routed Controller:

There is now a special object name to identify the original controller routed
by a request (in a world where you can load as many controllers as you want).
$this->CI->routed points to the original, which is also attached as its class
name. This allows libraries and other controllers to identify who's in charge.

### Config Files:

In an effort to consolidate the machinations of loading config files, the
Config class now offers two new methods:

	$this->CI->Config->get('myconfig.php', 'config');

This one loads the file (like "myconfig.php") and returns the specified
array (such as $config). Files with the given name located in the config
subdirectory of the application directory and all package paths are all loaded
and their contents merged. This is the method used for all the config files
loaded in the core classes and libraries, with the exception of config.php and
autoload.php, which are singly read out of the application config directory
during system bootstrapping.

This one allows you to get extra variables to go with the main array in a
config file:

	$extras = array();
	$this->CI->Config->get_ext('multiconfig.php', 'myvars', $extras);

In addition to returning the $myvars array from multiconfig.php, the $extras
array in this example will be populated with the names and values of all other
variables encountered in the file(s). This is the variant used to read
database.php, retrieving both the $db array and values like the active group.

### Exceptions:

Custom routes (those URI controller paths) can now be set in routes.php to
direct errors and 404 messages to a controller for display. This gives infinite
flexibility over error messages - far more than just the default templates.

## Bootstrapping and Application Flow:

These new features have necessitated some changes in the bootstrapping process.
To the application's controllers these will be undetectable, but for the
detail-minded, here is the sequence of events:

* Assess environment, paths, and overrides (routing and assign_to_config)
	in index.php and set constants (just as always)
* Define CI version
* Read config.php and autoload.php files from the application path
* Apply assign_to_config overrides if present
* Autoload package paths
* Load CodeIgniter extension class (from application or autoloaded package
	paths) if present
* Instantiate the CodeIgniter object
* Register the exception handler
* Disable magic quotes for PHP < 5.4
* Load **Benchmark** class
* _Mark total execution start time_
* _Mark base class loading start time_
* Load **Config** class and pass the core config items established during
	bootloading (including assign_to_config overrides)
* Read constants.php file(s) from all the application/package paths
* Autoload config files
* Load **Hooks** class
* _Call pre-system hook_
* Load **Loader** class and pass the base and application path lists with
	autoloaded package paths applied
* Load **Utf8** class
* Load **URI** class
* Load **Output** class (to be prepared for 404 output)
* Load **Router** class, set routing, and apply routing overrides
* _Call cache-override hook_, and if not overridden, check for cache
* If a valid cache is found, send it to Output and jump to the
	display-override hook below
* Load **Security** class
* Load **Input** class
* Load **Lang** class
* Load autoload helpers, languages, libraries, drivers, controllers, and models
	(in that order, and don't run controllers)
* _Mark base class loading end time_
* _Call pre-controller hook_
* _Mark controller execution start time_
* Load the routed controller (or 404 if not found)
* _Call post-controller-constructor hook_
* Call routed controller method (or remap) (or 404 if not found)
* **THE CONTROLLER RUNS**
* _Mark controller execution end time_
* _Call post-controller hook_
* _Call display-override hook_, and if not overridden, display output
* _Call post-system hook_

All core classes (now including Log) may be extended by classes with the
configured subclass prefix existing anywhere in the autoloaded package paths.

The Log class no longer reads its own config items directly, but rather is
passed all core config items starting with "log" found in config.php during
bootloading (and subject to assign_to_config). This prevents any dependencies
on other core classes so Log can be loaded at virtually any point in the
process (such as at the end of the CodeIgniter constructor). It also prevents
having to reload the same file that has already been processed.

## Unit Testing

Finally, the new CodeIgniter class, which internalizes the sequence listed
above, is fully accessible and subject to unit testing. An exhaustive test case
has been developed to verify each step and the sequence of events. Furthermore,
all of that is done in isolation without dependence upon the other core classes.

