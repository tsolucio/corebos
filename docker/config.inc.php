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
ini_set('display_errors',1);
error_reporting(E_ALL);
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
$dbconfig['db_password'] = 'evolutivo';
$dbconfig['db_name'] = 'corebostest';
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

$site_URL = 'http://localhost';

// root directory path
$root_directory = '/var/www/html/';

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
$application_unique_key = '85e3dfa4b6c3115295733e5d73411059';

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

// Override with developer settings
if (file_exists('config-dev.inc.php')) {
	include 'config-dev.inc.php';
}
?>
