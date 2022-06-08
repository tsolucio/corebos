<?php
$moduleTitle="coreBOS Customizations: upgrade old coreBOS installs";
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
echo '</td><td>';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="corebos.org"><img src="themes/images/coreboslogo.png" alt="coreBOS" title="coreBOS" border=0></a>';
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
global $current_user, $adb;
set_time_limit(0);
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('memory_limit', '1024M');

ExecuteQuery('UPDATE vtiger_users SET language=? WHERE vtiger_users.is_admin=?', array('en_us', 'on'));
$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
$default_language = 'en_us';
$current_language = 'en_us';
$_SESSION['authenticated_user_language'] = 'en_us';
$app_strings = return_application_language($current_language);

$query_count=0;
$success_query_count=0;
$failure_query_count=0;
$success_query_array=array();
$failure_query_array=array();

function ExecuteQuery($query, $params = array()) {
	global $adb,$log;
	global $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;
	$paramstring = (count($params)>0 ? '&nbsp;&nbsp;'.print_r($params, true) : '');
	$status = $adb->pquery($query, $params);
	$query_count++;
	if (is_object($status)) {
		echo '
	<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_2-of-8">'.get_class($status).'</div>
	<div class="slds-col slds-size_1-of-8"><span style="color:green"> S </span></div>
	<div class="slds-col slds-size_5-of-8">'.$query.$paramstring.'</div>
	</div>';
		$success_query_array[$success_query_count++] = $query;
		$log->debug("Query Success ==> $query");
	} else {
		echo '
	<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_2-of-8">'.$status.'</div>
	<div class="slds-col slds-size_1-of-8"><span style="color:red"> F </span></div>
	<div class="slds-col slds-size_5-of-8">'.$query.$paramstring.'</div>
	</div>';
		$failure_query_array[$failure_query_count++] = $query.$paramstring;
		$log->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}
function putMsg($msg) {
	echo '<div class="slds-col slds-size_10-of-10">'.$msg.'</div>';
}

function installManifestModule($module) {
	$package = new Vtiger_Package();
	ob_start();
	$rdo = $package->importManifest("modules/$module/manifest.xml");
	$out = ob_get_contents();
	ob_end_clean();
	putMsg($out);
	if ($rdo) {
		putMsg("$module installed!");
	} else {
		putMsg("<span style='color:red'>ERROR installing $module!</span>");
	}
}

echo '<article class="slds-card slds-m-left_x-large slds-p-left_small slds-m-right_x-large slds-p-right_small slds-p-bottom_small slds-m-top_small">';
$startTime = microtime(true);
//////////////
// Put your custom migration steps here
//////////////
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';

//Mandatory migration changes
$rs = $adb->query('select max(id) from vtiger_ws_entity');
$max = (int)$adb->query_result($rs, 0, 0)+2;
ExecuteQuery('ALTER TABLE vtiger_ws_entity MODIFY id int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT='.$max);
ExecuteQuery('UPDATE vtiger_ws_entity_seq SET id='.$max);
ExecuteQuery("update vtiger_crmentity set smcreatorid=smownerid where smcreatorid=0 and smownerid!=0");
ExecuteQuery("update vtiger_crmentity set smcreatorid=1 where smcreatorid=0");
ExecuteQuery("update vtiger_crmentity set smownerid=smcreatorid where smownerid=0 and smcreatorid!=0");
ExecuteQuery("update vtiger_crmentity set modifiedby=smcreatorid where modifiedby=0 and smcreatorid!=0");
ExecuteQuery("update vtiger_contactdetails set reportsto=null where reportsto=''");
ExecuteQuery("update vtiger_troubletickets set hours=0 where hours=''");
ExecuteQuery("update vtiger_troubletickets set hours=REPLACE(hours, ',', '.') where hours LIKE '%,%'");
ExecuteQuery("update vtiger_troubletickets set parent_id=0 where parent_id=''");
ExecuteQuery("update vtiger_troubletickets set product_id=0 where product_id=''");
ExecuteQuery("update vtiger_cron_task set laststart=null where laststart=''");
ExecuteQuery("update vtiger_cron_task set lastend=null where lastend=''");
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
ExecuteQuery('ALTER TABLE vtiger_cbupdater DROP INDEX `findupdate`, ADD UNIQUE `findupdate` (`filename`, `classname`, `pathfilename`) USING BTREE;');
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
	ExecuteQuery('ALTER TABLE `vtiger_crmentity` ADD `cbuuid` char(40) default "";');
}
ExecuteQuery("CREATE TABLE IF NOT EXISTS `com_vtiger_workflows_expfunctions` (
	`expname` varchar(180) NOT NULL,
	`expinfo` varchar(250) NOT NULL,
	`funcname` varchar(180) NOT NULL,
	`funcfile` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
ExecuteQuery("ALTER TABLE `com_vtiger_workflows_expfunctions` ADD PRIMARY KEY (`expname`);");
$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
if (!($adb->num_rows($result))) {
	ExecuteQuery('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
	ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)');
}
ExecuteQuery(
	"CREATE TABLE IF NOT EXISTS com_vtiger_workflow_tasktypes (
		id int(11) NOT NULL,
		tasktypename varchar(255) NOT NULL,
		label varchar(255),
		classname varchar(255),
		classpath varchar(255),
		templatepath varchar(255),
		modules text(500),
		sourcemodule varchar(255)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8",
	array()
);
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schtypeid'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtypeid INT(10)", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schminuteinterval'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schminuteinterval VARCHAR(200)", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schtime'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtime TIME", array());
} else {
	ExecuteQuery('ALTER TABLE com_vtiger_workflows CHANGE schtime schtime TIME NULL DEFAULT NULL', array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schdayofmonth'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofmonth VARCHAR(200)", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schdayofweek'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofweek VARCHAR(200)", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('schannualdates'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schannualdates VARCHAR(200)", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('nexttrigger_time'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('purpose'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE `com_vtiger_workflows` ADD `purpose` TEXT NULL;", array());
}
$cnmsg = $adb->getColumnNames('com_vtiger_workflows');
if (!in_array('wfstarton', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `wfstarton` datetime NULL;');
}
if (!in_array('wfendon', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `wfendon` datetime NULL;');
}
if (!in_array('active', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `active` varchar(10) NULL;');
}
if (!in_array('relatemodule', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `relatemodule` varchar(100) default NULL;');
}
if (!in_array('options', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `options` varchar(100) default NULL;');
}
if (!in_array('cbquestion', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `cbquestion` int(11) default NULL;');
}
if (!in_array('recordset', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `recordset` int(11) default NULL;');
}
if (!in_array('onerecord', $cnmsg)) {
	ExecuteQuery('ALTER TABLE `com_vtiger_workflows` ADD `onerecord` int(11) default NULL;');
}
$taskTypes = array();
$defaultModules = array('include' => array(), 'exclude'=>array());
$createToDoModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns","Quotes","PurchaseOrder","SalesOrder","Invoice"), 'exclude'=>array("Calendar", "FAQ", "Events"));
$createEventModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns"), 'exclude'=>array("Calendar", "FAQ", "Events"));

$taskTypes[] = array("name"=>"VTEmailTask", "label"=>"Send Mail", "classname"=>"VTEmailTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEmailTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEmailTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
$taskTypes[] = array("name"=>"VTEntityMethodTask", "label"=>"Invoke Custom Function", "classname"=>"VTEntityMethodTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEntityMethodTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
$taskTypes[] = array("name"=>"VTCreateTodoTask", "label"=>"Create Todo", "classname"=>"VTCreateTodoTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateTodoTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateTodoTask.tpl", "modules"=>$createToDoModules, "sourcemodule"=>'');
$taskTypes[] = array("name"=>"VTUpdateFieldsTask", "label"=>"Update Fields", "classname"=>"VTUpdateFieldsTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTUpdateFieldsTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTUpdateFieldsTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
$taskTypes[] = array("name"=>"VTCreateEntityTask", "label"=>"Create Entity", "classname"=>"VTCreateEntityTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateEntityTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateEntityTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
$taskTypes[] = array("name"=>"VTSMSTask", "label"=>"SMS Task", "classname"=>"VTSMSTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTSMSTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTSMSTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'SMSNotifier');

foreach ($taskTypes as $taskType) {
	VTTaskType::registerTaskType($taskType);
}

ExecuteQuery('ALTER TABLE `vtiger_currency_info` ADD `currency_position` CHAR(4) NOT NULL;');
ExecuteQuery("UPDATE `vtiger_currency_info` SET `currency_position` = '$1.0';");
ExecuteQuery("UPDATE `vtiger_currency_info` SET `currency_position` = '1.0$' where currency_name='Euro';");
$cnmsg = $adb->getColumnNames('vtiger_profile2field');
if (!in_array('summary', $cnmsg)) {
	$adb->query("ALTER TABLE vtiger_profile2field ADD summary enum('T', 'H','B', 'N') DEFAULT 'B' NOT NULL");
}

ExecuteQuery("DELETE FROM vtiger_def_org_share WHERE vtiger_def_org_share.tabid not in (select tabid from vtiger_tab)");
ExecuteQuery("update vtiger_users set theme='softed'");
ExecuteQuery("update vtiger_version set old_version='5.4.0', current_version='5.5.0' where id=1");
ExecuteQuery("DELETE FROM vtiger_field WHERE tablename = 'vtiger_inventoryproductrel'");
installManifestModule('cbupdater');

echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
require_once 'Smarty_setup.php';
$adb->query("SET sql_mode=''");
$currentModule = $_REQUEST['module'] = 'cbupdater';
$_REQUEST['action'] = 'getupdates';
// temporarily deactivate Calendar
vtlib_toggleModuleAccess('Calendar', false);
$DONOTDISPLAYUPDATES = 1;
include 'modules/cbupdater/getupdates.php';
vtlib_toggleModuleAccess('Calendar', true);
$_REQUEST['action'] = 'apply';

echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
$rsup = $adb->query("select cbupdaterid,execstate from vtiger_cbupdater where classname='mysqlstrictNO_ZERO_IN_DATE'");
if ($adb->query_result($rsup, 0, 'execstate')!='Executed') {
	$updid = $adb->query_result($rsup, 0, 'cbupdaterid');
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
$rsup = $adb->query("select cbupdaterid,execstate from vtiger_cbupdater where classname='denormalizechangeset'");
if ($adb->query_result($rsup, 0, 'execstate')!='Executed') {
	$updid = $adb->query_result($rsup, 0, 'cbupdaterid');
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
if (!vtlib_isModuleActive('GlobalVariable')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installglobalvars'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('GlobalVariable', true); // in case changeset is applied but module deactivated
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
if (!vtlib_isModuleActive('evvtMenu')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='ldMenu'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('evvtMenu', true); // in case changeset is applied but module deactivated
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
if (!vtlib_isModuleActive('cbCompany')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='addModulecbCompany'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('cbCompany', true); // in case changeset is applied but module deactivated
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='addFieldAppTocbUpdater'");
$updid = $adb->query_result($rsup, 0, 0);
$_REQUEST['idstring'] = $updid;
include 'modules/cbupdater/dowork.php';
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='addIsRelatedListBlock'");
$updid = $adb->query_result($rsup, 0, 0);
$_REQUEST['idstring'] = $updid;
include 'modules/cbupdater/dowork.php';
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
if (!vtlib_isModuleActive('cbMap')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installcbmap'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('cbMap', true); // in case changeset is applied but module deactivated
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
if (!vtlib_isModuleActive('BusinessActions')) {
	$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='modbusinessactions'");
	$updid = $adb->query_result($rsup, 0, 0);
	$_REQUEST['idstring'] = $updid;
	include 'modules/cbupdater/dowork.php';
	vtlib_toggleModuleAccess('BusinessActions', true); // in case changeset is applied but module deactivated
}
echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
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

// Delete all changesets that do not have a file
$cs = $adb->query('select cbupdaterid,pathfilename from vtiger_cbupdater inner join vtiger_crmentity on crmid=cbupdaterid where deleted=0');
while ($cbupd = $adb->fetch_array($cs)) {
	if (!file_exists($cbupd['pathfilename'])) {
		ExecuteQuery('update vtiger_crmentity set deleted=1 where crmid='.$cbupd['cbupdaterid']);
	}
}
ExecuteQuery('update vtiger_cbupdater set blocked=0,execstate=? where filename=? and classname=?', array('Pending', 'picklist_translations', 'picklist_translations'));
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

echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
// Recalculate permissions  RecalculateSharingRules
RecalculateSharingRules();

echo '<strong>&nbsp;&nbsp;time: '.round(microtime(true) - $startTime, 2).' seconds.</strong>';
putMsg("<H1 style='color:red'>NOW LOG IN AND APPLY ALL THE UPDATES AS USUAL</H1>");
echo '</article>';
if (count($failure_query_array)>0) {
	echo <<<EOT
<article class="slds-card slds-m-left_x-large slds-m-right_x-large slds-m-top_small slds-m-bottom_x-large slds-p-around_small">
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" class="slds-m-left_small slds-m-top_x-small" style="height:200px;overflow:auto;">
EOT;
	foreach ($failure_query_array as $failed_query) {
		echo '<span style="color:red">'.$failed_query.'</span><br>';
	}
	echo '</div></article>';
}
	echo <<<EOT
<article class="slds-card slds-m-left_x-large slds-m-right_x-large slds-m-top_small slds-m-bottom_x-large slds-p-around_small">
	<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_2-of-8">Total Number of queries executed : </div>
	<div class="slds-col slds-size_6-of-8"><b>{$query_count}</b></div>
	</div>
	<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_2-of-8">Queries Succeeded : </div>
	<div class="slds-col slds-size_6-of-8"><b style="color:#006600;">{$success_query_count}</b></div>
	</div>
	<div class="slds-grid slds-gutters">
	<div class="slds-col slds-size_2-of-8">Queries Failed : </div>
	<div class="slds-col slds-size_6-of-8"><b style="color:#FF0000;">{$failure_query_count}</b></div>
	</div>
</article>
</body>
</html>
EOT;
