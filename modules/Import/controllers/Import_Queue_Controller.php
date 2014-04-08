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

	static $IMPORT_STATUS_NONE = 0;
	static $IMPORT_STATUS_SCHEDULED = 1;
	static $IMPORT_STATUS_RUNNING = 2;
	static $IMPORT_STATUS_HALTED = 3;
	static $IMPORT_STATUS_COMPLETED = 4;

	public function  __construct() {
	}

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
								status INT default 0)",
							true);
		}

		if($userInputObject->get('is_scheduled')) {
			$status = self::$IMPORT_STATUS_SCHEDULED;
		} else {
			$status = self::$IMPORT_STATUS_NONE;
		}

		$adb->pquery('INSERT INTO vtiger_import_queue VALUES(?,?,?,?,?,?,?,?)',
				array($adb->getUniqueID('vtiger_import_queue'), 
						$user->id,
						getTabid($userInputObject->get('module')),
						Zend_Json::encode($userInputObject->get('field_mapping')),
						Zend_Json::encode($userInputObject->get('default_values')),
						$userInputObject->get('merge_type'),
						Zend_Json::encode($userInputObject->get('merge_fields')),
						$status));
	}

	public static function remove($importId) {
		$adb = PearDatabase::getInstance();
		if(Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$adb->pquery('DELETE FROM vtiger_import_queue WHERE importid=?', array($importId));
		}
	}

	public static function removeForUser($user) {
		$adb = PearDatabase::getInstance();
		if(Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$adb->pquery('DELETE FROM vtiger_import_queue WHERE userid=?', array($user->id));
		}
	}

	public static function getUserCurrentImportInfo($user) {
		$adb = PearDatabase::getInstance();

		if(Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery('SELECT * FROM vtiger_import_queue WHERE userid=? LIMIT 1', array($user->id));

			if($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}
	
	public static function getImportInfo($module, $user) {
		$adb = PearDatabase::getInstance();
		
		if(Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery('SELECT * FROM vtiger_import_queue WHERE tabid=? AND userid=?',
											array(getTabid($module), $user->id));

			if($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getImportInfoById($importId) {
		$adb = PearDatabase::getInstance();

		if(Vtiger_Utils::CheckTable('vtiger_import_queue')) {
			$queueResult = $adb->pquery('SELECT * FROM vtiger_import_queue WHERE importid=?', array($importId));

			if($queueResult && $adb->num_rows($queueResult) > 0) {
				$rowData = $adb->raw_query_result_rowdata($queueResult, 0);
				return self::getImportInfoFromResult($rowData);
			}
		}
		return null;
	}

	public static function getAll($status=false) {

		$adb = PearDatabase::getInstance();

		$query = 'SELECT * FROM vtiger_import_queue';
		$params = array();
		if($status !== false) {
			$query .= ' WHERE status = ?';
			array_push($params, $status);
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

	static function getImportInfoFromResult($rowData) {
		return array(
			'id' => $rowData['importid'],
			'module' => getTabModuleName($rowData['tabid']),
			'field_mapping' => Zend_Json::decode($rowData['field_mapping']),
			'default_values' => Zend_Json::decode($rowData['default_values']),
			'merge_type' => $rowData['merge_type'],
			'merge_fields' => Zend_Json::decode($rowData['merge_fields']),
			'user_id' => $rowData['userid'],
			'status' => $rowData['status']
		);
	}

	static function updateStatus($importId, $status) {
		$adb = PearDatabase::getInstance();

		$adb->pquery('UPDATE vtiger_import_queue SET status=? WHERE importid=?', array($status, $importId));
	}

}

?>