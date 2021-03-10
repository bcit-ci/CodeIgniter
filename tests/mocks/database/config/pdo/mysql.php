<?php

return array(

	// Typical Database configuration
	'pdo/mysql' => array(
		'dsn' => 'mysql:host=127.0.0.1;dbname=ci_test',
		'hostname' => '127.0.0.1',
		'username' => 'travis',
		'password' => 'travis',
		'database' => 'ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'mysql'
	),

	// Database configuration with failover
	'pdo/mysql_failover' => array(
		'dsn' => '',
		'hostname' => '127.0.0.1',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'pdo',
		'subdriver' => 'mysql',
		'failover' => array(
			array(
				'dsn' => 'mysql:host=127.0.0.1;dbname=ci_test',
				'hostname' => '127.0.0.1',
				'username' => 'travis',
				'password' => 'travis',
				'database' => 'ci_test',
				'dbdriver' => 'pdo',
				'subdriver' => 'mysql'
			)
		)
	)
);