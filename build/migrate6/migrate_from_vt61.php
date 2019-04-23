<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Migrate from vtiger CRM 6.1 to vtiger CRM 6.0
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

$delfields = array(
	'Users.defaulteventstatus', 'Users.defaultactivitytype','Users.hidecompletedevents','Users.is_owner',
	'Accounts.isconvertedfromlead', 'Contacts.isconvertedfromlead','Leads.isconvertedfromlead','Potentials.isconvertedfromlead',
	'Users.phone_crm_extension'
);

foreach ($delfields as $fqfn) {
	list($module,$field) = explode('.', $fqfn);
	$mod = Vtiger_Module::getInstance($module);
	$fld = Vtiger_Field::getInstance($field,$mod);
	if ($fld) {
		$fld->delete();
		ExecuteQuery('ALTER TABLE '.$mod->basetable.' DROP '.$field);
	}
}

$dropflds = array(
	'com_vtiger_workflows.schtypeid','com_vtiger_workflows.schtime',
	'com_vtiger_workflows.schdayofmonth','com_vtiger_workflows.schdayofweek',
	'com_vtiger_workflows.schannualdates','com_vtiger_workflows.nexttrigger_time',
	'vtiger_mailmanager_mailattachments.cid','vtiger_mailscanner_rules.assigned_to',
	'vtiger_mailscanner_rules.cc','vtiger_mailscanner_rules.bcc',
	'vtiger_recurringevents.recurringenddate','vtiger_mailscanner.time_zone',
	'vtiger_portal.createdtime','vtiger_mail_accounts.sent_folder',
	'vtiger_organizationdetails.vatid','vtiger_tab.trial',
);
foreach ($dropflds as $fqfn) {
	list($table,$field) = explode('.', $fqfn);
	ExecuteQuery("ALTER TABLE $table DROP $field");
}

// Add Scheduled Workflows fields
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
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schminuteinterval'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD schminuteinterval VARCHAR(200)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('nexttrigger_time'));
if (!($adb->num_rows($result))) {
	ExecuteQuery("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME", array());
}
$result = $adb->pquery('show columns from com_vtiger_workflowtasks like ?', array('executionorder'));
if (!($adb->num_rows($result))) {
	ExecuteQuery('ALTER TABLE com_vtiger_workflowtasks ADD executionorder INT(10)', array());
	ExecuteQuery('ALTER TABLE `com_vtiger_workflowtasks` ADD INDEX(`executionorder`)', array());
	$result = $adb->pquery('select task_id,workflow_id from com_vtiger_workflowtasks order by workflow_id', array());
	$upd = 'update com_vtiger_workflowtasks set executionorder=? where task_id=?';
	$wfid = null;
	while ($task = $adb->fetch_array($result)) {
		if ($task['workflow_id']!=$wfid) {
			$order = 1;
			$wfid = $task['workflow_id'];
		}
		$adb->pquery($upd, array($order,$task['task_id']));
		$order++;
	}
}

$droptable = array(
	'vtiger_feedback','vtiger_shareduserinfo','vtiger_schedulereports','vtiger_calendar_default_activitytypes',
	'vtiger_calendar_user_activitytypes','vtiger_reporttype'
);

foreach ($droptable as $table) {
	ExecuteQuery("DROP TABLE $table");
}

//Schema changes for vtiger_troubletickets hours & days column
ExecuteQuery('ALTER TABLE vtiger_troubletickets MODIFY hours varchar(200)', array());
ExecuteQuery('ALTER TABLE vtiger_troubletickets MODIFY days varchar(200)', array());
ExecuteQuery('UPDATE vtiger_field set typeofdata=? WHERE fieldname IN(?,?) AND tablename = ?', array('I~O', 'hours', 'days', 'vtiger_troubletickets'));

Vtiger_Cron::deregister('ScheduleReports');

//Updating actions for PriceBooks related list in Products and Services
$productsTabId = getTabId('Products');

ExecuteQuery('UPDATE vtiger_relatedlists SET actions=? WHERE label=? and tabid=?',array('ADD', 'PriceBooks', $productsTabId));

$eventsManager = new VTEventsManager($adb);
$eventsManager->unregisterHandler('Vtiger_RecordLabelUpdater_Handler');

