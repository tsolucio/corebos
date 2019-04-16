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

if (version_compare(phpversion(), '5.4.0') < 0 || version_compare(phpversion(), '7.4.0') >= 0) {
	header('Content-Type: text/html; charset=UTF-8');
	$serverPhpVersion = phpversion();
	require_once 'phpversionfail.php';
	die();
}

require_once 'include/utils/utils.php';

global $currentModule;

header('Content-Type: text/html; charset='. $default_charset);

// Create or reestablish the current session
coreBOS_Session::init(true, true);

if (isset($_REQUEST['view'])) {
	$view = $_REQUEST['view'];
	coreBOS_Session::set('view', $view);
}

if (!is_file('config.inc.php')) {
	header('Location: install.php');
	exit();
}

require_once 'config.inc.php';
if (!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
	header('Location: install.php');
	exit();
}

require_once 'include/logging.php';
require_once 'modules/Users/Users.php';
$calculate_response_time = GlobalVariable::getVariable('Debug_Calculate_Response_Time', 0, '', Users::getActiveAdminId());
if ($calculate_response_time) {
	$startTime = microtime(true);
}

$log = LoggerManager::getLogger('index');

global $seclog;
$seclog = LoggerManager::getLogger('SECURITY');

// We use the REQUEST_URI later to construct dynamic URLs.  IIS does not pass this field
// to prevent an error, if it is not set, we will assign it to ''
if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = '';
}

//Initialise CSRFGuard library
include_once 'include/csrfmagic/csrf-magic.php';

$action = '';
if (isset($_REQUEST['action'])) {
	$action = vtlib_purify($_REQUEST['action']);
}
if ($action == 'Export') {
	include 'include/utils/export.php';
}
if ($action == 'ExportAjax') {
	include 'include/utils/ExportAjax.php';
}
$nologinaction = array('sendnew2facode');
if (in_array($action, $nologinaction) && file_exists('modules/Utilities/'.$action.'.php')) {
	include 'modules/Utilities/'.$action.'.php';
}
// vtlib customization: Module manager export
if ($action == 'ModuleManagerExport') {
	include 'modules/Settings/ModuleManager/Export.php';
}

//Code added for 'Path Traversal/File Disclosure' security fix - Philip
$is_module = false;
$is_action = false;
if (isset($_REQUEST['module'])) {
	$module = vtlib_purify($_REQUEST['module']);
	if (!preg_match('/[\/.]/', $module)) {
		$dir = @scandir($root_directory.'modules', SCANDIR_SORT_NONE);
		$in_dir = @scandir($root_directory.'modules/'.$module, SCANDIR_SORT_NONE);
		$is_module = @in_array($module, $dir);
		$is_action = @in_array($action.'.php', $in_dir);
	}
	if (!$is_module) {
		die('Module name is missing or incorrect. Please check the module name.');
	}
	if (empty($action)) {
		$is_action = false;
	}
	if (!$is_action) {
		die('Action name is missing or incorrect. Please check the action name: '.vtlib_purify($action));
	}
}

//Code added for 'Multiple SQL Injection Vulnerabilities & XSS issue'
if (isset($_REQUEST['record']) && !is_numeric($_REQUEST['record']) && $_REQUEST['record']!='') {
	die('An invalid record number specified to view details.');
}

// Check to see if there is an authenticated user in the session.
$use_current_login = false;
if (isset($_SESSION['authenticated_user_id']) && (isset($_SESSION['app_unique_key']) && $_SESSION['app_unique_key'] == $application_unique_key)) {
	if ($cbodUniqueUserConnection) {
		$connection_id = coreBOS_Settings::getSetting('cbodUserConnection'.$_SESSION['authenticated_user_id'], -1);
		if (isset($_SESSION['conn_unique_key']) && $_SESSION['conn_unique_key'] == $connection_id) {
			$use_current_login = true;
		} else {
			//To prevent showing checkbox if user is forced to log out
			coreBOS_Session::delete('authenticated_user_id');
			coreBOS_Session::delete('can_unblock');
		}
	} else {
		$use_current_login = true;
	}
	if ($use_current_login) {
		coreBOS_Settings::setSetting('cbodLastLoginTime'.$_SESSION['authenticated_user_id'], time());
	}
}

