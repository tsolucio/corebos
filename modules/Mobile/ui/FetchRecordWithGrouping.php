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
include_once dirname(__FILE__) . '/../api/ws/FetchRecordWithGrouping.php';

class Mobile_UI_FetchRecordWithGrouping extends Mobile_WS_FetchRecordWithGrouping {
	function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) {
				return $module; 
			};
		}
		return false;
	}
	
	function process(Mobile_API_Request $request) {
		global $modules_with_comments;
		$wsResponse = parent::process($request);
		$current_user = $this->getActiveUser();
		$current_language = $this->sessionGet('language') ;
		include_once dirname(__FILE__) . '/../language/'.$current_language.'.lang.php';
		//generate dateformat for Smarty
		$target_date_format = $current_user->date_format;
		$target_date_format= str_replace("yyyy", "%Y", $target_date_format);
		$target_date_format= str_replace("mm", "%m", $target_date_format);
		$target_date_format= str_replace("dd", "%d", $target_date_format);
		
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} 
		else {
			$wsResponseResult = $wsResponse->getResult();

			$moduleObj = $this->cachedModuleLookupWithRecordId($wsResponseResult['record']['id']);
			$record = Mobile_UI_ModuleRecordModel::buildModelFromResponse($wsResponseResult['record']);
			$record->setId($wsResponseResult['record']['id']);
			
			$viewer = new Mobile_UI_Viewer();
			//display comments? $modules_with_comments comes from MobileSettings.config.php
			if (in_array($moduleObj->name(), $modules_with_comments)) {
				$viewer->assign('COMMENTDISPLAY', true);
			}
			else {
				$viewer->assign('COMMENTDISPLAY', false);
			}
			$viewer->assign('_MODULE', $moduleObj);
			$viewer->assign('_RECORD', $record);
			$viewer->assign('MOD', $mod_strings);
			$viewer->assign('DATEFORMAT',  $target_date_format);
			$viewer->assign('HOURFORMAT', $current_user->hour_format);
			$viewer->assign('LANGUAGE', $current_language);
			if (isset($wsResponseResult['comments'])) {
				$viewer->assign('_COMMENTS', $wsResponseResult['comments']);
			}
			$response = $viewer->process('generic/Detail.tpl');
		}
		return $response;
	}
}
?>