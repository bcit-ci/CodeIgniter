<?php

// First, include Requests
include('../library/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Now let's make a request via a proxy.
$options = array(
	'proxy' => '127.0.0.1:8080', // syntax: host:port, eg 12.13.14.14:8080 or someproxy.com:3128
	// If you need to authenticate, use the following syntax:
	// 'proxy' => array( '127.0.0.1:8080', 'username', 'password' ),
);
$request = Requests::get('http://httpbin.org/ip', array(), $options );

// See result
var_dump($request->body);
