<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'modules/WSAPP/Handlers/vtigerCRMHandler.php';
require_once 'include/Webservices/Utils.php';

class Google_Vtiger_Handler extends vtigerCRMHandler {

	public function put($recordDetails, $user) {
		global $current_user;
		$current_user = $user;
		$this->user = $user;
		$recordDetails = $this->syncToNativeFormat($recordDetails);
		$createdRecords = $recordDetails['created'];
		$updatedRecords = $recordDetails['updated'];
		$deletedRecords = $recordDetails['deleted'];

		if (count($createdRecords) > 0) {
			$createdRecords = $this->translateReferenceFieldNamesToIds($createdRecords, $user);
			$createdRecords = $this->fillNonExistingMandatoryPicklistValues($createdRecords);
			$createdRecords = $this->fillMandatoryFields($createdRecords, $user);
		}
		foreach ($createdRecords as $index => $record) {
			try {
				$createdRecords[$index] = vtws_create($record['module'], $record, $this->user);
			} catch (Exception $ex) {
				unset($createdRecords[$index]);
				continue;
			}
		}

		if (count($updatedRecords) > 0) {
			$updatedRecords = $this->translateReferenceFieldNamesToIds($updatedRecords, $user);
		}

		$crmIds = array();
		foreach ($updatedRecords as $index => $record) {
			$webserviceRecordId = $record["id"];
			$recordIdComp = vtws_getIdComponents($webserviceRecordId);
			$crmIds[] = $recordIdComp[1];
		}
		$assignedRecordIds = array();
		if ($this->isClientUserSyncType()|| $this->isClientUserAndGroupSyncType()) {
			$assignedRecordIds = wsapp_checkIfRecordsAssignToUser($crmIds, $this->user->id);
			// To check if the record assigned to group
			if ($this->isClientUserAndGroupSyncType()) {
				$groupIds = $this->getGroupIds($this->user->id);
				foreach ($groupIds as $group) {
					$groupRecordId = wsapp_checkIfRecordsAssignToUser($crmIds, $group);
					$assignedRecordIds = array_merge($assignedRecordIds, $groupRecordId);
				}
			}
		}
		foreach ($updatedRecords as $index => $record) {
			$webserviceRecordId = $record["id"];
			//While Updating Record, should not update these values for event
			if ($record['module'] == 'Events') {
				unset($record['eventstatus'], $record['activitytype'], $record['duration_hours']);
			}
			$recordIdComp = vtws_getIdComponents($webserviceRecordId);
			try {
				if (in_array($recordIdComp[1], $assignedRecordIds)) {
					$updatedRecords[$index] = vtws_revise($record, $this->user);
				} elseif (!$this->isClientUserSyncType()) {
					$updatedRecords[$index] = vtws_revise($record, $this->user);
				} else {
					$this->assignToChangedRecords[$index] = $record;
				}
			} catch (Exception $e) {
				unset($updatedRecords[$index]);
				continue;
			}
		}
		$hasDeleteAccess = null;
		$deletedCrmIds = array();
		foreach ($deletedRecords as $index => $record) {
			$webserviceRecordId = $record;
			$recordIdComp = vtws_getIdComponents($webserviceRecordId);
			$deletedCrmIds[] = $recordIdComp[1];
		}
		$assignedDeletedRecordIds = wsapp_checkIfRecordsAssignToUser($deletedCrmIds, $this->user->id);

		// To get record id's assigned to group of the current user
		if ($this->isClientUserAndGroupSyncType()) {
			foreach ($groupIds as $group) {
				$groupRecordId = wsapp_checkIfRecordsAssignToUser($deletedCrmIds, $group);
				$assignedDeletedRecordIds = array_merge($assignedDeletedRecordIds, $groupRecordId);
			}
		}

		foreach ($deletedRecords as $index => $record) {
			$idComp = vtws_getIdComponents($record);
			if (empty($hasDeleteAccess)) {
				$handler = vtws_getModuleHandlerFromId($idComp[0], $this->user);
				$meta = $handler->getMeta();
				$hasDeleteAccess = $meta->hasDeleteAccess();
			}
			if ($hasDeleteAccess) {
				if (in_array($idComp[1], $assignedDeletedRecordIds)) {
					try {
						vtws_delete($record, $this->user);
					} catch (Exception $e) {
						unset($deletedRecords[$index]);
						continue;
					}
				}
			}
		}

		$recordDetails['created'] = $createdRecords;
		$recordDetails['updated'] = $updatedRecords;
		$recordDetails['deleted'] = $deletedRecords;
		return $this->nativeToSyncFormat($recordDetails);
	}
}