<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TSolucio::coreBOS Customizations</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" media="all" href="themes/softed/style.css">
<style type="text/css">br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Module.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');

$current_user = Users::getActiveAdminUser();
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

function ExecuteQuery($query) {
	global $adb,$log;
	global $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;

	$status = $adb->query($query);
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

echo "<table width=80% align=center border=1>";

//Mandatory migration changes

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

global $adb;
ExecuteQuery('CREATE TABLE IF NOT EXISTS vtiger_crmobject (
	crmid int(19),
	deleted tinyint(1),
	setype varchar(100),
	smownerid int(19),
	modifiedtime datetime,
	PRIMARY KEY (crmid),
	INDEX (deleted),
	INDEX (setype)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
if (!($adb->num_rows($result))) {
	ExecuteQuery('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
	ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)');
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schminuteinterval'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schminuteinterval VARCHAR(200)", array());
}
ExecuteQuery("CREATE TABLE IF NOT EXISTS com_vtiger_workflow_tasktypes (
				id int(11) NOT NULL,
				tasktypename varchar(255) NOT NULL,
				label varchar(255),
				classname varchar(255),
				classpath varchar(255),
				templatepath varchar(255),
				modules text(500),
				sourcemodule varchar(255)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8", array());
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtypeid'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtypeid INT(10)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtime'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schtime TIME", array());
} else {
	ExecuteQuery('ALTER TABLE com_vtiger_workflows CHANGE schtime schtime TIME NULL DEFAULT NULL', array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofmonth'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofmonth VARCHAR(200)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofweek'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schdayofweek VARCHAR(200)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schannualdates'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schannualdates VARCHAR(200)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('nexttrigger_time'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('purpose'));
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
$cncrm = $adb->getColumnNames('vtiger_crmentity');
if (!in_array('cbuuid', $cncrm)) {
	ExecuteQuery('ALTER TABLE `vtiger_crmentity` ADD `cbuuid` char(40) default "";');
}

ExecuteQuery('CREATE TABLE `cb_settings` (
	`setting_key` varchar(200) NOT NULL,
	`setting_value` varchar(1000) NOT NULL,
	PRIMARY KEY (`setting_key`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8');

installManifestModule('cbupdater');

ob_start();
include 'modules/cbupdater/getupdatescli.php';
ob_end_clean();
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='mysqlstrictNO_ZERO_IN_DATE'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='ldMenu'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
vtlib_toggleModuleAccess('evvtMenu', true); // in case changeset is applied but module deactivated
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installglobalvars'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
vtlib_toggleModuleAccess('GlobalVariable', true); // in case changeset is applied but module deactivated
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='addModulecbCompany'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
vtlib_toggleModuleAccess('cbCompany', true); // in case changeset is applied but module deactivated
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='modbusinessactions'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
vtlib_toggleModuleAccess('BusinessActions', true); // in case changeset is applied but module deactivated
$rsup = $adb->query("select cbupdaterid from vtiger_cbupdater where classname='installcbmap'");
$updid = $adb->query_result($rsup, 0, 0);
$argv[0] = 'doworkcli';
$argv[1] = 'apply';
$argv[2] = $updid;
include 'modules/cbupdater/doworkcli.php';
vtlib_toggleModuleAccess('cbMap', true); // in case changeset is applied but module deactivated

// Recalculate permissions  RecalculateSharingRules
RecalculateSharingRules();
?>
</table>
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
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
		<b><?php echo $query_count;?> </b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Succeeded : 
	</td>
	<td align="left">
		<b style="color:#006600;">
		<?php echo $success_query_count;?>
		</b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Failed : 
	</td>
	<td align="left">
		<b style="color:#FF0000;">
		<?php echo $failure_query_count ;?>
		</b>
	</td>
   </tr>
</table>
</body>
</html>
