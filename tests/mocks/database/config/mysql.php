<?php

return [

	// Typical Database configuration
	'mysql' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'travis',
		'password' => '',
		'database' => 'ci_test',
		'dbdriver' => 'mysql',
	],

	// Database configuration with failover
	'mysql_failover' => [
		'dsn' => '',
		'hostname' => 'localhost',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'mysql',
		'failover' => [
			[
				'dsn' => '',
				'hostname' => 'localhost',
				'username' => 'travis',
				'password' => '',
				'database' => 'ci_test',
				'dbdriver' => 'mysql',
			],
		],
	],
];