<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
********************************************************************************/
require_once 'include/utils/GetGroupUsers.php';
require_once 'include/utils/utils.php';
require_once 'modules/Calendar4You/GoogleSync4You.php';

class GoogleSync4YouHandler extends VTEventHandler {
	public function handleEvent($handlerType, $entityData) {
		global $adb, $current_user;

		if ($handlerType == 'vtiger.entity.aftersave' && isset($_REQUEST['geventid']) && $_REQUEST['geventid'] != '') {
			$Res = $adb->pquery('select activitytypeid from vtiger_activitytype where activitytype = ?', array($_REQUEST['gevent_type']));
			$event = $adb->query_result($Res, 0, 'activitytypeid');
			$geventid = vtlib_purify($_REQUEST['geventid']);
			$id = $entityData->getId();
			$p = array($id, $geventid, $current_user->id, $event);
			$result1 = $adb->pquery('SELECT * FROM its4you_googlesync4you_events WHERE crmid = ? AND geventid = ? AND userid = ? AND eventtype = ?', $p);
			$num_rows1 = $adb->num_rows($result1);
			if ($num_rows1 == 0) {
				$adb->pquery('INSERT INTO its4you_googlesync4you_events (crmid,geventid,userid,eventtype) VALUES (?,?,?,?)', $p);
			}
		}

		if ($handlerType == 'vtiger.entity.aftersave' || $handlerType == 'vtiger.entity.beforedelete') {
			$moduleName = $entityData->getModuleName();
			if ($moduleName == 'Calendar' || $moduleName == 'Events' || $moduleName == 'cbCalendar') {
				$InGCalendars = array();
				$id = $entityData->getId();
				//$Data = $entityData->getData();
				$ev=CRMEntity::getInstance("Calendar");
				$ev->id=$id;
				$ev->mode='edit';
				$ev->retrieve_entity_info($id, "Events");
				$Data=$ev->column_fields;
				$result1 = $adb->pquery('SELECT userid, geventid, eventtype FROM its4you_googlesync4you_events WHERE crmid = ?', array($id));
				$num_rows1 = $adb->num_rows($result1);
				if ($num_rows1 > 0) {
					while ($row = $adb->fetchByAssoc($result1)) {
						$InGCalendars[$row['eventtype']][$row['userid']] = $row['geventid'];
					}
				}
				if ($handlerType == 'vtiger.entity.aftersave') {
					if ($moduleName == 'Calendar') {
						$event = 'task';
					} else {
						$Res = $adb->pquery('select activitytypeid from vtiger_activitytype where activitytype = ?', array($Data['activitytype']));
						$event = $adb->query_result($Res, 0, 'activitytypeid');
					}
					$assigned_user_id = $entityData->get('assigned_user_id');
					$user_name = getUserName($assigned_user_id);
					if ($user_name != '') {
						$this->AddIntoCalendar($id, $assigned_user_id, $event, $Data);
						unset($InGCalendars[$event][$assigned_user_id]);
					} else {
						$userGroups = new GetGroupUsers();
						$userGroups->getAllUsersInGroup($assigned_user_id);
						foreach ($userGroups->group_users as $to_email_id) {
							$this->AddIntoCalendar($id, $to_email_id, $event, $Data);
							unset($InGCalendars[$event][$to_email_id]);
						}
					}
				}
				if (count($InGCalendars) > 0) {
					while (list($event, $Events) = each($InGCalendars)) {
						foreach ($Events as $userid => $eventURL) {
							$this->DeleteGCalendarEvent($id, $userid, $eventURL, $event);
						}
					}
				}
			}
		}
	}

	public function AddIntoCalendar($id, $save_user_id, $event, $Data) {
		try {
			if ($GoogleSync4You = $this->getGoogleSyncClass($save_user_id, $event)) {
				$GoogleSync4You->saveEvent($id, $event, $Data);
			}
		} catch (Exception $e) {
		}
	}

	public function DeleteGCalendarEvent($id, $save_user_id, $eventURL, $event) {
		global $adb;
		try {
			if ($GoogleSync4You = $this->getGoogleSyncClass($save_user_id, $event, 'skip')) {
				$GoogleSync4You->deleteEvent($id, $eventURL);
				$adb->pquery('DELETE FROM its4you_googlesync4you_events WHERE crmid =? AND userid = ? AND eventtype = ?', array($id, $save_user_id, $event));
			}
		} catch (Exception $e) {
		}
	}

	public function getGoogleSyncClass($save_user_id, $event, $selected_calendar = '') {
		$GoogleSync4You = new GoogleSync4You();
		$have_access_data = $GoogleSync4You->setAccessDataForUser($save_user_id, true);
		if ($have_access_data) {
			$GoogleSync4You->connectToGoogle();
			if ($GoogleSync4You->is_logged) {
				$GoogleSync4You->setEvent($event);
				if ($selected_calendar == '') {
					$selected_calendar = $GoogleSync4You->getSCalendar(1);
				}
				$is_disabled = $GoogleSync4You->isDisabled(1); //export
				if (!$is_disabled && $selected_calendar != '') {
					return $GoogleSync4You;
				}
			}
		}
		return false;
	}
}
?>