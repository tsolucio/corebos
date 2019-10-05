<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/Login.php';

class crmtogo_WS_LoginAndFetchModules extends crmtogo_WS_Login {

	public function postProcess(crmtogo_API_Response $response) {
		$current_user = $this->getActiveUser();

		if ($current_user) {
			$result = $response->getResult();
			$result['modules'] = crmtogo_WS_Controller::getUserModule();
			$response->setResult($result);
		} else {
			$default_config = $this->getConfigDefaults();
			$default_lang_strings = return_module_language($default_config['language'], 'Mobile');
			$response->setError(1310, $default_lang_strings['LBL_LOGIN_REQUIRED']);
		}
	}
}
?>