<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class crmtogo_WS_Login extends crmtogo_WS_Controller {

	public function requireLogin() {
		return false;
	}

	public function process(crmtogo_API_Request $request) {
		if (vtlib_isModuleActive('Mobile') === false) {
			$response->setError(1501, $default_lang_strings['LBL_NO_SERVICE']);
			return $response;
		}

		$response = new crmtogo_API_Response();
		$username = $request->get('username');
		$password = $request->get('password');

		$current_user = CRMEntity::getInstance('Users');
		//get default language for Mobile from DB
		$default_config = $this->getConfigDefaults();
		$default_lang_strings = return_module_language($default_config['language'], 'Mobile');
		if ($this->hasActiveUser()) {
			$current_user = $this->getActiveUser();
			$username = $_SESSION ['username'];
			$password = $_SESSION ['password'];
		}
		$current_user->column_fields['user_name'] = $username;
		if (!$current_user->doLogin($password)) {
			$response->setError(1210, $default_lang_strings['LBL_INVALID_PASSWORD']);
		} else {
			// Start session now
			coreBOS_Session::init();

			$current_user->id = $current_user->retrieve_user_id($username);
			$current_user= $current_user->retrieveCurrentUserInfoFromFile($current_user->id);
			$this->setActiveUser($current_user);

			//one day
			coreBOS_Session::set('__HTTP_Session_Expire_TS', time() + (60 * 60 * 24));
			// 1 hour
			coreBOS_Session::set('__HTTP_Session_Idle_TS', 1*60*60);
			coreBOS_Session::set('loginattempts', 0);
			coreBOS_Session::set('_authenticated_user_id', $current_user->id);
			coreBOS_Session::set('username', $username);
			coreBOS_Session::set('password', $password);
			coreBOS_Session::set('language', $current_user->column_fields['language']);
			coreBOS_Session::set('user_tz', $current_user->column_fields['time_zone']);
			$result = array();
			$result['login'] = array(
				'userid' => $current_user->id,
				'user_name' => $username,
				'password' => $password,
				'crm_tz' => DateTimeField::getDBTimeZone(),
				'user_tz' => $current_user->time_zone,
				'session'=> '',
				'language' => $current_user->column_fields['language'],
				'vtiger_version' => crmtogo_WS_Utils::getVtigerVersion(),
				'crmtogo_module_version' => crmtogo_WS_Utils::getVersion()
			);
			$response->setResult($result);

			$this->postProcess($response);
		}
		return $response;
	}

	public function postProcess(crmtogo_API_Response $response) {
		return $response;
	}
}
