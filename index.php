<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
global $entityDel, $display, $category;

if(version_compare(phpversion(), '5.2.0') < 0 or version_compare(phpversion(), '7.1.0') >= 0) {
	insert_charset_header();
	$serverPhpVersion = phpversion();
	require_once('phpversionfail.php');
	die();
}

require_once('include/utils/utils.php');

global $currentModule;

/** Function to  return a string with backslashes stripped off
 * @param $value -- value:: Type string
 * @returns $value -- value:: Type string array
*/
function stripslashes_checkstrings($value){
	if(is_string($value)){
		return stripslashes($value);
	}
	return $value;
}

if(get_magic_quotes_gpc() == 1){
	$_REQUEST = array_map('stripslashes_checkstrings', $_REQUEST);
	$_POST = array_map('stripslashes_checkstrings', $_POST);
	$_GET = array_map('stripslashes_checkstrings', $_GET);
}

// Allow for the session information to be passed via the URL for printing.
if(isset($_REQUEST['PHPSESSID']))
{
	session_id($_REQUEST['PHPSESSID']);
	//Setting the same session id to Forums as in CRM
	$sid=$_REQUEST['PHPSESSID'];
}

if(isset($_REQUEST['view'])) {
	//setcookie("view",$_REQUEST['view']);
	$view = $_REQUEST["view"];
	coreBOS_Session::set('view', $view);
}

/** Function to set, character set in the header, as given in include/language/*_lang.php */
function insert_charset_header()
{
	global $app_strings, $default_charset;
	$charset = $default_charset;
	if(isset($app_strings['LBL_CHARSET'])) {
		$charset = $app_strings['LBL_CHARSET'];
	}
	header('Content-Type: text/html; charset='. $charset);
}

insert_charset_header();
// Create or reestablish the current session
coreBOS_Session::init(true);

if (!is_file('config.inc.php')) {
	header("Location: install.php");
	exit();
}

require_once('config.inc.php');
if (!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
	header("Location: install.php");
	exit();
}

// load up the config_override.php file.  This is used to provide default user settings
if (is_file('config_override.php')) {
	require_once('config_override.php');
}

/**
 * Check for vtiger installed version and codebase
 */
global $adb, $vtiger_current_version;
if(isset($_SESSION['VTIGER_DB_VERSION']) && isset($_SESSION['authenticated_user_id'])) {
	if(version_compare($_SESSION['VTIGER_DB_VERSION'], $vtiger_current_version, '!=')) {
		coreBOS_Session::delete('VTIGER_DB_VERSION');
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tbody><tr>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>Migration Incompleted.</span></td>
			</tr>
			<tr>
			<td class='small' align='right' nowrap='nowrap'>Please contact your system administrator.<br></td>
			</tr>
			</tbody></table>
			</div>";
		echo "</td></tr></table>";
		exit();
	}
} else {
	$result = $adb->query("SELECT * FROM vtiger_version");
	$dbversion = $adb->query_result($result, 0, 'current_version');
	if(version_compare($dbversion, $vtiger_current_version, '=')) {
		coreBOS_Session::set('VTIGER_DB_VERSION', $dbversion);
	} else {
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tbody><tr>
			<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>Migration Incompleted.</span></td>
			</tr>
			<tr>
			<td class='small' align='right' nowrap='nowrap'>Please contact your system administrator.<br></td>
			</tr>
			</tbody></table>
			</div>";
		echo "</td></tr></table>";
		exit();
	}
}

// Set the default timezone preferred by user
global $default_timezone;
if(isset($default_timezone) && function_exists('date_default_timezone_set')) {
	@date_default_timezone_set($default_timezone);
}

require_once('include/logging.php');
require_once('modules/Users/Users.php');
$calculate_response_time = GlobalVariable::getVariable('Debug_Calculate_Response_Time',0);
if($calculate_response_time) $startTime = microtime(true);

$log = LoggerManager::getLogger('index');

global $seclog;
$seclog = LoggerManager::getLogger('SECURITY');

