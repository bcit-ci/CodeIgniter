Usage
=====

Ready to go? Make sure you have Requests installed before attempting any of the
steps in this guide.


Loading Requests
----------------
Before we can load Requests up, we'll need to make sure it's loaded. This is a
simple two-step:

```php
// First, include Requests
include('/path/to/library/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();
```

If you'd like to bring along your own autoloader, you can forget about this
completely.


Make a GET Request
------------------
One of the most basic things you can do with HTTP is make a GET request.

Let's grab GitHub's public timeline:

```php
$response = Requests::get('https://github.com/timeline.json');
```

`$response` is now a **Requests_Response** object. Response objects are what
you'll be working with whenever you want to get data back from your request.


Using the Response Object
-------------------------
Now that we have the response from GitHub, let's get the body of the response.

```php
var_dump($response->body);
// string(42865) "[{"repository":{"url":"...
```


Custom Headers
--------------
If you want to add custom headers to the request, simply pass them in as an
associative array as the second parameter:

```php
$response = Requests::get('https://github.com/timeline.json', array('X-Requests' => 'Is Awesome!'));
```


Make a POST Request
-------------------
Making a POST request is very similar to making a GET:

```php
$response = Requests::post('http://httpbin.org/post');
```

You'll probably also want to pass in some data. You can pass in either a
string, an array or an object (Requests uses [`http_build_query`][build_query]
internally) as the third parameter (after the URL and headers):

[build_query]: http://php.net/http_build_query

```php
$data = array('key1' => 'value1', 'key2' => 'value2');
$response = Requests::post('http://httpbin.org/post', array(), $data);
var_dump($response->body);
```

This gives the output:

	string(503) "{
	  "origin": "124.191.162.147", 
	  "files": {}, 
	  "form": {
	    "key2": "value2", 
	    "key1": "value1"
	  }, 
	  "headers": {
	    "Content-Length": "23", 
	    "Accept-Encoding": "deflate;q=1.0, compress;q=0.5, gzip;q=0.5", 
	    "X-Forwarded-Port": "80", 
	    "Connection": "keep-alive", 
	    "User-Agent": "php-requests/1.6-dev", 
	    "Host": "httpbin.org", 
	    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
	  }, 
	  "url": "http://httpbin.org/post", 
	  "args": {}, 
	  "data": ""
	}"

To send raw data, simply pass in a string instead. You'll probably also want to
set the Content-Type header to ensure the remote server knows what you're
sending it:

```php
$url = 'https://api.github.com/some/endpoint';
$headers = array('Content-Type' => 'application/json');
$data = array('some' => 'data');
$response = Requests::post($url, $headers, json_encode($data));
```

Note that if you don't manually specify a Content-Type header, Requests has
undefined behaviour for the header. It may be set to various values depending
on the internal execution path, so it's recommended to set this explicitly if
you need to.


Status Codes
------------
The Response object also gives you access to the status code:

```php
var_dump($response->status_code);
// int(200)
```

You can also easily check if this status code is a success code, or if it's an
error:

```php
var_dump($response->success);
// bool(true)
```


Response Headers
----------------
We can also grab headers pretty easily:

```php
var_dump($response->headers['Date']);
// string(29) "Thu, 09 Feb 2012 15:22:06 GMT"
```

Note that this is case-insensitive, so the following are all equivalent:

* `$response->headers['Date']`
* `$response->headers['date']`
* `$response->headers['DATE']`
* `$response->headers['dAtE']`

If a header isn't set, this will give `null`. You can also check with
`isset($response->headers['date'])`
