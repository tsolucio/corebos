<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************/
include_once 'modules/cbMap/cbMap.php';
require_once 'data/CRMEntity.php';
require_once 'include/utils/utils.php';

class decisiontable_Action extends CoreBOS_ActionController {

	public static function getModuleValues() {
		global $log;
		$log->debug('> getModuleValues');
		$mapid = vtlib_purify($_REQUEST['MapID']);
		$uitype = vtlib_purify($_REQUEST['uitype']);
		$Map = new cbMap();
		$Map->retrieve_entity_info($mapid, 'cbMap');
		$module = $Map->column_fields['targetname'];
		$log->debug('< getModuleValues');
		return getPicklistValuesSpecialUitypes('1613', '', $module);
	}

	public static function getEntityName() {
		global $log;
		$log->debug('> getEntityName');
		$MapID = vtlib_purify($_REQUEST['MapID']);
		$ename = getEntityName('cbMap', $MapID);
		$log->debug('< getEntityName');
		return $ename[$MapID];
	}
}

$method = vtlib_purify($_REQUEST['method']);
if ($method == 'getModuleValues') {
	$res = decisiontable_Action::getModuleValues();
} elseif ($method == 'getEntityName') {
	$res = decisiontable_Action::getEntityName();
}
echo json_encode($res);
?>