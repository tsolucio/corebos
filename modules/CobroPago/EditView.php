<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $mod_strings, $current_language, $currentModule, $theme;

require_once('Smarty_setup.php');

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();

$category = getParentTab($currentModule);
$record = vtlib_purify($_REQUEST['record']);
$isduplicate = vtlib_purify($_REQUEST['isDuplicate']);

//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends
if (!empty($_REQUEST['related_id']) and ($_REQUEST['return_module']=='Invoice' or $_REQUEST['return_module']=='Quotes' or $_REQUEST['return_module']=='SalesOrder')) {
	$lermod=strtolower($_REQUEST['return_module']);
	$lermods=($lermod=='quotes' ? 'quote':$lermod);
	$_REQUEST['parent_id']=$adb->query_result($adb->pquery('select accountid from vtiger_'.$lermod.' where '.$lermods.'id=?',array($_REQUEST['related_id'])),0,0);
}
if (!empty($_REQUEST['related_id']) and $_REQUEST['return_module']=='PurchaseOrder') {
	$_REQUEST['parent_id']=$adb->query_result($adb->pquery('select vendorid from vtiger_purchaseorder where purchaseorderid=?',array($_REQUEST['related_id'])),0,0);
}
if($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	
	if(!$focus->permissiontoedit() and $isduplicate != 'true')
	{
		$log->debug("No tengo permiso. Se aborta la edicion");
		echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
		echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span clas
		s='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
		</td>
		</tr>
		</tbody></table>
		</div>";
		echo "</td></tr></table>";
		exit;
	}
	
}
if($isduplicate == 'true') {
	$focus->id = '';
	$focus->mode = '';
}
if(empty($_REQUEST['record']) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}

$disp_view = getView($focus->mode);
if($disp_view == 'edit_view') {
	$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));
} else {
	$smarty->assign('BASBLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields, 'BAS'));
	$smarty->assign('ADVBLOCKS',getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'ADV'));
}

$smarty->assign('OP_MODE',$disp_view);
$app_strings['LBL_CHANGE']=$mod_strings['LBL_CHANGE'];
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
// TODO: Update Single Module Instance name here.
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('CREATEMODE', vtlib_purify($_REQUEST['createmode']));

$smarty->assign('CHECK', Button_Check($currentModule));
$smarty->assign('DUPLICATE', $isduplicate);

if($focus->mode == 'edit' || $isduplicate) {
	$recordName = array_values(getEntityName($currentModule, $record));
	$recordName = $recordName[0];
	$smarty->assign('NAME', $recordName);
	$smarty->assign('UPDATEINFO',updateInfo($record));
}

if(isset($_REQUEST['return_module']))    $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if(isset($_REQUEST['return_action']))    $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if(isset($_REQUEST['return_id']))        $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));

// Field Validation Information 
$tabid = getTabid($currentModule);
$validationData = getDBValidationData($focus->tab_name,$tabid);
$validationArray = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);

// In case you have a date field
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if($focus->mode != 'edit' && $mod_seq_field != null) {
		$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
		$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1",array($currentModule));
        $mod_seq_prefix = $adb->query_result($mod_seq_string,0,'prefix');
        $mod_seq_no = $adb->query_result($mod_seq_string,0,'cur_id');
        if($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no))
                echo '<br><font color="#FF0000"><b>'. getTranslatedString('LBL_DUPLICATE'). ' '. getTranslatedString($mod_seq_field['label'])
                	.' - '. getTranslatedString('LBL_CLICK') .' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule='.$currentModule.'">'.getTranslatedString('LBL_HERE').'</a> '
                	. getTranslatedString('LBL_TO_CONFIGURE'). ' '. getTranslatedString($mod_seq_field['label']) .'</b></font>';
        else
                $smarty->assign("MOD_SEQ_ID",$autostr);
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

if($focus->mode == 'edit') {
	$smarty->display('salesEditView.tpl');
} else {
	$smarty->display('CreateView.tpl');
}

?>
