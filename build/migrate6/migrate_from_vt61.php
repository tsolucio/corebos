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
 
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtypeid'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD schtypeid INT(10)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schtime'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD schtime TIME", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofmonth'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD schdayofmonth VARCHAR(100)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schdayofweek'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD schdayofweek VARCHAR(100)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('schannualdates'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD schannualdates VARCHAR(100)", array());
}
$result = $adb->pquery("show columns from com_vtiger_workflows like ?", array('nexttrigger_time'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE com_vtiger_workflows ADD nexttrigger_time DATETIME", array());
}

//73 starts
$query = 'SELECT 1 FROM vtiger_currencies WHERE currency_name=?';
$result = $adb->pquery($query, array('Sudanese Pound'));
if($adb->num_rows($result) <= 0){
    //Inserting Currency Sudanese Pound to vtiger_currencies
    Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies (currencyid,currency_name,currency_code,currency_symbol) VALUES ('.$adb->getUniqueID("vtiger_currencies").',"Sudanese Pound","SDG","£")',array());
    Vtiger_Utils::AddColumn('vtiger_mailmanager_mailattachments', 'cid', 'VARCHAR(100)');
}
//73 ends

//74 starts

//Start: Moving Entity methods of Tickets to Workflows
$result = $adb->pquery('SELECT DISTINCT workflow_id FROM com_vtiger_workflowtasks WHERE workflow_id IN
                                (SELECT workflow_id FROM com_vtiger_workflows WHERE module_name IN (?) AND defaultworkflow = ?)
                                AND task LIKE ?', array($moduleName, 1, '%VTEntityMethodTask%'));
$numOfRows = $adb->num_rows($result);

for($i=0; $i<$numOfRows; $i++) {
    $wfs = new VTWorkflowManager($adb);
    $workflowModel = $wfs->retrieve($adb->query_result($result, $i, 'workflow_id'));
    $workflowModel->filtersavedinnew = 6;

    $tm = new VTTaskManager($adb);
    $tasks = $tm->getTasksForWorkflow($workflowModel->id);
    foreach ($tasks as $task) {
        $properties = get_object_vars($task);

        $emailTask = new VTEmailTask();
        $emailTask->executeImmediately = 0;
        $emailTask->summary = $properties['summary'];
        $emailTask->active = $properties['active'];
        switch($properties['methodName']) {
            case 'NotifyOnPortalTicketCreation' :
                    $conditions = Zend_Json::decode($workflowModel->test);
                    $oldCondtions = array();

                            if(!empty($conditions)) {
                                    $previousConditionGroupId = 0;
                                    foreach($conditions as $condition) {

                                            $fieldName = $condition['fieldname'];
                                            $fieldNameContents = explode(' ', $fieldName);
                                            if (count($fieldNameContents) > 1) {
                                                    $fieldName = '('. $fieldName .')';
                                            }

                                            $groupId = $condition['groupid'];
                                            if (!$groupId) {
                                                    $groupId = 0;
                                            }

                                            $groupCondition = 'or';
                                            if ($groupId === $previousConditionGroupId || count($conditions) === 1) {
                                                    $groupCondition = 'and';
                                            }

                                            $joinCondition = 'or';
                                            if (isset ($condition['joincondition'])) {
                                                    $joinCondition = $condition['joincondition'];
                                            } elseif($groupId === 0) {
                                                    $joinCondition = 'and';
                                            }

                                            $value = $condition['value'];
                                            switch ($value) {
                                                    case 'false:boolean'	: $value = 0;	break;
                                                    case 'true:boolean'		: $value = 1;	break;
                                                    default                     : $value;	break;
                                            }

                                            $oldCondtions[] = array(
                                                            'fieldname' => $fieldName,
                                                            'operation' => $condition['operation'],
                                                            'value' => $value,
                                                            'valuetype' => 'rawtext',
                                                            'joincondition' => $joinCondition,
                                                            'groupjoin' => $groupCondition,
                                                            'groupid' => $groupId
                                            );
                                            $previousConditionGroupId = $groupId;
                                    }
                            }
                    $newConditions = array(
                                    array('fieldname' => 'from_portal',
                                                    'operation' => 'is',
                                                    'value' => '1',
                                                    'valuetype' => 'rawtext',
                                                    'joincondition' => '',
                                                    'groupjoin' => 'and',
                                                    'groupid' => '0')
                    );
                    $newConditions = array_merge($oldCondtions, $newConditions);

                    $workflowModel->test = Zend_Json::encode($newConditions);
                    $workflowModel->description = 'Ticket Creation From Portal : Send Email to Record Owner and Contact';
                    $wfs->save($workflowModel);

                    $emailTask->id = '';
                    $emailTask->workflowId = $properties['workflowId'];
                    $emailTask->summary = 'Notify Record Owner when Ticket is created from Portal';
                    $emailTask->fromEmail =  '$(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname)&lt;$(general : (__VtigerMeta__) supportEmailId)&gt;';
                    $emailTask->recepient = ',$(assigned_user_id : (Users) email1)';
                    $emailTask->subject =  '[From Portal] $ticket_no [ Ticket Id : $(general : (__VtigerMeta__) recordId) ] $ticket_title';
                    $emailTask->content = 'Ticket No : $ticket_no<br>
                                              Ticket ID : $(general : (__VtigerMeta__) recordId)<br>
                                              Ticket Title : $ticket_title<br><br>
                                              $description';
                    $tm->saveTask($emailTask);

                    $emailTask->id = $properties['id'];
                    $emailTask->summary = 'Notify Related Contact when Ticket is created from Portal';
                    $emailTask->fromEmail =  '$(general : (__VtigerMeta__) supportName)&lt;$(general : (__VtigerMeta__) supportEmailId)&gt;';
                    $emailTask->recepient = ',$(contact_id : (Contacts) email)';

                    $tm->saveTask($emailTask);
                    break;
                case 'NotifyOnPortalTicketComment'	:
                            $tm->deleteTask($properties['id']);
                            Migration_Index_View::ExecuteQuery('DELETE FROM com_vtiger_workflows WHERE workflow_id = ?', array($workflowModel->id));
                            break;
                
                case 'NotifyParentOnTicketChange'	:
                            $newWorkflowModel = $wfs->newWorkflow($workflowModel->moduleName);
                            $workflowProperties = get_object_vars($workflowModel);
                            foreach ($workflowProperties as $workflowPropertyName => $workflowPropertyValue) {
                                    $newWorkflowModel->$workflowPropertyName = $workflowPropertyValue;
                            }

                            $conditions = Zend_Json::decode($newWorkflowModel->test);
                            $oldCondtions = array();

                            if(!empty($conditions)) {
                                    $previousConditionGroupId = 0;
                                    foreach($conditions as $condition) {

                                            $fieldName = $condition['fieldname'];
                                            $fieldNameContents = explode(' ', $fieldName);
                                            if (count($fieldNameContents) > 1) {
                                                    $fieldName = '('. $fieldName .')';
                                            }

                                            $groupId = $condition['groupid'];
                                            if (!$groupId) {
                                                    $groupId = 0;
                                            }

                                            $groupCondition = 'or';
                                            if ($groupId === $previousConditionGroupId || count($conditions) === 1) {
                                                    $groupCondition = 'and';
                                            }

                                            $joinCondition = 'or';
                                            if (isset ($condition['joincondition'])) {
                                                    $joinCondition = $condition['joincondition'];
                                            } elseif($groupId === 0) {
                                                    $joinCondition = 'and';
                                            }

                                            $value = $condition['value'];
                                            switch ($value) {
                                                    case 'false:boolean'	: $value = 0;	break;
                                                    case 'true:boolean'		: $value = 1;	break;
                                                    default                     : $value;	break;
                                            }

                                            $oldCondtions[] = array(
                                                            'fieldname' => $fieldName,
                                                            'operation' => $condition['operation'],
                                                            'value' => $value,
                                                            'valuetype' => 'rawtext',
                                                            'joincondition' => $joinCondition,
                                                            'groupjoin' => $groupCondition,
                                                            'groupid' => $groupId
                                            );
                                            $previousConditionGroupId = $groupId;
                                    }
                            }
                            $newConditions = array(
                                            array('fieldname' => 'ticketstatus',
                                                            'operation' => 'has changed to',
                                                            'value' => 'Closed',
                                                            'valuetype' => 'rawtext',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1'),
                                            array('fieldname' => 'solution',
                                                            'operation' => 'has changed',
                                                            'value' => '',
                                                            'valuetype' => '',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1'),
                                            array('fieldname' => 'description',
                                                            'operation' => 'has changed',
                                                            'value' => '',
                                                            'valuetype' => '',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1')
                            );
                            $newConditions = array_merge($oldCondtions, $newConditions);

                            $newAccountCondition = array(
                                            array('fieldname' => '(parent_id : (Accounts) emailoptout)',
                                                    'operation' => 'is',
                                                    'value' => '0',
                                                    'valuetype' => 'rawtext',
                                                    'joincondition' => 'and',
                                                    'groupjoin' => 'and',
                                                    'groupid' => '0')
                            );
                            $newWorkflowConditions = array_merge($newAccountCondition, $newConditions);

                            unset($newWorkflowModel->id);
                            $newWorkflowModel->test = Zend_Json::encode($newWorkflowConditions);
                            $newWorkflowModel->description = 'Send Email to Organization on Ticket Update';
                            $wfs->save($newWorkflowModel);

                            $emailTask->id = '';
                            $emailTask->summary = 'Send Email to Organization on Ticket Update';
                            $emailTask->fromEmail = '$(general : (__VtigerMeta__) supportName)&lt;$(general : (__VtigerMeta__) supportEmailId)&gt;';
                            $emailTask->recepient = ',$(parent_id : (Accounts) email1)';
                            $emailTask->subject = '$ticket_no [ Ticket Id : $(general : (__VtigerMeta__) recordId) ] $ticket_title';
                            $emailTask->content = 'Ticket ID : $(general : (__VtigerMeta__) recordId)<br>Ticket Title : $ticket_title<br><br>
                                                            Dear $(parent_id : (Accounts) accountname),<br><br>
                                                            The Ticket is replied the details are :<br><br>
                                                            Ticket No : $ticket_no<br>
                                                            Status : $ticketstatus<br>
                                                            Category : $ticketcategories<br>
                                                            Severity : $ticketseverities<br>
                                                            Priority : $ticketpriorities<br><br>
                                                            Description : <br>$description<br><br>
                                                            Solution : <br>$solution<br>
                                                            The comments are : <br>
                                                            $allComments<br><br>
                                                            Regards<br>Support Administrator';

                            $emailTask->workflowId = $newWorkflowModel->id;
                            $tm->saveTask($emailTask);

                            $portalCondition = array(
                                            array('fieldname' => 'from_portal',
                                                    'operation' => 'is',
                                                    'value' => '0',
                                                    'valuetype' => 'rawtext',
                                                    'joincondition' => '',
                                                    'groupjoin' => 'and',
                                                    'groupid' => '0')
                            );

                            unset($newWorkflowModel->id);
                            $newWorkflowModel->executionCondition = 1;
                            $newWorkflowModel->test = Zend_Json::encode(array_merge($newAccountCondition, $portalCondition));
                            $newWorkflowModel->description = 'Ticket Creation From CRM : Send Email to Organization';
                            $wfs->save($newWorkflowModel);

                            $emailTask->id = '';
                            $emailTask->workflowId = $newWorkflowModel->id;
                            $emailTask->summary = 'Ticket Creation From CRM : Send Email to Organization';
                            $tm->saveTask($emailTask);

                            $newContactCondition = array(
                                            array('fieldname' => '(contact_id : (Contacts) emailoptout)',
                                                    'operation' => 'is',
                                                    'value' => '0',
                                                    'valuetype' => 'rawtext',
                                                    'joincondition' => 'and',
                                                    'groupjoin' => 'and',
                                                    'groupid' => '0')
                            );
                            $newConditions = array_merge($newContactCondition, $newConditions);

                            $workflowModel->test = Zend_Json::encode($newConditions);
                            $workflowModel->description = 'Send Email to Contact on Ticket Update';
                            $wfs->save($workflowModel);

                            $emailTask->id = $properties['id'];
                            $emailTask->workflowId = $properties['workflowId'];
                            $emailTask->summary = 'Send Email to Contact on Ticket Update';
                            $emailTask->recepient = ',$(contact_id : (Contacts) email)';
                            $emailTask->content = 'Ticket ID : $(general : (__VtigerMeta__) recordId)<br>Ticket Title : $ticket_title<br><br>
                                                            Dear $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname),<br><br>
                                                            The Ticket is replied the details are :<br><br>
                                                            Ticket No : $ticket_no<br>
                                                            Status : $ticketstatus<br>
                                                            Category : $ticketcategories<br>
                                                            Severity : $ticketseverities<br>
                                                            Priority : $ticketpriorities<br><br>
                                                            Description : <br>$description<br><br>
                                                            Solution : <br>$solution<br>
                                                            The comments are : <br>
                                                            $allComments<br><br>
                                                            Regards<br>Support Administrator';

                            $tm->saveTask($emailTask);

                            unset($newWorkflowModel->id);
                            $newWorkflowModel->executionCondition = 1;
                            $newWorkflowModel->test = Zend_Json::encode(array_merge($newContactCondition, $portalCondition));
                            $newWorkflowModel->description = 'Ticket Creation From CRM : Send Email to Contact';
                            $wfs->save($newWorkflowModel);

                            $emailTask->id = '';
                            $emailTask->workflowId = $newWorkflowModel->id;
                            $emailTask->summary = 'Ticket Creation From CRM : Send Email to Contact';
                            $tm->saveTask($emailTask);
                            break;


                    case 'NotifyOwnerOnTicketChange'	:
                            $tm->deleteTask($task->id);

                            $newWorkflowModel = $wfs->newWorkflow($workflowModel->moduleName);
                            $workflowProperties = get_object_vars($workflowModel);
                            foreach ($workflowProperties as $workflowPropertyName => $workflowPropertyValue) {
                                    $newWorkflowModel->$workflowPropertyName = $workflowPropertyValue;
                            }

                            $conditions = Zend_Json::decode($newWorkflowModel->test);
                            $oldCondtions = array();

                            if(!empty($conditions)) {
                                    $previousConditionGroupId = 0;
                                    foreach($conditions as $condition) {

                                            $fieldName = $condition['fieldname'];
                                            $fieldNameContents = explode(' ', $fieldName);
                                            if (count($fieldNameContents) > 1) {
                                                    $fieldName = '('. $fieldName .')';
                                            }

                                            $groupId = $condition['groupid'];
                                            if (!$groupId) {
                                                    $groupId = 0;
                                            }

                                            $groupCondition = 'or';
                                            if ($groupId === $previousConditionGroupId || count($conditions) === 1) {
                                                    $groupCondition = 'and';
                                            }

                                            $joinCondition = 'or';
                                            if (isset ($condition['joincondition'])) {
                                                    $joinCondition = $condition['joincondition'];
                                            } elseif($groupId === 0) {
                                                    $joinCondition = 'and';
                                            }

                                            $value = $condition['value'];
                                            switch ($value) {
                                                    case 'false:boolean'	: $value = 0;	break;
                                                    case 'true:boolean'		: $value = 1;	break;
                                                    default                     : $value;	break;
                                            }

                                            $oldCondtions[] = array(
                                                            'fieldname' => $fieldName,
                                                            'operation' => $condition['operation'],
                                                            'value' => $value,
                                                            'valuetype' => 'rawtext',
                                                            'joincondition' => $joinCondition,
                                                            'groupjoin' => $groupCondition,
                                                            'groupid' => $groupId
                                            );
                                            $previousConditionGroupId = $groupId;
                                    }
                            }
                            $newConditions = array(
                                            array('fieldname' => 'ticketstatus',
                                                            'operation' => 'has changed to',
                                                            'value' => 'Closed',
                                                            'valuetype' => 'rawtext',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1'),
                                            array('fieldname' => 'solution',
                                                            'operation' => 'has changed',
                                                            'value' => '',
                                                            'valuetype' => '',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1'),
                                            array('fieldname' => 'assigned_user_id',
                                                            'operation' => 'has changed',
                                                            'value' => '',
                                                            'valuetype' => '',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1'),
                                            array('fieldname' => 'description',
                                                            'operation' => 'has changed',
                                                            'value' => '',
                                                            'valuetype' => '',
                                                            'joincondition' => 'or',
                                                            'groupjoin' => 'and',
                                                            'groupid' => '1')

                            );
                            $newConditions = array_merge($oldCondtions, $newConditions);

                            unset($newWorkflowModel->id);
                            $newWorkflowModel->test = Zend_Json::encode($newConditions);
                            $newWorkflowModel->description = 'Send Email to Record Owner on Ticket Update';
                            $wfs->save($newWorkflowModel);

                            $emailTask->id = '';
                            $emailTask->workflowId = $newWorkflowModel->id;
                            $emailTask->summary = 'Send Email to Record Owner on Ticket Update';
                            $emailTask->fromEmail = '$(general : (__VtigerMeta__) supportName)&lt;$(general : (__VtigerMeta__) supportEmailId)&gt;';
                            $emailTask->recepient = ',$(assigned_user_id : (Users) email1)';
                            $emailTask->subject =  'Ticket Number : $ticket_no $ticket_title';
                            $emailTask->content = 'Ticket ID : $(general : (__VtigerMeta__) recordId)<br>Ticket Title : $ticket_title<br><br>
                                                            Dear $(assigned_user_id : (Users) last_name) $(assigned_user_id : (Users) first_name),<br><br>
                                                            The Ticket is replied the details are :<br><br>
                                                            Ticket No : $ticket_no<br>
                                                            Status : $ticketstatus<br>
                                                            Category : $ticketcategories<br>
                                                            Severity : $ticketseverities<br>
                                                            Priority : $ticketpriorities<br><br>
                                                            Description : <br>$description<br><br>
                                                            Solution : <br>$solution
                                                            $allComments<br><br>
                                                            Regards<br>Support Administrator';
                            $emailTask->id = '';
                            $tm->saveTask($emailTask);

                            $portalCondition = array(
                                            array('fieldname' => 'from_portal',
                                                    'operation' => 'is',
                                                    'value' => '0',
                                                    'valuetype' => 'rawtext',
                                                    'joincondition' => '',
                                                    'groupjoin' => 'and',
                                                    'groupid' => '0')
                            );

                            unset($newWorkflowModel->id);
                            $newWorkflowModel->executionCondition = 1;
                            $newWorkflowModel->test = Zend_Json::encode($portalCondition);
                            $newWorkflowModel->description = 'Ticket Creation From CRM : Send Email to Record Owner';
                            $wfs->save($newWorkflowModel);

                            $emailTask->id = '';
                            $emailTask->workflowId = $newWorkflowModel->id;
                            $emailTask->summary = 'Ticket Creation From CRM : Send Email to Record Owner';
                            $tm->saveTask($emailTask);
                            break;
            }
    }
}
echo '<br>SuccessFully Done For Tickets<br>';
//End: Moved Entity methods of Tickets to Workflows
//74 ends

//75 starts
//create new table for feedback on removing old version
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_feedback (userid INT(19), dontshow VARCHAR(19) default false);");

//75 ends

//76 starts
$moduleInstance = Vtiger_Module::getInstance('Calendar');
$fieldInstance = Vtiger_Field::getInstance('activitytype',$moduleInstance);

$fieldInstance->setPicklistValues(array('Mobile Call'));  // añadir traducción
$sql = "UPDATE vtiger_activitytype SET presence = '0' WHERE activitytype ='Mobile Call'";
Migration_Index_View::ExecuteQuery($sql,array());

//76 ends

//77 starts
$sql = "ALTER TABLE vtiger_products MODIFY productname VARCHAR( 100 )";
Migration_Index_View::ExecuteQuery($sql,array());
echo "<br>Updated to varchar(100) for productname";

$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('CFA Franc BCEAO'));
    if(!$adb->num_rows($result)) {
        Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
            array($adb->getUniqueID('vtiger_currencies'), 'CFA Franc BCEAO', 'XOF', 'CFA'));
    }
$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('CFA Franc BEAC'));
    if(!$adb->num_rows($result)) {
        Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
            array($adb->getUniqueID('vtiger_currencies'), 'CFA Franc BEAC', 'XAF', 'CFA'));
    }    
echo "<br>Added CFA Franc BCEAO and CFA Franc BEAC currencies";

//77 ends(Some function addGroupTaxTemplatesForQuotesAndPurchaseOrder)

//78 starts
//78 ends

//79 starts
Migration_Index_View::ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_shareduserinfo
						(userid INT(19) NOT NULL default 0, shareduserid INT(19) NOT NULL default 0,
						color VARCHAR(50), visible INT(19) default 1);", array());

Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_mailscanner_rules ADD assigned_to INT(10), ADD cc VARCHAR(255), ADD bcc VARCHAR(255)', array());
$assignedToId = Users::getActiveAdminId();
Migration_Index_View::ExecuteQuery("UPDATE vtiger_mailscanner_rules SET assigned_to=?", array($assignedToId));
echo "<br> Adding assigned to, cc, bcc fields for mail scanner rules";


//Schema changes for vtiger_troubletickets hours & days column
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_troubletickets MODIFY hours decimal(25,8)', array());
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_troubletickets MODIFY days decimal(25,8)', array());

Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET defaultvalue=? WHERE tablename=? and fieldname=?", array('1', 'vtiger_pricebook', 'active'));
echo "<br> updated default value for pricebooks active";

$relationId = $adb->getUniqueID('vtiger_relatedlists');
$contactTabId = getTabid('Contacts');
$vendorTabId = getTabId('Vendors');
$actions = 'SELECT';

$query = 'SELECT max(sequence) as maxsequence FROM vtiger_relatedlists where tabid = ?';
$result = $adb->pquery($query, array($contactTabId));
$sequence = $adb->query_result($result, 0 ,'maxsequence');

$query = 'INSERT INTO vtiger_relatedlists VALUES(?,?,?,?,?,?,?,?)';
$result = Migration_Index_View::ExecuteQuery($query, array($relationId, $contactTabId,$vendorTabId,'get_vendors',($sequence+1),'Vendors',0,$actions));

//Schema changes for vtiger_troubletickets hours & days column
Migration_Index_View::ExecuteQuery('UPDATE vtiger_field set typeofdata=? WHERE fieldname IN(?,?) AND tablename = ?', array('N~O', 'hours', 'days', 'vtiger_troubletickets'));

//79 ends

//80 starts
//Added recurring enddate column for events,to vtiger_recurringevents table
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_recurringevents ADD COLUMN recurringenddate date', array());
echo "added field recurring enddate to vtiger_recurringevents to save untill date of repeat events";

//80 ends

//81 starts
//81 ends

//82 starts
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_mailscanner CHANGE timezone time_zone VARCHAR(10)", array());
echo "<br>Changed timezone column name for mail scanner";

//82 ends

//83 starts
$result = $adb->pquery('SELECT task_id FROM com_vtiger_workflowtasks WHERE workflow_id IN
                        (SELECT workflow_id FROM com_vtiger_workflows WHERE module_name IN (?, ?))
                        AND task LIKE ?', array('Calendar', 'Events', '%VTSendNotificationTask%'));
$numOfRowas = $adb->num_rows($result);
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
echo '<br>Successfully Done<br>';

//83 ends

//84 starts
$query = "ALTER table vtiger_relcriteria modify comparator varchar(20)";
Migration_Index_View::ExecuteQuery($query, array());

//To copy imagename saved in vtiger_attachments for products and contacts into respectively base table
//to support filters on imagename field
$productIdSql = 'SELECT productid,name FROM vtiger_seattachmentsrel INNER JOIN vtiger_attachments ON
                                        vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid INNER JOIN vtiger_products ON
                                        vtiger_products.productid = vtiger_seattachmentsrel.crmid';
$productIds = $adb->pquery($productIdSql,array());
$numOfRows = $adb->num_rows($productIds);

$productImageMap = array();
for ($i = 0; $i < $numOfRows; $i++) {
        $productId = $adb->query_result($productIds, $i, "productid");
        $imageName = decode_html($adb->query_result($productIds, $i, "name"));
        if(!empty($productImageMap[$productId])){
                array_push($productImageMap[$productId], $imageName);
        }elseif(empty($productImageMap[$productId])){
                $productImageMap[$productId] = array($imageName);
        }
}
foreach ($productImageMap as $productId => $imageNames) {
        $implodedNames = implode(",", $imageNames);
        Migration_Index_View::ExecuteQuery('UPDATE vtiger_products SET imagename = ? WHERE productid = ?',array($implodedNames,$productId));
}
echo 'updating image information for products table is completed';

$ContactIdSql = 'SELECT contactid,name FROM vtiger_seattachmentsrel INNER JOIN vtiger_attachments ON
                                        vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid INNER JOIN vtiger_contactdetails ON
                                        vtiger_contactdetails.contactid = vtiger_seattachmentsrel.crmid';
$contactIds = $adb->pquery($ContactIdSql,array());
$numOfRows = $adb->num_rows($contactIds);

for ($i = 0; $i < $numOfRows; $i++) {
        $contactId = $adb->query_result($contactIds, $i, "contactid");
        $imageName = decode_html($adb->query_result($contactIds, $i, "name"));
        Migration_Index_View::ExecuteQuery('UPDATE vtiger_contactdetails SET imagename = ? WHERE contactid = ?',array($imageName,$contactId));
}
echo 'updating image information for contacts table is completed';

//Updating actions for PriceBooks related list in Products and Services
$productsTabId = getTabId('Products');

Migration_Index_View::ExecuteQuery("UPDATE vtiger_relatedlists SET actions=? WHERE label=? and tabid=? ",array('ADD,SELECT', 'PriceBooks', $productsTabId));
echo '<br>Updated PriceBooks related list actions for products and services';

$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_schedulereports(
            reportid INT(10),
            scheduleid INT(3),
            recipients TEXT,
            schdate VARCHAR(20),
            schtime TIME,
            schdayoftheweek VARCHAR(100),
            schdayofthemonth VARCHAR(100),
            schannualdates VARCHAR(500),
            specificemails VARCHAR(500),
            next_trigger_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
            ENGINE=InnoDB DEFAULT CHARSET=utf8;", array());

Vtiger_Cron::register('ScheduleReports', 'cron/modules/Reports/ScheduleReports.service', 900);

Migration_Index_View::ExecuteQuery('UPDATE vtiger_cron_task set description = ?  where name = "ScheduleReports" ', array("Recommended frequency for ScheduleReports is 15 mins"));
Migration_Index_View::ExecuteQuery('UPDATE vtiger_cron_task set module = ? where name = "ScheduleReports" ', array("Reports"));
echo '<br>Enabled Scheduled reports feature';

/**
* To add defaulteventstatus and defaultactivitytype fields to Users Module
* Save 2 clicks usability feature
*/
require_once 'vtlib/Vtiger/Module.php';
$module = Vtiger_Module::getInstance('Users');
  if ($module) {
      $blockInstance = Vtiger_Block::getInstance('LBL_CALENDAR_SETTINGS', $module);
      if ($blockInstance) {
          $desField = Vtiger_Field::getInstance('defaulteventstatus', $module);
          if(!$desField) {
          $fieldInstance = new Vtiger_Field();
          $fieldInstance->name = 'defaulteventstatus';
          $fieldInstance->label = 'Default Event Status';
          $fieldInstance->uitype = 15;
          $fieldInstance->column = $fieldInstance->name;
          $fieldInstance->columntype = 'VARCHAR(50)';
          $fieldInstance->typeofdata = 'V~O';
          $blockInstance->addField($fieldInstance);
          $fieldInstance->setPicklistValues(Array('Planned','Held','Not Held'));
          }
          $datField = Vtiger_Field::getInstance('defaultactivitytype', $module);
          if(!$datField) {
          $fieldInstance1 = new Vtiger_Field();
          $fieldInstance1->name = 'defaultactivitytype';
          $fieldInstance1->label = 'Default Activity Type';
          $fieldInstance1->uitype = 15;
          $fieldInstance1->column = $fieldInstance1->name;
          $fieldInstance1->columntype = 'VARCHAR(50)';
          $fieldInstance1->typeofdata = 'V~O';
          $blockInstance->addField($fieldInstance1);
          $fieldInstance1->setPicklistValues(Array('Call','Meeting'));
          }
      }
  }
  echo 'Default status and activitytype field created';
//84 ends
  
//85 starts
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_account ALTER isconvertedfromlead SET DEFAULT ?', array('0'));
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_contactdetails ALTER isconvertedfromlead SET DEFAULT ?', array('0'));
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_potential ALTER isconvertedfromlead SET DEFAULT ?', array('0'));
Migration_Index_View::ExecuteQuery('Update vtiger_account SET isconvertedfromlead = ? where isconvertedfromlead is NULL',array('0'));
Migration_Index_View::ExecuteQuery('Update vtiger_contactdetails SET isconvertedfromlead = ? where isconvertedfromlead is NULL',array('0'));
Migration_Index_View::ExecuteQuery('Update vtiger_potential SET isconvertedfromlead = ? where isconvertedfromlead is NULL',array('0'));

//85 ends

//86 starts
//Duplicate of 85 script
//86 ends

//87 starts
$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('Haiti, Gourde'));
if(!$adb->num_rows($result)) {
                    Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
                    array($adb->getUniqueID('vtiger_currencies'), 'Haiti, Gourde', 'HTG', 'G'));
}
//87 ends   

//88 starts
Migration_Index_View::ExecuteQuery("UPDATE vtiger_currencies SET currency_symbol=? WHERE currency_code=?", array('₹','INR'));
Migration_Index_View::ExecuteQuery("UPDATE vtiger_currency_info SET currency_symbol=? WHERE currency_code=?", array('₹','INR'));

Migration_Index_View::ExecuteQuery('UPDATE vtiger_projecttaskstatus set presence = 0 where projecttaskstatus in (?,?,?,?,?)',
                    array('Open','In Progress','Completed','Deferred','Canceled'));
echo '<br> made projecttaskstatus picklist values as non editable';

//88 ends

// updating Emails module in sharing access rules
$EmailsTabId = getTabId('Emails');
$query = "SELECT tabid FROM vtiger_def_org_share";
$result = $adb->pquery($query, array());
$resultCount = $adb->num_rows($result);
$exist = false;
for($i=0; $i<$resultCount;$i++){
        $tabid = $adb->query_result($result,  $i,  'tabid');
        if($tabid == $EmailsTabId){
                $exist = true;
                echo 'Emails Sharing Access entry already exist';
                break;
        }
}

if(!$exist){
        $ruleid = $adb->getUniqueID('vtiger_def_org_share');
        $shareaccessquery = "INSERT INTO vtiger_def_org_share VALUES(?,?,?,?)";
        $result = Migration_Index_View::ExecuteQuery($shareaccessquery, array($ruleid, $EmailsTabId, 2, 0));
        echo 'Emails Sharing Access entry is added';
}
//90 ends

//92 starts
$result = $adb->pquery('SELECT max(templateid) AS maxtemplateid FROM vtiger_emailtemplates', array());
Migration_Index_View::ExecuteQuery('UPDATE vtiger_emailtemplates_seq SET id = ?', array(1 + ((int)$adb->query_result($result, 0, 'maxtemplateid'))));

 $result = $adb->pquery("SELECT 1 FROM vtiger_eventhandlers WHERE event_name=? AND handler_class=?",
                                    array('vtiger.entity.aftersave','Vtiger_RecordLabelUpdater_Handler'));
if($adb->num_rows($result) <= 0) {
    $lastMaxCRMId = 0;
    do {
        $rs = $adb->pquery("SELECT crmid,setype FROM vtiger_crmentity WHERE crmid > ? LIMIT 500", array($lastMaxCRMId));
        if (!$adb->num_rows($rs)) {
            break;
        }

        while ($row = $adb->fetch_array($rs)) {
            $imageType = stripos($row['setype'], 'image');
            $attachmentType = stripos($row['setype'], 'attachment');

            /**
             * TODO: Optimize underlying API to cache re-usable data, for speedy data.
             */
            if($attachmentType || $imageType) {
                $labelInfo = $row['setype'];
            } else {
                $labelInfo = getEntityName($row['setype'], array(intval($row['crmid'])));
            }

            if ($labelInfo) {
                $label = html_entity_decode($labelInfo[$row['crmid']],ENT_QUOTES);

                Migration_Index_View::ExecuteQuery('UPDATE vtiger_crmentity SET label=? WHERE crmid=? AND setype=?',
                            array($label, $row['crmid'], $row['setype']));
            }

            if (intval($row['crmid']) > $lastMaxCRMId) {
                $lastMaxCRMId = intval($row['crmid']);
            }
        }
        $rs = null;
        unset($rs);
    } while(true);

    $homeModule = Vtiger_Module::getInstance('Home');
    Vtiger_Event::register($homeModule, 'vtiger.entity.aftersave', 'Vtiger_RecordLabelUpdater_Handler', 'modules/Vtiger/handlers/RecordLabelUpdater.php');
                echo "Record Update Handler was updated successfully";
}
// To update the Campaign related status value in database as in language file
$updateQuery = "update vtiger_campaignrelstatus set campaignrelstatus=? where campaignrelstatus=?";
Migration_Index_View::ExecuteQuery($updateQuery,array('Contacted - Unsuccessful' , 'Contacted - Unsuccessful'));
echo 'Campaign related status value is updated';
//92 ends

//93 starts
//93 ends

//94 starts
$result = $adb->pquery('SELECT 1 FROM vtiger_currencies WHERE currency_name = ?', array('Libya, Dinar'));
if(!$adb->num_rows($result)) {
        Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_currencies (currencyid, currency_name, currency_code, currency_symbol) VALUES(?, ?, ?, ?)',
        array($adb->getUniqueID('vtiger_currencies'), 'Libya, Dinar', 'LYD', 'LYD'));
}

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
                $propertyValue = str_replace('$(general : (__VtigerMeta__) date)', "(general : (__VtigerMeta__) date) $dateFormat", $propertyValue);

                foreach ($dateFields as $fieldName) {
                        if ($taskModuleName === 'Events' && $fieldName === 'due_date') {
                                continue;
                        }
                        $propertyValue = str_replace("$$fieldName", "$$fieldName $dateFormat", $propertyValue);
                }

                foreach ($dateTimeFields as $fieldName) {
                        if ($taskModuleName === 'Calendar' && $fieldName === 'due_date') {
                                continue;
                        }
                        $propertyValue = str_replace("$$fieldName", "$$fieldName $timeZone", $propertyValue);
                }

                foreach ($dateFieldsList as $moduleName => $fieldNamesList) {
                        foreach ($fieldNamesList as $fieldName) {
                                $propertyValue = str_replace("($moduleName) $fieldName)", "($moduleName) $fieldName) $dateFormat", $propertyValue);
                        }
                }
                foreach ($dateTimeFieldsList as $moduleName => $fieldNamesList) {
                        foreach ($fieldNamesList as $fieldName) {
                                $propertyValue = str_replace("($moduleName) $fieldName)", "($moduleName) $fieldName) $timeZone", $propertyValue);
                        }
                }
                $emailTask->$propertyName = $propertyValue;
        }
        $tm->saveTask($emailTask);
}



