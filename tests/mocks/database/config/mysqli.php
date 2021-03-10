<?php

return array(

	// Typical Database configuration
	'mysqli' => array(
		'dsn' => '',
		'hostname' => '127.0.0.1',
		'username' => 'travis',
		'password' => 'travis',
		'database' => 'ci_test',
		'dbdriver' => 'mysqli'
	),

	// Database configuration with failover
	'mysqli_failover' => array(
		'dsn' => '',
		'hostname' => '127.0.0.1',
		'username' => 'not_travis',
		'password' => 'wrong password',
		'database' => 'not_ci_test',
		'dbdriver' => 'mysqli',
		'failover' => array(
			array(
				'dsn' => '',
				'hostname' => '127.0.0.1',
				'username' => 'travis',
				'password' => 'travis',
				'database' => 'ci_test',
				'dbdriver' => 'mysqli',
			)
		)
	)
);