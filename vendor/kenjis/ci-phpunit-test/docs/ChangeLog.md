# Change Log for ci-phpunit-test

## v0.12.1 (2016/06/11)

### Fixed

* Fix bug that *Function Patcher* on `openssl_random_pseudo_bytes()` may cause "Warning: Missing argument 2". See [#119](https://github.com/kenjis/ci-phpunit-test/issues/119).
* Fix bug that installation/update script for Composer installation causes "Notice: Undefined offset: 1".

## v0.12.0 (2016/04/17)

### Added

* Monkey Patching on constants. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.12.0/docs/HowToWriteTests.md#patching-constants).

### Others

* Update nikic/PHP-Parser to v2.0.1

## v0.11.3 (2016/03/25)

### Fixed

* `assertRedirect()` does not work with external redirects. See [#104](https://github.com/kenjis/ci-phpunit-test/pull/104).

### Others

* Compatible with CodeIgniter 3.0.6
* Improved installer. See [#103](https://github.com/kenjis/ci-phpunit-test/pull/103).

## v0.11.2 (2016/03/17)

### Others

* Compatible with CodeIgniter 3.0.5

## v0.11.1 (2016/02/22)

### Fixed

* Fix bug that `$this->input->get_request_header()` returns the first header value for all tests. See [#92](https://github.com/kenjis/ci-phpunit-test/issues/92).
* Fix bug that config values are not reset between tests. See [#94](https://github.com/kenjis/ci-phpunit-test/issues/94).
*  Fix bug that `CI_Output::_display()` is called even if you call a controller method directly (when you pass an array to the 2nd argument of `$this->request()`).

### Others

* Improved documentation for `$this->request()`.

## v0.11.0 (2016/01/20)

### Upgrade Note

* Now ci-phpunit-test replaces `CI_Input`. If you use MY_Input, see [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.11.0/docs/HowToWriteTests.md#my_input).
* If you use *Monkey Patching*, please update `tests/Bootstrap.php`. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.11.0/docs/HowToWriteTests.md#upgrade-note-for-v0110).
* If you use PsySH v0.5, please update to v0.6.

### Added

* `$this->assertResponseCookie()` to assert HTTP response cookies. See [#88](https://github.com/kenjis/ci-phpunit-test/pull/88).
* Now `$this->request->enableHooks()` calls hook `display_override`.
* `$this->request->addCallablePreConstructor()` to add callable.
* Now *Moneky Patching* can patch code with PHP 7 new syntax.
* `header()` and `setcookie()` are added to *Function Patcher*'s white list.

### Fixed

* `_output()` method in controllers does not work in controller testing.

### Others

* Compatible with CodeIgniter 3.0.4
* Update nikic/PHP-Parser to v2.0.0

## v0.10.1 (2015/12/31)

### Fixed

* Fix bug that global variables for core classes are null. See [#75](https://github.com/kenjis/ci-phpunit-test/issues/75).
* Fix bug that can't use constant in `config.php`. See [#78](https://github.com/kenjis/ci-phpunit-test/issues/78).
* Fix bug that can't autoload library with alternative library name. See [#79](https://github.com/kenjis/ci-phpunit-test/pull/79).
* Fix bug that *Function Patcher* on `openssl_random_pseudo_bytes()` which returns `null` does not work.

## v0.10.0 (2015/11/27)

### Fixed

* Fix wrong implementation of resetting CodeIgniter instance. Now `reset_instance()` removes the existing *CodeIgniter instance*. See [#74](https://github.com/kenjis/ci-phpunit-test/pull/74).

### Changed

* Now `$this->getDouble()` does not call the original constructor by default. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.10.0/docs/FunctionAndClassReference.md#testcasegetdoubleclassname-params-enable_constructor--false).
* Now `reset_instance()` removes the existing *CodeIgniter instance*. See [#74](https://github.com/kenjis/ci-phpunit-test/pull/74).

### Added

* NetBeans test suite provider `application/tests/_ci_phpunit_test/TestSuiteProvider.php`. To use it, go to *Project Properties* > *Testing*, check *Use Custom Test Suite* checkbox, and select the file.

## v0.9.1 (2015/11/22)

### Fixed

* Fix bug that `phpunit` dies when `Unable to locate the specified class` error.

### Others

* Improved documentation.

## v0.9.0 (2015/11/18)

### Added

* `$this->request->addCallable()` to add callable. See [#68](https://github.com/kenjis/ci-phpunit-test/pull/68).
* Autoloading classes in `application/modules` folder.
* You can configure search paths for autoloader. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.9.0/docs/HowToWriteTests.md#autoloader).

### Others

* Compatible with CodeIgniter 3.0.3

## v0.8.2 (2015/10/09)

### Fixed

* Fix bug that monkey patching changes original source code in some cases of heredoc/nowdoc strings.

### Others

* Compatible with CodeIgniter 3.0.2
* Compatible with PHP 7.0.0-RC4
* Update nikic/PHP-Parser to v1.4.1

## v0.8.1 (2015/10/01)

### Fixed

* Fix bug that `$route['404_override']` controller/method is called in Bootstrap. See [#63](https://github.com/kenjis/ci-phpunit-test/pull/63).

## v0.8.0 (2015/09/28)

### Changed

* Better support for SQLite in-memory database. Now `reset_instance()` does not close SQLite in-memory database connection.

### Fixed

* Fix bug that `$this->getDouble()` can't create mocks which have methods named method.
* Fix bug that monkey patching which returns `null` does not work.

### Removed

* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class (deprecated since v0.4.0). See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md#show_error-and-show_404).

## v0.7.0 (2015/09/09)

### Upgrade Note

* Please update `application/tests/phpunit.xml`. Replace it or apply [this patch](https://github.com/kenjis/ci-phpunit-test/commit/7af9e330251e2ab72a631f4d5f92a41c0ad37aca). See [#52](https://github.com/kenjis/ci-phpunit-test/pull/52).

### Changed

* Now `$this->warningOff()` turns off Notice, too.

### Added

* Now `$this->request()` can create REST request more easily. See [#47](https://github.com/kenjis/ci-phpunit-test/pull/47).
* `$this->request->setHeader()` to set HTTP request header. See [#47](https://github.com/kenjis/ci-phpunit-test/pull/47).
* `$this->assertResponseHeader()` to assert HTTP response header. See [#47](https://github.com/kenjis/ci-phpunit-test/pull/47).
* You can add query string in URI string of `$this->request()`. See [#51](https://github.com/kenjis/ci-phpunit-test/pull/51).
* Autoloading for libraries
* Add `application/libraries/Session/MY_Session.php` as a sample
* `ReflectionHelper` class to access non-public method or property. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.7.0/docs/FunctionAndClassReference.md#class-reflectionhelper).

### Fixed

* `$this->request()` returns null when `show_404()` or `show_error()` is called. Now it returns error message.
* `$this->CI` in `TestCase` class after calling `$this->request()` is still the previous instance. [#50](https://github.com/kenjis/ci-phpunit-test/issues/50).
* Autoloader only searches class files only in top level and sub folder for them. [#48](https://github.com/kenjis/ci-phpunit-test/issues/48).
* `set_status_header()` calls `header()` if `is_cli()` returns false.
* Fix `phpunit.xml`. See [#52](https://github.com/kenjis/ci-phpunit-test/pull/52).

### Removed

* 4th param `$callable` of `$this->request()` and `$this->ajaxRequest()` (deprecated since v0.3.0)  
  Use `$this->request->setCallable()` method instead. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md#testcaserequestmethod-argv-params---callable--null).

### Others

* Add documentation for CodeIgniter Rest Server. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.7.0/docs/HowToWriteTests.md#codeigniter-rest-server).
* Compatible with PsySH v0.5.2

## v0.6.2 (2015/08/13)

### Fixed

* Fix bug that can't test model classes (classes in `application/models` folder) which do not extend `CI_Model` more than once.

## v0.6.1 (2015/08/12)

### Changed

* How to enable Monkey Patching has been changed. `TestCase::$enable_patcher` was removed. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#monkey-patching).

### Added

* Monkey Patching on functions. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#patching-functions).
* Monkey Patching on methods in user-defined classes. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.1/docs/HowToWriteTests.md#patching-methods-in-user-defined-classes).
* `$this->resetInstance()` for better model testing. See [#40](https://github.com/kenjis/ci-phpunit-test/pull/40).

### Removed

* `TestCase::$enable_patcher` (Introduced in v0.5.0)

### Others

* Compatible with CodeIgniter 3.0.1

## v0.5.0 (2015/07/27)

### Changed

* Now ci-phpunit-test replaces `redirect()` function by default. See [#33](https://github.com/kenjis/ci-phpunit-test/pull/33).

### Added

* Monkey Patching on `exit()`. ci-phpunit-test could convert `exit()` in your code to Exception on the fly. See [#32](https://github.com/kenjis/ci-phpunit-test/pull/32).
* `$this->request->setCallablePreConstructor()` to inject mocks into your controller constructors. See [#29](https://github.com/kenjis/ci-phpunit-test/pull/29).

### Fixed

* Fix bug that PHPUnit debug info of the first test is not outputted.

### Removed

* `get_new_instance()` (deprecated since pre v0.1.0)

## v0.4.0 (2015/07/21)

### Changed

* Changed `MY_url_helper.php` as sample. If you use new `MY_url_helper.php`, you must update your tests for `redirect()` using new `$this->assertRedirect()` method. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md#redirect) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).
* Changed how to test `show_404()` and `show_error()`. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md#show_error-and-show_404) and [#28](https://github.com/kenjis/ci-phpunit-test/pull/28).

### Added

* `$this->assertResponseCode()` to check response code in controller tests. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md#testcaseassertresponsecodecode).
* `$this->assertRedirect()` to check if `redirect()` is called in controller tests. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md#testcaseassertredirecturi-code--null).
* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Deprecated

* Property `$bc_mode_throw_PHPUnit_Framework_Exception` in `CIPHPUnitTestRequest` class

### Others

* Improved documentation. See [How to Write Test](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md).

## v0.3.0 (2015/07/14)

### Deprecated

* 4th param `$callable` of `$this->request()` and `$this->ajaxRequest()`. Use `$this->request->setCallable()` method instead. See [Function/Class Reference](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md#testcaserequestmethod-argv-params---callable--null).

### Added

* `$this->request->setCallable()` See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/HowToWriteTests.md#request-and-use-mocks).
* You can enable hooks for controller in controller testing. `$this->request->enableHooks()` is added. See [How to Write Tests](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/HowToWriteTests.md#controller-with-hooks).

## v0.2.0 (2015/06/19)

* Change `MY_url_helper.php` as sample. If you use new `MY_url_helper.php`, you must catch `PHPUnit_Framework_Exception` when you test code using `redirect()`.
* Improve documentation

## v0.1.1 (2015/06/15)

* Improve installation. See [Installation](https://github.com/kenjis/ci-phpunit-test#installation).
* Fix bug that Bootstrap outputs 404 page when 404_override
* Fix bug that risky tests occur [#14](https://github.com/kenjis/ci-phpunit-test/issues/14)

## v0.1.0 (2015/06/12)

* Initial version
* Compatible with CodeIgniter 3.0.0
