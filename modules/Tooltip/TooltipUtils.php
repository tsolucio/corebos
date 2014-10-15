<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/Query.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/Webservices/ModuleTypes.php';

/**
 * this function returns the fields for a given module
 */
function getFieldList($module_name, $field_name = "") {
	global $adb;
	$tabid = getTabid($module_name);

	$query = "select * from vtiger_field where tabid = ?";
	$params = array($tabid);
	if (!empty ($field_name)) {
		$query .= " and fieldname not like ?";
		$params = array($tabid,$field_name);
	}
	$query.= " and columnname not like 'imagename' and uitype not in (61, 122) and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query, $params);
	while ($fieldinfo = $adb->fetch_array($result)) {
		$fields[] = array (
			"fieldlabel" => getTranslatedString($fieldinfo['fieldlabel'], $module_name),
			"fieldname" => $fieldinfo['fieldname'],
			"fieldid" => $fieldinfo['fieldid']
		);
	}
	return $fields;
}

/** 
 * this function returns the fields related to a field
 * @param array $result -- mysql query result that contains the field information
 * @param array $lang_strings -- language strings array
 */
function getRelatedFieldsList($fieldid, $related_fields) {
	global $adb;
	$relatedFieldsArray = array();
	foreach ($related_fields as $related_field) {
		$temp_relatedfield = array();
		$related_fieldid = $related_field['fieldid'];
		$related_fieldname = $related_field['fieldname'];
		$related_fieldlabel = $related_field['fieldlabel'];

		if (tooltip_exists($fieldid, $related_fieldid)) {
			$visible = "checked";
		} else {
			$visible = "";
		}
		$temp_relatedfield['fieldlabel'] = $related_fieldlabel;
		$temp_relatedfield['input'] = "<input type='checkbox' value='$related_fieldid' name='$related_fieldid' $visible>";
		$temp_relatedfield['fieldid'] = $related_fieldid;
		$temp_relatedfield['fieldname'] = $related_fieldname;
		$relatedFieldsArray[] = $temp_relatedfield;
	}

	$relatedFieldsArray = array_chunk($relatedFieldsArray, 4);
	return $relatedFieldsArray;
}

/**
 * function to get the module names
 * @return - all module names other than Calendar and Events
 */
function moduleList() {
	global $adb;
	$sql = "select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where name not in ('Calendar','Events','Users')";
	$result = $adb->pquery($sql, array ());
	while ($moduleinfo = $adb->fetch_array($result)) {
		$modulelist[$moduleinfo['name']] = getTranslatedString($moduleinfo['name']);
	}
	return $modulelist;
}

/**
 * this function determines if a given field has the related field already present in the tooltip
 */
function tooltip_exists($fieldid, $related_fieldid) {
	global $adb;
	$query = "select * from vtiger_quickview where fieldid=? and related_fieldid=?";
	$result = $adb->pquery($query, array ($fieldid,$related_fieldid));

	if ($adb->num_rows($result) > 0) {
		return true;
	} else {
		return false;
	}
}

/**
 * function to return the tooltip information
 * @param int $view - there can be multiple tooltips for a single module; this variable decides which is for which field
 * @param int $tabid - tabid of the field for which the tooltip has to be fetched
 * @param int $id - this  is the crmid of the record
 * returns the tooltip string 
 */
function getToolTipText($view,$fieldname,$module,$value){
	global $adb,$app_strings;
	$keys = array_keys($value[0]);
	//getting the quickview list here
	$fieldlabel = Array();
	$fieldid = getFieldid(getTabid($module), $fieldname);
	$quickview = 'select fieldname,fieldlabel from vtiger_quickview inner join vtiger_field on vtiger_quickview.related_fieldid=vtiger_field.fieldid where vtiger_quickview.fieldid = ? and currentview= ? and vtiger_field.presence in (0,2) order by vtiger_quickview.sequence';
	$result = $adb->pquery($quickview,array($fieldid,$view));
	$count = $adb->num_rows($result);
	
	$text=array();
	$fieldname = Array();
	for($i=0;$i<$count;$i++){
		$fieldname = $adb->query_result($result,$i,"fieldname");
		
		if(in_array($fieldname, $keys)){
			$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
			$label = getTranslatedString($fieldlabel,$module);
			$fieldvalue = $value[0][$fieldname];
			if(empty($fieldvalue)) {
				$fieldvalue = '&nbsp;';
			}
			if(strlen($fieldvalue)>35){
				$fieldvalue = substr($fieldvalue,0,35).'...';
			}
			$text[$label] = $fieldvalue;
		}
	}
	return $text;
}