// Prevent loading Login again if there is an authenticated user in the session.
if (isset($_SESSION["authenticated_user_id"]) && isset($module) && $module == 'Users' && $action == 'Login') {
	$default_action = GlobalVariable::getVariable('Application_Default_Action', 'index', 'Home', $_SESSION["authenticated_user_id"]);
	$default_module = GlobalVariable::getVariable('Application_Default_Module', 'Home', 'Home', $_SESSION["authenticated_user_id"]);
	$result = $adb->pquery('select tabid from vtiger_tab where name=?', array($default_module));
	if (!$result || $adb->num_rows($result)==0) {
		$default_module = 'Home';
	}
	header("Location: index.php?action=$default_action&module=$default_module");
}

if ($use_current_login) {
	//getting the internal_mailer flag
	if (!isset($_SESSION['internal_mailer'])) {
		$qry_res = $adb->pquery("select internal_mailer from vtiger_users where id=?", array($_SESSION["authenticated_user_id"]));
		coreBOS_Session::set('internal_mailer', $adb->query_result($qry_res, 0, 'internal_mailer'));
	}
	$log->debug('authenticated user: '.$_SESSION['authenticated_user_id']);
	if (coreBOS_Settings::getSetting('cbSMActive', 0) && !is_adminID($_SESSION['authenticated_user_id'])) {
		include 'modules/Vtiger/maintenance.php';
		exit;
	}
} elseif (isset($action) && isset($module) && $action=="Authenticate" && $module=="Users") {
	$log->debug('authenticating user');
} else {
	if (!isset($_REQUEST['action']) || ($_REQUEST['action'] != 'Logout' && $_REQUEST['action'] != 'Login')) {
		coreBOS_Session::set('lastpage', $_SERVER['QUERY_STRING']);
	}
	$log->debug('no session > login page');
	if (isset($_REQUEST['action']) && substr($_REQUEST['action'], -4)=='Ajax') {
		echo 'Login';
		die();
	}
	$action = 'Login';
	$module = 'Users';
	include 'modules/Users/Login.php';
	exit;
}

$skipHeaders=false;
$skipFooters=false;
$viewAttachment = false;
$skipSecurityCheck= false;

