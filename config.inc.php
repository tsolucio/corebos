<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include 'vtigerversion.php';

// memory limit default value = 64M
ini_set('memory_limit', '1024M');
error_reporting(E_ERROR);
/* database configuration
 db_server
 db_port
 db_hostname
 db_username
 db_password
 db_name
*/
$dbconfig['db_server'] = 'localhost';
$dbconfig['db_port'] = ':3306';
$dbconfig['db_username'] = 'root';
$dbconfig['db_password'] = 'root';
$dbconfig['db_name'] = 'cbdevelop2';
$dbconfig['db_type'] = 'mysqli';
$dbconfig['db_status'] = 'true';
$dbconfig['persistent'] = false;
$dbconfig['db_hostname'] = $dbconfig['db_server'].$dbconfig['db_port'];
$host_name = $dbconfig['db_hostname'];

// log_sql default value = false
$dbconfig['log_sql'] = false;

// Should the caller information be captured in SQL Logging?
// Adds a little overhead for performance but will be useful for debugging
$SQL_LOG_INCLUDE_CALLER = false;

$site_URL = 'http://cbdevelop2.local';

// root directory path
$root_directory = '/home/guido/public_html/cbdevelop2/';

// cache direcory path
$cache_dir = 'cache/';

// tmp_dir default value prepended by cache_dir = images/
$tmp_dir = 'cache/images/';

// import_dir default value prepended by cache_dir = import/
$import_dir = 'cache/import/';

// upload_dir default value prepended by cache_dir = upload/
$upload_dir = 'cache/upload/';

// files with one of these extensions will have '.txt' appended to their filename on upload
// upload_badext default value = php, php3, php4, php5, pl, cgi, py, asp, cfm, js, vbs, html, htm
$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm', 'exe', 'bin', 'bat', 'sh', 'dll', 'phps', 'phtml', 'xhtml', 'rb', 'msi', 'jsp', 'shtml', 'sth', 'shtm');

// full path to include directory including the trailing slash
// includeDirectory default value = $root_directory..'include/
$includeDirectory = $root_directory.'include/';

// set default theme
// default_theme default value = blue
$default_theme = 'softed';

// default text that is placed initially in the login form for user name
// no default_user_name default value
$default_user_name = '';

// default text that is placed initially in the login form for password
// no default_password default value
$default_password = '';

// create user with default username and password
// create_default_user default value = false
$create_default_user = false;
// default_user_is_admin default value = false
$default_user_is_admin = false;

//Master currency name
$currency_name = 'Euro';

// default charset
// default charset default value = 'UTF-8' or 'ISO-8859-1'
$default_charset = 'UTF-8';
$default_charset = strtoupper($default_charset);  // DO NOT MODIFY THIS LINE, IT IS IMPORTANT

// default language
// default_language default value = en_us
$default_language = 'en_us';

// Generating Unique Application Key
$application_unique_key = '17565f92d80ec6d79c0d50bd4ce5b05f';

// Maximum time limit for PHP script execution (in seconds)
$php_max_execution_time = 0;

// Set the default timezone as per your preference
$default_timezone = 'UTC';

/** If timezone is configured, try to set it */
if (isset($default_timezone) && function_exists('date_default_timezone_set')) {
	@date_default_timezone_set($default_timezone);
}

// Enable log4php debugging only if requried
$LOG4PHP_DEBUG = true;

// Override database with enviroment variables
$dbconfig['db_server'] = getenv('COREBOS_DBSERVER') ? getenv('COREBOS_DBSERVER') : $dbconfig['db_server'];
$dbconfig['db_username'] = getenv('COREBOS_DBUSER') ? getenv('COREBOS_DBUSER') : $dbconfig['db_username'];
$dbconfig['db_password'] = getenv('COREBOS_DBPASS') ? getenv('COREBOS_DBPASS') : $dbconfig['db_password'];
$dbconfig['db_name'] = getenv('COREBOS_DBNAME') ? getenv('COREBOS_DBNAME') : $dbconfig['db_name'];
$site_URL = getenv('COREBOS_SITEURL') ? getenv('COREBOS_SITEURL') : $site_URL;

// Override with developer settings
if (file_exists('config-dev.inc.php')) {
	include 'config-dev.inc.php';
}
?>
