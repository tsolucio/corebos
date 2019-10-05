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
include_once __DIR__ . '/../api/ws/Login.php';

class crmtogo_UI_Logout extends crmtogo_WS_Login {

	public function requireLogin() {
		return false;
	}

	public function hasActiveUser() {
		return false;
	}

	public function process(crmtogo_API_Request $request) {
		$default_config = $this->getConfigDefaults();
		$default_lang_strings = return_module_language($default_config['language'], 'Mobile');
		coreBOS_Session::destroy();
		$viewer = new crmtogo_UI_Viewer();
		$viewer->assign('MOD', $default_lang_strings);
		$viewer->assign('LANGUAGE', $default_lang_strings);
		$viewer->assign('COLOR_HEADER_FOOTER', $default_config['theme']);
		$viewer->assign('LOGOUT', true);
		$viewer->assign('COMPANY_LOGO', $default_config['company_logo']);
		$viewer->assign('COMPANY_NAME', $default_config['company_name']);
		return $viewer->process('Login.tpl');
	}
}
