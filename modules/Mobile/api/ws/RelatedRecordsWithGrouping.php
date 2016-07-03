<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/

include_once dirname(__FILE__) . '/QueryWithGrouping.php';

class Mobile_WS_RelatedRecordsWithGrouping extends Mobile_WS_QueryWithGrouping {
	
	function process(Mobile_API_Request $request) {
		global $current_user, $adb, $currentModule;
		$current_user = $this->getActiveUser();
		
		$response = new Mobile_API_Response();

		$record = $request->get('record');
		$currentPage = $request->get('page', 0);
		
		// Input validation
		if (empty($record)) {
			$response->setError(1001, 'Record id is empty');
			return $response;
		}
		$recordid = vtws_getIdComponents($record);
		$recordid = $recordid[1];
		
		$module = Mobile_WS_Utils::detectModulenameFromRecordId($record);

		// Initialize global variable
		$currentModule = $module;
		
		//related module currently supported
		$relatedmodule = Array ('Contacts','Potentials','HelpDesk');
		$activemodule = $this->sessionGet('_MODULES');
		foreach($activemodule as $amodule) {
			if (in_array($amodule->name(), $relatedmodule)) {
				$active_related_module[] = $amodule->name();
			}
		}

		foreach ($active_related_module as $relmod) {
			$functionHandler = Mobile_WS_Utils::getRelatedFunctionHandler($module, $relmod); 
			$fieldmodel = new Mobile_UI_FieldModel();
			if ($functionHandler) {
				$sourceFocus = CRMEntity::getInstance($module);
				$relationResult = call_user_func_array(	array($sourceFocus, $functionHandler), array($recordid, getTabid($module), getTabid($relmod)) );
				$relatedRecords[$relmod] = array_keys ($relationResult['entries']);

				$response->setResult($relatedRecords);
			}
			else {
				$response->setError(1018, 'Function Handler for module '.$module.' for related Module '.$relmod.'  not found.');
			}
		}
		return $response;
	}
}