global $root_directory;

// To update vtiger_modcomments table for permormance issue
$datatypeQuery = "ALTER TABLE vtiger_modcomments MODIFY COLUMN related_to int(19)";
$dtresult = Migration_Index_View::ExecuteQuery($datatypeQuery, array());
if($dtresult){
echo 'ModComments related_to field Datatype updated';
}else{
echo 'Failed to update Modcomments Datatype';
}
echo '</br>';
$indexQuery = "ALTER TABLE vtiger_modcomments ADD INDEX relatedto_idx (related_to)";
$indexResult = Migration_Index_View::ExecuteQuery($indexQuery, array());
if($indexResult){
echo 'Index added on ModComments';
}else{
echo 'Failed to add index on ModComments';
}
// End

$maxActionIdResult = $adb->pquery('SELECT MAX(actionid) AS maxid FROM vtiger_actionmapping', array());
$maxActionId = $adb->query_result($maxActionIdResult, 0, 'maxid');
Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_actionmapping(actionid, actionname, securitycheck) VALUES(?,?,?)', array($maxActionId+1 ,'Print', '0'));
echo "<br> added print to vtiger_actionnmapping";
$module = Vtiger_Module_Model::getInstance('Reports');
$module->enableTools(Array('Print', 'Export'));
echo "<br> enabled Print and export";

