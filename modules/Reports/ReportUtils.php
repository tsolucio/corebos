<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 *  ("License"); You may not use this file except in compliance with the License
 *  The Original Code is:  vtiger CRM Open Source
 *  The Initial Developer of the Original Code is vtiger.
 *  Portions created by vtiger are Copyright (C) vtiger.
 *  All Rights Reserved.
 ******************************************************************************** */

/**
 * Function to get the field information from module name and field label
 */
function getFieldByReportLabel($module, $label) {
	// this is required so the internal cache is populated or reused.
	getColumnFields($module);
	//lookup all the accessible fields
	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
	if ($module == 'Calendar') {
		$cachedEventsFields = VTCacheUtils::lookupFieldInfo_Module('Events');
		if ($cachedModuleFields == false) {
			$cachedModuleFields = $cachedEventsFields;
		} else {
			$cachedModuleFields = array_merge($cachedModuleFields, $cachedEventsFields);
		}
	}
	if (empty($cachedModuleFields)) {
		return null;
	}
	$label = str_replace('&', 'and', decode_html($label));
	foreach ($cachedModuleFields as $fieldInfo) {
		$fieldLabel = str_replace(' ', '_', $fieldInfo['fieldlabel']);
		$fieldLabel = str_replace('&', 'and', $fieldLabel);
		if ($label == $fieldLabel) {
			return $fieldInfo;
		}
	}
	return null;
}

function isReferenceUIType($uitype) {
	static $options = array('101', '117', '26', '357', '51', '52', '53', '57', '66', '73', '76', '77', '78', '80');
	return in_array($uitype, $options);
}

function isPicklistUIType($uitype) {
	static $options = array('15','16','1613','1614','1615','33','3313','3314','1024');
	return in_array($uitype, $options);
}

/**
 *
 * @global Users $current_user
 * @param ReportRun $report
 * @param Array $picklistArray
 * @param ADOFieldObject $dbField
 * @param Array $valueArray
 * @param String $fieldName
 * @return String
 */
function getReportFieldValue($report, $picklistArray, $dbField, $valueArray, $fieldName) {
	global $current_user;

	$db = PearDatabase::getInstance();
	$value = $valueArray[$fieldName];
	$fld_type = $dbField->type;
	if ($dbField->name=='LBL_ACTION') {
		$module = 'Reports';
		$fieldLabel = 'LBL_ACTION';
	} elseif (strpos($dbField->name, '_')) {
		list($module, $fieldLabel) = explode('_', $dbField->name, 2);
	} else {
		$module = $report->primarymodule;
		$fieldLabel = $dbField->name;
	}
	$fieldInfo = getFieldByReportLabel($module, $fieldLabel);
	$fieldType = null;
	$fieldvalue = $value;
	if (!empty($fieldInfo)) {
		$field = WebserviceField::fromArray($db, $fieldInfo);
		$fieldType = $field->getFieldDataType();
	}

	if ($fieldType == 'currency' && $value != '') {
		// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
		if ($field->getUIType() == '72') {
			$curid_value = explode("::", $value);
			$currency_id = $curid_value[0];
			$currency_value = $curid_value[1];
			$cur_sym_rate = getCurrencySymbolandCRate($currency_id);
			if ($value!='') {
				$formattedCurrencyValue = CurrencyField::convertToUserFormat($currency_value, null, true);
				$fieldvalue = CurrencyField::appendCurrencySymbol($formattedCurrencyValue, $cur_sym_rate['symbol']);
			}
		} else {
			$currencyField = new CurrencyField($value);
			$fieldvalue = $currencyField->getDisplayValue();
		}
	} elseif ($dbField->name == "PurchaseOrder_Currency" || $dbField->name == "SalesOrder_Currency"
				|| $dbField->name == "Invoice_Currency" || $dbField->name == "Quotes_Currency" || $dbField->name == "PriceBooks_Currency") {
		if ($value!='') {
			$fieldvalue = getTranslatedCurrencyString($value);
		}
	} elseif ((in_array($dbField->name, $report->ui101_fields) || (isset($field) && $field->getUIType() == '52')) && !empty($value)) {
		if (is_numeric($value)) {
			$entityNames = getEntityName('Users', $value);
			$fieldvalue = $entityNames[$value];
		} else {
			$fieldvalue = $value;
		}
	} elseif ($fieldType == 'date' && !empty($value)) {
			$fieldvalue = DateTimeField::convertToUserFormat($value);
	} elseif ($fieldType == "datetime" && !empty($value)) {
		$date = new DateTimeField($value);
		$fieldvalue = $date->getDisplayDateTimeValue();
		$user_format = ($current_user->hour_format=='24' ? '24' : '12');
		if ($user_format != '24') {
			$curr_time = DateTimeField::formatUserTimeString($fieldvalue, '12');
			list($dt,$tm) = explode(' ', $fieldvalue);
			$fieldvalue = $dt . ' ' . $curr_time;
		}
	} elseif ($fieldType == "picklist" && !empty($value)) {
		$fieldvalue = getTranslatedString($value, $module);
	} elseif ($fieldType == "multipicklist" && !empty($value)) {
		if (count($picklistArray)>0 && is_array($picklistArray[1])) {
			$valueList = explode(' |##| ', $value);
			$translatedValueList = array();
			foreach ($valueList as $value) {
				if (is_array($picklistArray[1][$dbField->name]) && !in_array($value, $picklistArray[1][$dbField->name])) {
					continue;
				}
				$translatedValueList[] = getTranslatedString($value, $module);
			}
		}
		if (count($picklistArray)==0 || !is_array($picklistArray[1]) || !is_array($picklistArray[1][$dbField->name])) {
			$fieldvalue = str_replace(' |##| ', ', ', $value);
		} else {
			$fieldvalue = implode(', ', $translatedValueList);
		}
	} elseif ($fieldType == "multireference" && !empty($value)) {
		require_once 'modules/PickList/PickListUtils.php';
		$content = getPicklistValuesSpecialUitypes($field->getUIType(), $field->getFieldName(), $value, 'DetailView');
		$fieldvalue = strip_tags(implode(', ', $content));
	} elseif ($fieldInfo['uitype'] == '1616' && !empty($value)) {
		global $adb;
		$cvrs = $adb->pquery('select viewname,entitytype from vtiger_customview where cvid=?', array($value));
		if ($cvrs && $adb->num_rows($cvrs)>0) {
			$cv = $adb->fetch_array($cvrs);
			$fieldvalue = $cv['viewname'].' ('.getTranslatedString($cv['entitytype'], $cv['entitytype']).')';
		} else {
			$fieldvalue = $value;
		}
	}
	if ($fieldvalue == "") {
		return "-";
	}
	$fieldvalue = str_replace("<", "&lt;", $fieldvalue);
	$fieldvalue = str_replace(">", "&gt;", $fieldvalue);
	$fieldvalue = decode_html($fieldvalue);

	if (stristr($fieldvalue, "|##|") && empty($fieldType)) {
		$fieldvalue = str_ireplace(' |##| ', ', ', $fieldvalue);
	} elseif ($fld_type == "date" && empty($fieldType)) {
		$fieldvalue = DateTimeField::convertToUserFormat($fieldvalue);
	} elseif ($fld_type == "datetime" && empty($fieldType)) {
		$date = new DateTimeField($fieldvalue);
		$fieldvalue = $date->getDisplayDateTimeValue();
	}
	if ($module != 'Reports' && vtlib_isModuleActive($module)) {
		$modobj = CRMEntity::getInstance($module);
		if (!empty($valueArray['lbl_action']) && method_exists($modobj, 'formatValueForReport')) {
			$fieldvalue = $modobj->formatValueForReport($dbField, $fieldType, $value, $fieldvalue, $valueArray['lbl_action']);
		}
	}
	return $fieldvalue;
}

