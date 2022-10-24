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
global $adb;
switch ($_REQUEST['rlaction']) {
	case 'delete':
		$rs = gridUnrelate($adb, $_REQUEST);
		echo json_encode($rs);
		break;
	case 'list':
	default:
		if (empty($_REQUEST['mapname'])) {
			echo getEmptyDataGridResponse();
		} else {
			$mname = vtlib_purify($_REQUEST['mapname']);
			$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$mname, cbMap::getMapIdByName($mname));
			if ($cbMapid) {
				$cbMap = cbMap::getMapByID($cbMapid);
				$map = $cbMap->RelatedListBlock();
				$mods = end($map['modules']);
				if (!empty($mods['listview']) && !empty($_REQUEST['pid'])) {
					echo getRelatedListGridResponse($map);
				} else {
					echo getEmptyDataGridResponse();
				}
			} else {
				echo getEmptyDataGridResponse();
			}
		}
		break;
}
