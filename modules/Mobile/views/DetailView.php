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
include_once dirname(__FILE__) . '/../api/ws/FetchRecordDetails.php';

class crmtogo_UI_DetailView extends crmtogo_WS_FetchRecordDetails {
	function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
		$modules = $this->sessionGet('_MODULES');
		foreach($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) {
				return $module; 
			}
		}
		return false;
	}
	
	function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$modules_with_comments = $this->getConfigSettingsComments();
		$current_user = $this->getActiveUser();
		$current_language = $this->sessionGet('language') ;
		//generate dateformat for Smarty
		$target_date_format = $current_user->date_format;
		$target_date_format= str_replace("yyyy", "%Y", $target_date_format);
		$target_date_format= str_replace("mm", "%m", $target_date_format);
		$target_date_format= str_replace("dd", "%d", $target_date_format);
		$user_hourformat = $current_user->hour_format;
		if ($user_hourformat == '24') {
			$target_hourformat = '%H:%M';
		}
		else {
			$target_hourformat = '%I:%M %p';
		}
		
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} 
		else {
			$viewer = new crmtogo_UI_Viewer();
			$wsResponseResult = $wsResponse->getResult();

			$moduleObj = $this->cachedModuleLookupWithRecordId($wsResponseResult['record']['id']);
			if (!$moduleObj) {
				//module currently not supported
				$current_module_strings = return_module_language($current_language, 'Mobile');
				$viewer->assign('MESSAGE', $current_module_strings['LBL_NOT_SUPPORTED']);
				//$response = $viewer->process('Unsupported.tpl');
				return $response;
			}
			$record = crmtogo_UI_ModuleRecordModel::buildModelFromResponse($wsResponseResult['record']);
			$record->setId($wsResponseResult['record']['id']);
			
			$config = $this->getUserConfigSettings();
			//display comments? $modules_with_comments come from ini file
			if (in_array($moduleObj->name(), $modules_with_comments)) {
				$viewer->assign('COMMENTDISPLAY', true);
			}
			else {
				$viewer->assign('COMMENTDISPLAY', false);
			}
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('_MODULE', $moduleObj);
			$viewer->assign('_RECORD', $record);
			$viewer->assign('DATEFORMAT',  $target_date_format);
			$viewer->assign('HOURFORMAT', $target_hourformat);
			$viewer->assign('LANGUAGE', $current_language);
			if (isset($wsResponseResult['comments'])) {
				$viewer->assign('_COMMENTS', $wsResponseResult['comments']);
			}
			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			$viewer->assign('_MODULES', $modules);
			$response = $viewer->process('DetailView.tpl');
		}
		return $response;
	}
}
?>