<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class Mobile_WS_Login extends Mobile_WS_Controller {
	
	function requireLogin() {
		return false;
	}

	function process(Mobile_API_Request $request) {
		$response = new Mobile_API_Response();
		
		$username = $request->get('username');
		$password = $request->get('password');
		
		$current_user = CRMEntity::getInstance('Users');
		$current_user->column_fields['user_name'] = $username;
		
		if(vtlib_isModuleActive('Mobile') === false) {
			$response->setError(1501, 'Service not available');
			return $response;
		}
		
		if(!$current_user->load_user($password) || !$current_user->authenticated) {
			global $mod_strings;
			$response->setError(1210, $mod_strings['ERR_INVALID_PASSWORD']);
			
		} else {
			// Start session now
			coreBOS_Session::init();

			include_once('config.php'); 
			global $application_unique_key;	

			$current_user->id = $current_user->retrieve_user_id($username);
			$this->setActiveUser($current_user);
			coreBOS_Session::set('authenticated_user_id', $current_user->id);
			coreBOS_Session::set('app_unique_key', $application_unique_key);
			$result = array();
			$result['login'] = array(
				'userid' => $current_user->id,
				'crm_tz' => DateTimeField::getDBTimeZone(),
				'user_tz' => $current_user->time_zone,
				'session'=> '',
				'language' => $current_user->language,
				'vtiger_version' => Mobile_WS_Utils::getVtigerVersion(),
				'mobile_module_version' => Mobile_WS_Utils::getVersion()
			);
			$response->setResult($result);
			
			$this->postProcess($response);
		}
		return $response;
	}
	
	function postProcess(Mobile_API_Response $response) {
		return $response;
	}
}