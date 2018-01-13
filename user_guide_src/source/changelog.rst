##########
Change Log
##########

Version 3.2.0
=============

Release Date: Not Released

-  General Changes

   -  Officially dropped any kind of support for anything under PHP 5.4.8.
   -  Updated Welcome view and HTML error templates with new styling.
   -  Updated configurable directory paths to handle missing trailing ``DIRECTORY_SEPARATOR`` automatically.

-  Core

   -  Removed ``$config['rewrite_short_tags']`` (irrelevant on PHP 5.4+).
   -  Removed previously deprecated ``$config['global_xss_filtering']``.
   -  Removed previously deprecated :doc:`Routing Class <general/routing>` methods ``fetch_directory()``, ``fetch_class()`` and ``fetch_method()`` (use the respective class properties instead).
   -  Removed previously deprecated :doc:`Config Library <libraries/config>` method ``system_url()`` (encourages insecure practices).
   -  Changed :doc:`URI Library <libraries/uri>` to ignore the ``$config['url_suffix']``, ``$config['permitted_uri_chars']`` configuration settings for CLI requests.
   -  Changed :doc:`Loader <libraries/loader>` method ``model()`` to always check if the loaded class extends ``CI_Model``.
   -  Changed :doc:`Output Library <libraries/output>` method ``_display()`` default parameter value to ``NULL`` instead of empty string.

   -  :doc:`Input Library <libraries/input>` changes include:

      - Removed previously deprecated ``$config['allow_get_array']``.
      - Removed previously deprecated ``$config['standardize_newlines']``.
      - Removed previously deprecated method ``is_cli_request()`` (use :php:func:`is_cli()` instead).
      - Changed the ``set_cookie()`` method's default expiry time to 0 (expires when browser is closed).
      - Changed the ``set_cookie()`` method to delete the cookie if a negative expiry time is passed to it.

-  Libraries

   -  Removed previously deprecated *Cart Library*.
   -  Removed previously deprecated *Javascript Library* (it was always experimental in the first place).
   -  Added UNIX socket connection support to :doc:`Session Library <libraries/sessions>` 'redis' driver.

   -  :doc:`Cache Library <libraries/caching>` changes include:

      - Added 'apcu' driver.
      - Added UNIX socket connection support to the 'memcached' driver.
      - Added 'database' configuration option to the 'redis' driver, allowing to auto-select another database.
      - Added method ``get_loaded_driver()`` to return the currently used driver.
      - Changed the 'memcached' driver to ignore configurations that don't specify a hostname.
      - Removed the *socket_type* configuration setting from the 'redis' driver.
      - Changed data serialization logic in 'redis' driver for better performance.

   -  :doc:`Form Validation Library <libraries/form_validation>` changes include:

      - Removed previously deprecated method ``prep_for_form()`` / rule *prep_for_form*.
      - Changed method ``set_rules()`` to throw a ``BadMethodCallException`` when its first parameter is not an array and the ``$rules`` one is unused.
      - Added rule **valid_mac**, which replicates PHP's native ``filter_var()`` with ``FILTER_VALIDATE_MAC``.
      - Added ability to validate entire arrays at once, if ``is_array`` is within the list of rules.
      - Added ability to fetch processed data via a second parameter to ``run()``.

   -  :doc:`HTML Table Library <libraries/table>` changes include:

      - Changed method ``clear()`` to also reset captions.

   -  :doc:`Email Library <libraries/email>` changes include:

      - Changed the default value of the **validate** option to ``TRUE``.
      - Changed the ``send()`` method to always return ``TRUE`` when sending multiple batches of emails.

-  :doc:`Database <database/index>` changes include:

   -  Removed previously deprecated 'sqlite' driver (used for SQLite version 2; no longer shipped with PHP 5.4+).
   -  Removed method ``db_set_charset()`` and the ability to change a connection character set at runtime.
   -  Changed method ``initialize()`` to return void and instead throw a ``RuntimeException`` in case of failure.
   -  Changed method ``db_connect()`` to always set the connection character set (if supported by the driver) and to fail if it can't.

   -  :doc:`Database Forge <database/forge>`:

      - Added support for declaring date/time type fields default values as ``CURRENT_TIMESTAMP`` and similar.

   -  :doc:`Query Builder <database/query_builder>`:

      - Added methods ``having_in()``, ``or_having_in()``, ``not_having_in()``, ``or_not_having_in()``.
      - Updated method ``join()`` to allow accepting ``NATURAL`` clauses in its third parameter.
      - Updated logic to allow dots in alias names.

