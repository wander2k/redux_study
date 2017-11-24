<?php
define('APP_DIR', dirname(__FILE__).'/..');
define('NURSERY_DIR', APP_DIR.'/nursery');
define('LOG_DIR', APP_DIR.'/storage/logs');
/**
 * -- Assign and define constants --
 *
 * Constants used here are used globally primarily for
 * configuration aspects (rover host address, username/pass, client key, secret, etc)
 *
 *
 * -- These are obtained two ways --
 *
 * The primary way is to grab the constants directly from the environment
 * If the env var is not found, call back to the config.cfg file
 *
 *
 * Note: some commands have functionality to allow overriding the constants
 * with optional flags at the command line level for more granularity
 */
// config.cfg lists all the env vars that will be used
$config = parse_ini_file( APP_DIR.'/config/config.cfg');
foreach( $config as $confName => $confVal ){
	// obtain the env var or fall back to the value in the cfg file
	$valueToDefine = getenv(strtoupper($confName)) ?: $confVal;
	define( strtoupper($confName), $valueToDefine);
}