if (isset($action) && isset($module)) {
	$log->debug('action '.$action);
	if (preg_match("/^Popup/", $action) ||
		preg_match("/^".$module."Ajax/", $action) ||
		preg_match("/^Save/", $action) ||
		preg_match("/^MassEditSave/", $action) ||
		preg_match("/^Delete/", $action) ||
		preg_match("/^ChangePassword/", $action) ||
		preg_match("/^Authenticate/", $action) ||
		preg_match("/^Logout/", $action) ||
		preg_match("/^LeadConvertToEntities/", $action) ||
		preg_match("/^massdelete/", $action) ||
		preg_match("/^updateRole/", $action) ||
		preg_match("/^UserInfoUtil/", $action) ||
		preg_match("/^deleteRole/", $action) ||
		preg_match("/^minical/", $action) ||
		preg_match("/^populatetemplate/", $action) ||
		preg_match("/^TemplateMerge/", $action) ||
		preg_match("/^testemailtemplateusage/", $action) ||
		preg_match("/^saveemailtemplate/", $action) ||
		preg_match("/^ProcessDuplicates/", $action) ||
		preg_match("/^deleteattachments/", $action) ||
		preg_match("/^CreateXL/", $action) ||
		preg_match("/^lastImport/", $action) ||
		preg_match("/^lookupemailtemplate/", $action) ||
		preg_match("/^deleteemailtemplate/", $action) ||
		preg_match("/^CurrencyDelete/", $action) ||
		preg_match("/^UpdateFieldLevelAccess/", $action) ||
		preg_match("/^UpdateDefaultFieldLevelAccess/", $action) ||
		preg_match("/^UpdateProfile/", $action) ||
		preg_match("/^updateRelations/", $action) ||
		preg_match("/^updateNotificationSchedulers/", $action) ||
		preg_match("/^Star/", $action) ||
		preg_match("/^addPbProductRelToDB/", $action) ||
		preg_match("/^UpdateListPrice/", $action) ||
		preg_match("/^PriceListPopup/", $action) ||
		preg_match("/^CreatePDF/", $action) ||
		preg_match("/^CreateSOPDF/", $action) ||
		preg_match("/^redirect/", $action) ||
		preg_match("/^webmail/", $action) ||
		preg_match("/^download/", $action) ||
		preg_match("/^home_rss/", $action) ||
		preg_match("/^ConvertAsFAQ/", $action) ||
		preg_match("/^ActivityAjax/", $action) ||
		preg_match("/^updateCalendarSharing/", $action) ||
		preg_match("/^disable_sharing/", $action) ||
		preg_match("/^RecalculateSharingRules/", $action) ||
		preg_match("/^savewordtemplate/", $action) ||
		preg_match("/^deletewordtemplate/", $action) ||
		preg_match("/^mailmergedownloadfile/", $action) ||
		preg_match("/^getListOfRecords/", $action) ||
		preg_match("/^iCalExport/", $action)
		) {
		$skipHeaders=true;
		//skip headers for all these invocations
		if (preg_match("/^Popup/", $action) ||
			preg_match("/^".$module."Ajax/", $action) ||
			preg_match("/^MassEditSave/", $action) ||
			preg_match("/^ChangePassword/", $action) ||
			preg_match("/^lookupemailtemplate/", $action) ||
			preg_match("/^home_rss/", $action) ||
			preg_match("/^massdelete/", $action) ||
			preg_match("/^mailmergedownloadfile/", $action) ||
			preg_match("/^download/", $action) ||
			preg_match("/^ProcessDuplicates/", $action) ||
			preg_match("/^lastImport/", $action) ||
			preg_match("/^getListOfRecords/", $action) ||
			preg_match("/^iCalExport/", $action)
			) {
			$skipFooters=true;
		}
		//skip footers for all these invocations
		if (preg_match("/^mailmergedownloadfile/", $action) || preg_match("/^iCalExport/", $action)) {
			$viewAttachment = true;
		}
		if ($action == ' Delete ') {
			$skipHeaders=false;
		}
	}

	if ($action == 'Save') {
		header('Expires: Mon, 20 Dec 1998 01:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
	}

	if (($module == 'Users' || $module == 'Home') && (empty($_REQUEST['parenttab']) || $_REQUEST['parenttab'] != 'Settings')) {
		$skipSecurityCheck=true;
	}

	if ($action == 'UnifiedSearch') {
		$currentModuleFile = 'modules/Home/'.$action.'.php';
	} else {
		$currentModuleFile = 'modules/'.$module.'/'.$action.'.php';
	}
	$currentModule = $module;
} else {
	// use $default_module and $default_action
	// Redirect to the correct module with the correct action. We need the URI to include these fields.
	if (isset($_SESSION['authenticated_user_id'])) {
		$userid = $_SESSION['authenticated_user_id'];
	} else {
		$userid = 1;
	}
	$default_action = GlobalVariable::getVariable('Application_Default_Action', 'index', 'Home', $userid);
	$default_module = GlobalVariable::getVariable('Application_Default_Module', 'Home', 'Home', $userid);
	$result = $adb->pquery('select tabid from vtiger_tab where name=?', array($default_module));
	if (!$result || $adb->num_rows($result)==0) {
		$default_module = 'Home';
	}
	header("Location: index.php?action=$default_action&module=$default_module");
	exit();
}

$module = (isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) : '');
$action = (isset($_REQUEST['action']) ? vtlib_purify($_REQUEST['action']) : '');
$record = (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : (isset($_REQUEST['recordid']) ? vtlib_purify($_REQUEST['recordid']) : ''));

