<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function updaterevisionwf($entityData) {
	global $adb, $currentModule;
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];
	$focus = CRMEntity::getInstance($currentModule);
	$entityidfield = $focus->table_index;
	$table_name = $focus->table_name;
	$adb->pquery("update $table_name set revisionactiva=1,revision=1 where $entityidfield=?", array($entityId));
}