Authentication
==============
Many requests that you make will require authentication of some type. Requests
includes support out of the box for HTTP Basic authentication, with more
built-ins coming soon.

Making a Basic authenticated call is ridiculously easy:

```php
$options = array(
	'auth' => new Requests_Auth_Basic(array('user', 'password'))
);
Requests::get('http://httpbin.org/basic-auth/user/password', array(), $options);
```

As Basic authentication is usually what you want when you specify a username
and password, you can also just pass in an array as a shorthand:

```php
$options = array(
	'auth' => array('user', 'password')
);
Requests::get('http://httpbin.org/basic-auth/user/password', array(), $options);
```

Note that POST/PUT can also take a data parameter, so you also need that
before `$options`:

```php
Requests::get('http://httpbin.org/basic-auth/user/password', array(), null, $options);
```