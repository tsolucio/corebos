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
include_once dirname(__FILE__) . '/../api/ws/Utils.php';
include_once dirname(__FILE__) . '/../api/ws/Describe.php';

class Mobile_UI_FetchRecordWithGrouping extends Mobile_WS_FetchRecordWithGrouping {

	function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
    	$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) { return $module; };
		}
		return false;
	}
	function cachedModuleLookup($currentmodule) {
    	$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->name() == $currentmodule) { return $module; };
		}
		return false;
	}
	
	function process(Mobile_API_Request $request) {
		global $currentModule;
		if($request->getOperation()!='create') {
			$wsResponse = parent::process($request);
		}
		else {
			$wsResponse = Mobile_WS_Describe::process($request);
		}
	
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			$currentModule = vtlib_purify($_REQUEST['module']);
			$origmodule = $currentModule;
			if ( $currentModule == 'Events') {
				$targetModule = 'Calendar';
			}
			else {
				$targetModule = $currentModule;
			}
			
			if($request->getOperation()!='create') {
				$moduleObj = $this->cachedModuleLookupWithRecordId($wsResponseResult['record']['id']);
				$record = Mobile_UI_ModuleRecordModel::buildModelFromResponse($wsResponseResult['record']);
				$record->setId($wsResponseResult['record']['id']);
			}
			else {
				$moduleObj = $this->cachedModuleLookup($targetModule);
				$record = Mobile_UI_ModuleRecordModel::buildModel($wsResponseResult['record']);
				$record->setId('');
			}
			$current_user = $this->getActiveUser();
			//for compatibility to CRM versions 5.2.1 and 5.3.0 ff.
			$current_language = $this->sessionGet('language') ;
			include_once dirname(__FILE__) . '/../language/'.$current_language.'.lang.php';
			//generate dateformat for Smarty
			$target_date_format = $current_user->date_format;
			$target_date_format= str_replace("yyyy", "%Y", $target_date_format);
			$target_date_format= str_replace("mm", "%m", $target_date_format);
			$target_date_format= str_replace("dd", "%d", $target_date_format);
			//generate language for Smarty date (like 'de')
			$target_lang_format= substr($current_language, 3, 2);

			// change variance for split record id
			$recordIdComponents = explode('x', $wsResponseResult['record']['id']);
			//this is a temporary fix for invitees for events, must get modified later			
			$invited_users=Array();
			if ($currentModule == 'Events') {
				global $adb;
				$sql = 'select vtiger_users.user_name,vtiger_invitees.* from vtiger_invitees left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id where activityid=?';
				$result = $adb->pquery($sql, array($recordIdComponents[1]));
				$num_rows=$adb->num_rows($result);
				for($i=0;$i<$num_rows;$i++) {
					$userid=$adb->query_result($result,$i,'inviteeid');
					$username=$adb->query_result($result,$i,'user_name');
					$invited_users[$userid]=$username;
				}
			}
			//hour format for wheel
			if ($current_user->hour_format == '24') {
				$timewheelformat ='HHii';
			}
			else {
				$timewheelformat ='';
			}
			$viewer = new Mobile_UI_Viewer();
			$viewer->assign('_MODULE', $moduleObj);
			$viewer->assign('CURRENTMODUL', $currentModule);
			$viewer->assign('_RECORD', $record);
            $viewer->assign('id', $wsResponseResult['record']['id']);
			$viewer->assign('mode', $request->getOperation());
			$viewer->assign('mobilerecordid', $wsResponseResult['record']['id']);
			$viewer->assign('MOD', $mod_strings);
			$viewer->assign('DATEFORMAT',  $current_user->date_format);
			$viewer->assign('SMARTYDATEFORMAT', $target_date_format);
			$viewer->assign('HOURFORMATFORMAT', $current_user->hour_format);
			$viewer->assign('LANGFORMATFORMAT', $target_lang_format);
			$viewer->assign('INVITEES',  implode (",", array_keys($invited_users)));
			$viewer->assign('LANGUAGE', $current_language);
			$viewer->assign('ORIGMODULE', $origmodule);

			$viewer->assign('TIMEWHEEL', $timewheelformat);

			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			//remove Events from module list display
			function filter_by_value ($array, $value){
				if(is_array($array) && count($array)>0) {
					foreach(array_keys($array) as $key){
						$temp[$key] = $array[$key]->name();
						if ($temp[$key] == $value){
							$newarray[$key] = $array[$key]->name();
						}
					}
				}
				return $newarray;
			}
			$eventarray = filter_by_value($modules, 'Events');
			$eventkey = array_keys($eventarray);
			unset($modules[$eventkey[0]]);

			$viewer->assign('_MODULES', $modules);
			//reserved for future use: list modules for global search
			$viewer->assign('SEARCHIN', implode(",", $displayed_modules));

			$response = $viewer->process('generic/edit.tpl');
		}
		return $response;
	}
}
?>