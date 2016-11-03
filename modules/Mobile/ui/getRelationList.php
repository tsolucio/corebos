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
include_once dirname(__FILE__) . '/../api/ws/RelatedRecordsWithGrouping.php';
include_once dirname(__FILE__) . '/../api/ws/Utils.php';
include_once dirname(__FILE__) . '/../api/ws/FetchRecordWithGrouping.php';

class Mobile_UI_GetRelatedLists extends Mobile_WS_RelatedRecordsWithGrouping {
	function process(Mobile_API_Request $request) {
		global $app_strings,$mod_strings;
		$wsResponse = parent::process($request);
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} 
		else {
			$wsResponseResult = $wsResponse->getResult();

			$current_user = $this->getActiveUser();
			$current_language = $this->sessionGet('language') ;
			$app_strings = return_application_language($current_language);
			$relatedlistsmodule = array_keys($wsResponseResult);
			$relatedresponse = new Mobile_API_Response();
			foreach ($relatedlistsmodule as $module) {
				$moduleWSId = Mobile_WS_Utils::getEntityModuleWSId($module);
				if($module == 'Events' || $module == 'Calendar') {
					$fieldnames = Mobile_WS_Utils::getEntityFieldnames('Calendar');
				} else {
					$fieldnames = Mobile_WS_Utils::getEntityFieldnames($module);
				}
				foreach ($wsResponseResult[$module] as $key => $shortrecordid) { 
					$recordid = $moduleWSId.'x'.$shortrecordid;

					$detailrequest = new Mobile_API_Request();
					$detailrequest->set('record',$recordid);
					$detailrequest->set('_operation','fetchRecordWithGrouping');
					$detailrequest->set('module',$module);
					$detailresponse = Mobile_WS_FetchRecordWithGrouping::process($detailrequest);
					$detailresponse_record[$module][$key] = $detailresponse->getResult();
				}
			}
			$relatedresponse -> setResult($detailresponse_record);
			$response = new Mobile_API_Response();
			$current_language = $this->sessionGet('language') ;
			include_once dirname(__FILE__) . '/../language/'.$current_language.'.lang.php';
			$viewer = new Mobile_UI_Viewer();
			$viewer->assign('LANGUAGE', $current_language);
			$viewer->assign('MOD', $mod_strings);
			$viewer->assign('_MODULE', $module);
			$viewer->assign('_RECORDS', $relatedresponse);

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

			$response = $viewer->process('generic/getRelatedLists.tpl');
		}
		return $response;
	}
}
?>