//94 ends

//95 starts
Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_webforms MODIFY COLUMN description TEXT',array());
require_once 'vtlib/Vtiger/Module.php';
$module = Vtiger_Module::getInstance('Users');
if ($module) {
    $blockInstance = Vtiger_Block::getInstance('LBL_CALENDAR_SETTINGS', $module);
    if ($blockInstance) {
        $hideCompletedField = Vtiger_Field::getInstance('hidecompletedevents', $module);
        if(!$hideCompletedField){
            $fieldInstance = new Vtiger_Field();
            $fieldInstance->name = 'hidecompletedevents';
            $fieldInstance->label = 'LBL_HIDE_COMPLETED_EVENTS';
            $fieldInstance->uitype = 56;
            $fieldInstance->column = $fieldInstance->name;
            $fieldInstance->columntype = 'INT';
            $fieldInstance->typeofdata = 'C~O';
            $fieldInstance->diplaytype = '1';
            $fieldInstance->defaultvalue = '0';
            $blockInstance->addField($fieldInstance);
            echo '<br>Hide/Show, completed/held, events/todo FIELD ADDED IN USERS';
        }
    }
}

$entityModulesModels = Vtiger_Module_Model::getEntityModules();
$modules = array();
if($entityModulesModels){
    foreach($entityModulesModels as $model){
       $modules[] =  $model->getName();
    }
}