if (isset($_REQUEST['PHPSESSID'])) $log->debug("****Starting for session ".$_REQUEST['PHPSESSID']);
else $log->debug("****Starting for new session");

// We use the REQUEST_URI later to construct dynamic URLs.  IIS does not pass this field
// to prevent an error, if it is not set, we will assign it to ''
if(!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = '';
}

$action = '';
if(isset($_REQUEST['action']))
{
	$action = vtlib_purify($_REQUEST['action']);
}
if($action == 'Export')
{
	include ('include/utils/export.php');
}
if($action == 'ExportAjax')
{
	include ('include/utils/ExportAjax.php');
}
// vtlib customization: Module manager export
if($action == 'ModuleManagerExport') {
	include('modules/Settings/ModuleManager/Export.php');
}
// END

//Code added for 'Path Traversal/File Disclosure' security fix - Philip
$is_module = false;
$is_action = false;
if(isset($_REQUEST['module']))
{
	$module = vtlib_purify($_REQUEST['module']);
	$dir = @scandir($root_directory."modules");
	$temp_arr = Array("CVS","Attic");
	$res_arr = @array_intersect($dir,$temp_arr);
	if(count($res_arr) == 0 && !preg_match("/[\/.]/",$module)) {
		if(@in_array($module,$dir))
			$is_module = true;
	}
	$in_dir = @scandir($root_directory."modules/".$module);
	$res_arr = @array_intersect($in_dir,$temp_arr);
	if(count($res_arr) == 0 && !preg_match("/[\/.]/",$module)) {
		if(@in_array($action.".php",$in_dir))
			$is_action = true;
	}
	if (empty($action)) $is_action = false;
	if(!$is_module)
	{
		die("Module name is missing or incorrect. Please check the module name.");
	}
	if(!$is_action)
	{
		die("Action name is missing or incorrect. Please check the action name.");
	}
}

//Code added for 'Multiple SQL Injection Vulnerabilities & XSS issue' fixes - Philip
if(isset($_REQUEST['record']) && !is_numeric($_REQUEST['record']) && $_REQUEST['record']!='')
{
	die("An invalid record number specified to view details.");
}

// Check to see if there is an authenticated user in the session.
$use_current_login = false;
if(isset($_SESSION["authenticated_user_id"]) && (isset($_SESSION["app_unique_key"]) && $_SESSION["app_unique_key"] == $application_unique_key))
{
	$use_current_login = true;
}

// Prevent loading Login again if there is an authenticated user in the session.
if (isset($_SESSION["authenticated_user_id"]) && isset($module) && $module == 'Users' && $action == 'Login') {
	$default_action = GlobalVariable::getVariable('Application_Default_Action','index','',$_SESSION["authenticated_user_id"]);
	$default_module = GlobalVariable::getVariable('Application_Default_Module','Home','',$_SESSION["authenticated_user_id"]);
	header("Location: index.php?action=$default_action&module=$default_module");
}

if($use_current_login){
	//getting the internal_mailer flag
	if(!isset($_SESSION['internal_mailer'])){
		$qry_res = $adb->pquery("select internal_mailer from vtiger_users where id=?", array($_SESSION["authenticated_user_id"]));
		coreBOS_Session::set('internal_mailer', $adb->query_result($qry_res,0,'internal_mailer'));
	}
	$log->debug("We have an authenticated user id: ".$_SESSION["authenticated_user_id"]);
}else if(isset($action) && isset($module) && $action=="Authenticate" && $module=="Users"){
	$log->debug("We are authenticating user now");
}else{
	if(!isset($_REQUEST['action']) || ($_REQUEST['action'] != 'Logout' && $_REQUEST['action'] != 'Login')){
		coreBOS_Session::set('lastpage', $_SERVER['QUERY_STRING']);
	}
	$log->debug('The current user does not have a session. Going to the login page');
	$action = 'Login';
	$module = 'Users';
	include 'modules/Users/Login.php';
	exit;
}

