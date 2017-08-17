<?php

return [

	// Typical Database configuration
	'sqlite' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => realpath(__DIR__.'/..').'/ci_test.sqlite',
		'dbdriver' => 'sqlite3'
	],

	// Database configuration with failover
	'sqlite_failover' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => '../not_exists.sqlite',
		'dbdriver' => 'sqlite3',
		'failover' => [
			[
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'sqlite',
				'password' => 'sqlite',
				'database' => realpath(__DIR__.'/..').'/ci_test.sqlite',
				'dbdriver' => 'sqlite3'
			]
		]
	]
];