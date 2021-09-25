<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Import_Queue_Controller {

	public static $IMPORT_STATUS_NONE = 0;
	public static $IMPORT_STATUS_SCHEDULED = 1;
	public static $IMPORT_STATUS_RUNNING = 2;
	public static $IMPORT_STATUS_HALTED = 3;
	public static $IMPORT_STATUS_COMPLETED = 4;

	public static function add($userInputObject, $user) {
		$adb = PearDatabase::getInstance();

		if (!Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			Vtiger_Utils::CreateTable(
				'vtiger_import_queue',
				"(importid INT NOT NULL PRIMARY KEY,
					userid INT NOT NULL,
					tabid INT NOT NULL,
					field_mapping TEXT,
					default_values TEXT,
					merge_type INT,
					merge_fields TEXT,
					status INT default 0,
					importmergecondition INT default 0,
					skipcreate INT default 0)",
				true
			);
		}

		if ($userInputObject->get('is_scheduled')) {
			$status = self::$IMPORT_STATUS_SCHEDULED;
		} else {
			$status = self::$IMPORT_STATUS_NONE;
		}
		$skipCreate = $userInputObject->get('skipcreate');
		$adb->pquery(
			'INSERT INTO vtiger_import_queue (importid,userid,tabid,field_mapping,default_values,merge_type,merge_fields,`status`,importmergecondition,skipcreate)
				VALUES (?,?,?,?,?,?,?,?,?,?)',
			array(
				$adb->getUniqueID('vtiger_import_queue'),
				$user->id,
				getTabid($userInputObject->get('module')),
				json_encode($userInputObject->get('field_mapping')),
				json_encode($userInputObject->get('default_values')),
				$userInputObject->get('merge_type'),
				json_encode($userInputObject->get('merge_fields')),
				$status,
				empty($userInputObject->get('importmergecondition')) ? 0 : $userInputObject->get('importmergecondition'),
				empty($skipCreate) ? 0 : ($skipCreate=='on' || $skipCreate=='1' ? 1 : 0),
			)
		);
	}

	public static function remove($importId) {
		$adb = PearDatabase::getInstance();
		if (Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$adb->pquery('DELETE FROM vtiger_import_queue WHERE importid=?', array($importId));
		}
	}

	public static function removeForUser($user) {
		$adb = PearDatabase::getInstance();
		if (Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$adb->pquery('DELETE FROM vtiger_import_queue WHERE userid=?', array($user->id));
		}
	}

	public static function getUserCurrentImportInfo($user) {
		$adb = PearDatabase::getInstance();
		if (Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery('SELECT * FROM vtiger_import_queue WHERE userid=? LIMIT 1', array($user->id));

			if ($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getImportInfo($module, $user) {
		$adb = PearDatabase::getInstance();
		if (Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery(
				'SELECT * FROM vtiger_import_queue WHERE tabid=? AND userid=?',
				array(getTabid($module), $user->id)
			);

			if ($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getImportInfoById($importId) {
		$adb = PearDatabase::getInstance();
		if (Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery('SELECT * FROM vtiger_import_queue WHERE importid=?', array($importId));

			if ($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getAll($status = false) {
		$adb = PearDatabase::getInstance();
		$query = 'SELECT * FROM vtiger_import_queue';
		$params = array();
		if ($status !== false) {
			$query .= ' WHERE status=?';
			$params[] = $status;
		}
		$result = $adb->pquery($query, $params);
		$noOfImports = $adb->num_rows($result);
		$scheduledImports = array();
		for ($i = 0; $i < $noOfImports; ++$i) {
			$rowData = $adb->raw_query_result_rowdata($result, $i);
			$scheduledImports[$rowData['importid']] = self::getImportInfoFromResult($rowData);
		}
		return $scheduledImports;
	}

	public static function getImportInfoFromResult($rowData) {
		return array(
			'id' => $rowData['importid'],
			'module' => getTabModuleName($rowData['tabid']),
			'field_mapping' => json_decode($rowData['field_mapping'], true),
			'default_values' => json_decode($rowData['default_values'], true),
			'merge_type' => $rowData['merge_type'],
			'merge_fields' => json_decode($rowData['merge_fields'], true),
			'importmergecondition' => $rowData['importmergecondition'],
			'skipcreate' => $rowData['skipcreate'],
			'user_id' => $rowData['userid'],
			'status' => $rowData['status']
		);
	}

	public static function updateStatus($importId, $status) {
		$adb = PearDatabase::getInstance();
		$adb->pquery('UPDATE vtiger_import_queue SET status=? WHERE importid=?', array($status, $importId));
	}
}
?>