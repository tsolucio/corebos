<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include 'include/adodb/adodb.inc.php';

if (version_compare(phpversion(), '5.5') < 0) {
	$serverPhpVersion = phpversion();
	require_once 'phpversionfail.php';
	die();
}

require_once 'include/install/language/en_us.lang.php';
require_once 'include/install/resources/utils.php';
require_once 'vtigerversion.php';
global $installationStrings, $vtiger_current_version, $coreBOS_app_version, $current_user, $adb;
include_once 'vtlib/Vtiger/Module.php';
error_reporting(E_ERROR);
ini_set('display_errors', 'on');
$current_user = Users::getActiveAdminUser();

@include_once 'install/config.db.php';
$adb->query("SET SESSION sql_mode = ''");
$adb->query('CREATE TABLE IF NOT EXISTS vtiger_crmobject (
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

// mandatory coreBOS DB changes
$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
	$adb->query('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)');
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schminuteinterval'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schminuteinterval VARCHAR(200)", array());
}
$adb->query("CREATE TABLE IF NOT EXISTS com_vtiger_workflow_tasktypes (
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
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schtypeid INT(10)");
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtime'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schtime TIME");
} else {
	$adb->query('ALTER TABLE com_vtiger_workflows CHANGE schtime schtime TIME NULL DEFAULT NULL');
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofmonth'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schdayofmonth VARCHAR(200)");
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofweek'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schdayofweek VARCHAR(200)");
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schannualdates'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD schannualdates VARCHAR(200)");
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('nexttrigger_time'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME");
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('purpose'));
if (!($adb->num_rows($result))) {
	$adb->query("ALTER TABLE `com_vtiger_workflows` ADD `purpose` TEXT NULL;");
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('relatemodule'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE `com_vtiger_workflows` ADD `relatemodule` varchar(100) default NULL;');
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('options'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE com_vtiger_workflows ADD options VARCHAR(100)');
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('cbquestion'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE com_vtiger_workflows ADD cbquestion INT(11)');
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('recordset'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE com_vtiger_workflows ADD recordset INT(11)');
}
$result = $adb->pquery('show columns from com_vtiger_workflows like ?', array('onerecord'));
if (!($adb->num_rows($result))) {
	$adb->query('ALTER TABLE com_vtiger_workflows ADD onerecord INT(11)');
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

$adb->query('ALTER TABLE `vtiger_currency_info` ADD `currency_position` CHAR(4) NOT NULL;');
$adb->query("UPDATE `vtiger_currency_info` SET `currency_position` = '$1.0';");
$adb->query("UPDATE `vtiger_currency_info` SET `currency_position` = '1.0$' where currency_name='Euro';");
$adb->query("ALTER TABLE vtiger_profile2field ADD summary enum('T', 'H','B', 'N') DEFAULT 'B' NOT NULL");

$adb->query('CREATE TABLE `cb_settings` (
	`setting_key` varchar(200) NOT NULL,
	`setting_value` varchar(1000) NOT NULL,
	PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$adb->query('ALTER TABLE `vtiger_email_access` CHANGE `accesstime` `accesstime` TIME NULL DEFAULT NULL');
$adb->query('ALTER TABLE `vtiger_users` CHANGE `date_entered` `date_entered` DATETIME NOT NULL');
$adb->query('ALTER TABLE `vtiger_users` CHANGE `date_modified` `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;');
$adb->query('ALTER TABLE `vtiger_import_maps` CHANGE `date_entered` `date_entered` DATETIME NOT NULL');
$adb->query('ALTER TABLE `vtiger_import_maps` CHANGE `date_modified` `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;');
$adb->query('ALTER TABLE `vtiger_loginhistory` CHANGE `login_time` `login_time` TIMESTAMP NULL DEFAULT NULL;');
$adb->query('ALTER TABLE `vtiger_loginhistory` CHANGE `logout_time` `logout_time` TIMESTAMP NULL DEFAULT NULL');
$adb->query("UPDATE `vtiger_users` set date_modified=date_entered");
$adb->query("UPDATE `vtiger_import_maps` set date_modified=date_entered");
$adb->query("UPDATE `vtiger_loginhistory` set login_time=null where login_time='0000-00-00 00:00:00'");
$adb->query("UPDATE `vtiger_loginhistory` set logout_time=null where logout_time='0000-00-00 00:00:00'");
$adb->query("UPDATE `vtiger_crmentity` set modifiedtime=createdtime where modifiedtime='0000-00-00 00:00:00'");
//

$the_file = 'CheckSystem.php';
Common_Install_Wizard_Utils::checkFileAccessForInclusion("install/".$the_file);
$_REQUEST['filename'] = 'SetMigrationConfig.php';
$_REQUEST['file'] = 'CheckSystem.php';
$_REQUEST['migrate'] = 'true';

include 'install/'.$the_file;
?>
