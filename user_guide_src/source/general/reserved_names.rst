##############
Reserved Names
##############

In order to help out, CodeIgniter uses a series of functions and names
in its operation. Because of this, some names cannot be used by a
developer. Following is a list of reserved names that cannot be used.

Controller names
----------------

Since your controller classes will extend the main application
controller you must be careful not to name your functions identically to
the ones used by that class, otherwise your local functions will
override them. The following is a list of reserved names. Do not name
your controller any of these:

-  Controller
-  CI_Base
-  _ci_initialize
-  Default
-  index

Functions
---------

-  is_really_writable()
-  load_class()
-  get_config()
-  config_item()
-  show_error()
-  show_404()
-  log_message()
-  _exception_handler()
-  get_instance()

Variables
---------

-  $config
-  $mimes
-  $lang

Constants
---------

-  ENVIRONMENT
-  EXT
-  FCPATH
-  SELF
-  BASEPATH
-  APPPATH
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