function report_getMoreInfoFromRequest($cbreporttype, $pmodule, $smodule, $pivotcolumns) {
	global $adb;
	if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='external') {
		if (isset($_REQUEST['adduserinfo']) && ($_REQUEST['adduserinfo'] == 'on' || $_REQUEST['adduserinfo'] == 1)) {
			$aui = 1;
		} else {
			$aui = 0;
		}
		$minfo = serialize(array(
			'url' => vtlib_purify($_REQUEST['externalurl']),
			'adduserinfo' => $aui,
		));
		$cbreporttype = 'external';
	} elseif (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='directsql') {
		$minfo = vtlib_purify($_REQUEST['directsqlcommand']);
		$cbreporttype = 'directsql';
	} elseif (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='crosstabsql') {
		require_once 'include/adodb/pivottable.inc.php';
		$pmod = CRMEntity::getInstance($pmodule);
		$smod = CRMEntity::getInstance($smodule);
		$moduleInstance = Vtiger_Module::getInstance($pmodule);
		$refs = $moduleInstance->getFieldsByType('reference');
		$found = false;
		foreach ($refs as $field) {
			$rs = $adb->pquery('select relmodule from vtiger_fieldmodulerel where fieldid=?', array($field->id));
			$relmod = $adb->query_result($rs, 0, 0);
			if ($relmod==$smodule) {
				$found = $field;
				break;
			}
		}
		$reljoin = $pmod->table_name . '.' . $found->column . ' = ' . $smod->table_name . '.' . $smod->table_index;
		$colinfo = explode(':', $_REQUEST['pivotfield']);
		$pivotfield = $colinfo[0].'.'.$colinfo[1];
		$colinfo = explode(':', $_REQUEST['aggfield']);
		$aggfield = $colinfo[0].'.'.$colinfo[1];
		switch ($_REQUEST['crosstabaggfunction']) {
			case 'sum':
				$agglabel = getTranslatedString('LBL_COLUMNS_SUM', 'Reports');
				break;
			case 'avg':
				$agglabel = getTranslatedString('LBL_COLUMNS_AVERAGE', 'Reports');
				break;
			case 'min':
				$agglabel = getTranslatedString('LBL_COLUMNS_LOW_VALUE', 'Reports');
				break;
			case 'max':
				$agglabel = getTranslatedString('LBL_COLUMNS_LARGE_VALUE', 'Reports');
				break;
			default:
				$aggfield = false;
				$agglabel = 'Sum';
				$_REQUEST['crosstabaggfunction'] = 'sum';
				break;
		}
		$sql = PivotTableSQL(
			$adb->database, // adodb connection
			$pmod->table_name.',vtiger_crmentity,'.$smod->table_name, // tables
			$pivotcolumns, // rows (multiple fields allowed)
			$pivotfield, // column to pivot on
			$pmod->table_name.'.'.$pmod->table_index.'=vtiger_crmentity.crmid and vtiger_crmentity.deleted=0 and '.$reljoin, // joins/where
			$aggfield,
			$agglabel,
			vtlib_purify($_REQUEST['crosstabaggfunction'])
		);
		$minfo = serialize(array(
			'pivotfield' => vtlib_purify($_REQUEST['pivotfield']),
			'aggfield' => vtlib_purify($_REQUEST['aggfield']),
			'crosstabaggfunction' => vtlib_purify($_REQUEST['crosstabaggfunction']),
			'sql' => $sql
		));
		$cbreporttype = 'crosstabsql';
	} else {
		$minfo = '';
		$cbreporttype='corebos';
	}
	return array(
		$cbreporttype,
		$minfo
	);
}