$log->debug($_REQUEST);
$skipHeaders=false;
$skipFooters=false;
$viewAttachment = false;
$skipSecurityCheck= false;
//echo $module;
// echo $action;

if(isset($action) && isset($module))
{
	$log->info("About to take action ".$action);
	if(preg_match("/^Save/", $action) ||
		preg_match("/^Delete/", $action) ||
		preg_match("/^Choose/", $action) ||
		preg_match("/^Popup/", $action) ||
		preg_match("/^ChangePassword/", $action) ||
		preg_match("/^Authenticate/", $action) ||
		preg_match("/^Logout/", $action) ||
		preg_match("/^add2db/", $action) ||
		preg_match("/^result/", $action) ||
		preg_match("/^LeadConvertToEntities/", $action) ||
		preg_match("/^downloadfile/", $action) ||
		preg_match("/^massdelete/", $action) ||
		preg_match("/^updateLeadDBStatus/",$action) ||
		preg_match("/^AddCustomFieldToDB/", $action) ||
		preg_match("/^updateRole/",$action) ||
		preg_match("/^UserInfoUtil/",$action) ||
		preg_match("/^deleteRole/",$action) ||
		preg_match("/^UpdateComboValues/",$action) ||
		preg_match("/^fieldtypes/",$action) ||
		preg_match("/^app_ins/",$action) ||
		preg_match("/^minical/",$action) ||
		preg_match("/^minitimer/",$action) ||
		preg_match("/^app_del/",$action) ||
		preg_match("/^send_mail/",$action) ||
		preg_match("/^populatetemplate/",$action) ||
		preg_match("/^TemplateMerge/",$action) ||
		preg_match("/^testemailtemplateusage/",$action) ||
		preg_match("/^saveemailtemplate/",$action) ||
		preg_match("/^ProcessDuplicates/", $action ) ||
		preg_match("/^lastImport/", $action ) ||
		preg_match("/^lookupemailtemplate/",$action) ||
		preg_match("/^deletewordtemplate/",$action) ||
		preg_match("/^deleteemailtemplate/",$action) ||
		preg_match("/^CurrencyDelete/",$action) ||
		preg_match("/^deleteattachments/",$action) ||
		preg_match("/^MassDeleteUsers/",$action) ||
		preg_match("/^UpdateFieldLevelAccess/",$action) ||
		preg_match("/^UpdateDefaultFieldLevelAccess/",$action) ||
		preg_match("/^UpdateProfile/",$action) ||
		preg_match("/^updateRelations/",$action) ||
		preg_match("/^updateNotificationSchedulers/",$action) ||
		preg_match("/^Star/",$action) ||
		preg_match("/^addPbProductRelToDB/",$action) ||
		preg_match("/^UpdateListPrice/",$action) ||
		preg_match("/^PriceListPopup/",$action) ||
		preg_match("/^SalesOrderPopup/",$action) ||
		preg_match("/^CreatePDF/",$action) ||
		preg_match("/^CreateSOPDF/",$action) ||
		preg_match("/^redirect/",$action) ||
		preg_match("/^webmail/",$action) ||
		preg_match("/^left_main/",$action) ||
		preg_match("/^delete_message/",$action) ||
		preg_match("/^mime/",$action) ||
		preg_match("/^move_messages/",$action) ||
		preg_match("/^folders_create/",$action) ||
		preg_match("/^imap_general/",$action) ||
		preg_match("/^mime/",$action) ||
		preg_match("/^download/",$action) ||
		preg_match("/^SendMailAction/",$action) ||
		preg_match("/^CreateXL/",$action) ||
		preg_match("/^savetermsandconditions/",$action) ||
		preg_match("/^home_rss/",$action) ||
		preg_match("/^ConvertAsFAQ/",$action) ||
		preg_match("/^".$module."Ajax/",$action) ||
		preg_match("/^ActivityAjax/",$action) ||
		preg_match("/^updateCalendarSharing/",$action) ||
		preg_match("/^disable_sharing/",$action) ||
		preg_match("/^TodoSave/",$action) ||
		preg_match("/^RecalculateSharingRules/",$action) ||
		(preg_match("/^body/",$action) && preg_match("/^Webmails/",$module)) ||
		(preg_match("/^dlAttachments/",$action) && preg_match("/^Webmails/",$module)) ||
		(preg_match("/^DetailView/",$action) && preg_match("/^Webmails/",$module)) ||
		preg_match("/^savewordtemplate/",$action) ||
		preg_match("/^mailmergedownloadfile/",$action) ||
		(preg_match("/^Webmails/",$module) && preg_match("/^get_img/",$action)) ||
		preg_match("/^download/",$action) ||
		preg_match("/^getListOfRecords/", $action) ||
		preg_match("/^AddBlockFieldToDB/", $action) ||
		preg_match("/^AddBlockToDB/", $action) ||
		preg_match("/^MassEditSave/", $action) ||
		preg_match("/^iCalExport/",$action)
		)
	{
		$skipHeaders=true;
		//skip headers for all these invocations as they are mostly popups
		if(preg_match("/^Popup/", $action) ||
			preg_match("/^ChangePassword/", $action) ||
			//preg_match("/^Export/", $action) ||
			preg_match("/^downloadfile/", $action) ||
			preg_match("/^fieldtypes/",$action) ||
			preg_match("/^lookupemailtemplate/",$action) ||
			preg_match("/^home_rss/",$action) ||
			preg_match("/^".$module."Ajax/",$action) ||
			preg_match("/^massdelete/", $action) ||
			preg_match("/^mailmergedownloadfile/",$action) ||
			preg_match("/^get_img/",$action) ||
			preg_match("/^download/",$action) ||
			preg_match("/^ProcessDuplicates/", $action ) ||
			preg_match("/^lastImport/", $action ) ||
			preg_match("/^massdelete/", $action ) ||
			preg_match("/^getListOfRecords/", $action) ||
			preg_match("/^MassEditSave/", $action) ||
			preg_match("/^iCalExport/",$action)
			)
			$skipFooters=true;
		//skip footers for all these invocations as they are mostly popups
		if(preg_match("/^downloadfile/", $action)
		|| preg_match("/^fieldtypes/",$action)
		|| preg_match("/^mailmergedownloadfile/",$action)
		|| preg_match("/^get_img/",$action)
		|| preg_match("/^MergeFieldLeads/", $action)
		|| preg_match("/^MergeFieldContacts/", $action )
		|| preg_match("/^MergeFieldAccounts/", $action )
		|| preg_match("/^MergeFieldProducts/", $action )
		|| preg_match("/^MergeFieldHelpDesk/", $action )
		|| preg_match("/^MergeFieldPotentials/", $action )
		|| preg_match("/^MergeFieldVendors/", $action )
		|| preg_match("/^dlAttachments/", $action )
		|| preg_match("/^iCalExport/", $action)
		)
		{
			$viewAttachment = true;
		}
		if(($action == ' Delete ') && (!$entityDel))
		{
			$skipHeaders=false;
		}
	}

	if($action == 'Save')
	{
		header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
		header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header( "Cache-Control: no-cache, must-revalidate" );
		header( "Pragma: no-cache" );
	}

	if(($module == 'Users' || $module == 'Home' || $module == 'uploads') && (empty($_REQUEST['parenttab']) || $_REQUEST['parenttab'] != 'Settings')) {
		$skipSecurityCheck=true;
	}

	if($action == 'UnifiedSearch') {
		$currentModuleFile = 'modules/Home/'.$action.'.php';
	} else {
		$currentModuleFile = 'modules/'.$module.'/'.$action.'.php';
	}
	$currentModule = $module;
} else {
	// use $default_module and $default_action as set in config.php
	// Redirect to the correct module with the correct action.  We need the URI to include these fields.
	if(isset($_SESSION["authenticated_user_id"])){
		$userid = $_SESSION["authenticated_user_id"];
	}else{
		$userid = 1;
	}
	$default_action = GlobalVariable::getVariable('Application_Default_Action','index','',$userid);
	$default_module = GlobalVariable::getVariable('Application_Default_Module','Home','',$userid);
	header("Location: index.php?action=$default_action&module=$default_module");
	exit();
}

