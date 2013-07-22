Add PDO to Codeigniter using all of the regular PHP PDO functionality => TRUE PDO

Files different from Ellis Labs Codeigniter
  READMEPDO.txt
  PDOschema.sql
  /application/config/config.php
  /application/config/database.php
  /application/libraries/Session.php
  /system/database/DB.php

For a complete step by step installation tutorial, visit 
http://christopherickes.com/web-app-development/pdo-for-codeigniter-2/ 

Replace your current system/database/DB.php file
Add application/libraries/Session.php file
Add application/config/database.php file
	Set the following values
		$active_record = FALSE;
		$PDO_conn = TRUE;
	Fill the remaining values based on your unique configuration.

If storing sessions in the database (recommended)
	Create a session table as instructed by Codeigniter
		CREATE TABLE IF NOT EXISTS  `ci_sessions` (
			session_id varchar(40) DEFAULT '0' NOT NULL,
			ip_address varchar(45) DEFAULT '0' NOT NULL,
			user_agent varchar(120) NOT NULL,
			last_activity int(10) unsigned DEFAULT 0 NOT NULL,
			user_data text NOT NULL,
			PRIMARY KEY (session_id),
			KEY `last_activity_idx` (`last_activity`)
		);
	Add application/config/config.php
		$config['sess_use_database']	= TRUE;				
		$config['sess_table_name']		= 'ci_sessions';	// or whatever you named your created table
		
		If using encryption (recommended)
			$config['sess_encrypt_cookie']	= TRUE;
			$config['encryption_key'] = '';					// 32 upper & lower case plus numbers
