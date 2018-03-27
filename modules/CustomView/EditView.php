<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'data/Tracker.php';

global $mod_strings, $app_strings, $current_user, $theme, $log, $default_charset;
$focus = 0;

//<<<<<>>>>>>
global $oCustomView;
//<<<<<>>>>>>

$error_msg = '';
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once 'modules/CustomView/CustomView.php';

$cv_module = vtlib_purify($_REQUEST['module']);
$recordid = isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : '';

$smarty->assign('MOD', $mod_strings);
$smarty->assign('CATEGORY', getParentTab());
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MODULE', $cv_module);
$smarty->assign('MODULELABEL', getTranslatedString($cv_module, $cv_module));
$smarty->assign('CVMODULE', $cv_module);
$smarty->assign('CUSTOMVIEWID', $recordid);
$smarty->assign('DATEFORMAT', $current_user->date_format);
$smarty->assign('JS_DATEFORMAT', parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$smarty->assign('CHECKED', '');
$smarty->assign('MCHECKED', '');
$smarty->assign('STATUS', '');
if ($recordid == '') {
	$oCustomView = new CustomView();
	$modulecollist = $oCustomView->getModuleColumnsList($cv_module);
	$log->info('CustomView :: Successfully got ColumnsList for the module'.$cv_module);
	if (isset($modulecollist)) {
		$choosecolslist = getByModule_ColumnsList($cv_module, $modulecollist);
	}
	$smarty->assign('CHOOSECOLUMN', $choosecolslist);
	$smarty->assign('SELECTEDCOLUMN', array('none'));
	$Application_ListView_MaxColumns = GlobalVariable::getVariable('Application_ListView_MaxColumns', 12);
	$smarty->assign('ListView_MaxColumns', $Application_ListView_MaxColumns);
	$smarty->assign('FILTERROWS', ceil($Application_ListView_MaxColumns/4)+1);
	$stdfilterhtml = $oCustomView->getStdFilterCriteria();
	$stdfiltercolhtml = getStdFilterHTML($cv_module);
	$stdfilterjs = $oCustomView->getCriteriaJS();

	$smarty->assign("STDFILTERCOLUMNS", $stdfiltercolhtml);
	$smarty->assign("STDCOLUMNSCOUNT", count($stdfiltercolhtml));
	$smarty->assign("STDFILTERCRITERIA", $stdfilterhtml);
	$smarty->assign("STDFILTER_JAVASCRIPT", $stdfilterjs);

	$advfilterhtml = getAdvCriteriaHTML();
	$modulecolumnshtml = getByModule_ColumnsHTML($cv_module, $modulecollist);
	$smarty->assign("FOPTION", $advfilterhtml);
	$smarty->assign("COLUMNS_BLOCK", $modulecolumnshtml);

	$smarty->assign("MANDATORYCHECK", implode(",", array_unique($oCustomView->mandatoryvalues)));
	$smarty->assign("SHOWVALUES", implode(",", $oCustomView->showvalues));
	$smarty->assign('EXIST', 'false');
	$data_type[] = $oCustomView->data_type;
	$smarty->assign("DATATYPE", $data_type);
} else {
	$oCustomView = new CustomView($cv_module);
	$now_action = vtlib_purify($_REQUEST['action']);
	if ($oCustomView->isPermittedCustomView($recordid, $now_action, $oCustomView->customviewmodule) == 'yes') {
		$customviewdtls = $oCustomView->getCustomViewByCvid($recordid);
		$log->info('CustomView :: Successfully got ViewDetails for the Viewid'.$recordid);
		$modulecollist = $oCustomView->getModuleColumnsList($cv_module);
		$selectedcolumnslist = $oCustomView->getColumnsListByCvid($recordid);
		$log->info('CustomView :: Successfully got ColumnsList for the Viewid'.$recordid);

		$smarty->assign("VIEWNAME", $customviewdtls["viewname"]);

		if ($customviewdtls["setdefault"] == 1) {
			$smarty->assign("CHECKED", "checked");
		}
		if ($customviewdtls["setmetrics"] == 1) {
			$smarty->assign("MCHECKED", "checked");
		}
		$status = $customviewdtls["status"];
		$smarty->assign("STATUS", $status);
		$choosecolslist = getByModule_ColumnsList($cv_module, $modulecollist);
		$smarty->assign('CHOOSECOLUMN', $choosecolslist);
		$smarty->assign('SELECTEDCOLUMN', $selectedcolumnslist);
		$Application_ListView_MaxColumns = GlobalVariable::getVariable('Application_ListView_MaxColumns', 12);
		$smarty->assign('ListView_MaxColumns', $Application_ListView_MaxColumns);
		$smarty->assign('FILTERROWS', ceil($Application_ListView_MaxColumns/4)+1);
		$stdfilterlist = $oCustomView->getStdFilterByCvid($recordid);
		$log->info('CustomView :: Successfully got Standard Filter for the Viewid'.$recordid);
		$stdfilterlist["stdfilter"] = ($stdfilterlist["stdfilter"] != "") ? ($stdfilterlist["stdfilter"]) : ("custom");
		$stdfilterhtml = $oCustomView->getStdFilterCriteria($stdfilterlist["stdfilter"]);
		$stdfiltercolhtml = getStdFilterHTML($cv_module, $stdfilterlist["columnname"]);
		$stdfilterjs = $oCustomView->getCriteriaJS();

		$smarty->assign('STARTDATE', $stdfilterlist['startdate']);
		$smarty->assign('ENDDATE', $stdfilterlist['enddate']);
		$smarty->assign('STDFILTERCOLUMNS', $stdfiltercolhtml);
		$smarty->assign('STDCOLUMNSCOUNT', count($stdfiltercolhtml));
		$smarty->assign('STDFILTERCRITERIA', $stdfilterhtml);
		$smarty->assign('STDFILTER_JAVASCRIPT', $stdfilterjs);

		$advfilterlist = $oCustomView->getAdvFilterByCvid($recordid);
		$advfilterhtml = getAdvCriteriaHTML();
		$modulecolumnshtml = getByModule_ColumnsHTML($cv_module, $modulecollist);
		$smarty->assign('FOPTION', $advfilterhtml);
		$smarty->assign('COLUMNS_BLOCK', $modulecolumnshtml);
		$smarty->assign('CRITERIA_GROUPS', $advfilterlist);

		$smarty->assign('MANDATORYCHECK', implode(',', array_unique($oCustomView->mandatoryvalues)));
		$smarty->assign('SHOWVALUES', implode(',', $oCustomView->showvalues));
		$smarty->assign('EXIST', 'true');
		$data_type[] = $oCustomView->data_type;
		$smarty->assign("DATATYPE", $data_type);
	} else {
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
		exit;
	}
}

$smarty->assign('RETURN_MODULE', $cv_module);
$return_action = 'index';

if ($recordid == '') {
	$act = $mod_strings['LBL_NEW'];
} else {
	$act = $mod_strings['LBL_EDIT'];
}

$smarty->assign("ACT", $act);
$smarty->assign("RETURN_ACTION", $return_action);

$smarty->display("CustomView.tpl");

function getByModule_ColumnsHTML($module, $columnslist, $selected = "") {
	$columnsList = getByModule_ColumnsList($module, $columnslist, $selected);
	return generateSelectColumnsHTML($columnsList, $module);
}

function generateSelectColumnsHTML($columnsList, $module) {
	$shtml = '';
	foreach ($columnsList as $blocklabel => $blockcolumns) {
		$shtml .= "<optgroup label='".getTranslatedString($blocklabel, $module)."' class='select' style='border:none'>";
		foreach ($blockcolumns as $columninfo) {
			$shtml .= "<option ".$columninfo['selected']." value='".$columninfo['value']."'>".$columninfo['text']."</option>";
		}
	}
	return $shtml;
}

function getByModule_ColumnsList($mod, $columnslist, $selected = '') {
	global $oCustomView;
	$advfilter = array();
	$check_dup = array();
	foreach ($oCustomView->module_list as $module => $blks) {
		$modname = getTranslatedString($module, $module);
		foreach ($blks as $key => $value) {
			$advfilter = array();
			$label = $key;
			if (isset($columnslist[$module][$key])) {
				foreach ($columnslist[$module][$key] as $field => $fieldlabel) {
					if (!in_array($module.$fieldlabel, $check_dup)) {
						$advfilter_option['value'] = $field;
						$advfilter_option['text'] = getTranslatedString($fieldlabel, $module);
						$advfilter_option['selected'] = ($selected == $field ? 'selected' : '');
						$advfilter[] = $advfilter_option;
						$check_dup [] = $module.$fieldlabel;
					}
				}
				if (count($advfilter)>0) {
					$advfilter_out[$modname.' - '.$label]= $advfilter;
				}
			}
		}
	}
	return $advfilter_out;
}

/** to get the standard filter criteria
* @param $module(module name) :: Type String
* @param $elected (selection status) :: Type String (optional)
* @returns  $filter Array in the following format
* $filter = Array( 0 => array('value'=>$tablename:$colname:$fieldname:$fieldlabel,'text'=>$mod_strings[$field label],'selected'=>$selected),
*	1 => array('value'=>$$tablename1:$colname1:$fieldname1:$fieldlabel1,'text'=>$mod_strings[$field label1],'selected'=>$selected),
*/
function getStdFilterHTML($module, $selected = '') {
	global $app_strings, $current_user, $oCustomView;
	$stdfilter = array();
	$result = $oCustomView->getStdCriteriaByModule($module);

	if (isset($result)) {
		foreach ($result as $key => $value) {
			if ($value == 'Start Date & Time') {
				$value = 'Start Date';
			}
			$use_module_label =  getTranslatedString($module, $module);
			$filter['value'] = $key;
			$filter['text'] = $use_module_label.' - '.getTranslatedString($value, $module);
			if ($key == $selected) {
				$filter['selected'] = 'selected';
			} else {
				$filter['selected'] = '';
			}
			$stdfilter[]=$filter;
			// If a user doesn't have permission for a field and it has been used to filter a custom view, it should be displayed to him as Not Accessible.
			if (!is_admin($current_user) && $selected != '' && $filter['selected'] == '') {
				$keys = explode(":", $selected);
				if (getFieldVisibilityPermission($module, $current_user->id, $keys[2]) != '0') {
					$filter['value'] = "not_accessible";
					$filter['text'] = $app_strings["LBL_NOT_ACCESSIBLE"];
					$filter['selected'] = "selected";
					$stdfilter[]=$filter;
				}
			}
		}
	}
	return $stdfilter;
}

/** to get the Advanced filter criteria
* @param $selected :: Type String (optional)
* @returns  $AdvCriteria Array in the following format
* $AdvCriteria = Array( 0 => array('value'=>$tablename:$colname:$fieldname:$fieldlabel,'text'=>$mod_strings[$field label],'selected'=>$selected),
* 	1 => array('value'=>$$tablename1:$colname1:$fieldname1:$fieldlabel1,'text'=>$mod_strings[$field label1],'selected'=>$selected),
* 	n => array('value'=>$$tablenamen:$colnamen:$fieldnamen:$fieldlabeln,'text'=>$mod_strings[$field labeln],'selected'=>$selected))
*/
function getAdvCriteriaHTML($selected = '') {
	global $adv_filter_options;
	$shtml = '';
	foreach ($adv_filter_options as $key => $value) {
		if ($selected == $key) {
			$shtml .= "<option selected value=\"".$key."\">".$value."</option>";
		} else {
			$shtml .= "<option value=\"".$key."\">".$value."</option>";
		}
	}
	return $shtml;
}
?>