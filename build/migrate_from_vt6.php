<?php
$moduleTitle="TSolucio::coreBOS Customizations: migrate from vtiger CRM 6.0";
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>';
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
include_once('vtlib/Vtiger/Module.php');
require_once("modules/com_vtiger_workflow/include.inc");
require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');

$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(1); // admin
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if(!empty($current_user->language)) {
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
	if(is_object($status)) {
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

function deleteWorkflow($wfid) {
	ExecuteQuery("DELETE FROM com_vtiger_workflowtasks WHERE workflow_id = $wfid");
	ExecuteQuery("DELETE FROM com_vtiger_workflows WHERE workflow_id = $wfid");
}

echo "<table width=80% align=center border=1>";

$delfields = array(
	'Users.calendarsharedtype', 'Users.callduration', 'Users.dayoftheweek', 'Users.default_record_view',
	'Users.leftpanelhide', 'Users.no_of_currency_decimals', 'Users.othereventduration', 'Users.rowheight',
	'Users.truncate_trailing_zeros','ModComments.reasontoedit'
);

foreach ($delfields as $fqfn) {
	list($module,$field) = explode('.', $fqfn);
	$mod = Vtiger_Module::getInstance($module);
	$fld = Vtiger_Field::getInstance($field,$mod);
	if ($fld) $fld->delete();
}

$dropflds = array(
		'vtiger_accounttype.sortorderid', 'vtiger_activitytype.sortorderid',
		'vtiger_assetstatus.sortorderid', 'vtiger_campaignstatus.sortorderid',
		'vtiger_campaigntype.sortorderid', 'vtiger_carrier.sortorderid',
		'vtiger_contract_priority.sortorderid', 'vtiger_contract_status.sortorderid',
		'vtiger_contract_type.sortorderid', 'vtiger_eventstatus.sortorderid',
		'vtiger_expectedresponse.sortorderid', 'vtiger_faqcategories.sortorderid',
		'vtiger_faqstatus.sortorderid', 'vtiger_glacct.sortorderid',
		'vtiger_industry.sortorderid', 'vtiger_invoicestatus.sortorderid',
		'vtiger_leadsource.sortorderid', 'vtiger_leadstatus.sortorderid',
		'vtiger_manufacturer.sortorderid', 'vtiger_opportunity_type.sortorderid',
		'vtiger_postatus.sortorderid', 'vtiger_productcategory.sortorderid',
		'vtiger_progress.sortorderid', 'vtiger_projectmilestonetype.sortorderid',
		'vtiger_projectpriority.sortorderid', 'vtiger_projectstatus.sortorderid',
		'vtiger_projecttaskpriority.sortorderid', 'vtiger_projecttaskprogress.sortorderid',
		'vtiger_projecttasktype.sortorderid', 'vtiger_projecttype.sortorderid',
		'vtiger_quotestage.sortorderid', 'vtiger_rating.sortorderid',
		'vtiger_sales_stage.sortorderid', 'vtiger_salutationtype.sortorderid',
		'vtiger_servicecategory.sortorderid', 'vtiger_service_usageunit.sortorderid',
		'vtiger_sostatus.sortorderid', 'vtiger_taskpriority.sortorderid',
		'vtiger_taskstatus.sortorderid', 'vtiger_ticketcategories.sortorderid',
		'vtiger_ticketpriorities.sortorderid', 'vtiger_ticketseverities.sortorderid',
		'vtiger_ticketstatus.sortorderid', 'vtiger_tracking_unit.sortorderid',
		'vtiger_usageunit.sortorderid','vtiger_crmentity.label','vtiger_field.summaryfield',
		'vtiger_profile.directly_related_to_role','vtiger_role.allowassignedrecordsto',
		'vtiger_settings_field.pinned',	'vtiger_users.calendarsharedtype', 'vtiger_users.callduration',
		'vtiger_users.dayoftheweek', 'vtiger_users.default_record_view', 'vtiger_users.leftpanelhide',
		'vtiger_users.no_of_currency_decimals', 'vtiger_users.othereventduration', 'vtiger_users.rowheight',
		'vtiger_users.truncate_trailing_zeros','vtiger_projecttaskstatus.sortorderid',
		'com_vtiger_workflows.filtersavedinnew','com_vtiger_workflowtask_queue.task_contents'
);

foreach ($dropflds as $fqfn) {
	list($table,$field) = explode('.', $fqfn);
	ExecuteQuery("ALTER TABLE $table DROP $field");
}

$picklistResult = $adb->pquery('SELECT distinct fieldname FROM vtiger_field WHERE uitype IN (15,33)', array());
$numRows = $adb->num_rows($picklistResult);
for($i=0; $i<$numRows; $i++) {
	$fieldName = $adb->query_result($picklistResult,$i,'fieldname');
	$cninv=$adb->getColumnNames('vtiger_'.$fieldName);
	if (!in_array('sortorderid', $cninv)) continue;
	ExecuteQuery('ALTER TABLE vtiger_'.$fieldName.' DROP sortorderid');
}

$modname = 'Users';
$module = Vtiger_Module::getInstance($modname);
$field = Vtiger_Field::getInstance('hour_format',$module);
$field->delete();
$field = Vtiger_Field::getInstance('start_hour',$module);
$field->delete();

$droptable = array(
	'vtiger_calendarsharedtype', 'vtiger_dayoftheweek', 'vtiger_hour_format', 'vtiger_start_hour',
	'vtiger_callduration', 'vtiger_crmsetup', 'vtiger_default_record_view', 'vtiger_module_dashboard_widgets',
	'vtiger_shorturls', 'vtiger_rowheight', 'vtiger_sqltimelog', 'vtiger_modtracker_relations', 'vtiger_othereventduration',
	'vtiger_no_of_currency_decimals','vtiger_customerportal_fields'
);

foreach ($droptable as $table) {
	ExecuteQuery("DROP TABLE $table");
}

ExecuteQuery('ALTER TABLE `vtiger_troubletickets` ADD `from_portal` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;');
ExecuteQuery('UPDATE `vtiger_troubletickets`
		INNER JOIN `vtiger_ticketcf` ON `vtiger_ticketcf`.ticketid=`vtiger_troubletickets`.ticketid
		SET `vtiger_troubletickets`.`from_portal`=`vtiger_ticketcf`.`from_portal`');
ExecuteQuery('ALTER TABLE `vtiger_ticketcf` DROP `from_portal`');

// Migrate FAQ Comments
ExecuteQuery("INSERT INTO `vtiger_faqcomments`(`commentid`, `faqid`, `comments`, `createdtime`) 
		SELECT `modcommentsid`, `related_to`, `commentcontent`, CURDATE()
		FROM vtiger_modcomments
		INNER JOIN vtiger_crmentity ON crmid = related_to
		WHERE deleted = 0 and setype='FAQ'");
// Migrate Ticket Comments
ExecuteQuery("INSERT INTO `vtiger_ticketcomments`(`commentid`, `ticketid`, `comments`, `ownerid`, `ownertype`, `createdtime`)
		SELECT `modcommentsid`, `related_to`, `commentcontent`, 
				CASE WHEN customer = '' THEN userid ELSE customer END, 
				CASE WHEN customer = '' THEN 'user' ELSE 'customer' END, CURDATE()
		FROM vtiger_modcomments
		INNER JOIN vtiger_crmentity ON crmid = related_to
		WHERE deleted = 0 and setype='HelpDesk'");

// Recover chat functionality
ExecuteQuery("CREATE TABLE `vtiger_chat_users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `nick` varchar(50) NOT NULL,
  `session` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL DEFAULT '000.000.000.000',
  `ping` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_users_nick_idx` (`nick`),
  KEY `chat_users_session_idx` (`session`),
  KEY `chat_users_ping_idx` (`ping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `vtiger_chat_msg` (
`id` int(20) NOT NULL AUTO_INCREMENT,
`chat_from` int(20) NOT NULL DEFAULT '0',
`chat_to` int(20) NOT NULL DEFAULT '0',
`born` datetime DEFAULT NULL,
`msg` varchar(255) NOT NULL,
PRIMARY KEY (`id`),
KEY `chat_msg_chat_from_idx` (`chat_from`),
KEY `chat_msg_chat_to_idx` (`chat_to`),
KEY `chat_msg_born_idx` (`born`),
CONSTRAINT `fk_1_vtiger_chat_msg` FOREIGN KEY (`chat_from`) REFERENCES `vtiger_chat_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `vtiger_chat_pvchat` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `msg` int(20) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_pvchat_msg_idx` (`msg`),
  CONSTRAINT `fk_1_vtiger_chat_pvchat` FOREIGN KEY (`msg`) REFERENCES `vtiger_chat_msg` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `vtiger_chat_pchat` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `msg` int(20) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_pchat_msg_idx` (`msg`),
  CONSTRAINT `fk_1_vtiger_chat_pchat` FOREIGN KEY (`msg`) REFERENCES `vtiger_chat_msg` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

ExecuteQuery("CREATE TABLE `vtiger_ownernotify` (
`crmid` int(19) DEFAULT NULL,
`smownerid` int(19) DEFAULT NULL,
`flag` int(3) DEFAULT NULL,
KEY `ownernotify_crmid_flag_idx` (`crmid`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

$package = new Vtiger_Package();
$rdo = $package->importManifest("modules/PBXManager/manifest.xml");

ExecuteQuery('ALTER TABLE vtiger_inventoryproductrel MODIFY comment text', array());

// Meta information cleanup

$inventoryModules = array('Invoice','SalesOrder','PurchaseOrder','Quotes');
$actions = array('Import','Export');
for($i = 0; $i < count($inventoryModules); $i++) {
	$moduleName = $inventoryModules[$i];
	$moduleInstance = Vtiger_Module::getInstance($moduleName);
	foreach ($actions as $actionName) {
		Vtiger_Access::updateTool($moduleInstance, $actionName, false, '');
	}
}

$delws = array('LineItem','ProductTaxes','Tax');
foreach ($delws as $ws) {
	$wsrs = $adb->query("select id from vtiger_ws_entity where name='$ws'");
	$wsid = $adb->query_result($wsrs,0,0);
	ExecuteQuery("delete from vtiger_ws_entity_tables where webservice_entity_id=$wsid");
	ExecuteQuery("delete from vtiger_ws_entity where id=$wsid");
}
$wsfrs = $adb->query("SELECT fieldtypeid FROM `vtiger_ws_entity_fieldtype` WHERE `table_name` in ('vtiger_inventoryproductrel','vtiger_producttaxrel')");
while ($wse = $adb->fetch_array($wsfrs)) {
	$wseid = $wse['fieldtypeid'];
	ExecuteQuery("delete from vtiger_ws_entity_referencetype where fieldtypeid=$wseid");
	ExecuteQuery("delete from vtiger_ws_entity_fieldtype where fieldtypeid=$wseid");
}

$wsoprs = $adb->query("select operationid from vtiger_ws_operation where name='retrieve_inventory'");
$wsopid = $adb->query_result($wsoprs,0,0);
ExecuteQuery("delete from vtiger_ws_operation_parameters where operationid=$wsopid");
ExecuteQuery("delete from vtiger_ws_operation where operationid=$wsopid");
ExecuteQuery("UPDATE vtiger_ws_entity SET handler_path='include/Webservices/VtigerModuleOperation.php',handler_class='VtigerModuleOperation' where name in ('Invoice','Quotes','PurchaseOrder','SalesOrder');");
ExecuteQuery("delete from com_vtiger_workflowtasks_entitymethod where module_name='ModComments'");
ExecuteQuery("delete from vtiger_eventhandlers where handler_class='ModCommentsHandler' and handler_path='modules/ModComments/ModCommentsHandler.php'");
ExecuteQuery("delete from vtiger_eventhandlers where handler_class='Vtiger_RecordLabelUpdater_Handler' and handler_path='modules/Vtiger/handlers/RecordLabelUpdater.php'");
ExecuteQuery("DELETE FROM vtiger_eventhandler_module WHERE handler_class='Vtiger_RecordLabelUpdater_Handler'");
ExecuteQuery("delete from vtiger_eventhandlers where handler_class='InvoiceHandler' and handler_path='modules/Invoice/InvoiceHandler.php'");
ExecuteQuery("delete from vtiger_eventhandlers where handler_class='PurchaseOrderHandler' and handler_path='modules/PurchaseOrder/PurchaseOrderHandler.php'");
ExecuteQuery("delete from vtiger_links where linktype in ('DASHBOARDWIDGET','DETAILVIEWSIDEBARWIDGET','LISTVIEWSIDEBARWIDGET')");
ExecuteQuery("delete from vtiger_eventhandlers where handler_class='PickListHandler' and handler_path='modules/Settings/Picklist/handlers/PickListHandler.php'");
ExecuteQuery("update vtiger_blocks set blocklabel='' where blocklabel like 'Emails_Block%'");

ExecuteQuery("delete from vtiger_settings_field where name='LBL_EDIT_FIELDS'");
$fieldId = $adb->getUniqueID('vtiger_settings_field');
$query = "INSERT INTO vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto, sequence) ".
		"VALUES ($fieldId,4,'EMAILTEMPLATES','ViewTemplate.gif','LBL_EMAIL_TEMPLATE_DESCRIPTION','index.php?module=Settings&action=listemailtemplates&parenttab=Settings',4)";
ExecuteQuery($query);
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Administration&action=index&parenttab=Settings' where name = 'LBL_USERS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=listroles&parenttab=Settings' where name = 'LBL_ROLES'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=ListProfiles&parenttab=Settings' where name = 'LBL_PROFILES'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=listgroups&parenttab=Settings' where name = 'USERGROUPLIST'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=OrgSharingDetailView&parenttab=Settings' where name = 'LBL_SHARING_ACCESS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=DefaultFieldPermissions&parenttab=Settings' where name = 'LBL_FIELDS_ACCESS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=AuditTrailList&parenttab=Settings' where name = 'LBL_AUDIT_TRAIL'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=ListLoginHistory&parenttab=Settings' where name = 'LBL_LOGIN_HISTORY_DETAILS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=ModuleManager&parenttab=Settings' where name = 'VTLIB_LBL_MODULE_MANAGER'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=PickList&action=PickList&parenttab=Settings' where name = 'LBL_PICKLIST_EDITOR'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings' where name = 'LBL_PICKLIST_DEPENDENCY_SETUP'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=listwordtemplates&parenttab=Settings' where name = 'LBL_MAIL_MERGE'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=listnotificationschedulers&parenttab=Settings' where name = 'NOTIFICATIONSCHEDULERS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=listinventorynotifications&parenttab=Settings' where name = 'INVENTORYNOTIFICATION'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=OrganizationConfig&parenttab=Settings' where name = 'LBL_COMPANY_DETAILS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=EmailConfig&parenttab=Settings' where name = 'LBL_MAIL_SERVER_SETTINGS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=BackupServerConfig&parenttab=Settings' where name = 'LBL_BACKUP_SERVER_SETTINGS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=CurrencyListView&parenttab=Settings' where name = 'LBL_CURRENCY_SETTINGS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=TaxConfig&parenttab=Settings' where name = 'LBL_TAX_SETTINGS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=System&action=listsysconfig&parenttab=Settings' where name = 'LBL_SYSTEM_INFO'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=ProxyServerConfig&parenttab=Settings' where name = 'LBL_PROXY_SETTINGS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=Announcements&parenttab=Settings' where name = 'LBL_ANNOUNCEMENT'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=DefModuleView&parenttab=Settings' where name = 'LBL_DEFAULT_MODULE_VIEW'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=OrganizationTermsandConditions&parenttab=Settings' where name = 'INVENTORYTERMSANDCONDITIONS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings' where name = 'LBL_CUSTOMIZE_MODENT_NUMBER'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=MailScanner&parenttab=Settings' where name = 'LBL_MAIL_SCANNER'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings' where name = 'LBL_LIST_WORKFLOWS'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Settings&action=MenuEditor&parenttab=Settings' where name = 'LBL_MENU_EDITOR'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=com_vtiger_workflow&action=workflowlist' where name = 'LBL_WORKFLOW_LIST'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=ConfigEditor&action=index', description='Update configuration file of the application' where name = 'Configuration Editor' or name = 'LBL_CONFIG_EDITOR'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=ModTracker&action=BasicSettings&parenttab=Settings&formodule=ModTracker' where name = 'ModTracker'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=CustomerPortal&action=index&parenttab=Settings' where name = 'LBL_CUSTOMER_PORTAL'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Webforms&action=index&parenttab=Settings', description='Allows you to manage Webforms' where name = 'Webforms'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=CronTasks&action=ListCronJobs&parenttab=Settings', description='Allows you to Configure Cron Task' where name = 'Scheduler'");
ExecuteQuery("UPDATE `vtiger_settings_field` set `linkto` = 'index.php?module=Tooltip&action=QuickView&parenttab=Settings' where name = 'LBL_TOOLTIP_MANAGEMENT'");

// Change HelpDesk Workflows
global $adb;
$workflowManager = new VTWorkflowManager($adb);
$taskManager = new VTTaskManager($adb);
$wfrs = $adb->query("SELECT workflow_id,summary FROM com_vtiger_workflows WHERE module_name='HelpDesk'");
while ($wfid = $adb->fetch_array($wfrs)) {
	deleteWorkflow($wfid['workflow_id']);
	putMsg('Workflow "'.$wfid['summary'].'" deleted!');
}

// Trouble Tickets workflow on creation from Customer Portal
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Created from Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner and the Related Contact when Ticket is created from Portal';
$task->methodName = "NotifyOnPortalTicketCreation";
$taskManager->saveTask($task);
putMsg('Workflow "'.$helpDeskWorkflow->description.'" created!');

// Trouble Tickets workflow on ticket update from Customer Portal
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Updated from Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_MODIFY;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner when Comment is added to a Ticket from Customer Portal';
$task->methodName = "NotifyOnPortalTicketComment";
$taskManager->saveTask($task);
putMsg('Workflow "'.$helpDeskWorkflow->description.'" created!');

// Trouble Tickets workflow on ticket change, which is not from Customer Portal - Both Record Owner and Related Customer
$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"false:boolean"}]';
$helpDeskWorkflow->description = "Workflow for Ticket Change, not from the Portal";
$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
$helpDeskWorkflow->defaultworkflow = 1;
$workflowManager->save($helpDeskWorkflow);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Record Owner on Ticket Change, which is not done from Portal';
$task->methodName = "NotifyOwnerOnTicketChange";
$taskManager->saveTask($task);

$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
$task->active = true;
$task->summary = 'Notify Related Customer on Ticket Change, which is not done from Portal';
$task->methodName = "NotifyParentOnTicketChange";
$taskManager->saveTask($task);
putMsg('Workflow "'.$helpDeskWorkflow->description.'" created!');

ExecuteQuery("update vtiger_version set old_version='5.4.0', current_version='5.5.0' where id=1");

// Recalculate permissions  RecalculateSharingRules
RecalculateSharingRules();

?>
</table>
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
	<?php
		foreach($failure_query_array as $failed_query)
			echo '<br><font color="red">'.$failed_query.'</font>';
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
		Queries Successed : 
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
