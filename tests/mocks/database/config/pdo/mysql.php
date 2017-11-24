<?php

return [

	// Typical Database configuration
	'pdo/mysql' => [
		'dsn' => 'mysql:host=localhost;dbname=ci_test',
		'hostname' => 'localhost',
		'username' => 'travis',
		'password' => '',
		'database' => 'ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'mysql'
	],

	// Database configuration with failover
	'pdo/mysql_failover' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'mysql',
		'failover' => [
			[
				'dsn' => 'mysql:host=localhost;dbname=ci_test',
				'hostname' => 'localhost',
				'username' => 'travis',
				'password' => '',
				'database' => 'ci_test',
				'dbdriver' => 'pdo',
				'subdriver' => 'mysql'
			]
		]
	]
];