<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

$config['migration_enabled'] = true;

/*
|--------------------------------------------------------------------------
| Migration Type
|--------------------------------------------------------------------------
|
| Migration file names may be based on a sequential identifier or on
| a timestamp. Options are:
|
|   'sequential' = Sequential migration naming (001_add_blog.php)
|   'timestamp'  = Timestamp migration naming (20121031104401_add_blog.php)
|                  Use timestamp format YYYYMMDDHHIISS.
|
| Note: If this configuration value is missing the Migration library
|       defaults to 'sequential' for backward compatibility with CI2.
|
*/
$config['migration_type'] = 'timestamp';

/*
|--------------------------------------------------------------------------
| Migrations table
|--------------------------------------------------------------------------
|
| This is the name of the table that will store the current migrations state.
| When migrations runs it will store in a database table which migration
| level the system is at. It then compares the migration level in this
| table to the $config['migration_version'] if they are not the same it
| will migrate up. This must be set.
|
*/
$config['migration_table'] = 'migrations';

/*
|--------------------------------------------------------------------------
| Auto Migrate To Latest
|--------------------------------------------------------------------------
|
| If this is set to TRUE when you load the migrations class and have
| $config['migration_enabled'] set to TRUE the system will auto migrate
| to your latest migration (whatever $config['migration_version'] is
| set to). This way you do not have to call migrations anywhere else
| in your code to have the latest migration.
|
*/
$config['migration_auto_latest'] = FALSE;

/*
|--------------------------------------------------------------------------
| Migrations version
|--------------------------------------------------------------------------
|
| This is used to set migration version that the file system should be on.
| If you run $this->migration->current() this is the version that schema will
| be upgraded / downgraded to.
|
*/

//$config['migration_version'] = '20170413203000'; // git tag Prod-2017-04-15
//$config['migration_version'] = '20170413203000'; // Value in backup 2017-04-19 20:00
//$config['migration_version'] = '20170417125200'; // git tag Prod-2017-04-20
//$config['migration_version'] = '20170421180800'; // git tag Prod-2017-04-23
//$config['migration_version'] = '20170426164000'; // git tag Prod-2017-04-27
//$config['migration_version'] = '20170505090800'; // git tag Prod-2017-05-06
//$config['migration_version'] = '20170505090800'; // git tag Prod-2017-05-14
//$config['migration_version'] = '20170519024000'; // git tag Prod-2017-05-22
//$config['migration_version'] = '20170519024000'; // Value in backup 2017-05-23
//$config['migration_version'] = '20170602210600'; // git tag Prod-2017-06-04
$config['migration_version'] = '20170612060000';   // git tag Prod-2017-06-13
$config['migration_version'] = '20170616165030';

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
$config['migration_path'] = APPPATH.'migrations/';