foreach($modules as $module){
    $moduleInstance = Vtiger_Module::getInstance($module);
    if($moduleInstance){
        $result = Migration_Index_View::ExecuteQuery("select blocklabel from vtiger_blocks where tabid=? and sequence = ?", array($moduleInstance->id, 1));
        $block = $adb->query_result($result,0,'blocklabel');
        if($block){
            $blockInstance = Vtiger_Block::getInstance($block, $moduleInstance);
            $field = new Vtiger_Field();
            $field->name = 'created_user_id';
            $field->label = 'Created By';
            $field->table = 'vtiger_crmentity';
            $field->column = 'smcreatorid';
            $field->uitype = 52;
            $field->typeofdata = 'V~O';
            $field->displaytype= 2;
            $field->quickcreate = 3;
            $field->masseditable = 0;
            $blockInstance->addField($field);
            echo "Creator field added for $module";
            echo '<br>';
        }
    }else{
        echo "Unable to find $module instance";
        echo '<br>';
    }
}
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET presence=0 WHERE fieldname='unit_price' and columnname='unit_price'", array());
Migration_Index_View::ExecuteQuery("ALTER TABLE vtiger_portal ADD createdtime datetime", array());

$adb->query("CREATE TABLE IF NOT EXISTS vtiger_calendar_default_activitytypes (id INT(19), module VARCHAR(50), fieldname VARCHAR(50), defaultcolor VARCHAR(50));");

