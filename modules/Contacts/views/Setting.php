<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Setting_View {

	public function __construct() {
	}

	public function process($request) {
		switch ($request['sourcemodule']) {
			case 'Contacts':
				$this->emitContactsSyncSettingUI($request);
				break;
		}
	}

	public function emitContactsSyncSettingUI($request) {
		global $current_user, $app_strings, $mod_strings, $theme;
		$user = $current_user;
		$connector = new Google_Contacts_Connector(false);
		$fieldMappping = Google_Utils_Helper::getFieldMappingForUser();
		$oauth2 = new Google_Oauth2_Connector($request['sourcemodule']);
		if ($oauth2->hasStoredToken()) {
			$controller = new Google_Contacts_Controller($user);
			$connector = $controller->getTargetConnector();
			$groups = $connector->pullGroups();
		}
		$targetFields = $connector->getFields();
		$selectedGroup = Google_Utils_Helper::getSelectedContactGroupForUser();
		$syncDirection = Google_Utils_Helper::getSyncDirectionForUser($user);
		$contactsModuleModel = CRMEntity::getInstance($request['sourcemodule']);
		$handler = vtws_getModuleHandlerFromName($request['sourcemodule'], $current_user);
		$meta = $handler->getMeta();
		$moduleFields = $meta->getModuleFields();
		$mandatoryMapFields = array('salutationtype','firstname','lastname','title','account_id','birthday',
			'email','secondaryemail','mobile','phone','homephone','mailingstreet','otherstreet','mailingpobox',
			'otherpobox','mailingcity','othercity','mailingstate','otherstate','mailingzip','otherzip','mailingcountry',
			'othercountry','otheraddress','description','mailingaddress','otheraddress');
		$customFieldMapping = array();
		$contactsFields = $contactsModuleModel->column_fields;
		foreach ($fieldMappping as $vtFieldName => $googleFieldDetails) {
			if (!in_array($vtFieldName, $mandatoryMapFields)) {
				$customFieldMapping[$vtFieldName] = $googleFieldDetails;
			}
		}
		$skipFields = array('reference','contact_id','leadsource','assigned_user_id','donotcall','notify_owner',
			'emailoptout','createdtime','modifiedtime','contact_no','modifiedby','isconvertedfromlead','created_user_id',
			'portal','support_start_date','support_end_date','imagename');
		$emailFields = $phoneFields = $urlFields = $otherFields = array();
		$disAllowedFieldTypes = array('reference','picklist','multipicklist');
		foreach ($contactsFields as $contactField => $fieldValue) {
			if (!in_array($contactField, array_merge($mandatoryMapFields, $skipFields)) && array_key_exists($contactField, $moduleFields)) {
				$contactFieldModel=$moduleFields["$contactField"];
				if ($contactFieldModel->getFieldDataType() == 'email') {
					$emailFields[$contactField] = $contactFieldModel->getFieldLabelKey();
				} elseif ($contactFieldModel->getFieldDataType() == 'phone') {
					$phoneFields[$contactField] = $contactFieldModel->getFieldLabelKey();
				} elseif ($contactFieldModel->getFieldDataType() == 'url') {
					$urlFields[$contactField] = $contactFieldModel->getFieldLabelKey();
				} elseif (!in_array($contactFieldModel->getFieldDataType(), $disAllowedFieldTypes)) {
					$otherFields[$contactField] = $contactFieldModel->getFieldLabelKey();
				}
			}
		}
		$viewer = new vtigerCRM_Smarty();
		$viewer->assign('APP', $app_strings);
		$viewer->assign('MOD', $mod_strings);
		$viewer->assign("THEME", $theme);
		$viewer->assign('MODULENAME', $request['sourcemodule']);
		$viewer->assign('SOURCE_MODULE', $request['sourcemodule']);
		$viewer->assign('SELECTED_GROUP', $selectedGroup);
		$viewer->assign('SYNC_DIRECTION', $syncDirection);
		$viewer->assign('GOOGLE_GROUPS', (empty($groups) ? '' : $groups));
		$viewer->assign('GOOGLE_FIELDS', $targetFields);
		$viewer->assign('FIELD_MAPPING', $fieldMappping);
		$viewer->assign('CUSTOM_FIELD_MAPPING', $customFieldMapping);
		$viewer->assign('VTIGER_EMAIL_FIELDS', $emailFields);
		$viewer->assign('VTIGER_PHONE_FIELDS', $phoneFields);
		$viewer->assign('VTIGER_URL_FIELDS', $urlFields);
		$viewer->assign('VTIGER_OTHER_FIELDS', $otherFields);
		global $coreBOS_app_name;
		$coreBOS_uiapp_name = GlobalVariable::getVariable('Application_UI_Name', $coreBOS_app_name);
		$viewer->assign('coreBOS_uiapp_name', $coreBOS_uiapp_name);
		$viewer->display('modules/Contacts/ContactsSyncSettings.tpl');
	}
}
?>