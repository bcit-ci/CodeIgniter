<?php

// First, include Requests
include('../library/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

// Set up our session
$session = new Requests_Session('http://httpbin.org/');
$session->headers['Accept'] = 'application/json';
$session->useragent = 'Awesomesauce';

// Now let's make a request!
$request = $session->get('/get');

// Check what we received
var_dump($request);

// Let's check our user agent!
$request = $session->get('/user-agent');

// And check again
var_dump($request);