$current_user = new Users();

if ($use_current_login) {
	//$result = $current_user->retrieve($_SESSION['authenticated_user_id']);
	//getting the current user info from flat file
	$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);

	if ($result == null) {
		coreBOS_Session::destroy();
		header('Location: index.php?action=Login&module=Users');
	}
	coreBOS_Session::setUserGlobalSessionVariables();

	/* Skip audit trail log for special request types */
	$skip_auditing = false;
	if (($action == 'ActivityReminderCallbackAjax' || (isset($_REQUEST['file']) && $_REQUEST['file'] == 'ActivityReminderCallbackAjax')) && $module == 'Calendar') {
		$skip_auditing = true;
	} elseif (($action == 'TraceIncomingCall' || (isset($_REQUEST['file']) && $_REQUEST['file'] == 'TraceIncomingCall')) && $module == 'PBXManager') {
		$skip_auditing = true;
	}
	$privileges = $current_user->getPrivileges();
	if (coreBOS_Settings::getSetting('audit_trail', false) && !$skip_auditing) {
		$auditaction = $action;
		if ($action=='Save') {
			if (empty($record)) {
				$auditaction = 'Save (Create)';
			} else {
				$auditaction = 'Save (Edit)';
			}
		} elseif ($action=='ReportsAjax') {
			switch ($_REQUEST['file']) {
				case 'CreatePDF':
					$auditaction = 'Report Export PDF';
					break;
				case 'CreateCSV':
					$auditaction = 'Report Export CSV';
					break;
				case 'CreateXL':
					$auditaction = 'Report Export XLS';
					break;
				case 'PrintReport':
					$auditaction = 'Report Print';
					break;
				case 'getJSON':
				default:
					$auditaction = 'Report View';
					break;
			}
		}
		$date_var = $adb->formatDate(date('Y-m-d H:i:s'), true);
		$query = 'insert into vtiger_audit_trial values(?,?,?,?,?,?)';
		$qparams = array($adb->getUniqueID('vtiger_audit_trial'), $current_user->id, $module, $auditaction, $record, $date_var);
		$adb->pquery($query, $qparams);
	}
	if (!$skip_auditing) {
		cbEventHandler::do_action('corebos.audit.action', array($current_user->id, $module, $action, $record, date('Y-m-d H:i:s')));
	}
}
// Force password change
if ($current_user->mustChangePassword() && $_REQUEST['action']!='Logout' && $_REQUEST['action']!='CalendarAjax' && $_REQUEST['action']!='UsersAjax'
	&& $_REQUEST['action']!='ChangePassword' && $_REQUEST['action'] != 'PBXManagerAjax' && !($_REQUEST['module']=='Users' && $_REQUEST['action']=='Save')
) {
	$currentModule = 'Users';
	$currentModuleFile = 'modules/Users/DetailView.php';
	$_REQUEST['action'] = $action = 'DetailView';
	$_REQUEST['module'] = $module = 'Users';
	$_REQUEST['record'] = $current_user->id;
}

if (isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '') {
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
} else {
	if (!empty($current_user->theme)) {
		$theme = $current_user->theme;
	} else {
		$theme = $default_theme;
	}
}
$theme = basename(vtlib_purify($theme));

// if the language is not set yet, then set it to the default language.
if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if (!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}

//set module and application string arrays based upon selected language
$app_currency_strings = return_app_currency_strings_language($current_language);
$app_strings = return_application_language($current_language);
$mod_strings = return_module_language($current_language, $currentModule);

//If DetailView, set focus to record passed in
if ($action == 'DetailView') {
	if (empty($_REQUEST['record'])) {
		die('A record number must be specified to view details.');
	}
	// If we are going to a detail form, load up the record now and use the record to track the viewing.
	if (!empty($_REQUEST['record']) && !empty($current_user->id)) {
		// Only track a viewing if the record was retrieved.
		$focus = CRMEntity::getInstance($currentModule);
		$focus->track_view($current_user->id, $currentModule, $_REQUEST['record']);
	}
}

