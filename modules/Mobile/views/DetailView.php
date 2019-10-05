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

class crmtogo_UI_DetailView extends crmtogo_WS_FetchRecordDetails {

	public function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
		$modules = $this->sessionGet('_MODULES');
		foreach ($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) {
				return $module;
			}
		}
		return false;
	}

	public function process(crmtogo_API_Request $request) {
		global $adb;
		$wsResponse = parent::process($request);
		$modules_with_comments = $this->getConfigSettingsComments();
		$current_user = $this->getActiveUser();
		$current_language = $this->sessionGet('language') ;
		//generate dateformat for Smarty
		$target_date_format = $current_user->date_format;
		$target_date_format= str_replace('yyyy', '%Y', $target_date_format);
		$target_date_format= str_replace('mm', '%m', $target_date_format);
		$target_date_format= str_replace('dd', '%d', $target_date_format);
		$user_hourformat = $current_user->hour_format;
		if ($user_hourformat == '24') {
			$target_hourformat = '%H:%M';
		} else {
			$target_hourformat = '%I:%M %p';
		}

		$response = false;
		if ($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
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
			} else {
				$viewer->assign('COMMENTDISPLAY', false);
			}
			$viewer->assign('MOD', $this->getUsersLanguage());
			$viewer->assign('COLOR_HEADER_FOOTER', $config['theme']);
			$viewer->assign('_MODULE', $moduleObj);
			$viewer->assign('_RECORD', $record);
			$viewer->assign('DATEFORMAT', $target_date_format);
			$viewer->assign('HOURFORMAT', $target_hourformat);
			$viewer->assign('LANGUAGE', $current_language);
			if (isset($wsResponseResult['comments'])) {
				$viewer->assign('_COMMENTS', $wsResponseResult['comments']);
			}
			//Get signature if exist and ticketstatus
			if ($moduleObj->name() == 'HelpDesk') {
				$array_recordid = explode('x', $record->id());
				$ticketid = $array_recordid[1];
				$reshd = $adb->pquery('Select ticket_no,status From vtiger_troubletickets Where ticketid = ?', array($ticketid));
				if ($adb->num_rows($reshd) > 0) {
					$ticket_no = $adb->query_result($reshd, 0, 'ticket_no');
					$ticketstatus = $adb->query_result($reshd, 0, 'status');
					if ($ticketstatus == 'Closed') {
						$query_docs = 'SELECT vtiger_attachments.path, vtiger_attachments.name, vtiger_attachments.attachmentsid
							FROM vtiger_attachments
							INNER JOIN vtiger_crmentity ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
							WHERE deleted=0 AND vtiger_attachments.name = ? ORDER BY vtiger_attachments.attachmentsid DESC';
						$res_docs = $adb->pquery($query_docs, array('firma_'.$ticket_no.'.png'));
						$doc_path = '';
						if ($adb->num_rows($res_docs) > 0) {
							$doc_path=$adb->query_result($res_docs, 0, 'path').$adb->query_result($res_docs, 0, 'attachmentsid').'_'.$adb->query_result($res_docs, 0, 'name');
						}
						$viewer->assign('SIGNPATH', $doc_path);
					}
					$viewer->assign('TICKETSTATUS', $ticketstatus);
				}
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