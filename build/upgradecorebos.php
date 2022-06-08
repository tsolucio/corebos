<?php
$moduleTitle="TSolucio::coreBOS Customizations: upgrade old coreBOS installs";
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css" />';
echo '<link rel="stylesheet" href="include/LD/assets/styles/override_lds.css" type="text/css" />';
echo '<link rel="stylesheet" href="include/style.css" type="text/css" />';
echo '<link rel="stylesheet" type="text/css" media="all" href="themes/softed/style.css">';
echo '<style type="text/css">br { display: block; margin: 2px; }</style>';
echo '</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">';
echo '<table width=100% border=0><tr><td align=left>';
echo '</td><td align=center style="background-image: url(\'vtlogowmg.png\'); background-repeat: no-repeat; background-position: center;">';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="corebos.org"><img src="include/install/images/app_logo.png" alt="coreBOS" title="coreBOS" border=0></a>';
echo '</td></tr></table>';
echo '<hr style="height: 1px">';

// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Cron.php';
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'include/Webservices/Utils.php';
@include_once 'include/events/include.inc';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');

$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if (!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}
$app_strings = return_application_language($current_language);

$query_count=0;
$success_query_count=0;
$failure_query_count=0;
$success_query_array=array();
$failure_query_array=array();

function ExecuteQuery($query, $params = array()) {
	global $adb,$log;
	global $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;

	$status = $adb->pquery($query, $params);
	$query_count++;
	if (is_object($status)) {
		echo '
		<tr width="100%">
		<td width="10%">'.get_class($status).'</td>
		<td width="10%"><font color="green"> S </font></td>
		<td width="80%">'.$query.'</td>
		</tr>';
		$success_query_array[$success_query_count++] = $query;
		$log->debug("Query Success ==> $query");
	} else {
		echo '
		<tr width="100%">
		<td width="25%">'.$status.'</td>
		<td width="5%"><font color="red"> F </font></td>
		<td width="70%">'.$query.'</td>
		</tr>';
		$failure_query_array[$failure_query_count++] = $query;
		$log->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}
function putMsg($msg) {
	echo '<tr width="100%"><td colspan=3>'.$msg.'</td></tr>';
}

echo "<table width=80% align=center border=1>";

// Some records in VT6x are incorrectly assigned to inexistent users so we fix that before starting by assigning them to the admin user
ExecuteQuery(
	'update vtiger_crmentity
		set smownerid=?
		where smownerid not in (select id from vtiger_users union select groupid from vtiger_groups);',
	array($current_user->id)
);
ExecuteQuery(
	'update vtiger_crmentity
		set smcreatorid=?
		where smcreatorid not in (select id from vtiger_users union select groupid from vtiger_groups);',
	array($current_user->id)
);
ExecuteQuery(
	'update vtiger_crmentity
		set modifiedby=?
		where modifiedby not in (select id from vtiger_users union select groupid from vtiger_groups);',
	array($current_user->id)
);
ExecuteQuery('CREATE TABLE IF NOT EXISTS `cb_settings` (
	`setting_key` varchar(200) NOT NULL,
	`setting_value` varchar(1000) NOT NULL,
	PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

ExecuteQuery('CREATE TABLE IF NOT EXISTS vtiger_crmobject (
	crmid int(19),
	cbuuid char(40),
	deleted tinyint(1),
	setype varchar(100),
	smownerid int(19),
	modifiedtime datetime,
	PRIMARY KEY (crmid),
	INDEX (cbuuid),
	INDEX (deleted),
	INDEX (setype)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$cncrm = $adb->getColumnNames('vtiger_users');
if (!in_array('ename', $cncrm)) {
	$adb->query('ALTER TABLE `vtiger_users` ADD `ename` varchar(200) default "";');
}
$cncrm = $adb->getColumnNames('vtiger_crmentity');
if (!in_array('cbuuid', $cncrm)) {
	$adb->query('ALTER TABLE `vtiger_crmentity` ADD `cbuuid` char(40) default "";');
}
$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
if (!($adb->num_rows($result))) {
	ExecuteQuery('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
	ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)');
}
$cnmsg = $adb->getColumnNames('com_vtiger_workflows');
if (!in_array('purpose', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `purpose` TEXT NULL;');
}
if (!in_array('wfstarton', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `wfstarton` datetime NULL;');
}
if (!in_array('wfendon', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `wfendon` datetime NULL;');
}
if (!in_array('active', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `active` varchar(10) NULL;');
}
if (!in_array('relatemodule', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `relatemodule` varchar(100) default NULL;');
}
if (!in_array('options', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `options` varchar(100) default NULL;');
}
if (!in_array('cbquestion', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `cbquestion` int(11) default NULL;');
}
if (!in_array('recordset', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `recordset` int(11) default NULL;');
}
if (!in_array('onerecord', $cnmsg)) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `onerecord` int(11) default NULL;');
}
$cnmsg = $adb->getColumnNames('vtiger_profile2field');
if (!in_array('summary', $cnmsg)) {
	$adb->query("ALTER TABLE vtiger_profile2field ADD summary enum('T', 'H','B', 'N') DEFAULT 'B' NOT NULL");
}

ExecuteQuery("DELETE FROM vtiger_def_org_share WHERE vtiger_def_org_share.tabid not in (select tabid from vtiger_tab)");
ExecuteQuery("update vtiger_users set theme='softed'");
ExecuteQuery("update vtiger_version set old_version='5.4.0', current_version='5.5.0' where id=1");
ExecuteQuery("DELETE FROM vtiger_field WHERE tablename = 'vtiger_inventoryproductrel'");

?>
</table>
<br /><br />
<strong style="color:#FF0000">Failed Queries Log</strong>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
<?php
foreach ($failure_query_array as $failed_query) {
	echo '<br><font color="red">'.$failed_query.'</font>';
}
?>
</div>
<br /><br />
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
   <tr>
	<td width="75%" align="right" nowrap>
		Total Number of queries executed :
	</td>
	<td width="25%" align="left">
		<strong><?php echo $query_count;?> </strong>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Succeeded :
	</td>
	<td align="left">
		<strong style="color:#006600;">
		<?php echo $success_query_count;?>
		</strong>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Failed :
	</td>
	<td align="left">
		<strong style="color:#FF0000;">
		<?php echo $failure_query_count ;?>
		</strong>
	</td>
   </tr>
</table>
<?php

require_once 'Smarty_setup.php';
$adb->query("SET sql_mode=''");
$currentModule = $_REQUEST['module'] = 'cbupdater';
$_REQUEST['action'] = 'getupdates';
// temporarily deactivate Calendar
vtlib_toggleModuleAccess('Calendar', false);
include 'modules/cbupdater/getupdates.php';
vtlib_toggleModuleAccess('Calendar', true);
$_REQUEST['action'] = 'apply';

$rsup = $adb->query("select cbupdaterid,execstate from vtiger_cbupdater where classname='denormalizechangeset'");
if ($adb->query_result($rsup, 0, 'execstate')!='Executed') {
	$updid = $adb->query_result($rsup, 0, 'cbupdaterid');
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
}
if (!vtlib_isModuleActive('GlobalVariable')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installglobalvars'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('GlobalVariable', true); // in case changeset is applied but module deactivated
}
if (!vtlib_isModuleActive('evvtMenu')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='ldMenu'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('evvtMenu', true); // in case changeset is applied but module deactivated
}
if (!vtlib_isModuleActive('cbCompany')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='addModulecbCompany'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('cbCompany', true); // in case changeset is applied but module deactivated
}
if (!vtlib_isModuleActive('cbMap')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installcbmap'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('cbMap', true); // in case changeset is applied but module deactivated
}
if (!vtlib_isModuleActive('BusinessActions')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='modbusinessactions'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('BusinessActions', true); // in case changeset is applied but module deactivated
}
if (!vtlib_isModuleActive('cbTermConditions')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installcbTermConditions'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('cbTermConditions', true); // in case changeset is applied but module deactivated
}

if (file_exists('modules/cbupdater/cbupdates/coreboscrm.xml')) {
	// Install and setup new modules
	define('postINSTALL', 'postINSTALL');
	require 'install_modules.php';
}

$activeModules = array(
	'BusinessActions',
	'cbCompany',
	'cbCVManagement',
	'cbMap',
	'cbtranslation',
	'cbupdater',
	'com_vtiger_workflow',
	'evvtMenu',
	'GlobalVariable',
);

foreach ($activeModules as $activateModule) {
	vtlib_toggleModuleAccess($activateModule, true);
}

// Recalculate permissions  RecalculateSharingRules
RecalculateSharingRules();

putMsg("<H1 style='color:red'>NOW LOG IN AND APPLY ALL THE UPDATES AS USUAL</H1>");
?>
</body>
</html>
