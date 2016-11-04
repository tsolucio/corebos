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
include_once 'include/Webservices/Retrieve.php';
include_once dirname(__FILE__) . '/FetchRecord.php';
include_once 'include/Webservices/DescribeObject.php';
include_once dirname(__FILE__) . '/Describe.php';

class Mobile_WS_FetchRecordWithGrouping extends Mobile_WS_FetchRecord {

	private $_cachedDescribeInfo = false;
	private $_cachedDescribeFieldInfo = false;

	protected function cacheDescribeInfo($describeInfo) {
		$this->_cachedDescribeInfo = $describeInfo;
		$this->_cachedDescribeFieldInfo = array();
		if(!empty($describeInfo['fields'])) {
			foreach($describeInfo['fields'] as $describeFieldInfo) {
				$this->_cachedDescribeFieldInfo[$describeFieldInfo['name']] = $describeFieldInfo;
			}
		}
	}

	protected function cachedDescribeInfo() {
		return $this->_cachedDescribeInfo;
	}

	protected function cachedDescribeFieldInfo($fieldname) {
		if ($this->_cachedDescribeFieldInfo !== false) {
			if(isset($this->_cachedDescribeFieldInfo[$fieldname])) {
				return $this->_cachedDescribeFieldInfo[$fieldname];
			}
		}
		return false;
	}

	protected function cachedEntityFieldnames($module) {
		$describeInfo = $this->cachedDescribeInfo();
		$labelFields = $describeInfo['labelFields'];
		switch($module) {
			case 'HelpDesk': $labelFields = 'ticket_title'; break;
			case 'Documents': $labelFields = 'notes_title'; break;
		}
		return explode(',', $labelFields);
	}

	protected function isTemplateRecordRequest(Mobile_API_Request $request) {
		$recordid = $request->get('record');
		return (preg_match("/([0-9]+)x0/", $recordid));
	}

	protected function processRetrieve(Mobile_API_Request $request) {
		$recordid = $request->get('record');

		// Create a template record for use
		if ($this->isTemplateRecordRequest($request)) {
			$current_user = $this->getActiveUser();
			$module = $this->detectModuleName($recordid);
			$describeInfo = vtws_describe($module, $current_user);
			Mobile_WS_Utils::fixDescribeFieldInfo($module, $describeInfo,$current_user);
			$this->cacheDescribeInfo($describeInfo);
			$templateRecord = array();
			foreach($describeInfo['fields'] as $describeField) {
				$templateFieldValue = '';
				if (isset($describeField['type']) && isset($describeField['type']['defaultValue'])) {
					$templateFieldValue = $describeField['type']['defaultValue'];
				} else if (isset($describeField['default'])) {
					$templateFieldValue = $describeField['default'];
				}
				$templateRecord[$describeField['name']] = $templateFieldValue;
			}
			if (isset($templateRecord['assigned_user_id'])) {
				$templateRecord['assigned_user_id'] = sprintf("%sx%s", Mobile_WS_Utils::getEntityModuleWSId('Users'), $current_user->id);
			}
			// Reset the record id
			$templateRecord['id'] = $recordid;
			return $templateRecord;
		}
		// Or else delegate the action to parent
		return parent::processRetrieve($request);
	}

	function process(Mobile_API_Request $request) {
		$operation = new Mobile_API_Request();
		$operation = $request->getOperation();
		if ($operation =='create') {
			$newrecord = Mobile_WS_Describe::process($request);
			$response = new Mobile_API_Response();
			$response->setResult(array('record' => $newrecord));
			return $response;
		}
		else {
			$response = parent::process($request);
			return $this->processWithGrouping($request, $response);
		}
	}

	protected function processWithGrouping(Mobile_API_Request $request, $response) {
		$isTemplateRecord = $this->isTemplateRecordRequest($request);
		$result = $response->getResult();

		$resultRecord = $result['record'];
		$relatedlistcontent = $result['relatedlistcontent'];
		$comments = $result['comments'];
		$module = $this->detectModuleName($resultRecord['id']);

		$modifiedRecord = $this->transformRecordWithGrouping($resultRecord, $module, $isTemplateRecord);
		$ret_arr = array('record' => $modifiedRecord);
		if (is_array ($relatedlistcontent)) {
			$ret_arr['relatedlistcontent'] = $relatedlistcontent;
		}
		if (isset($comments)) {
			$ret_arr['comments'] = $comments;
		}
		$response->setResult($ret_arr);
		return $response;
	}

