##############
Reserved Names
##############

In order to help out, CodeIgniter uses a series of function, method,
class and variable names in its operation. Because of this, some names
cannot be used by a developer. Following is a list of reserved names
that cannot be used.

Controller names
----------------

Since your controller classes will extend the main application
controller you must be careful not to name your methods identically to
the ones used by that class, otherwise your local methods will
override them. The following is a list of reserved names. Do not name
your controller any of these:

-  Controller
-  CI_Base
-  _ci_initialize
-  Default
-  index

Functions
---------

-  :php:func:`is_really_writable()`
-  ``load_class()``
-  ``get_config()``
-  :php:func:`config_item()`
-  :php:func:`show_error()`
-  :php:func:`show_404()`
-  :php:func:`log_message()`
-  :php:func:`get_mimes()`
-  :php:func:`html_escape()`
-  :php:func:`get_instance()`
-  ``_exception_handler()``
-  ``_stringify_attributes()``

Variables
---------

-  ``$config``
-  ``$db``
-  ``$lang``

Constants
---------

-  ENVIRONMENT
-  FCPATH
-  SELF
-  BASEPATH
-  APPPATH
-  VIEWPATH
-  CI_VERSION
-  FILE_READ_MODE
-  FILE_WRITE_MODE
-  DIR_READ_MODE
-  DIR_WRITE_MODE
-  FOPEN_READ
-  FOPEN_READ_WRITE
-  FOPEN_WRITE_CREATE_DESTRUCTIVE
-  FOPEN_READ_WRITE_CREATE_DESTRUCTIVE
-  FOPEN_WRITE_CREATE
-  FOPEN_READ_WRITE_CREATE
-  FOPEN_WRITE_CREATE_STRICT
-  FOPEN_READ_WRITE_CREATE_STRICT