$result = Migration_Index_View::ExecuteQuery('SELECT * FROM vtiger_calendar_default_activitytypes', array());
if ($adb->num_rows($result) <= 0) {
        $calendarViewTypes = array('Events' => array('Events'=>'#17309A'),
                                                        'Calendar' => array('Tasks'=>'#3A87AD'),
                                                        'Potentials' => array('Potentials'=>'#AA6705'),
                                                        'Contacts' => array('support_end_date'=>'#953B39',
                                                                                                'birthday'=>'#545252'),
                                                        'Invoice' => array('Invoice'=>'#87865D'),
                                                        'Project' => array('Project'=>'#C71585'),
                                                        'ProjectTask' => array('Project Task'=>'#006400'),
                                                );

        foreach($calendarViewTypes as $module=>$viewInfo) {
                foreach($viewInfo as $fieldname=>$color) {
                        Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_calendar_default_activitytypes (id, module, fieldname, defaultcolor) VALUES (?,?,?,?)', array($adb->getUniqueID('vtiger_calendar_default_activitytypes'), $module, $fieldname, $color));
                }
        }
        echo '<br>Default Calendar view types added to the table.<br>';
}
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_calendar_user_activitytypes (id INT(19), defaultid INT(19), userid INT(19), color VARCHAR(50), visible INT(19) default 1);");

