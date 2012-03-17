<?php
$dir = realpath(dirname(__FILE__));

// bootstrap CodeIgniter
define('ENVIRONMENT', 'testing');
require_once $dir . '/../../index.php';

// load controller test case class
require_once $dir . '/lib/ci_controller_testcase.php';
