<?php

return array(

	// Typical Database configuration
	'sqlite' => array(
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => realpath(__DIR__.'/..').'/ci_test.sqlite',
		'dbdriver' => is_php('5.4') ? 'sqlite3' : 'sqlite',
	),

	// Database configuration with failover
	'sqlite_failover' => array(
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => '../not_exists.sqlite',
		'dbdriver' => is_php('5.4') ? 'sqlite3' : 'sqlite',
		'failover' => array(
			array(
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'sqlite',
				'password' => 'sqlite',
				'database' => realpath(__DIR__.'/..').'/ci_testf.sqlite',
				'dbdriver' => is_php('5.4') ? 'sqlite3' : 'sqlite',
			),
		),
	),
);