$result = Migration_Index_View::ExecuteQuery('SELECT * FROM vtiger_calendar_user_activitytypes', array());
if ($adb->num_rows($result) <= 0) {
    $queryResult = Migration_Index_View::ExecuteQuery('SELECT id, defaultcolor FROM vtiger_calendar_default_activitytypes', array());
    $numRows = $adb->num_rows($queryResult);
    for ($i = 0; $i < $numRows; $i++) {
            $row = $adb->query_result_rowdata($queryResult, $i);
            $activityIds[$row['id']] = $row['defaultcolor'];
    }

    $allUsers = Users_Record_Model::getAll(true);
    foreach($allUsers as $userId=>$userModel) {
            foreach($activityIds as $activityId=>$color) {
                   Migration_Index_View::ExecuteQuery('INSERT INTO vtiger_calendar_user_activitytypes (id, defaultid, userid, color) VALUES (?,?,?,?)', array($adb->getUniqueID('vtiger_calendar_user_activitytypes'), $activityId, $userId, $color));
            }
    }
    echo '<br>Default Calendar view types added to the table for all existing users';
}

Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET quickcreate = ? WHERE tabid = 8 AND (fieldname = ? OR fieldname = ?);", array(0,"filename","filelocationtype"));

//95 ends

//96 starts
    $entityModulesModels = Vtiger_Module_Model::getEntityModules();
    $fieldNameToDelete = 'created_user_id';
    if($entityModulesModels){
        foreach($entityModulesModels as $moduleInstance){
            if($moduleInstance){
                $module = $moduleInstance->name;
                $fieldInstance = Vtiger_Field::getInstance($fieldNameToDelete,$moduleInstance);
                if($fieldInstance){
                    $fieldInstance->delete();
                    echo "<br>";
                    echo "For $module created by is removed";
                }else{
                    echo "<br>";
                    echo "For $module created by is not there";
                }

            }else{
                echo "Unable to find $module instance";
                echo '<br>';
            }
        }
    }
//96 ends
    
//97 starts
    //delete modtracker detail view links
    Migration_Index_View::ExecuteQuery('DELETE FROM vtiger_links WHERE linktype = ? AND handler_class = ? AND linkurl like "javascript:ModTrackerCommon.showhistory%"',
                    array('DETAILVIEWBASIC', 'ModTracker'));

    //Added New field in mailmanager
    Migration_Index_View::ExecuteQuery('ALTER TABLE vtiger_mail_accounts ADD COLUMN sent_folder VARCHAR(50)', array());
    echo '<br>selected folder field added in mailmanager.<br>';
    
//97 ends
    
