<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once __DIR__ .'/ModTracker.php';
require_once 'data/VTEntityDelta.php';

class ModTrackerHandler extends VTEventHandler {

	public function handleEvent($eventName, $data) {
		global $adb, $current_user;
		$moduleName = $data->getModuleName();

		$flag = ModTracker::isTrackingEnabledForModule($moduleName);

		if ($flag) {
			if ($eventName == 'vtiger.entity.aftersave.final') {
				$recordId = $data->getId();
				$columnFields = $data->getData();
				$vtEntityDelta = new VTEntityDelta();
				$delta = $vtEntityDelta->getEntityDelta($moduleName, $recordId, true);

				$newerEntity = $vtEntityDelta->getNewEntity($moduleName, $recordId);
				$newerEntity->getData();

				if (is_array($delta)) {
					$inserted = false;
					foreach ($delta as $fieldName => $values) {
						if ($fieldName != 'modifiedtime') {
							if (!$inserted) {
								$checkRecordPresentResult = $adb->pquery('SELECT * FROM vtiger_modtracker_basic WHERE crmid = ?', array($recordId));
								if ($adb->num_rows($checkRecordPresentResult)) {
									$status = ModTracker::$UPDATED;
								} else {
									$status = ModTracker::$CREATED;
								}
								// get modified time from database in case it has been hidden in GUI
								$crmrs = $adb->pquery('select modifiedtime from vtiger_crmentity where crmid=?', array($recordId));
								$modtime = $adb->query_result($crmrs, 0, 0);
								$this->id = $adb->getUniqueId('vtiger_modtracker_basic');
								$adb->pquery(
									'INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
									array($this->id, $recordId, $moduleName, $current_user->id, $modtime, $status)
								);
								$inserted = true;
							}
							$adb->pquery(
								'INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue,postvalue) VALUES(?,?,?,?)',
								array($this->id, $fieldName, $values['oldValue'], $values['currentValue'])
							);
						}
					}
				}
			}

			if ($eventName == 'vtiger.entity.beforedelete') {
				$recordId = $data->getId();
				$columnFields = $data->getData();
				$id = $adb->getUniqueId('vtiger_modtracker_basic');
				$adb->pquery(
					'INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
					array($id, $recordId, $moduleName, $current_user->id, date('Y-m-d H:i:s', time()), ModTracker::$DELETED)
				);
			}

			if ($eventName == 'vtiger.entity.afterrestore') {
				$recordId = $data->getId();
				$columnFields = $data->getData();
				$id = $adb->getUniqueId('vtiger_modtracker_basic');
				$adb->pquery(
					'INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status) VALUES(?,?,?,?,?,?)',
					array($id, $recordId, $moduleName, $current_user->id, date('Y-m-d H:i:s', time()), ModTracker::$RESTORED)
				);
			}
		}
	}
}
?>