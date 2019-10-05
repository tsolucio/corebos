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
include_once __DIR__ . '/QueryWithGrouping.php';

class crmtogo_WS_RelatedRecords extends crmtogo_WS_QueryWithGrouping {

	public function process(crmtogo_API_Request $request) {
		global $current_user, $currentModule;
		$current_user = $this->getActiveUser();

		$response = new crmtogo_API_Response();

		$record = $request->get('record');
		//$currentPage = $request->get('page', 0);

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
		$gvRelatedModules = GlobalVariable::getVariable('Mobile_Related_Modules', 'Contacts,Potentials,HelpDesk,Documents,Timecontrol', 'Mobile');
		$relatedmodule = explode(',', $gvRelatedModules);
		$activemodule = $this->sessionGet('_MODULES');
		$relatedRecords = array();
		foreach ($activemodule as $amodule) {
			if (in_array($amodule->name(), $relatedmodule)) {
				if ($amodule->name() != $module) {
					$functionHandler = crmtogo_WS_Utils::getRelatedFunctionHandler($module, $amodule->name());
					//$fieldmodel = new crmtogo_UI_FieldModel();
					if ($functionHandler) {
						$sourceFocus = CRMEntity::getInstance($module);
						$relationResult = call_user_func_array(array($sourceFocus, $functionHandler), array($recordid, getTabid($module), getTabid($amodule->name())));
						if ($relationResult['entries']) {
							$relatedRecords[$amodule->name()] = array_keys($relationResult['entries']);
						} else {
							$relatedRecords[$amodule->name()] = array();
						}
					}
				} else {
					$relatedRecords[$amodule->name()] = array();
				}
				$response->setResult($relatedRecords);
			}
		}
		return $response;
	}
}