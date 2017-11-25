<?php

return [

	// Typical Database configuration
	'pdo/sqlite' => [
		'dsn' => 'sqlite:/'.realpath(__DIR__.'/../..').'/ci_test.sqlite',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => 'sqlite',
		'dbdriver' => 'pdo',
		'subdriver' => 'sqlite',
	],

	// Database configuration with failover
	'pdo/sqlite_failover' => [
		'dsn' => 'sqlite:not_exists.sqlite',
		'hostname' => 'localhost',
		'username' => 'sqlite',
		'password' => 'sqlite',
		'database' => 'sqlite',
		'dbdriver' => 'pdo',
		'subdriver' => 'sqlite',
		'failover' => [
			[
				'dsn' => 'sqlite:/'.realpath(__DIR__.'/../..').'/ci_test.sqlite',
				'hostname' => 'localhost',
				'username' => 'sqlite',
				'password' => 'sqlite',
				'database' => 'sqlite',
				'dbdriver' => 'pdo',
				'subdriver' => 'sqlite',
			],
		],
	],
];