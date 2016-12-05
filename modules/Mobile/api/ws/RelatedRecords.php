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

class crmtogo_WS_RelatedRecords extends crmtogo_WS_QueryWithGrouping {
	
	function process(crmtogo_API_Request $request) {
		global $current_user, $adb, $currentModule;
		$current_user = $this->getActiveUser();
		
		$response = new crmtogo_API_Response();

		$record = $request->get('record');
		$currentPage = $request->get('page', 0);
		
		// Input validation
		if (empty($record)) {
			$response->setError(1001, 'Record id is empty');
			return $response;
		}
		$recordid = vtws_getIdComponents($record);
		$recordid = $recordid[1];
		
		$module = crmtogo_WS_Utils::detectModulenameFromRecordId($record);
		// Initialize global variable
		$currentModule = $module;
		
		//related module currently supported
		$relatedmodule = Array ('Contacts','Potentials','HelpDesk','Documents');
		$activemodule = $this->sessionGet('_MODULES');
		foreach($activemodule as $amodule) {
			if (in_array($amodule->name(), $relatedmodule)) {
				if ($currentModule == 'HelpDesk') {
					if ($amodule->name() != 'Contacts' AND $amodule->name() != 'Potentials') {
						$active_related_module[] = $amodule->name();
					}
				}
				else {
					$active_related_module[] = $amodule->name();
				}
			}
		}

		foreach ($active_related_module as $relmod) {
			if ($relmod != $module) {
				$functionHandler = crmtogo_WS_Utils::getRelatedFunctionHandler($module, $relmod); 
				$fieldmodel = new crmtogo_UI_FieldModel();
				if ($functionHandler) {
					$sourceFocus = CRMEntity::getInstance($module);
					$relationResult = call_user_func_array(	array($sourceFocus, $functionHandler), array($recordid, getTabid($module), getTabid($relmod)) );
					if ($relationResult['entries']) {
						$relatedRecords[$relmod] = array_keys ($relationResult['entries']);
					}
					else {
						$relatedRecords[$relmod] = array();
					}

				}
				else {
					$response->setError(1018, 'Function Handler for module '.$module.' for related Module '.$relmod.'  not found.');
				}
			}
			else {
				$relatedRecords[$relmod] = array();
			}
			$response->setResult($relatedRecords);
		}
		return $response;
	}
}