// set user, theme and language cookies so that login screen defaults to last values
$siteURLParts = parse_url($site_URL);
$cookieDomain = $siteURLParts['host'];
if (isset($_SESSION['authenticated_user_id'])) {
	setcookie('ck_login_id_vtiger', $_SESSION['authenticated_user_id'], 0, null, $cookieDomain, false, true);
}

if ($_REQUEST['module'] == 'Documents' && $action == 'DownloadFile') {
	checkFileAccess('modules/Documents/DownloadFile.php');
	include 'modules/Documents/DownloadFile.php';
	exit;
}

//skip headers for popups, deleting, saving, importing and other actions
if (!$skipHeaders) {
	if ($use_current_login) {
		include 'modules/Vtiger/header.php';
	}
} else {
	/*if(($action != 'mytkt_rss') && ($action != 'home_rss') && ($action != $module."Ajax") && ($action != "body") && ($action != 'ActivityAjax')) {
		require_once 'Smarty_setup.php';
		$vartpl = new vtigerCRM_Smarty;
		getBrowserVariables($vartpl);
		$vartpl->display('BrowserVariables.tpl');
	}*/
}

//logging the security Information
$seclog->debug('########  Module -->  '.$module.'  :: Action --> '.$action.' ::  UserID --> '.$current_user->id.' :: RecordID --> '.$record.' #######');

if (!$skipSecurityCheck && $use_current_login) {
	require_once 'include/utils/UserInfoUtil.php';
	if (preg_match('/Ajax/', $action)) {
		if (isset($_REQUEST['ajxaction']) && $_REQUEST['ajxaction'] == 'LOADRELATEDLIST') {
			$now_action = 'DetailView';
		} else {
			$now_action = (isset($_REQUEST['file']) ? vtlib_purify($_REQUEST['file']) : (isset($_REQUEST['orgajax']) ? vtlib_purify($_REQUEST['orgajax']) : $action));
		}
	} else {
		$now_action=$action;
	}

	if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
		$display = isPermitted($module, $now_action, $_REQUEST['record']);
	} else {
		if ($now_action=='EditView' || $now_action=='EventEditView' || $now_action=='Save') {
			$now_action = 'CreateView';
		}
		$display = isPermitted($module, $now_action);
	}
	$seclog->debug('########### Pemitted ---> '.$display.'  ##############');
} else {
	$display = 'yes';
	$seclog->debug('########### Pemitted ---> yes  ##############');
}

if ($display == 'no'
		&& !(($currentModule=='Tooltip' && $action==$module.'Ajax' && $_REQUEST['file']=='ComputeTooltip')
			|| ($currentModule=='GlobalVariable' && $action==$module.'Ajax' && $_REQUEST['file']=='SearchGlobalVar'))
	) {
		require_once 'Smarty_setup.php';
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
	if ($action==$module.'Ajax') {
		$smarty->assign('PUT_BACK_ACTION', false);
	}
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
} elseif (!vtlib_isModuleActive($currentModule)
		&& !(($currentModule=='Tooltip' && $action==$module.'Ajax' && $_REQUEST['file']=='ComputeTooltip')
			|| ($currentModule=='GlobalVariable' && $action==$module.'Ajax' && $_REQUEST['file']=='SearchGlobalVar'))
	) {
		require_once 'Smarty_setup.php';
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('OPERATION_MESSAGE', getTranslatedString($currentModule, $currentModule) . $app_strings['VTLIB_MOD_NOT_ACTIVE']);
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
} else {
	include_once $currentModuleFile;
}

