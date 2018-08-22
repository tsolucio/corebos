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
include_once __DIR__ . '/../api/ws/LoginAndFetchModules.php';
include_once __DIR__ . '/../api/ws/Utils.php';

class crmtogo_UI_LoginAndFetchModules extends crmtogo_WS_LoginAndFetchModules {

	protected function cacheModules($modules) {
		$this->sessionSet('_MODULES', $modules);
	}

	public function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			$modules = crmtogo_UI_ModuleModel::buildModelsFromResponse($wsResponseResult['modules']);
			$this->cacheModules($modules);
			//$config = $this->getUserConfigSettings();
			$module_by_default = GlobalVariable::getVariable('Mobile_Module_by_default', 'cbCalendar', 'Mobile');
			header('Location:index.php?_operation=listModuleRecords&module='.$module_by_default);
		}
		return $response;
	}
}