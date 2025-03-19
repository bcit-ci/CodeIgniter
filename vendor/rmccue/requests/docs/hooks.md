Hooks
=====
Requests has a hook system that you can use to manipulate parts of the request
process along with internal transport hooks.

Check out the [API documentation for `Requests_Hooks`][requests_hooks] for more
information on how to use the hook system.

Available Hooks
---------------

* `requests.before_request`

    Alter the request before it's sent to the transport.

    Parameters: `string &$url`, `array &$headers`, `array|string &$data`,
    `string &$type`, `array &$options`

* `requests.before_parse`

    Alter the raw HTTP response before parsing

    Parameters: `string &$response`

* `requests.after_request`

    Alter the response object before it's returned to the user

    Parameters: `Requests_Response &$return`

* `curl.before_request`

    Set cURL options before the transport sets any (note that Requests may
    override these)

    Parameters: `cURL resource &$fp`

* `curl.before_send`

    Set cURL options just before the request is actually sent via `curl_exec`

    Parameters: `cURL resource &$fp`

* `curl.after_request`

    Alter the raw HTTP response before returning for parsing

    Parameters: `string &$response`

* `fsockopen.before_request`

    Run events before the transport does anything

* `fsockopen.after_headers`

    Add extra headers before the body begins (i.e. before `\r\n\r\n`)

    Parameters: `string &$out`

* `fsockopen.before_send`

    Add body data before sending the request

    Parameters: `string &$out`

* `fsockopen.after_send`

   Run events after writing the data to the socket

* `fsockopen.after_request`

    Alter the raw HTTP response before returning for parsing

    Parameters: `string &$response`


Registering Hooks
-----------------
Note: if you're doing this in an authentication handler, see the [Custom
Authentication guide][authentication-custom] instead.

[authentication-custom]: authentication-custom.md

In order to register your own hooks, you need to instantiate `Requests_hooks`
and pass this in via the 'hooks' option.

```php
$hooks = new Requests_Hooks();
$hooks->register('requests.after_request', 'mycallback');

$request = Requests::get('http://httpbin.org/get', array(), array('hooks' => $hooks));
```