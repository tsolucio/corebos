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

class delSettingsNotifications extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$this->sendMsg('This changeset eliminates Notifications from Settings section');
			$this->sendMsg('From now on you must use the workflow system to accomplish these tasks');
			$this->sendMsg('The default notifications have been created as workflows for you, please review and adjust them to your needs.');
			$this->sendMsg('You can find some additional information on our documentation site.');
			// Send Reminder Notification
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Send Reminder Template' and module_name='Events'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw Send Reminder already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Events");
				$potentailsWorkFlow->test = '';
				$potentailsWorkFlow->description = "Send Reminder Template";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$MANUAL;
				$potentailsWorkFlow->defaultworkflow = 1;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$sql = 'select active,notificationsubject,notificationbody from vtiger_notificationscheduler where schedulednotificationid=8';
				$result_main = $adb->pquery($sql, array());
				$subject = '';
				$content = '';
				if ($result_main and $adb->num_rows($result_main)) {
					if ($adb->query_result($result_main, 0, 'active')==1) {
						$task->active = true;
					} else {
						$task->active = false;
					}
					$subject = $adb->query_result($result_main, 0, 'notificationsubject');
					$subject = getTranslatedString('Reminder','Calendar').getTranslatedString($result_set['activitytype'],'Calendar').' @ $date_start $time_start'.
						'] ($(general : (__VtigerMeta__) dbtimezone)) '.getTranslatedString($adb->query_result($result_main,0,'notificationsubject'),'Calendar');
					$content = nl2br(getTranslatedString($adb->query_result($result_main,0,'notificationbody'),'Calendar')) ."\n\n ".
						getTranslatedString('Subject','Calendar').' : $subject'."\n ".
						getTranslatedString('Date & Time','Calendar').' : $date_start $time_start&nbsp;($(general : (__VtigerMeta__) dbtimezone))'."\n\n ".
						getTranslatedString('Visit_Link','Calendar')." <a href='$(general : (__VtigerMeta__) crmdetailviewurl)'>".getTranslatedString('Click here','Calendar').'</a>';
				} else {
					$task->active = false;
				}
				if (empty($subject)) {
					$subject = getTranslatedString('Reminder','Calendar').getTranslatedString($result_set['activitytype'],'Calendar').' @ @$date_start $time_start'.
						'] ($(general : (__VtigerMeta__) dbtimezone)) ';
				}
				if (empty($content)) {
					$content = '<p>This is a reminder notification for the Activity<br />'."\n\n<br /> ".
						getTranslatedString('Subject','Calendar').' : $subject'."\n ".
						getTranslatedString('Date & Time','Calendar').' : $date_start $time_start&nbsp;($(general : (__VtigerMeta__) dbtimezone))'."\n\n ".
						getTranslatedString('Visit_Link','Calendar')." <a href='$(general : (__VtigerMeta__) crmdetailviewurl)'>".getTranslatedString('Click here','Calendar').'</a></p>';
				}
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Send Reminder Template';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$(assigned_user_id : (Users) email1),$(general : (__VtigerMeta__) Events_Users_Invited)';
				$task->emailcc ='';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 8');
			// Notify when a task is delayed beyond 24 hrs
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Notify when a task is delayed beyond 24 hrs' and module_name='Calendar'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw 24hrs already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Calendar");
				$potentailsWorkFlow->test = '[{"fieldname":"taskstatus","operation":"is not","value":"Completed","valuetype":"rawtext","joincondition":"and","groupid":"0"},{"fieldname":"date_start","operation":"less than days ago","value":"1","valuetype":"expression","joincondition":"and","groupid":"0"}]';
				$potentailsWorkFlow->description = "Notify when a task is delayed beyond 24 hrs";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
				$potentailsWorkFlow->defaultworkflow = 0;
				$potentailsWorkFlow->schtypeid = 2;
				$potentailsWorkFlow->schtime = '02:15:00';
				$potentailsWorkFlow->schdayofmonth = null;
				$potentailsWorkFlow->schdayofweek = '';
				$potentailsWorkFlow->schannualdates = '';
				$potentailsWorkFlow->nexttrigger_time = null;
				$potentailsWorkFlow->schminuteinterval = 5;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$sql = 'select active,notificationsubject,notificationbody from vtiger_notificationscheduler where schedulednotificationid=1';
				$result_main = $adb->pquery($sql, array());
				if ($result_main and $adb->num_rows($result_main)) {
					if ($adb->query_result($result_main, 0, 'active')==1) {
						$task->active = true;
					} else {
						$task->active = false;
					}
				} else {
					$task->active = false;
				}
				$subject = getTranslatedString('Task_Not_completed','Calendar').' : $subject';
				$content = getTranslatedString('Dear_Admin_tasks_not_been_completed','Calendar')." ".getTranslatedString('LBL_SUBJECT','Calendar').
					": $subject<br> ".getTranslatedString('LBL_ASSIGNED_TO','Calendar').": $(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name)<br><br>".
					getTranslatedString('Task_sign','Calendar');
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Delayed Task Notification';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$(assigned_user_id : (Users) email1)';
				$task->emailcc ='';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 1');
			// Big deal notification
			// I directly eliminate this, it doesn't make sense and it is easy to do as a workflow: sales_stage='Closed Won' and amount > 10000
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 2');
			// Pending Tickets Notification
			// I directly eliminate this, it is more  powerful to create a workflow adapted to your specific needs
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 3');
			// Too many tickets Notification
			// I directly eliminate this, there is no way to do this in the application
			// Maybe some day we will be able to do it as a scheduled report
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 4');
			// Product Support Start Notification
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Product Support Starting' and module_name='Products'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw Product support starts already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Products");
				$potentailsWorkFlow->test = '[{"fieldname":"start_date","operation":"is","value":"get_date(\'today\')","valuetype":"expression","joincondition":"and","groupid":"0"}]';
				$potentailsWorkFlow->description = "Product Support Starting";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
				$potentailsWorkFlow->defaultworkflow = 0;
				$potentailsWorkFlow->schtypeid = 2;
				$potentailsWorkFlow->schtime = '01:15:00';
				$potentailsWorkFlow->schdayofmonth = null;
				$potentailsWorkFlow->schdayofweek = '';
				$potentailsWorkFlow->schannualdates = '';
				$potentailsWorkFlow->nexttrigger_time = null;
				$potentailsWorkFlow->schminuteinterval = 5;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$sql = 'select active,notificationsubject,notificationbody from vtiger_notificationscheduler where schedulednotificationid=5';
				$result_main = $adb->pquery($sql, array());
				if ($result_main and $adb->num_rows($result_main)) {
					if ($adb->query_result($result_main, 0, 'active')==1) {
						$task->active = true;
					} else {
						$task->active = false;
					}
				} else {
					$task->active = false;
				}
				$subject = getTranslatedString('Support_starting','Products');
				$content = getTranslatedString('Hello_Support','Products').' $productname'."\n ".getTranslatedString('Congratulations');
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Product Support Starting';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$(assigned_user_id : (Users) email1)';
				$task->emailcc ='';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			// Product Support End Notification
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Product Support Ended' and module_name='Products'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw Product support Ended already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Products");
				$potentailsWorkFlow->test = '[{"fieldname":"expiry_date","operation":"is","value":"get_date(\'today\')","valuetype":"expression","joincondition":"and","groupid":"0"}]';
				$potentailsWorkFlow->description = "Product Support Ended";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
				$potentailsWorkFlow->defaultworkflow = 0;
				$potentailsWorkFlow->schtypeid = 2;
				$potentailsWorkFlow->schtime = '01:25:00';
				$potentailsWorkFlow->schdayofmonth = null;
				$potentailsWorkFlow->schdayofweek = '';
				$potentailsWorkFlow->schannualdates = '';
				$potentailsWorkFlow->nexttrigger_time = null;
				$potentailsWorkFlow->schminuteinterval = 5;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$sql = 'select active,notificationsubject,notificationbody from vtiger_notificationscheduler where schedulednotificationid=5';
				$result_main = $adb->pquery($sql, array());
				if ($result_main and $adb->num_rows($result_main)) {
					if ($adb->query_result($result_main, 0, 'active')==1) {
						$task->active = true;
					} else {
						$task->active = false;
					}
				} else {
					$task->active = false;
				}
				$subject = getTranslatedString('Support_Ending_Subject','Products');
				$content = getTranslatedString('Support_Ending_Content','Products').' $productname'."\n ".getTranslatedString('kindly_renew');
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Product Support Ended';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$(assigned_user_id : (Users) email1)';
				$task->emailcc ='';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 5');
			// Client End Support Notification 1 month
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Client End Support Notification 1 month' and module_name='Contacts'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw Client End Support Notification 1 month already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Contacts");
				$potentailsWorkFlow->test = '[{"fieldname":"support_end_date","operation":"is","value":"add_days(get_date(\'today\'), 30)","valuetype":"expression","joincondition":"and","groupid":"0"}]';
				$potentailsWorkFlow->description = "Client End Support Notification 1 month";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
				$potentailsWorkFlow->defaultworkflow = 0;
				$potentailsWorkFlow->schtypeid = 2;
				$potentailsWorkFlow->schtime = '05:15:00';
				$potentailsWorkFlow->schdayofmonth = null;
				$potentailsWorkFlow->schdayofweek = '';
				$potentailsWorkFlow->schannualdates = '';
				$potentailsWorkFlow->nexttrigger_time = null;
				$potentailsWorkFlow->schminuteinterval = 5;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$task->active = false; // I leave this one deactivated, if it is needed the user must activate it
				$subject = 'End of Support Notification';
				$content = 'Dear $firstname $lastname,<br>This is just a notification mail regarding your support end.<br />
					<span style="font-weight: bold;">Priority:</span> Normal<br />
					Your Support is going to expire next month.<br />
					Please contact support.';
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Client End Support Notification 1 month';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$email';
				$task->emailcc ='$(assigned_user_id : (Users) email1)';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 6');
			// Client End Support Notification week
			$wfrs = $adb->query("SELECT workflow_id FROM com_vtiger_workflows WHERE summary='Client End Support Notification 1 week' and module_name='Contacts'");
			if ($wfrs and $adb->num_rows($wfrs)==1) {
				$this->sendMsg('Workfolw Client End Support Notification 1 week already exists!');
			} else {
				$workflowManager = new VTWorkflowManager($adb);
				$taskManager = new VTTaskManager($adb);
				
				$potentailsWorkFlow = $workflowManager->newWorkFlow("Contacts");
				$potentailsWorkFlow->test = '[{"fieldname":"support_end_date","operation":"is","value":"add_days(get_date(\'today\'), 7)","valuetype":"expression","joincondition":"and","groupid":"0"}]';
				$potentailsWorkFlow->description = "Client End Support Notification 1 week";
				$potentailsWorkFlow->executionCondition = VTWorkflowManager::$ON_SCHEDULE;
				$potentailsWorkFlow->defaultworkflow = 0;
				$potentailsWorkFlow->schtypeid = 2;
				$potentailsWorkFlow->schtime = '05:25:00';
				$potentailsWorkFlow->schdayofmonth = null;
				$potentailsWorkFlow->schdayofweek = '';
				$potentailsWorkFlow->schannualdates = '';
				$potentailsWorkFlow->nexttrigger_time = null;
				$potentailsWorkFlow->schminuteinterval = 5;
				$workflowManager->save($potentailsWorkFlow);
				
				$task = $taskManager->createTask('VTEmailTask', $potentailsWorkFlow->id);
				$task->active = false; // I leave this one deactivated, if it is needed the user must activate it
				$subject = 'End of Support Notification';
				$content = 'Dear $firstname $lastname,<br>This is just a notification mail regarding your support end.<br />
					<span style="font-weight: bold;">Priority:</span> Urgent<br />
					Your Support is going to expire next week.<br />
					Please contact support.';
				$task->subject = $subject;
				$task->content = $content;
				$task->summary = 'Client End Support Notification 1 week';
				$task->fromname = '';
				$task->fromemail = '';
				$task->replyto = '';
				$task->recepient = '$email';
				$task->emailcc ='$(assigned_user_id : (Users) email1)';
				$task->emailbcc = '';
				$taskManager->saveTask($task);
			}
			$this->ExecuteQuery('DELETE FROM vtiger_notificationscheduler WHERE schedulednotificationid = 7');
			// Delete the table if it is empty
			$nrs = $adb->query('select count(*) from vtiger_notificationscheduler');
			if ($adb->query_result($nrs, 0, 0)==0) {
				$this->ExecuteQuery('DROP TABLE vtiger_notificationscheduler');
				$this->ExecuteQuery('DROP TABLE vtiger_notificationscheduler_seq');
				$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('NOTIFICATIONSCHEDULERS'));
			}
			// Inventory Notifications
			// I simply delete all these as they can be done using workflows against InventoryDetails module now that we have total stock
			$this->ExecuteQuery('DELETE FROM vtiger_inventorynotification WHERE notificationid = 1');
			$this->ExecuteQuery('DELETE FROM vtiger_inventorynotification WHERE notificationid = 2');
			$this->ExecuteQuery('DELETE FROM vtiger_inventorynotification WHERE notificationid = 3');
			$modname = 'InventoryDetails';
			$module = Vtiger_Module::getInstance($modname);
			$block = Vtiger_Block::getInstance('LBL_INVENTORYDETAILS_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('total_stock',$module);
			if (!$field) {
				$field1 = new Vtiger_Field();
				$field1->name = 'total_stock';
				$field1->label= 'Total Stock';
				$field1->column = 'total_stock';
				$field1->columntype = 'DECIMAL(28,6)';
				$field1->uitype = 7;
				$field1->typeofdata = 'N~O';
				$field1->displaytype = 2;
				$field1->presence = 0;
				$block->addField($field1);
			}
			// Delete the table if it is empty
			$nrs = $adb->query('select count(*) from vtiger_inventorynotification');
			if ($adb->query_result($nrs, 0, 0)==0) {
				$this->ExecuteQuery('DROP TABLE vtiger_inventorynotification');
				$this->ExecuteQuery('DROP TABLE vtiger_inventorynotification_seq');
				$this->ExecuteQuery('DELETE FROM vtiger_settings_field WHERE vtiger_settings_field.name = ?', array('INVENTORYNOTIFICATION'));
			}
			// eliminate this email template which is not used
			$this->ExecuteQuery('DELETE FROM vtiger_emailtemplates WHERE templatename = ?', array('Announcement for Release'));
			// eliminate this table which is not used anymore
			$nrs = $adb->query('select count(*) from vtiger_salesmanticketrel');
			if ($adb->query_result($nrs, 0, 0)==0) {
				$this->ExecuteQuery('DROP TABLE vtiger_salesmanticketrel'); // this table is no longer used
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

}
