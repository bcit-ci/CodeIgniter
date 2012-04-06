<?php
$dbdriver = is_php('5.4') ? 'sqlite3' : 'sqlite';

return array(

	// Typical Database configuration
	'sqlite' => array(
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => realpath(__DIR__.'/..').'/ci_test.sqlite',
		'dbdriver' => $dbdriver,
	),

	// Database configuration with failover
	'sqlite_failover' => array(
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => '../not_exists.sqlite',
		'dbdriver' => $dbdriver,
		'failover' => array(
			array(
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'sqlite',
				'password' => 'sqlite',
				'database' => realpath(__DIR__.'/..').'/ci_testf.sqlite',
				'dbdriver' => $dbdriver,
			),
		),
	),
);