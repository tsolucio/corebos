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
include_once __DIR__ . '/../api/ws/FetchRecordDetails.php';
include_once __DIR__ . '/../api/ws/Utils.php';
include_once __DIR__ . '/../api/ws/Describe.php';

class crmtogo_UI_EditView extends crmtogo_WS_FetchRecordDetails {

	public function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach ($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) {
				return $module;
			}
		}
		return false;
	}

	public function cachedModuleLookup($currentmodule) {
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach ($modules as $module) {
			if ($module->name() == $currentmodule) {
				return $module;
			}
		}
		return false;
	}

	public function process(crmtogo_API_Request $request) {
		if ($request->getOperation()!='create') {
			$wsResponse = parent::process($request);
		} else {
			$wsResponse = crmtogo_WS_Describe::process($request);
		}

		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$response = new crmtogo_API_Response();
			$wsResponseResult = $wsResponse->getResult();
			$currentModule = $request->get('module');
			$targetModule = $currentModule;

			if ($request->getOperation()!='create') {
				$moduleObj = $this->cachedModuleLookupWithRecordId($wsResponseResult['record']['id']);
				$record = crmtogo_UI_ModuleRecordModel::buildModelFromResponse($wsResponseResult['record']);
				if ($request->getOperation()=='duplicate') {
					$record->setId();
					$wsResponseResult['record']['id'] = '';
				} else {
					$record->setId($wsResponseResult['record']['id']);
				}
			} else {
				$moduleObj = $this->cachedModuleLookup($targetModule);
				$record = crmtogo_UI_ModuleRecordModel::buildModel($wsResponseResult['record']);
				$record->setId('');
			}
			$current_user = $this->getActiveUser();
			//for compatibility to CRM versions 5.2.1 and 5.3.0 ff.
			$current_language = $this->sessionGet('language') ;
			//generate dateformat for Smarty
			$target_date_format = $current_user->date_format;
			$target_date_format= str_replace('yyyy', '%Y', $target_date_format);
			$target_date_format= str_replace('mm', '%m', $target_date_format);
			$target_date_format= str_replace('dd', '%d', $target_date_format);
			//generate language for Smarty date (like 'de')
			$target_lang_format= substr($current_language, 3, 2);

			// change variance for split record id
			$recordIdComponents = explode('x', $wsResponseResult['record']['id']);
			//this is a temporary fix for invitees for events, must get modified later
			$invited_users=array();
			if ($currentModule=='cbCalendar' && $request->getOperation()!='create') {
				global $adb;
				$sql = 'select vtiger_users.user_name,vtiger_invitees.*
					from vtiger_invitees
					left join vtiger_users on vtiger_invitees.inviteeid=vtiger_users.id
					where activityid=?';
				$result = $adb->pquery($sql, array($recordIdComponents[1]));
				$num_rows=$adb->num_rows($result);
				for ($i=0; $i<$num_rows; $i++) {
					$userid=$adb->query_result($result, $i, 'inviteeid');
					$username=$adb->query_result($result, $i, 'user_name');
					$invited_users[$userid]=$username;
				}
			}
			$config = $this->getUserConfigSettings();
			$viewer = new crmtogo_UI_Viewer();
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('_MODULE', $moduleObj);
			$viewer->assign('CURRENTMODUL', $currentModule);
			$viewer->assign('CURRENTUSERwsid', vtws_getEntityId('Users') . 'x' . $current_user->id);
			$viewer->assign('_RECORD', $record);
			$viewer->assign('id', $wsResponseResult['record']['id']);
			$viewer->assign('mode', $request->getOperation());
			$viewer->assign('crmtogorecordid', $wsResponseResult['record']['id']);
			$viewer->assign('DATEFORMAT', $current_user->date_format);
			$viewer->assign('SMARTYDATEFORMAT', $target_date_format);
			$viewer->assign('HOURFORMATFORMAT', $current_user->hour_format);
			$viewer->assign('LANGFORMATFORMAT', $target_lang_format);
			$viewer->assign('INVITEES', implode(';', array_keys($invited_users)));
			$viewer->assign('LANGUAGE', $current_language);

			$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, $currentModule);
			$viewer->assign('UPLOADSIZE', $upload_maxsize/1000000); //Convert to MB
			$viewer->assign('UPLOAD_MAXSIZE', $upload_maxsize);
			$viewer->assign('MAX_FILE_SIZE', $upload_maxsize);

			//Get PanelMenu data
			$modules = $this->sessionGet('_MODULES');
			$viewer->assign('_MODULES', $modules);
			if (isset($_REQUEST['quickcreate']) && $_REQUEST['quickcreate'] == 1) {
				$response = $viewer->process('QuickCreateView.tpl');
			} else {
				$response = $viewer->process('EditView.tpl');
			}
		}
		return $response;
	}
}
?>
