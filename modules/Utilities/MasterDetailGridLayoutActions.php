<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  coreBOS Open Source
 * The Initial Developer of the Original Code is coreBOS.
 * Portions created by vtiger are Copyright (C) coreBOS.
 * All Rights Reserved.
 ********************************************************************************/
include_once 'include/ListView/GridUtils.php';

$mdaction = empty($_REQUEST['mdaction']) ? 'list' : $_REQUEST['mdaction'];
switch ($_REQUEST['mdaction']) {
	case 'delete':
		$rs = gridDeleteRow($adb, $_REQUEST);
		echo json_encode($rs);
		break;
	case 'move':
		$rs = gridMoveRowUpDown($adb, $_REQUEST);
		echo json_encode($rs);
		break;
	case 'inline_edit':
		$res = gridInlineCellEdit($_REQUEST);
		echo json_encode($res);
		break;
	case 'list':
	default:
		if (empty($_REQUEST['mdmap'])) {
			echo getEmptyDataGridResponse();
		} else {
			$mname = vtlib_purify($_REQUEST['mdmap']);
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mname, cbMap::getMapIdByName($mname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$mdmap = $cbMap->MasterDetailLayout();
				if (!empty($mdmap['listview']) && !empty($mdmap['listview']['fields'] && !empty($_REQUEST['pid']))) {
					echo getDataGridResponse($mdmap);
				} else {
					echo getEmptyDataGridResponse();
				}
			} else {
				echo getEmptyDataGridResponse();
			}
		}
		break;
}