$log->info("current page is $currentModuleFile current module is $currentModule ");

$module = (isset($_REQUEST['module'])) ? vtlib_purify($_REQUEST['module']) : "";
$action = (isset($_REQUEST['action'])) ? vtlib_purify($_REQUEST['action']) : "";
$record = (isset($_REQUEST['record'])) ? vtlib_purify($_REQUEST['record']) : "";

$current_user = new Users();

if($use_current_login)
{
	//$result = $current_user->retrieve($_SESSION['authenticated_user_id']);
	//getting the current user info from flat file
	$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);

	if($result == null)
	{
		coreBOS_Session::destroy();
		header("Location: index.php?action=Login&module=Users");
	}
	coreBOS_Session::setUserGlobalSessionVariables();
	$moduleList = getPermittedModuleNames();

	//auditing
	require_once('user_privileges/audit_trail.php');
	/* Skip audit trail log for special request types */
	$skip_auditing = false;
	if(($action == 'ActivityReminderCallbackAjax' || (isset($_REQUEST['file']) && $_REQUEST['file'] == 'ActivityReminderCallbackAjax')) && $module == 'Calendar') {
		$skip_auditing = true;
	} else if(($action == 'TraceIncomingCall' || (isset($_REQUEST['file']) && $_REQUEST['file'] == 'TraceIncomingCall')) && $module == 'PBXManager') {
		$skip_auditing = true;
	}
	/* END */
	if($audit_trail == 'true' and !$skip_auditing) {
		$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$query = "insert into vtiger_audit_trial values(?,?,?,?,?,?)";
		$qparams = array($adb->getUniqueID('vtiger_audit_trial'), $current_user->id, $module, $action, $record, $date_var);
		$adb->pquery($query, $qparams);
	}
	if(!$skip_auditing) {
		cbEventHandler::do_action('corebos.audit.action',array($current_user->id, $module, $action, $record, date('Y-m-d H:i:s')));
	}
	$log->debug('Current user is: '.$current_user->user_name);
}
// Force password change
if($current_user->mustChangePassword() and $_REQUEST['action']!='Logout' and $_REQUEST['action']!='CalendarAjax' and $_REQUEST['action']!='UsersAjax' and $_REQUEST['action']!='ChangePassword' and !($_REQUEST['module']=='Users' and $_REQUEST['action']=='Save')) {
	$currentModule = 'Users';
	$currentModuleFile = 'modules/Users/DetailView.php';
	$_REQUEST['action'] = $action = 'DeatilView';
	$_REQUEST['module'] = $module = 'Users';
	$_REQUEST['record'] = $current_user->id;
}

