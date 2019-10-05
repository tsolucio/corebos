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
include_once __DIR__ . '/../api/ws/Utils.php';
include_once __DIR__ . '/../api/ws/editConfiguration.php';

class crmtogo_UI_Configuration extends crmtogo_WS_Configuration {

	public function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$response = new crmtogo_API_Response();
		$wsResponseResult = $wsResponse->getResult();
		$modules = crmtogo_UI_ModuleModel::buildModelsFromResponse($wsResponseResult);
		$config = $this->getUserConfigSettings();
		$viewer = new crmtogo_UI_Viewer();
		$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
		$viewer->assign('_MODULES', $modules);
		$viewer->assign('MOD', $this->getUsersLanguage());
		$viewer->assign('NAVISETTING', $config['NavigationLimit']);
		$response = $viewer->process('Config.tpl');
		return $response;
	}
}