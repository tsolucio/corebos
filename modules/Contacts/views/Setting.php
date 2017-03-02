<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Setting_View extends Vtiger_PopupAjax_View {
    
    public function __construct() {
        $this->exposeMethod('emitContactSyncSettingUI');
    }
    
    public function process(Vtiger_Request $request) {
        switch ($request->get('sourcemodule')) {
            case "Contacts" : $this->emitContactsSyncSettingUI($request);
                break;
        }
    }
    
    public function emitContactsSyncSettingUI(Vtiger_Request $request) {
        $user = Users_Record_Model::getCurrentUserModel();
        $connector = new Google_Contacts_Connector(FALSE);
        $fieldMappping = Google_Utils_Helper::getFieldMappingForUser();
        $oauth2 = new Google_Oauth2_Connector($request->get('sourcemodule'));
        if($oauth2->hasStoredToken()) {
            $controller = new Google_Contacts_Controller($user);
            $connector = $controller->getTargetConnector();
            $groups = $connector->pullGroups();
        }
        $targetFields = $connector->getFields();
        $selectedGroup = Google_Utils_Helper::getSelectedContactGroupForUser();
        $syncDirection = Google_Utils_Helper::getSyncDirectionForUser($user);
        $contactsModuleModel = Vtiger_Module_Model::getInstance($request->get('sourcemodule'));
        $mandatoryMapFields = array('salutationtype','firstname','lastname','title','account_id','birthday',
            'email','secondaryemail','mobile','phone','homephone','mailingstreet','otherstreet','mailingpobox',
            'otherpobox','mailingcity','othercity','mailingstate','otherstate','mailingzip','otherzip','mailingcountry',
            'othercountry','otheraddress','description','mailingaddress','otheraddress');
        $customFieldMapping = array();
        $contactsFields = $contactsModuleModel->getFields();
        foreach($fieldMappping as $vtFieldName => $googleFieldDetails) {
            if(!in_array($vtFieldName, $mandatoryMapFields) && $contactsFields[$vtFieldName]->isViewable())
                $customFieldMapping[$vtFieldName] = $googleFieldDetails;
        }
        $skipFields = array('reference','contact_id','leadsource','assigned_user_id','donotcall','notify_owner',
            'emailoptout','createdtime','modifiedtime','contact_no','modifiedby','isconvertedfromlead','created_user_id',
            'portal','support_start_date','support_end_date','imagename');
        $emailFields = $phoneFields = $urlFields = $otherFields = array();
        $disAllowedFieldTypes = array('reference','picklist','multipicklist');
        foreach($contactsFields as $contactFieldModel) {
            if($contactFieldModel->isViewable() && !in_array($contactFieldModel->getFieldName(),array_merge($mandatoryMapFields,$skipFields))) {
                if($contactFieldModel->getFieldDataType() == 'email')
                    $emailFields[$contactFieldModel->getFieldName()] = $contactFieldModel->get('label');
                else if($contactFieldModel->getFieldDataType() == 'phone') 
                    $phoneFields[$contactFieldModel->getFieldName()] = $contactFieldModel->get('label');
                else if($contactFieldModel->getFieldDataType() == 'url')
                    $urlFields[$contactFieldModel->getFieldName()] = $contactFieldModel->get('label');
                else if(!in_array ($contactFieldModel->getFieldDataType(), $disAllowedFieldTypes))
                    $otherFields[$contactFieldModel->getFieldName()] = $contactFieldModel->get('label');
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', $request->getModule());
        $viewer->assign('SOURCE_MODULE', $request->get('sourcemodule'));
        $viewer->assign('SELECTED_GROUP', $selectedGroup);
        $viewer->assign('SYNC_DIRECTION', $syncDirection);
        $viewer->assign('GOOGLE_GROUPS', $groups);
        $viewer->assign('GOOGLE_FIELDS',$targetFields);
        $viewer->assign('FIELD_MAPPING',$fieldMappping);
        $viewer->assign('CUSTOM_FIELD_MAPPING',$customFieldMapping);
        $viewer->assign('VTIGER_EMAIL_FIELDS',$emailFields);
        $viewer->assign('VTIGER_PHONE_FIELDS',$phoneFields);
        $viewer->assign('VTIGER_URL_FIELDS',$urlFields);
        $viewer->assign('VTIGER_OTHER_FIELDS',$otherFields);
        echo $viewer->view('ContactsSyncSettings.tpl', $request->getModule(), true);
    }
    
}

?>