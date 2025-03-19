Proxy Support
=============

You can easily make requests through HTTP proxies.

To make requests through an open proxy, specify the following options:

```php
$options = array(
	'proxy' => '127.0.0.1:3128'
);
Requests::get('http://httpbin.org/ip', array(), $options);
```

If your proxy needs you to authenticate, the option will become an array like
the following:

```php
$options = array(
	'proxy' => array( '127.0.0.1:3128', 'my_username', 'my_password' )
);
Requests::get('http://httpbin.org/ip', array(), $options);
```
