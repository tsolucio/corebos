<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by coreBOS are Copyright (C) coreBOS.
 * All Rights Reserved.
 ************************************************************************************/
global $log;
$log->debug('< MasterGridAPI');
Vtiger_Request::validateRequest();
$data = json_decode(vtlib_purify($_REQUEST['data']), true);
$module = $data['module'];
$rowid = isset($data['rowid']) ? $data['rowid'] : 0;
$log->debug('Method: '.$data['method']);
switch ($data['method']) {
	case 'deleteRow':
		if ($rowid > 0 && !empty($module)) {
			$focus = CRMEntity::getInstance($module);
			list($delerror,$errormessage) = $focus->preDeleteCheck();
			if (!$delerror) {
				$focus->trash($module, $rowid);
				$ret = true;
			} else {
				$log->debug('Error: '.$errormessage);
				$ret = false;
			}
		}
		break;
	case 'save':
		$ret = false;
		if (isset($data['MasterGridValues']) && !empty($data['MasterGridValues'])) {
			$MasterGridValues = json_decode($data['MasterGridValues'], true);
			$MasterGridModule = json_decode($data['MasterGridModule'], true);
			$MasterGridRelatedField = json_decode($data['MasterGridRelatedField'], true);
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$data['mapname'], cbMap::getMapIdByName($data['mapname']), $data['currentModule']);
			if (!$cbMapid) {
				echo json_encode(false);
				return false;
			}
			$cbMap = cbMap::getMapByID($cbMapid);
			$MapMG = $cbMap->cbMasterGrid();
			foreach ($MasterGridValues as $key => $row) {
				if ($data['__mastergridid'] != $row[0]) {
					continue;
				}
				$MasterModule = $MasterGridModule[$key][1]['module'];
				$MasterRelatedField = $MasterGridRelatedField[$key][1]['relatedfield'];
				foreach ($row[1] as $r) {
					CreateMasterRecord($r, $MasterModule, $MasterRelatedField, $data['id']);
				}
				$ret[] = getMasterGridData($MasterModule, $data['currentModule'], $MasterRelatedField, $data['id'], $MapMG, $data['__mastergridid']);
			}
		}
		break;
	default:
		$ret = false;
		break;
}
$log->debug('> MasterGridAPI');
echo json_encode($ret);
?>
