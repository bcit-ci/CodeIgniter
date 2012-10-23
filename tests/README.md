# CodeIgniter Unit Tests #

Status : [![Build Status](https://secure.travis-ci.org/EllisLab/CodeIgniter.png?branch=develop)](http://travis-ci.org/EllisLab/CodeIgniter)

### Introduction:

This is the preliminary CodeIgniter testing documentation. It
will cover both internal as well as external APIs and the reasoning
behind their implemenation, where appropriate. As with all CodeIgniter
documentation, this file should maintain a mostly human readable
format to facilitate clean api design. [see http://arrenbrecht.ch/testing/]

*First public draft: everything is subject to change*

### Requirements

PHP Unit >= 3.5.6

	pear channel-discover pear.phpunit.de
	pear install phpunit/PHPUnit

vfsStream

	pear channel-discover pear.bovigo.org
	pear install bovigo/vfsStream-beta

#### Installation of PEAR and PHPUnit on Ubuntu

  Installation on Ubuntu requires a few steps. Depending on your setup you may
  need to use 'sudo' to install these. Mileage may vary but these steps are a
  good start.

	# Install the PEAR package
	sudo apt-get install php-pear

	# Add a few sources to PEAR
	pear channel-discover pear.phpunit.de
	pear channel-discover pear.symfony-project.com
	pear channel-discover components.ez.no
	pear channel-discover pear.bovigo.org

	# Finally install PHPUnit and vfsStream (including dependencies)
	pear install --alldeps phpunit/PHPUnit
	pear install --alldeps bovigo/vfsStream-beta

	# Finally, run 'phpunit' from within the ./tests directory
	# and you should be on your way!

## Test Suites:

CodeIgniter bootstraps a request very directly, with very flat class
hierarchy. As a result, there is no main CodeIgniter class until the
controller is instantiated.

This has forced the core classes to be relatively decoupled, which is
a good thing. However, it makes that portion of code relatively hard
to test.

Right now that means we'll probably have two core test suites, along
with a base for application and package tests. That gives us:

1. Bootstrap Test	- test common.php and sanity check codeigniter.php [in planning]
2. System Test		- test core components in relative isolation [in development]
3. Application Test	- bootstrapping for application/tests [not started]
4. Package Test		- bootstrapping for <package>/tests [not started]

### Test Environment:

The test/Bootstrap.php file establishes global constants such as BASEPATH,
APPPATH, and VIEWPATH, initializing them to point to VFS locations. The
test case class employs vfsStream to make a clean virtual filesystem with
the necessary paths for every individual test.

Within each test case, VFS directory objects are available to use as arguments
to the VFS convenience functions (see below):

- ci_vfs_root: VFS filesystem root
- ci_app_root: Application directory
- ci_base_root: System directory
- ci_view_root: Views directory

Classes being instantiated for testing are read from the actual filesystem
by the unit test autoloader, as are mockups created in tests/mocks. If you
need access to the real system directory, the SYSTEM_PATH constant always
points to it.

Any other resources which need to be read from the path constants must be
created or cloned within your test. Functions for doing so are outlined
below.

### CI_TestCase Documentation

Test cases should extend CI_TestCase. This internally extends
PHPUnit\_Framework\_TestCase, so you have access to all of your
usual PHPUnit methods.

We need to provide a simple way to modify the globals and the
common function output. We also need to be able to mock up
the super object as we please.

Current API is *not stable*. Names and implementations will change.

    $this->ci_set_config($key, $val)

Set the global config variables in a mock Config object. If key is an array,
it will replace the entire config array. They are _not_ merged. If called
without any parameters, it will create the mock object but not set any values.
The mock Config object also provides rudimentary item() and load() stubs for
delivering configured values to classes being tested and handling config load
calls, respectively. The load() stub does _not_ actually load any files, it
only records the filename provided. Check the config->loaded array to verify
calls made.

    $this->ci_instance($obj)

Set the object to use as the "super object", in a lot
of cases this will be a simple stdClass with the attributes
you need it to have. If no parameter, will return the instance.

	$this->ci_instance_var($name, $val)

Add an attribute to the super object. This is useful if you
set up a simple instance in setUp and then need to add different
class mockups to your super object.

	$this->ci_core_class($name)

Get the _class name_ of a core class, so that you can instantiate
it. The variable is returned by reference and is tied to the correct
$GLOBALS key. For example:
    
	$cfg =& $this->ci_core_class('cfg'); // returns 'CI_Config'
    $cfg = new $cfg; // instantiates config and overwrites the CFG global

	$this->ci_set_core_class($name, $obj)

An alternative way to set one of the core globals.

	$this->ci_vfs_mkdir($name, $root)

Creates a new directory in the test VFS. Pass a directory object to be the
parent directory or none to create a root-level directory. Returns the new
directory object.

	$this->ci_vfs_create($file, $content, $root, $path)

Creates a new VFS file. '.php' is automatically appended to the filename if
it has no extension. Pass a directory object as the root, and an optional path
to recurse and/or create for containing the file. Path may be a string (such
as 'models/subdir') or an array (e.g. - array('models', 'subdir') ). Existing
directories in the VFS root will be recursed until a new directory is
identified - all others in the path will be created, so you can mix-and-match
old and new directories. If $file is an array (key = name, value = content),
multiple files will be created in the same path.

	$this->ci_vfs_clone($path)

Clones an existing file from the real filesystem to exist in the same path of
the VFS. Path must be relative to the project root (i.e. - starting with
'system' or 'application').

	$this->ci_vfs_path($path, $base)

Creates a VFS file path string suitable for use with PHP file operations. Path
may be absolute from the VFS root, or relative to a base path. It is often
useful to use APPPATH or BASEPATH as the base.

	$this->helper($name)

Loads a helper from the real filesystem.

	$this->lang($name)

Loads a language file from the real filesystem and returns the $lang array.

	$this->ci_get_config()  __internal__

Returns the global config array. Internal as you shouldn't need to
call this (you're setting it, after all). Used internally to make
CI's get_config() work.

	CI_TestCase::instance()  __internal__

Returns an instance of the current test case. We force phpunit to
run with backup-globals enabled, so this will always be the instance
of the currently running test class.

### Going forward

#### 1. Bootstrap Test

Testing common.php should be pretty simple. Include the file, and test the
functions. May require some tweaking so that we can grab the statics from all
methods (see is_loaded()). Testing the actual CodeIgniter.php file will most
likely be an output test for the default view, with some object checking after
the file runs. Needs consideration.

#### 2. System Test

Testing the core system relies on being able to isolate the core components
as much as possible. A few of them access other core classes as globals. These
should be mocked up and easy to manipulate.

All functions in common.php should be a minimal implementation, or and mapped
to a method in the test's parent class to gives us full control of their output.

#### 3. Application Test:

Not sure yet, needs to handle:

- Libraries
- Helpers
- Models
- MY_* files
- Controllers (uh...?)
- Views? (watir, selenium, cucumber?)
- Database Testing

#### 4. Package Test:

I don't have a clue how this will work.

Needs to be able to handle packages
that are used multiple times within the application (i.e. EE/Pyro modules)
as well as packages that are used by multiple applications (library distributions)