/** Function to get visible criteria for a report
 *  This function accepts The reportid as an argument
 *  It returns an array of selected option of sharing along with other options
 */
function getVisibleCriteria($recordid = '', $selectedBoolean = true) {
	global $adb;
	$filter = array();
	$selcriteria = '';
	if ($recordid!='') {
		$result = $adb->pquery('select sharingtype from vtiger_report where reportid=?', array($recordid));
		$selcriteria=$adb->query_result($result, 0, 'sharingtype');
	}
	if ($selcriteria == '') {
		$selcriteria = 'Public';
	}
	$filter_result = $adb->query('select name from vtiger_reportfilters');
	$numrows = $adb->num_rows($filter_result);
	for ($j=0; $j<$numrows; $j++) {
		$filtername = $adb->query_result($filter_result, $j, 'name');
		if ($filtername == 'Private') {
			$FilterKey='Private';
			$FilterValue=getTranslatedString('PRIVATE_FILTER');
		} elseif ($filtername=='Shared') {
			$FilterKey='Shared';
			$FilterValue=getTranslatedString('SHARE_FILTER');
		} else {
			$FilterKey='Public';
			$FilterValue=getTranslatedString('PUBLIC_FILTER');
		}
		$shtml['value'] = $FilterKey;
		$shtml['label'] = $FilterValue;
		$shtml['text'] = $FilterValue;
		if ($FilterKey == $selcriteria) {
			$shtml['selected'] = ($selectedBoolean ? true : 'selected');
		} else {
			$shtml['selected'] = ($selectedBoolean ? false : '');
		}
		$filter[] = $shtml;
	}
	return $filter;
}

function getShareInfo($recordid = '', $idname = true) {
	global $adb;
	$member_data = array();
	$member_query = $adb->pquery(
		"SELECT vtiger_reportsharing.setype,vtiger_users.id,vtiger_users.user_name
			FROM vtiger_reportsharing
			INNER JOIN vtiger_users on vtiger_users.id = vtiger_reportsharing.shareid
			WHERE vtiger_reportsharing.setype='users' AND vtiger_reportsharing.reportid = ?",
		array($recordid)
	);
	$noofrows = $adb->num_rows($member_query);
	if ($noofrows > 0) {
		for ($i=0; $i<$noofrows; $i++) {
			$userid = $adb->query_result($member_query, $i, 'id');
			$username = $adb->query_result($member_query, $i, 'user_name');
			$setype = $adb->query_result($member_query, $i, 'setype');
			if ($idname) {
				$mdata = array('id'=>$setype.'::'.$userid, 'name'=>$setype.'::'.$username);
			} else {
				$mdata = array('value'=>$setype.'::'.$userid, 'label'=>$setype.'::'.$username);
			}
			$member_data[] = $mdata;
		}
	}

	$member_query = $adb->pquery(
		"SELECT vtiger_reportsharing.setype,vtiger_groups.groupid,vtiger_groups.groupname
			FROM vtiger_reportsharing
			INNER JOIN vtiger_groups on vtiger_groups.groupid = vtiger_reportsharing.shareid
			WHERE vtiger_reportsharing.setype='groups' AND vtiger_reportsharing.reportid = ?",
		array($recordid)
	);
	$noofrows = $adb->num_rows($member_query);
	if ($noofrows > 0) {
		for ($i=0; $i<$noofrows; $i++) {
			$grpid = $adb->query_result($member_query, $i, 'groupid');
			$grpname = $adb->query_result($member_query, $i, 'groupname');
			$setype = $adb->query_result($member_query, $i, 'setype');
			if ($idname) {
				$mdata = array('id'=>$setype.'::'.$grpid, 'name'=>$setype.'::'.$grpname);
			} else {
				$mdata = array('value'=>$setype.'::'.$grpid, 'label'=>$setype.'::'.$grpname);
			}
			$member_data[] = $mdata;
		}
	}
	return $member_data;
}
?>
