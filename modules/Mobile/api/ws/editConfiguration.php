<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class crmtogo_WS_Configuration extends crmtogo_WS_Controller {

	public function requireLogin() {
		return true;
	}

	public function process(crmtogo_API_Request $request) {
		$config_settings = crmtogo_WS_Controller::getUserModule();
		$response = new crmtogo_API_Response();
		$response->setResult($config_settings);
		return $response;
	}
}