//added to get the theme . This is a bad fix as we need to know where the problem lies yet
if (isset($_SESSION['vtiger_authenticated_user_theme']) && $_SESSION['vtiger_authenticated_user_theme'] != '') {
	$theme = $_SESSION['vtiger_authenticated_user_theme'];
} else {
	$theme = $default_theme;
}
$theme = basename(vtlib_purify($theme));
$Ajx_module = (isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) : $module);
if ($Ajx_module == 'Events') {
	$Ajx_module = 'Calendar';
}
if ((!$viewAttachment) && (!$viewAttachment && $action!='home_rss') && $action!=$Ajx_module.'Ajax' && $action!='massdelete' && $action!='DashboardAjax' && $action!='ActivityAjax') {
	if ((!$skipFooters) && $action != "ChangePassword" && $action != "body" && $action != $Ajx_module."Ajax" && $action!='Popup' && $action != 'ImportStep3' && $action != 'ActivityAjax' && $action != 'getListOfRecords') {
		cbEventHandler::do_action('corebos.footer.prefooter');
		$coreBOS_uiapp_name = GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name);
		$coreBOS_uiapp_companyname = GlobalVariable::getVariable('Application_UI_CompanyName', $coreBOS_uiapp_name);
		$coreBOS_uiapp_version = GlobalVariable::getVariable('Application_UI_Version', $coreBOS_app_version);
		$coreBOS_uiapp_url = GlobalVariable::getVariable('Application_UI_URL', $coreBOS_app_url);
		echo "<br><br><br><table border=0 cellspacing=0 cellpadding=5 width=100% class=settingsSelectedUI >";
		echo "<tr><td class=small align=left><span style='color: rgb(153, 153, 153);'>".$coreBOS_uiapp_name." <span id='_vtiger_product_version_'>$coreBOS_uiapp_version</span>";
		$coreBOS_uiapp_showgitversion = GlobalVariable::getVariable('Application_UI_ShowGITVersion', 0);
		$coreBOS_uiapp_showgitdate = GlobalVariable::getVariable('Application_UI_ShowGITDate', 0);
		if ($coreBOS_uiapp_showgitversion || $coreBOS_uiapp_showgitdate) {
			list($gitversion,$gitdate) = explode(' ', file_get_contents('include/sw-precache/gitversion'));
			$gitdate = trim(str_replace('-', '', $gitdate));
			echo '&nbsp;('.($coreBOS_uiapp_showgitversion ? $gitversion : '').($coreBOS_uiapp_showgitdate ? $gitdate : '').')';
		}
		echo '</span></td>';
		echo "<td class='cblds-t-align_right small' align=right><span>&copy; 2004-".date('Y')." <a href='$coreBOS_uiapp_url' target='_blank'>$coreBOS_uiapp_companyname</a></span></td></tr></table>";
		if ($calculate_response_time) {
			$endTime = microtime(true);
			echo "<table align='center'><tr><td align='center'>";
			$deltaTime = round($endTime - $startTime, 2);
			echo('&nbsp;Server response time: '.$deltaTime.' seconds.');
			echo "</td></tr></table>\n";
		}
	}
	// ActivityReminder Customization for callback
	if (!$skipFooters) {
		if ($current_user->id!=null && isPermitted('Calendar', 'index') == 'yes' && vtlib_isModuleActive('Calendar')) {
			echo "<script type='text/javascript'>if(typeof(ActivityReminderCallback) != 'undefined') ";
			$cur_time = time();
			$last_reminder_check_time = (isset($_SESSION['last_reminder_check_time']) ? $_SESSION['last_reminder_check_time'] : 0);
			$next_reminder_interval = (isset($_SESSION['next_reminder_interval']) ? $_SESSION['next_reminder_interval'] : 0);
			$reminder_interval_reset = ($last_reminder_check_time + $next_reminder_interval - $cur_time) * 1000;
			if (isset($_SESSION['last_reminder_check_time']) && $reminder_interval_reset > 0) {
				echo "window.setTimeout(function(){
						ActivityReminderCallback();
					},$reminder_interval_reset);";
			} else {
				echo 'ActivityReminderCallback();';
			}
			echo '</script>';
		}
	}

	if ((!$skipFooters) && ($action != 'body') && ($action != $Ajx_module.'Ajax') && ($action != 'ActivityAjax')) {
		include 'modules/Vtiger/footer.php';
	}
}
?>