if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '')
{
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
}
else
{
	if(!empty($current_user->theme)) {
		$theme = $current_user->theme;
	} else {
		$theme = $default_theme;
	}
}
$log->debug('Current theme is: '.$theme);

//Used for current record focus
$focus = "";

// if the language is not set yet, then set it to the default language.
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '')
{
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if(!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}
$log->debug('current_language is: '.$current_language);

//set module and application string arrays based upon selected language
$app_currency_strings = return_app_currency_strings_language($current_language);
$app_strings = return_application_language($current_language);
$app_list_strings = return_app_list_strings_language($current_language);
$mod_strings = return_module_language($current_language, $currentModule);

//If DetailView, set focus to record passed in
if($action == "DetailView")
{
	if(empty($_REQUEST['record']))
		die('A record number must be specified to view details.');

	// If we are going to a detail form, load up the record now and use the record to track the viewing.
	switch($currentModule) {
		case 'Webmails':
			//No need to create a webmail object here
			break;
		default:
			$focus = CRMEntity::getInstance($currentModule);
			break;
	}

	if(isset($_REQUEST['record']) && $_REQUEST['record']!='' && $_REQUEST["module"] != "Webmails" && $current_user->id != '') {
		// Only track a viewing if the record was retrieved.
		$focus->track_view($current_user->id, $currentModule,$_REQUEST['record']);
	}
}

// set user, theme and language cookies so that login screen defaults to last values
$siteURLParts = parse_url($site_URL); $cookieDomain = $siteURLParts['host'];
if (isset($_SESSION['authenticated_user_id'])) {
	$log->debug("setting cookie ck_login_id_vtiger to ".$_SESSION['authenticated_user_id']);
	setcookie('ck_login_id_vtiger', $_SESSION['authenticated_user_id'],0,null,$cookieDomain,false,true);
}

if($_REQUEST['module'] == 'Documents' && $action == 'DownloadFile')
{
	checkFileAccess('modules/Documents/DownloadFile.php');
	include('modules/Documents/DownloadFile.php');
	exit;
}

//skip headers for popups, deleting, saving, importing and other actions
if(!$skipHeaders) {
	$log->debug("including headers");
	if($use_current_login)
	{
		if(isset($_REQUEST['category']) && $_REQUEST['category'] !='')
		{
			$category = vtlib_purify($_REQUEST['category']);
		}
		else
		{
			$category = getParentTabFromModule($currentModule);
		}
		include('modules/Vtiger/header.php');
	}
} else {
	/*if(($action != 'mytkt_rss') && ($action != 'home_rss') && ($action != $module."Ajax") && ($action != "body") && ($action != 'ActivityAjax')) {
		require_once('Smarty_setup.php');
		$vartpl = new vtigerCRM_Smarty;
		getBrowserVariables($vartpl);
		$vartpl->display('BrowserVariables.tpl');
	}*/
	$log->debug("skipping headers");
}

//fetch the permission set from session and search it for the requisite data
if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '')
{
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
} else {
	if(!empty($current_user->theme)) {
		$theme = $current_user->theme;
	} else {
		$theme = $default_theme;
	}
}

