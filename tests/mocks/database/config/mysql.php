<?php

return array(

	// Typical Database configuration
	'mysql' => array(
		'dsn' => '',
		'hostname' => '127.0.0.1',
		'username' => 'travis',
		'password' => 'travis',
		'database' => 'ci_test',
		'dbdriver' => 'mysql'
	),

	// Database configuration with failover
	'mysql_failover' => array(
		'dsn' => '',
		'hostname' => '127.0.0.1',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'mysql',
		'failover' => array(
			array(
				'dsn' => '',
				'hostname' => '127.0.0.1',
				'username' => 'travis',
				'password' => 'travis',
				'database' => 'ci_test',
				'dbdriver' => 'mysql',
			)
		)
	)
);