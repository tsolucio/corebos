<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'include/Webservices/Utils.php';
require_once 'include/events/VTEntityData.inc';
require_once 'data/VTEntityDelta.php';
require_once 'include/Webservices/DataTransform.php';
require_once 'modules/WSAPP/SyncServer.php';

class WSAPPAssignToTracker extends VTEventHandler {

	public function __construct() {
	}

	public function handleEvent($eventName, $entityData) {
		global $current_user;
		$moduleName = $entityData->getModuleName();

		//Specific to VAS
		if ($moduleName == 'Users') {
			return;
		}

		$recordId = $entityData->getId();
		$vtEntityDelta = new VTEntityDelta();
		$newEntityData = $vtEntityDelta->getNewEntity($moduleName, $recordId);
		$recordValues = $newEntityData->getData();
		$isAssignToModified = $this->isAssignToChanged($moduleName, $recordId, $current_user);
		if (!$isAssignToModified) {
			return;
		}
		$handler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$meta = $handler->getMeta();
		$recordWsValues = DataTransform::sanitizeData($recordValues, $meta);
		$syncServer = new SyncServer();
		$syncServer->markRecordAsDeleteForAllCleints($recordWsValues);
	}

	public function isAssignToChanged($moduleName, $recordId, $user) {
		$handler = vtws_getModuleHandlerFromName($moduleName, $user);
		$meta = $handler->getMeta();
		$moduleOwnerFields = $meta->getOwnerFields();
		$assignToChanged = false;
		$vtEntityDelta = new VTEntityDelta();
		foreach ($moduleOwnerFields as $ownerField) {
			$assignToChanged = $vtEntityDelta->hasChanged($moduleName, $recordId, $ownerField);
			if ($assignToChanged) {
				break;
			}
		}
		return $assignToChanged;
	}
}
?>
