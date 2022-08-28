<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

@include_once 'install/config.db.php';
global $dbconfig, $vtiger_current_version, $vtconfig, $coreBOS_app_version;

$hostname = $_SERVER['SERVER_NAME'];
$web_root = ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
$web_root .= $_SERVER['REQUEST_URI'];
$web_root = str_replace('/install.php', '', $web_root);
$web_root = $_SERVER['REQUEST_SCHEME'].'://'.$web_root;

$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname'].'/';
$cache_dir = 'cache/';

session_start();

$host_name = !isset($_REQUEST['hostName']) ? $dbconfig['db_server'].':'.$dbconfig['db_port'] : $_REQUEST['hostName'];
$demoData = !isset($_REQUEST['demoData']) ? $vtconfig['demoData'] : $_REQUEST['demoData'];
$currencyName = !isset($_REQUEST['currencyName']) ? preg_replace('/\s+$/', '', $vtconfig['currencyName']) : $_REQUEST['currencyName'];
$adminEmail = !isset($_REQUEST['adminEmail']) ? $vtconfig['adminEmail'] : $_REQUEST['adminEmail'];
$adminPwd = !isset($_REQUEST['adminPwd']) ? $vtconfig['adminPwd'] : $_REQUEST['adminPwd'];
$standarduserEmail = !isset($_REQUEST['standarduserEmail']) ? $vtconfig['standarduserEmail'] : $_REQUEST['standarduserEmail'];
$standarduserPwd = !isset($_REQUEST['standarduserPwd']) ? $vtconfig['standarduserPwd'] : $_REQUEST['standarduserPwd'];
$dbUsername = !isset($_REQUEST['dbUsername']) ? $dbconfig['db_username'] : $_REQUEST['dbUsername'];
$dbPassword = !isset($_REQUEST['dbPassword']) ? $dbconfig['db_password'] : $_REQUEST['dbPassword'];
$dbType = !isset($_REQUEST['dbType']) ? $dbconfig['db_type'] : $_REQUEST['dbType'];
$dbName = !isset($_REQUEST['dbName']) ? $dbconfig['db_name'] : $_REQUEST['dbName'];

session_start();
$create_db = (isset($_REQUEST['check_createdb']) && $_REQUEST['check_createdb'] == 'on');

$_SESSION['config_file_info']['db_hostname'] = $host_name;
$_SESSION['config_file_info']['db_username'] = $dbUsername;
$_SESSION['config_file_info']['db_password'] = $dbPassword;
$_SESSION['config_file_info']['db_name'] = $dbName;
$_SESSION['config_file_info']['db_type'] = $dbType;
$_SESSION['config_file_info']['site_URL']= $web_root;
$_SESSION['config_file_info']['root_directory'] = $current_dir;
$_SESSION['config_file_info']['currency_name'] = $currencyName;
$_SESSION['config_file_info']['admin_email'] = $adminEmail;

$_SESSION['installation_info']['currency_name'] = $currencyName;
$_SESSION['installation_info']['check_createdb'] = $create_db;
if (!isset($_REQUEST['root_user'])) {
	$_SESSION['installation_info']['root_user'] = $dbUsername;
} else {
	$_SESSION['installation_info']['root_user'] = $_REQUEST['root_user'];
}
if (!isset($_REQUEST['root_password'])) {
	$_SESSION['installation_info']['root_password'] = $dbPassword;
} else {
	$_SESSION['installation_info']['root_password'] = $_REQUEST['root_password'];
}
$_SESSION['installation_info']['admin_email']= $adminEmail;
$_SESSION['installation_info']['admin_password'] = $adminPwd;
$_SESSION['installation_info']['standarduser_email']= $standarduserEmail;
$_SESSION['installation_info']['standarduser_password'] = $standarduserPwd;

if (isset($_REQUEST['create_utf8_db'])) {
	$_SESSION['installation_info']['create_utf8_db'] = 'true';
} else {
	$_SESSION['installation_info']['create_utf8_db'] = 'false';
}
$_SESSION['config_file_info']['vt_charset']= 'UTF-8';

if (isset($_REQUEST['db_populate'])) {
	$_SESSION['installation_info']['db_populate'] = 'true';
} else {
	$_SESSION['installation_info']['db_populate'] = ($demoData == '1')? 'true': 'false';
}
require_once 'modules/Utilities/Currencies.php';
if (isset($currencyName)) {
	$_SESSION['installation_info']['currency_code'] = $currencies[$currencyName][0];
	$_SESSION['installation_info']['currency_symbol'] = $currencies[$currencyName][1];
}
require 'install/CreateTables.php';
?>