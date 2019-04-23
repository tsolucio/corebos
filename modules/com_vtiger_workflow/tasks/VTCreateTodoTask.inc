<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/Webservices/Utils.php';
require_once "include/Webservices/VtigerCRMObject.php";
require_once "include/Webservices/VtigerCRMObjectMeta.php";
require_once "include/Webservices/DataTransform.php";
require_once "include/Webservices/WebServiceError.php";
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';

require_once "modules/Users/Users.php";

class VTCreateTodoTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('todo', 'description', 'sendNotification', 'time', 'date', 'status', 'priority', 'days', 'direction', 'datefield', 'sendNotification', 'assigned_user_id');
	}

	public function getAdmin() {
		$user = Users::getActiveAdminUser();
		global $current_user;
		$this->originalUser = $current_user;
		$current_user = $user;
		return $user;
	}

	public function doTask(&$entityData) {
		if (!vtlib_isModuleActive('Calendar')) {
			return;
		}
		global $adb, $current_user, $logbg, $from_wf;
		$from_wf = true;
		$logbg->info('Entering workflow task VTCreateTodoTask');
		$userId = $entityData->get('assigned_user_id');
		if ($userId===null) {
			$userId = vtws_getWebserviceEntityId('Users', 1);
		}

		$baseDate = $entityData->get($this->datefield);
		$logbg->debug('baseDate: '.$this->datefield.' > '.$baseDate);
		if ($baseDate == '') {
			$baseDate = date('Y-m-d');
		}
		$time = explode(' ', $baseDate);
		if (count($time) < 2) {
			$time[] = date('H:i');
		}
		preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
		$baseDate = strtotime($match[0]);
		$date = strftime('%Y-%m-%d', $baseDate+$this->days*24*60*60*(strtolower($this->direction)=='before'?-1:1));
		$startDate = new DateTimeField($date.' '.$time[1]);
		$date = $startDate->getDisplayDate();
		// Added to check if the user/group is active
		if (!empty($this->assigned_user_id)) {
			$userExists = $adb->pquery('SELECT 1 FROM vtiger_users WHERE id = ? AND status = ?', array($this->assigned_user_id, 'Active'));
			if ($adb->num_rows($userExists)) {
				$assignedUserId = vtws_getWebserviceEntityId('Users', $this->assigned_user_id);
				$userId = $assignedUserId;
			} else {
				$groupExist = $adb->pquery('SELECT 1 FROM vtiger_groups WHERE groupid = ?', array($this->assigned_user_id));
				if ($adb->num_rows($groupExist)) {
					$assignedGroupId = vtws_getWebserviceEntityId('Groups', $this->assigned_user_id);
					$userId = $assignedGroupId;
				} else {
					if ($this->assigned_user_id == 'copyParentOwner') {
						$userId = $entityData->get('assigned_user_id');
					}
				}
			}
		}
		$fields = array(
			'activitytype'=>'Task',
			'description'=>$this->description,
			'subject'=>$this->todo,
			'taskpriority'=>$this->priority,
			'taskstatus'=>$this->status,
			'assigned_user_id'=>$userId,
			'time_start'=> self::conv12to24hour($this->time),
			'sendnotification'=>($this->sendNotification!='' && $this->sendNotification!='N')? true: false,
			'date_start'=>$date,
			'due_date'=>$date,
			'visibility'=>'all',
			'eventstatus'=>''
		);
		$moduleName = $entityData->getModuleName();
		$adminUser = $this->getAdmin();
		$logbg->debug('User: '.$adminUser->id);
		$id = $entityData->getId();
		if ($moduleName=='Contacts') {
			$fields['contact_id'] = $id;
		} else {
			$data = vtws_describe('Calendar', $adminUser);
			$fieldInfo = $data['fields'];
			foreach ($fieldInfo as $field) {
				if ($field['name']=='parent_id') {
					$parentIdField = $field;
				}
			}
			$refersTo = $parentIdField['type']['refersTo'];

			if (in_array($moduleName, $refersTo)) {
				$fields['parent_id'] = $id;
			}
		}
		$logbg->debug('Create TODO: '.print_r($fields, true));
		$todo = vtws_create('Calendar', $fields, $adminUser);
		$logbg->debug('Created TODO: '.$todo['id']);
		$todoIdDetails = vtws_getIdComponents($todo['id']);
		$entityIdDetails = vtws_getIdComponents($id);

		relateEntities(CRMEntity::getInstance($moduleName), $moduleName, $entityIdDetails[1], 'Calendar', $todoIdDetails[1]);

		$from_wf = false;
		global $current_user;
		$current_user = $this->originalUser;
		$logbg->info('Exiting workflow task VTCreateTodoTask');
	}

	public static function conv12to24hour($timeStr) {
		$arr = array();
		preg_match('/(\d{1,2}):(\d{1,2})(am|pm)/', $timeStr, $arr);
		if ($arr[3]=='am') {
			$hours = ((int)$arr[1]) % 12;
		} else {
			$hours = ((int)$arr[1]) % 12 + 12;
		}
		return str_pad($hours, 2, '0', STR_PAD_LEFT).':'.str_pad($arr[2], 2, '0', STR_PAD_LEFT);
	}

	public function getTimeFieldList() {
		return array('time');
	}
}
?>