//logging the security Information
$seclog->debug('########  Module -->  '.$module.'  :: Action --> '.$action.' ::  UserID --> '.$current_user->id.' :: RecordID --> '.$record.' #######');

if(!$skipSecurityCheck && $use_current_login)
{
	require_once('include/utils/UserInfoUtil.php');
	if(preg_match('/Ajax/',$action)) {
		if(isset($_REQUEST['ajxaction']) and $_REQUEST['ajxaction'] == 'LOADRELATEDLIST'){
			$now_action = 'DetailView';
		} else {
			$now_action=vtlib_purify($_REQUEST['file']);
		}
	} else {
		$now_action=$action;
	}

	if(isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
		$display = isPermitted($module,$now_action,$_REQUEST['record']);
	} else {
		if ($now_action=='EditView' or $now_action=='EventEditView' or $now_action=='Save') $now_action = 'CreateView';
		$display = isPermitted($module,$now_action);
	}
	$seclog->debug('########### Pemitted ---> '.$display.'  ##############');
} else {
	$seclog->debug('########### Pemitted ---> yes  ##############');
}

if($display == "no"
		and !(($currentModule=='Tooltip' and $action==$module."Ajax" and $_REQUEST['file']=='ComputeTooltip')
			or ($currentModule=='GlobalVariable' and $action==$module."Ajax" and $_REQUEST['file']=='SearchGlobalVar'))
	) {
	echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";
	if ($action==$module."Ajax") {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' width='70%'><span class='genHeaderSmall'>".$app_strings['LBL_PERMISSION']."</span></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
	} else {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>".$app_strings['LBL_PERMISSION']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
	}
}
// vtlib customization: Check if module has been de-activated
else if(!vtlib_isModuleActive($currentModule)
		and !(($currentModule=='Tooltip' and $action==$module."Ajax" and $_REQUEST['file']=='ComputeTooltip')
			or ($currentModule=='GlobalVariable' and $action==$module."Ajax" and $_REQUEST['file']=='SearchGlobalVar'))
	) {
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 85%; position: relative; z-index: 10;'>
		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) . "' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' width='70%'><span class='genHeaderSmall'>$currentModule ".$app_strings['VTLIB_MOD_NOT_ACTIVE']."</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
		</tr>
		</tbody></table>
		</div>
		</td></tr></table>";
}
// END
else
{
	include($currentModuleFile);
}

