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
	if($module == 'Calendar') {
		$cachedEventsFields = VTCacheUtils::lookupFieldInfo_Module('Events');
		if($cachedModuleFields == false) $cachedModuleFields = $cachedEventsFields;
		else $cachedModuleFields = array_merge($cachedModuleFields, $cachedEventsFields);
	}
	if(empty($cachedModuleFields)) {
		return null;
	}
	$label = str_replace('&', 'and', decode_html($label));
	foreach ($cachedModuleFields as $fieldInfo) {
		$fieldLabel = str_replace(' ', '_', $fieldInfo['fieldlabel']);
		$fieldLabel = str_replace('&', 'and', $fieldLabel);
		if($label == $fieldLabel) {
			return $fieldInfo;
		}
	}
	return null;
}

function isReferenceUIType($uitype) {
	static $options = array('101', '116', '117', '26', '357', '51', '52', '53', '57', '59', '66', '73', '75', '76', '77', '78', '80', '81');

	if(in_array($uitype, $options)) {
		return true;
	}
	return false;
}

function isPicklistUIType($uitype) {
	static $options = array('15','16','1613','1614','1615','33','3313','3314','1024');

	if(in_array($uitype, $options)) {
		return true;
	}
	return false;
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
	if(!empty($fieldInfo)) {
		$field = WebserviceField::fromArray($db, $fieldInfo);
		$fieldType = $field->getFieldDataType();
	}

	if ($fieldType == 'currency' && $value != '') {
		// Some of the currency fields like Unit Price, Total, Sub-total etc of Inventory modules, do not need currency conversion
		if($field->getUIType() == '72') {
			$curid_value = explode("::", $value);
			$currency_id = $curid_value[0];
			$currency_value = $curid_value[1];
			$cur_sym_rate = getCurrencySymbolandCRate($currency_id);
			if($value!='') {
				$formattedCurrencyValue = CurrencyField::convertToUserFormat($currency_value, null, true);
				$fieldvalue = CurrencyField::appendCurrencySymbol($formattedCurrencyValue, $cur_sym_rate['symbol']);
			}
		} else {
			$currencyField = new CurrencyField($value);
			$fieldvalue = $currencyField->getDisplayValue();
		}

	} elseif ($dbField->name == "PurchaseOrder_Currency" || $dbField->name == "SalesOrder_Currency"
				|| $dbField->name == "Invoice_Currency" || $dbField->name == "Quotes_Currency" || $dbField->name == "PriceBooks_Currency") {
		if($value!='') {
			$fieldvalue = getTranslatedCurrencyString($value);
		}
	} elseif ((in_array($dbField->name,$report->ui101_fields) or (isset($field) and $field->getUIType() == '52')) && !empty($value)) {
		if(is_numeric($value))
		{
			$entityNames = getEntityName('Users', $value);
			$fieldvalue = $entityNames[$value];
		}
		else {
			$fieldvalue = $value;
		}
	} elseif( $fieldType == 'date' && !empty($value)) {
		if($module == 'Calendar' && $field->getFieldName() == 'due_date') {
			if (empty($valueArray['calendar_end_time'])) {
				if (!empty($valueArray['calendar_id'])) {
					$endTime = getSingleFieldValue('vtiger_activity', 'time_end', 'activityid', $valueArray['calendar_id']);
				} else if (!empty($valueArray['lbl_action'])) {
					$endTime = getSingleFieldValue('vtiger_activity', 'time_end', 'activityid', $valueArray['lbl_action']);
				} else {
					$endTime = '';
				}
			} else {
				$endTime = $valueArray['calendar_end_time'];
			}
			$date = new DateTimeField($value.' '.$endTime);
			$fieldvalue = $date->getDisplayDate();
		} elseif ($module == 'Calendar' && $field->getFieldName() == 'date_start') {
			$date = new DateTimeField($value);
			$fieldvalue = $date->getDisplayDateTimeValue();
		} else {
			$fieldvalue = DateTimeField::convertToUserFormat($value);
		}
	} elseif( $fieldType == "datetime" && !empty($value)) {
		$date = new DateTimeField($value);
		$fieldvalue = $date->getDisplayDateTimeValue();
		$user_format = ($current_user->hour_format=='24' ? '24' : '12');
		if ($user_format != '24') {
			$curr_time = DateTimeField::formatUserTimeString($fieldvalue, '12');
			list($dt,$tm) = explode(' ',$fieldvalue);
			$fieldvalue = $dt . ' ' . $curr_time;
		}
	} elseif ($fieldType == 'time' && !empty($value) && $field->getFieldName() != 'totaltime') {
		$date = new DateTimeField($value);
		$fieldvalue = $date->getDisplayTime();
	} elseif( $fieldType == "picklist" && !empty($value) ) {
		if(is_array($picklistArray)) {
			if(isset($picklistArray[$dbField->name]) && is_array($picklistArray[$dbField->name])
					&& $field->getFieldName() != 'activitytype'
					&& !in_array($value, $picklistArray[$dbField->name])) {
				$fieldvalue =$app_strings['LBL_NOT_ACCESSIBLE'];
			} else {
				$fieldvalue = getTranslatedString($value, $module);
			}
		} else {
			$fieldvalue = getTranslatedString($value, $module);
		}
	} elseif( $fieldType == "multipicklist" && !empty($value) ) {
		if(is_array($picklistArray[1])) {
			$valueList = explode(' |##| ', $value);
			$translatedValueList = array();
			foreach ( $valueList as $value) {
				if(is_array($picklistArray[1][$dbField->name]) && !in_array($value, $picklistArray[1][$dbField->name])) {
					$translatedValueList[] = $app_strings['LBL_NOT_ACCESSIBLE'];
				} else {
					$translatedValueList[] = getTranslatedString($value, $module);
				}
			}
		}
		if (!is_array($picklistArray[1]) || !is_array($picklistArray[1][$dbField->name])) {
			$fieldvalue = str_replace(' |##| ', ', ', $value);
		} else {
			implode(', ', $translatedValueList);
		}
	} elseif( $fieldType == "multireference" && !empty($value)){
		require_once 'modules/PickList/PickListUtils.php';
		$content = getPicklistValuesSpecialUitypes($field->getUIType(),$field->getFieldName(),$value,'DetailView');
		$fieldvalue = strip_tags(implode(', ',$content));
	}
	if($fieldvalue == "") {
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

	return $fieldvalue;
}

function report_getMoreInfoFromRequest($cbreporttype,$pmodule,$smodule,$pivotcolumns) {
	global $adb;
	if (isset($_REQUEST['cbreporttype']) && $_REQUEST['cbreporttype']=='external') {
		if (isset($_REQUEST['adduserinfo']) and ($_REQUEST['adduserinfo'] == 'on' || $_REQUEST['adduserinfo'] == 1)) {
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
		foreach ($refs as $fname => $field) {
			$rs = $adb->pquery('select relmodule from vtiger_fieldmodulerel where fieldid=?',array($field->id));
			$relmod = $adb->query_result($rs,0,0);
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
				$agglabel = getTranslatedString('LBL_COLUMNS_SUM','Reports');
			break;
			case 'avg':
				$agglabel = getTranslatedString('LBL_COLUMNS_AVERAGE','Reports');
			break;
			case 'min':
				$agglabel = getTranslatedString('LBL_COLUMNS_LOW_VALUE','Reports');
			break;
			case 'max':
				$agglabel = getTranslatedString('LBL_COLUMNS_LARGE_VALUE','Reports');
			break;
			default:
				$aggfield = false;
				$agglabel = 'Sum';
				$_REQUEST['crosstabaggfunction'] = 'sum';
			break;
		}
		$sql = PivotTableSQL($adb->database, // adodb connection
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
?>
