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
include_once __DIR__ . '/../api/ws/RelatedRecords.php';
include_once __DIR__ . '/../api/ws/Utils.php';
include_once __DIR__ . '/../api/ws/FetchRecordDetails.php';

class crmtogo_UI_GetRelatedLists extends crmtogo_WS_RelatedRecords {
	public function process(crmtogo_API_Request $request) {
		$wsResponse = parent::process($request);
		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			$current_language = $this->sessionGet('language') ;
			$relatedlistsmodule = array_keys($wsResponseResult);
			$relatedresponse = new crmtogo_API_Response();
			$detailresponse_record = array();
			foreach ($relatedlistsmodule as $module) {
				$moduleWSId = crmtogo_WS_Utils::getEntityModuleWSId($module);
				//$fieldnames = crmtogo_WS_Utils::getEntityFieldnames($module);
				foreach ($wsResponseResult[$module] as $key => $shortrecordid) {
					if ($shortrecordid > 0) {
						$recordid = $moduleWSId.'x'.$shortrecordid;
						$detailrequest = new crmtogo_API_Request();
						$detailrequest->set('record', $recordid);
						$detailrequest->set('_operation', 'fetchRecord');
						$detailrequest->set('module', $module);
						$detailresponse = crmtogo_WS_FetchRecordDetails::process($detailrequest);
						$detailresponse_record[$module][$key] = $detailresponse->getResult();
					}
				}
			}
			$relatedresponse->setResult((count($detailresponse_record)>0 ? $detailresponse_record : null));
			$response = new crmtogo_API_Response();
			$config = $this->getUserConfigSettings();
			$viewer = new crmtogo_UI_Viewer();
			$viewer->assign('RECORDID', $request->get('record'));
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('LANGUAGE', $current_language);
			$viewer->assign('_MODULE', $module);
			$viewer->assign('_PARENT_MODULE', $request->get('module'));
			$viewer->assign('_RECORDS', $relatedresponse);
			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			$viewer->assign('_MODULES', $modules);
			$response = $viewer->process('RelatedListView.tpl');
		}
		return $response;
	}
}
?>
