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
include_once 'include/Webservices/DescribeObject.php';
include_once dirname(__FILE__) . '/FetchRecord.php';
include_once dirname(__FILE__) . '/Describe.php';

class crmtogo_WS_FetchRecordDetails extends crmtogo_WS_FetchRecord {
	
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
	
	function process(crmtogo_API_Request $request) {
		$operation = $request->getOperation();
		if ($operation =='create') {
			$newrecord = crmtogo_WS_Describe::process($request);
			$response = new crmtogo_API_Response();
			$response->setResult(array('record' => $newrecord));
			return $response;
		}
		else {
			$response = parent::process($request);
			return $this->processWithGrouping($request, $response);
		}
	}
	
	protected function processWithGrouping(crmtogo_API_Request $request, $response) {
		$result = $response->getResult();
		$operation = $request->getOperation();
		$resultRecord = $result['record'];
		$relatedlistcontent = $result['relatedlistcontent'];
		$comments = $result['comments'];
		$module = $this->detectModuleName($resultRecord['id']);
		//set download pathinfo
		
		$modifiedRecord = $this->transformRecordWithGrouping($resultRecord, $module,$operation);
		$ret_arr = array('record' => $modifiedRecord);
		if (is_array ($relatedlistcontent)) {
			$ret_arr['relatedlistcontent'] = $relatedlistcontent;
		}
		if (isset($comments)) {
			$ret_arr['comments'] = $comments;
		}
		if (isset($resultRecord['attachmentinfo']) and $resultRecord['attachmentinfo']!='') {
			$ret_arr['attachmentinfo'] = $resultRecord['attachmentinfo'];
		}
		$response->setResult($ret_arr);
		return $response;
	}
	
	protected function transformRecordWithGrouping($resultRecord, $module, $operation='') {
		$current_user = $this->getActiveUser();
		$moduleFieldGroups = crmtogo_WS_Utils::gatherModuleFieldGroupInfo($module);
		$modifiedResult = array();
		$blocks = array(); 
		$labelFields = false;

		if($module == 'Events' || $module == 'Calendar') {
			// sets times & dates to local time zone and format
			$date = new DateTimeField($resultRecord['date_start'].' '.$resultRecord['time_start']);
			$startDateTime = $date->getDisplayDateTimeValue();	
			$startDateTimeArray = explode(' ', $startDateTime);
			$date = new DateTimeField($resultRecord['due_date']." ".$resultRecord['time_end']);
			$endDateTime = $date->getDisplayDateTimeValue();	
			$endDateTimeArray = explode(' ', $endDateTime);
			if ($operation =='edit') {
				//dates always in yyyy-mm-dd format
				//needed for strtotime for none European formats
				if ($current_user->date_format != 'dd-mm-yyyy') {
					$formated_date = str_replace('-', '/', $startDateTimeArray[0]);
				}
				else {
					$formated_date =$startDateTimeArray[0];
				}
				$resultRecord['date_start'] = date("Y-m-d",strtotime($formated_date));
				//remove trailing seconds
				$time_arr = explode(':', $startDateTimeArray[1]);
				$resultRecord['time_start'] =  $time_arr[0].':'.$time_arr[1];
				
				if ($current_user->date_format != 'dd-mm-yyyy') {
					$formated_date = str_replace('-', '/', $endDateTimeArray[0]);
				}
				else {
					$formated_date =$endDateTimeArray[0];
				}
				$resultRecord['due_date'] = date("Y-m-d",strtotime($formated_date));
				//remove trailing seconds
				$time_arr = explode(':', $endDateTimeArray[1]);
				$resultRecord['time_end'] = $time_arr[0].':'.$time_arr[1];
			}
			else {
				$resultRecord['date_start'] = $startDateTimeArray[0];
				$time_arr = explode(':', $startDateTimeArray[1]);
				$resultRecord['time_start'] =  $time_arr[0].':'.$time_arr[1];
				// format end times to local timezone and hour format
				$resultRecord['due_date'] = $endDateTimeArray[0];
				$time_arr = explode(':', $endDateTimeArray[1]);
				$resultRecord['time_end'] = $time_arr[0].':'.$time_arr[1];
				if ($current_user->hour_format == '12') {
					// add AM/PM to time strings
					$resultRecord['time_start'] .= " ".$startDateTimeArray[2]; 
					$resultRecord['time_end'] .= " ".$endDateTimeArray[2]; 
				}
			}
		}
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
						if ($operation =='edit') {
							$resultRecord[$fieldname]= $resultRecord[$fieldname];
						}
						else {
							$htmlstring = str_replace("http://", "", $resultRecord[$fieldname]);
							$resultRecord[$fieldname]= "<A HREF=\"#\" onclick=\"window.open('http://" . $htmlstring . "','_blank');\" rel=external>"  . $htmlstring . "</A>";
						}
					}
					if($fieldinfo['uitype'] == 13 && strlen($resultRecord[$fieldname]) ) {
						// email fields
						if ($operation =='edit') {
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
					if($fieldinfo['uitype'] == 83 && strlen($resultRecord[$fieldname]) ) {
						//tax fields
						$resultRecord[$fieldname]= round($resultRecord[$fieldname],2);
					}
					if($fieldinfo['uitype'] == 69 && strlen($resultRecord[$fieldname]) ) {
						//image --> get it base64 coded
						if ($module =='Contacts') {
							$resultRecord[$fieldname]= crmtogo_WS_Utils::getContactBase64Image($resultRecord['id']);
						}
						elseif ($module =='Products') {
							$resultRecord[$fieldname]= crmtogo_WS_Utils::getProductBase64Image($resultRecord['id']);
						}
						else {
							$resultRecord[$fieldname]= '';
						}
					}
					$field = array(
							'name'  => $fieldname,
							'value' => $resultRecord[$fieldname],
							'label' => $fieldinfo['label'],
							'uitype'=> $fieldinfo['uitype'],
							'typeofdata'=> $fieldinfo['typeofdata'],
							'displaytype'=> $fieldinfo['displaytype'],
							'mandatory'=> $fieldinfo['mandatory']
					);
					//handling for special UI types: modify $field
					if ($field['uitype'] == '53') {
						//assigned user
						$output = array_chunk($value, 1);
						$recordarray=explode('x', $output[0][0]);
						$recordprefix=$recordarray[0];
						$value = $output[0][0];
		                if($value != '' && $value != 0) {
			                    $assigned_user_id = $value;
						}		
	                    else {
							$assigned_user_id = $current_user->id;
						}		
						$fieldvalue = crmtogo_WS_Utils::getassignedtoValues($current_user,$module,$assigned_user_id);
			            $field['type']['value'] = array('value' => $fieldvalue, 'name' =>$fieldname);
					//end UI 53
					} 
					else if($field['uitype'] == '117') {
						$field['type']['defaultValue'] = $field['value'];
					} 
					else if($field['uitype'] == '15' || $field['uitype'] == '16'  || $field['uitype'] == '33')   {
						//picklists
						$fieldvalue = Array();
						$options = array();
						$chk_val="";
						$picklistValues = vtlib_getPicklistValues($fieldname);
						foreach($picklistValues as $pickkey => $pickvalue) {
							$picklistValues[$pickkey] = decode_html($pickvalue);
						}
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
								$options[] = array('label'=>getTranslatedString($pickListValue, $module),'value'=>$pickListValue,'selected'=>$chk_val);
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
					}
					else if($field['uitype'] == '51' || $field['uitype'] == '59' || $field['uitype'] == '10'){
						$field['relatedmodule'] = crmtogo_WS_Utils::getEntityName($field['name'], $module);
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
				} 
				else {
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
