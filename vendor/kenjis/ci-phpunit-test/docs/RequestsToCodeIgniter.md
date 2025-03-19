# Requests to CodeIgniter

Writing tests for CodeIgniter 3.0 application has troublesome. Because CodeIgniter has some code which is untestable or difficult to test.

This is request list for CodeIgniter 3.x.

## Bootstrap file for PHPUnit

`core/CodeIgniter.php` calls a controller. We need bootstrap file which does not call controllers for testing.

## Functions which don't call `exit()`

* `redirect()`
* `show_error()`
* `show_404()`

`exit()` stops phpunit. We hope they throw exceptions.

## Output class has status code

If so, we can test the status code.

## Output class has cookie data

If so, we can test the cookie data.

## Input class has no static variables in methods

We can't reset the static variable below. It makes difficult to run another test using headers.

~~~php
    public function get_request_header($index, $xss_clean = FALSE)
    {
        static $headers;
        ...
~~~

## Way to reset CodeIgniter object

To test singleton, we need a method to reset it.
