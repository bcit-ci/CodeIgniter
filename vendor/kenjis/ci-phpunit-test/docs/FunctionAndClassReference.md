# ci-phpunit-test for CodeIgniter 3.0

version: **v0.12.1** | 
[v0.11.3](https://github.com/kenjis/ci-phpunit-test/blob/v0.11.3/docs/FunctionAndClassReference.md) | 
[v0.10.1](https://github.com/kenjis/ci-phpunit-test/blob/v0.10.1/docs/FunctionAndClassReference.md) | 
[v0.9.1](https://github.com/kenjis/ci-phpunit-test/blob/v0.9.1/docs/FunctionAndClassReference.md) | 
[v0.8.2](https://github.com/kenjis/ci-phpunit-test/blob/v0.8.2/docs/FunctionAndClassReference.md) | 
[v0.7.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.7.0/docs/FunctionAndClassReference.md) | 
[v0.6.2](https://github.com/kenjis/ci-phpunit-test/blob/v0.6.2/docs/FunctionAndClassReference.md) | 
[v0.5.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.5.0/docs/FunctionAndClassReference.md) | 
[v0.4.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.4.0/docs/FunctionAndClassReference.md) | 
[v0.3.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.3.0/docs/FunctionAndClassReference.md) | 
[v0.2.0](https://github.com/kenjis/ci-phpunit-test/blob/v0.2.0/docs/FunctionAndClassReference.md)

## Function/Class Reference

- [*function* `reset_instance()`](#function-reset_instance)
- [*function* `set_is_cli($return)`](#function-set_is_clireturn)
- [*function* `load_class_instance($classname, $instance)`](#function-load_class_instanceclassname-instance)
- [*class* TestCase](#class-testcase)
	- [`TestCase::resetInstance()`](#testcaseresetinstance)
	- [`TestCase::request($method, $argv, $params = [])`](#testcaserequestmethod-argv-params--)
		- [`request->setHeader()`](#request-setheader)
		- [`request->setCallable()`](#request-setcallable)
		- [`request->addCallable()`](#request-addcallable)
		- [`request->setCallablePreConstructor()`](#request-setcallablepreconstructor)
		- [`request->addCallablePreConstructor()`](#request-addcallablepreconstructor)
		- [`request->enableHooks()`](#request-enablehooks)
	- [`TestCase::ajaxRequest($method, $argv, $params = [])`](#testcaseajaxrequestmethod-argv-params--)
	- [`TestCase::assertResponseCode($code)`](#testcaseassertresponsecodecode)
	- [`TestCase::assertRedirect($uri, $code = null)`](#testcaseassertredirecturi-code--null)
	- [`TestCase::assertResponseHeader($name, $value)`](#testcaseassertresponseheadername-value)
	- [`TestCase::assertResponseCookie($name, $value, $allow_duplicate = false)`](#testcaseassertresponsecookiename-value-allow_duplicate--false)
	- [`TestCase::getDouble($classname, $params, $enable_constructor = false)`](#testcasegetdoubleclassname-params-enable_constructor--false)
	- [`TestCase::verifyInvoked($mock, $method, $params)`](#testcaseverifyinvokedmock-method-params)
	- [`TestCase::verifyInvokedOnce($mock, $method, $params)`](#testcaseverifyinvokedoncemock-method-params)
	- [`TestCase::verifyInvokedMultipleTimes($mock, $method, $times, $params)`](#testcaseverifyinvokedmultipletimesmock-method-times-params)
	- [`TestCase::verifyNeverInvoked($mock, $method, $params)`](#testcaseverifyneverinvokedmock-method-params)
	- [`TestCase::warningOff()`](#testcasewarningoff)
	- [`TestCase::warningOn()`](#testcasewarningon)
- [*class* ReflectionHelper](#class-reflectionhelper)
	- [`ReflectionHelper::getPrivateProperty($obj, $property)`](#reflectionhelpergetprivatepropertyobj-property)
	- [`ReflectionHelper::setPrivateProperty($obj, $property, $value)`](#reflectionhelpersetprivatepropertyobj-property-value)
	- [`ReflectionHelper::getPrivateMethodInvoker($obj, $method)`](#reflectionhelpergetprivatemethodinvokerobj-method)
- [*class* MonkeyPatch](#class-monkeypatch)
	- [`MonkeyPatch::patchFunction($function, $return_value, $class_method)`](#monkeypatchpatchfunctionfunction-return_value-class_method)
	- [`MonkeyPatch::resetFunctions()`](#monkeypatchresetfunctions)
	- [`MonkeyPatch::patchMethod($classname, $params)`](#monkeypatchpatchmethodclassname-params)
	- [`MonkeyPatch::resetMethods()`](#monkeypatchresetmethods)
	- [`MonkeyPatch::patchConstant($constant, $value, $class_method)`](#monkeypatchpatchconstantconstant-value-class_method)
	- [`MonkeyPatch::resetConstants()`](#monkeypatchresetconstants)
	- [`MonkeyPatch::verifyInvoked($class_method, $params)`](#monkeypatchverifyinvokedclass_method-params)
	- [`MonkeyPatch::verifyInvokedOnce($class_method, $params)`](#monkeypatchverifyinvokedonceclass_method-params)
	- [`MonkeyPatch::verifyInvokedMultipleTimes($class_method, $times, $params)`](#monkeypatchverifyinvokedmultipletimesclass_method-times-params)
	- [`MonkeyPatch::verifyNeverInvoked($class_method, $params)`](#monkeypatchverifyneverinvokedclass_method-params)

### *function* `reset_instance()`

Resets CodeIgniter instance. You must create a new controller instance after calling this function.

~~~php
reset_instance();
$controller = new Welcome();
$this->CI =& get_instance();
~~~

Normally, you don't have to use this function. Use [`TestCase::resetInstance()`](#testcaseresetinstance) method instead.

**Note:** Before you create a new controller instance, `get_instance()` returns `CIPHPUnitTestNullCodeIgniter` object.

### *function* `set_is_cli($return)`

| param   | type | description         |
|---------|------|---------------------|
|`$return`| bool | return value to set |

Sets return value of `is_cli()` function.

~~~php
set_is_cli(FALSE);
~~~

### *function* `load_class_instance($classname, $instance)`

| param      | type   | description     |
|------------|--------|-----------------|
|`$classname`| string | class name      |
|`$instance` | object | object instance |

Injects an instance directly into `load_class()` function.

~~~php
$email = $this->getMockBuilder('CI_Email')
	->setMethods(['send'])
	->getMock();
$email->method('send')
	->willReturn(TRUE);
load_class_instance('email', $email);
~~~

### *class* TestCase

#### `TestCase::resetInstance()`

Resets CodeIgniter instance and assign new CodeIgniter instance as `$this->CI`.

~~~php
public function setUp()
{
	$this->resetInstance();
	$this->CI->load->model('Category_model');
	$this->obj = $this->CI->Category_model;
}
~~~

**Note:** When you call [$this->request()](#testcaserequestmethod-argv-params--), you don't have to use this method. Because `$this->request()` resets CodeIgniter instance internally.

**Upgrade Note for v0.6.0**

Before v0.6.0, we write `setUp()` method like this:

~~~php
public function setUp()
{
	$this->CI =& get_instance();
	$this->CI->load->model('Category_model');
	$this->obj = $this->CI->Category_model;
}
~~~

When you use the way, you use the same CodeIgniter instance and the same `Category_model` instance in every test method.

In contrast, if you use `$this->resetInstance()`, it resets CodeIgniter instance and `Category_model`. So you use new CodeIgniter instance and new `Category_model` instance in every test method.

#### `TestCase::request($method, $argv, $params = [])`

| param     | type         | description                                   |
|-----------|--------------|-----------------------------------------------|
|`$method`  | string       | HTTP method                                   |
|`$argv`    | array/string | controller, method [, arg1, ...] / URI string |
|`$params`  | array/string | POST params or GET params / raw_input_stream  |

`returns` (string) output strings (view)

Runs a controller method or make a request to URI string, after `reset_instance()`.

If you want to invoke routing, specify URI string:

~~~php
$output = $this->request('GET', 'products/shoes/show/123');
~~~

You could add query string in URI string:

~~~php
$output = $this->request('GET', 'users/detail?name=John+O%27Reilly');
~~~

If you want to make POST request:

~~~php
$output = $this->request(
	'POST',
	'form/index',
	['name' => 'John Smith', 'email' => 'john@example.com']
);
~~~

If you want to call a controller method directly:

~~~php
$output = $this->request('GET', ['Form', 'index']);
~~~

**Note:** If you pass an array to the 2nd argument, it does not invoke routing, `_remap()` and `_output()` methods.

##### `request->setHeader()`

Sets HTTP request header.

~~~php
$this->request->setHeader('Accept', 'application/csv');
~~~

##### `request->setCallable()`

Sets (and resets) a function (callable) to run after controller instantiation.

~~~php
$this->request->setCallable(
	function ($CI) {
		$CI->load->library('user_agent');
	};
);
$output = $this->request('GET', ['Bbs', 'index']);
~~~

You can set one callable with `$this->request->setCallable()`. If you want to add more than one callable, you can use `$this->request->addCallable()` below.

##### `request->addCallable()`

Adds a function (callable) to run after controller instantiation.

~~~php
$this->request->addCallable(
	function ($CI) {
		$CI->load->library('user_agent');
	};
);
$output = $this->request('GET', ['Bbs', 'index']);
~~~

##### `request->setCallablePreConstructor()`

Sets (and resets) a function to run before controller instantiation.

~~~php
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
~~~

##### `request->addCallablePreConstructor()`

Adds a function (callable) to run before controller instantiation.

~~~php
$this->request->addCallablePreConstructor(
	function () {
		// Get mock object
		$auth = $this->getDouble(
			'Ion_auth', ['logged_in' => TRUE]
		);
		// Inject mock object
		load_class_instance('ion_auth', $auth);
	}
);
~~~

##### `request->enableHooks()`

If you want to enable hooks, call `$this->request->enableHooks()` method. It enables only `pre_controller`, `post_controller_constructor`, `post_controller` and `display_override` hooks.

~~~php
$this->request->enableHooks();
$output = $this->request('GET', 'products/shoes/show/123');
~~~

#### `TestCase::ajaxRequest($method, $argv, $params = [])`

| param     | type         | description                                   |
|-----------|--------------|-----------------------------------------------|
|`$method`  | string       | HTTP method                                   |
|`$argv`    | array/string | controller, method [, arg1, ...] / URI string |
|`$params`  | array/string | POST params or GET params / raw_input_stream  |

`returns` (string) output strings

The same as `TestCase::request()`, but this makes an Ajax request. This adds only `$_SERVER['HTTP_X_REQUESTED_WITH']`.

~~~php
$output = $this->ajaxRequest('GET', 'api/books');
~~~

#### `TestCase::assertResponseCode($code)`

| param   | type | description      |
|---------|------|------------------|
|`$code`  | int  | HTTP status code |

Checks for a specific response code in your controller tests.

~~~php
$this->assertResponseCode(200);
~~~

#### `TestCase::assertRedirect($uri, $code = null)`

| param   | type   | description      |
|---------|--------|------------------|
|`$uri`   | string | URI to redirect  |
|`$code`  | int    | HTTP status code |

Checks if `redirect()` is called in your controller tests.

~~~php
$this->assertRedirect('auth/login');
~~~

#### `TestCase::assertResponseHeader($name, $value)`

| param   | type   | description  |
|---------|--------|--------------|
|`$name`  | string | header name  |
|`$value` | string | header value |

Checks for a specific response header in your controller tests.

~~~php
$this->assertResponseHeader(
	'Content-Type', 'application/csv; charset=utf-8'
);
~~~

**Note:** This method can only assert headers set by `$this->output->set_header()` method.

#### `TestCase::assertResponseCookie($name, $value, $allow_duplicate = false)`

| param             | type         | description                           |
|-------------------|--------------|---------------------------------------|
|`$name`            | string       | cookie name                           |
|`$value`           | string/array | cookie value / array of cookie params |
|`$allow_duplicate` | bool         | whether to allow duplicated cookies   |

Checks for a specific response cookie in your controller tests.

~~~php
$this->assertResponseCookie('cookie-name', 'cookie value');
~~~

You can also check cookie params.

~~~php
$this->assertResponseCookie(
	'cookie-name',
	[
		'value'  => 'cookie value',
		'domain' => '.example.com',
		'path'   => '/',
		'secure' => TRUE,
		'httponly' => TRUE,
	]
);
~~~

**Note:** This method can only assert cookies set by `$this->input->set_cooke()` method.

#### `TestCase::getDouble($classname, $params, $enable_constructor = false)`

| param               | type    | description                   |
|---------------------|---------|-------------------------------|
|`$classname`         | string  | class name                    |
|`$params`            | array   | [method_name => return_value] |
|`$enable_constructor`| bool    | enable constructor or not     |

`returns` (object) PHPUnit mock object

Gets PHPUnit mock object.

~~~php
$email = $this->getMockBuilder('CI_Email')
	->disableOriginalConstructor()
	->setMethods(['send'])
	->getMock();
$email->method('send')
	->willReturn(TRUE);
~~~

You could write code above like below:

~~~php
$email = $this->getDouble('CI_Email', ['send' => TRUE]);
~~~

**Upgrade Note for v0.10.0**

v0.10.0 has changed the default behavior of `$this->getDouble()` and disabled original constructor. If the change causes errors, update your test code like below:

*before:*
~~~php
$validation = $this->getDouble('CI_Form_validation', ['run' => TRUE]);
~~~

↓

*after:*
~~~php
$validation = $this->getDouble('CI_Form_validation', ['run' => TRUE], TRUE);
~~~

#### `TestCase::verifyInvoked($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies a method was invoked at least once.

~~~php
$loader->expects($this->atLeastOnce())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvoked(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::verifyInvokedOnce($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies that method was invoked only once.

~~~php
$loader->expects($this->once())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvokedOnce(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::verifyInvokedMultipleTimes($mock, $method, $times, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$times` | int    | times               |
|`$params`| array  | arguments           |

Verifies that method was called exactly $times times.

~~~php
$loader->expects($this->exactly(2))
	->method('view')
	->withConsecutive(
		['shop_confirm', $this->anything(), TRUE],
		['shop_tmpl_checkout', $this->anything()]
	);
~~~

You could write code above like below:

~~~php
$this->verifyInvokedMultipleTimes(
	$loader,
	'view',
	2,
	[
		['shop_confirm', $this->anything(), TRUE],
		['shop_tmpl_checkout', $this->anything()]
	]
);
~~~

#### `TestCase::verifyNeverInvoked($mock, $method, $params)`

| param   | type   | description         |
|---------|--------|---------------------|
|`$mock`  | object | PHPUnit mock object |
|`$method`| string | method name         |
|`$params`| array  | arguments           |

Verifies that method was not called.

~~~php
$loader->expects($this->never())
	->method('view')
	->with(
		['shop_confirm', $this->anything(), TRUE]
	);
~~~

You could write code above like below:

~~~php
$this->verifyNeverInvoked(
	$loader,
	'view',
	[
		['shop_confirm', $this->anything(), TRUE]
	]
);
~~~

#### `TestCase::warningOff()`

Turns off WARNING and Notice in PHP error reporting.

~~~php
$this->warningOff();
$output = $this->request('GET', 'api/example/users');
$this->warningOn();
~~~

#### `TestCase::warningOn()`

Restores PHP error reporting.

~~~php
$this->warningOn();
~~~

### *class* ReflectionHelper

This class provides helper methods to access private or protected properties and methods.

But generally it is not recommended to test non-public properties or methods, so think twice before you use methods in this class.

#### ReflectionHelper::getPrivateProperty($obj, $property)

| param     | type          | description         |
|-----------|---------------|---------------------|
|`$obj`     | object/string | object / class name |
|`$property`| string        | property name       |

`returns` (mixed) property value

Gets private or protected property value.

~~~php
$obj = new SomeClass();
$private_propery = ReflectionHelper::getPrivateProperty(
	$obj,
	'private_propery',
);
~~~

#### ReflectionHelper::setPrivateProperty($obj, $property, $value)

| param     | type          | description         |
|-----------|---------------|---------------------|
|`$obj`     | object/string | object / class name |
|`$property`| string        | property name       |
|`$value`   | mixed         | value               |

Sets private or protected property value.

~~~php
$obj = new SomeClass();
ReflectionHelper::setPrivateProperty(
	$obj,
	'private_propery',
	'new value'
);
~~~

#### ReflectionHelper::getPrivateMethodInvoker($obj, $method)

| param   | type          | description         |
|---------|---------------|---------------------|
|`$obj`   | object/string | object / class name |
|`$method`| string        | method name         |

`returns` (closure) method invoker

Gets private or protected method invoker.

~~~php
$obj = new SomeClass();
$method = ReflectionHelper::getPrivateMethodInvoker(
	$obj, 'privateMethod'
);
$this->assertEquals(
	'return value of the privateMethod() method', $method()
);
~~~

### *class* MonkeyPatch

To use this class, you have to enable monkey patching. See [How to Write Tests](HowToWriteTests.md#monkey-patching).

#### `MonkeyPatch::patchFunction($function, $return_value, $class_method)`

| param         | type   | description                                    |
|---------------|--------|------------------------------------------------|
|`$function`    | string | function name to patch                         |
|`$return_value`| mixed  | return value / callback                        |
|`$class_method`| string | class::method or classname to apply this patch |

Replaces function on the fly.

If `$class_method` is present, the patch is applied to the functions only in the class method or in the class.

There are some known limitations. See [How to Write Tests](HowToWriteTests.md#patching-functions) for details.

~~~php
MonkeyPatch::patchFunction('mt_rand', 100, 'Welcome::index');
~~~

#### `MonkeyPatch::resetFunctions()`

Resets all patched functions.

This method is called on `TestCase::tearDown()` by default. So you don't have to call it normally.

#### `MonkeyPatch::patchMethod($classname, $params)`

| param       | type   | description                   |
|-------------|--------|-------------------------------|
|`$classname` | string | class name to patch           |
|`$params`    | array  | [method_name => return_value] |

Replaces method in user-defined class on the fly.

~~~php
MonkeyPatch::patchMethod(
	'Category_model',
	['get_category_list' => [(object) ['name' => 'Nothing']]]
);
~~~

#### `MonkeyPatch::resetMethods()`

Resets all patched class methods.

This method is called on `TestCase::tearDown()` by default. So you don't have to call it normally.

#### `MonkeyPatch::patchConstant($constant, $value, $class_method)`

| param         | type   | description                                    |
|---------------|--------|------------------------------------------------|
|`$constant`    | string | constant name to patch                         |
|`$value`       | mixed  | value                                          |
|`$class_method`| string | class::method or classname to apply this patch |

Replaces constant value on the fly.

If `$class_method` is present, the patch is applied to the constants only in the class method or in the class.

There are some known limitations. See [How to Write Tests](HowToWriteTests.md#patching-constants) for details.

~~~php
MonkeyPatch::patchConstant('ENVIRONMENT', 'development', 'Welcome::index');
~~~

#### `MonkeyPatch::resetConstants()`

Resets all patched constants.

This method is called on `TestCase::tearDown()` by default. So you don't have to call it normally.

#### `MonkeyPatch::verifyInvoked($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies a patched class method or a patched function was invoked at least once.

~~~php
MonkeyPatch::verifyInvoked(
	'Ion_auth_model::login', ['foo', 'bar']
);
~~~

#### `MonkeyPatch::verifyInvokedOnce($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies that patched class method or a patched function was invoked only once.

~~~php
MonkeyPatch::verifyInvokedOnce(
	'CI_Input::post', ['id']
);
~~~

#### `MonkeyPatch::verifyInvokedMultipleTimes($class_method, $times, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$times`       | int    | times                    |
|`$params`      | array  | arguments                |

Verifies that patched method or a patched function was called exactly $times times.

~~~php
MonkeyPatch::verifyInvokedMultipleTimes(
	'CI_Input::post', 2
);
~~~

#### `MonkeyPatch::verifyNeverInvoked($class_method, $params)`

| param         | type   | description              |
|---------------|--------|--------------------------|
|`$class_method`| string | class::method / function |
|`$params`      | array  | arguments                |

Verifies that patched method or a patched function was not called.

~~~php
MonkeyPatch::verifyNeverInvoked(
	'Ion_auth_model::login', ['username', 'PHS/DL1m6OMYg']
);
~~~