//Migrating PBXManager 5.4.0 to 6.x
if(!defined('INSTALLATION_MODE')) {
    $moduleInstance = Vtiger_Module_Model::getInstance('PBXManager');
    if(!$moduleInstance){ 
       echo '<br>Installing PBX Manager starts<br>'; 
       installVtlibModule('PBXManager', 'packages/vtiger/mandatory/PBXManager.zip'); 
    }else{ 
        $result = $adb->pquery('SELECT server, port FROM vtiger_asterisk', array());
        $server = $adb->query_result($result, 0, 'server');

        $qualifiedModuleName = 'PBXManager';
        $recordModel = Settings_PBXManager_Record_Model::getCleanInstance();
        $recordModel->set('gateway', $qualifiedModuleName);

        $connector = new PBXManager_PBXManager_Connector;
        foreach ($connector->getSettingsParameters() as $field => $type) {
            $fieldValue = "";
            if ($field == "webappurl") {
                $fieldValue = "http://" . $server . ":";
            }
            if ($field == "vtigersecretkey") {
                $fieldValue = uniqid(rand());
            }
            $recordModel->set($field, $fieldValue);
        }
        $recordModel->save();

        $modules = array('Contacts', 'Accounts', 'Leads');
        $recordModel = new PBXManager_Record_Model;

        foreach ($modules as $module) {
            $moduleInstance = CRMEntity::getInstance($module);

            $query = $moduleInstance->buildSearchQueryForFieldTypes(array('11'));
            $result = $adb->pquery($query, array());
            $rows = $adb->num_rows($result);

            for ($i = 0; $i < $rows; $i++) {
                $row = $adb->query_result_rowdata($result, $i);
                $crmid = $row['id'];
                
                foreach ($row as $name => $value) {
                    $values = array();
                    $values['crmid'] = $crmid;
                    $values['setype'] = $module;
                    
                    if ($name != 'name' && !empty($value) && $name != 'id' && !is_numeric($name)
                        && $name != 'firstname' && $name != 'lastname') {
                        $values[$name] = $value;
                        $recordModel->receivePhoneLookUpRecord($name, $values, true);
                    }
                }
            }
        }
            //Data migrate from old columns to new columns in vtiger_pbxmanager 
            $query = 'SELECT * FROM vtiger_pbxmanager';
            $result = $adb->pquery($query, array());
            $params = array();
            $rowCount = $adb->num_rows($result);
            for ($i = 0; $i < $rowCount; $i++) {
                $pbxmanagerid = $adb->query_result($result, $i, 'pbxmanagerid');
                $callfrom = $adb->query_result($result, $i, 'callfrom');
                $callto = $adb->query_result($result, $i, 'callto');
                $timeofcall = $adb->query_result($result, $i, 'timeofcall');
                $status = $adb->query_result($result, $i, 'status');
                $customer = PBXManager_Record_Model::lookUpRelatedWithNumber($callfrom);
                $userIdQuery = $adb->pquery('SELECT userid FROM vtiger_asteriskextensions WHERE asterisk_extension = ?', array($callto));
                $user = $adb->query_result($userIdQuery, $i, 'userid');
                if ($status == 'outgoing') {
                    $callstatus = 'outbound';
                } else if ($status == 'incoming') {
                    $callstatus = 'inbound';
                }
                //Update query 
                $adb->pquery('UPDATE vtiger_pbxmanager SET customer = ? AND user = ? AND totalduration = ? AND callstatus = ? WHERE pbxmanagerid = ?', array($customer, $user, $timeofcall, $callstatus, $pbxmanagerid));
            }

            //Adding PBXManager PostUpdate API's 
            //Add user extension field 

            $module = Vtiger_Module::getInstance('Users');
            if ($module) {
                $module->initTables();
                $blockInstance = Vtiger_Block::getInstance('LBL_MORE_INFORMATION', $module);
                if ($blockInstance) {
                    $fieldInstance = new Vtiger_Field();
                    $fieldInstance->name = 'phone_crm_extension';
                    $fieldInstance->label = 'CRM Phone Extension';
                    $fieldInstance->uitype = 11;
                    $fieldInstance->typeofdata = 'V~O';
                    $blockInstance->addField($fieldInstance);
                }
            }
            echo '<br>Added PBXManager User extension field.<br>';
            //Query to fetch asterisk extension 
            $extensionResult = $adb->pquery('SELECT userid, asterisk_extension FROM vtiger_asteriskextensions', array());
            for ($i = 0; $i < $adb->num_rows($extensionResult); $i++) {
                $userId = $adb->query_result($extensionResult, 0, 'userid');
                $extensionNumber = $adb->query_result($extensionResult, 0, 'asterisk_extension');
                $adb->pquery('UPDATE vtiger_users SET phone_crm_extension = ? WHERE id = ?', array($extensionNumber, $userId));
            }
            //Add PBXManager Links 

            $handlerInfo = array('path' => 'modules/PBXManager/PBXManager.php',
                'class' => 'PBXManager',
                'method' => 'checkLinkPermission');
            $headerScriptLinkType = 'HEADERSCRIPT';
            $incomingLinkLabel = 'Incoming Calls';
            Vtiger_Link::addLink(0, $headerScriptLinkType, $incominglinkLabel, 'modules/PBXManager/resources/PBXManagerJS.js', '', '', $handlerInfo);
            echo '<br>Added PBXManager links<br>';

            //Add settings links 

            $adb = PearDatabase::getInstance();
            $integrationBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_INTEGRATION'));
            $integrationBlockCount = $adb->num_rows($integrationBlock);

            // To add Block 
            if ($integrationBlockCount > 0) {
                $blockid = $adb->query_result($integrationBlock, 0, 'blockid');
            } else {
                $blockid = $adb->getUniqueID('vtiger_settings_blocks');
                $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks", array());
                if ($adb->num_rows($sequenceResult)) {
                    $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
                }
                $adb->pquery("INSERT INTO vtiger_settings_blocks(blockid, label, sequence) VALUES(?,?,?)", array($blockid, 'LBL_INTEGRATION', ++$sequence));
            }

            // To add a Field 
            $fieldid = $adb->getUniqueID('vtiger_settings_field');
            $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active) 
                        VALUES(?,?,?,?,?,?,?,?)", array($fieldid, $blockid, 'LBL_PBXMANAGER', '', 'PBXManager module Configuration', 'index.php?module=PBXManager&parent=Settings&view=Index', 2, 0));

            echo '<br>Added PBXManager settings links<br>';

            //Add module related dependencies 

            $pbxmanager = Vtiger_Module::getInstance('PBXManager');
            $dependentModules = array('Contacts', 'Leads', 'Accounts');
            foreach ($dependentModules as $module) {
                $moduleInstance = Vtiger_Module::getInstance($module);
                $moduleInstance->setRelatedList($pbxmanager, "PBXManager", array(), 'get_dependents_list');
            }

            echo '<br>Added PBXManager related list<br>';

            //Add action mapping 

            $adb = PearDatabase::getInstance();
            $module = new Vtiger_Module();
            $moduleInstance = $module->getInstance('PBXManager');

            //To add actionname as ReceiveIncomingcalls 
            $maxActionIdresult = $adb->pquery('SELECT max(actionid+1) AS actionid FROM vtiger_actionmapping', array());
            if ($adb->num_rows($maxActionIdresult)) {
                $actionId = $adb->query_result($maxActionIdresult, 0, 'actionid');
            }
            $adb->pquery('INSERT INTO vtiger_actionmapping 
                                 (actionid, actionname, securitycheck) VALUES(?,?,?)', array($actionId, 'ReceiveIncomingCalls', 0));
            $moduleInstance->enableTools('ReceiveIncomingcalls');

            //To add actionname as MakeOutgoingCalls 
            $maxActionIdresult = $adb->pquery('SELECT max(actionid+1) AS actionid FROM vtiger_actionmapping', array());
            if ($adb->num_rows($maxActionIdresult)) {
                $actionId = $adb->query_result($maxActionIdresult, 0, 'actionid');
            }
            $adb->pquery('INSERT INTO vtiger_actionmapping 
                                 (actionid, actionname, securitycheck) VALUES(?,?,?)', array($actionId, 'MakeOutgoingCalls', 0));
            $moduleInstance->enableTools('MakeOutgoingCalls');

            echo '<br>Added PBXManager action mapping<br>';

            //Add lookup events 

            $adb = PearDatabase::getInstance();
            $EventManager = new VTEventsManager($adb);
            $createEvent = 'vtiger.entity.aftersave';
            $deleteEVent = 'vtiger.entity.afterdelete';
            $restoreEvent = 'vtiger.entity.afterrestore';
            $batchSaveEvent = 'vtiger.batchevent.save';
            $batchDeleteEvent = 'vtiger.batchevent.delete';
            $handler_path = 'modules/PBXManager/PBXManagerHandler.php';
            $className = 'PBXManagerHandler';
            $batchEventClassName = 'PBXManagerBatchHandler';
            $EventManager->registerHandler($createEvent, $handler_path, $className, '', '["VTEntityDelta"]');
            $EventManager->registerHandler($deleteEVent, $handler_path, $className);
            $EventManager->registerHandler($restoreEvent, $handler_path, $className);
            $EventManager->registerHandler($batchSaveEvent, $handler_path, $batchEventClassName);
            $EventManager->registerHandler($batchDeleteEvent, $handler_path, $batchEventClassName);

            echo 'Added PBXManager lookup events';

            //Existing Asterisk extension block removed from vtiger_users if exist 
            $moduleInstance = Vtiger_Module_Model::getInstance('Users');
            $fieldInstance = $moduleInstance->getField('asterisk_extension');

            if (!empty($fieldInstance)) {
                $blockId = $fieldInstance->getBlockId();
                $fieldInstance->delete();
            }

            $fieldInstance = $moduleInstance->getField('use_asterisk');
            if (!empty($fieldInstance)) {
                $fieldInstance->delete();
            }
    }
}

//Hiding previous PBXManager fields. 
$tabId = getTabid('PBXManager');
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET presence=? WHERE tabid=? AND fieldname=?;", array(1, $tabId, "callfrom"));
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET presence=? WHERE tabid=? AND fieldname=?;", array(1, $tabId, "callto"));
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET presence=? WHERE tabid=? AND fieldname=?;", array(1, $tabId, "timeofcall"));
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET presence=? WHERE tabid=? AND fieldname=?;", array(1, $tabId, "status"));
echo '<br>Hiding previous PBXManager fields done.<br>'; 
//PBXManager porting ends.

//Making document module fields masseditable
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"notes_title")); 
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"assigned_user_id")); 
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"notecontent")); 
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"fileversion")); 
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"filestatus")); 
Migration_Index_View::ExecuteQuery("UPDATE vtiger_field SET masseditable = ? WHERE tabid = 8 AND fieldname = ?;", array(1,"folderid")); 

