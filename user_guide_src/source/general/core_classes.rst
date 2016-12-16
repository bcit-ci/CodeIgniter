############################
Creating Core System Classes
############################

Every time CodeIgniter runs there are several base classes that are
initialized automatically as part of the core framework. It is possible,
however, to swap any of the core system classes with your own versions
or even extend the core versions.

**Most users will never have any need to do this, but the option to
replace or extend them does exist for those who would like to
significantly alter the CodeIgniter core.**

.. note:: Messing with a core system class has a lot of implications, so
	make sure you know what you are doing before attempting it.

System Class List
=================

The following is a list of the core system files that are invoked every
time CodeIgniter runs:

-  Benchmark
-  Config
-  Controller
-  Exceptions
-  Hooks
-  Input
-  Language
-  Loader
-  Log
-  Output
-  Router
-  Security
-  URI
-  Utf8

Replacing Core Classes
======================

To use one of your own system classes instead of a default one simply
place your version inside your local *application/core/* directory::

	application/core/some_class.php

If this directory does not exist you can create it.

Any file named identically to one from the list above will be used
instead of the one normally used.

Please note that your class must use CI as a prefix. For example, if
your file is named Input.php the class will be named::

	class CI_Input {

	}

Extending Core Class
====================

If all you need to do is add some functionality to an existing library -
perhaps add a method or two - then it's overkill to replace the entire
library with your version. In this case it's better to simply extend the
class. Extending a class is nearly identical to replacing a class with a
couple exceptions:

-  The class declaration must extend the parent class.
-  Your new class name and filename must be prefixed with MY\_ (this
   item is configurable. See below.).

For example, to extend the native Input class you'll create a file named
application/core/MY_Input.php, and declare your class with::

	class MY_Input extends CI_Input {

	}

.. note:: If you need to use a constructor in your class make sure you
	extend the parent constructor::

		class MY_Input extends CI_Input {

			public function __construct()
			{
				parent::__construct();
			}
		}

**Tip:** Any functions in your class that are named identically to the
methods in the parent class will be used instead of the native ones
(this is known as "method overriding"). This allows you to substantially
alter the CodeIgniter core.

If you are extending the Controller core class, then be sure to extend
your new class in your application controller's constructors.

::

	class Welcome extends MY_Controller {

		public function __construct()
		{
			parent::__construct();
			// Your own constructor code
		}

		public function index()
		{
			$this->load->view('welcome_message');
		}
	}

Setting Your Own Prefix
-----------------------

To set your own sub-class prefix, open your
*application/config/config.php* file and look for this item::

	$config['subclass_prefix'] = 'MY_';

Please note that all native CodeIgniter libraries are prefixed
with CI\_ so DO NOT use that as your prefix.