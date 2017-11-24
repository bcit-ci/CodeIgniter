<?php

return [

	// Typical Database configuration
	'pdo/pgsql' => [
		'dsn' => 'pgsql:host=localhost;port=5432;dbname=ci_test;',
		'hostname' => 'localhost',
		'username' => 'postgres',
		'password' => '',
		'database' => 'ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'pgsql',
	],

	// Database configuration with failover
	'pdo/pgsql_failover' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'pgsql',
		'failover' => [
			[
				'dsn' => 'pgsql:host=localhost;port=5432;dbname=ci_test;',
				'hostname' => 'localhost',
				'username' => 'postgres',
				'password' => '',
				'database' => 'ci_test',
				'dbdriver' => 'pdo',
				'subdriver' => 'pgsql',
			],
		],
	],
];