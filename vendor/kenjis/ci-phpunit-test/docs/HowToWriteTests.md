# ci-phpunit-test for CodeIgniter 3.0

version: **v0.12.1** | 
[v0.11.3](https://github.com/kenjis/ci-phpunit-test/blob/v0.11.3/docs/HowToWriteTests.md) | 
[v0.10.1](https://github.com/kenjis/ci-phpunit-test/blob/v0.10.1/docs/HowToWriteTests.md) | 
[v0.9.1](https://github.com/kenjis/ci-phpunit-test/blob/v0.9.1/docs/HowToWriteTests.md) | 
[v0.8.2](https://github.com/kenjis/ci-phpunit-test/blob/v0.8.2/docs/HowToWriteTests.md) | 
[v0.7.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.7.0/docs/HowToWriteTests.md) | 
[v0.6.2](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.2/docs/HowToWriteTests.md) | 
[v0.5.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.5.0/docs/HowToWriteTests.md) | 
[v0.4.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/HowToWriteTests.md) | 
[v0.3.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/HowToWriteTests.md) | 
[v0.2.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.2.0/docs/HowToWriteTests.md)

## How to Write Tests

- [Introduction](#introduction)
- [Testing Environment](#testing-environment)
- [Can and Can't](#can-and-cant)
	- [MY_Loader](#my_loader)
	- [MY_Input](#my_input)
	- [`exit()`](#exit)
	- [Reset CodeIgniter object](#reset-codeigniter-object)
	- [Hooks](#hooks)
	- [Autoloader](#autoloader)
- [Basic Conventions](#basic-conventions)
- [Models](#models)
	- [Using Database](#using-database)
	- [Database Seeding](#database-seeding)
	- [Using PHPUnit Mock Objects](#using-phpunit-mock-objects)
- [Libraries](#libraries)
- [Controllers](#controllers)
	- [Request to Controller](#request-to-controller)
	- [REST Request](#rest-request)
	- [Ajax Request](#ajax-request)
	- [Request and Use Mocks](#request-and-use-mocks)
	- [Request and Use Monkey Patching](#request-and-use-monkey-patching)
	- [Check Status Code](#check-status-code)
	- [Examine DOM in Controller Output](#examine-dom-in-controller-output)
	- [Controller with Authentication](#controller-with-authentication)
	- [`redirect()`](#redirect)
	- [`show_error()` and `show_404()`](#show_error-and-show_404)
	- [Session](#session)
	- [Controller with Hooks](#controller-with-hooks)
	- [Controller with Name Collision](#controller-with-name-collision)
- [Mock Libraries](#mock-libraries)
- [Monkey Patching](#monkey-patching)
	- [Converting `exit()` to Exception](#converting-exit-to-exception)
	- [Patching Functions](#patching-functions)
	- [Patching Methods in User-defined Classes](#patching-methods-in-user-defined-classes)
	- [Patching Constants](#patching-constants)
- [More Samples](#more-samples)
- [Third Party Libraries](#third-party-libraries)
	- [CodeIgniter Rest Server](#codeigniter-rest-server)
	- [Modular Extensions - HMVC](#modular-extensions---hmvc)

### Introduction

Here is my advice:

* You don't have to write your business logic in your controllers. Write them in your models.
* You should test models first, and test them well.

And PHPUnit has great documentation. You should read [Writing Tests for PHPUnit](https://phpunit.de/manual/current/en/writing-tests-for-phpunit.html).

If you are not familiar with *testing*, I recommend you read my book, *[CodeIgniter Testing Guide](https://leanpub.com/codeigniter-testing-guide)*. It is a beginners' guide to automated testing in PHP.

### Testing Environment

Tests always run on `testing` environment.

If you don't know well about config files and environments, see [CodeIgniter User Guide](http://www.codeigniter.com/user_guide/libraries/config.html#environments).

### Can and Can't

ci-phpunit-test does not want to modify CodeIgniter files. The more you modify them, the more you get difficulties when you update CodeIgniter.

In fact, it uses modified classes and some functions. But I try to modify as little as possible.

The core functions and classes which are modified:

* function `get_instance()`
* function `load_class()`
* function `is_loaded()`
* function `get_config()`
* function `config_item()`
* function `is_cli()`
* function `show_error()`
* function `show_404()`
* function `set_status_header()`
* class `CI_Loader`
* class `CI_Input`

and a helper which is modified:

* function `redirect()` in URL helper

All of them are placed in `tests/_ci_phpunit_test/replacing` folder.

And ci-phpunit-test adds properties dynamically:

* property `CI_Output::_status`
* property `CI_Output::_cookies`

And ci-phpunit-test has a modified bootstrap file:

* `core/CodeIgniter.php`

**Note to Maintainer:** If you modify another CodeIgniter file, update `bin/check-diff.sh` and `bin/check-ci-diff.sh`, too.

#### MY_Loader

ci-phpunit-test replaces `CI_Loader` and modifies below methods:

* `CI_Loader::model()`
* `CI_Loader::_ci_load_library()`
* `CI_Loader::_ci_load_stock_library()`

But if you place MY_Loader, your MY_Loader extends the loader of ci-phpunit-test.

If your MY_Loader overrides the above methods, you have to take care of changes in the loader of ci-phpunit-test.

#### MY_Input

ci-phpunit-test replaces `CI_Input` and modifies below method:

* `CI_Input::set_cookie()`
* `CI_Input::get_request_header()`

But if you place MY_Input, your MY_Input extends the CI_Input of ci-phpunit-test.

If your MY_Input overrides the above method, you have to take care of changes in the CI_Input of ci-phpunit-test.

#### `exit()`

When a test exercises code that contains `exit()` or `die()` statement, the execution of the whole test suite is aborted.

For example, if you write `exit()` in your controller code, your testing ends with it.

I recommend you not using `exit()` or `die()` in your code.

**Monkey Patching on `exit()`**

ci-phpunit-test has functionality that makes all `exit()` and `die()` in your code throw `CIPHPUnitTestExitException`.

See [Monkey Patching](#monkey-patching) for details.

**`show_error()` and `show_404()`**

And ci-phpunit-test has special [show_error() and show_404()](#show_error-and-show_404).

**`redirect()`**

ci-phpunit-test replaces `redirect()` function in URL helper. Using it, you can easily test controllers that contain `redirect()`. See [redirect()](#redirect) for details.

#### Reset CodeIgniter object

CodeIgniter has a function `get_instance()` to get the CodeIgniter object (CodeIgniter instance or CodeIgniter super object).

ci-phpunit-test has a new function [reset_instance()](FunctionAndClassReference.md#function-reset_instance) which reset the current CodeIgniter object. After resetting, you can (and must) create a new your Controller instance with new state.

#### Hooks

If you enable CodeIgniter's hooks, hook `pre_system` is called once in PHPUnit bootstrap.

If you use `$this->request->enableHooks()` and `$this->request()`, hook `pre_controller`, `post_controller_constructor`, `post_controller` and `display_override` are called on every `$this->request()` to a controller.

See [Controller with Hooks](#controller-with-hooks) for details.

#### Autoloader

ci-phpunit-test has an autoloader for class files.

To change the search paths, change the line [`CIPHPUnitTest::init();`](https://github.com/kenjis/ci-phpunit-test/blob/v0.12.1/application/tests/Bootstrap.php#L336) in `tests/Bootstrap.php` like below:

~~~php
CIPHPUnitTest::init([
	// Directories for autoloading
	APPPATH.'models',
	APPPATH.'libraries',
	APPPATH.'controllers',
	APPPATH.'modules',
]);
~~~

You must put all directories to search class files in the array.

### Basic Conventions

1. The tests for a class `Class` go into a class `Class_test`.
2. `Class_test` inherits from [TestCase](FunctionAndClassReference.md#class-testcase) class in ci-phpunit-test.
3. The tests are public methods that are named `test_*`. (Or you can use the `@test` annotation in a method's docblock to mark it as a test method.)

* Don't forget to write `parent::setUpBeforeClass();` if you override `setUpBeforeClass()` method.
* Don't forget to write `parent::tearDown();` if you override `tearDown()` method.

*tests/libraries/Foo_test.php*
~~~php
class Foo_test extends TestCase
{
	public function setUp()
	{
		$this->resetInstance();
		$this->CI->load->library('Foo');
		$this->obj = $this->CI->foo;
	}

	public function test_doSomething()
	{
		$actual = $this->obj->doSomething();
		$expected = 'something';
		$this->assertEquals($expected, $actual);
	}
}
~~~

[$this->resetInstance()](FunctionAndClassReference.md#testcaseresetinstance) method in ci-phpunit-test is a helper method to reset CodeIgniter instance and assign new CodeIgniter instance as `$this->CI`.

### Models

#### Using Database

*tests/models/Inventory_model_test.php*
~~~php
<?php

class Inventory_model_test extends TestCase
{
	public function setUp()
	{
		$this->resetInstance();
		$this->CI->load->model('shop/Inventory_model');
		$this->obj = $this->CI->Inventory_model;
	}

	public function test_get_category_list()
	{
		$expected = [
			1 => 'Book',
			2 => 'CD',
			3 => 'DVD',
		];
		$list = $this->obj->get_category_list();
		foreach ($list as $category) {
			$this->assertEquals($expected[$category->id], $category->name);
		}
	}

	public function test_get_category_name()
	{
		$actual = $this->obj->get_category_name(1);
		$expected = 'Book';
		$this->assertEquals($expected, $actual);
	}
}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/models/Category_model_test.php).

#### Database Seeding

I put [Seeder Library](../application/libraries/Seeder.php) and a sample [Seeder File](../application/database/seeds/CategorySeeder.php).

They are not installed, so if you want to use, copy them manually.

You can use them like below:

~~~php
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$CI =& get_instance();
		$CI->load->library('Seeder');
		$CI->seeder->call('CategorySeeder');
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/models/Category_model_test.php).

#### Using PHPUnit Mock Objects

You can use `$this->getMockBuilder()` method in PHPUnit and [$this->verifyInvoked*()](FunctionAndClassReference.md#testcaseverifyinvokedmock-method-params) helper method in ci-phpunit-test.

If you don't know well about PHPUnit Mock Objects, see [Test Doubles](https://phpunit.de/manual/current/en/test-doubles.html).

~~~php
	public function setUp()
	{
		$this->resetInstance();
		$this->CI->load->model('Category_model');
		$this->obj = $this->CI->Category_model;
	}

	public function test_get_category_list()
	{
		// Create mock objects for CI_DB_pdo_result and CI_DB_pdo_sqlite_driver
		$return = [
			0 => (object) ['id' => '1', 'name' => 'Book'],
			1 => (object) ['id' => '2', 'name' => 'CD'],
			2 => (object) ['id' => '3', 'name' => 'DVD'],
		];
		$db_result = $this->getMockBuilder('CI_DB_pdo_result')
			->disableOriginalConstructor()
			->getMock();
		$db_result->method('result')->willReturn($return);
		$db = $this->getMockBuilder('CI_DB_pdo_sqlite_driver')
			->disableOriginalConstructor()
			->getMock();
		$db->method('get')->willReturn($db_result);

		// Verify invocations
		$this->verifyInvokedOnce(
			$db_result,
			'result',
			[]
		);
		$this->verifyInvokedOnce(
			$db,
			'order_by',
			['id']
		);
		$this->verifyInvokedOnce(
			$db,
			'get',
			['category']
		);

		// Replace property db with mock object
		$this->obj->db = $db;

		$expected = [
			1 => 'Book',
			2 => 'CD',
			3 => 'DVD',
		];
		$list = $this->obj->get_category_list();
		foreach ($list as $category) {
			$this->assertEquals($expected[$category->id], $category->name);
		}
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/models/Category_model_mocking_db_test.php).

### Libraries

If your library depends on CodeIgniter functionality, I recommend using `setUp()` method like this:

~~~php
	public function setUp()
	{
		$this->resetInstance();
		$this->CI->load->library('Someclass');
		$this->obj = $this->CI->someclass;
	}
~~~

If your library is decoupled from CodeIgniter functionality, you can use `setUp()` method like this:

~~~php
	public function setUp()
	{
		$this->obj = new Someclass();
	}
~~~

In this case, ci-phpunit-test autoloads your libraries in `application/libraries` folder.

### Controllers

#### Request to Controller

You can use [$this->request()](FunctionAndClassReference.md#testcaserequestmethod-argv-params--) method in ci-phpunit-test.

~~~php
	public function test_uri_sub_sub_index()
	{
		$output = $this->request('GET', 'sub/sub/index');
		$this->assertContains('<title>Page Title</title>', $output);
	}
~~~

**Note:** If you pass URI string to the 2nd argument of `$this->request()`, it invokes the routing. If the resolved controller has `_remap()` and/or `_output()` methods, they will be invoked, too.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/sub/Sub_test.php).

If you want to call a controller method directly, you can pass an array to the 2nd argument of `$this->request()`.

*tests/controllers/Welcome_test.php*
~~~php
<?php

class Welcome_test extends TestCase
{
	public function test_index()
	{
		$output = $this->request('GET', ['Welcome', 'index']);
		$this->assertContains('<title>Welcome to CodeIgniter</title>', $output);
	}
}
~~~

**Note:** If you pass an array to the 2nd argument of `$this->request()`, it does not invokes the routing. The `_remap()` and/or `_output()` methods in a controller are not invoked, too.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Welcome_test.php).

#### REST Request

You can specify request method in 2nd argument of [$this->request()](FunctionAndClassReference.md#testcaserequestmethod-argv-params--) method and request body in 3rd argument of `$this->request()`.

~~~php
		$output = $this->request(
			'PUT', 'api/user', json_encode(['name' => 'mike'])
		);
~~~

~~~php
		$output = $this->request(
			'DELETE', 'api/key', 'key=12345678'
		);
~~~

You can set request header with [$this->request->setHeader()](FunctionAndClassReference.md#request-setheader) method in ci-phpunit-test. And you can confirm response header with [$this->assertResponseHeader()](FunctionAndClassReference.md#testcaseassertresponseheadername-value) method in ci-phpunit-test.

~~~php
	public function test_users_get_id_with_http_accept_header()
	{
		$this->request->setHeader('Accept', 'application/csv');
		$output = $this->request('GET', 'api/example/users/id/1');
		$this->assertEquals(
			'id,name,email,fact
1,John,john@example.com,"Loves coding"
',
			$output
		);
		$this->assertResponseCode(200);
		$this->assertResponseHeader(
			'Content-Type', 'application/csv; charset=utf-8'
		);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/api/Example_test.php).

#### Ajax Request

You can use [$this->ajaxRequest()](FunctionAndClassReference.md#testcaseajaxrequestmethod-argv-params--) method in ci-phpunit-test.

~~~php
	public function test_index_ajax_call()
	{
		$output = $this->ajaxRequest('GET', 'ajax/index');
		$expected = '{"name":"John Smith","age":33}';
		$this->assertEquals($expected, $output);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Ajax_test.php).

#### Request and Use Mocks

You can use [$this->request->setCallable()](FunctionAndClassReference.md#request-setcallable) method in ci-phpunit-test. [$this->getDouble()](FunctionAndClassReference.md#testcasegetdoubleclassname-params-enable_constructor--false) is a helper method in ci-phpunit-test.

~~~php
	public function test_send_okay()
	{
		$this->request->setCallable(
			function ($CI) {
				$email = $this->getDouble('CI_Email', ['send' => TRUE]);
				$CI->email = $email;
			}
		);
		$output = $this->request(
			'POST',
			['Contact', 'send'],
			[
				'name' => 'Mike Smith',
				'email' => 'mike@example.jp',
				'body' => 'This is test mail.',
			]
		);
		$this->assertContains('Mail sent', $output);
	}
~~~

**Note:** When you have not loaded a class with CodeIgniter loader, if you make a mock object for the class, your application code may not work correclty. If you have got an error, please try to load it with CodeIgniter loader, before getting the mock object.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Mock_phpunit_test.php).

The function you set by `$this->request->setCallable()` runs after controller instantiation. So you can't inject mocks into controller constructor.

##### Inject Mocks into Controller Constructors

For example, if you have a controller like this:

~~~php
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('ion_auth');
		if ( ! $this->ion_auth->logged_in())
		{
			$this->load->helper('url');
			redirect('auth/login');
		}
	}

	...
}
~~~

In this case, You can use [$this->request->setCallablePreConstructor()](FunctionAndClassReference.md#request-setcallablepreconstructor) method and [load_class_instance()](FunctionAndClassReference.md#function-load_class_instanceclassname-instance) function in ci-phpunit-test.

**Note:** Unlike `$this->request->setCallable()`, this callback runs before the controller is created. So there is no CodeIgniter instance yet. You can't use CodeIgniter objects.

~~~php
	public function test_index_logged_in()
	{
		$this->request->setCallablePreConstructor(
			function () {
				// Get mock object
				$auth = $this->getDouble(
					'Ion_auth', ['logged_in' => TRUE]
				);
				// Inject mock object
				load_class_instance('ion_auth', $auth);
			}
		);

		$output = $this->request('GET', 'auth/login');
		$this->assertContains('You are logged in.', $output);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Auth_check_in_construct_test.php).

**Note:** If you can't create mocks or it is too hard to create mocks, it may be better to use Monkey Patching.

#### Request and Use Monkey Patching

To use Monkey Patching, you have to enable it. See [Monkey Patching](#monkey-patching).

~~~php
	public function test_index_logged_in()
	{
		MonkeyPatch::patchMethod('Ion_auth', ['logged_in' => TRUE]);

		$output = $this->request('GET', 'auth/login');
		$this->assertContains('You are logged in.', $output);
	}
~~~

See also [Patching Methods in User-defined Classes](#patching-methods-in-user-defined-classes).

#### Check Status Code

You can use [$this->assertResponseCode()](FunctionAndClassReference.md#testcaseassertresponsecodecode) method in ci-phpunit-test.

~~~php
		$this->request('GET', 'welcome');
		$this->assertResponseCode(200);
~~~

#### Examine DOM in Controller Output

I recommend using [symfony/dom-crawler](http://symfony.com/doc/current/components/dom_crawler.html).

~~~php
		$output = $this->request('GET', ['Welcome', 'index']);
		$crawler = new Symfony\Component\DomCrawler\Crawler($output);
		// Get the text of the first <h1>
		$text = $crawler->filter('h1')->eq(0)->text();
~~~

See [working sample](https://github.com/kenjis/codeigniter-tettei-apps/blob/develop/application/tests/controllers/Bbs_test.php#L126-128).

#### Controller with Authentication

I recommend using PHPUnit mock objects. [$this->getDouble()](FunctionAndClassReference.md#testcasegetdoubleclassname-params-enable_constructor--false) is a helper method in ci-phpunit-test.

~~~php
	public function test_index_logged_in()
	{
		$this->request->setCallable(
			function ($CI) {
				// Get mock object
				$auth = $this->getDouble(
					'Ion_auth', ['logged_in' => TRUE, 'is_admin' => TRUE]
				);
				// Inject mock object
				$CI->ion_auth = $auth;
			}
		);
		$output = $this->request('GET', ['Auth', 'index']);
		$this->assertContains('<p>Below is a list of the users.</p>', $output);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Auth_test.php).

#### `redirect()`

By default, ci-phpunit-test replaces `redirect()` function in URL helper. Using it, you can easily test controllers that contain `redirect()`.

But you could still override `redirect()` using your `MY_url_helper.php`. If you place `MY_url_helper.php`, your `redirect()` will be used.

If you use `redirect()` in ci-phpunit-test, you can write tests like this:

~~~php
	public function test_index()
	{
		$this->request('GET', ['Admin', 'index']);
		$this->assertRedirect('login', 302);
	}
~~~

[$this->assertRedirect()](FunctionAndClassReference.md#testcaseassertredirecturi-code--null) is a method in ci-phpunit-test.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Redirect_test.php).

##### Upgrade Note for v0.4.0

v0.4.0 has new `MY_url_helper.php`. If you use it, you must update your tests.

*before:*
~~~php
	/**
	 * @expectedException				PHPUnit_Framework_Exception
	 * @expectedExceptionCode			302
	 * @expectedExceptionMessageRegExp	!\ARedirect to http://localhost/\z!
	 */
	public function test_index()
	{
		$this->request('GET', ['Redirect', 'index']);
	}
~~~

↓

*after:*
~~~php
	public function test_index()
	{
		$this->request('GET', ['Redirect', 'index']);
		$this->assertRedirect('/', 302);
	}
~~~

#### `show_error()` and `show_404()`

You can use [$this->assertResponseCode()](FunctionAndClassReference.md#testcaseassertresponsecodecode) method in ci-phpunit-test.

~~~php
	public function test_index()
	{
		$this->request('GET', ['nocontroller', 'noaction']);
		$this->assertResponseCode(404);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Nocontroller_test.php).

If you don't call `$this->request()` in your tests, `show_error()` throws `CIPHPUnitTestShowErrorException` and `show_404()` throws `CIPHPUnitTestShow404Exception`. So you must expect the exceptions. You can use `@expectedException` annotation in PHPUnit.

##### Upgrade Note for v0.4.0

v0.4.0 has changed how to test `show_error()` and `show_404()`. You must update your tests.

*before:*
~~~php
	/**
	 * @expectedException		PHPUnit_Framework_Exception
	 * @expectedExceptionCode	404
	 */
	public function test_index()
	{
		$this->request('GET', 'show404');
	}
~~~

↓

*after:*
~~~php
	public function test_index()
	{
		$this->request('GET', 'show404');
		$this->assertResponseCode(404);
	}
~~~

#### Session

If you run CodeIgniter via CLI, CodeIgniter's Session class does not call `session_start()`. So normally you don't see warning like "session_start(): Cannot send session cookie - headers already sent by ...".

But if libraries which you use have logic runs only when not in CLI mode, you have to use `set_is_cli(FALSE)` for testing. (Don't forget calling `set_is_cli(TRUE)` after running the code.)

In that case, Session class calls `session_start()` and you will see "Cannot send session cookie" warning.

To test that code, you can add `$this->warningOff()` to your test code (don't forget calling `$this->warningOn()` after running the code), or you can use *MY_Session* class like this: [application/libraries/Session/MY_Session.php](../application/libraries/Session/MY_Session.php).

#### Controller with Hooks

If you want to enable hooks, call [$this->request->enableHooks()](FunctionAndClassReference.md#request-enablehooks) method. It enables `pre_controller`, `post_controller_constructor`, `post_controller` and `display_override` hooks.

~~~php
		$this->request->enableHooks();
		$output = $this->request('GET', 'products/shoes/show/123');
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Hook_test.php).

#### Controller with Name Collision

If you have two controllers with the exact same name, PHP Fatal error stops PHPUnit testing.

In this case, you can use PHPUnit annotations `@runInSeparateProcess` and `@preserveGlobalState disabled`. But tests in a separate PHP process are very slow.

*tests/controllers/sub/Welcome_test.php*
~~~php
<?php

class sub_Welcome_test extends TestCase
{
	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_uri_sub_welcome_index()
	{
		$output = $this->request('GET', 'sub/welcome/index');
		$this->assertContains('<title>Page Title</title>', $output);
	}
}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/sub/Welcome_test.php).

### Mock Libraries

You can put mock libraries in `tests/mocks/libraries` folder. You can see [application/tests/mocks/libraries/email.php](../application/tests/mocks/libraries/email.php) as a sample.

With mock libraries, you could replace your object in CodeIgniter instance.

This is how to replace Email library with `Mock_Libraries_Email` class.

~~~php
	public function setUp()
	{
		$this->resetInstance();
		$this->CI->load->model('Mail_model');
		$this->obj = $this->CI->Mail_model;
		$this->CI->email = new Mock_Libraries_Email();
	}
~~~

Mock library class name must be `Mock_Libraries_*`, and it is autoloaded.

### Monkey Patching

ci-phpunit-test has three monkey patchers.

* `ExitPatcher`: Converting `exit()` to Exception
* `FunctionPatcher`: Patching Functions
* `MethodPatcher`: Patching Methods in User-defined Classes
* `ConstantPatcher`: Changing Constant Values

**Note:** This functionality has a negative impact on speed of tests.

To enable monkey patching, uncomment below code in `tests/Bootstrap.php` and configure them:

~~~php
/*
require __DIR__ . '/_ci_phpunit_test/patcher/bootstrap.php';
MonkeyPatchManager::init([
	// PHP Parser: PREFER_PHP7, PREFER_PHP5, ONLY_PHP7, ONLY_PHP5
	'php_parser' => 'PREFER_PHP5',
	'cache_dir' => APPPATH . 'tests/_ci_phpunit_test/tmp/cache',
	// Directories to patch on source files
	'include_paths' => [
		APPPATH,
		BASEPATH,
		APPPATH . 'tests/_ci_phpunit_test/replacing/',
	],
	// Excluding directories to patch
	'exclude_paths' => [
		APPPATH . 'tests/',
		'-' . APPPATH . 'tests/_ci_phpunit_test/replacing/',
	],
	// All patchers you use.
	'patcher_list' => [
		'ExitPatcher',
		'FunctionPatcher',
		'MethodPatcher',
		'ConstantPatcher',
	],
	// Additional functions to patch
	'functions_to_patch' => [
		//'random_string',
	],
	'exit_exception_classname' => 'CIPHPUnitTestExitException',
]);
*/
~~~

##### Upgrade Note for v0.11.0

Add the below line in `include_paths`.

~~~php
		APPPATH . 'tests/_ci_phpunit_test/replacing/',
~~~

And add the below line in `exclude_paths`.

~~~php
		'-' . APPPATH . 'tests/_ci_phpunit_test/replacing/',
~~~

You can add the parser preference with `php_parser`. The default is `PREFER_PHP5`. Change the config if you need.

~~~php
	// PHP Parser: PREFER_PHP7, PREFER_PHP5, ONLY_PHP7, ONLY_PHP5
	'php_parser' => 'PREFER_PHP5',
~~~

##### Upgrade Note for v0.6.0

Add the above code (`require` and `MonkeyPatchManager::init()`) before

~~~php
/*
 * -------------------------------------------------------------------
 *  Added for ci-phpunit-test
 * -------------------------------------------------------------------
 */
~~~

in [tests/Bootstrap.php](https://github.com/kenjis/ci-phpunit-test/blob/v0.5.0/application/tests/Bootstrap.php).

`TestCase::$enable_patcher` was removed. Please remove it.

#### Converting `exit()` to Exception

This patcher converts `exit()` or `die()` statements to exceptions on the fly.

If you have a controller like below:

~~~php
	public function index()
	{
		$this->output
			->set_status_header(200)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode(['foo' => 'bar']))
			->_display();
		exit();
	}
~~~

A test case could be like this:

~~~php
	public function test_index()
	{
		try {
			$this->request('GET', 'welcome/index');
		} catch (CIPHPUnitTestExitException $e) {
			$output = ob_get_clean();
		}
		$this->assertContains('{"foo":"bar"}', $output);
	}
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Exit_to_exception_test.php).

#### Patching Functions

This patcher allows replacement of global functions that can't be mocked by PHPUnit.

But it has a few limitations. Some functions can't be replaced and it might cause errors.

So by default we can replace only a dozen pre-defined functions in [FunctionPatcher](https://github.com/kenjis/ci-phpunit-test/blob/v0.12.1/application/tests/_ci_phpunit_test/patcher/Patcher/FunctionPatcher.php#L27).

~~~php
	public function test_index()
	{
		MonkeyPatch::patchFunction('mt_rand', 100, 'Welcome::index');
		$output = $this->request('GET', 'welcome/index');
		$this->assertContains('100', $output);
	}
~~~

[MonkeyPatch::patchFunction()](FunctionAndClassReference.md#monkeypatchpatchfunctionfunction-return_value-class_method) replaces PHP native function `mt_rand()` in `Welcome::index` method, and it will return `100` in the test method.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Patching_on_function_test.php).

**Note:** If you call `MonkeyPatch::patchFunction()` without 3rd argument, all the functions (located in `include_paths` and not in `exclude_paths`) called in the test method will be replaced. So, for example, a function in CodeIgniter code might be replaced and it results in unexpected outcome.

**Change Return Value**

You could change return value of patched function using PHP closure:

~~~php
		MonkeyPatch::patchFunction(
			'function_exists',
			function ($function) {
				if ($function === 'random_bytes')
				{
					return true;
				}
				elseif ($function === 'openssl_random_pseudo_bytes')
				{
					return false;
				}
				elseif ($function === 'mcrypt_create_iv')
				{
					return false;
				}
				else
				{
					return __GO_TO_ORIG__;
				}
			},
			'Welcome'
		);
~~~

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Patching_on_function_test.php#L59-L80).

**Patch on Other Functions**

If you want to patch other functions, you can add them to [functions_to_patch](https://github.com/kenjis/ci-phpunit-test/blob/v0.12.1/application/tests/Bootstrap.php#L323) in `MonkeyPatchManager::init()`.

But there are a few known limitations:

* Patched functions which have parameters called by reference don't work.
* You may see visibility errors if you pass non-public callbacks to patched functions. For example, you pass `[$this, 'method']` to `array_map()` and the `method()` method in the class is not public.

#### Patching Methods in User-defined Classes

This patcher allows replacement of methods in user-defined classes.

~~~php
	public function test_index()
	{
		MonkeyPatch::patchMethod(
			'Category_model',
			['get_category_list' => [(object) ['name' => 'Nothing']]]
		);
		$output = $this->request('GET', 'welcome/index');
		$this->assertContains('Nothing', $output);
	}
~~~

[MonkeyPatch::patchMethod()](FunctionAndClassReference.md#monkeypatchpatchmethodclassname-params) replaces `get_category_list()` method in `Category_model`, and it will return `[(object) ['name' => 'Nothing']]` in the test method.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/v0.12.1/application/tests/controllers/Patching_on_method_test.php).

#### Patching Constants

This patcher allows replacement of constant value.

~~~php
	public function test_index()
	{
		MonkeyPatch::patchConstant('ENVIRONMENT', 'development', 'Welcome::index');
		$output = $this->request('GET', 'welcome/index');
		$this->assertContains('development', $output);
	}
~~~

[MonkeyPatch::patchConstant()](FunctionAndClassReference.md#monkeypatchpatchconstantconstant-value-class_method) replaces the return value of the constant `ENVIRONMENT` in `Welcome::index` method.

There are a few known limitations:

* Cannot patch constants that are used as default values in function arguments.
* Cannot patch constants that are used as default values in constant declarations.
* Cannot patch constants that are used as default values in property declarations.
* Cannot patch constants that are used as default values in static variable declarations.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/blob/master/application/tests/controllers/Patching_on_constant_test.php).

##### Upgrade Note for v0.12.0

If you want to use the constant patcher, please add `ConstantPatcher` in the `patcher_list` in [tests/Bootstrap.php](https://github.com/kenjis/ci-phpunit-test/blob/master/application/tests/Bootstrap.php#L340).

*before:*
~~~php
	// All patchers you use.
	'patcher_list' => [
		'ExitPatcher',
		'FunctionPatcher',
		'MethodPatcher',
	],
~~~

↓

*after:*
~~~php
	// All patchers you use.
	'patcher_list' => [
		'ExitPatcher',
		'FunctionPatcher',
		'MethodPatcher',
		'ConstantPatcher',  // Add this
	],
~~~

### More Samples

Want to see more tests?

* https://github.com/kenjis/ci-app-for-ci-phpunit-test/tree/v0.12.0/application/tests
* https://github.com/kenjis/codeigniter-tettei-apps/tree/develop/application/tests

### Third Party Libraries

ci-phpunit-test has powerful functionality for testing. So normally you don't have to modify your application or library code.

But there are still libraries which can't be tested without code modification.

#### [CodeIgniter Rest Server](https://github.com/chriskacerguis/codeigniter-restserver/)

codeigniter-restserver calls `exit()`. So you have to enable [Monkey Patching](#monkey-patching) and at least you have to use `ExitPatcher`.

Additionally you have to apply patch on `application/libraries/REST_Controller.php`.

This is patch for codeigniter-restserver 2.7.2:

~~~diff
--- a/application/libraries/REST_Controller.php
+++ b/application/libraries/REST_Controller.php
@@ -653,6 +653,11 @@ abstract class REST_Controller extends CI_Controller {
         {
             call_user_func_array([$this, $controller_method], $arguments);
         }
+        catch (CIPHPUnitTestExitException $ex)
+        {
+            // This block is for ci-phpunit-test
+            throw $ex;
+        }
         catch (Exception $ex)
         {
             // If the method doesn't exist, then the error will be caught and an error response shown
~~~

Then, you can write test case class like this:

*tests/controllers/api/Example_test.php*
~~~php
<?php

class Example_test extends TestCase
{
	public function test_users_get()
	{
		try {
			$output = $this->request('GET', 'api/example/users');
		} catch (CIPHPUnitTestExitException $e) {
			$output = ob_get_clean();
		}

		$this->assertEquals(
			'[{"id":1,"name":"John","email":"john@example.com","fact":"Loves coding"},{"id":2,"name":"Jim","email":"jim@example.com","fact":"Developed on CodeIgniter"},{"id":3,"name":"Jane","email":"jane@example.com","fact":"Lives in the USA","0":{"hobbies":["guitar","cycling"]}}]',
			$output
		);
		$this->assertResponseCode(200);
	}
}
~~~

And if you copy sample api controllers, you must change `require` statement to `require_once`:

~~~diff
--- a/application/controllers/api/Example.php
+++ b/application/controllers/api/Example.php
@@ -3,7 +3,7 @@
 defined('BASEPATH') OR exit('No direct script access allowed');
 
 // This can be removed if you use __autoload() in config.php OR use Modular Extensions
-require APPPATH . '/libraries/REST_Controller.php';
+require_once APPPATH . '/libraries/REST_Controller.php';
 
 /**
  * This is an example of a few basic user interaction methods you could use
~~~

If you require `REST_Controller.php` more than once, you get `Fatal error: Cannot redeclare class REST_Controller`.

See [working sample](https://github.com/kenjis/ci-app-for-ci-phpunit-test/tree/v0.12.0/application/tests/controllers/api).

#### [Modular Extensions - HMVC](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc)

It seems some users try to work ci-phpunit-test with the HMVC, and they work mostly. But the HMVC is a very complex system, and is against CodeIgniter's basic design. It brings complexity to CodeIgniter.

There is a known limitation:
See <https://github.com/kenjis/ci-hmvc-ci-phpunit-test#note-to-use>.

And if you have an issue, please report it to: <https://github.com/kenjis/ci-phpunit-test/issues/34>

See [working sample](https://github.com/kenjis/ci-hmvc-ci-phpunit-test).
