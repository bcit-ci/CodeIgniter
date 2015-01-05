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

-  CI_Controller
-  Default
-  index

Functions
---------

-  :func:`is_php()`
-  :func:`is_really_writable()`
-  ``load_class()``
-  ``is_loaded()``
-  ``get_config()``
-  :func:`config_item()`
-  :func:`show_error()`
-  :func:`show_404()`
-  :func:`log_message()`
-  :func:`set_status_header()`
-  :func:`get_mimes()`
-  :func:`html_escape()`
-  :func:`remove_invisible_characters()`
-  :func:`is_https()`
-  :func:`function_usable()`
-  :func:`get_instance()`
-  ``_error_handler()``
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
-  MB_ENABLED
-  ICONV_ENABLED
-  UTF8_ENABLED
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
-  EXIT_SUCCESS
-  EXIT_ERROR
-  EXIT_CONFIG
-  EXIT_UNKNOWN_FILE
-  EXIT_UNKNOWN_CLASS
-  EXIT_UNKNOWN_METHOD
-  EXIT_USER_INPUT
-  EXIT_DATABASE
-  EXIT__AUTO_MIN
-  EXIT__AUTO_MAX