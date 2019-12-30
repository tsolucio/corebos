<?php
 /*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/duplicate.php';

function dq_createRevision($crmid, $module) {
	$map = $module.'_DuplicateRelations';
	$new_record_id = duplicaterec($module, $crmid, $map);
	dq_updateRevisionFields($module, $crmid, $new_record_id);
	return $new_record_id;
}

function dq_recoverRevision($currentcrmid, $newcrmid, $module) {
	global $adb;
	include_once "modules/$module/$module.php";
	$focus = new $module;
	$entityidfield = $focus->table_index;
	$table_name  = $focus->table_name;
	$adb->pquery("update $table_name set revisionactiva=0 where $entityidfield=?", array($currentcrmid));
	$adb->pquery("update $table_name set revisionactiva=1 where $entityidfield=?", array($newcrmid));
}

$function = vtlib_purify($_REQUEST['function']);
switch ($function) {
	case 'createrevision':
		$crmid = vtlib_purify($_REQUEST['crmid']);
		$module = vtlib_purify($_REQUEST['dupmodule']);
		if (!empty($crmid) && is_numeric($crmid)) {
			$new_record_id = dq_createRevision($crmid, $module);
			echo $new_record_id;
		} else {
			echo 'nok';
		}
		break;
	case 'recoverrevision':
		$currentcrmid = vtlib_purify($_REQUEST['currentcrmid']);
		$newcrmid = vtlib_purify($_REQUEST['newcrmid']);
		$module = vtlib_purify($_REQUEST['dupmodule']);
		if (!empty($currentcrmid) && !empty($newcrmid) && is_numeric($currentcrmid) && is_numeric($newcrmid)) {
			dq_recoverRevision($currentcrmid, $newcrmid, $module);
			echo 'ok';
		} else {
			echo 'nok';
		}
		break;
}
?>