/**
 * this function accepts the tooltip text and returns it after formatting
 * @param $text - the tooltip text which is to be formatted
 * @param $format - the format in which tooltip has to be formatted; default value will be each entry in single line
 */
function getToolTip($text,$format = "default"){
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty;
	$tip = "";
	if(trim(implode('', $text)) == ''){
		return $tip;
	}
	
	$smarty->assign("TEXT",$text);
	$tip = $smarty->fetch("modules/Tooltip/$format.tpl");
	return $tip;
}

/**
 * this function checks if tooltip exists for a given field or not
 */
function ToolTipExists($fieldname,$tabid){
	if(empty($fieldname) || empty($tabid)){
		return false;
	}else{
		global $adb;
		$sql = "select fieldid from vtiger_field where tabid = ? and fieldname = ? and vtiger_field.presence in (0,2)";
		$result = $adb->pquery($sql,array($tabid,$fieldname));
		$count = $adb->num_rows($result);
		if($count > 0){
			$fieldid = $adb->query_result($result,0,'fieldid');
		
			$sql = "select * from vtiger_quickview where fieldid = ?";
			$result = $adb->pquery($sql, array($fieldid));
		
			if($adb->num_rows($result) > 0){
				return $fieldid;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
}

/**
 * this function processes the given result and returns the value :: for now we are getting the values for the
 * reference, owner fields, booleans and currency fields; other processing might be added later if required
 * @param array $result - the webservices result object
 * @param array $descObj - the webservices describe object
 * @return array $result - the processes webservices result object
 */
function vttooltip_processResult($result, $descObj){
	global $current_user;
	foreach($descObj['fields'] as $field){
		$name = $field['name'];
		$value = $result[0][$name];
		if($field['type']['name'] == 'reference'){
			$name = $field['name'];
			
			if(!empty($value)){
				$result[0][$name] = vtws_getName($value,$current_user);
			}else{
				$result[0][$name] = '';
			}
		}elseif($field['type']['name'] == 'owner'){
			list($info, $id) = explode("x",$value);
			$result[0][$name] = getOwnerName($id);
		}elseif($field['type']['name'] == 'boolean'){
			if($result[0][$name] == 1){
				$result[0][$name] = "on";
			}else{
				$result[0][$name] = "off";
			}
		}elseif($field['type']['name'] == 'picklist'){
			$temp = '';
			foreach($field['type']['picklistValues'] as $value){
				if(strcmp($value['value'],$result[0][$name])== 0){
					$temp = $value['value'];
				}
			}
			$result[0][$name] = $temp;
		} elseif($field['type']['name'] == 'date') {
			$result[0][$name] = DateTimeField::convertToUserFormat($value);
		} elseif($field['type']['name'] == 'datetime') {
			$date = new DateTimeField($value);
			$result[0][$name] = $date->getDisplayDateTimeValue();
		} elseif($field['type']['name'] == 'time') {
			$date = new DateTimeField($value);
			$result[0][$name] = $date->getDisplayTime();
		} elseif($field['type']['name'] == 'currency') {
			$currencyField = new CurrencyField($value);
			$result[0][$name] = $currencyField->getDisplayValueWithSymbol();
		}
	}
	return $result;
}


/**
 * this function returns the fields for a given module in a select dropdown format
 * @param string $module - the module name
 * @return the fields in a select dropdown if fields exist else a blank value
 */
function QuickViewFieldList($module){
	global $adb, $app_strings,$mod_strings;
	
	$tabid = getTabid($module);
	
	$query = "select * from vtiger_field where tabid = ? and columnname not like 'imagename' and uitype not in (61, 122) and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query,array($tabid));
	if($adb->num_rows($result)>0){
		$fieldlist = '<select onchange="getRelatedFieldInfo(this)" class="importBox" id="pick_field" name="pick_field">';
		$fieldlist.= 	'<option value="" disabled="true" selected>'
							.$app_strings['LBL_SELECT'].' '. $mod_strings['LBL_FIELD'].'
						</option>';
		while($fieldsinfo=$adb->fetch_array($result)){
			$fieldlabel = $fieldsinfo['fieldlabel'];
			$fieldname = $fieldsinfo['fieldname'];
			$fieldlist.= "<option value='$fieldname'>".getTranslatedString($fieldlabel, $module)."</option>";
		}
		$fieldlist.= '</select>';
		return $fieldlist;
	}else{
		return '';
	}
}

?>