//Start: Customer - Feature #17656 Allow users to add/remove date format with the date fields in workflow send mail task.
$fieldResult = $adb->pquery('SELECT fieldname, name, typeofdata FROM vtiger_field
  INNER JOIN vtiger_tab ON vtiger_tab.tabid = vtiger_field.tabid WHERE typeofdata LIKE ?', array('D%'));
$dateFieldsList = $dateTimeFieldsList = array();
while ($rowData = $adb->fetch_array($fieldResult)) {
	$moduleName = $rowData['name'];
	$fieldName = $rowData['fieldname'];
	$pos = stripos($rowData['typeofdata'], 'DT');
	if ($pos !== false) {
		$dateTimeFieldsList[$moduleName][$fieldName] = $fieldName;
	} else {
		$dateFieldsList[$moduleName][$fieldName] = $fieldName;
	}
}
unset($dateFieldsList['Events']['due_date']);
$dateTimeFieldsList['Events']['due_date'] = 'due_date';

$dateFields = array();
foreach ($dateFieldsList as $moduleName => $fieldNamesList) {
	$dateFields = array_merge($dateFields, $fieldNamesList);
}

$dateTimeFields = array();
foreach ($dateTimeFieldsList as $moduleName => $fieldNamesList) {
	$dateTimeFields = array_merge($dateTimeFields, $fieldNamesList);
}

$taskIdsList = array();
$result = $adb->pquery('SELECT task_id, module_name FROM com_vtiger_workflowtasks
                        INNER JOIN com_vtiger_workflows ON com_vtiger_workflows.workflow_id = com_vtiger_workflowtasks.workflow_id
                        WHERE task LIKE ?', array('%VTEmailTask%'));
while ($rowData = $adb->fetch_array($result)) {
        $taskIdsList[$rowData['task_id']] = $rowData['module_name'];
}

$dateFormat = '($_DATE_FORMAT_)';
$timeZone = '($(general : (__VtigerMeta__) usertimezone))';
foreach ($taskIdsList as $taskId => $taskModuleName) {
        $tm = new VTTaskManager($adb);
        $task = $tm->retrieveTask($taskId);

        $emailTask = new VTEmailTask();
        $properties = get_object_vars($task);
        foreach ($properties as $propertyName => $propertyValue) {
                $propertyValue = str_replace("(general : (__VtigerMeta__) date) $dateFormat", '$(general : (__VtigerMeta__) date)', $propertyValue);

                foreach ($dateFields as $fieldName) {
                        if ($taskModuleName === 'Events' && $fieldName === 'due_date') {
                                continue;
                        }
                        $propertyValue = str_replace("$$fieldName $dateFormat", "$$fieldName", $propertyValue);
                }

                foreach ($dateTimeFields as $fieldName) {
                        if ($taskModuleName === 'Calendar' && $fieldName === 'due_date') {
                                continue;
                        }
                        $propertyValue = str_replace("$$fieldName $timeZone", "$$fieldName", $propertyValue);
                }

                foreach ($dateFieldsList as $moduleName => $fieldNamesList) {
                        foreach ($fieldNamesList as $fieldName) {
                                $propertyValue = str_replace("($moduleName) $fieldName) $dateFormat", "($moduleName) $fieldName)", $propertyValue);
                        }
                }
                foreach ($dateTimeFieldsList as $moduleName => $fieldNamesList) {
                        foreach ($fieldNamesList as $fieldName) {
                                $propertyValue = str_replace("($moduleName) $fieldName) $timeZone", "($moduleName) $fieldName)", $propertyValue);
                        }
                }
                $emailTask->$propertyName = $propertyValue;
        }
        $tm->saveTask($emailTask);
}

$result = $adb->pquery('SELECT task_id FROM com_vtiger_workflowtasks WHERE workflow_id IN
                        (SELECT workflow_id FROM com_vtiger_workflows WHERE module_name IN (?, ?))
                        AND task LIKE ?', array('Calendar', 'Events', '%VTSendNotificationTask%'));
$numOfRows = $adb->num_rows($result);
for ($i = 0; $i < $numOfRows; $i++) {
        $tm = new VTTaskManager($adb);
        $task = $tm->retrieveTask($adb->query_result($result, $i, 'task_id'));
        $emailTask = new VTEmailTask();
        $properties = get_object_vars($task);
        foreach ($properties as $propertyName => $propertyValue) {
                $propertyValue = str_replace('$date_start  $time_start ( $(general : (__VtigerMeta__) usertimezone) ) ', '$date_start', $propertyValue);
                $propertyValue = str_replace('$due_date  $time_end ( $(general : (__VtigerMeta__) usertimezone) )', '$due_date', $propertyValue);
                $propertyValue = str_replace('$due_date ( $(general : (__VtigerMeta__) usertimezone) )', '$due_date', $propertyValue);
                $propertyValue = str_replace('$(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname)', '$contact_id', $propertyValue);
                $emailTask->$propertyName = $propertyValue;
        }
        $tm->saveTask($emailTask);
}

// $maxActionIdResult = $adb->pquery('SELECT MAX(actionid) AS maxid FROM vtiger_actionmapping', array());
// $maxActionId = $adb->query_result($maxActionIdResult, 0, 'maxid');
// Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_actionmapping(actionid, actionname, securitycheck) VALUES(?,?,?)', array($maxActionId+1 ,'Print', '0'));
// echo "<br> added print to vtiger_actionnmapping";
// $module = Vtiger_Module_Model::getInstance('Reports');
// $module->enableTools(Array('Print', 'Export'));
// echo "<br> enabled Print and export";

ExecuteQuery('DELETE FROM vtiger_actionmapping WHERE actionname=? and securitycheck=0', array('Print'));

//Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET quickcreate = ? WHERE tabid = 8 AND (fieldname = ? OR fieldname = ?);", array(0,"filename","filelocationtype"));

//Making document module fields NON masseditable
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"notes_title")); 
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"assigned_user_id")); 
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"notecontent")); 
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"fileversion")); 
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"filestatus")); 
ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?", array(0,"folderid")); 

