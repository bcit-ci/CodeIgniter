<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Enable/Disable Migrations
|--------------------------------------------------------------------------
|
| Migrations are disabled by default for security reasons.
| You should enable migrations whenever you intend to do a schema migration
| and disable it back when you're done.
|
*/
$config['migration_enabled'] = FALSE;

/*
|--------------------------------------------------------------------------
| Migrations table
|--------------------------------------------------------------------------
|
| This is the name of the table that will store the current migrations state.
| When migrations runs it will store in a database table which migration 
| level the system is at. It then compares the migration level in the this
| table to the $config['migration_version'] if they are not the same it
| will migrate up. This must be set.
|
*/
$config['migration_table'] = 'migrations';

/*
|--------------------------------------------------------------------------
| Migrations version
|--------------------------------------------------------------------------
|
| This is used to set migration version that the file system should be on.
| If you run $this->migration->latest() this is the version that schema will
| be upgraded / downgraded to.
|
*/
$config['migration_version'] = 0;


/*
|--------------------------------------------------------------------------
| Migrations Path
|--------------------------------------------------------------------------
|
| Path to your migrations folder.
| Typically, it will be within your application path.
| Also, writing permission is required within the migrations path.
|
*/
$config['migration_path'] = APPPATH . 'migrations/';


/* End of file migration.php */
/* Location: ./application/config/migration.php */