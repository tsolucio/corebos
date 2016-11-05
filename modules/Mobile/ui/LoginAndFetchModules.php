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
include_once dirname(__FILE__) . '/../api/ws/LoginAndFetchModules.php';
include_once dirname(__FILE__) . '/../api/ws/Utils.php';

global $current_language;
class Mobile_UI_LoginAndFetchModules extends Mobile_WS_LoginAndFetchModules {
	
	protected function cacheModules($modules) {
		$this->sessionSet("_MODULES", $modules);
	}
	
	function process(Mobile_API_Request $request) {

		if($request->get('username') == '') {
				$response = new Mobile_API_Response();
				$response->setError(1501, 'Login required');
		}
		else {
			global $displayed_modules,$current_language, $current_user, $mod_strings, $app_strings;
			$username = $request->get('username');
			$current_user = CRMEntity::getInstance('Users');
			$userid = $current_user->retrieve_user_id($username);
			$current_user = $current_user->retrieveCurrentUserInfoFromFile($userid);
			$language = $current_user->language;
			$CRM_Version = Mobile::config('crm_version');
			if ($CRM_Version!='5.2.1') {
				//for v5.3.0 use User's Settings
				if($language!='') {
					$current_language = $language;
				}
				else {
					$current_language ='en_us';
				}
			}
			else {
				//for v5.2.1 use default language
				global $adb;
				$queryResult = $adb->pquery("SELECT prefix FROM vtiger_language WHERE isdefault =1", array());
				$current_language = $adb->query_result($queryResult,0,'prefix');
			}
			//set $app_strings
			Mobile_WS_Utils::initAppGlobals();

			$wsResponse = parent::process($request);
			$response = false;
			if($wsResponse->hasError()) {
				$response = $wsResponse;
			} 
			else {
				$wsResponseResult = $wsResponse->getResult();
				//fill cache
				$modules = Mobile_UI_ModuleModel::buildModelsFromResponse($wsResponseResult['modules']);
				$this->cacheModules($modules);
				Mobile_API_Session::set('language',$current_language);
				include dirname(__FILE__) . '/../language/'.$current_language .'.lang.php';
				$module_by_default = GlobalVariable::getVariable('Mobile_Module_by_default', 'Calendar', 'Mobile', $current_user->id);
				header("Location:index.php?_operation=listModuleRecords&module=".$module_by_default);
				die();
			}
		}
		return $response;
	}

}