	protected function transformRecordWithGrouping($resultRecord, $module, $isTemplateRecord=false) {
		$current_user = $this->getActiveUser();
		$moduleFieldGroups = Mobile_WS_Utils::gatherModuleFieldGroupInfo($module);
		$modifiedResult = array();
		$blocks = array();
		$labelFields = false;
		foreach($moduleFieldGroups as $blocklabel => $fieldgroups) {
			$fields = array();
			foreach($fieldgroups as $fieldname => $fieldinfo) {
				$value = $resultRecord[$fieldname];
				$fieldlabel = $fieldinfo['label'];

				// get field information
				if(isset($resultRecord[$fieldname])) {
					//get standard content & perform special settings
					if($fieldinfo['uitype'] == 17 && strlen($resultRecord[$fieldname]) ) {
						//www fields
						if ($_REQUEST['_operation'] =='edit') {
							$resultRecord[$fieldname]= $resultRecord[$fieldname];
						}
						else {
							$htmlstring = str_replace("http://", "", $resultRecord[$fieldname]);
							$resultRecord[$fieldname]= "<A HREF=\"#\" onclick=\"window.location.href ='http://" . $htmlstring . "';\" rel=external>"  . $htmlstring . "</A>";
						}
					}
					if($fieldinfo['uitype'] == 13 && strlen($resultRecord[$fieldname]) ) {
						// email fields
						if ($_REQUEST['_operation'] =='edit') {
							$resultRecord[$fieldname]= $resultRecord[$fieldname];
						}
						else {
							$resultRecord[$fieldname]= "<A HREF=\"#\" onclick=\"window.location.href ='mailto:" . $resultRecord[$fieldname] . "';\">"  . $resultRecord[$fieldname] . "</A>";
						}
					}
					if($fieldinfo['uitype'] == 72 && strlen($resultRecord[$fieldname]) ) {
						//currency fields
						$resultRecord[$fieldname]= round($resultRecord[$fieldname],2);
					}
					$field = array(
							'name'  => $fieldname,
							'value' => $resultRecord[$fieldname],
							'label' => $fieldinfo['label'],
							'uitype'=> $fieldinfo['uitype'],
							'typeofdata'=> $fieldinfo['typeofdata'],
							'quickcreate'=> $fieldinfo['quickcreate']
					);
					// Template record requested, send more details if available
					if ($isTemplateRecord) {
						$describeFieldInfo = $this->cachedDescribeFieldInfo($fieldname);
						foreach($describeFieldInfo as $k=>$v) {
							if (isset($field[$k])) continue;
							$field[$k] = $v;
						}
						// Entity fieldnames
						$labelFields = $this->cachedEntityFieldnames($module);
					}
					//handling for special UI types: modify $field
					if ($field['uitype'] == '53') {
						//assigned user
						global $adb;
						$output = array_chunk($value, 1);
						$recordarray=explode('x', $output[0][0]);
						$recordprefix=$recordarray[0];
						$value = $output[0][0];
						if($value != '' && $value != 0) {
							$assigned_user_id = $value;
						} else {
							$assigned_user_id = $current_user->id;
						}
						$fieldvalue = Mobile_WS_Utils::getassignedtoValues($current_user,$assigned_user_id);
						$field['type']['value'] = array('value' => $fieldvalue, 'name' =>$fieldname);
					//end UI 53
					}
					else if($field['uitype'] == '117') {
						$field['type']['defaultValue'] = $field['value'];
					}
					else if($field['uitype'] == '15' || $field['uitype'] == '16'  || $field['uitype'] == '33')   {
						//picklists
						global $adb;
						require_once 'modules/PickList/PickListUtils.php';
						$fieldvalue = Array();
						$options = array();
						$chk_val="";
						$roleid=$current_user->roleid;
						$picklistValues = getAssignedPicklistValues($fieldname, $roleid, $adb);
						$valueArr = explode("|##|", $value);
						$pickcount = 0;
						//get values
						if(!empty($picklistValues)){
							foreach($picklistValues as $order=>$pickListValue){
								if(in_array(trim($pickListValue),array_map("trim", $valueArr))){
									$chk_val = "selected";
									$pickcount++;
								}
								else {
									$chk_val = '';
								}
								if(isset($_REQUEST['file']) && $_REQUEST['file'] == 'QuickCreate'){
									$options[] = array('label'=> htmlentities(getTranslatedString($pickListValue),ENT_QUOTES,$default_charset),'value'=>$pickListValue,'selected'=>$chk_val );
								}
								else {
									$options[] = array('label'=>getTranslatedString($pickListValue),'value'=>$pickListValue,'selected'=>$chk_val);
								}
							}
							if($pickcount == 0 && !empty($value)){
								$options[] =  array('label'=>$app_strings['LBL_NOT_ACCESSIBLE'],'value'=>$value,'selected');
							}
						}
						$editview_label[]=getTranslatedString($fieldlabel, $module);
						if ($field['uitype'] == '33') {
							$field['value'] = implode ( ',' , $valueArr ) ;
						}

						$fieldvalue [] = $options;
						$field['type']['value'] =array('value' =>$options,'name' =>$fieldname);
					//end picklists
					}else if($field['uitype'] == '51' || $field['uitype'] == '59' || $field['uitype'] == '10'){
						$field['relatedmodule'] = Mobile_WS_Utils::getEntityName($field['name'], $module);
					}
					$fields[] = $field;
				}
			}
			// build address for "open address in maps" button
			// array with all different address fieldnames for each module
			$fieldnamesByModule = array(
				"Accounts" 		=> array("bill_street", "ship_street", "bill_city", "ship_city", "bill_state", "ship_state", "bill_code", "ship_code", "bill_country", "ship_country", "ship_address", "bill_address"),
				"SalesOrder" 	=> array("bill_street", "ship_street", "bill_city", "ship_city", "bill_state", "ship_state", "bill_code", "ship_code", "bill_country", "ship_country", "ship_address", "bill_address"),
				"Contacts" 		=> array("mailingstreet", "otherstreet", "mailingcity", "othercity", "mailingstate", "otherstate", "mailingzip", "otherzip", "mailingcountry", "othercountry", "mailingaddress", "otheraddress"),
				"Leads" 			=> array("lane", "", "city", "", "state", "", "code", "", "country", "", "mailingaddress", ""),
			);

			// get the right array depending on current module
			$fieldnames = $fieldnamesByModule[$module];
			/*
			0 = appears if fieldgroup is not address information
			1 = address values are set, show button
			-1 = city or street is missing, don't show the button and avoid set back to 1
			*/
			$mailingAddressOK = 0;
			$otherAddressOK = 0;
			$mailingAddress = "";
			$otherAddress = "";
			// go through all fields
			foreach($fieldgroups as $fieldname => $fieldinfo) {
				if(!is_array($resultRecord[$fieldname]) AND !is_object($resultRecord[$fieldname])) {
					$value = trim($resultRecord[$fieldname]);
					// check street and city for first address
					if($mailingAddressOK != -1 AND ($fieldname == $fieldnames[0] OR $fieldname == $fieldnames[2])) {
						$mailingAddressOK = 1;
						if(strlen($value)>0){
							$mailingAddress .= $value." ";
						}
						else {
							$mailingAddressOK = -1;
						}
					}
					// check street and city for second address
					else if($otherAddressOK != -1 AND ($fieldname == $fieldnames[1] OR $fieldname == $fieldnames[3])) {
						$otherAddressOK = 1;
						if(strlen($value)>0) {
							$otherAddress .= $value." ";
						}
						else {
							$otherAddressOK = -1;
						}
					}
					// check state and ZIP for first address
					else if(in_array($fieldname, array($fieldnames[4], $fieldnames[6])) AND strlen($value)>0) {
						$mailingAddress .= $value." ";
					}
					// check state and ZIP for second address
					else if(in_array($fieldname, array($fieldnames[5],$fieldnames[7])) AND strlen($value)>0) {
						$otherAddress .= $value." ";
					}
				}
			}
			if($mailingAddressOK == 1) {
				if($module == 'Contacts') {
					$label = getTranslatedString("address", "Mobile");
				} else {
					$label = getTranslatedString("bill_address", "Mobile");
				}
				$fields[] = array("name" => $fieldnames[10], "value" => $mailingAddress, "label" => $label, "uitype" => "crm_app_map", "typeofdata" => "O");
			}
			if($otherAddressOK == 1) {
				if($module == 'Contacts') {
					$label = getTranslatedString("otheraddress", "Mobile");
				}
				else {
					$label = getTranslatedString("ship_address", "Mobile");
				}
				$fields[] = array("name" => $fieldnames[11], "value" => $otherAddress, "label" => $label, "uitype" => "crm_app_map", "typeofdata" => "O");
			}
			$blocks[] = array( 'label' => $blocklabel, 'fields' => $fields );
		}
		$sections = array();
		$moduleFieldGroupKeys = array_keys($moduleFieldGroups);
		foreach($moduleFieldGroupKeys as $blocklabel) {
			// eliminate empty blocks
			if(isset($groups[$blocklabel]) && !empty($groups[$blocklabel])) {
				$sections[] = array( 'label' => $blocklabel, 'count' => count($groups[$blocklabel]) );
			}
		}
		$modifiedResult = array('blocks' => $blocks, 'id' => $resultRecord['id']);
		if($labelFields) {
			$modifiedResult['labelFields'] = $labelFields;
		}
		return $modifiedResult;
	}
}
