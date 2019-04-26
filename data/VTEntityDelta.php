<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/events/VTEntityData.inc';

class VTEntityDelta extends VTEventHandler {
	private static $oldEntity;
	private static $newEntity;
	private static $oldEntity_pimages = array();
	private static $newEntity_pimages = array();
	private static $entityDelta;

	public function __construct() {
	}

	public function handleEvent($eventName, $entityData) {
		$adb = PearDatabase::getInstance();
		$moduleName = $entityData->getModuleName();
		$recordId = $entityData->getId();

		if ($eventName == 'vtiger.entity.beforesave') {
			if (!empty($recordId)) {
				$entityData = VTEntityData::fromEntityId($adb, $recordId);
				if ($moduleName == 'HelpDesk') {
					$entityData->set('comments', getTicketComments($recordId));
				}
				self::$oldEntity[$moduleName][$recordId] = $entityData;
				if ($moduleName=='Products') {
					self::$oldEntity_pimages = array();
					$sql = 'SELECT vtiger_attachments.`attachmentsid`,name FROM `vtiger_seattachmentsrel`
						inner join vtiger_attachments on vtiger_attachments.`attachmentsid` = `vtiger_seattachmentsrel`.`attachmentsid`
						WHERE `crmid`=?';
					$imagesrs = $adb->pquery($sql, array($recordId));
					while ($image = $adb->fetch_array($imagesrs)) {
						self::$oldEntity_pimages[$image['attachmentsid']] = $image['name'];
					}
				}
			}
		}

		if ($eventName == 'vtiger.entity.aftersave') {
			$this->fetchEntity($moduleName, $recordId);
			if ($moduleName=='Products') {
				self::$newEntity_pimages = array();
				$sql = 'SELECT vtiger_attachments.`attachmentsid`,name FROM `vtiger_seattachmentsrel`
					inner join vtiger_attachments on vtiger_attachments.`attachmentsid` = `vtiger_seattachmentsrel`.`attachmentsid`
					WHERE `crmid`=?';
				$imagesrs = $adb->pquery($sql, array($recordId));
				while ($image = $adb->fetch_array($imagesrs)) {
					self::$newEntity_pimages[$image['attachmentsid']] = $image['name'];
				}
			}
			$this->computeDelta($moduleName, $recordId);
		}
	}

	public function fetchEntity($moduleName, $recordId) {
		$adb = PearDatabase::getInstance();
		$entityData = VTEntityData::fromEntityId($adb, $recordId);
		if ($moduleName == 'HelpDesk') {
			$entityData->set('comments', getTicketComments($recordId));
		}
		self::$newEntity[$moduleName][$recordId] = $entityData;
	}

	public function computeDelta($moduleName, $recordId) {
		$delta = array();

		$oldData = array();
		if (!empty(self::$oldEntity[$moduleName][$recordId])) {
			$oldEntity = self::$oldEntity[$moduleName][$recordId];
			$oldData = $oldEntity->getData();
		}
		$newEntity = self::$newEntity[$moduleName][$recordId];
		$newData = $newEntity->getData();
		/** Detect field value changes **/
		foreach ($newData as $fieldName => $fieldValue) {
			$isModified = false;
			if (empty($oldData[$fieldName])) {
				if (!empty($newData[$fieldName])) {
					$isModified = true;
				}
			} elseif ($oldData[$fieldName] != $newData[$fieldName]) {
				$isModified = true;
			}
			if ($isModified) {
				$delta[$fieldName] = array(
					'oldValue' => isset($oldData[$fieldName]) ? $oldData[$fieldName] : '',
					'currentValue' => $newData[$fieldName]);
			}
		}
		if ($moduleName=='Products') {
			$new = array_diff(self::$newEntity_pimages, self::$oldEntity_pimages);
			foreach ($new as $key => $value) {
				$delta['deltaimage'.$key] = array(
					'oldValue' => '',
					'currentValue' => $value
				);
			}
			$old = array_diff(self::$oldEntity_pimages, self::$newEntity_pimages);
			foreach ($old as $key => $value) {
				$delta['deltaimage'.$key] = array(
					'oldValue' => $value,
					'currentValue' => getTranslatedString('LBL_DELETED', 'ModTracker')
				);
			}
		}
		self::$entityDelta[$moduleName][$recordId] = $delta;
	}

	public function getEntityDelta($moduleName, $recordId, $forceFetch = false) {
		if ($forceFetch) {
			$this->fetchEntity($moduleName, $recordId);
			$this->computeDelta($moduleName, $recordId);
		}
		return self::$entityDelta[$moduleName][$recordId];
	}

	public function getOldValue($moduleName, $recordId, $fieldName) {
		$entityDelta = self::$entityDelta[$moduleName][$recordId];
		return (isset($entityDelta[$fieldName]) ? $entityDelta[$fieldName]['oldValue'] : '');
	}

	public function getOldEntityValue($moduleName, $recordId, $fieldName) {
		$oldData = array();
		if (!empty(self::$oldEntity[$moduleName][$recordId])) {
			$oldEntity = self::$oldEntity[$moduleName][$recordId];
			$oldData = $oldEntity->getData();
		}
		return (isset($oldData[$fieldName]) ? $oldData[$fieldName] : '');
	}

	public function getCurrentValue($moduleName, $recordId, $fieldName) {
		$entityDelta = self::$entityDelta[$moduleName][$recordId];
		return $entityDelta[$fieldName]['currentValue'];
	}

	public function getOldEntity($moduleName, $recordId) {
		return (isset(self::$oldEntity[$moduleName]) ? self::$oldEntity[$moduleName][$recordId] : '');
	}

	public function getNewEntity($moduleName, $recordId) {
		return self::$newEntity[$moduleName][$recordId];
	}

	public function hasChanged($moduleName, $recordId, $fieldName, $fieldValue = null) {
		if (empty(self::$oldEntity[$moduleName][$recordId])) {
			return false;
		}
		@$fieldDelta = self::$entityDelta[$moduleName][$recordId][$fieldName];  // we know this will be empty sometimes, so we ignore the error
		$result = $fieldDelta['oldValue'] != $fieldDelta['currentValue'];
		if ($fieldValue !== null) {
			$result = $result && ($fieldDelta['currentValue'] === $fieldValue);
		}
		return $result;
	}
}
?>