//added to get the theme . This is a bad fix as we need to know where the problem lies yet
if(isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '') {
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
} else {
	$theme = $default_theme;
}
$Ajx_module= $module;
if($module == 'Events')
	$Ajx_module = 'Calendar';
if((!$viewAttachment) && (!$viewAttachment && $action != 'home_rss') && $action != $Ajx_module."Ajax" && $action != 'massdelete' && $action != "DashboardAjax" && $action != "ActivityAjax")
{
	// Under the SPL you do not have the right to remove this copyright statement.
	$copyrightstatement="<style>
		.bggray
		{
			background-color: #dfdfdf;
		}
	.bgwhite
	{
		background-color: #FFFFFF;
	}
	.copy
	{
		font-size:9px;
		font-family: Verdana, Arial, Helvetica, Sans-serif;
	}
	</style>";

	if((!$skipFooters) && $action != "ChangePassword" && $action != "body" && $action != $module."Ajax" && $action!='Popup' && $action != 'ImportStep3' && $action != 'ActivityAjax' && $action != 'getListOfRecords') {
		echo $copyrightstatement;
		cbEventHandler::do_action('corebos.footer.prefooter');
		$coreBOS_uiapp_name = GlobalVariable::getVariable('Application_UI_Name',$coreBOS_app_name);
		$coreBOS_uiapp_version = GlobalVariable::getVariable('Application_UI_Version',$coreBOS_app_version);
		$coreBOS_uiapp_url = GlobalVariable::getVariable('Application_UI_URL',$coreBOS_app_url);
		echo "<br><br><br><table border=0 cellspacing=0 cellpadding=5 width=100% class=settingsSelectedUI >";
		echo "<tr><td class=small align=left><span style='color: rgb(153, 153, 153);'>Powered by ".$coreBOS_uiapp_name." <span id='_vtiger_product_version_'>$coreBOS_uiapp_version</span></span></td>";
		echo "<td class=small align=right><span>&copy; 2004-".date('Y')." <a href='$coreBOS_uiapp_url' target='_blank'>$coreBOS_uiapp_name</a></span></td></tr></table>";
		if($calculate_response_time) {
			$endTime = microtime(true);
			echo "<table align='center'><tr><td align='center'>";
			$deltaTime = round($endTime - $startTime,2);
			echo('&nbsp;Server response time: '.$deltaTime.' seconds.');
			echo "</td></tr></table>\n";
		}
	}
	// ActivityReminder Customization for callback
	if(!$skipFooters) {
		if($current_user->id!=NULL && isPermitted('Calendar','index') == 'yes' && vtlib_isModuleActive('Calendar')) {
			echo "<script type='text/javascript'>if(typeof(ActivityReminderCallback) != 'undefined') ";
			$cur_time = time();
			$last_reminder_check_time = (isset($_SESSION['last_reminder_check_time']) ? $_SESSION['last_reminder_check_time'] : 0);
			$next_reminder_interval = (isset($_SESSION['next_reminder_interval']) ? $_SESSION['next_reminder_interval'] : 0);
			$reminder_interval_reset = ($last_reminder_check_time + $next_reminder_interval - $cur_time) * 1000;
			if(isset($_SESSION['last_reminder_check_time']) && $reminder_interval_reset > 0){
				echo "window.setTimeout(function(){
						ActivityReminderCallback();
					},$reminder_interval_reset);";
			} else {
				echo 'ActivityReminderCallback();';
			}
			echo '</script>';
		}
	}

	if((!$skipFooters) && ($action != "body") && ($action != $module."Ajax") && ($action != "ActivityAjax"))
		include('modules/Vtiger/footer.php');
}
?>