//Configuration Editor fix
$sql = "UPDATE vtiger_settings_field SET name = ? WHERE name = ?";
ExecuteQuery($sql,array('Configuration Editor','LBL_CONFIG_EDITOR'));

//Avoid premature deletion of activity related records
$relatedToQuery = "SELECT fieldid FROM vtiger_field WHERE tabid=? AND fieldname=?";
$calendarInstance = Vtiger_Module::getInstance('Calendar');
$tabId = $calendarInstance->id;
$result = $adb->pquery($relatedToQuery, array($tabId, 'parent_id'));
$fieldId = $adb->query_result($result,0, 'fieldid');
$insertQuery = "DELETE FROM vtiger_fieldmodulerel where fieldid=?";
$adb->pquery($insertQuery, array($fieldId));
//For contacts the fieldname is contact_id
$contactsRelatedToQuery = "SELECT fieldid FROM vtiger_field WHERE tabid=? AND fieldname=?";
$contactsResult = $adb->pquery($contactsRelatedToQuery, array($tabId, 'contact_id'));
$contactsFieldId = $adb->query_result($contactsResult,0, 'fieldid');
$insertQuery = "DELETE FROM vtiger_fieldmodulerel where fieldid=?";
$adb->pquery($insertQuery, array($contactsFieldId));

$module = Vtiger_Module::getInstance('PBXManager');
$ev = new VTEventsManager($adb);
$ev->unregisterHandler('PBXManagerHandler');
$ev->unregisterHandler('PBXManagerBatchHandler');
if($module) {
	$module->deleteRelatedLists();
	$module->deleteLinks();
	$module->delete();
	ExecuteQuery("DELETE FROM vtiger_def_org_share WHERE tabid=?", Array($module->id));
}
ExecuteQuery("delete from vtiger_asterisk");
ExecuteQuery("DROP TABLE vtiger_pbxmanager");
ExecuteQuery("DROP TABLE vtiger_pbxmanager_phonelookup");
ExecuteQuery("DELETE FROM vtiger_links WHERE linktype=? AND linklabel=? AND linkurl=?",
 Array('HEADERSCRIPT','Incoming Calls', 'modules/PBXManager/resources/PBXManagerJS.js'));
ExecuteQuery("DELETE FROM vtiger_relatedlists WHERE name=? and label=?", Array('get_dependents_list', "PBXManager"));
vtws_deleteWebserviceEntity('PBXManager');
ExecuteQuery('DELETE FROM vtiger_settings_blocks WHERE label=?', array('LBL_INTEGRATION'));
ExecuteQuery('DELETE FROM vtiger_settings_field WHERE name=? and linkto=?', array('LBL_PBXMANAGER', 'index.php?module=PBXManager&parent=Settings&view=Index'));
ExecuteQuery('DELETE FROM vtiger_actionmapping WHERE actionname=? and securitycheck=0', array('ReceiveIncomingcalls'));
ExecuteQuery('DELETE FROM vtiger_actionmapping WHERE actionname=? and securitycheck=0', array('MakeOutgoingCalls'));
ExecuteQuery("DELETE FROM vtiger_tab WHERE name=?", Array('PBXManager'));
ExecuteQuery("DELETE FROM vtiger_entityname WHERE modulename=?", Array('PBXManager'));
ExecuteQuery("DELETE FROM vtiger_field WHERE tablename=?", Array('vtiger_pbxmanager'));
echo "<b>Module PBXManager EXTERMINATED!</b><br>";

$delmods = array(
	'ExtensionStore'
);

foreach ($delmods as $module) {
	$mod = Vtiger_Module::getInstance($module);
	if ($mod) {
		$mod->deleteRelatedLists();
		$mod->deleteLinks();
		$mod->deinitWebservice();
		$mod->delete();
		echo "<b>Module $module EXTERMINATED!</b><br>";
	}
}

ExecuteQuery("update vtiger_version set old_version='5.4.0', current_version='6.0.0' where id=1");
