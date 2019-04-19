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
include_once __DIR__ . '/../api/ws/Controller.php';
include_once __DIR__ . '/../api/ws/Utils.php';

class crmtogo_UI_DecideActivityType extends crmtogo_WS_Controller {
	public function process(crmtogo_API_Request $request) {
		$response = new crmtogo_API_Response();
		$current_language = $this->sessionGet('language') ;
		$viewer = new crmtogo_UI_Viewer();
		$viewer->assign('MOD', $this->getUsersLanguage());
		$viewer->assign('LANGUAGE', $current_language);
		$response = $viewer->process('decideActivityType.tpl');
		return $response;
	}
}
?>