//Add Vat ID to Company Details 
Vtiger_Utils::AddColumn('vtiger_organizationdetails', 'vatid', 'VARCHAR(100)');

//Add Column trial for vtiger_tab table if not exists
$result = $adb->pquery("SHOW COLUMNS FROM vtiger_tab LIKE ?", array('trial'));
if (!($adb->num_rows($result))) {
    $adb->pquery("ALTER TABLE vtiger_tab ADD trial INT(1) NOT NULL DEFAULT 0",array());
}

##--http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7635--##
//Avoid premature deletion of activity related records
$moduleArray = array('Accounts', 'Leads', 'HelpDesk', 'Campaigns', 'Potentials', 'PurchaseOrder', 'SalesOrder', 'Quotes', 'Invoice');
$relatedToQuery = "SELECT fieldid FROM vtiger_field WHERE tabid=? AND fieldname=?";
$calendarInstance = Vtiger_Module::getInstance('Calendar');
$tabId = $calendarInstance->getId();
$result = $adb->pquery($relatedToQuery, array($tabId, 'parent_id'));
$fieldId = $adb->query_result($result,0, 'fieldid');
$insertQuery = "INSERT INTO vtiger_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES(?,?,?,?,?)";
$relModule = 'Calendar';
foreach ($moduleArray as $module) {
    $adb->pquery($insertQuery, array($fieldId, $module, $relModule, NULL, NULL));
}
//For contacts the fieldname is contact_id
$contactsRelatedToQuery = "SELECT fieldid FROM vtiger_field WHERE tabid=? AND fieldname=?";
$contactsResult = $adb->pquery($contactsRelatedToQuery, array($tabId, 'contact_id'));
$contactsFieldId = $adb->query_result($contactsResult,0, 'fieldid');
$insertContactsQuery = "INSERT INTO vtiger_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES(?,?,?,?,?)";
$module = 'Contacts';
$adb->pquery($insertContactsQuery, array($contactsFieldId, $module, $relModule, NULL, NULL));

##--http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/7635--##


//Adding is_owner to existing vtiger users

$usersModuleInstance = Vtiger_Module::getInstance('Users');
$usersBlockInstance = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $usersModuleInstance);

$usersFieldInstance = Vtiger_Field::getInstance('is_owner', $usersModuleInstance);
if (!$usersFieldInstance) {
    $field = new Vtiger_Field();
    $field->name = 'is_owner';
    $field->label = 'Account Owner';
    $field->column = 'is_owner';
    $field->table = 'vtiger_users';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->readonly = '0';
    $field->displaytype = '5';
    $field->masseditable = '0';
    $field->quickcreate = '0';
    $field->columntype = 'VARCHAR(5)';
    $field->defaultvalue = 0;
    $usersBlockInstance->addField($field);
    echo '<br> Added isOwner field in Users';
}

//Setting up is_owner for every admin user of CRM
$adb = PearDatabase::getInstance();
$idResult = $adb->pquery('SELECT id FROM vtiger_users WHERE is_admin = ? AND status=?', array('on', 'Active'));
if ($adb->num_rows($idResult) > 0) {
    for($i = 0;$i<=$adb->num_rows($idResult);$i++) {
        $userid = $adb->query_result($idResult, $i, 'id');
        $adb->pquery('UPDATE vtiger_users SET is_owner=? WHERE id=?', array(1, $userid));
        echo '<br>Account Owner Informnation saved in vtiger';
        //Recreate user prvileges
        createUserPrivilegesfile($userId);
        echo '<br>User previleges file recreated aftter adding is_owner field';
    } 
}else {
        echo '<br>Account Owner was not existed in this database';
    }
    
//Reports Chart Supported
Migration_Index_View::ExecuteQuery("CREATE TABLE IF NOT EXISTS vtiger_reporttype(
                        reportid INT(10),
                        data text,
						PRIMARY KEY (`reportid`),
						CONSTRAINT `fk_1_vtiger_reporttype` FOREIGN KEY (`reportid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE)
                        ENGINE=InnoDB DEFAULT CHARSET=utf8;", array()); 

//Configuration Editor fix
$sql = "UPDATE vtiger_settings_field SET name = ? WHERE name = ?";
Migration_Index_View::ExecuteQuery($sql,array('LBL_CONFIG_EDITOR', 'Configuration Editor'));