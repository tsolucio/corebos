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
function getFieldList($module_name, $field_name = '') {
	global $adb;
	$tabid = getTabid($module_name);

	$query = 'select fieldid,fieldname,fieldlabel from vtiger_field where tabid=?';
	$params = array($tabid);
	$query.= " and columnname not like 'imagename' and uitype not in (61, 122) and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query, $params);
	while ($fieldinfo = $adb->fetch_array($result)) {
		$fields[] = array (
			'fieldlabel' => getTranslatedString($fieldinfo['fieldlabel'], $module_name),
			'fieldname' => $fieldinfo['fieldname'],
			'fieldid' => $fieldinfo['fieldid']
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
	$relatedFieldsArray = array();
	foreach ($related_fields as $related_field) {
		$temp_relatedfield = array();
		$related_fieldid = $related_field['fieldid'];
		$related_fieldname = $related_field['fieldname'];
		$related_fieldlabel = $related_field['fieldlabel'];

		if (tooltip_exists($fieldid, $related_fieldid)) {
			$visible = 'checked';
		} else {
			$visible = '';
		}
		$temp_relatedfield['fieldlabel'] = $related_fieldlabel;
		$temp_relatedfield['input'] = "<input type='checkbox' value='$related_fieldid' name='$related_fieldid' $visible>";
		$temp_relatedfield['fieldid'] = $related_fieldid;
		$temp_relatedfield['fieldname'] = $related_fieldname;
		$relatedFieldsArray[] = $temp_relatedfield;
	}
	$relatedFieldsArray[] = array(
		'fieldlabel' => getTranslatedString('ModComments', 'ModComments'),
		'input' => "<input type='checkbox' value='-1' name='ModComments' ".(tooltip_exists($fieldid, -1) ? 'checked' : '').'>',
		'fieldid' => -1,
		'fieldname' => 'ModComments',
	);
	$relatedFieldsArray = array_chunk($relatedFieldsArray, 4);
	return $relatedFieldsArray;
}

/**
 * function to get the module names
 * @return - all module names other than Users
 */
function moduleList() {
	global $adb;
	$result = $adb->pquery(
		"select distinct vtiger_field.tabid,name from vtiger_field inner join vtiger_tab on vtiger_field.tabid=vtiger_tab.tabid where name != 'Users'",
		array ()
	);
	$modulelist = array();
	while ($moduleinfo = $adb->fetch_array($result)) {
		$modulelist[$moduleinfo['name']] = getTranslatedString($moduleinfo['name'], $moduleinfo['name']);
	}
	return $modulelist;
}

/**
 * this function determines if a given field has the related field already present in the tooltip
 */
function tooltip_exists($fieldid, $related_fieldid) {
	global $adb;
	$query = 'select * from vtiger_quickview where fieldid=? and related_fieldid=?';
	$result = $adb->pquery($query, array ($fieldid,$related_fieldid));
	return ($adb->num_rows($result) > 0);
}

/**
 * function to return the tooltip information
 * @param int $view - there can be multiple tooltips for a single module; this variable decides which is for which field
 * @param int $fieldname - field for which the tooltip has to be fetched
 * @param string $module - this is the module of the field
 * @param array $value - column fields with values of the current record
 * returns the tooltip string
 */
function getToolTipText($view, $fieldname, $module, $value) {
	global $adb;
	$keys = array_keys($value[0]);
	//getting the quickview list here
	$fieldlabel = array();
	require_once 'modules/Reports/ReportUtils.php';
	$fieldid = getFieldid(getTabid($module), $fieldname);
	$quickview = "select fieldname,fieldlabel,uitype,vtiger_quickview.sequence
		from vtiger_quickview
		inner join vtiger_field on vtiger_quickview.related_fieldid=vtiger_field.fieldid
		where vtiger_quickview.fieldid=? and currentview=? and vtiger_field.presence in (0,2)
		UNION
		select 'ModComments','ModComments',1,2000
		from vtiger_quickview
		where vtiger_quickview.fieldid=? and currentview=? and vtiger_quickview.related_fieldid=-1
		order by 4";
	$result = $adb->pquery($quickview, array($fieldid, $view, $fieldid, $view));
	$count = $adb->num_rows($result);
	$text=array();
	for ($i=0; $i<$count; $i++) {
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		if (in_array($fieldname, $keys)) {
			$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
			$label = getTranslatedString($fieldlabel, $module);
			$fieldvalue = $value[0][$fieldname];
			if (empty($fieldvalue)) {
				$fieldvalue = '&nbsp;';
			}
			$fieldvalue = textlength_check($fieldvalue, GlobalVariable::getVariable('ToolTip_MaxFieldValueLength', 35, $module));
			$uitype = $adb->query_result($result, $i, 'uitype');
			if ($uitype==17) { // website
				$fieldvalue = '<a href="//'.$value[0][$fieldname].'" target=_blank>'.$fieldvalue.'</a>';
			}
			if (($uitype==10 || isReferenceUIType($uitype)) && !empty($value[0][$fieldname])) {
				list($fieldvalue,$wsid) = explode('::::', $value[0][$fieldname]);
				list($wsmod,$crmid) = explode('x', $wsid);
				$relmodule = getSalesEntityType($crmid);
				$fieldvalue = '<a href="index.php?module='.$relmodule.'&action=DetailView&record='.$crmid.'" target=_blank>'.$fieldvalue.'</a>';
			}
			$text[$label] = $fieldvalue;
		} elseif ($fieldname=='ModComments') {
			list($wsmod, $crmid) = explode('x', $value[0]['id']);
			$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Accounts');
			$query = 'SELECT vtiger_crmentity.smownerid, vtiger_modcomments.commentcontent, vtiger_crmentity.modifiedtime
				FROM vtiger_modcomments'
				.' INNER JOIN '.$crmEntityTable.' ON vtiger_crmentity.crmid=vtiger_modcomments.modcommentsid'
				.' LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted=0 AND vtiger_modcomments.related_to=?
				ORDER BY vtiger_crmentity.createdtime desc LIMIT '.GlobalVariable::getVariable('ToolTip_NumberOfComments', 5, $module);
			$rs = $adb->pquery($query, array($crmid));
			$coms = '<br><div id="ttmodcomments" style="max-height:210px;overflow-y:auto;">';
			$i18nAuthor = getTranslatedString('LBL_AUTHOR', 'ModComments');
			$i18nOn = getTranslatedString('LBL_ON_DATE', 'ModComments');
			while ($row=$adb->fetch_array($rs)) {
				$coms .= '<div class="dataField" style="width: 80%; padding-top: 5px;" valign="top">'.nl2br($row['commentcontent']).'</div>
				<div class="dataLabel" style="border-bottom: 1px dotted rgb(204, 204, 204); width: 80%; padding-bottom: 5px;" valign="top">
					<span style="color:darkred;">'.$i18nAuthor.': '.getUserName($row['smownerid']).' '.$i18nOn.' '.$row['modifiedtime'].'</span>
				</div>';
				if ($row['commentcontent'] != '') {
					$text['ModComments'] = true;
				}
			}
			$text[getTranslatedString('ModComments', 'ModComments')] = $coms.'</div>';
		}
	}
	return $text;
}

/**
 * this function accepts the tooltip text and returns it after formatting
 * @param $text - the tooltip text which is to be formatted
 * @param $format - the format in which tooltip has to be formatted; default value will be each entry in single line
 */
function getToolTip($text, $format = 'default') {
	require_once 'Smarty_setup.php';
	$smarty = new vtigerCRM_Smarty;
	$tip = '';
	if (trim(implode('', $text)) == '') {
		return $tip;
	}
	$smarty->assign('TEXT', $text);
	return $smarty->fetch("modules/Tooltip/$format.tpl");
}

/**
 * this function checks if tooltip exists for a given field or not
 */
function ToolTipExists($fieldname, $tabid) {
	if (empty($fieldname) || empty($tabid)) {
		return false;
	} else {
		global $adb;
		$sql = 'select fieldid from vtiger_field where tabid = ? and fieldname = ? and vtiger_field.presence in (0,2)';
		$result = $adb->pquery($sql, array($tabid,$fieldname));
		$count = $adb->num_rows($result);
		if ($count > 0) {
			$fieldid = $adb->query_result($result, 0, 'fieldid');
			$result = $adb->pquery('select * from vtiger_quickview where fieldid = ?', array($fieldid));
			if ($adb->num_rows($result) > 0) {
				return $fieldid;
			} else {
				return false;
			}
		} else {
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
function vttooltip_processResult($result, $descObj) {
	global $current_user;
	foreach ($descObj['fields'] as $field) {
		$name = $field['name'];
		$value = $result[0][$name];
		if ($field['type']['name'] == 'reference') {
			$name = $field['name'];
			if (!empty($value)) {
				$result[0][$name] = vtws_getName($value, $current_user).'::::'.$value;
			} else {
				$result[0][$name] = '';
			}
		} elseif ($field['type']['name'] == 'owner') {
			list($info, $id) = explode('x', $value);
			$result[0][$name] = getOwnerName($id);
		} elseif ($field['type']['name'] == 'boolean') {
			if ($result[0][$name] == 1) {
				$result[0][$name] = 'on';
			} else {
				$result[0][$name] = 'off';
			}
		} elseif ($field['type']['name'] == 'picklist') {
			$temp = '';
			foreach ($field['type']['picklistValues'] as $value) {
				if (strcmp($value['value'], $result[0][$name])== 0) {
					$temp = $value['value'];
				}
			}
			$result[0][$name] = $temp;
		} elseif ($field['type']['name'] == 'date') {
			$result[0][$name] = DateTimeField::convertToUserFormat($value);
		} elseif ($field['type']['name'] == 'datetime') {
			$date = new DateTimeField($value);
			$result[0][$name] = $date->getDisplayDateTimeValue();
		} elseif ($field['type']['name'] == 'time') {
			$date = new DateTimeField($value);
			$result[0][$name] = $date->getDisplayTime();
		} elseif ($field['type']['name'] == 'currency') {
			$currencyField = new CurrencyField($value);
			$result[0][$name] = $currencyField->getDisplayValueWithSymbol();
		}
	}
	return $result;
}

/**
 * this function returns the fields for a given module in a select dropdown format
 * @param string $module - the module name
 * @return string the fields in a select dropdown if fields exist else a blank value
 */
function QuickViewFieldList($module) {
	global $adb, $app_strings,$mod_strings;

	$tabid = getTabid($module);
	$query = "select fieldname,fieldlabel from vtiger_field where tabid=? and columnname not like 'imagename' and uitype not in (61, 122) and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query, array($tabid));
	if ($adb->num_rows($result)>0) {
		$fieldlist = '<select onchange="getRelatedFieldInfo(this)" class="importBox" id="pick_field" name="pick_field">';
		$fieldlist.= '<option value="" disabled="true" selected>'.$app_strings['LBL_SELECT'].' '. $mod_strings['LBL_FIELD'].'</option>';
		while ($fieldsinfo=$adb->fetch_array($result)) {
			$fieldlabel = $fieldsinfo['fieldlabel'];
			$fieldname = $fieldsinfo['fieldname'];
			$fieldlist.= "<option value='$fieldname'>".getTranslatedString($fieldlabel, $module).'</option>';
		}
		$fieldlist.= '</select>';
		return $fieldlist;
	} else {
		return '';
	}
}
?>