-  Helpers

   -  Removed previously deprecated *Email Helper* (had only two functions, aliases for PHP's native ``filter_var()`` and ``mail()``).
   -  Removed previously deprecated *Smiley Helper*.
   -  Removed previously deprecated :doc:`Date Helper <helpers/date_helper>` function ``standard_date()`` (use PHP's native ``date()`` instead).
   -  Removed previously deprecated :doc:`Security Helper <helpers/security_helper>` function ``do_hash()`` (use PHP's native ``hash()`` instead).
   -  Removed previously deprecated :doc:`File Helper <helpers/file_helper>` function ``read_file()`` (use PHP's native ``file_get_contents()`` instead).
   -  Added new function :php:func:`ordinal_format()` to :doc:`Inflector Helper <helpers/inflector_helper>`.

   -  :doc:`Download Helper <helpers/download_helper>` changes include:

      - Updated :php:func:`force_download()` to allow existing files to be renamed for download.
      - Updated :php:func:`force_download()` to better utilize available server memory.

   -  :doc:`String Helper <helpers/string_helper>` changes include:

      - Removed previously deprecated function ``trim_slashes()`` (use PHP's native ``trim()`` with ``'/'`` instead).
      - Removed previously deprecated function ``repeater()`` (use PHP's native ``str_repeat()`` instead).

   -  :doc:`HTML Helper <helpers/html_helper>` changes include:

      - Removed previously deprecated function ``br()`` (use PHP's native ``str_repeat()`` with ``'<br />'`` instead).
      - Removed previously deprecated function ``nbs()`` (use PHP's native ``str_repeat()`` with ``'&nbsp;'`` instead).
      - Updated function :php:func:`meta()` with support for "charset" and "property" properties.
      - Changed function :php:func:`doctype()` default document type to HTML 5.

   -  :doc:`Form Helper <helpers/form_helper>` changes include:

      - Removed previously deprecated function ``form_prep()`` (use :php:func:`html_escape()` instead).
      - Removed the second (out of three) parameter from the :php:func:`form_upload()` function (it was never used).

   -  :doc:`CAPTCHA Helper <helpers/captcha_helper>` changes include:

      - Added 'img_alt' option with a default value of 'captcha'.
      - Added ability to generate ``data:image/png;base64`` URIs instead of writing image files to disk.
      - Updated to always create PNG images instead of JPEG.


Version 3.1.8
=============

Release Date: Not Released


Version 3.1.7
=============

Release Date: Jan 13, 2018

-  General Changes

   -  Updated :doc:`Form Validation Library <libraries/form_validation>` rule ``valid_email`` to use ``INTL_IDNA_VARIANT_UTS46`` for non-ASCII domain names.
   -  Updated :doc:`Email Library <libraries/email>` to use ``INTL_IDNA_VARIANT_UTS46`` for non-ASCII domain names.
   -  Updated :doc:`Loader Library <libraries/loader>` method ``model()`` to log both ``CI_Model`` class loading and individual models' initialization.
   -  Updated :doc:`Pagination Library <libraries/pagination>` to preserve previously set attributes while calling ``initialize()``.
   -  Updated :doc:`Cache Library <libraries/caching>` to automatically add items to cache on ``increment()``, ``decrement()`` calls for missing keys.
   -  Deprecated usage of :doc:`CAPTCHA Helper <helpers/captcha_helper>` function :php:func:`create_captcha()` with parameters other than ``$data``.

Bug fixes for 3.1.7
-------------------

-  Fixed a regression (#5276) - :doc:`Database Utilities <database/utilities>` method ``backup()`` generated incorrect ``INSERT`` statements with the 'mysqli' driver.
-  Fixed a regression where :doc:`Database Results <database/results>` method ``field_data()`` returned incorrect type names.
-  Fixed a bug (#5278) - :doc:`URL Helper <helpers/url_helper>` function :php:func:`auto_link()` didn't detect trailing slashes in URLs.
-  Fixed a regression (#5282) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` breaks ``ORDER BY`` clauses for subsequent queries.
-  Fixed a bug (#5279) - :doc:`Query Builder <database/query_builder>` didn't account for already escaped identifiers while applying database name prefixes.
-  Fixed a bug (#5331) - :doc:`URL Helper <helpers/url_helper>` function :php:func:`auto_link()` converted e-mail addresses starting with 'www.' to both "url" and "email" links.
-  Fixed a bug where ``$config['allow_get_array']`` defaulted to ``FALSE`` if it didn't exist in the config file.
-  Fixed a bug (#5379) - :doc:`Session Library <libraries/sessions>` would incorrectly fail to obtain a lock that it already has on PHP 7 with the 'memcached' driver.

Version 3.1.6
=============

Release Date: Sep 25, 2017

-  **Security**

   -  Fixed a potential object injection in :doc:`Cache Library <libraries/caching>` 'apc' driver when ``save()`` is used with ``$raw = TRUE`` (thanks to Tomas Bortoli).

-  General Changes

   -  Deprecated :doc:`Cache Library Library <libraries/caching>` driver 'apc'.
   -  Updated the :doc:`Session Library <libraries/sessions>` 'redis', 'memcached' drivers to reduce the potential of a locking race conditions.


Bug fixes for 3.1.6
-------------------

-  Fixed a bug (#5164) - :doc:`Loader Library <libraries/loader>` method ``library()`` ignored requests to load libraries previously assigned to super-object properties named differently than the library name.
-  Fixed a bug (#5168) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` produced erroneous queries on Microsoft SQL Server when ``ORDER BY`` clauses are cached.
-  Fixed a bug (#5128) - :doc:`Profiler <general/profiling>` didn't wrap ``$_SESSION`` and configuration arrays in ``<pre>`` tags.
-  Fixed a bug (#5183) - :doc:`Database Library <database/index>` method ``is_write_type()`` didn't return TRUE for ``MERGE`` statements.
-  Fixed a bug where :doc:`Image Manipulation Library <libraries/image_lib>` didn't escape image source paths passed to NetPBM as shell arguments.
-  Fixed a bug (#5236) - :doc:`Query Builder <database/query_builder>` methods ``limit()``, ``offset()`` break SQL Server 2005, 2008 queries with ``"<tablename>".*`` in the ``SELECT`` clause.
-  Fixed a bug (#5243) - :doc:`Database Library <database/index>` method ``version()`` didn't work with the 'pdo/dblib' driver.
-  Fixed a bug (#5246) - :doc:`Database transactions <database/transactions>` status wasn't reset unless ``trans_complete()`` was called.
-  Fixed a bug (#5260) - :doc:`Database Utilities <database/utilities>` method ``backup()`` generated incorrect ``INSERT`` statements with the 'mysqli' driver.
-  Fixed a bug where :doc:`Database Results <database/results>` method ``field_data()`` didn't parse field types with the 'mysqli' driver.

Version 3.1.5
=============

Release Date: Jun 19, 2017

-  **Security**

   -  :doc:`Form Validation Library <libraries/form_validation>` rule ``valid_email`` could be bypassed if ``idn_to_ascii()`` is available.

-  General Changes

   -  Updated :doc:`Form Helper <helpers/form_helper>` function :php:func:`form_label()` to accept HTML attributes as a string.

Bug fixes for 3.1.5
-------------------

-  Fixed a bug (#5070) - :doc:`Email Library <libraries/email>` didn't properly detect 7-bit encoding.
-  Fixed a bug (#5084) - :doc:`XML-RPC Library <libraries/xmlrpc>` errored because of a variable name typo.
-  Fixed a bug (#5108) - :doc:`Inflector Helper <helpers/inflector_helper>` function :php:func:`singular()` didn't properly handle 'quizzes'.
-  Fixed a regression (#5131) - private controller methods triggered PHP errors instead of a 404 response.
-  Fixed a bug (#5150) - :doc:`Database Forge <database/forge>` method ``modify_column()`` triggered an error while renaming columns with the 'oci8', 'pdo/oci' drivers.
-  Fixed a bug (#5155) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` returned incorrect result for queries using ``LIMIT``, ``OFFSET``.

Version 3.1.4
=============

Release Date: Mar 20, 2017

-  **Security**

   -  Fixed a header injection vulnerability in :doc:`common function <general/common_functions>` :php:func:`set_status_header()` under Apache (thanks to Guillermo Caminer from `Flowgate <https://flowgate.net/>`_).
   -  Fixed byte-safety issues in :doc:`Encrypt Library <libraries/encrypt>` (DEPRECATED) when ``mbstring.func_overload`` is enabled.
   -  Fixed byte-safety issues in :doc:`Encryption Library <libraries/encryption>` when ``mbstring.func_overload`` is enabled.
   -  Fixed byte-safety issues in :doc:`compatibility functions <general/compatibility_functions>` ``password_hash()``, ``hash_pbkdf2()`` when ``mbstring.func_overload`` is enabled.
   -  Updated :doc:`Encrypt Library <libraries/encrypt>` (DEPRECATED) to call ``mcrypt_create_iv()`` with ``MCRYPT_DEV_URANDOM``.

-  General Changes

   -  Updated the :doc:`Image Manipulation Library <libraries/image_lib>` to work-around an issue with some JPEGs when using GD.

Bug fixes for 3.1.4
-------------------

-  Fixed a regression (#4975) - :doc:`Loader Library <libraries/loader>` couldn't handle objects passed as view variables.
-  Fixed a bug (#4977) - :doc:`Loader Library <libraries/loader>` method ``helper()`` could accept any character as a filename extension separator.
-  Fixed a regression where the :doc:`Session Library <libraries/sessions>` would fail on a ``session_regenerate_id(TRUE)`` call with the 'database' driver.
-  Fixed a bug (#4987) - :doc:`Query Builder <database/query_builder>` caching didn't keep track of table aliases.
-  Fixed a bug where :doc:`Text Helper <helpers/text_helper>` function ``ascii_to_entities()`` wasn't byte-safe when ``mbstring.func_overload`` is enabled.
-  Fixed a bug where ``CI_Log``, ``CI_Output``, ``CI_Email`` and ``CI_Zip`` didn't handle strings in a byte-safe manner when ``mbstring.func_overload`` is enabled.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` didn't read session data in a byte-safe manner when ``mbstring.func_overload`` is enabled.
-  Fixed a bug (#4990) - :doc:`Profiler <general/profiling>` didn't close ``<pre>`` tags it generated.
-  Fixed a bug (#4990) - :doc:`Profiler <general/profiling>` didn't HTML-escape quotes for ``$_SESSION`` variables.
-  Fixed a bug where :doc:`Input Library <libraries/input>` method ``set_cookie()`` didn't allow its *httponly* and *secure* parameters to be overriden to ``FALSE``.
-  Fixed a bug (#5006) - :doc:`common function <general/common_functions>` :php:func:`get_mimes()` didn't load *application/config/mimes.php* if an environment specific config exists.
-  Fixed a bug (#5006) - :doc:`common function <general/common_functions>` :php:func:`remove_invisible_characters()` didn't remove URL-encoded ``0x7F``.
-  Fixed a bug (#4815) - :doc:`Database Library <database/index>` stripped URL-encoded sequences while escaping strings with the 'mssql' driver.
-  Fixed a bug (#5044) - :doc:`HTML Helper <helpers/html_helper>` function :php:func:`img()` didn't accept ``data:`` URI schemes for the image source.
-  Fixed a bug (#5050) - :doc:`Database Library <database/index>` tried to access an undefined property in a number of error handling cases.
-  Fixed a bug (#5057) - :doc:`Database <database/index>` driver 'postgre' didn't actually apply extra options (such as 'connect_timeout') to its DSN.

Version 3.1.3
=============

Release Date: Jan 09, 2017

-  **Security**

   -  Fixed an XSS vulnerability in :doc:`Security Library <libraries/security>` method ``xss_clean()``.
   -  Fixed a possible file inclusion vulnerability in :doc:`Loader Library <libraries/loader>` method ``vars()``.
   -  Fixed a possible remote code execution vulnerability in the :doc:`Email Library <libraries/email>` when 'mail' or 'sendmail' are used (thanks to Paul Buonopane from `NamePros <https://www.namepros.com/>`_).
   -  Added protection against timing side-channel attacks in :doc:`Security Library <libraries/security>` method ``csrf_verify()``.
   -  Added protection against BREACH attacks targeting the CSRF token field generated by :doc:`Form Helper <helpers/form_helper>` function :php:func:`form_open()`.

-  General Changes

   -  Deprecated ``$config['allow_get_array']``.
   -  Deprecated ``$config['standardize_newlines']``.
   -  Deprecated :doc:`Date Helper <helpers/date_helper>` function :php:func:`nice_date()`.

Bug fixes for 3.1.3
-------------------

-  Fixed a bug (#4886) - :doc:`Database Library <database/index>` didn't differentiate bind markers inside double-quoted strings in queries.
-  Fixed a bug (#4890) - :doc:`XML-RPC Library <libraries/xmlrpc>` didn't work on PHP 7.
-  Fixed a regression (#4887) - :doc:`File Uploading Library <libraries/file_uploading>` triggered fatal errors due to numerous PHP distribution channels (XAMPP and cPanel confirmed) explicitly disabling ext/fileinfo by default.
-  Fixed a bug (#4679) - :doc:`Input Library <libraries/input>` method ``ip_address()`` didn't properly resolve ``$config['proxy_ips']`` IPv6 addresses.
-  Fixed a bug (#4902) - :doc:`Image Manipulation Library <libraries/image_lib>` processing via ImageMagick didn't work.
-  Fixed a bug (#4905) - :doc:`Loader Library <libraries/loader>` didn't take into account possible user-provided directory paths when loading helpers.
-  Fixed a bug (#4916) - :doc:`Session Library <libraries/sessions>` with ``sess_match_ip`` enabled was unusable for IPv6 clients when using the 'database' driver on MySQL 5.7.5+.
-  Fixed a bug (#4917) - :doc:`Date Helper <helpers/date_helper>` function :php:func:`nice_date()` didn't handle YYYYMMDD inputs properly.
-  Fixed a bug (#4923) - :doc:`Session Library <libraries/sessions>` could execute an erroneous SQL query with the 'database' driver, if the lock attempt times out.
-  Fixed a bug (#4927) - :doc:`Output Library <libraries/output>` method ``get_header()`` returned the first matching header, regardless of whether it would be replaced by a second ``set_header()`` call.
-  Fixed a bug (#4844) - :doc:`Email Library <libraries/email>` didn't apply ``escapeshellarg()`` to the while passing the Sendmail ``-f`` parameter through ``popen()``.
-  Fixed a bug (#4928) - the bootstrap file didn't check if *config/constants.php* exists before trying to load it.
-  Fixed a bug (#4937) - :doc:`Image Manipulation Library <libraries/image_lib>` method ``initialize()`` didn't translate *new_image* inputs to absolute paths.
-  Fixed a bug (#4941) - :doc:`Query Builder <database/query_builder>` method ``order_by()`` didn't work with 'RANDOM' under the 'pdo/sqlite' driver.
-  Fixed a regression (#4892) - :doc:`Query Builder <database/query_builder>` method ``update_batch()`` didn't properly handle identifier escaping.
-  Fixed a bug (#4953) - :doc:`Database Forge <database/forge>` method ``create_table()`` didn't update an internal tables list cache if it exists but is empty.
-  Fixed a bug (#4958) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` didn't take into account cached ``ORDER BY`` clauses.
-  Fixed a bug (#4804) - :doc:`Query Builder <database/query_builder>` method ``insert_batch()`` could fail if the input array pointer was modified.
-  Fixed a bug (#4962) - :doc:`Database Force <database/forge>` method ``alter_table()`` would fail with the 'oci8' driver.
-  Fixed a bug (#4457) - :doc:`Image Manipulation Library <libraries/image_lib>` method ``get_image_properties()`` didn't detect invalid images.
-  Fixed a bug (#4765) - :doc:`Email Library <libraries/email>` didn't send the ``User-Agent`` header without a prior call to ``clear()``.

Version 3.1.2
=============

Release Date: Oct 28, 2016

-  **Security**

   -  Fixed a number of new vulnerabilities in :doc:`Security Library <libraries/security>` method ``xss_clean()``.

-  General Changes

   -  Allowed PHP 4-style constructors (``Matching_name::Matching_name()`` methods) to be used as routes, if there's a ``__construct()`` to override them.

Bug fixes for 3.1.2
-------------------

-  Fixed a regression (#4874) - :doc:`Session Library <libraries/sessions>` didn't take into account ``session.hash_bits_per_character`` when validating session IDs.
-  Fixed a bug (#4871) - :doc:`Query Builder <database/query_builder>` method ``update_batch()`` didn't properly handle identifier escaping.
-  Fixed a bug (#4884) - :doc:`Query Builder <database/query_builder>` didn't properly parse field names ending in 'is' when used inside WHERE and HAVING statements.
-  Fixed a bug where ``CI_Log``, ``CI_Output``, ``CI_Email`` and ``CI_Zip`` didn't handle strings in a byte-safe manner when ``mbstring.func_overload`` is enabled.

Version 3.1.1
=============

Release Date: Oct 22, 2016

-  **Security**

   -  Fixed a flaw in :doc:`Security Library <libraries/security>` method ``entity_decode()`` (used by ``xss_clean()``) that affects HTML 5 entities when using PHP 5.3.

-  General Changes

   -  Added ``E_PARSE`` to the list of error levels detected by the shutdown handler.
   -  Updated :doc:`Inflector Helper <helpers/inflector_helper>` :php:func:`is_countable()` with more words.
   -  Updated :doc:`common function <general/common_functions>` :php:func:`set_status_header()` with new status codes from IETF RFCs
      `2817 <https://tools.ietf.org/html/rfc2817>`_ (426)
      and `6585 <https://tools.ietf.org/html/rfc6585>`_ (428, 429, 431, 511).

Bug fixes for 3.1.1
-------------------

-  Fixed a bug (#4732) - :doc:`Session Library <libraries/sessions>` triggered errors while writing data for a newly-created sessions with the 'memcached' driver.
-  Fixed a regression (#4736) - :doc:`Image Manipulation Library <libraries/image_lib>` processing via ImageMagick didn't work.
-  Fixed a bug (#4737) - :doc:`Query Builder <database/query_builder>` didn't add an ``OFFSET`` when ``LIMIT`` is zero or unused.
-  Fixed a regression (#4739) - :doc:`Email Library <libraries/email>` doesn't properly separate attachment bodies from headers.
-  Fixed a bug (#4754) - :doc:`Unit Testing Library <libraries/unit_testing>` method ``result()`` didn't translate ``res_datatype``.
-  Fixed a bug (#4759) - :doc:`Form Validation <libraries/form_validation>`, :doc:`Trackback <libraries/trackback>` and :doc:`XML-RPC <libraries/xmlrpc>` libraries treated URI schemes in a case-sensitive manner.
-  Fixed a bug (#4762) - :doc:`Cache Library <libraries/caching>` 'file' driver method ``get_metadata()`` checked TTL time against ``mtime`` instead of the cache item's creation time.
-  Fixed a bug where :doc:`File Uploading Library <libraries/file_uploading>` generated error messages on PHP 7.1.
-  Fixed a bug (#4780) - :doc:`compatibility function <general/compatibility_functions>` ``hex2bin()`` didn't reject inputs of type "resource".
-  Fixed a bug (#4787) - :doc:`Form Validation Library <libraries/form_validation>` method ``valid_email()`` triggered ``E_WARNING`` when input emails have empty domain names.
-  Fixed a bug (#4805) - :doc:`Database <database/index>` driver 'mysqli' didn't use the ``MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT`` flag properly.
-  Fixed a bug (#4808) - :doc:`Database <database/index>` method ``is_write_type()`` only looked at the first line of a queries using ``RETURNING`` with the 'postgre', 'pdo/pgsql', 'odbc' and 'pdo/odbc' drivers.
-  Fixed a bug where :doc:`Query Builder <database/query_builder>` method ``insert_batch()`` tried to execute an unsupported SQL query with the 'ibase' and 'pdo/firebird' drivers.
-  Fixed a bug (#4809) - :doc:`Database <database/index>` driver 'pdo/mysql' didn't turn off ``AUTOCOMMIT`` when starting a transaction.
-  Fixed a bug (#4822) - :doc:`CAPTCHA Helper <helpers/captcha_helper>` didn't clear expired PNG images.
-  Fixed a bug (#4823) - :doc:`Session Library <libraries/sessions>` 'files' driver could enter an infinite loop if ``mbstring.func_overload`` is enabled.
-  Fixed a bug (#4851) - :doc:`Database Forge <database/forge>` didn't quote schema names passed to its ``create_database()`` method.
-  Fixed a bug (#4863) - :doc:`HTML Table Library <libraries/table>` method ``set_caption()`` was missing method chaining support.
-  Fixed a bug (#4843) - :doc:`XML-RPC Library <libraries/xmlrpc>` client class didn't set a read/write socket timeout.
-  Fixed a bug (#4865) - uncaught exceptions didn't set the HTTP Response status code to 500 unless ``display_errors`` was turned On.
-  Fixed a bug (#4830) - :doc:`Session Library <libraries/sessions>` didn't take into account the new session INI settings in PHP 7.1.

Version 3.1.0
=============

Release Date: July 26, 2016

-  **Security**

   -  Fixed an SQL injection in the 'odbc' database driver.
   -  Updated :php:func:`set_realpath()` :doc:`Path Helper <helpers/path_helper>` function to filter-out ``php://`` wrapper inputs.
   -  Officially dropped any kind of support for PHP 5.2.x and anything under 5.3.7.

-  General Changes

   -  Updated :doc:`Image Manipulation Library <libraries/image_lib>` to validate *width* and *height* configuration values.
   -  Updated :doc:`Encryption Library <libraries/encryption>` to always prefer ``random_bytes()`` when it is available.
   -  Updated :doc:`Session Library <libraries/sessions>` to log 'debug' messages when using fallbacks to *session.save_path* (php.ini) or 'sess_use_database', 'sess_table_name' settings.
   -  Added a 'LONGTEXT' to 'STRING' alias to :doc:`Database Forge <database/forge>` for the 'cubrid', 'pdo/cubrid' drivers.
   -  Added 'TINYINT', 'MEDIUMINT', 'INT' and 'BIGINT' aliases to 'NUMBER' to :doc:`Database Forge <database/forge>` for the 'oci8', 'pdo/oci' drivers.

   -  :php:func:`password_hash()` :doc:`compatibility function <general/compatibility_functions>` changes:

      - Changed salt-generation logic to prefer ``random_bytes()`` when it is available.
      - Changed salt-generation logic to prefer direct access to */dev/urandom* over ``openssl_random_pseudo_bytes()``.
      - Changed salt-generation logic to error if ``openssl_random_pseudo_bytes()`` sets its ``$crypto_strong`` flag to FALSE.

Bug fixes for 3.1.0
-------------------

-  Fixed a bug where :doc:`Image Manipulation Library <libraries/image_lib>` didn't escape image source paths passed to ImageMagick as shell arguments.
-  Fixed a bug (#861) - :doc:`Database Forge <database/forge>` method ``create_table()`` incorrectly accepts field width constraints for MSSQL/SQLSRV integer-type columns.
-  Fixed a bug (#4562) - :doc:`Cache Library <libraries/caching>` didn't check if ``Memcached::quit()`` is available before calling it.
-  Fixed a bug (#4563) - :doc:`Input Library <libraries/input>` method ``request_headers()`` ignores ``$xss_clean`` parameter value after first call.
-  Fixed a bug (#4605) - :doc:`Config Library <libraries/config>` method ``site_url()`` stripped trailing slashes from relative URIs passed to it.
-  Fixed a bug (#4613) - :doc:`Email Library <libraries/config>` failed to send multiple emails via SMTP due to "already authenticated" errors when keep-alive is enabled.
-  Fixed a bug (#4633) - :doc:`Form Validation Library <libraries/form_validation>` ignored multiple "callback" rules for empty, non-required fields.
-  Fixed a bug (#4637) - :doc:`Database <database/index>` method ``error()`` returned ``FALSE`` with the 'oci8' driver if there was no error.
-  Fixed a bug (#4647) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` doesn't take into account ``GROUP BY`` clauses while deciding whether to do a subquery or not.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` 'redis' driver didn't properly detect if a connection is properly closed on PHP 5.x.
-  Fixed a bug (#4583) - :doc:`Email Library <libraries/email>` didn't properly handle inline attachments in HTML emails.
-  Fixed a bug where :doc:`Database <database/index>` method ``db_select()`` didn't clear metadata cached for the previously used database.
-  Fixed a bug (#4675) - :doc:`File Helper <helpers/file_helper>` function :php:func:`delete_files()` treated symbolic links as regular directories.
-  Fixed a bug (#4674) - :doc:`Database <database/index>` driver 'dblib' triggered E_WARNING messages while connecting.
-  Fixed a bug (#4678) - :doc:`Database Forge <database/forge>` tried to use unsupported ``IF NOT EXISTS`` clause when creating tables on Oracle.
-  Fixed a bug (#4691) - :doc:`File Uploading Library <libraries/file_uploading>` method ``data()`` returns wrong 'raw_name' when the filename extension is also contained in the raw filename.
-  Fixed a bug (#4679) - :doc:`Input Library <libraries/input>` method ``ip_address()`` errors with a matching ``$config['proxy_ips']`` IPv6 address.
-  Fixed a bug (#4695) - :doc:`User Agent Library <libraries/user_agent>` didn't load the *config/user_agents.php* file when there's no ``User-Agent`` HTTP request header.
-  Fixed a bug (#4713) - :doc:`Query Builder <database/query_builder>` methods ``insert_batch()``, ``update_batch()`` could return wrong affected rows count.
-  Fixed a bug (#4712) - :doc:`Email Library <libraries/email>` doesn't sent ``RSET`` to SMTP servers after a failure and while using keep-alive.
-  Fixed a bug (#4724) - :doc:`Common function <general/common_functions>` :php:func:`is_https()` compared the ``X-Forwarded-Proto`` HTTP header case-sensitively.
-  Fixed a bug (#4725) - :doc:`Common function <general/common_functions>` :php:func:`remove_invisible_characters()` searched case-sensitively for URL-encoded characters.

Version 3.0.6
=============

Release Date: March 21, 2016

-  General Changes

   -  Added a destructor to :doc:`Cache Library <libraries/caching>` 'memcached' driver to ensure that Memcache(d) connections are properly closed.
   -  Deprecated :doc:`Form Validation Library <libraries/form_validation>` method ``prep_for_form()``.

Bug fixes for 3.0.6
-------------------

-  Fixed a bug (#4516) - :doc:`Form Validation Library <libraries/form_validation>` always accepted empty array inputs.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` allowed accessing ``$_SESSION`` values as class properties but ``isset()`` didn't work on them.
-  Fixed a bug where :doc:`Form Validation Library <libraries/form_validation>` modified the ``$_POST`` array when the data being validated was actually provided via ``set_data()``.
-  Fixed a bug (#4539) - :doc:`Migration Library <libraries/migration>` applied migrations before validating that all migrations within the requested version range are valid.
-  Fixed a bug (#4539) - :doc:`Migration Library <libraries/migration>` triggered failures for migrations that are out of the requested version range.

Version 3.0.5
=============

Release Date: March 11, 2016

-  Core

   -  Changed :doc:`Loader Library <libraries/loader>` to allow ``$autoload['drivers']`` assigning with custom property names.
   -  Changed :doc:`Loader Library <libraries/loader>` to ignore variables prefixed with '_ci_' when loading views.

-  General Changes

   -  Updated the :doc:`Session Library <libraries/sessions>` to produce friendlier error messages on failures with drivers other than 'files'.

-  :doc:`Query Builder <database/query_builder>`

   -  Added a ``$batch_size`` parameter to the ``insert_batch()`` method (defaults to 100).
   -  Added a ``$batch_size`` parameter to the ``update_batch()`` method (defaults to 100).

Bug fixes for 3.0.5
-------------------

-  Fixed a bug (#4391) - :doc:`Email Library <libraries/email>` method ``reply_to()`` didn't apply Q-encoding.
-  Fixed a bug (#4384) - :doc:`Pagination Library <libraries/pagination>` ignored (possible) *cur_page* configuration value.
-  Fixed a bug (#4395) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` still fails if an ``ORDER BY`` condition is used.
-  Fixed a bug (#4399) - :doc:`Query Builder <database/query_builder>` methods ``insert_batch()``, ``update_batch()`` produced confusing error messages when called with no data and *db_debug* is enabled.
-  Fixed a bug (#4401) - :doc:`Query Builder <database/query_builder>` breaks ``WHERE`` and ``HAVING`` conditions that use ``IN()`` with strings containing a closing parenthesis.
-  Fixed a regression in :doc:`Form Helper <helpers/form_helper>` functions :php:func:`set_checkbox()`, :php:func:`set_radio()` where "checked" inputs aren't recognized after a form submit.
-  Fixed a bug (#4407) - :doc:`Text Helper <helpers/text_helper>` function :php:func:`word_censor()` doesn't work under PHP 7 if there's no custom replacement provided.
-  Fixed a bug (#4415) - :doc:`Form Validation Library <libraries/form_validation>` rule **valid_url** didn't accept URLs with IPv6 addresses enclosed in square brackets under PHP 5 (upstream bug).
-  Fixed a bug (#4427) - :doc:`CAPTCHA Helper <helpers/captcha_helper>` triggers an error if the provided character pool is too small.
-  Fixed a bug (#4430) - :doc:`File Uploading Library <libraries/file_uploading>` option **file_ext_tolower** didn't work.
-  Fixed a bug (#4431) - :doc:`Query Builder <database/query_builder>` method ``join()`` discarded opening parentheses.
-  Fixed a bug (#4424) - :doc:`Session Library <libraries/sessions>` triggered a PHP warning when writing a newly created session with the 'redis' driver.
-  Fixed a bug (#4437) - :doc:`Inflector Helper <helpers/inflector_helper>` function :php:func:`humanize()` didn't escape its ``$separator`` parameter while using it in a regular expression.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` didn't properly handle its locks' statuses with the 'memcached' driver.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` triggered a PHP warning when writing a newly created session with the 'memcached' driver.
-  Fixed a bug (#4449) - :doc:`Query Builder <database/query_builder>` method ``join()`` breaks conditions containing ``IS NULL``, ``IS NOT NULL``.
-  Fixed a bug (#4491) - :doc:`Session Library <libraries/sessions>` didn't clean-up internal variables for emulated locks with the 'redis' driver.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` didn't clean-up internal variables for emulated locks with the 'memcached' driver.
-  Fixed a bug where :doc:`Database <database/index>` transactions didn't work with the 'ibase' driver.
-  Fixed a bug (#4475) - :doc:`Security Library <libraries/security>` method ``strip_image_tags()`` preserves only the first URL character from non-quoted *src* attributes.
-  Fixed a bug where :doc:`Profiler Library <general/profiling>` didn't apply ``htmlspecialchars()`` to all displayed inputs.
-  Fixed a bug (#4277) - :doc:`Cache Library <libraries/caching>` triggered fatal errors if accessing the Memcache(d) and/or Redis driver and they are not available on the system.
-  Fixed a bug where :doc:`Cache Library <libraries/caching>` method ``is_supported()`` logged an error message when it returns ``FALSE`` for the APC and Wincache drivers.

Version 3.0.4
=============

Release Date: January 13, 2016

-  General Changes

   -  Updated :doc:`Security Library <libraries/security>` method ``get_random_bytes()`` to use PHP 7's ``random_bytes()`` function when possible.
   -  Updated :doc:`Encryption Library <libraries/security>` method ``create_key()`` to use PHP 7's ``random_bytes()`` function when possible.

-  :doc:`Database <database/index>`

   -  Added support for ``OFFSET-FETCH`` with Oracle 12c for the 'oci8' and 'pdo/oci' drivers.
   -  Added support for the new ``MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT`` constant from `PHP 5.6.16 <https://secure.php.net/ChangeLog-5.php#5.6.16>`_ for the 'mysqli' driver.

Bug fixes for 3.0.4
-------------------

-  Fixed a bug (#4212) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` could fail if an ``ORDER BY`` condition is used.
-  Fixed a bug where :doc:`Form Helper <helpers/form_helper>` functions :php:func:`set_checkbox()`, :php:func:`set_radio()` didn't "uncheck" inputs on a submitted form if the default state is "checked".
-  Fixed a bug (#4217) - :doc:`Config Library <libraries/config>` method ``base_url()`` didn't use proper formatting for IPv6 when it falls back to ``$_SERVER['SERVER_ADDR']``.
-  Fixed a bug where :doc:`CAPTCHA Helper <helpers/captcha_helper>` entered an infinite loop while generating a random string.
-  Fixed a bug (#4223) - :doc:`Database <database/index>` method ``simple_query()`` blindly executes queries without checking if the connection was initialized properly.
-  Fixed a bug (#4244) - :doc:`Email Library <libraries/email>` could improperly use "unsafe" US-ASCII characters during Quoted-printable encoding.
-  Fixed a bug (#4245) - :doc:`Database Forge <database/forge>` couldn't properly handle ``SET`` and ``ENUM`` type fields with string values.
-  Fixed a bug (#4283) - :doc:`String Helper <helpers/string_helper>` function :php:func:`alternator()` couldn't be called without arguments.
-  Fixed a bug (#4306) - :doc:`Database <database/index>` method ``version()`` didn't work properly with the 'mssql' driver.
-  Fixed a bug (#4039) - :doc:`Session Library <libraries/sessions>` could generate multiple (redundant) warnings in case of a read failure with the 'files' driver, due to a bug in PHP.
-  Fixed a bug where :doc:`Session Library <libraries/sessions>` didn't have proper error handling on PHP 5 (due to a PHP bug).
-  Fixed a bug (#4312) - :doc:`Form Validation Library <libraries/form_validation>` didn't provide error feedback for failed validation on empty requests.
-  Fixed a bug where :doc:`Database <database/index>` method `version()` returned banner text instead of only the version number with the 'oci8' and 'pdo/oci' drivers.
-  Fixed a bug (#4331) - :doc:`Database <database/index>` method ``error()`` didn't really work for connection errors with the 'mysqli' driver.
-  Fixed a bug (#4343) - :doc:`Email Library <libraries/email>` failing with a *"More than one 'from' person"* message when using *sendmail*.
-  Fixed a bug (#4350) - :doc:`Loader Library <libraries/loader>` method ``model()`` logic directly instantiated the ``CI_Model`` or ``MY_Model`` classes.
-  Fixed a bug (#4337) - :doc:`Database <database/index>` method ``query()`` didn't return a result set for queries with the ``RETURNING`` statement on PostgreSQL.
-  Fixed a bug (#4362) - :doc:`Session Library <libraries/sessions>` doesn't properly maintain its state after ID regeneration with the 'redis' and 'memcached' drivers on PHP 7.
-  Fixed a bug (#4349) - :doc:`Database <database/index>` drivers 'mysql', 'mysqli', 'pdo/mysql' discard other ``sql_mode`` flags when "stricton" is enabled.
-  Fixed a bug (#4349) - :doc:`Database <database/index>` drivers 'mysql', 'mysqli', 'pdo/mysql' don't turn off ``STRICT_TRANS_TABLES`` on MySQL 5.7+ when "stricton" is disabled.
-  Fixed a bug (#4374) - :doc:`Session Library <libraries/sessions>` with the 'database' driver could be affected by userspace :doc:`Query Builder <database/query_builder>` conditions.

Version 3.0.3
=============

Release Date: October 31, 2015

-  **Security**

   -  Fixed an XSS attack vector in :doc:`Security Library <libraries/security>` method ``xss_clean()``.
   -  Changed :doc:`Config Library <libraries/config>` method ``base_url()`` to fallback to ``$_SERVER['SERVER_ADDR']`` when ``$config['base_url']`` is empty in order to avoid *Host* header injections.
   -  Changed :doc:`CAPTCHA Helper <helpers/captcha_helper>` to use the operating system's PRNG when possible.

-  :doc:`Database <database/index>`

   -  Optimized :doc:`Database Utility <database/utilities>` method ``csv_from_result()`` for speed with larger result sets.
   -  Added proper return values to :doc:`Database Transactions <database/transactions>` method ``trans_start()``.

Bug fixes for 3.0.3
-------------------

-  Fixed a bug (#4170) - :doc:`Database <database/index>` method ``insert_id()`` could return an identity from the wrong scope with the 'sqlsrv' driver.
-  Fixed a bug (#4179) - :doc:`Session Library <libraries/sessions>` doesn't properly maintain its state after ID regeneration with the 'database' driver on PHP 7.
-  Fixed a bug (#4173) - :doc:`Database Forge <database/forge>` method ``add_key()`` didn't allow creation of non-PRIMARY composite keys after the "bugfix" for #3968.
-  Fixed a bug (#4171) - :doc:`Database Transactions <database/transactions>` didn't work with nesting in methods ``trans_begin()``, ``trans_commit()``, ``trans_rollback()``.
-  Fixed a bug where :doc:`Database Transaction <database/transactions>` methods ``trans_begin()``, ``trans_commit()``, ``trans_rollback()`` ignored failures.
-  Fixed a bug where all :doc:`Database Transaction <database/transactions>` methods returned TRUE while transactions are actually disabled.
-  Fixed a bug where :doc:`common function <general/common_functions>` :php:func:`html_escape()` modified keys of its array inputs.
-  Fixed a bug (#4192) - :doc:`Email Library <libraries/email>` wouldn't always have proper Quoted-printable encoding due to a bug in PHP's own ``mb_mime_encodeheader()`` function.

Version 3.0.2
=============

Release Date: October 8, 2015

-  **Security**

   -  Fixed a number of XSS attack vectors in :doc:`Security Library <libraries/security>` method ``xss_clean()``  (thanks to Frans Rosén from `Detectify <https://detectify.com/>`_).

-  General Changes

   -  Updated the *application/config/constants.php* file to check if constants aren't already defined before doing that.
   -  Changed :doc:`Loader Library <libraries/loader>` method ``model()`` to only apply ``ucfirst()`` and not ``strtolower()`` to the requested class name.
   -  Changed :doc:`Config Library <libraries/config>` methods ``base_url()``, ``site_url()`` to allow protocol-relative URLs by passing an empty string as the protocol.

Bug fixes for 3.0.2
-------------------

-  Fixed a bug (#2284) - :doc:`Database <database/index>` method ``protect_identifiers()`` breaks when :doc:`Query Builder <database/query_builder>` isn't enabled.
-  Fixed a bug (#4052) - :doc:`Routing <general/routing>` with anonymous functions didn't work for routes that don't use regular expressions.
-  Fixed a bug (#4056) - :doc:`Input Library <libraries/input>` method ``get_request_header()`` could not return a value unless ``request_headers()`` was called beforehand.
-  Fixed a bug where the :doc:`Database Class <database/index>` entered an endless loop if it fails to connect with the 'sqlsrv' driver.
-  Fixed a bug (#4065) - :doc:`Database <database/index>` method ``protect_identifiers()`` treats a traling space as an alias separator if the input doesn't contain ' AS '.
-  Fixed a bug (#4066) - :doc:`Cache Library <libraries/caching>` couldn't fallback to a backup driver if the primary one is Memcache(d) or Redis.
-  Fixed a bug (#4073) - :doc:`Email Library <libraries/email>` method ``send()`` could return TRUE in case of an actual failure when an SMTP command fails.
-  Fixed a bug (#4086) - :doc:`Query Builder <database/query_builder>` didn't apply *dbprefix* to LIKE conditions if the pattern included spaces.
-  Fixed a bug (#4091) - :doc:`Cache Library <libraries/caching>` 'file' driver could be tricked into accepting empty cache item IDs.
-  Fixed a bug (#4093) - :doc:`Query Builder <database/query_builder>` modified string values containing 'AND', 'OR' while compiling WHERE conditions.
-  Fixed a bug (#4096) - :doc:`Query Builder <database/query_builder>` didn't apply *dbprefix* when compiling BETWEEN conditions.
-  Fixed a bug (#4105) - :doc:`Form Validation Library <libraries/form_validation>` didn't allow pipe characters inside "bracket parameters" when using a string ruleset.
-  Fixed a bug (#4109) - :doc:`Routing <general/routing>` to *default_controller* didn't work when *enable_query_strings* is set to TRUE.
-  Fixed a bug (#4044) - :doc:`Cache Library <libraries/caching>` 'redis' driver didn't catch ``RedisException`` that could be thrown during authentication.
-  Fixed a bug (#4120) - :doc:`Database <database/index>` method ``error()`` didn't return error info when called after ``query()`` with the 'mssql' driver.
-  Fixed a bug (#4116) - :doc:`Pagination Library <libraries/pagination>` set the wrong page number on the "data-ci-pagination-page" attribute in generated links.
-  Fixed a bug where :doc:`Pagination Library <libraries/pagination>` added the 'rel="start"' attribute to the first displayed link even if it's not actually linking the first page.
-  Fixed a bug (#4137) - :doc:`Error Handling <general/errors>` breaks for the new ``Error`` exceptions under PHP 7.
-  Fixed a bug (#4126) - :doc:`Form Validation Library <libraries/form_validation>` method ``reset_validation()`` discarded validation rules from config files.

Version 3.0.1
=============

Release Date: August 7, 2015

-  Core

   -  Added DoS mitigation to :php:func:`hash_pbkdf2()` :doc:`compatibility function <general/compatibility_functions>`.

-  Database

   -  Added ``list_fields()`` support for SQLite ('sqlite3' and 'pdo_sqlite' drivers).
   -  Added SSL connection support for the 'mysqli' and 'pdo_mysql' drivers.

-  Libraries

   -  :doc:`File Uploading Library <libraries/file_uploading>` changes:

      - Changed method ``set_error()`` to accept a custom log level (defaults to 'error').
      - Errors "no_file_selected", "file_partial", "stopped_by_extension", "no_file_types", "invalid_filetype", "bad_filename" are now logged at the 'debug' level.
      - Errors "file_exceeds_limit", "file_exceeds_form_limit", "invalid_filesize", "invalid_dimensions" are now logged at the 'info' level.

   -  Added 'is_resource' to the available expectations in :doc:`Unit Testing Library <libraries/unit_testing>`.

-  Helpers

   -  Added Unicode support to :doc:`URL Helper <helpers/url_helper>` function :php:func:`url_title()`.
   -  Added support for passing the "extra" parameter as an array to all :doc:`Form Helper <helpers/form_helper>` functions that use it.

-  Core

   -  Added support for defining a list of specific query parameters in ``$config['cache_query_string']`` for the :doc:`Output Library <libraries/output>`.
   -  Added class existence and inheritance checks to ``CI_Loader::model()`` in order to ease debugging in case of name collisions.

Bug fixes for 3.0.1
-------------------

-  Fixed a bug (#3733) - Autoloading of libraries with aliases didn't work, although it was advertised to.
-  Fixed a bug (#3744) - Redis :doc:`Caching <libraries/caching>` driver didn't handle authentication failures properly.
-  Fixed a bug (#3761) - :doc:`URL Helper <helpers/url_helper>` function :php:func:`anchor()` didn't work with array inputs.
-  Fixed a bug (#3773) - ``db_select()`` didn't work for MySQL with the PDO :doc:`Database <database/index>` driver.
-  Fixed a bug (#3771) - :doc:`Form Validation Library <libraries/form_validation>` was looking for a 'form_validation\_' prefix when trying to translate field name labels.
-  Fixed a bug (#3787) - :doc:`FTP Library <libraries/ftp>` method ``delete_dir()`` failed when the target has subdirectories.
-  Fixed a bug (#3801) - :doc:`Output Library <libraries/output>` method ``_display_cache()`` incorrectly looked for the last modified time of a directory instead of the cache file.
-  Fixed a bug (#3816) - :doc:`Form Validation Library <libraries/form_validation>` treated empty string values as non-existing ones.
-  Fixed a bug (#3823) - :doc:`Session Library <libraries/sessions>` drivers Redis and Memcached didn't properly handle locks that are blocking the request for more than 30 seconds.
-  Fixed a bug (#3846) - :doc:`Image Manipulation Library <libraries/image_lib>` method `image_mirror_gd()` didn't properly initialize its variables.
-  Fixed a bug (#3854) - `field_data()` didn't work properly with the Oracle (OCI8) database driver.
-  Fixed a bug in the :doc:`Database Utility Class <database/utilities>` method ``csv_from_result()`` didn't work with a whitespace CSV delimiter.
-  Fixed a bug (#3890) - :doc:`Input Library <libraries/input>` method ``get_request_header()`` treated header names as case-sensitive.
-  Fixed a bug (#3903) - :doc:`Form Validation Library <libraries/form_validation>` ignored "unnamed" closure validation rules.
-  Fixed a bug (#3904) - :doc:`Form Validation Library <libraries/form_validation>` ignored "named" callback rules when the field is empty and there's no 'required' rule.
-  Fixed a bug (#3922) - :doc:`Email <libraries/email>` and :doc:`XML-RPC <libraries/xmlrpc>` libraries could enter an infinite loop due to `PHP bug #39598 <https://bugs.php.net/bug.php?id=39598>`_.
-  Fixed a bug (#3913) - :doc:`Cache Library <libraries/caching>` didn't work with the direct ``$this->cache->$driver_name->method()`` syntax with Redis and Memcache(d).
-  Fixed a bug (#3932) - :doc:`Query Builder <database/query_builder>` didn't properly compile WHERE and HAVING conditions for field names that end with "and", "or".
-  Fixed a bug in :doc:`Query Builder <database/query_builder>` where ``delete()`` didn't properly work on multiple tables with a WHERE condition previously set via ``where()``.
-  Fixed a bug (#3952) - :doc:`Database <database/index>` method ``list_fields()`` didn't work with SQLite3.
-  Fixed a bug (#3955) - :doc:`Cache Library <libraries/caching>` methods ``increment()`` and ``decrement()`` ignored the 'key_prefix' setting.
-  Fixed a bug (#3963) - :doc:`Unit Testing Library <libraries/unit_testing>` wrongly tried to translate filenames, line numbers and notes values in test results.
-  Fixed a bug (#3965) - :doc:`File Uploading Library <libraries/file_uploading>` ignored the "encrypt_name" setting when "overwrite" is enabled.
-  Fixed a bug (#3968) - :doc:`Database Forge <database/forge>` method ``add_key()`` didn't treat array inputs as composite keys unless it's a PRIMARY KEY.
-  Fixed a bug (#3715) - :doc:`Pagination Library <libraries/pagination>` could generate broken link when a protocol-relative base URL is used.
-  Fixed a bug (#3828) - :doc:`Output Library <libraries/output>` method ``delete_cache()`` couldn't delete index page caches.
-  Fixed a bug (#3704) - :doc:`Database <database/index>` method ``stored_procedure()`` in the 'oci8' driver didn't properly bind parameters.
-  Fixed a bug (#3778) - :doc:`Download Helper <helpers/download_helper>` function :php:func:`force_download()` incorrectly sent a *Pragma* response header.
-  Fixed a bug (#3752) - ``$routing['directory']`` overrides were not properly handled and always resulted in a 404 "Not Found" error.
-  Fixed a bug (#3279) - :doc:`Query Builder <database/query_builder>` methods ``update()`` and ``get_compiled_update()`` did double escaping on the table name if it was provided via ``from()``.
-  Fixed a bug (#3991) - ``$config['rewrite_short_tags']`` never worked due to ``function_exists('eval')`` always returning FALSE.
-  Fixed a bug where the :doc:`File Uploading Library <libraries/file_uploading>` library will not properly configure its maximum file size unless the input value is of type integer.
-  Fixed a bug (#4000) - :doc:`Pagination Library <libraries/pagination>` didn't enable "rel" attributes by default if no attributes-related config options were used.
-  Fixed a bug (#4004) - :doc:`URI Class <libraries/uri>` didn't properly parse the request URI if it contains a colon followed by a digit.
-  Fixed a bug in :doc:`Query Builder <database/query_builder>` where the ``$escape`` parameter for some methods only affected field names.
-  Fixed a bug (#4012) - :doc:`Query Builder <database/query_builder>` methods ``where_in()``, ``or_where_in()``, ``where_not_in()``, ``or_where_not_in()`` didn't take into account previously cached WHERE conditions when query cache is in use.
-  Fixed a bug (#4015) - :doc:`Email Library <libraries/email>` method ``set_header()`` didn't support method chaining, although it was advertised.
-  Fixed a bug (#4027) - :doc:`Routing <general/routing>` with HTTP verbs only worked if the route request method was declared in all-lowercase letters.
-  Fixed a bug (#4026) - :doc:`Database Transactions <database/transactions>` always rollback if any previous ``query()`` call fails.
-  Fixed a bug (#4023) - :doc:`String Helper <helpers/string_helper>` function ``increment_string()`` didn't escape its ``$separator`` parameter.

Version 3.0.0
=============

Release Date: March 30, 2015

-  License

   -  CodeIgniter has been relicensed with the `MIT License <http://opensource.org/licenses/MIT>`_, eliminating its old proprietary licensing.

-  General Changes

   -  PHP 5.1.6 is no longer supported. CodeIgniter now requires PHP 5.2.4 and recommends PHP 5.4+ or newer to be used.
   -  Changed filenaming convention (class file names now must be Ucfirst and everything else in lowercase).
   -  Changed the default database driver to 'mysqli' (the old 'mysql' driver is DEPRECATED).
   -  ``$_SERVER['CI_ENV']`` can now be set to control the ``ENVIRONMENT`` constant.
   -  Added an optional backtrace to php-error template.
   -  Added Android to the list of user agents.
   -  Added Windows 7, Windows 8, Windows 8.1, Android, Blackberry, iOS and PlayStation 3 to the list of user platforms.
   -  Added Fennec (Firefox for mobile) to the list of mobile user agents.
   -  Ability to log certain error types, not all under a threshold.
   -  Added support for pem, p10, p12, p7a, p7c, p7m, p7r, p7s, crt, crl, der, kdb, rsa, cer, sst, csr Certs to mimes.php.
   -  Added support for pgp, gpg, zsh and cdr files to mimes.php.
   -  Added support for 3gp, 3g2, mp4, wmv, f4v, vlc Video files to mimes.php.
   -  Added support for m4a, aac, m4u, xspf, au, ac3, flac, ogg, wma Audio files to mimes.php.
   -  Added support for kmz and kml (Google Earth) files to mimes.php.
   -  Added support for ics Calendar files to mimes.php.
   -  Added support for rar, jar and 7zip archives to mimes.php.
   -  Updated support for xml ('application/xml') and xsl ('application/xml', 'text/xsl') files in mimes.php.
   -  Updated support for doc files in mimes.php.
   -  Updated support for docx files in mimes.php.
   -  Updated support for php files in mimes.php.
   -  Updated support for zip files in mimes.php.
   -  Updated support for csv files in mimes.php.
   -  Added Romanian, Greek, Vietnamese and Cyrilic characters in *application/config/foreign_characters.php*.
   -  Changed logger to only chmod when file is first created.
   -  Removed previously deprecated SHA1 Library.
   -  Removed previously deprecated use of ``$autoload['core']`` in *application/config/autoload.php*.
      Only entries in ``$autoload['libraries']`` are auto-loaded now.
   -  Removed previously deprecated EXT constant.
   -  Updated all classes to be written in PHP 5 style, with visibility declarations and no ``var`` usage for properties.
   -  Added an Exception handler.
   -  Moved error templates to *application/views/errors/* and made the path configurable via ``$config['error_views_path']``.
   -  Added support non-HTML error templates for CLI applications.
   -  Moved the Log class to *application/core/*
   -  Global config files are loaded first, then environment ones. Environment config keys overwrite base ones, allowing to only set the keys we want changed per environment.
   -  Changed detection of ``$view_folder`` so that if it's not found in the current path, it will now also be searched for under the application folder.
   -  Path constants BASEPATH, APPPATH and VIEWPATH are now (internally) defined as absolute paths.
   -  Updated email validation methods to use ``filter_var()`` instead of PCRE.
   -  Changed environment defaults to report all errors in *development* and only fatal ones in *testing*, *production* but only display them in *development*.
   -  Updated *ip_address* database field lengths from 16 to 45 for supporting IPv6 address on :doc:`Trackback Library <libraries/trackback>` and :doc:`Captcha Helper <helpers/captcha_helper>`.
   -  Removed *cheatsheets* and *quick_reference* PDFs from the documentation.
   -  Added availability checks where usage of dangerous functions like ``eval()`` and ``exec()`` is required.
   -  Added support for changing the file extension of log files using ``$config['log_file_extension']``.
   -  Added support for turning newline standardization on/off via ``$config['standardize_newlines']`` and set it to FALSE by default.
   -  Added configuration setting ``$config['composer_autoload']`` to enable loading of a `Composer <https://getcomposer.org>`_ auto-loader.
   -  Removed the automatic conversion of 'programmatic characters' to HTML entities from the :doc:`URI Library <libraries/uri>`.
   -  Changed log messages that say a class or file was loaded to "info" level instead of "debug", so that they don't pollute log files when ``$config['log_threshold']`` is set to 2 (debug).

-  Helpers

   -  :doc:`Date Helper <helpers/date_helper>` changes include:

      - Added an optional third parameter to :php:func:`timespan()` that constrains the number of time units displayed.
      - Added an optional parameter to :php:func:`timezone_menu()` that allows more attributes to be added to the generated select tag.
      - Added function :php:func:`date_range()` that generates a list of dates between a specified period.
      - Deprecated ``standard_date()``, which now just uses the native ``date()`` with `DateTime constants <http://php.net/manual/en/class.datetime.php#datetime.constants.types>`_.
      - Changed :php:func:`now()` to work with all timezone strings supported by PHP.
      - Changed :php:func:`days_in_month()` to use the native ``cal_days_in_month()`` PHP function, if available.

   -  :doc:`URL Helper <helpers/url_helper>` changes include:

      - Deprecated *separator* options **dash** and **underscore** for function :php:func:`url_title()` (they are only aliases for '-' and '_' respectively).
      - :php:func:`url_title()` will now trim extra dashes from beginning and end.
      - :php:func:`anchor_popup()` will now fill the *href* attribute with the URL and its JS code will return FALSE instead.
      - Added JS window name support to the :php:func:`anchor_popup()` function.
      - Added support for menubar attribute to the :php:func:`anchor_popup()`.
      - Added support (auto-detection) for HTTP/1.1 response codes 303, 307 in :php:func:`redirect()`.
      - Changed :php:func:`redirect()` to choose the **refresh** method only on IIS servers, instead of all servers on Windows (when **auto** is used).
      - Changed :php:func:`anchor()`, :php:func:`anchor_popup()`, and :php:func:`redirect()` to support protocol-relative URLs (e.g. *//ellislab.com/codeigniter*).

   -  :doc:`HTML Helper <helpers/html_helper>` changes include:

      - Added more doctypes.
      - Changed application and environment config files to be loaded in a cascade-like manner.
      - Changed :php:func:`doctype()` to cache and only load once the doctypes array.
      - Deprecated functions ``nbs()`` and ``br()``, which are just aliases for the native ``str_repeat()`` with ``&nbsp;`` and ``<br />`` respectively.

   -  :doc:`Inflector Helper <helpers/inflector_helper>` changes include:

      - Changed :php:func:`humanize()` to allow passing an input separator as its second parameter.
      - Changed :php:func:`humanize()` and :php:func:`underscore()` to utilize `mbstring <http://php.net/mbstring>`_, if available.
      - Changed :php:func:`plural()` and :php:func:`singular()` to avoid double pluralization and support more words.

   -  :doc:`Download Helper <helpers/download_helper>` changes include:

      - Added an optional third parameter to :php:func:`force_download()` that enables/disables sending the actual file MIME type in the Content-Type header (disabled by default).
      - Added a work-around in :php:func:`force_download()` for a bug Android <= 2.1, where the filename extension needs to be in uppercase.
      - Added support for reading from an existing file path by passing NULL as the second parameter to :php:func:`force_download()` (useful for large files and/or safely transmitting binary data).

   -  :doc:`Form Helper <helpers/form_helper>` changes include:

      - :php:func:`form_dropdown()` will now also take an array for unity with other form helpers.
      - ``form_prep()`` is now DEPRECATED and only acts as an alias for :doc:`common function <general/common_functions>` :php:func:`html_escape()`.
      - :php:func:`set_value()` will now also accept a third argument, allowing to turn off HTML escaping of the value.

   -  :doc:`Security Helper <helpers/security_helper>` changes include:

      - ``do_hash()`` now uses PHP's native ``hash()`` function (supporting more algorithms) and is deprecated.
      - :php:func:`strip_image_tags()` is now an alias for the same method in the :doc:`Security Library <libraries/security>`.

   -  *Smiley Helper* changes include:

      - Deprecated the whole helper as too specific for CodeIgniter.
      - Removed previously deprecated function ``js_insert_smiley()``.
      - Changed application and environment config files to be loaded in a cascade-like manner.
      - The smileys array is now cached and loaded only once.

   -  :doc:`File Helper <helpers/file_helper>` changes include:

      - :php:func:`set_realpath()` can now also handle file paths as opposed to just directories.
      - Added an optional paramater to :php:func:`delete_files()` to enable it to skip deleting files such as *.htaccess* and *index.html*.
      - Deprecated function ``read_file()`` - it's just an alias for PHP's native ``file_get_contents()``.

   -  :doc:`String Helper <helpers/string_helper>` changes include:

      - Deprecated function ``repeater()`` - it's just an alias for PHP's native ``str_repeat()``.
      - Deprecated function ``trim_slashes()`` - it's just an alias for PHP's native ``trim()`` (with a slash as its second argument).
      - Deprecated randomization type options **unique** and **encrypt** for funcion :php:func:`random_string()` (they are only aliases for **md5** and **sha1** respectively).

   -  :doc:`CAPTCHA Helper <helpers/captcha_helper>` changes include:

      - Added *word_length* and *pool* options to allow customization of the generated word.
      - Added *colors* configuration to allow customization for the *background*, *border*, *text* and *grid* colors.
      - Added *filename* to the returned array elements.
      - Updated to use `imagepng()` in case that `imagejpeg()` isn't available.
      - Added **font_size** option to allow customization of font size.
      - Added **img_id** option to set id attribute of captcha image.

   -  :doc:`Text Helper <helpers/text_helper>` changes include:

      - Changed the default tag for use in :php:func:`highlight_phrase()` to ``<mark>`` (formerly ``<strong>``).
      - Changed :php:func:`character_limiter()`, :php:func:`word_wrap()` and :php:func:`ellipsize()` to utilize `mbstring <http://php.net/mbstring>`_ or `iconv <http://php.net/iconv>`_, if available.

   -  :doc:`Directory Helper <helpers/directory_helper>` :php:func:`directory_map()` will now append ``DIRECTORY_SEPARATOR`` to directory names in the returned array.
   -  :doc:`Array Helper <helpers/array_helper>` :php:func:`element()` and :php:func:`elements()` now return NULL instead of FALSE when the required elements don't exist.
   -  :doc:`Language Helper <helpers/language_helper>` :php:func:`lang()` now accepts an optional list of additional HTML attributes.
   -  Deprecated the *Email Helper* as its ``valid_email()``, ``send_email()`` functions are now only aliases for PHP native functions ``filter_var()`` and ``mail()`` respectively.

-  Database

   -  DEPRECATED the 'mysql', 'sqlite', 'mssql' and 'pdo/dblib' (also known as 'pdo/mssql' or 'pdo/sybase') drivers.
   -  Added **dsn** configuration setting for drivers that support DSN strings (PDO, PostgreSQL, Oracle, ODBC, CUBRID).
   -  Added **schema** configuration setting (defaults to *public*) for drivers that might need it (currently used by PostgreSQL and ODBC).
   -  Added **save_queries** configuration setting to *application/config/database.php* (defaults to ``TRUE``).
   -  Removed **autoinit** configuration setting as it doesn't make sense to instantiate the database class but not connect to the database.
   -  Added subdrivers support (currently only used by PDO).
   -  Added an optional database name parameter to ``db_select()``.
   -  Removed ``protect_identifiers()`` and renamed internal method ``_protect_identifiers()`` to it instead - it was just an alias.
   -  Renamed internal method ``_escape_identifiers()`` to ``escape_identifiers()``.
   -  Updated ``escape_identifiers()`` to accept an array of fields as well as strings.
   -  MySQL and MySQLi drivers now require at least MySQL version 5.1.
   -  Added a ``$persistent`` parameter to ``db_connect()`` and changed ``db_pconnect()`` to be an alias for ``db_connect(TRUE)``.
   -  ``db_set_charset()`` now only requires one parameter (collation was only needed due to legacy support for MySQL versions prior to 5.1).
   -  ``db_select()`` will now always (if required by the driver) be called by ``db_connect()`` instead of only when initializing.
   -  Replaced the ``_error_message()`` and ``_error_number()`` methods with ``error()``, which returns an array containing the last database error code and message.
   -  Improved ``version()`` implementation so that drivers that have a native function to get the version number don't have to be defined in the core ``DB_driver`` class.
   -  Added capability for packages to hold *config/database.php* config files.
   -  Added MySQL client compression support.
   -  Added encrypted connections support (for *mysql*, *sqlsrv* and PDO with *sqlsrv*).
   -  Removed :doc:`Loader Class <libraries/loader>` from Database error tracing to better find the likely culprit.
   -  Added support for SQLite3 database driver.
   -  Added Interbase/Firebird database support via the *ibase* driver.
   -  Added ODBC support for ``create_database()``, ``drop_database()`` and ``drop_table()`` in :doc:`Database Forge <database/forge>`.
   -  Added support to binding arrays as ``IN()`` sets in ``query()``.

   -  :doc:`Query Builder <database/query_builder>` changes include:

      - Renamed the Active Record class to Query Builder to remove confusion with the Active Record design pattern.
      - Added the ability to insert objects with ``insert_batch()``.
      - Added new methods that return the SQL string of queries without executing them: ``get_compiled_select()``, ``get_compiled_insert()``, ``get_compiled_update()``, ``get_compiled_delete()``.
      - Added an optional parameter that allows to disable escaping (useful for custom fields) for methods ``join()``, ``order_by()``, ``where_in()``, ``or_where_in()``, ``where_not_in()``, ``or_where_not_in()``, ``insert()``, ``insert_batch()``.
      - Added support for ``join()`` with multiple conditions.
      - Added support for *USING* in ``join()``.
      - Added support for *EXISTS* in ``where()``.
      - Added seed values support for random ordering with ``order_by(seed, 'RANDOM')``.
      - Changed ``limit()`` to ignore NULL values instead of always casting to integer.
      - Changed ``offset()`` to ignore empty values instead of always casting to integer.
      - Methods ``insert_batch()`` and ``update_batch()`` now return an integer representing the number of rows affected by them.
      - Methods ``where()``, ``or_where()``, ``having()`` and ``or_having()`` now convert trailing  ``=`` and ``<>``,  ``!=`` SQL operators to ``IS NULL`` and ``IS NOT NULL`` respectively when the supplied comparison value is ``NULL``.
      - Added method chaining support to ``reset_query()``, ``start_cache()``, ``stop_cache()`` and ``flush_cache()``.
      - Added an optional second parameter to ``count_all_results()`` to disable resetting of QB values.

   -  :doc:`Database Results <database/results>` changes include:

      - Added a constructor to the ``DB_result`` class and moved all driver-specific properties and logic out of the base ``DB_driver`` class to allow better abstraction.
      - Added method ``unbuffered_row()`` for fetching a row without prefetching the whole result (consume less memory).
      - Renamed former method ``_data_seek()`` to ``data_seek()`` and made it public.

   -  Improved support for the MySQLi driver, including:

      - OOP style usage of the PHP extension is now used, instead of the procedural aliases.
      - Server version checking is now done via ``mysqli::$server_info`` instead of running an SQL query.
      - Added persistent connections support for PHP >= 5.3.
      - Added support for configuring socket pipe connections.
      - Added support for ``backup()`` in :doc:`Database Utilities <database/utilities>`.
      - Changed methods ``trans_begin()``, ``trans_commit()`` and ``trans_rollback()`` to use the PHP API instead of sending queries.

   -  Improved support of the PDO driver, including:

      - Added support for ``create_database()``, ``drop_database()`` and ``drop_table()`` in :doc:`Database Forge <database/forge>`.
      - Added support for ``list_fields()`` in :doc:`Database Results <database/results>`.
      - Subdrivers are now isolated from each other instead of being in one large class.

   -  Improved support of the PostgreSQL driver, including:

      - ``pg_version()`` is now used to get the database version number, when possible.
      - Added ``db_set_charset()`` support.
      - Added support for ``optimize_table()`` in :doc:`Database Utilities <database/utilities>` (rebuilds table indexes).
      - Added boolean data type support in ``escape()``.
      - Added ``update_batch()`` support.
      - Removed ``limit()`` and ``order_by()`` support for *UPDATE* and *DELETE* queries as PostgreSQL does not support those features.
      - Added a work-around for dead persistent connections to be re-created after a database restart.
      - Changed ``db_connect()`` to include the (new) **schema** value into Postgre's **search_path** session variable.
      - ``pg_escape_literal()`` is now used for escaping strings, if available.

   -  Improved support of the CUBRID driver, including:

      - Added DSN string support.
      - Added persistent connections support.
      - Improved ``list_databases()`` in :doc:`Database Utility <database/utilities>` (until now only the currently used database was returned).

   -  Improved support of the MSSQL and SQLSRV drivers, including:

      - Added random ordering support.
      - Added support for ``optimize_table()`` in :doc:`Database Utility <database/utilities>`.
      - Added escaping with *QUOTE_IDENTIFIER* setting detection.
      - Added port handling support for UNIX-based systems (MSSQL driver).
      - Added *OFFSET* support for SQL Server 2005 and above.
      - Added ``db_set_charset()`` support (MSSQL driver).
      - Added a *scrollable* property to enable configuration of the cursor to use (SQLSRV driver).
      - Added support and auto-detection for the ``SQLSRV_CURSOR_CLIENT_BUFFERED`` scrollable cursor flag (SQLSRV driver).
      - Changed default behavior to not use ``SQLSRV_CURSOR_STATIC`` due to performance issues (SQLSRV driver).

   -  Improved support of the Oracle (OCI8) driver, including:

      - Added DSN string support (Easy Connect and TNS).
      - Added support for ``drop_table()`` in :doc:`Database Forge <database/forge>`.
      - Added support for ``list_databases()`` in :doc:`Database Utilities <database/utilities>`.
      - Generally improved for speed and cleaned up all of its components.
      - ``num_rows()`` is now only called explicitly by the developer and no longer re-executes statements.

   -  Improved support of the SQLite driver, including:

      - Added support for ``replace()`` in :doc:`Query Builder <database/query_builder>`.
      - Added support for ``drop_table()`` in :doc:`Database Forge <database/forge>`.

   -  :doc:`Database Forge <database/forge>` changes include:

      - Added an optional second parameter to ``drop_table()`` that allows adding the **IF EXISTS** condition, which is no longer the default.
      - Added support for passing a custom database object to the loader.
      - Added support for passing custom table attributes (such as ``ENGINE`` for MySQL) to ``create_table()``.
      - Added support for usage of the *FIRST* clause in ``add_column()`` for MySQL and CUBRID.
      - Added partial support for field comments (MySQL, PostgreSQL, Oracle).
      - Deprecated ``add_column()``'s third method. *AFTER* clause should now be added to the field definition array instead.
      - Overall improved support for all of the drivers.

   -  :doc:`Database Utility <database/utilities>` changes include:

      - Added support for passing a custom database object to the loader.
      - Modified the class to no longer extend :doc:`Database Forge <database/forge>`, which has been a deprecated behavior for awhile.
      - Overall improved support for all of the drivers.
      - Added *foreign_key_checks* option to MySQL/MySQLi backup, allowing statement to disable/re-enable foreign key checks to be inserted into the backup output.

-  Libraries

   -  Added a new :doc:`Encryption Library <libraries/encryption>` to replace the old, largely insecure :doc:`Encrypt Library <libraries/encrypt>`.

   -  :doc:`Encrypt Library <libraries/encrypt>` changes include:

      -  Deprecated the library in favor of the new :doc:`Encryption Library <libraries/encryption>`.
      -  Added support for hashing algorithms other than SHA1 and MD5.
      -  Removed previously deprecated ``sha1()`` method.

   -  :doc:`Session Library <libraries/sessions>` changes include:

      -  Completely re-written the library to use self-contained drivers via ``$config['sess_driver']``.
      -  Added 'files', 'database', 'redis' and 'memcached' drivers (using 'files' by default).
      -  Added ``$config['sess_save_path']`` setting to specify where the session data is stored, depending on the driver.
      -  Dropped support for storing session data in cookies (which renders ``$config['sess_encrypt_cookie']`` useless and is therefore also removed).
      -  Dropped official  support for storing session data in databases other than MySQL and PostgreSQL.
      -  Changed table structure for the 'database' driver.
      -  Added a new **tempdata** feature that allows setting userdata items with expiration time (``mark_as_temp()``, ``tempdata()``, ``set_tempdata()``, ``unset_tempdata()``).
      -  Changed method ``keep_flashdata()`` to also accept an array of keys.
      -  Changed methods ``userdata()``, ``flashdata()`` to return an array of all userdata/flashdata when no parameter is passed.
      -  Deprecated method ``all_userdata()`` - it is now just an alias for ``userdata()`` with no parameters.
      -  Added method ``has_userdata()`` that verifies the existence of a userdata item.
      -  Added *debug* level log messages for key events in the session validation process.
      -  Dropped support for the *sess_match_useragent* option.

   -  :doc:`File Uploading Library <libraries/file_uploading>` changes include:

      -  Added method chaining support.
      -  Added support for using array notation in file field names.
      -  Added **max_filename_increment** and **file_ext_tolower** configuration settings.
      -  Added **min_width** and **min_height** configuration settings for images.
      -  Added **mod_mime_fix** configuration setting to disable suffixing multiple file extensions with an underscore.
      -  Added the possibility pass **allowed_types** as an array.
      -  Added an ``$index`` parameter to the method ``data()``.
      -  Added a ``$reset`` parameter to method ``initialize()``.
      -  Removed method ``clean_file_name()`` and its usage in favor of :doc:`Security Library <libraries/security>`'s ``sanitize_filename()``.
      -  Removed method ``mimes_types()``.
      -  Changed ``CI_Upload::_prep_filename()`` to simply replace all (but the last) dots in the filename with underscores, instead of suffixing them.

   -  :doc:`Calendar Library <libraries/calendar>` changes include:

      -  Added method chaining support.
      -  Added configuration to generate days of other months instead of blank cells.
      -  Added auto-configuration for *next_prev_url* if it is empty and *show_prev_next* is set to TRUE.
      -  Added support for templating via an array in addition to the encoded string.
      -  Changed method ``get_total_days()`` to be an alias for :doc:`Date Helper <helpers/date_helper>` :php:func:`days_in_month()`.

   -  *Cart Library* changes include:

      -  Deprecated the library as too specific for CodeIgniter.
      -  Added method ``remove()`` to remove a cart item, updating with quantity of 0 seemed like a hack but has remained to retain compatibility.
      -  Added method ``get_item()`` to enable retrieving data for a single cart item.
      -  Added unicode support for product names.
      -  Added support for disabling product name strictness via the ``$product_name_safe`` property.
      -  Changed ``insert()`` method to auto-increment quantity for an item when inserted twice instead of resetting it.
      -	 Changed ``update()`` method to support updating all properties attached to an item and not to require 'qty'.

   -  :doc:`Image Manipulation Library <libraries/image_lib>` changes include:

      -  The ``initialize()`` method now only sets existing class properties.
      -  Added support for 3-length hex color values for *wm_font_color* and *wm_shadow_color* properties, as well as validation for them.
      -  Class properties *wm_font_color*, *wm_shadow_color* and *wm_use_drop_shadow* are now protected, to avoid breaking the ``text_watermark()`` method if they are set manually after initialization.
      -  If property *maintain_ratio* is set to TRUE, ``image_reproportion()`` now doesn't need both width and height to be specified.
      -  Property *maintain_ratio* is now taken into account when resizing images using ImageMagick library.
      -  Added support for maintaining transparency for PNG images when watermarking.
      -  Added a **file_permissions** setting.

   -  :doc:`Form Validation Library <libraries/form_validation>` changes include:

      -  Added method ``error_array()`` to return all error messages as an array.
      -  Added method ``set_data()`` to set an alternative data array to be validated instead of the default ``$_POST``.
      -  Added method ``reset_validation()`` which resets internal validation variables in case of multiple validation routines.
      -  Added support for setting error delimiters in the config file via ``$config['error_prefix']`` and ``$config['error_suffix']``.
      -  Internal method ``_execute()`` now considers input data to be invalid if a specified rule is not found.
      -  Removed method ``is_numeric()`` as it exists as a native PHP function and ``_execute()`` will find and use that (the **is_numeric** rule itself is deprecated since 1.6.1).
      -  Native PHP functions used as rules can now accept an additional parameter, other than the data itself.
      -  Updated method ``set_rules()`` to accept an array of rules as well as a string.
      -  Fields that have empty rules set no longer run through validation (and therefore are not considered erroneous).
      -  Added rule **differs** to check if the value of a field differs from the value of another field.
      -  Added rule **valid_url**.
      -  Added rule **in_list** to check if the value of a field is within a given list.
      -  Added support for named parameters in error messages.
      -  :doc:`Language <libraries/language>` line keys must now be prefixed with **form_validation_**.
      -  Added rule **alpha_numeric_spaces**.
      -  Added support for custom error messages per field rule.
      -  Added support for callable rules when they are passed as an array.
      -  Added support for non-ASCII domains in **valid_email** rule, depending on the Intl extension.
      -  Changed the debug message about an error message not being set to include the rule name it is about.

   -  :doc:`Caching Library <libraries/caching>` changes include:

      -  Added Wincache driver.
      -  Added Redis driver.
      -  Added a *key_prefix* option for cache IDs.
      -  Updated driver ``is_supported()`` methods to log at the "debug" level.
      -  Added option to store raw values instead of CI-formatted ones (APC, Memcache).
      -  Added atomic increment/decrement feature via ``increment()``, ``decrement()``.

   -  :doc:`E-mail Library <libraries/email>` changes include:

      -  Added a custom filename parameter to ``attach()`` as ``$this->email->attach($filename, $disposition, $newname)``.
      -  Added possibility to send attachment as buffer string in ``attach()`` as ``$this->email->attach($buffer, $disposition, $newname, $mime)``.
      -  Added possibility to attach remote files by passing a URL.
      -  Added method ``attachment_cid()`` to enable embedding inline attachments into HTML.
      -  Added dsn (delivery status notification) option.
      -  Renamed method ``_set_header()`` to ``set_header()`` and made it public to enable adding custom headers.
      -  Successfully sent emails will automatically clear the parameters.
      -  Added a *return_path* parameter to the ``from()`` method.
      -  Removed the second parameter (character limit) from internal method ``_prep_quoted_printable()`` as it is never used.
      -  Internal method ``_prep_quoted_printable()`` will now utilize the native ``quoted_printable_encode()``, ``imap_8bit()`` functions (if available) when CRLF is set to "\r\n".
      -  Default charset now relies on the global ``$config['charset']`` setting.
      -  Removed unused protected method ``_get_ip()`` (:doc:`Input Library <libraries/input>`'s ``ip_address()`` should be used anyway).
      -  Internal method ``_prep_q_encoding()`` now utilizes PHP's *mbstring* and *iconv* extensions (when available) and no longer has a second (``$from``) argument.
      -  Added an optional parameter to ``print_debugger()`` to allow specifying which parts of the message should be printed ('headers', 'subject', 'body').
      -  Added SMTP keepalive option to avoid opening the connection for each ``send()`` call. Accessible as ``$smtp_keepalive``.
      -  Public method ``set_header()`` now filters the input by removing all "\\r" and "\\n" characters.
      -  Added support for non-ASCII domains in ``valid_email()``, depending on the Intl extension.

   -  :doc:`Pagination Library <libraries/pagination>` changes include:

      -  Deprecated usage of the "anchor_class" setting (use the new "attributes" setting instead).
      -  Added method chaining support to ``initialize()`` method.
      -  Added support for the anchor "rel" attribute.
      -  Added support for setting custom attributes.
      -  Added support for language translations of the *first_link*, *next_link*, *prev_link* and *last_link* values.
      -  Added support for ``$config['num_links'] = 0`` configuration.
      -  Added ``$config['reuse_query_string']`` to allow automatic repopulation of query string arguments, combined with normal URI segments.
      -  Added ``$config['use_global_url_suffix']`` to allow overriding the library 'suffix' value with that of the global ``$config['url_suffix']`` setting.
      -  Removed the default ``&nbsp;`` from a number of the configuration variables.

   -  :doc:`Profiler Library <general/profiling>` changes include:

      -  Database object names are now being displayed.
      -  The sum of all queries running times in seconds is now being displayed.
      -  Added support for displaying the HTTP DNT ("Do Not Track") header.
      -  Added support for displaying ``$_FILES``.

   -  :doc:`Migration Library <libraries/migration>` changes include:

      -  Added support for timestamp-based migrations (enabled by default).
      -  Added ``$config['migration_type']`` to allow switching between *sequential* and *timestamp* migrations.

   -  :doc:`XML-RPC Library <libraries/xmlrpc>` changes include:

      -  Added the ability to use a proxy.
      -  Added Basic HTTP authentication support.

   -  :doc:`User Agent Library <libraries/user_agent>` changes include:

      - Added check to detect if robots are pretending to be mobile clients (helps with e.g. Google indexing mobile website versions).
      - Added method ``parse()`` to allow parsing a custom user-agent string, different from the current visitor's.

   -  :doc:`HTML Table Library <libraries/table>` changes include:

      - Added method chaining support.
      - Added support for setting table class defaults in a config file.

   -  :doc:`Zip Library <libraries/zip>` changes include:

      - Method ``read_file()`` can now also alter the original file path/name while adding files to an archive.
      - Added support for changing the compression level.

   -  :doc:`Trackback Library <libraries/trackback>` method ``receive()`` will now utilize ``iconv()`` if it is available but ``mb_convert_encoding()`` is not.

-  Core

   -  :doc:`Routing <general/routing>` changes include:

      -  Added support for multiple levels of controller directories.
      -  Added support for per-directory *default_controller* and *404_override* classes.
      -  Added possibility to route requests using HTTP verbs.
      -  Added possibility to route requests using callbacks.
      -  Added a new reserved route (*translate_uri_dashes*) to allow usage of dashes in the controller and method URI segments.
      -  Deprecated methods ``fetch_directory()``, ``fetch_class()`` and ``fetch_method()`` in favor of their respective public properties.
      -  Removed method ``_set_overrides()`` and moved its logic to the class constructor.

   -  :doc:`URI Library <libraries/uri>` changes include:

      -  Added conditional PCRE UTF-8 support to the "invalid URI characters" check and removed the ``preg_quote()`` call from it to allow more flexibility.
      -  Renamed method ``_filter_uri()`` to ``filter_uri()``.
      -  Changed method ``filter_uri()`` to accept by reference and removed its return value.
      -  Changed private methods to protected so that MY_URI can override them.
      -  Renamed internal method ``_parse_cli_args()`` to ``_parse_argv()``.
      -  Renamed internal method ``_detect_uri()`` to ``_parse_request_uri()``.
      -  Changed ``_parse_request_uri()`` to accept absolute URIs for compatibility with HTTP/1.1 as per `RFC2616 <http://www.ietf.org/rfc/rfc2616.txt>`.
      -  Added protected method ``_parse_query_string()`` to URI paths in the the **QUERY_STRING** value, like ``_parse_request_uri()`` does.
      -  Changed URI string detection logic to always default to **REQUEST_URI** unless configured otherwise or under CLI.
      -  Removed methods ``_remove_url_suffix()``, ``_explode_segments()`` and moved their logic into ``_set_uri_string()``.
      -  Removed method ``_fetch_uri_string()`` and moved its logic into the class constructor.
      -  Removed method ``_reindex_segments()``.

   -  :doc:`Loader Library <libraries/loader>` changes include:

      -  Added method chaining support.
      -  Added method ``get_vars()`` to the Loader to retrieve all variables loaded with ``$this->load->vars()``.
      -  ``_ci_autoloader()`` is now a protected method.
      -  Added autoloading of drivers with ``$autoload['drivers']``.
      -  ``$config['rewrite_short_tags']`` now has no effect when using PHP 5.4 as ``<?=`` will always be available.
      -  Changed method ``config()`` to return whatever ``CI_Config::load()`` returns instead of always being void.
      -  Added support for library and model aliasing on autoload.
      -  Changed method ``is_loaded()`` to ask for the (case sensitive) library name instead of its instance name.
      -  Removed ``$_base_classes`` property and unified all class data in ``$_ci_classes`` instead.
      -  Added method ``clear_vars()`` to allow clearing the cached variables for views.

   -  :doc:`Input Library <libraries/input>` changes include:

      -  Deprecated the ``$config['global_xss_filtering']`` setting.
      -  Added ``method()`` to retrieve ``$_SERVER['REQUEST_METHOD']``.
      -  Added support for arrays and network addresses (e.g. 192.168.1.1/24) for use with the *proxy_ips* setting.
      -  Added method ``input_stream()`` to aid in using **php://input** stream data such as one passed via PUT, DELETE and PATCH requests.
      -  Changed method ``valid_ip()`` to use PHP's native ``filter_var()`` function.
      -  Changed internal method ``_sanitize_globals()`` to skip enforcing reversal of *register_globals* in PHP 5.4+, where this functionality no longer exists.
      -  Changed methods ``get()``, ``post()``, ``get_post()``, ``cookie()``, ``server()``, ``user_agent()`` to return NULL instead of FALSE when no value is found.
      -  Changed default value of the ``$xss_clean`` parameter to NULL for all methods that utilize it, the default value is now determined by the ``$config['global_xss_filtering']`` setting.
      -  Added method ``post_get()`` and changed ``get_post()`` to search in GET data first. Both methods' names now properly match their GET/POST data search priorities.
      -  Changed method ``_fetch_from_array()`` to parse array notation in field name.
      -  Changed method ``_fetch_from_array()`` to allow retrieving multiple fields at once.
      -  Added an option for ``_clean_input_keys()`` to return FALSE instead of terminating the whole script.
      -  Deprecated the ``is_cli_request()`` method, it is now an alias for the new :php:func:`is_cli()` common function.
      -  Added an ``$xss_clean`` parameter to method ``user_agent()`` and removed the ``$user_agent`` property.
      -  Added property ``$raw_input_stream`` to access **php://input** data.

   -  :doc:`Common functions <general/common_functions>` changes include:

      -  Added function :php:func:`get_mimes()` to return the *application/config/mimes.php* array.
      -  Added support for HTTP code 303 ("See Other") in :php:func:`set_status_header()`.
      -  Removed redundant conditional to determine HTTP server protocol in :php:func:`set_status_header()`.
      -  Renamed ``_exception_handler()`` to ``_error_handler()`` and replaced it with a real exception handler.
      -  Changed ``_error_handler()`` to respect php.ini *display_errors* setting.
      -  Added function :php:func:`is_https()` to check if a secure connection is used.
      -  Added function :php:func:`is_cli()` to replace the ``CI_Input::is_cli_request()`` method.
      -  Added function :php:func:`function_usable()` to work around a bug in `Suhosin <http://www.hardened-php.net/suhosin/>`.
      -  Removed the third (`$php_error`) argument from function :php:func:`log_message()`.
      -  Changed internal function ``load_class()`` to accept a constructor parameter instead of (previously unused) class name prefix.
      -  Removed default parameter value of :php:func:`is_php()`.
      -  Added a second argument ``$double_encode`` to :php:func:`html_escape()`.
      -  Changed function :php:func:`config_item()` to return NULL instead of FALSE when no value is found.
      -  Changed function :php:func:`set_status_header()` to return immediately when run under CLI.

   -  :doc:`Output Library <libraries/output>` changes include:

      -  Added a second argument to method ``set_content_type()`` that allows setting the document charset as well.
      -  Added methods ``get_content_type()`` and ``get_header()``.
      -  Added method ``delete_cache()``.
      -  Added configuration option ``$config['cache_query_string']`` to enable taking the query string into account when caching.
      -  Changed caching behavior to compress the output before storing it, if ``$config['compress_output']`` is enabled.

   -  :doc:`Config Library <libraries/config>` changes include:

      -  Changed ``site_url()`` method  to accept an array as well.
      -  Removed internal method ``_assign_to_config()`` and moved its implementation to *CodeIgniter.php* instead.
      -  ``item()`` now returns NULL instead of FALSE when the required config item doesn't exist.
      -  Added an optional second parameter to both ``base_url()`` and ``site_url()`` that allows enforcing of a protocol different than the one in the *base_url* configuration setting.
      -  Added HTTP "Host" header character validation to prevent cache poisoning attacks when ``base_url`` auto-detection is used.

   -  :doc:`Security Library <libraries/security>` changes include:

      -  Added ``$config['csrf_regeneration']``, which makes CSRF token regeneration optional.
      -  Added ``$config['csrf_exclude_uris']``, allowing for exclusion of URIs from the CSRF protection (regular expressions are supported).
      -  Added method ``strip_image_tags()``.
      -  Added method ``get_random_bytes()`` and switched CSRF & XSS token generation to use it.
      -  Modified method ``sanitize_filename()`` to read a public ``$filename_bad_chars`` property for getting the invalid characters list.
      -  Return status code of 403 instead of a 500 if CSRF protection is enabled but a token is missing from a request.

   -  :doc:`Language Library <libraries/language>` changes include:

      -  Changed method ``load()`` to filter the language name with ``ctype_alpha()``.
      -  Changed method ``load()`` to also accept an array of language files.
      -  Added an optional second parameter to method ``line()`` to disable error logging for line keys that were not found.
      -  Language files are now loaded in a cascading style with the one in **system/** always loaded and overridden afterwards, if another one is found.

   -  :doc:`Hooks Library <general/hooks>` changes include:

      -  Added support for closure hooks (or anything that ``is_callable()`` returns TRUE for).
      -  Renamed method ``_call_hook()`` to ``call_hook()``.
      -  Class instances are now stored in order to maintain their state.

   -  UTF-8 Library changes include:

      -  ``UTF8_ENABLED`` now requires only one of `Multibyte String <http://php.net/mbstring>`_ or `iconv <http://php.net/iconv>`_ to be available instead of both.
      -  Changed method ``clean_string()`` to utilize ``mb_convert_encoding()`` if it is available.
      -  Renamed method ``_is_ascii()`` to ``is_ascii()`` and made it public.

   -  Log Library changes include:

      -  Added a ``$config['log_file_permissions']`` setting.
      -  Changed the library constructor to try to create the **log_path** directory if it doesn't exist.
      -  Added support for microseconds ("u" date format character) in ``$config['log_date_format']``.

   -  Added :doc:`compatibility layers <general/compatibility_functions>` for:

      - `Multibyte String <http://php.net/mbstring>`_ (limited support).
      - `Hash <http://php.net/hash>`_ (``hash_equals()``, ``hash_pbkdf2()``).
      - `Password Hashing <http://php.net/password>`_.
      - `Standard Functions ``array_column()``, ``array_replace()``, ``array_replace_recursive()``, ``hex2bin()``, ``quoted_printable_encode()``.

   -  Removed ``CI_CORE`` boolean constant from *CodeIgniter.php* (no longer Reactor and Core versions).
   -  Added support for HTTP-Only cookies with new config option *cookie_httponly* (default FALSE).
   -  ``$config['time_reference']`` now supports all timezone strings supported by PHP.
   -  Fatal PHP errors are now also passed to ``_error_handler()``, so they can be logged.


Bug fixes for 3.0
-----------------

-  Fixed a bug where ``unlink()`` raised an error if cache file did not exist when you try to delete it.
-  Fixed a bug (#181) - a typo in the form validation language file.
-  Fixed a bug (#159, #163) - :doc:`Query Builder <database/query_builder>` nested transactions didn't work properly due to ``$_trans_depth`` not being incremented.
-  Fixed a bug (#737, #75) - :doc:`Pagination <libraries/pagination>` anchor class was not set properly when using initialize method.
-  Fixed a bug (#419) - :doc:`URL Helper <helpers/url_helper>` :php:func:`auto_link()` didn't recognize URLs that come after a word boundary.
-  Fixed a bug (#724) - :doc:`Form Validation Library <libraries/form_validation>` rule **is_unique** didn't check if a database connection exists.
-  Fixed a bug (#647) - :doc:`Zip Library <libraries/zip>` internal method ``_get_mod_time()`` didn't suppress possible "stat failed" errors generated by ``filemtime()``.
-  Fixed a bug (#157, #174) - :doc:`Image Manipulation Library <libraries/image_lib>` method ``clear()`` didn't completely clear properties.
-  Fixed a bug where :doc:`Database Forge <database/forge>` method ``create_table()`` with PostgreSQL database could lead to fetching the whole table.
-  Fixed a bug (#795) - :doc:`Form Helper <helpers/form_helper>` :php:func:`form_open()` didn't add the default form *method* and *accept-charset* when an empty array is passed to it.
-  Fixed a bug (#797) - :doc:`Date Helper <helpers/date_helper>` :php:func:`timespan()` was using incorrect seconds for year and month.
-  Fixed a bug in *Cart Library* method ``contents()`` where if called without a TRUE (or equal) parameter, it would fail due to a typo.
-  Fixed a bug (#406) - SQLSRV DB driver not returning resource on ``db_pconnect()``.
-  Fixed a bug in :doc:`Image Manipulation Library <libraries/image_lib>` method ``gd_loaded()`` where it was possible for the script execution to end or a PHP E_WARNING message to be emitted.
-  Fixed a bug in the :doc:`Pagination library <libraries/pagination>` where when use_page_numbers=TRUE previous link and page 1 link did not have the same url.
-  Fixed a bug (#561) - errors in :doc:`XML-RPC Library <libraries/xmlrpc>` were not properly escaped.
-  Fixed a bug (#904) - :doc:`Loader Library <libraries/loader>` method ``initialize()`` caused a PHP Fatal error to be triggered if error level E_STRICT is used.
-  Fixed a hosting edge case where an empty ``$_SERVER['HTTPS']`` variable would evaluate to 'on'.
-  Fixed a bug (#154) - :doc:`Session Library <libraries/sessions>` method ``sess_update()`` caused the session to be destroyed on pages where multiple AJAX requests were executed at once.
-  Fixed a possible bug in :doc:`Input Libary <libraries/input>` method ``is_ajax_request()`` where some clients might not send the X-Requested-With HTTP header value exactly as 'XmlHttpRequest'.
-  Fixed a bug (#1039) - :doc:`Database Utilities <database/utilities>` internal method ``_backup()`` method failed for the 'mysql' driver due to a table name not being escaped.
-  Fixed a bug (#1070) - ``CI_DB_driver::initialize()`` didn't set a character set if a database is not selected.
-  Fixed a bug (#177) - :doc:`Form Validation Library <libraries/form_validation>` method ``set_value()`` didn't set the default value if POST data is NULL.
-  Fixed a bug (#68, #414) - :Oracle's ``escape_str()`` didn't properly escape LIKE wild characters.
-  Fixed a bug (#81) - ODBC's ``list_fields()`` and ``field_data()`` methods skipped the first column due to ``odbc_field_*()`` functions' index starting at 1 instead of 0.
-  Fixed a bug (#129) - ODBC's ``num_rows()`` method returned -1 in some cases, due to not all subdrivers supporting the ``odbc_num_rows()`` function.
-  Fixed a bug (#153) - E_NOTICE being generated by ``getimagesize()`` in the :doc:`File Uploading Library <libraries/file_uploading>`.
-  Fixed a bug (#611) - SQLSRV's error handling methods used to issue warnings when there's no actual error.
-  Fixed a bug (#1036) - ``is_write_type()`` method in the :doc:`Database Library <database/index>` didn't return TRUE for RENAME queries.
-  Fixed a bug in PDO's ``_version()`` method where it used to return the client version as opposed to the server one.
-  Fixed a bug in PDO's ``insert_id()`` method where it could've failed if it's used with Postgre versions prior to 8.1.
-  Fixed a bug in CUBRID's ``affected_rows()`` method where a connection resource was passed to ``cubrid_affected_rows()`` instead of a result.
-  Fixed a bug (#638) - ``db_set_charset()`` ignored its arguments and always used the configured charset instead.
-  Fixed a bug (#413) - Oracle's error handling methods used to only return connection-related errors.
-  Fixed a bug (#1101) - :doc:`Database Result <database/results>` method ``field_data()`` for 'mysql', 'mysqli' drivers was implemented as if it was handling a DESCRIBE result instead of the actual result set.
-  Fixed a bug in Oracle's :doc:`Database Forge <database/forge>` method ``_create_table()`` where it failed with AUTO_INCREMENT as it's not supported.
-  Fixed a bug (#1080) - when using the SMTP protocol, :doc:`Email Library <libraries/email>` method ``send()`` was returning TRUE even if the connection/authentication against the server failed.
-  Fixed a bug (#306) - ODBC's ``insert_id()`` method was calling non-existent function ``odbc_insert_id()``, which resulted in a fatal error.
-  Fixed a bug in Oracle's :doc:`Database Result <database/results>` implementation where the cursor ID passed to it was always NULL.
-  Fixed a bug (#64) - Regular expression in *DB_query_builder.php* failed to handle queries containing SQL bracket delimiters in the JOIN condition.
-  Fixed a bug in the :doc:`Session Library <libraries/sessions>` where a PHP E_NOTICE error was triggered by ``_unserialize()`` due to results from databases such as MSSQL and Oracle being space-padded on the right.
-  Fixed a bug (#501) - :doc:`Form Validation Library <libraries/form_validation>` method ``set_rules()`` depended on ``count($_POST)`` instead of actually checking if the request method 'POST' before aborting.
-  Fixed a bug (#136) - PostgreSQL and MySQL's ``escape_str()`` method didn't properly escape LIKE wild characters.
-  Fixed a bug in :doc:`Loader Library <libraries/loader>` method ``library()`` where some PHP versions wouldn't execute the class constructor.
-  Fixed a bug (#88) - An unexisting property was used for configuration of the Memcache cache driver.
-  Fixed a bug (#14) - :doc:`Database Forge <database/forge>` method ``create_database()`` didn't utilize the configured database character set.
-  Fixed a bug (#23, #1238) - :doc:`Database Caching <database/caching>` method ``delete_all()`` used to delete .htaccess and index.html files, which is a potential security risk.
-  Fixed a bug in :doc:`Trackback Library <libraries/trackback>` method ``validate_url()`` where it didn't actually do anything, due to input not being passed by reference.
-  Fixed a bug (#11, #183, #863) - :doc:`Form Validation Library <libraries/form_validation>` method ``_execute()`` silently continued to the next rule, if a rule method/function is not found.
-  Fixed a bug (#122) - routed URI string was being reported incorrectly in sub-directories.
-  Fixed a bug (#1241) - :doc:`Zip Library <libraries/zip>` method ``read_dir()`` wasn't compatible with Windows.
-  Fixed a bug (#306) - ODBC driver didn't have an ``_insert_batch()`` method, which resulted in fatal error being triggered when ``insert_batch()`` is used with it.
-  Fixed a bug in MSSQL and SQLSrv's ``_truncate()`` where the TABLE keyword was missing.
-  Fixed a bug in PDO's ``trans_commit()`` method where it failed due to an erroneous property name.
-  Fixed a bug (#798) - :doc:`Query Builder <database/query_builder>` method ``update()`` used to ignore LIKE conditions that were set with ``like()``.
-  Fixed a bug in Oracle's and MSSQL's ``delete()`` methods where an erroneous SQL statement was generated when used with ``limit()``.
-  Fixed a bug in SQLSRV's ``delete()`` method where ``like()`` and ``limit()`` conditions were ignored.
-  Fixed a bug (#1265) - Database connections were always closed, regardless of the 'pconnect' option value.
-  Fixed a bug (#128) - :doc:`Language Library <libraries/language>` did not correctly keep track of loaded language files.
-  Fixed a bug (#1349) - :doc:`File Uploading Library <libraries/file_uploading>` method ``get_extension()`` returned the original filename when it didn't have an actual extension.
-  Fixed a bug (#1273) - :doc:`Query Builder <database/query_builder>` method ``set_update_batch()`` generated an E_NOTICE message.
-  Fixed a bug (#44, #110) - :doc:`File Uploading Library <libraries/file_uploading>` method ``clean_file_name()`` didn't clear '!' and '#' characters.
-  Fixed a bug (#121) - :doc:`Database Results <database/results>` method ``row()`` returned an array when there's no actual result to be returned.
-  Fixed a bug (#319) - SQLSRV's ``affected_rows()`` method failed due to a scrollable cursor being created for write-type queries.
-  Fixed a bug (#356) - :doc:`Database <database/index>` driver 'postgre' didn't have an ``_update_batch()`` method, which resulted in fatal error being triggered when ``update_batch()`` is used with it.
-  Fixed a bug (#784, #862) - :doc:`Database Forge <database/forge>` method ``create_table()`` failed on SQLSRV/MSSQL when used with 'IF NOT EXISTS'.
-  Fixed a bug (#1419) - :doc:`Driver Library <general/creating_drivers>` had a static variable that was causing an error.
-  Fixed a bug (#1411) - the :doc:`Email Library <libraries/email>` used its own short list of MIMEs instead the one from *config/mimes.php*.
-  Fixed a bug where php.ini setting *magic_quotes_runtime* wasn't turned off for PHP 5.3 (where it is indeed deprecated, but not non-existent).
-  Fixed a bug (#666) - :doc:`Output Library <libraries/output>` method ``set_content_type()`` didn't set the document charset.
-  Fixed a bug (#784, #861) - :doc:`Database Forge <database/forge>` method ``create_table()`` used to accept constraints for MSSQL/SQLSRV integer-type columns.
-  Fixed a bug (#706) - SQLSRV/MSSSQL :doc:`Database <database/index>` drivers didn't escape field names.
-  Fixed a bug (#1452) - :doc:`Query Builder <database/query_builder>` method ``protect_identifiers()`` didn't properly detect identifiers with spaces in their names.
-  Fixed a bug where :doc:`Query Builder <database/query_builder>` method ``protect_identifiers()`` ignored its extra arguments when the value passed to it is an array.
-  Fixed a bug where :doc:`Query Builder <database/query_builder>` internal method ``_has_operator()`` didn't detect BETWEEN.
-  Fixed a bug where :doc:`Query Builder <database/query_builder>` method ``join()`` failed with identifiers containing dashes.
-  Fixed a bug (#1264) - :doc:`Database Forge <database/forge>` and :doc:`Database Utilities <database/utilities>` didn't update/reset the databases and tables list cache when a table or a database is created, dropped or renamed.
-  Fixed a bug (#7) - :doc:`Query Builder <database/query_builder>` method ``join()`` only escaped one set of conditions.
-  Fixed a bug (#1321) - ``CI_Exceptions`` couldn't find the *errors/* directory in some cases.
-  Fixed a bug (#1202) - :doc:`Encrypt Library <libraries/encrypt>` ``encode_from_legacy()`` didn't set back the encrypt mode on failure.
-  Fixed a bug (#145) - :doc:`Database Class <database/index>` method ``compile_binds()`` failed when the bind marker was present in a literal string within the query.
-  Fixed a bug in :doc:`Query Builder <database/query_builder>` method ``protect_identifiers()`` where if passed along with the field names, operators got escaped as well.
-  Fixed a bug (#10) - :doc:`URI Library <libraries/uri>` internal method ``_detect_uri()`` failed with paths containing a colon.
-  Fixed a bug (#1387) - :doc:`Query Builder <database/query_builder>` method ``from()`` didn't escape table aliases.
-  Fixed a bug (#520) - :doc:`Date Helper <helpers/date_helper>` function :php:func:``nice_date()`` failed when the optional second parameter is not passed.
-  Fixed a bug (#318) - :doc:`Profiling Library <general/profiling>` setting *query_toggle_count* was not settable as described in the manual.
-  Fixed a bug (#938) - :doc:`Config Library <libraries/config>` method ``site_url()`` added a question mark to the URL string when query strings are enabled even if it already existed.
-  Fixed a bug (#999) - :doc:`Config Library <libraries/config>` method ``site_url()`` always appended ``$config['url_suffix']`` to the end of the URL string, regardless of whether a query string exists in it.
-  Fixed a bug where :doc:`URL Helper <helpers/url_helper>` function :php:func:`anchor_popup()` ignored the attributes argument if it is not an array.
-  Fixed a bug (#1328) - :doc:`Form Validation Library <libraries/form_validation>` didn't properly check the type of the form fields before processing them.
-  Fixed a bug (#79) - :doc:`Form Validation Library <libraries/form_validation>` didn't properly validate array fields that use associative keys or have custom indexes.
-  Fixed a bug (#427) - :doc:`Form Validation Library <libraries/form_validation>` method ``strip_image_tags()`` was an alias to a non-existent method.
-  Fixed a bug (#1545) - :doc:`Query Builder <database/query_builder>` method ``limit()`` wasn't executed properly under Oracle.
-  Fixed a bug (#1551) - :doc:`Date Helper <helpers/date_helper>` function ``standard_date()`` didn't properly format *W3C* and *ATOM* standard dates.
-  Fixed a bug where :doc:`Query Builder <database/query_builder>` method ``join()`` escaped literal values as if they were fields.
-  Fixed a bug (#135) - PHP Error logging was impossible without the errors being displayed.
-  Fixed a bug (#1613) - :doc:`Form Helper <helpers/form_helper>` functions :php:func:`form_multiselect()`, :php:func:`form_dropdown()` didn't properly handle empty array option groups.
-  Fixed a bug (#1605) - :doc:`Pagination Library <libraries/pagination>` produced incorrect *previous* and *next* link values.
-  Fixed a bug in SQLSRV's ``affected_rows()`` method where an erroneous function name was used.
-  Fixed a bug (#1000) - Change syntax of ``$view_file`` to ``$_ci_view_file`` to prevent being overwritten by application.
-  Fixed a bug (#1757) - :doc:`Directory Helper <helpers/directory_helper>` function :php:func:`directory_map()` was skipping files and directories named '0'.
-  Fixed a bug (#1789) - :doc:`Database Library <database/index>` method ``escape_str()`` escaped quote characters in LIKE conditions twice under MySQL.
-  Fixed a bug (#395) - :doc:`Unit Testing Library <libraries/unit_testing>` method ``result()`` didn't properly check array result columns when called from ``report()``.
-  Fixed a bug (#1692) - :doc:`Database Class <database/index>` method ``display_error()`` didn't properly trace the possible error source on Windows systems.
-  Fixed a bug (#1745) - :doc:`Database Class <database/index>` method ``is_write_type()`` didn't return TRUE for LOAD queries.
-  Fixed a bug (#1765) - :doc:`Database Class <database/index>` didn't properly detect connection errors for the 'mysqli' driver.
-  Fixed a bug (#1257) - :doc:`Query Builder <database/query_builder>` used to (unnecessarily) group FROM clause contents, which breaks certain queries and is invalid for some databases.
-  Fixed a bug (#1709) - :doc:`Email <libraries/email>` headers were broken when using long email subjects and \r\n as CRLF.
-  Fixed a bug where ``MB_ENABLED`` constant was only declared if ``UTF8_ENABLED`` was set to TRUE.
-  Fixed a bug where the :doc:`Session Library <libraries/sessions>` accepted cookies with *last_activity* values being in the future.
-  Fixed a bug (#1897) - :doc:`Email Library <libraries/email>` triggered PHP E_WARNING errors when *mail* protocol used and ``to()`` is never called.
-  Fixed a bug (#1409) - :doc:`Email Library <libraries/email>` didn't properly handle multibyte characters when applying Q-encoding to headers.
-  Fixed a bug where :doc:`Email Library <libraries/email>` ignored its *wordwrap* setting while handling alternative messages.
-  Fixed a bug (#1476, #1909) - :doc:`Pagination Library <libraries/pagination>` didn't take into account actual routing when determining the current page.
-  Fixed a bug (#1766) - :doc:`Query Builder <database/query_builder>` didn't always take into account the *dbprefix* setting.
-  Fixed a bug (#779) - :doc:`URI Class <libraries/uri>` didn't always trim slashes from the *uri_string* as shown in the documentation.
-  Fixed a bug (#134) - :doc:`Database Caching <database/caching>` method ``delete_cache()`` didn't work in some cases due to *cachedir* not being initialized properly.
-  Fixed a bug (#191) - :doc:`Loader Library <libraries/loader>` ignored attempts for (re)loading databases to ``get_instance()->db`` even when the old database connection is dead.
-  Fixed a bug (#1255) - :doc:`User Agent Library <libraries/user_agent>` method ``is_referral()`` only checked if ``$_SERVER['HTTP_REFERER']`` exists.
-  Fixed a bug (#1146) - :doc:`Download Helper <helpers/download_helper>` function :php:func:`force_download()` incorrectly sent *Cache-Control* directives *pre-check* and *post-check* to Internet Explorer.
-  Fixed a bug (#1811) - :doc:`URI Library <libraries/uri>` didn't properly cache segments for ``uri_to_assoc()`` and ``ruri_to_assoc()``.
-  Fixed a bug (#1506) - :doc:`Form Helpers <helpers/form_helper>` set empty *name* attributes.
-  Fixed a bug (#59) - :doc:`Query Builder <database/query_builder>` method ``count_all_results()`` ignored the DISTINCT clause.
-  Fixed a bug (#1624) - :doc:`Form Validation Library <libraries/form_validation>` rule **matches** didn't property handle array field names.
-  Fixed a bug (#1630) - :doc:`Form Helper <helpers/form_helper>` function :php:func:`set_value()` didn't escape HTML entities.
-  Fixed a bug (#142) - :doc:`Form Helper <helpers/form_helper>` function :php:func:`form_dropdown()` didn't escape HTML entities in option values.
-  Fixed a bug (#50) - :doc:`Session Library <libraries/sessions>` unnecessarily stripped slashed from serialized data, making it impossible to read objects in a namespace.
-  Fixed a bug (#658) - :doc:`Routing <general/routing>` wildcard **:any** didn't work as advertised and matched multiple URI segments instead of all characters within a single segment.
-  Fixed a bug (#1938) - :doc:`Email Library <libraries/email>` removed multiple spaces inside a pre-formatted plain text message.
-  Fixed a bug (#122) - :doc:`URI Library <libraries/uri>` method ``ruri_string()`` didn't include a directory if one is used.
-  Fixed a bug - :doc:`Routing Library <general/routing>` didn't properly handle *default_controller* in a subdirectory when a method is also specified.
-  Fixed a bug (#953) - :doc:`post_controller_constructor hook <general/hooks>` wasn't called with a *404_override*.
-  Fixed a bug (#1220) - :doc:`Profiler Library <general/profiling>` didn't display information for database objects that are instantiated inside models.
-  Fixed a bug (#1978) - :doc:`Directory Helper <helpers/directory_helper>` function :php:func:`directory_map()`'s return array didn't make a distinction between directories and file indexes when a directory with a numeric name is present.
-  Fixed a bug (#777) - :doc:`Loader Library <libraries/loader>` didn't look for helper extensions in added package paths.
-  Fixed a bug (#18) - :doc:`APC Cache <libraries/caching>` driver didn't (un)serialize data, resulting in failure to store objects.
-  Fixed a bug (#188) - :doc:`Unit Testing Library <libraries/unit_testing>` filled up logs with error messages for non-existing language keys.
-  Fixed a bug (#113) - :doc:`Form Validation Library <libraries/form_validation>` didn't properly handle empty fields that were specified as an array.
-  Fixed a bug (#2061) - :doc:`Routing Class <general/routing>` didn't properly sanitize directory, controller and function triggers with **enable_query_strings** set to TRUE.
-  Fixed a bug - SQLSRV didn't support ``escape_like_str()`` or escaping an array of values.
-  Fixed a bug - :doc:`Database Results <database/results>` method ``list_fields()`` didn't reset its field pointer for the 'mysql', 'mysqli' and 'mssql' drivers.
-  Fixed a bug (#2211) - :doc:`Migration Library <libraries/migration>` extensions couldn't execute ``CI_Migration::__construct()``.
-  Fixed a bug (#2255) - :doc:`Email Library <libraries/email>` didn't apply *smtp_timeout* to socket reads and writes.
-  Fixed a bug (#2239) - :doc:`Email Library <libraries/email>` improperly handled the Subject when used with *bcc_batch_mode* resulting in E_WARNING messages and an empty Subject.
-  Fixed a bug (#2234) - :doc:`Query Builder <database/query_builder>` didn't reset JOIN cache for write-type queries.
-  Fixed a bug (#2298) - :doc:`Database Results <database/results>` method ``next_row()`` kept returning the last row, allowing for infinite loops.
-  Fixed a bug (#2236, #2639) - :doc:`Form Helper <helpers/form_helper>` functions :php:func:`set_value()`, :php:func:`set_select()`, :php:func:`set_radio()`, :php:func:`set_checkbox()` didn't parse array notation for keys if the rule was not present in the :doc:`Form Validation Library <libraries/form_validation>`.
-  Fixed a bug (#2353) - :doc:`Query Builder <database/query_builder>` erroneously prefixed literal strings with **dbprefix**.
-  Fixed a bug (#78) - *Cart Library* didn't allow non-English letters in product names.
-  Fixed a bug (#77) - :doc:`Database Class <database/index>` didn't properly handle the transaction "test mode" flag.
-  Fixed a bug (#2380) - :doc:`URI Routing <general/routing>` method ``fetch_method()`` returned 'index' if the requested method name matches its controller name.
-  Fixed a bug (#2388) - :doc:`Email Library <libraries/email>` used to ignore attachment errors, resulting in broken emails being sent.
-  Fixed a bug (#2498) - :doc:`Form Validation Library <libraries/form_validation>` rule **valid_base64** only checked characters instead of actual validity.
-  Fixed a bug (#2425) - OCI8 :doc:`database <database/index>` driver method ``stored_procedure()`` didn't log an error unless **db_debug** was set to TRUE.
-  Fixed a bug (#2490) - :doc:`Database Class <database/queries>` method ``query()`` returning boolean instead of a result object for PostgreSQL-specific *INSERT INTO ... RETURNING* statements.
-  Fixed a bug (#249) - :doc:`Cache Library <libraries/caching>` didn't properly handle Memcache(d) configurations with missing options.
-  Fixed a bug (#180) - :php:func:`config_item()` didn't take into account run-time configuration changes.
-  Fixed a bug (#2551) - :doc:`Loader Library <libraries/loader>` method ``library()`` didn't properly check if a class that is being loaded already exists.
-  Fixed a bug (#2560) - :doc:`Form Helper <helpers/form_helper>` function :php:func:`form_open()` set the 'method="post"' attribute only if the passed attributes equaled an empty string.
-  Fixed a bug (#2585) - :doc:`Query Builder <database/query_builder>` methods ``min()``, ``max()``, ``avg()``, ``sum()`` didn't escape field names.
-  Fixed a bug (#2590) - :doc:`Common function <general/common_functions>` :php:func:`log_message()` didn't actually cache the ``CI_Log`` class instance.
-  Fixed a bug (#2609) - :doc:`Common function <general/common_functions>` :php:func:`get_config()` optional argument was only effective on first function call. Also, it can now add items, in addition to updating existing items.
-  Fixed a bug in the 'postgre' :doc:`database <database/index>` driver where the connection ID wasn't passed to ``pg_escape_string()``.
-  Fixed a bug (#33) - Script execution was terminated when an invalid cookie key was encountered.
-  Fixed a bug (#2691) - nested :doc:`database <database/index>` transactions could end in a deadlock when an error is encountered with *db_debug* set to TRUE.
-  Fixed a bug (#2515) - ``_exception_handler()`` used to send the 200 "OK" HTTP status code and didn't stop script exection even on fatal errors.
-  Fixed a bug - Redis :doc:`Caching <libraries/caching>` driver didn't handle connection failures properly.
-  Fixed a bug (#2756) - :doc:`Database Class <database/index>` executed the MySQL-specific `SET SESSION sql_mode` query for all drivers when the 'stricton' option is set.
-  Fixed a bug (#2579) - :doc:`Query Builder <database/query_builder>` "no escape" functionality didn't work properly with query cache.
-  Fixed a bug (#2237) - :doc:`Parser Library <libraries/parser>` failed if the same tag pair is used more than once within a template.
-  Fixed a bug (#2143) - :doc:`Form Validation Library <libraries/form_validation>` didn't check for rule groups named in a *controller/method* manner when trying to load from a config file.
-  Fixed a bug (#2762) - :doc:`Hooks Class <general/hooks>` didn't properly check if the called class/function exists.
-  Fixed a bug (#148) - :doc:`Input Library <libraries/input>` internal method ``_clean_input_data()`` assumed that it data is URL-encoded, stripping certain character sequences from it.
-  Fixed a bug (#346) - with ``$config['global_xss_filtering']`` turned on, the ``$_GET``, ``$_POST``, ``$_COOKIE`` and ``$_SERVER`` superglobals were overwritten during initialization time, resulting in XSS filtering being either performed twice or there was no possible way to get the original data, even though options for this do exist.
-  Fixed an edge case (#555) - :doc:`User Agent Library <libraries/user_agent>` reported an incorrect version Opera 10+ due to a non-standard user-agent string.
-  Fixed a bug (#133) - :doc:`Text Helper <helpers/text_helper>` :php:func:`ascii_to_entities()` stripped the last character if it happens to be in the extended ASCII group.
-  Fixed a bug (#2822) - ``fwrite()`` was used incorrectly throughout the whole framework, allowing incomplete writes when writing to a network stream and possibly a few other edge cases.
-  Fixed a bug where :doc:`User Agent Library <libraries/user_agent>` methods ``accept_charset()`` and ``accept_lang()`` didn't properly parse HTTP headers that contain spaces.
-  Fixed a bug where *default_controller* was called instad of triggering a 404 error if the current route is in a controller directory.
-  Fixed a bug (#2737) - :doc:`XML-RPC Library <libraries/xmlrpc>` used objects as array keys, which triggered E_NOTICE messages.
-  Fixed a bug (#2771) - :doc:`Security Library <libraries/security>` method ``xss_clean()`` didn't take into account HTML5 entities.
-  Fixed a bug (#2856) - ODBC method ``affected_rows()`` passed an incorrect value to ``odbc_num_rows()``.
-  Fixed a bug (#43) :doc:`Image Manipulation Library <libraries/image_lib>` method ``text_watermark()`` didn't properly determine watermark placement.
-  Fixed a bug where :doc:`HTML Table Library <libraries/table>` ignored its *auto_heading* setting if headings were not already set.
-  Fixed a bug (#2364) - :doc:`Pagination Library <libraries/pagination>` appended the query string (if used) multiple times when there are successive calls to ``create_links()`` with no ``initialize()`` in between them.
-  Partially fixed a bug (#261) - UTF-8 class method ``clean_string()`` generating log messages and/or not producing the desired result due to an upstream bug in iconv.
-  Fixed a bug where ``CI_Xmlrpcs::parseRequest()`` could fail if ``$HTTP_RAW_POST_DATA`` is not populated.
-  Fixed a bug in :doc:`Zip Library <libraries/zip>` internal method ``_get_mod_time()`` where it was not parsing result returned by ``filemtime()``.
-  Fixed a bug (#3161) - :doc:`Cache Library <libraries/caching>` methods `increment()`, `decrement()` didn't auto-create non-existent items when using redis and/or file storage.
-  Fixed a bug (#3189) - :doc:`Parser Library <libraries/parser>` used double replacement on ``key->value`` pairs, exposing a potential template injection vulnerability.
-  Fixed a bug (#3573) - :doc:`Email Library <libraries/email>` violated `RFC5321 <https://tools.ietf.org/rfc/rfc5321.txt>`_ by sending 'localhost.localdomain' as a hostname.
-  Fixed a bug (#3572) - ``CI_Security::_remove_evil_attributes()`` failed for large-sized inputs due to *pcre.backtrack_limit* and didn't properly match HTML tags.

Version 2.2.3
=============

Release Date: July 14, 2015

-  Security

   - Removed a fallback to ``mysql_escape_string()`` in the 'mysql' database driver (``escape_str()`` method) when there's no active database connection.

Version 2.2.2
=============

Release Date: April 15, 2015

-  General Changes

   - Added HTTP "Host" header character validation to prevent cache poisoning attacks when *base_url* auto-detection is used.
   - Added *FSCommand* and *seekSegmentTime* to the "evil attributes" list in ``CI_Security::xss_clean()``.

Bug fixes for 2.2.2
-------------------

-  Fixed a bug (#3665) - ``CI_Security::entity_decode()`` triggered warnings under some circumstances.

Version 2.2.1
=============

Release Date: January 22, 2015

-  General Changes

   - Improved security in ``xss_clean()``.
   - Updated timezones in :doc:`Date Helper <helpers/date_helper>`.

Bug fixes for 2.2.1
-------------------

-  Fixed a bug (#3094) - Internal method ``CI_Input::_clean_input_data()`` breaks encrypted session cookies.
-  Fixed a bug (#2268) - :doc:`Security Library <libraries/security>` method ``xss_clean()`` didn't properly match JavaScript events.
-  Fixed a bug (#3309) - :doc:`Security Library <libraries/security>` method ``xss_clean()`` used an overly-invasive pattern to strip JS event handlers.
-  Fixed a bug (#2771) - :doc:`Security Library <libraries/security>` method ``xss_clean()`` didn't take into account HTML5 entities.
-  Fixed a bug (#73) - :doc:`Security Library <libraries/security>` method ``sanitize_filename()`` could be tricked by an XSS attack.
-  Fixed a bug (#2681) - :doc:`Security Library <libraries/security>` method ``entity_decode()`` used the ``PREG_REPLACE_EVAL`` flag, which is deprecated since PHP 5.5.
-  Fixed a bug (#3302) - Internal function ``get_config()`` triggered an E_NOTICE message on PHP 5.6.
-  Fixed a bug (#2508) - :doc:`Config Library <libraries/config>` didn't properly detect if the current request is via HTTPS.
-  Fixed a bug (#3314) - SQLSRV :doc:`Database driver <database/index>`'s method ``count_all()`` didn't escape the supplied table name.
-  Fixed a bug (#3404) - MySQLi :doc:`Database driver <database/index>`'s method ``escape_str()`` had a wrong fallback to ``mysql_escape_string()`` when there was no active connection.
-  Fixed a bug in the :doc:`Session Library <libraries/sessions>` where session ID regeneration occurred during AJAX requests.

Version 2.2.0
=============

Release Date: June 2, 2014

-  General Changes

   - Security: :doc:`Encrypt Library <libraries/encrypt>` method ``xor_encode()`` has been removed. The Encrypt Class now requires the Mcrypt extension to be installed.
   - Security: The :doc:`Session Library <libraries/sessions>` now uses HMAC authentication instead of a simple MD5 checksum.

Bug fixes for 2.2.0
-------------------

-  Fixed an edge case (#2583) in the :doc:`Email Library <libraries/email>` where `Suhosin <http://www.hardened-php.net/suhosin/>` blocked messages sent via ``mail()`` due to trailing newspaces in headers.
-  Fixed a bug (#696) - make ``oci_execute()`` calls inside ``num_rows()`` non-committing, since they are only there to reset which row is next in line for oci_fetch calls and thus don't need to be committed.
-  Fixed a bug (#2689) - :doc:`Database Force <database/forge>` methods ``create_table()``, ``drop_table()`` and ``rename_table()`` produced broken SQL for tge 'sqlsrv' driver.
-  Fixed a bug (#2427) - PDO :doc:`Database driver <database/index>` didn't properly check for query failures.
-  Fixed a bug in the :doc:`Session Library <libraries/sessions>` where authentication was not performed for encrypted cookies.

Version 2.1.4
=============

Release Date: July 8, 2013

-  General Changes

   - Improved security in ``xss_clean()``.

Bug fixes for 2.1.4
-------------------

-  Fixed a bug (#1936) - :doc:`Migration Library <libraries/migration>` method ``latest()`` had a typo when retrieving language values.
-  Fixed a bug (#2021) - :doc:`Migration Library <libraries/migration>` configuration file was mistakenly using Windows style line feeds.
-  Fixed a bug (#1273) - ``E_NOTICE`` being generated by :doc:`Query Builder <database/query_builder>`'s ``set_update_batch()`` method.
-  Fixed a bug (#2337) - :doc:`Email Library <libraries/email>` method ``print_debugger()`` didn't apply ``htmlspecialchars()`` to headers.

Version 2.1.3
=============

Release Date: October 8, 2012

-  Core

   - :doc:`Common function <general/common_functions>` ``is_loaded()`` now returns a reference.

Bug fixes for 2.1.3
-------------------

-  Fixed a bug (#1543) - File-based :doc:`Caching <libraries/caching>` method ``get_metadata()`` used a non-existent array key to look for the TTL value.
-  Fixed a bug (#1314) - :doc:`Session Library <libraries/sessions>` method ``sess_destroy()`` didn't destroy the userdata array.
-  Fixed a bug (#804) - :doc:`Profiler library <general/profiling>` was trying to handle objects as strings in some cases, resulting in *E_WARNING* messages being issued by ``htmlspecialchars()``.
-  Fixed a bug (#1699) - :doc:`Migration Library <libraries/migration>` ignored the ``$config['migration_path']`` setting.
-  Fixed a bug (#227) - :doc:`Input Library <libraries/input>` allowed unconditional spoofing of HTTP clients' IP addresses through the *HTTP_CLIENT_IP* header.
-  Fixed a bug (#907) - :doc:`Input Library <libraries/input>` ignored *HTTP_X_CLUSTER_CLIENT_IP* and *HTTP_X_CLIENT_IP* headers when checking for proxies.
-  Fixed a bug (#940) - ``csrf_verify()`` used to set the CSRF cookie while processing a POST request with no actual POST data, which resulted in validating a request that should be considered invalid.
-  Fixed a bug (#499) - :doc:`Security Library <libraries/security>` where a CSRF cookie was created even if ``$config['csrf_protection']`` is set to FALSE.
-  Fixed a bug (#1715) - :doc:`Input Library <libraries/input>` triggered ``csrf_verify()`` on CLI requests.
-  Fixed a bug (#751) - :doc:`Query Builder <database/query_builder>` didn't properly handle cached field escaping overrides.
-  Fixed a bug (#2004) - :doc:`Query Builder <database/query_builder>` didn't properly merge cached calls with non-cache ones.

Version 2.1.2
=============

Release Date: June 29, 2012

-  General Changes

   -  Improved security in ``xss_clean()``.

Version 2.1.1
=============

Release Date: June 12, 2012

-  General Changes

   -  Fixed support for docx, xlsx files in mimes.php.

-  Libraries

   -  Further improved MIME type detection in the :doc:`File Uploading Library <libraries/file_uploading>`.
   -  Added support for IPv6 to the :doc:`Input Library <libraries/input>`.
   -  Added support for the IP format parameter to the :doc:`Form Validation Library <libraries/form_validation>`.

-  Helpers

   -  ``url_title()`` performance and output improved. You can now use any string as the word delimiter, but 'dash' and 'underscore' are still supported.

Bug fixes for 2.1.1
-------------------

-  Fixed a bug (#697) - A wrong array key was used in the :doc:`File Uploading Library <libraries/file_uploading>` to check for mime-types.
-  Fixed a bug - ``form_open()`` compared $action against ``site_url()`` instead of ``base_url()``.
-  Fixed a bug - ``CI_Upload::_file_mime_type()`` could've failed if ``mime_content_type()`` is used for the detection and returns FALSE.
-  Fixed a bug (#538) - Windows paths were ignored when using the :doc:`Image Manipulation Library <libraries/image_lib>` to create a new file.
-  Fixed a bug - When database caching was enabled, $this->db->query() checked the cache before binding variables which resulted in cached queries never being found.
-  Fixed a bug - CSRF cookie value was allowed to be any (non-empty) string before being written to the output, making code injection a risk.
-  Fixed a bug (#726) - PDO put a 'dbname' argument in its connection string regardless of the database platform in use, which made it impossible to use SQLite.
-  Fixed a bug - ``CI_DB_pdo_driver::num_rows()`` was not returning properly value with SELECT queries, cause it was relying on ``PDOStatement::rowCount()``.
-  Fixed a bug (#1059) - ``CI_Image_lib::clear()`` was not correctly clearing all necessary object properties, namely width and height.

Version 2.1.0
=============

Release Date: November 14, 2011

-  General Changes

   -  Callback validation rules can now accept parameters like any other
      validation rule.
   -  Added html_escape() to :doc:`Common
      functions <general/common_functions>` to escape HTML output
      for preventing XSS.

-  Helpers

   -  Added increment_string() to :doc:`String
      Helper <helpers/string_helper>` to turn "foo" into "foo-1"
      or "foo-1" into "foo-2".
   -  Altered form helper - made action on form_open_multipart helper
      function call optional. Fixes (#65)
   -  url_title() will now trim extra dashes from beginning and end.
   -  Improved speed of :doc:`String Helper <helpers/string_helper>`'s random_string() method

-  Database

   -  Added a `CUBRID <http://www.cubrid.org/>`_ driver to the :doc:`Database
      Driver <database/index>`. Thanks to the CUBRID team for
      supplying this patch.
   -  Added a PDO driver to the :doc:`Database Driver <database/index>`.
   -  Typecast limit and offset in the :doc:`Database
      Driver <database/queries>` to integers to avoid possible
      injection.
   -  Added additional option 'none' for the optional third argument for
      $this->db->like() in the :doc:`Database
      Driver <database/query_builder>`.
   -  Added $this->db->insert_batch() support to the OCI8 (Oracle) driver.
   -  Added failover if the main connections in the config should fail

-  Libraries

   -  Changed ``$this->cart->insert()`` in the *Cart Library*
      to return the Row ID if a single item was inserted successfully.
   -  Added support to set an optional parameter in your callback rules
      of validation using the :doc:`Form Validation
      Library <libraries/form_validation>`.
   -  Added a :doc:`Migration library <libraries/migration>` to assist with applying
      incremental updates to your database schema.
   -  Driver children can be located in any package path.
   -  Added max_filename_increment config setting for Upload library.
   -  Added ``is_unique`` to the :doc:`Form Validation library <libraries/form_validation>`.
   -  Added $config['use_page_numbers'] to the :doc:`Pagination library <libraries/pagination>`, which enables real page numbers in the URI.
   -  Added TLS and SSL Encryption for SMTP.

-  Core

   -  Changed private functions in CI_URI to protected so MY_URI can
      override them.
   -  Removed CI_CORE boolean constant from CodeIgniter.php (no longer Reactor and Core versions).

Bug fixes for 2.1.0
-------------------

-  Fixed #378 Robots identified as regular browsers by the User Agent
   class.
-  If a config class was loaded first then a library with the same name
   is loaded, the config would be ignored.
-  Fixed a bug (Reactor #19) where 1) the 404_override route was being
   ignored in some cases, and 2) auto-loaded libraries were not
   available to the 404_override controller when a controller existed
   but the requested method did not.
-  Fixed a bug (Reactor #89) where MySQL export would fail if the table
   had hyphens or other non alphanumeric/underscore characters.
-  Fixed a bug (#105) that stopped query errors from being logged unless database debugging was enabled
-  Fixed a bug (#160) - Removed unneeded array copy in the file cache
   driver.
-  Fixed a bug (#150) - field_data() now correctly returns column
   length.
-  Fixed a bug (#8) - load_class() now looks for core classes in
   APPPATH first, allowing them to be replaced.
-  Fixed a bug (#24) - ODBC database driver called incorrect parent in __construct().
-  Fixed a bug (#85) - OCI8 (Oracle) database escape_str() function did not escape correct.
-  Fixed a bug (#344) - Using schema found in :doc:`Saving Session Data to a Database <libraries/sessions>`, system would throw error "user_data does not have a default value" when deleting then creating a session.
-  Fixed a bug (#112) - OCI8 (Oracle) driver didn't pass the configured database character set when connecting.
-  Fixed a bug (#182) - OCI8 (Oracle) driver used to re-execute the statement whenever num_rows() is called.
-  Fixed a bug (#82) - WHERE clause field names in the DB update_string() method were not escaped, resulting in failed queries in some cases.
-  Fixed a bug (#89) - Fix a variable type mismatch in DB display_error() where an array is expected, but a string could be set instead.
-  Fixed a bug (#467) - Suppress warnings generated from get_magic_quotes_gpc() (deprecated in PHP 5.4)
-  Fixed a bug (#484) - First time _csrf_set_hash() is called, hash is never set to the cookie (in Security.php).
-  Fixed a bug (#60) - Added _file_mime_type() method to the :doc:`File Uploading Library <libraries/file_uploading>` in order to fix a possible MIME-type injection.
-  Fixed a bug (#537) - Support for all wav type in browser.
-  Fixed a bug (#576) - Using ini_get() function to detect if apc is enabled or not.
-  Fixed invalid date time format in :doc:`Date helper <helpers/date_helper>` and :doc:`XMLRPC library <libraries/xmlrpc>`.
-  Fixed a bug (#200) - MySQL queries would be malformed after calling db->count_all() then db->get().

Version 2.0.3
=============

Release Date: August 20, 2011

-  Security

   -  An improvement was made to the MySQL and MySQLi drivers to prevent
      exposing a potential vector for SQL injection on sites using
      multi-byte character sets in the database client connection.
      An incompatibility in PHP versions < 5.2.3 and MySQL < 5.0.7 with
      *mysql_set_charset()* creates a situation where using multi-byte
      character sets on these environments may potentially expose a SQL
      injection attack vector. Latin-1, UTF-8, and other "low ASCII"
      character sets are unaffected on all environments.

      If you are running or considering running a multi-byte character
      set for your database connection, please pay close attention to
      the server environment you are deploying on to ensure you are not
      vulnerable.

-  General Changes

   -  Fixed a bug where there was a misspelling within a code comment in
      the index.php file.
   -  Added Session Class userdata to the output profiler. Additionally,
      added a show/hide toggle on HTTP Headers, Session Data and Config
      Variables.
   -  Removed internal usage of the EXT constant.
   -  Visual updates to the welcome_message view file and default error
      templates. Thanks to `danijelb <https://bitbucket.org/danijelb>`_
      for the pull request.
   -  Added insert_batch() function to the PostgreSQL database driver.
      Thanks to epallerols for the patch.
   -  Added "application/x-csv" to mimes.php.
   -  Fixed a bug where :doc:`Email library <libraries/email>`
      attachments with a "." in the name would using invalid MIME-types.

-  Helpers

   -  Added an optional third parameter to heading() which allows adding
      html attributes to the rendered heading tag.
   -  form_open() now only adds a hidden (Cross-site Reference Forgery)
      protection field when the form's action is internal and is set to
      the post method. (Reactor #165)
   -  Re-worked plural() and singular() functions in the :doc:`Inflector
      helper <helpers/inflector_helper>` to support considerably
      more words.

-  Libraries

   -  Altered Session to use a longer match against the user_agent
      string. See upgrade notes if using database sessions.
   -  Added $this->db->set_dbprefix() to the :doc:`Database
      Driver <database/queries>`.
   -  Changed ``$this->cart->insert()`` in the *Cart Library*
      to return the Row ID if a single item was inserted successfully.
   -  Added $this->load->get_var() to the :doc:`Loader
      library <libraries/loader>` to retrieve global vars set with
      $this->load->view() and $this->load->vars().
   -  Changed $this->db->having() to insert quotes using escape() rather
      than escape_str().

Bug fixes for 2.0.3
-------------------

-  Added ENVIRONMENT to reserved constants. (Reactor #196)
-  Changed server check to ensure SCRIPT_NAME is defined. (Reactor #57)
-  Removed APPPATH.'third_party' from the packages autoloader to negate
   needless file stats if no packages exist or if the developer does not
   load any other packages by default.
-  Fixed a bug (Reactor #231) where Sessions Library database table
   example SQL did not contain an index on last_activity. See :doc:`Upgrade
   Notes <installation/upgrade_203>`.
-  Fixed a bug (Reactor #229) where the Sessions Library example SQL in
   the documentation contained incorrect SQL.
-  Fixed a bug (Core #340) where when passing in the second parameter to
   $this->db->select(), column names in subsequent queries would not be
   properly escaped.
-  Fixed issue #199 - Attributes passed as string does not include a
   space between it and the opening tag.
-  Fixed a bug where the method ``$this->cart->total_items()`` from
   *Cart Library* now returns the sum of the quantity
   of all items in the cart instead of your total count.
-  Fixed a bug where not setting 'null' when adding fields in db_forge
   for mysql and mysqli drivers would default to NULL instead of NOT
   NULL as the docs suggest.
-  Fixed a bug where using $this->db->select_max(),
   $this->db->select_min(), etc could throw notices. Thanks to w43l for
   the patch.
-  Replace checks for STDIN with php_sapi_name() == 'cli' which on the
   whole is more reliable. This should get parameters in crontab
   working.

Version 2.0.2
=============

Release Date: April 7, 2011
Hg Tag: v2.0.2

-  General changes

   -  The :doc:`Security library <./libraries/security>` was moved to
      the core and is now loaded automatically. Please remove your
      loading calls.
   -  The CI_SHA class is now deprecated. All supported versions of PHP
      provide a sha1() function.
   -  constants.php will now be loaded from the environment folder if
      available.
   -  Added language key error logging
   -  Made Environment Support optional. Comment out or delete the
      constant to stop environment checks.
   -  Added Environment Support for Hooks.
   -  Added CI\_ Prefix to the :doc:`Cache driver <libraries/caching>`.
   -  Added :doc:`CLI usage <./general/cli>` documentation.

-  Helpers

   -  Removed the previously deprecated ``dohash()`` from the :doc:`Security
      helper <./helpers/security_helper>`; use ``do_hash()`` instead.
   -  Changed the 'plural' function so that it doesn't ruin the
      captalization of your string. It also take into consideration
      acronyms which are all caps.

-  Database

   -  $this->db->count_all_results() will now return an integer
      instead of a string.

Bug fixes for 2.0.2
-------------------

-  Fixed a bug (Reactor #145) where the Output Library had
   parse_exec_vars set to protected.
-  Fixed a bug (Reactor #80) where is_really_writable would create an
   empty file when on Windows or with safe_mode enabled.
-  Fixed various bugs with User Guide.
-  Added is_cli_request() method to documentation for :doc:`Input
   class <libraries/input>`.
-  Added form_validation_lang entries for decimal, less_than and
   greater_than.
-  Fixed issue #153 Escape Str Bug in MSSQL driver.
-  Fixed issue #172 Google Chrome 11 posts incorrectly when action is empty.

Version 2.0.1
=============

Release Date: March 15, 2011
Hg Tag: v2.0.1

-  General changes

   -  Added $config['cookie_secure'] to the config file to allow
      requiring a secure (HTTPS) in order to set cookies.
   -  Added the constant CI_CORE to help differentiate between Core:
      TRUE and Reactor: FALSE.
   -  Added an ENVIRONMENT constant in index.php, which affects PHP
      error reporting settings, and optionally, which configuration
      files are loaded (see below). Read more on the :doc:`Handling
      Environments <general/environments>` page.
   -  Added support for
      :ref:`environment-specific <config-environments>`
      configuration files.

-  Libraries

   -  Added decimal, less_than and greater_than rules to the :doc:`Form
      validation Class <libraries/form_validation>`.
   -  :doc:`Input Class <libraries/input>` methods post() and get()
      will now return a full array if the first argument is not
      provided.
   -  Secure cookies can now be made with the set_cookie() helper and
      :doc:`Input Class <libraries/input>` method.
   -  Added set_content_type() to :doc:`Output
      Class <libraries/output>` to set the output Content-Type
      HTTP header based on a MIME Type or a config/mimes.php array key.
   -  :doc:`Output Class <libraries/output>` will now support method
      chaining.

-  Helpers

   -  Changed the logic for form_open() in :doc:`Form
      helper <helpers/form_helper>`. If no value is passed it will
      submit to the current URL.

Bug fixes for 2.0.1
-------------------

-  CLI requests can now be run from any folder, not just when CD'ed next
   to index.php.
-  Fixed issue #41: Added audio/mp3 mime type to mp3.
-  Fixed a bug (Core #329) where the file caching driver referenced the
   incorrect cache directory.
-  Fixed a bug (Reactor #69) where the SHA1 library was named
   incorrectly.

.. _2.0.0-changelog:

Version 2.0.0
=============

Release Date: January 28, 2011
Hg Tag: v2.0.0

-  General changes

   -  PHP 4 support is removed. CodeIgniter now requires PHP 5.1.6.
   -  Scaffolding, having been deprecated for a number of versions, has
      been removed.
   -  Plugins have been removed, in favor of Helpers. The CAPTCHA plugin
      has been converted to a Helper and
      :doc:`documented <./helpers/captcha_helper>`. The JavaScript
      calendar plugin was removed due to the ready availability of great
      JavaScript calendars, particularly with jQuery.
   -  Added new special Library type:
      :doc:`Drivers <./general/drivers>`.
   -  Added full query-string support. See the config file for details.
   -  Moved the application folder outside of the system folder.
   -  Moved system/cache and system/logs directories to the application
      directory.
   -  Added routing overrides to the main index.php file, enabling the
      normal routing to be overridden on a per "index" file basis.
   -  Added the ability to set config values (or override config values)
      directly from data set in the main index.php file. This allows a
      single application to be used with multiple front controllers,
      each having its own config values.
   -  Added $config['directory_trigger'] to the config file so that a
      controller sub-directory can be specified when running _GET
      strings instead of URI segments.
   -  Added ability to set "Package" paths - specific paths where the
      Loader and Config classes should try to look first for a requested
      file. This allows distribution of sub-applications with their own
      libraries, models, config files, etc. in a single "package"
      directory. See the :doc:`Loader class <libraries/loader>`
      documentation for more details.
   -  In-development code is now hosted at BitBucket .
   -  Removed the deprecated Validation Class.
   -  Added CI\_ Prefix to all core classes.
   -  Package paths can now be set in application/config/autoload.php.
   -  :doc:`Upload library <libraries/file_uploading>` file_name can
      now be set without an extension, the extension will be taken from
      the uploaded file instead of the given name.
   -  In :doc:`Database Forge <database/forge>` the name can be omitted
      from $this->dbforge->modify_column()'s 2nd param if you aren't
      changing the name.
   -  $config['base_url'] is now empty by default and will guess what
      it should be.
   -  Enabled full Command Line Interface compatibility with
      config['uri_protocol'] = 'CLI';.

-  Libraries

   -  Added a :doc:`Cache driver <libraries/caching>` with APC,
      memcached, and file-based support.
   -  Added $prefix, $suffix and $first_url properties to :doc:`Pagination
      library <./libraries/pagination>`.
   -  Added the ability to suppress first, previous, next, last, and
      page links by setting their values to FALSE in the :doc:`Pagination
      library <./libraries/pagination>`.
   -  Added :doc:`Security library <./libraries/security>`, which now
      contains the xss_clean function, filename_security function and
      other security related functions.
   -  Added CSRF (Cross-site Reference Forgery) protection to the
      :doc:`Security library <./libraries/security>`.
   -  Added $parse_exec_vars property to Output library.
   -  Added ability to enable / disable individual sections of the
      :doc:`Profiler <general/profiling>`
   -  Added a wildcard option $config['allowed_types'] = '\*' to the
      :doc:`File Uploading Class <./libraries/file_uploading>`.
   -  Added an 'object' config variable to the XML-RPC Server library so
      that one can specify the object to look for requested methods,
      instead of assuming it is in the $CI superobject.
   -  Added "is_object" into the list of unit tests capable of being
      run.
   -  Table library will generate an empty cell with a blank string, or
      NULL value.
   -  Added ability to set tag attributes for individual cells in the
      Table library
   -  Added a parse_string() method to the :doc:`Parser
      Class <libraries/parser>`.
   -  Added HTTP headers and Config information to the
      :doc:`Profiler <general/profiling>` output.
   -  Added Chrome and Flock to the list of detectable browsers by
      browser() in the :doc:`User Agent Class <libraries/user_agent>`.
   -  The :doc:`Unit Test Class <libraries/unit_testing>` now has an
      optional "notes" field available to it, and allows for discrete
      display of test result items using
      $this->unit->set_test_items().
   -  Added a $xss_clean class variable to the XMLRPC library, enabling
      control over the use of the Security library's xss_clean()
      method.
   -  Added a download() method to the :doc:`FTP
      library <libraries/ftp>`
   -  Changed do_xss_clean() to return FALSE if the uploaded file
      fails XSS checks.
   -  Added stripslashes() and trim()ing of double quotes from $_FILES
      type value to standardize input in Upload library.
   -  Added a second parameter (boolean) to
      $this->zip->read_dir('/path/to/directory', FALSE) to remove the
      preceding trail of empty folders when creating a Zip archive. This
      example would contain a zip with "directory" and all of its
      contents.
   -  Added ability in the Image Library to handle PNG transparency for
      resize operations when using the GD lib.
   -  Modified the Session class to prevent use if no encryption key is
      set in the config file.
   -  Added a new config item to the Session class
      sess_expire_on_close to allow sessions to auto-expire when the
      browser window is closed.
   -  Improved performance of the Encryption library on servers where
      Mcrypt is available.
   -  Changed the default encryption mode in the Encryption library to
      CBC.
   -  Added an encode_from_legacy() method to provide a way to
      transition encrypted data from CodeIgniter 1.x to CodeIgniter 2.x.
      Please see the :doc:`upgrade
      instructions <./installation/upgrade_200>` for details.
   -  Altered Form_Validation library to allow for method chaining on
      set_rules(), set_message() and set_error_delimiters()
      functions.
   -  Altered Email Library to allow for method chaining.
   -  Added request_headers(), get_request_header() and
      is_ajax_request() to the input class.
   -  Altered :doc:`User agent library <libraries/user_agent>` so that
      is_browser(), is_mobile() and is_robot() can optionally check
      for a specific browser or mobile device.
   -  Altered :doc:`Input library <libraries/input>` so that post() and
      get() will return all POST and GET items (respectively) if there
      are no parameters passed in.

-  Database

   -  :doc:`database configuration <./database/configuration>`.
   -  Added autoinit value to :doc:`database
      configuration <./database/configuration>`.
   -  Added stricton value to :doc:`database
      configuration <./database/configuration>`.
   -  Added database_exists() to the :doc:`Database Utilities
      Class <database/utilities>`.
   -  Semantic change to db->version() function to allow a list of
      exceptions for databases with functions to return version string
      instead of specially formed SQL queries. Currently this list only
      includes Oracle and SQLite.
   -  Fixed a bug where driver specific table identifier protection
      could lead to malformed queries in the field_data() functions.
   -  Fixed a bug where an undefined class variable was referenced in
      database drivers.
   -  Modified the database errors to show the filename and line number
      of the problematic query.
   -  Removed the following deprecated functions: orwhere, orlike,
      groupby, orhaving, orderby, getwhere.
   -  Removed deprecated _drop_database() and _create_database()
      functions from the db utility drivers.
   -  Improved dbforge create_table() function for the Postgres driver.

-  Helpers

   -  Added convert_accented_characters() function to :doc:`text
      helper <./helpers/text_helper>`.
   -  Added accept-charset to the list of inserted attributes of
      form_open() in the :doc:`Form Helper <helpers/form_helper>`.
   -  Deprecated the ``dohash()`` function in favour of ``do_hash()`` for
      naming consistency.
   -  Non-backwards compatible change made to get_dir_file_info() in
      the :doc:`File Helper <helpers/file_helper>`. No longer recurses
      by default so as to encourage responsible use (this function can
      cause server performance issues when used without caution).
   -  Modified the second parameter of directory_map() in the
      :doc:`Directory Helper <helpers/directory_helper>` to accept an
      integer to specify recursion depth.
   -  Modified delete_files() in the :doc:`File
      Helper <helpers/file_helper>` to return FALSE on failure.
   -  Added an optional second parameter to byte_format() in the
      :doc:`Number Helper <helpers/number_helper>` to allow for decimal
      precision.
   -  Added alpha, and sha1 string types to random_string() in the
      :doc:`String Helper <helpers/string_helper>`.
   -  Modified prep_url() so as to not prepend http&#58;// if the supplied
      string already has a scheme.
   -  Modified get_file_info in the file helper, changing filectime()
      to filemtime() for dates.
   -  Modified ``smiley_js()`` to add optional third parameter to return
      only the javascript with no script tags.
   -  The img() function of the :doc:`HTML
      helper <./helpers/html_helper>` will now generate an empty
      string as an alt attribute if one is not provided.
   -  If CSRF is enabled in the application config file, form_open()
      will automatically insert it as a hidden field.
   -  Added sanitize_filename() into the :doc:`Security
      helper <./helpers/security_helper>`.
   -  Added ellipsize() to the :doc:`Text
      Helper <./helpers/text_helper>`
   -  Added elements() to the :doc:`Array
      Helper <./helpers/array_helper>`

-  Other Changes

   -  Added an optional second parameter to show_404() to disable
      logging.
   -  Updated loader to automatically apply the sub-class prefix as an
      option when loading classes. Class names can be prefixed with the
      standard "CI\_" or the same prefix as the subclass prefix, or no
      prefix at all.
   -  Increased randomness with is_really_writable() to avoid file
      collisions when hundreds or thousands of requests occur at once.
   -  Switched some DIR_WRITE_MODE constant uses to FILE_WRITE_MODE
      where files and not directories are being operated on.
   -  get_mime_by_extension() is now case insensitive.
   -  Added "default" to the list :doc:`Reserved
      Names <general/reserved_names>`.
   -  Added 'application/x-msdownload' for .exe files and
      'application/x-gzip-compressed' for .tgz files to
      config/mimes.php.
   -  Updated the output library to no longer compress output or send
      content-length headers if the server runs with
      zlib.output_compression enabled.
   -  Eliminated a call to is_really_writable() on each request unless
      it is really needed (Output caching)
   -  Documented append_output() in the :doc:`Output
      Class <libraries/output>`.
   -  Documented a second argument in the decode() function for the
      :doc:`Encrypt Class <libraries/encrypt>`.
   -  Documented db->close().
   -  Updated the router to support a default route with any number of
      segments.
   -  Moved _remove_invisible_characters() function from the
      :doc:`Security Library <libraries/security>` to :doc:`common
      functions. <general/common_functions>`
   -  Added audio/mpeg3 as a valid mime type for MP3.

Bug fixes for 2.0.0
-------------------

-  Fixed a bug where you could not change the User-Agent when sending
   email.
-  Fixed a bug where the Output class would send incorrect cached output
   for controllers implementing their own _output() method.
-  Fixed a bug where a failed query would not have a saved query
   execution time causing errors in the Profiler
-  Fixed a bug that was writing log entries when multiple identical
   helpers and plugins were loaded.
-  Fixed assorted user guide typos or examples (#10693, #8951, #7825,
   #8660, #7883, #6771, #10656).
-  Fixed a language key in the profiler: "profiler_no_memory_usage"
   to "profiler_no_memory".
-  Fixed an error in the Zip library that didn't allow downloading on
   PHP 4 servers.
-  Fixed a bug in the Form Validation library where fields passed as
   rule parameters were not being translated (#9132)
-  Modified inflector helper to properly pluralize words that end in
   'ch' or 'sh'
-  Fixed a bug in xss_clean() that was not allowing hyphens in query
   strings of submitted URLs.
-  Fixed bugs in get_dir_file_info() and get_file_info() in the
   File Helper with recursion, and file paths on Windows.
-  Fixed a bug where Active Record override parameter would not let you
   disable Active Record if it was enabled in your database config file.
-  Fixed a bug in reduce_double_slashes() in the String Helper to
   properly remove duplicate leading slashes (#7585)
-  Fixed a bug in values_parsing() of the XML-RPC library which
   prevented NULL variables typed as 'string' from being handled
   properly.
-  Fixed a bug were form_open_multipart() didn't accept string
   attribute arguments (#10930).
-  Fixed a bug (#10470) where get_mime_by_extension() was case
   sensitive.
-  Fixed a bug where some error messages for the SQLite and Oracle
   drivers would not display.
-  Fixed a bug where files created with the Zip Library would result in
   file creation dates of 1980.
-  Fixed a bug in the Session library that would result in PHP error
   when attempting to store values with objects.
-  Fixed a bug where extending the Controller class would result in a
   fatal PHP error.
-  Fixed a PHP Strict Standards Error in the index.php file.
-  Fixed a bug where getimagesize() was being needlessly checked on
   non-image files in is_allowed_type().
-  Fixed a bug in the Encryption library where an empty key was not
   triggering an error.
-  Fixed a bug in the Email library where CC and BCC recipients were not
   reset when using the clear() method (#109).
-  Fixed a bug in the URL Helper where prep_url() could cause a PHP
   error on PHP versions < 5.1.2.
-  Added a log message in core/output if the cache directory config
   value was not found.
-  Fixed a bug where multiple libraries could not be loaded by passing
   an array to load->library()
-  Fixed a bug in the html helper where too much white space was
   rendered between the src and alt tags in the img() function.
-  Fixed a bug in the profilers _compile_queries() function.
-  Fixed a bug in the date helper where the DATE_ISO8601 variable was
   returning an incorrectly formatted date string.

Version 1.7.2
=============

Release Date: September 11, 2009
Hg Tag: v1.7.2

-  Libraries

   -  Added a new *Cart Class*.
   -  Added the ability to pass $config['file_name'] for the :doc:`File
      Uploading Class <libraries/file_uploading>` and rename the
      uploaded file.
   -  Changed order of listed user-agents so Safari would more
      accurately report itself. (#6844)

-  Database

   -  Switched from using gettype() in escape() to is\_* methods, since
      future PHP versions might change its output.
   -  Updated all database drivers to handle arrays in escape_str()
   -  Added escape_like_str() method for escaping strings to be used
      in LIKE conditions
   -  Updated Active Record to utilize the new LIKE escaping mechanism.
   -  Added reconnect() method to DB drivers to try to keep alive /
      reestablish a connection after a long idle.
   -  Modified MSSQL driver to use mssql_get_last_message() for error
      messages.

-  Helpers

   -  Added form_multiselect() to the :doc:`Form
      helper <helpers/form_helper>`.
   -  Modified form_hidden() in the :doc:`Form
      helper <helpers/form_helper>` to accept multi-dimensional
      arrays.
   -  Modified ``form_prep()`` in the :doc:`Form
      helper <helpers/form_helper>` to keep track of prepped
      fields to avoid multiple prep/mutation from subsequent calls which
      can occur when using Form Validation and form helper functions to
      output form fields.
   -  Modified directory_map() in the :doc:`Directory
      helper <helpers/directory_helper>` to allow the inclusion of
      hidden files, and to return FALSE on failure to read directory.
   -  Modified the *Smiley helper* to work
      with multiple fields and insert the smiley at the last known
      cursor position.

-  General

   -  Compatible with PHP 5.3.0.
   -  Modified :doc:`show_error() <general/errors>` to allow sending
      of HTTP server response codes.
   -  Modified :doc:`show_404() <general/errors>` to send 404 status
      code, removing non-CGI compatible header() statement from
      error_404.php template.
   -  Added set_status_header() to the :doc:`Common
      functions <general/common_functions>` to allow use when the
      Output class is unavailable.
   -  Added is_php() to :doc:`Common
      functions <general/common_functions>` to facilitate PHP
      version comparisons.
   -  Added 2 CodeIgniter "cheatsheets" (thanks to DesignFellow.com for
      this contribution).

Bug fixes for 1.7.2
-------------------

-  Fixed assorted user guide typos or examples (#6743, #7214, #7516,
   #7287, #7852, #8224, #8324, #8349).
-  Fixed a bug in the Form Validation library where multiple callbacks
   weren't working (#6110)
-  doctype helper default value was missing a "1".
-  Fixed a bug in the language class when outputting an error for an
   unfound file.
-  Fixed a bug in the Calendar library where the shortname was output
   for "May".
-  Fixed a bug with ORIG_PATH_INFO that was allowing URIs of just a
   slash through.
-  Fixed a fatal error in the Oracle and ODBC drivers (#6752)
-  Fixed a bug where xml_from_result() was checking for a nonexistent
   method.
-  Fixed a bug where Database Forge's add_column and modify_column
   were not looping through when sent multiple fields.
-  Fixed a bug where the File Helper was using '/' instead of the
   DIRECTORY_SEPARATOR constant.
-  Fixed a bug to prevent PHP errors when attempting to use sendmail on
   servers that have manually disabled the PHP popen() function.
-  Fixed a bug that would cause PHP errors in XML-RPC data if the PHP
   data type did not match the specified XML-RPC type.
-  Fixed a bug in the XML-RPC class with parsing dateTime.iso8601 data
   types.
-  Fixed a case sensitive string replacement in xss_clean()
-  Fixed a bug in form_textarea() where form data was not prepped
   correctly.
-  Fixed a bug in ``form_prep()`` causing it to not preserve entities in
   the user's original input when called back into a form element
-  Fixed a bug in _protect_identifiers() where the swap prefix
   ($swap_pre) was not being observed.
-  Fixed a bug where the 400 status header sent with the 'disallowed URI
   characters' was not compatible with CGI environments.
-  Fixed a bug in the typography class where heading tags could have
   paragraph tags inserted when using auto_typography().

Version 1.7.1
=============

Release Date: February 10, 2009
Hg Tag: 1.7.1

-  Libraries

   -  Fixed an arbitrary script execution security flaw (#6068) in the
      Form Validation library (thanks to hkk)
   -  Changed default current page indicator in the Pagination library
      to use <strong> instead of <b>
   -  A "HTTP/1.1 400 Bad Request" header is now sent when disallowed
      characters are encountered.
   -  Added <big>, <small>, <q>, and <tt> to the Typography parser's
      inline elements.
   -  Added more accurate error reporting for the Email library when
      using sendmail.
   -  Removed a strict type check from the rotate() function of the
      :doc:`Image Manipulation Class <libraries/image_lib>`.
   -  Added enhanced error checking in file saving in the Image library
      when using the GD lib.
   -  Added an additional newline between multipart email headers and
      the MIME message text for better compatibility with a variety of
      MUAs.
   -  Made modest improvements to efficiency and accuracy of
      explode_name() in the Image lib.

-  Database

   -  Added where_in to the list of expected arguments received by
      delete().

-  Helpers

   -  Added the ability to have optgroups in form_dropdown() within the
      :doc:`form helper <helpers/form_helper>`.
   -  Added a doctype() function to the :doc:`HTML
      helper <helpers/html_helper>`.
   -  Added ability to force lowercase for url_title() in the :doc:`URL
      helper <helpers/url_helper>`.
   -  Changed the default "type" of form_button() to "button" from
      "submit" in the :doc:`form helper <helpers/form_helper>`.
   -  Changed redirect() in the URL helper to allow redirections to URLs
      outside of the CI site.
   -  Updated get_cookie() to try to fetch the cookie using the global
      cookie prefix if the requested cookie name doesn't exist.

-  Other Changes

   -  Improved security in xss_clean() to help prevent attacks
      targeting Internet Explorer.
   -  Added 'application/msexcel' to config/mimes.php for .xls files.
   -  Added 'proxy_ips' config item to whitelist reverse proxy servers
      from which to trust the HTTP_X_FORWARDED_FOR header to to
      determine the visitor's IP address.
   -  Improved accuracy of Upload::is_allowed_filetype() for images
      (#6715)

Bug fixes for 1.7.1
-------------------

-  Database

   -  Fixed a bug when doing 'random' on order_by() (#5706).
   -  Fixed a bug where adding a primary key through Forge could fail
      (#5731).
   -  Fixed a bug when using DB cache on multiple databases (#5737).
   -  Fixed a bug where TRUNCATE was not considered a "write" query
      (#6619).
   -  Fixed a bug where csv_from_result() was checking for a
      nonexistent method.
   -  Fixed a bug _protect_identifiers() where it was improperly
      removing all pipe symbols from items

-  Fixed assorted user guide typos or examples (#5998, #6093, #6259,
   #6339, #6432, #6521).
-  Fixed a bug in the MySQLi driver when no port is specified
-  Fixed a bug (#5702), in which the field label was not being fetched
   properly, when "matching" one field to another.
-  Fixed a bug in which identifers were not being escaped properly when
   reserved characters were used.
-  Fixed a bug with the regular expression used to protect submitted
   paragraph tags in auto typography.
-  Fixed a bug where double dashes within tag attributes were being
   converted to em dash entities.
-  Fixed a bug where double spaces within tag attributes were being
   converted to non-breaking space entities.
-  Fixed some accuracy issues with curly quotes in
   Typography::format_characters()
-  Changed a few docblock comments to reflect actual return values.
-  Fixed a bug with high ascii characters in subject and from email
   headers.
-  Fixed a bug in xss_clean() where whitespace following a validated
   character entity would not be preserved.
-  Fixed a bug where HTML comments and <pre> tags were being parsed in
   Typography::auto_typography().
-  Fixed a bug with non-breaking space cleanup in
   Typography::auto_typography().
-  Fixed a bug in database escaping where a compound statement (ie:
   SUM()) wasn't handled correctly with database prefixes.
-  Fixed a bug when an opening quote is preceded by a paragraph tag and
   immediately followed by another tag.
-  Fixed a bug in the Text Helper affecting some locales where
   word_censor() would not work on words beginning or ending with an
   accented character.
-  Fixed a bug in the Text Helper character limiter where the provided
   limit intersects the last word of the string.
-  Fixed a bug (#6342) with plural() in the Inflection helper with words
   ending in "y".
-  Fixed bug (#6517) where Routed URI segments returned by
   URI::rsegment() method were incorrect for the default controller.
-  Fixed a bug (#6706) in the Security Helper where xss_clean() was
   using a deprecated second argument.
-  Fixed a bug in the URL helper url_title() function where trailing
   periods were allowed at the end of a URL.
-  Fixed a bug (#6669) in the Email class when CRLF's are used for the
   newline character with headers when used with the "mail" protocol.
-  Fixed a bug (#6500) where URI::A_filter_uri() was exit()ing an
   error instead of using show_error().
-  Fixed a bug (#6592) in the File Helper where get_dir_file_info()
   where recursion was not occurring properly.
-  Tweaked Typography::auto_typography() for some edge-cases.

Version 1.7
===========

Release Date: October 23, 2008
Hg Tag: 1.7.0

-  Libraries

   -  Added a new :doc:`Form Validation
      Class <libraries/form_validation>`. It simplifies setting
      rules and field names, supports arrays as field names, allows
      groups of validation rules to be saved in a config file, and adds
      some helper functions for use in view files. **Please note that
      the old Validation class is now deprecated**. We will leave it in
      the library folder for some time so that existing applications
      that use it will not break, but you are encouraged to migrate to
      the new version.
   -  Updated the :doc:`Sessions class <libraries/sessions>` so that
      any custom data being saved gets stored to a database rather than
      the session cookie (assuming you are using a database to store
      session data), permitting much more data to be saved.
   -  Added the ability to store libraries in subdirectories within
      either the main "libraries" or the local application "libraries"
      folder. Please see the :doc:`Loader class <libraries/loader>` for
      more info.
   -  Added the ability to assign library objects to your own variable
      names when you use $this->load->library(). Please see the :doc:`Loader
      class <libraries/loader>` for more info.
   -  Added controller class/method info to :doc:`Profiler
      class <general/profiling>` and support for multiple database
      connections.
   -  Improved the "auto typography" feature and moved it out of the
      helper into its own :doc:`Typography
      Class <libraries/typography>`.
   -  Improved performance and accuracy of xss_clean(), including
      reduction of false positives on image/file tests.
   -  Improved :doc:`Parser class <./libraries/parser>` to allow
      multiple calls to the parse() function. The output of each is
      appended in the output.
   -  Added max_filename option to set a file name length limit in the
      :doc:`File Upload Class <libraries/file_uploading>`.
   -  Added set_status_header() function to :doc:`Output
      class <libraries/output>`.
   -  Modified :doc:`Pagination <libraries/pagination>` class to only
      output the "First" link when the link for page one would not be
      shown.
   -  Added support for mb_strlen in the :doc:`Form
      Validation <libraries/form_validation>` class so that
      multi-byte languages will calculate string lengths properly.

-  Database

   -  Improved Active Record class to allow full path column and table
      names: hostname.database.table.column. Also improved the alias
      handling.
   -  Improved how table and column names are escaped and prefixed. It
      now honors full path names when adding prefixes and escaping.
   -  Added Active Record caching feature to "update" and "delete"
      functions.
   -  Added removal of non-printing control characters in escape_str()
      of DB drivers that do not have native PHP escaping mechanisms
      (mssql, oci8, odbc), to avoid potential SQL errors, and possible
      sources of SQL injection.
   -  Added port support to MySQL, MySQLi, and MS SQL database drivers.
   -  Added driver name variable in each DB driver, based on bug report
      #4436.

-  Helpers

   -  Added several new "setting" functions to the :doc:`Form
      helper <helpers/form_helper>` that allow POST data to be
      retrieved and set into forms. These are intended to be used on
      their own, or with the new :doc:`Form Validation
      Class <libraries/form_validation>`.
   -  Added current_url() and uri_segments() to :doc:`URL
      helper <helpers/url_helper>`.
   -  Altered auto_link() in the :doc:`URL
      helper <helpers/url_helper>` so that email addresses with
      "+" included will be linked.
   -  Added meta() function to :doc:`HTML
      helper <helpers/html_helper>`.
   -  Improved accuracy of calculations in :doc:`Number
      helper <helpers/number_helper>`.
   -  Removed added newlines ("\\n") from most form and html helper
      functions.
   -  Tightened up validation in the :doc:`Date
      helper <helpers/date_helper>` function human_to_unix(),
      and eliminated the POSIX regex.
   -  Updated :doc:`Date helper <helpers/date_helper>` to match the
      world's current time zones and offsets.
   -  Modified url_title() in the :doc:`URL
      helper <helpers/url_helper>` to remove characters and digits
      that are part of character entities, to allow dashes, underscores,
      and periods regardless of the $separator, and to allow uppercase
      characters.
   -  Added support for arbitrary attributes in anchor_popup() of the
      :doc:`URL helper <helpers/url_helper>`.

-  Other Changes

   -  Added :doc:`PHP Style Guide <./general/styleguide>` to docs.
   -  Added sanitization in xss_clean() for a deprecated HTML tag that
      could be abused in user input in Internet Explorer.
   -  Added a few openxml document mime types, and an additional mobile
      agent to mimes.php and user_agents.php respectively.
   -  Added a file lock check during caching, before trying to write to
      the file.
   -  Modified Cookie key cleaning to unset a few troublesome key names
      that can be present in certain environments, preventing CI from
      halting execution.
   -  Changed the output of the profiler to use style attribute rather
      than clear, and added the id "codeigniter_profiler" to the
      container div.

Bug fixes for 1.7.0
-------------------

-  Fixed bug in xss_clean() that could remove some desirable tag
   attributes.
-  Fixed assorted user guide typos or examples (#4807, #4812, #4840,
   #4862, #4864, #4899, #4930, #5006, #5071, #5158, #5229, #5254,
   #5351).
-  Fixed an edit from 1.6.3 that made the $robots array in
   user_agents.php go poof.
-  Fixed a bug in the :doc:`Email library <libraries/email>` with
   quoted-printable encoding improperly encoding space and tab
   characters.
-  Modified XSS sanitization to no longer add semicolons after &[single
   letter], such as in M&M's, B&B, etc.
-  Modified XSS sanitization to no longer strip XHTML image tags of
   closing slashes.
-  Fixed a bug in the Session class when database sessions are used
   where upon session update all userdata would be errantly written to
   the session cookie.
-  Fixed a bug (#4536) in backups with the MySQL driver where some
   legacy code was causing certain characters to be double escaped.
-  Fixed a routing bug (#4661) that occurred when the default route
   pointed to a subfolder.
-  Fixed the spelling of "Dhaka" in the timezone_menu() function of the
   :doc:`Date helper. <helpers/date_helper>`
-  Fixed the spelling of "raspberry" in config/smileys.php.
-  Fixed incorrect parenthesis in form_open() function (#5135).
-  Fixed a bug that was ignoring case when comparing controller methods
   (#4560).
-  Fixed a bug (#4615) that was not setting SMTP authorization settings
   when using the initialize function.
-  Fixed a bug in highlight_code() in the :doc:`Text
   helper <helpers/text_helper>` that would leave a stray </span>
   in certain cases.
-  Fixed Oracle bug (#3306) that was preventing multiple queries in one
   action.
-  Fixed ODBC bug that was ignoring connection params due to its use of
   a constructor.
-  Fixed a DB driver bug with num_rows() that would cause an error with
   the Oracle driver.
-  Fixed MS SQL bug (#4915). Added brackets around database name in MS
   SQL driver when selecting the database, in the event that reserved
   characters are used in the name.
-  Fixed a DB caching bug (4718) in which the path was incorrect when no
   URI segments were present.
-  Fixed Image_lib class bug #4562. A path was not defined for NetPBM.
-  Fixed Image_lib class bug #4532. When cropping an image with
   identical height/width settings on output, a copy is made.
-  Fixed DB_driver bug (4900), in which a database error was not being
   logged correctly.
-  Fixed DB backup bug in which field names were not being escaped.
-  Fixed a DB Active Record caching bug in which multiple calls to
   cached data were not being honored.
-  Fixed a bug in the Session class that was disallowing slashes in the
   serialized array.
-  Fixed a Form Validation bug in which the "isset" error message was
   being trigged by the "required" rule.
-  Fixed a spelling error in a Loader error message.
-  Fixed a bug (5050) with IP validation with empty segments.
-  Fixed a bug in which the parser was being greedy if multiple
   identical sets of tags were encountered.

Version 1.6.3
=============

Release Date: June 26, 2008
Hg Tag: v1.6.3

Version 1.6.3 is a security and maintenance release and is recommended
for all users.

-  Database

   -  Modified MySQL/MySQLi Forge class to give explicit names to keys
   -  Added ability to set multiple column non-primary keys to the
      :doc:`Forge class <database/forge>`
   -  Added ability to set additional database config values in :doc:`DSN
      connections <database/connecting>` via the query string.

-  Libraries

   -  Set the mime type check in the :doc:`Upload
      class <libraries/file_uploading>` to reference the global
      mimes variable.
   -  Added support for query strings to the :doc:`Pagination
      class <libraries/pagination>`, automatically detected or
      explicitly declared.
   -  Added get_post() to the :doc:`Input class <libraries/input>`.
   -  Documented get() in the :doc:`Input class <libraries/input>`.
   -  Added the ability to automatically output language items as form
      labels in the :doc:`Language class <libraries/language>`.

-  Helpers

   -  Added a :doc:`Language helper <helpers/language_helper>`.
   -  Added a :doc:`Number helper <helpers/number_helper>`.
   -  :doc:`Form helper <helpers/form_helper>` refactored to allow
      form_open() and form_fieldset() to accept arrays or strings as
      arguments.

-  Other changes

   -  Improved security in xss_clean().
   -  Removed an unused Router reference in _display_cache().
   -  Added ability to :doc:`use xss_clean() to test
      images <libraries/input>` for XSS, useful for upload
      security.
   -  Considerably expanded list of mobile user-agents in
      config/user_agents.php.
   -  Charset information in the userguide has been moved above title
      for internationalization purposes (#4614).
   -  Added "Using Associative Arrays In a Request Parameter" example to
      the :doc:`XMLRPC userguide page <libraries/xmlrpc>`.
   -  Removed maxlength and size as automatically added attributes of
      form_input() in the :doc:`form helper <helpers/form_helper>`.
   -  Documented the language file use of byte_format() in the :doc:`number
      helper <helpers/number_helper>`.

Bug fixes for 1.6.3
-------------------

-  Added a language key for valid_emails in validation_lang.php.
-  Amended fixes for bug (#3419) with parsing DSN database connections.
-  Moved the _has_operator() function (#4535) into DB_driver from
   DB_active_rec.
-  Fixed a syntax error in upload_lang.php.
-  Fixed a bug (#4542) with a regular expression in the Image library.
-  Fixed a bug (#4561) where orhaving() wasn't properly passing values.
-  Removed some unused variables from the code (#4563).
-  Fixed a bug where having() was not adding an = into the statement
   (#4568).
-  Fixed assorted user guide typos or examples (#4574, #4706).
-  Added quoted-printable headers to Email class when the multi-part
   override is used.
-  Fixed a double opening <p> tag in the index pages of each system
   directory.

Version 1.6.2
=============

Release Date: May 13, 2008
Hg Tag: 1.6.2

-  Active Record

   -  Added the ability to prevent escaping in having() clauses.
   -  Added rename_table() into :doc:`DBForge <./database/forge>`.
   -  Fixed a bug that wasn't allowing escaping to be turned off if the
      value of a query was NULL.
   -  DB Forge is now assigned to any models that exist after loading
      (#3457).

-  Database

   -  Added :doc:`Strict Mode <./database/transactions>` to database
      transactions.
   -  Escape behaviour in where() clauses has changed; values in those
      with the "FALSE" argument are no longer escaped (ie: quoted).

-  Config

   -  Added 'application/vnd.ms-powerpoint' to list of mime types.
   -  Added 'audio/mpg' to list of mime types.
   -  Added new user-modifiable file constants.php containing file mode
      and fopen constants.
   -  Added the ability to set CRLF settings via config in the
      :doc:`Email <libraries/email>` class.

-  Libraries

   -  Added increased security for filename handling in the Upload
      library.
   -  Added increased security for sessions for client-side data
      tampering.
   -  The MySQLi forge class is now in sync with MySQL forge.
   -  Added the ability to set CRLF settings via config in the
      :doc:`Email <libraries/email>` class.
   -  :doc:`Unit Testing <libraries/unit_testing>` results are now
      colour coded, and a change was made to the default template of
      results.
   -  Added a valid_emails rule to the Validation class.
   -  The :doc:`Zip class <libraries/zip>` now exits within download().
   -  The :doc:`Zip class <libraries/zip>` has undergone a substantial
      re-write for speed and clarity (thanks stanleyxu for the hard work
      and code contribution in bug report #3425!)

-  Helpers

   -  Added a Compatibility
      Helper for using some common
      PHP 5 functions safely in applications that might run on PHP 4
      servers (thanks Seppo for the hard work and code contribution!)
   -  Added form_button() in the :doc:`Form
      helper <helpers/form_helper>`.
   -  Changed the radio() and checkbox() functions to default to not
      checked by default.
   -  Added the ability to include an optional HTTP Response Code in the
      redirect() function of the :doc:`URL
      Helper <helpers/url_helper>`.
   -  Modified img() in the :doc:`HTML Helper <helpers/html_helper>` to
      remove an unneeded space (#4208).
   -  Modified anchor() in the :doc:`URL helper <helpers/url_helper>`
      to no longer add a default title= attribute (#4209).
   -  The :doc:`Download helper <helpers/download_helper>` now exits
      within force_download().
   -  Added get_dir_file_info(), get_file_info(), and
      get_mime_by_extension() to the :doc:`File
      Helper <helpers/file_helper>`.
   -  Added symbolic_permissions() and octal_permissions() to the
      :doc:`File helper <helpers/file_helper>`.

-  Plugins

   -  Modified captcha generation to first look for the function
      imagecreatetruecolor, and fallback to imagecreate if it isn't
      available (#4226).

-  Other Changes

   -  Added ability for :doc:`xss_clean() <libraries/input>` to accept
      arrays.
   -  Removed closing PHP tags from all PHP files to avoid accidental
      output and potential 'cannot modify headers' errors.
   -  Removed "scripts" from the auto-load search path. Scripts were
      deprecated in Version 1.4.1 (September 21, 2006). If you still
      need to use them for legacy reasons, they must now be manually
      loaded in each Controller.
   -  Added a :doc:`Reserved Names <general/reserved_names>` page to
      the userguide, and migrated reserved controller names into it.
   -  Added a :doc:`Common Functions <general/common_functions>` page
      to the userguide for globally available functions.
   -  Improved security and performance of xss_clean().

Bugfixes for 1.6.2
------------------

-  Fixed a bug where SET queries were not being handled as "write"
   queries.
-  Fixed a bug (#3191) with ORIG_PATH_INFO URI parsing.
-  Fixed a bug in DB Forge, when inserting an id field (#3456).
-  Fixed a bug in the table library that could cause identically
   constructed rows to be dropped (#3459).
-  Fixed DB Driver and MySQLi result driver checking for resources
   instead of objects (#3461).
-  Fixed an AR_caching error where it wasn't tracking table aliases
   (#3463).
-  Fixed a bug in AR compiling, where select statements with arguments
   got incorrectly escaped (#3478).
-  Fixed an incorrect documentation of $this->load->language (#3520).
-  Fixed bugs (#3523, #4350) in get_filenames() with recursion and
   problems with Windows when $include_path is used.
-  Fixed a bug (#4153) in the XML-RPC class preventing dateTime.iso8601
   from being used.
-  Fixed an AR bug with or_where_not_in() (#4171).
-  Fixed a bug with :doc:`xss_clean() <libraries/input>` that would
   add semicolons to GET URI variable strings.
-  Fixed a bug (#4206) in the Directory Helper where the directory
   resource was not being closed, and minor improvements.
-  Fixed a bug in the FTP library where delete_dir() was not working
   recursively (#4215).
-  Fixed a Validation bug when set_rules() is used with a non-array
   field name and rule (#4220).
-  Fixed a bug (#4223) where DB caching would not work for returned DB
   objects or multiple DB connections.
-  Fixed a bug in the Upload library that might output the same error
   twice (#4390).
-  Fixed an AR bug when joining with a table alias and table prefix
   (#4400).
-  Fixed a bug in the DB class testing the $params argument.
-  Fixed a bug in the Table library where the integer 0 in cell data
   would be displayed as a blank cell.
-  Fixed a bug in link_tag() of the :doc:`URL
   helper <helpers/url_helper>` where a key was passed instead of
   a value.
-  Fixed a bug in DB_result::row() that prevented it from returning
   individual fields with MySQL NULL values.
-  Fixed a bug where SMTP emails were not having dot transformation
   performed on lines that begin with a dot.
-  Fixed a bug in display_error() in the DB driver that was
   instantiating new Language and Exception objects, and not using the
   error heading.
-  Fixed a bug (#4413) where a URI containing slashes only e.g.
   'http&#58;//example.com/index.php?//' would result in PHP errors
-  Fixed an array to string conversion error in the Validation library
   (#4425)
-  Fixed bug (#4451, #4299, #4339) where failed transactions will not
   rollback when debug mode is enabled.
-  Fixed a bug (#4506) with overlay_watermark() in the Image library
   preventing support for PNG-24s with alpha transparency
-  Fixed assorted user guide typos (#3453, #4364, #4379, #4399, #4408,
   #4412, #4448, #4488).

Version 1.6.1
=============

Release Date: February 12, 2008
Hg Tag: 1.6.1

-  Active Record

   -  Added :ref:`Active Record
      Caching <ar-caching>`.
   -  Made Active Record fully database-prefix aware.

-  Database drivers

   -  Added support for setting client character set and collation for
      MySQLi.

-  Core Changes

   -  Modified xss_clean() to be more intelligent with its handling of
      URL encoded strings.
   -  Added $_SERVER, $_FILES, $_ENV, and $_SESSION to sanitization
      of globals.
   -  Added a :doc:`Path Helper <./helpers/path_helper>`.
   -  Simplified _reindex_segments() in the URI class.
   -  Escaped the '-' in the default 'permitted_uri_chars' config
      item, to prevent errors if developers just try to add additional
      characters to the end of the default expression.
   -  Modified method calling to controllers to show a 404 when a
      private or protected method is accessed via a URL.
   -  Modified framework initiated 404s to log the controller and method
      for invalid requests.

-  Helpers

   -  Modified get_filenames() in the File Helper to return FALSE if
      the $source_dir is not readable.

Bugfixes for 1.6.1
------------------

-  Deprecated is_numeric as a validation rule. Use of numeric and
   integer are preferred.
-  Fixed bug (#3379) in DBForge with SQLite for table creation.
-  Made Active Record fully database prefix aware (#3384).
-  Fixed a bug where DBForge was outputting invalid SQL in Postgres by
   adding brackets around the tables in FROM.
-  Changed the behaviour of Active Record's update() to make the WHERE
   clause optional (#3395).
-  Fixed a bug (#3396) where certain POST variables would cause a PHP
   warning.
-  Fixed a bug in query binding (#3402).
-  Changed order of SQL keywords in the Profiler $highlight array so OR
   would not be highlighted before ORDER BY.
-  Fixed a bug (#3404) where the MySQLi driver was testing if
   $this->conn_id was a resource instead of an object.
-  Fixed a bug (#3419) connecting to a database via a DSN string.
-  Fixed a bug (#3445) where the routed segment array was not re-indexed
   to begin with 1 when the default controller is used.
-  Fixed assorted user guide typos.

Version 1.6.0
=============

Release Date: January 30, 2008

-  DBForge

   -  Added :doc:`DBForge <./database/forge>` to the database tools.
   -  Moved create_database() and drop_database() into
      :doc:`DBForge <./database/forge>`.
   -  Added add_field(), add_key(), create_table(), drop_table(),
      add_column(), drop_column(), modify_column() into
      :doc:`DBForge <./database/forge>`.

-  Active Record

   -  Added protect_identifiers() in :doc:`Active
      Record <./database/query_builder>`.
   -  All AR queries are backticked if appropriate to the database.
   -  Added where_in(), or_where_in(), where_not_in(),
      or_where_not_in(), not_like() and or_not_like() to :doc:`Active
      Record <./database/query_builder>`.
   -  Added support for limit() into update() and delete() statements in
      :doc:`Active Record <./database/query_builder>`.
   -  Added empty_table() and truncate_table() to :doc:`Active
      Record <./database/query_builder>`.
   -  Added the ability to pass an array of tables to the delete()
      statement in :doc:`Active Record <./database/query_builder>`.
   -  Added count_all_results() function to :doc:`Active
      Record <./database/query_builder>`.
   -  Added select_max(), select_min(), select_avg() and
      select_sum() to :doc:`Active Record <./database/query_builder>`.
   -  Added the ability to use aliases with joins in :doc:`Active
      Record <./database/query_builder>`.
   -  Added a third parameter to Active Record's like() clause to
      control where the wildcard goes.
   -  Added a third parameter to set() in :doc:`Active
      Record <./database/query_builder>` that withholds escaping
      data.
   -  Changed the behaviour of variables submitted to the where() clause
      with no values to auto set "IS NULL"

-  Other Database Related

   -  MySQL driver now requires MySQL 4.1+
   -  Added $this->DB->save_queries variable to DB driver, enabling
      queries to get saved or not. Previously they were always saved.
   -  Added $this->db->dbprefix() to manually add database prefixes.
   -  Added 'random' as an order_by() option , and removed "rand()" as
      a listed option as it was MySQL only.
   -  Added a check for NULL fields in the MySQL database backup
      utility.
   -  Added "constrain_by_prefix" parameter to db->list_table()
      function. If set to TRUE it will limit the result to only table
      names with the current prefix.
   -  Deprecated from Active Record; getwhere() for get_where();
      groupby() for group_by(); havingor() for having_or(); orderby()
      for order_by; orwhere() for or_where(); and orlike() for
      or_like().
   -  Modified csv_from_result() to output CSV data more in the spirit
      of basic rules of RFC 4180.
   -  Added 'char_set' and 'dbcollat' database configuration settings,
      to explicitly set the client communication properly.
   -  Removed 'active_r' configuration setting and replaced with a
      global $active_record setting, which is more in harmony with the
      global nature of the behavior (#1834).

-  Core changes

   -  Added ability to load multiple views, whose content will be
      appended to the output in the order loaded.
   -  Added the ability to :doc:`auto-load <./general/autoloader>`
      :doc:`Models <./general/models>`.
   -  Reorganized the URI and Routes classes for better clarity.
   -  Added Compat.php to allow function overrides for older versions of
      PHP or PHP environments missing certain extensions / libraries
   -  Added memory usage, GET, URI string data, and individual query
      execution time to Profiler output.
   -  Deprecated Scaffolding.
   -  Added is_really_writable() to Common.php to provide a
      cross-platform reliable method of testing file/folder writability.

-  Libraries

   -  Changed the load protocol of Models to allow for extension.
   -  Strengthened the Encryption library to help protect against man in
      the middle attacks when MCRYPT_MODE_CBC mode is used.
   -  Added Flashdata variables, session_id regeneration and
      configurable session update times to the :doc:`Session
      class. <./libraries/sessions>`
   -  Removed 'last_visit' from the Session class.
   -  Added a language entry for valid_ip validation error.
   -  Modified ``prep_for_form()`` in the Validation class to accept
      arrays, adding support for POST array validation (via callbacks
      only)
   -  Added an "integer" rule into the Validation library.
   -  Added valid_base64() to the Validation library.
   -  Documented clear() in the :doc:`Image
      Processing <./libraries/image_lib>` library.
   -  Changed the behaviour of custom callbacks so that they no longer
      trigger the "required" rule.
   -  Modified Upload class $_FILES error messages to be more precise.
   -  Moved the safe mode and auth checks for the Email library into the
      constructor.
   -  Modified variable names in _ci_load() method of Loader class to
      avoid conflicts with view variables.
   -  Added a few additional mime type variations for CSV.
   -  Enabled the 'system' methods for the XML-RPC Server library,
      except for 'system.multicall' which is still disabled.

-  Helpers & Plugins

   -  Added link_tag() to the :doc:`HTML
      helper. <./helpers/html_helper>`
   -  Added img() to the :doc:`HTML helper. <./helpers/html_helper>`
   -  Added ability to :doc:`"extend" Helpers <./general/helpers>`.
   -  Added an *Email Helper* into core helpers.
   -  Added strip_quotes() function to :doc:`string
      helper <./helpers/string_helper>`.
   -  Added reduce_multiples() function to :doc:`string
      helper <./helpers/string_helper>`.
   -  Added quotes_to_entities() function to :doc:`string
      helper <./helpers/string_helper>`.
   -  Added form_fieldset(), form_fieldset_close(), form_label(),
      and form_reset() function to :doc:`form
      helper <./helpers/form_helper>`.
   -  Added support for external urls in form_open().
   -  Removed support for db_backup in MySQLi due to incompatible
      functions.
   -  Javascript Calendar plugin now uses the months and days from the
      calendar language file, instead of hard-coded values,
      internationalizing it.

-  Documentation Changes

   -  Added Writing Documentation section
      for the community to use in writing their own documentation.
   -  Added titles to all user manual pages.
   -  Added attributes into <html> of userguide for valid html.
   -  Added :doc:`Zip Encoding Class <libraries/zip>`
      to the table of contents of the userguide.
   -  Moved part of the userguide menu javascript to an external file.
   -  Documented distinct() in :doc:`Active
      Record <./database/query_builder>`.
   -  Documented the timezones() function in the :doc:`Date
      Helper <./helpers/date_helper>`.
   -  Documented unset_userdata in the :doc:`Session
      class <./libraries/sessions>`.
   -  Documented 2 config options to the :doc:`Database
      configuration <./database/configuration>` page.

Bug fixes for Version 1.6.0
---------------------------

-  Fixed a bug (#1813) preventing using $CI->db in the same application
   with returned database objects.
-  Fixed a bug (#1842) where the $this->uri->rsegments array would not
   include the 'index' method if routed to the controller without an
   implicit method.
-  Fixed a bug (#1872) where word_limiter() was not retaining
   whitespace.
-  Fixed a bug (#1890) in csv_from_result() where content that
   included the delimiter would break the file.
-  Fixed a bug (#2542)in the clean_email() method of the Email class to
   allow for non-numeric / non-sequential array keys.
-  Fixed a bug (#2545) in _html_entity_decode_callback() when
   'global_xss_filtering' is enabled.
-  Fixed a bug (#2668) in the :doc:`parser class <./libraries/parser>`
   where numeric data was ignored.
-  Fixed a bug (#2679) where the "previous" pagination link would get
   drawn on the first page.
-  Fixed a bug (#2702) in _object_to_array that broke some types of
   inserts and updates.
-  Fixed a bug (#2732) in the SQLite driver for PHP 4.
-  Fixed a bug (#2754) in Pagination to scan for non-positive
   num_links.
-  Fixed a bug (#2762) in the :doc:`Session
   library <./libraries/sessions>` where user agent matching would
   fail on user agents ending with a space.
-  Fixed a bug (#2784) $field_names[] vs $Ffield_names[] in postgres
   and sqlite drivers.
-  Fixed a bug (#2810) in the typography helper causing extraneous
   paragraph tags when string contains tags.
-  Fixed a bug (#2849) where arguments passed to a subfolder controller
   method would be incorrectly shifted, dropping the 3rd segment value.
-  Fixed a bug (#2858) which referenced a wrong variable in the Image
   class.
-  Fixed a bug (#2875)when loading plugin files as _plugin. and not
   _pi.
-  Fixed a bug (#2912) in get_filenames() in the :doc:`File
   Helper <helpers/file_helper>` where the array wasn't cleared
   after each call.
-  Fixed a bug (#2974) in highlight_phrase() that caused an error with
   slashes.
-  Fixed a bug (#3003) in the Encryption Library to support modes other
   than MCRYPT_MODE_ECB
-  Fixed a bug (#3015) in the :doc:`User Agent
   library <./libraries/user_agent>` where more than 2 languages
   where not reported with languages().
-  Fixed a bug (#3017) in the :doc:`Email <./libraries/email>` library
   where some timezones were calculated incorrectly.
-  Fixed a bug (#3024) in which master_dim wasn't getting reset by
   clear() in the Image library.
-  Fixed a bug (#3156) in Text Helper highlight_code() causing PHP tags
   to be handled incorrectly.
-  Fixed a bug (#3166) that prevented num_rows from working in Oracle.
-  Fixed a bug (#3175) preventing certain libraries from working
   properly when autoloaded in PHP 4.
-  Fixed a bug (#3267) in the Typography Helper where unordered list was
   listed "un.
-  Fixed a bug (#3268) where the Router could leave '/' as the path.
-  Fixed a bug (#3279) where the Email class was sending the wrong
   Content-Transfer-Encoding for some character sets.
-  Fixed a bug (#3284) where the rsegment array would not be set
   properly if the requested URI contained more segments than the routed
   URI.
-  Removed extraneous load of $CFG in _display_cache() of the Output
   class (#3285).
-  Removed an extraneous call to loading models (#3286).
-  Fixed a bug (#3310) with sanitization of globals in the Input class
   that could unset CI's global variables.
-  Fixed a bug (#3314) which would cause the top level path to be
   deleted in delete_files() of the File helper.
-  Fixed a bug (#3328) where the smiley helper might return an undefined
   variable.
-  Fixed a bug (#3330) in the FTP class where a comparison wasn't
   getting made.
-  Removed an unused parameter from Profiler (#3332).
-  Fixed a bug in database driver where num_rows property wasn't
   getting updated.
-  Fixed a bug in the :doc:`upload
   library <./libraries/file_uploading>` when allowed_files
   wasn't defined.
-  Fixed a bug in word_wrap() of the Text Helper that incorrectly
   referenced an object.
-  Fixed a bug in Validation where valid_ip() wasn't called properly.
-  Fixed a bug in Validation where individual error messages for
   checkboxes wasn't supported.
-  Fixed a bug in captcha calling an invalid PHP function.
-  Fixed a bug in the cookie helper "set_cookie" function. It was not
   honoring the config settings.
-  Fixed a bug that was making validation callbacks required even when
   not set as such.
-  Fixed a bug in the XML-RPC library so if a type is specified, a more
   intelligent decision is made as to the default type.
-  Fixed an example of comma-separated emails in the email library
   documentation.
-  Fixed an example in the Calendar library for Showing Next/Previous
   Month Links.
-  Fixed a typo in the database language file.
-  Fixed a typo in the image language file "suppor" to "support".
-  Fixed an example for XML RPC.
-  Fixed an example of accept_charset() in the :doc:`User Agent
   Library <./libraries/user_agent>`.
-  Fixed a typo in the docblock comments that had CodeIgniter spelled
   CodeIgnitor.
-  Fixed a typo in the :doc:`String Helper <./helpers/string_helper>`
   (uniquid changed to uniqid).
-  Fixed typos in the email Language class
   (email_attachment_unredable, email_filed_smtp_login), and FTP
   Class (ftp_unable_to_remame).
-  Added a stripslashes() into the Upload Library.
-  Fixed a series of grammatical and spelling errors in the language
   files.
-  Fixed assorted user guide typos.

Version 1.5.4
=============

Release Date: July 12, 2007

-  Added :doc:`custom Language files <./libraries/language>` to the
   :doc:`autoload <./general/autoloader>` options.
-  Added stripslashes() to the _clean_input_data() function in the
   :doc:`Input class <./libraries/input>` when magic quotes is on so
   that data will always be un-slashed within the framework.
-  Added array to string into the :doc:`profiler <general/profiling>`.
-  Added some additional mime types in application/config/mimes.php.
-  Added filename_security() method to :doc:`Input
   library <./libraries/input>`.
-  Added some additional arguments to the :doc:`Inflection
   helper <./helpers/inflector_helper>` singular() to compensate
   for words ending in "s". Also added a force parameter to pluralize().
-  Added $config['charset'] to the config file. Default value is
   'UTF-8', used in some string handling functions.
-  Fixed MSSQL insert_id().
-  Fixed a logic error in the DB trans_status() function. It was
   incorrectly returning TRUE on failure and FALSE on success.
-  Fixed a bug that was allowing multiple load attempts on extended
   classes.
-  Fixed a bug in the bootstrap file that was incorrectly attempting to
   discern the full server path even when it was explicity set by the
   user.
-  Fixed a bug in the escape_str() function in the MySQL driver.
-  Fixed a typo in the :doc:`Calendar library <./libraries/calendar>`
-  Fixed a typo in rpcs.php library
-  Fixed a bug in the :doc:`Zip library <./libraries/zip>`, providing
   PC Zip file compatibility with Mac OS X
-  Fixed a bug in router that was ignoring the scaffolding route for
   optimization
-  Fixed an IP validation bug.
-  Fixed a bug in display of POST keys in the
   :doc:`Profiler <./general/profiling>` output
-  Fixed a bug in display of queries with characters that would be
   interpreted as HTML in the :doc:`Profiler <./general/profiling>`
   output
-  Fixed a bug in display of Email class print debugger with characters
   that would be interpreted as HTML in the debugging output
-  Fixed a bug in the Content-Transfer-Encoding of HTML emails with the
   quoted-printable MIME type
-  Fixed a bug where one could unset certain PHP superglobals by setting
   them via GET or POST data
-  Fixed an undefined function error in the insert_id() function of the
   PostgreSQL driver
-  Fixed various doc typos.
-  Documented two functions from the :doc:`String
   helper <./helpers/string_helper>` that were missing from the
   user guide: ``trim_slashes()`` and ``reduce_double_slashes()``.
-  Docs now validate to XHTML 1 transitional
-  Updated the XSS Filtering to take into account the IE expression()
   ability and improved certain deletions to prevent possible exploits
-  Modified the Router so that when Query Strings are Enabled, the
   controller trigger and function trigger values are sanitized for
   filename include security.
-  Modified the is_image() method in the Upload library to take into
   account Windows IE 6/7 eccentricities when dealing with MIMEs
-  Modified XSS Cleaning routine to be more performance friendly and
   compatible with PHP 5.2's new PCRE backtrack and recursion limits.
-  Modified the :doc:`URL Helper <./helpers/url_helper>` to type cast
   the $title as a string in case a numeric value is supplied
-  Modified Form Helper form_dropdown() to type cast the keys and
   values of the options array as strings, allowing numeric values to be
   properly set as 'selected'
-  Deprecated the use if is_numeric() in various places since it allows
   periods. Due to compatibility problems with ctype_digit(), making it
   unreliable in some installations, the following regular expression
   was used instead: preg_match("/[^0-9]/", $n)
-  Deprecated: APPVER has been deprecated and replaced with CI_VERSION
   for clarity.

Version 1.5.3
=============

Release Date: April 15, 2007

-  Added array to string into the profiler
-  Code Igniter references updated to CodeIgniter
-  pMachine references updated to EllisLab
-  Fixed a bug in the ``repeater()`` function of :doc:`string
   helper <./helpers/string_helper>`.
-  Fixed a bug in ODBC driver
-  Fixed a bug in result_array() that was returning an empty array when
   no result is produced.
-  Fixed a bug in the redirect function of the :doc:`url
   helper <./helpers/url_helper>`.
-  Fixed an undefined variable in Loader
-  Fixed a version bug in the Postgres driver
-  Fixed a bug in the textarea function of the form helper for use with
   strings
-  Fixed doc typos.

Version 1.5.2
=============

Release Date: February 13, 2007

-  Added subversion information
   to the :doc:`downloads <installation/downloads>` page.
-  Added support for captions in the :doc:`Table
   Library <./libraries/table>`
-  Fixed a bug in the
   :doc:`download_helper <helpers/download_helper>` that was causing
   Internet Explorer to load rather than download
-  Fixed a bug in the Active Record Join function that was not taking
   table prefixes into consideration.
-  Removed unescaped variables in error messages of Input and Router
   classes
-  Fixed a bug in the Loader that was causing errors on Libraries loaded
   twice. A debug message is now silently made in the log.
-  Fixed a bug in the :doc:`form helper <helpers/form_helper>` that
   gave textarea a value attribute
-  Fixed a bug in the :doc:`Image Library <libraries/image_lib>` that
   was ignoring resizing the same size image
-  Fixed some doc typos.

Version 1.5.1
=============

Release Date: November 23, 2006

-  Added support for submitting arrays of libraries in the
   $this->load->library function.
-  Added support for naming custom library files in lower or uppercase.
-  Fixed a bug related to output buffering.
-  Fixed a bug in the active record class that was not resetting query
   data after a completed query.
-  Fixed a bug that was suppressing errors in controllers.
-  Fixed a problem that can cause a loop to occur when the config file
   is missing.
-  Fixed a bug that occurred when multiple models were loaded with the
   third parameter set to TRUE.
-  Fixed an oversight that was not unsetting globals properly in the
   input sanitize function.
-  Fixed some bugs in the Oracle DB driver.
-  Fixed an incorrectly named variable in the MySQLi result driver.
-  Fixed some doc typos.

Version 1.5.0.1
===============

Release Date: October 31, 2006

-  Fixed a problem in which duplicate attempts to load helpers and
   classes were not being stopped.
-  Fixed a bug in the word_wrap() helper function.
-  Fixed an invalid color Hex number in the Profiler class.
-  Fixed a corrupted image in the user guide.

Version 1.5.0
=============

Release Date: October 30, 2006

-  Added :doc:`DB utility class <./database/utilities>`, permitting DB
   backups, CVS or XML files from DB results, and various other
   functions.
-  Added :doc:`Database Caching Class <./database/caching>`.
-  Added :doc:`transaction support <./database/transactions>` to the
   database classes.
-  Added :doc:`Profiler Class <./general/profiling>` which generates a
   report of Benchmark execution times, queries, and POST data at the
   bottom of your pages.
-  Added :doc:`User Agent Library <./libraries/user_agent>` which
   allows browsers, robots, and mobile devises to be identified.
-  Added :doc:`HTML Table Class <./libraries/table>` , enabling tables
   to be generated from arrays or database results.
-  Added :doc:`Zip Encoding Library <./libraries/zip>`.
-  Added :doc:`FTP Library <./libraries/ftp>`.
-  Added the ability to :doc:`extend
   libraries <./general/creating_libraries>` and :doc:`extend core
   classes <./general/core_classes>`, in addition to being able to
   replace them.
-  Added support for storing :doc:`models within
   sub-folders <./general/models>`.
-  Added :doc:`Download Helper <./helpers/download_helper>`.
-  Added :doc:`simple_query() <./database/queries>` function to the
   database classes
-  Added ``standard_date()`` function function to the :doc:`Date Helper <helpers/date_helper>`.
-  Added :doc:`$query->free_result() <./database/results>` to database
   class.
-  Added :doc:`$query->list_fields() <./database/metadata>` function to
   database class
-  Added :doc:`$this->db->platform() <./database/helpers>` function
-  Added new :doc:`File Helper <./helpers/file_helper>`:
   get_filenames()
-  Added new helper: *Smiley Helper*
-  Added support for <ul> and <ol> lists in the :doc:`HTML
   Helper <./helpers/html_helper>`
-  Added the ability to rewrite :doc:`short
   tags <./general/alternative_php>` on-the-fly, converting them
   to standard PHP statements, for those servers that do not support
   short tags. This allows the cleaner syntax to be used regardless of
   whether it's supported by the server.
-  Added the ability to :doc:`rename or relocate the "application"
   folder <./general/managing_apps>`.
-  Added more thorough initialization in the upload class so that all
   class variables are reset.
-  Added "is_numeric" to validation, which uses the native PHP
   is_numeric function.
-  Improved the URI handler to make it more reliable when the
   $config['uri_protocol'] item is set to AUTO.
-  Moved most of the functions in the Controller class into the Loader
   class, allowing fewer reserved function names for controllers when
   running under PHP 5.
-  Updated the DB Result class to return an empty array when
   $query->result() doesn't produce a result.
-  Updated the input->cookie() and input->post() functions in :doc:`Input
   Class <./libraries/input>` to permit arrays contained cookies
   that are arrays to be run through the XSS filter.
-  Documented three functions from the Validation
   class that were missing from the user
   guide: set_select(), set_radio(), and set_checkbox().
-  Fixed a bug in the Email class related to SMTP Helo data.
-  Fixed a bug in the word wrapping helper and function in the email
   class.
-  Fixed a bug in the validation class.
-  Fixed a bug in the typography helper that was incorrectly wrapping
   block level elements in paragraph tags.
-  Fixed a problem in the ``form_prep()`` function that was double encoding
   entities.
-  Fixed a bug that affects some versions of PHP when output buffering
   is nested.
-  Fixed a bug that caused CI to stop working when the PHP magic
   __get() or __set() functions were used within models or
   controllers.
-  Fixed a pagination bug that was permitting negative values in the
   URL.
-  Fixed an oversight in which the Loader class was not allowed to be
   extended.
-  Changed _get_config() to get_config() since the function is not a
   private one.
-  **Deprecated "init" folder**. Initialization happens automatically
   now. :doc:`Please see documentation <./general/creating_libraries>`.
-  **Deprecated** $this->db->field_names() USE
   $this->db->list_fields()
-  **Deprecated** the $config['log_errors'] item from the config.php
   file. Instead, $config['log_threshold'] can be set to "0" to turn it
   off.

Version 1.4.1
=============

Release Date: September 21, 2006

-  Added a new feature that passes URI segments directly to your
   function calls as parameters. See the
   :doc:`Controllers <general/controllers>` page for more info.
-  Added support for a function named _output(), which when used in
   your controllers will received the final rendered output from the
   output class. More info in the :doc:`Controllers <general/controllers>`
   page.
-  Added several new functions in the :doc:`URI
   Class <./libraries/uri>` to let you retrieve and manipulate URI
   segments that have been re-routed using the :doc:`URI
   Routing <general/routing>` feature. Previously, the URI class did not
   permit you to access any re-routed URI segments, but now it does.
-  Added :doc:`$this->output->set_header() <./libraries/output>`
   function, which allows you to set server headers.
-  Updated plugins, helpers, and language classes to allow your
   application folder to contain its own plugins, helpers, and language
   folders. Previously they were always treated as global for your
   entire installation. If your application folder contains any of these
   resources they will be used *instead* the global ones.
-  Added :doc:`Inflector helper <./helpers/inflector_helper>`.
-  Added element() function in the :doc:`array
   helper <./helpers/array_helper>`.
-  Added RAND() to active record orderby() function.
-  Added delete_cookie() and get_cookie() to :doc:`Cookie
   helper <./helpers/cookie_helper>`, even though the input class
   has a cookie fetching function.
-  Added Oracle database driver (still undergoing testing so it might
   have some bugs).
-  Added the ability to combine pseudo-variables and php variables in
   the template parser class.
-  Added output compression option to the config file.
-  Removed the is_numeric test from the db->escape() function.
-  Fixed a MySQLi bug that was causing error messages not to contain
   proper error data.
-  Fixed a bug in the email class which was causing it to ignore
   explicitly set alternative headers.
-  Fixed a bug that was causing a PHP error when the Exceptions class
   was called within the get_config() function since it was causing
   problems.
-  Fixed an oversight in the cookie helper in which the config file
   cookie settings were not being honored.
-  Fixed an oversight in the upload class. An item mentioned in the 1.4
   changelog was missing.
-  Added some code to allow email attachments to be reset when sending
   batches of email.
-  Deprecated the application/scripts folder. It will continue to work
   for legacy users, but it is recommended that you create your own
   :doc:`libraries <./general/libraries>` or
   :doc:`models <./general/models>` instead. It was originally added
   before CI had user libraries or models, but it's not needed anymore.
-  Deprecated the $autoload['core'] item from the autoload.php file.
   Instead, please now use: $autoload['libraries']
-  Deprecated the following database functions:
   $this->db->smart_escape_str() and $this->db->fields().

Version 1.4.0
=============

Release Date: September 17, 2006

-  Added :doc:`Hooks <./general/hooks>` feature, enabling you to tap
   into and modify the inner workings of the framework without hacking
   the core files.
-  Added the ability to organize controller files :doc:`into
   sub-folders <general/controllers>`. Kudos to Marco for
   suggesting this (and the next two) feature.
-  Added regular expressions support for :doc:`routing
   rules <./general/routing>`.
-  Added the ability to :doc:`remap function
   calls <./general/controllers>` within your controllers.
-  Added the ability to :doc:`replace core system
   classes <./general/core_classes>` with your own classes.
-  Added support for % character in URL.
-  Added the ability to supply full URLs using the
   :doc:`anchor() <./helpers/url_helper>` helper function.
-  Added mode parameter to :doc:`file_write() <./helpers/file_helper>`
   helper.
-  Added support for changing the port number in the :doc:`Postgres
   driver <./database/configuration>`.
-  Moved the list of "allowed URI characters" out of the Router class
   and into the config file.
-  Moved the MIME type array out of the Upload class and into its own
   file in the application/config/ folder.
-  Updated the Upload class to allow the upload field name to be set
   when calling :doc:`do_upload() <./libraries/file_uploading>`.
-  Updated the :doc:`Config Library <./libraries/config>` to be able to
   load config files silently, and to be able to assign config files to
   their own index (to avoid collisions if you use multiple config
   files).
-  Updated the URI Protocol code to allow more options so that URLs will
   work more reliably in different environments.
-  Updated the form_open() helper to allow the GET method to be used.
-  Updated the MySQLi execute() function with some code to help prevent
   lost connection errors.
-  Updated the SQLite Driver to check for object support before
   attempting to return results as objects. If unsupported it returns an
   array.
-  Updated the Models loader function to allow multiple loads of the
   same model.
-  Updated the MS SQL driver so that single quotes are escaped.
-  Updated the Postgres and ODBC drivers for better compatibility.
-  Removed a strtolower() call that was changing URL segments to lower
   case.
-  Removed some references that were interfering with PHP 4.4.1
   compatibility.
-  Removed backticks from Postgres class since these are not needed.
-  Renamed display() to _display() in the Output class to make it clear
   that it's a private function.
-  Deprecated the hash() function due to a naming conflict with a native
   PHP function with the same name. Please use dohash() instead.
-  Fixed an bug that was preventing the input class from unsetting GET
   variables.
-  Fixed a router bug that was making it too greedy when matching end
   segments.
-  Fixed a bug that was preventing multiple discrete database calls.
-  Fixed a bug in which loading a language file was producing a "file
   contains no data" message.
-  Fixed a session bug caused by the XSS Filtering feature inadvertently
   changing the case of certain words.
-  Fixed some missing prefixes when using the database prefix feature.
-  Fixed a typo in the Calendar class (cal_november).
-  Fixed a bug in the form_checkbox() helper.
-  Fixed a bug that was allowing the second segment of the URI to be
   identical to the class name.
-  Fixed an evaluation bug in the database initialization function.
-  Fixed a minor bug in one of the error messages in the language class.
-  Fixed a bug in the date helper timespan function.
-  Fixed an undefined variable in the DB Driver class.
-  Fixed a bug in which dollar signs used as binding replacement values
   in the DB class would be treated as RegEx back-references.
-  Fixed a bug in the set_hash() function which was preventing MD5 from
   being used.
-  Fixed a couple bugs in the Unit Testing class.
-  Fixed an incorrectly named variable in the Validation class.
-  Fixed an incorrectly named variable in the URI class.
-  Fixed a bug in the config class that was preventing the base URL from
   being called properly.
-  Fixed a bug in the validation class that was not permitting callbacks
   if the form field was empty.
-  Fixed a problem that was preventing scaffolding from working properly
   with MySQLi.
-  Fixed some MS SQL bugs.
-  Fixed some doc typos.

Version 1.3.3
=============

Release Date: June 1, 2006

-  Models do **not** connect automatically to the database as of this
   version. :doc:`More info here <./general/models>`.
-  Updated the Sessions class to utilize the active record class when
   running session related queries. Previously the queries assumed MySQL
   syntax.
-  Updated alternator() function to re-initialize when called with no
   arguments, allowing multiple calls.
-  Fixed a bug in the active record "having" function.
-  Fixed a problem in the validation class which was making checkboxes
   be ignored when required.
-  Fixed a bug in the word_limiter() helper function. It was cutting
   off the fist word.
-  Fixed a bug in the xss_clean function due to a PHP bug that affects
   some versions of html_entity_decode.
-  Fixed a validation bug that was preventing rules from being set twice
   in one controller.
-  Fixed a calendar bug that was not letting it use dynamically loaded
   languages.
-  Fixed a bug in the active record class when using WHERE clauses with
   LIKE
-  Fixed a bug in the hash() security helper.
-  Fixed some typos.

Version 1.3.2
=============

Release Date: April 17, 2006

-  Changed the behavior of the validation class such that if a
   "required" rule is NOT explicitly stated for a field then all other
   tests get ignored.
-  Fixed a bug in the Controller class that was causing it to look in
   the local "init" folder instead of the main system one.
-  Fixed a bug in the init_pagination file. The $config item was not
   being set correctly.
-  Fixed a bug in the auto typography helper that was causing
   inconsistent behavior.
-  Fixed a couple bugs in the Model class.
-  Fixed some documentation typos and errata.

Version 1.3.1
=============

Release Date: April 11, 2006

-  Added a :doc:`Unit Testing Library <./libraries/unit_testing>`.
-  Added the ability to pass objects to the **insert()** and
   **update()** database functions. This feature enables you to (among
   other things) use your :doc:`Model class <./general/models>`
   variables to run queries with. See the Models page for details.
-  Added the ability to pass objects to the :doc:`view loading
   function <./general/views>`: $this->load->view('my_view',
   $object);
-  Added getwhere function to :doc:`Active Record
   class <./database/query_builder>`.
-  Added count_all function to :doc:`Active Record
   class <./database/query_builder>`.
-  Added language file for scaffolding and fixed a scaffolding bug that
   occurs when there are no rows in the specified table.
-  Added :doc:`$this->db->last_query() <./database/queries>`, which
   allows you to view your last query that was run.
-  Added a new mime type to the upload class for better compatibility.
-  Changed how cache files are read to prevent PHP errors if the cache
   file contains an XML tag, which PHP wants to interpret as a short
   tag.
-  Fixed a bug in a couple of the active record functions (where and
   orderby).
-  Fixed a bug in the image library when realpath() returns false.
-  Fixed a bug in the Models that was preventing libraries from being
   used within them.
-  Fixed a bug in the "exact_length" function of the validation class.
-  Fixed some typos in the user guide

Version 1.3
===========

Release Date: April 3, 2006

-  Added support for :doc:`Models <general/models>`.
-  Redesigned the database libraries to support additional RDBMs
   (Postgres, MySQLi, etc.).
-  Redesigned the :doc:`Active Record class <./database/query_builder>`
   to enable more varied types of queries with simpler syntax, and
   advanced features like JOINs.
-  Added a feature to the database class that lets you run :doc:`custom
   function calls <./database/call_function>`.
-  Added support for :doc:`private functions <general/controllers>` in your
   controllers. Any controller function name that starts with an
   underscore will not be served by a URI request.
-  Added the ability to pass your own initialization parameters to your
   :doc:`custom core libraries <general/creating_libraries>` when using
   $this->load->library()
-  Added support for running standard :doc:`query string URLs <general/urls>`.
   These can be optionally enabled in your config file.
-  Added the ability to :doc:`specify a "suffix" <general/urls>`, which will be
   appended to your URLs. For example, you could add .html to your URLs,
   making them appear static. This feature is enabled in your config
   file.
-  Added a new error template for use with native PHP errors.
-  Added "alternator" function in the :doc:`string
   helpers <./helpers/string_helper>`.
-  Removed slashing from the input class. After much debate we decided
   to kill this feature.
-  Change the commenting style in the scripts to the PEAR standard so
   that IDEs and tools like phpDocumenter can harvest the comments.
-  Added better class and function name-spacing to avoid collisions with
   user developed classes. All CodeIgniter classes are now prefixed with
   CI\_ and all controller methods are prefixed with _ci to avoid
   controller collisions. A list of reserved function names can be
   :doc:`found here <general/controllers>`.
-  Redesigned how the "CI" super object is referenced, depending on
   whether PHP 4 or 5 is being run, since PHP 5 allows a more graceful
   way to manage objects that utilizes a bit less resources.
-  Deprecated: $this->db->use_table() has been deprecated. Please read
   the :doc:`Active Record <./database/query_builder>` page for
   information.
-  Deprecated: $this->db->smart_escape_str() has been deprecated.
   Please use this instead: $this->db->escape()
-  Fixed a bug in the exception handler which was preventing some PHP
   errors from showing up.
-  Fixed a typo in the URI class. $this->total_segment() should be
   plural: $this->total_segments()
-  Fixed some typos in the default calendar template
-  Fixed some typos in the user guide

Version 1.2
===========

Release Date: March 21, 2006

-  Redesigned some internal aspects of the framework to resolve scoping
   problems that surfaced during the beta tests. The problem was most
   notable when instantiating classes in your constructors, particularly
   if those classes in turn did work in their constructors.
-  Added a global function named
   :doc:`get_instance() <general/ancillary_classes>` allowing the main
   CodeIgniter object to be accessible throughout your own classes.
-  Added new :doc:`File Helper <./helpers/file_helper>`:
   delete_files()
-  Added new :doc:`URL Helpers <./helpers/url_helper>`: base_url(),
   index_page()
-  Added the ability to create your own :doc:`core
   libraries <general/creating_libraries>` and store them in your local
   application directory.
-  Added an overwrite option to the :doc:`Upload
   class <./libraries/file_uploading>`, enabling files to be
   overwritten rather than having the file name appended.
-  Added Javascript Calendar plugin.
-  Added search feature to user guide. Note: This is done using Google,
   which at the time of this writing has not crawled all the pages of
   the docs.
-  Updated the parser class so that it allows tag pars within other tag
   pairs.
-  Fixed a bug in the DB "where" function.
-  Fixed a bug that was preventing custom config files to be
   auto-loaded.
-  Fixed a bug in the mysql class bind feature that prevented question
   marks in the replacement data.
-  Fixed some bugs in the xss_clean function

Version Beta 1.1
================

Release Date: March 10, 2006

-  Added a :doc:`Calendaring class <./libraries/calendar>`.
-  Added support for running :doc:`multiple
   applications <general/managing_apps>` that share a common CodeIgniter
   backend.
-  Moved the "uri protocol" variable from the index.php file into the
   config.php file
-  Fixed a problem that was preventing certain function calls from
   working within constructors.
-  Fixed a problem that was preventing the $this->load->library function
   from working in constructors.
-  Fixed a bug that occurred when the session class was loaded using the
   auto-load routine.
-  Fixed a bug that can happen with PHP versions that do not support the
   E_STRICT constant
-  Fixed a data type error in the form_radio function (form helper)
-  Fixed a bug that was preventing the xss_clean function from being
   called from the validation class.
-  Fixed the cookie related config names, which were incorrectly
   specified as $conf rather than $config
-  Fixed a pagination problem in the scaffolding.
-  Fixed a bug in the mysql class "where" function.
-  Fixed a regex problem in some code that trimmed duplicate slashes.
-  Fixed a bug in the ``br()`` function in the HTML helper
-  Fixed a syntax mistake in the form_dropdown function in the Form
   Helper.
-  Removed the "style" attributes form the form helpers.
-  Updated the documentation. Added "next/previous" links to each page
   and fixed various typos.

Version Beta 1.0
================

Release Date: February 28, 2006

First publicly released version.
