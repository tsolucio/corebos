<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $app_strings, $app_list_strings, $mod_strings, $current_language, $currentModule, $theme, $adb;
require_once('Smarty_setup.php');
require_once('data/Tracker.php');

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();

$category = getParentTab($currentModule);
$record = vtlib_purify($_REQUEST['record']);
$isduplicate = vtlib_purify($_REQUEST['isDuplicate']);

//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

if($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields['notes_title'];
}

if($focus->mode != 'edit')
{
	if(isset($_REQUEST['parent_id']) && isset($_REQUEST['return_module']))
	{
		$owner = getRecordOwnerId($_REQUEST['parent_id']);
		if(isset($owner['Users']) && $owner['Users'] != '') {
			$permitted_users = get_user_array('true', 'Active',$current_user->id);
			if(!in_array($owner['Users'],$permitted_users)){
				$owner['Users'] = $current_user->id;
			}
			$focus->column_fields['assigntype'] = 'U';
			$focus->column_fields['assigned_user_id'] = $owner['Users'];
		} elseif(isset($owner['Groups']) && $owner['Groups'] != '') {
			$focus->column_fields['assigntype'] = 'T';
			$focus->column_fields['assigned_user_id'] = $owner['Groups'];
		}
	}
}
if($isduplicate == 'true') {
	$focus->id = '';
	$focus->mode = '';
}
$focus->preEditCheck($_REQUEST,$smarty);
if (!empty($_REQUEST['save_error']) and $_REQUEST['save_error'] == "true") {
	if (!empty($_REQUEST['encode_val'])) {
		global $current_user;
		$encode_val = vtlib_purify($_REQUEST['encode_val']);
		$decode_val = base64_decode($encode_val);
		$explode_decode_val = explode('&', trim($decode_val,'&'));
		$tabid = getTabid($currentModule);
		foreach ($explode_decode_val as $fieldvalue) {
			$value = explode("=", $fieldvalue);
			$field_name_val = $value[0];
			$field_value =urldecode($value[1]);
			$finfo = VTCacheUtils::lookupFieldInfo($tabid, $field_name_val);
			if ($finfo !== false) {
				switch ($finfo['uitype']) {
					case '56':
						$field_value = $field_value=='on' ? '1' : '0';
						break;
					case '7':
					case '9':
					case '72':
						$field_value = CurrencyField::convertToDBFormat($field_value, null, true);
						break;
					case '71':
						$field_value = CurrencyField::convertToDBFormat($field_value);
						break;
					case '33':
					case '3313':
						if (is_array($field_value)) {
							$field_value = implode(' |##| ', $field_value);
						}
						break;
				}
			}
			$focus->column_fields[$field_name_val] = $field_value;
		}
	}
	$errormessageclass = isset($_REQUEST['error_msgclass']) ? vtlib_purify($_REQUEST['error_msgclass']) : '';
	$errormessage = isset($_REQUEST['error_msg']) ? vtlib_purify($_REQUEST['error_msg']) : '';
	$smarty->assign('ERROR_MESSAGE_CLASS', $errormessageclass);
	$smarty->assign('ERROR_MESSAGE', $errormessage);
} elseif($focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}

if(isset($_REQUEST['parent_id']) && $focus->mode != 'edit') {
	$smarty->assign('PARENTID',vtlib_purify($_REQUEST['parent_id']));
}

$dbQuery="select filename from vtiger_notes where notesid = ?";
$result=$adb->pquery($dbQuery,array($focus->id));
$filename=$adb->query_result($result,0,'filename');
if(is_null($filename) || $filename == '') {
	$smarty->assign('FILE_EXIST','no');
} else {
	$smarty->assign('FILE_NAME',$filename);
	$smarty->assign('FILE_EXIST','yes');
}

//needed when creating a new case with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) {
	$focus->contact_name = vtlib_purify($_REQUEST['contact_name']);
}
if (isset($_REQUEST['contact_id']) /* && is_null($focus->contact_id) */ ) {
	$focus->contact_id = vtlib_purify($_REQUEST['contact_id']);
}
if (isset($_REQUEST['parent_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = vtlib_purify($_REQUEST['parent_name']);
}
if (isset($_REQUEST['parent_id']) /* && is_null($focus->parent_id) */ ) {
	$focus->parent_id = vtlib_purify($_REQUEST['parent_id']);
}
if (isset($_REQUEST['parent_type'])) {
	$focus->parent_type = vtlib_purify($_REQUEST['parent_type']);
}
elseif (!isset($focus->parent_type)) {
	$focus->parent_type = $app_list_strings['record_type_default_key'];
}

$disp_view = getView($focus->mode);
$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));
$smarty->assign('BASBLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields, 'BAS'));
$smarty->assign('ADVBLOCKS',getBlocks($currentModule,$disp_view,$focus->mode,$focus->column_fields,'ADV'));

$custom_blocks = getCustomBlocks($currentModule,$disp_view);
$smarty->assign('CUSTOMBLOCKS', $custom_blocks);
$smarty->assign('FIELDS',$focus->column_fields);

$smarty->assign('OP_MODE',$disp_view);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('CREATEMODE', isset($_REQUEST['createmode']) ? vtlib_purify($_REQUEST['createmode']) : '');

$smarty->assign('CHECK', Button_Check($currentModule));
$smarty->assign('DUPLICATE', $isduplicate);

if($focus->mode == 'edit' || $isduplicate == 'true') {
	$recordName = array_values(getEntityName($currentModule, $record));
	$recordName = $recordName[0];
	$smarty->assign('NAME', $recordName);
	$smarty->assign('UPDATEINFO',updateInfo($record));
}

if(isset($_REQUEST['return_module']))    $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if(isset($_REQUEST['return_action']))    $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if(isset($_REQUEST['return_id']))        $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));
$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize',3000000,$currentModule);
$smarty->assign("UPLOADSIZE", $upload_maxsize/1000000); //Convert to MB
$smarty->assign("UPLOAD_MAXSIZE",$upload_maxsize);
$USE_RTE = vt_hasRTE();
if(getFieldVisibilityPermission('Documents',$current_user->id,'notecontent') != '0')
	$USE_RTE = false;
$smarty->assign('USE_RTE',$USE_RTE);
if (isset($_REQUEST['email_id']))
	$smarty->assign('EMAILID', vtlib_purify($_REQUEST['email_id']));
if (isset($_REQUEST['ticket_id'])) $smarty->assign("TICKETID", vtlib_purify($_REQUEST['ticket_id']));
if (isset($_REQUEST['fileid']))
	$smarty->assign('FILEID', vtlib_purify($_REQUEST['fileid']));
if (isset($_REQUEST['record'])) {
	$smarty->assign('CANCELACTION', 'DetailView');
} else {
	$smarty->assign('CANCELACTION', 'index');
}
if($_REQUEST['upload_error'] == true)
{
	echo '<br><b><font color="red"> '.$mod_strings['FILE_HAS_NO_DATA'].'.</font></b><br>';
}

if ( empty($focus->filename)) {
	$smarty->assign('FILENAME_TEXT', '');
	$smarty->assign('FILENAME', '');
} else {
	$smarty->assign('FILENAME_TEXT', '('.$focus->filename.')');
	$smarty->assign('FILENAME', $focus->filename);
}
if ($focus->parent_type == 'Account') $smarty->assign('DEFAULT_SEARCH', "&query=true&account_id=$focus->parent_id&account_name=".urlencode($focus->parent_name));

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

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if($focus->mode != 'edit' && $mod_seq_field != null) {
	$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
	list($mod_seq_string, $mod_seq_prefix, $mod_seq_no, $doNative) = cbEventHandler::do_filter('corebos.filter.ModuleSeqNumber.get', array('', '', '', true));
	if ($doNative) {
		$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from vtiger_modentity_num where semodule = ? and active=1",array($currentModule));
		$mod_seq_prefix = $adb->query_result($mod_seq_string,0,'prefix');
		$mod_seq_no = $adb->query_result($mod_seq_string,0,'cur_id');
	}
	if ($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no)) {
		$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-warning');
		$smarty->assign('ERROR_MESSAGE', '<b>'. getTranslatedString($mod_seq_field['label']). ' '. getTranslatedString('LBL_NOT_CONFIGURED')
			.' - '. getTranslatedString('LBL_PLEASE_CLICK') .' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule='.$currentModule.'">'.getTranslatedString('LBL_HERE').'</a> '
			. getTranslatedString('LBL_TO_CONFIGURE'). ' '. getTranslatedString($mod_seq_field['label']) .'</b>');
	} else {
		$smarty->assign("MOD_SEQ_ID",$autostr);
	}
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));

$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", json_encode($picklistDependencyDatasource));

$smarty->display('salesEditView.tpl');
?>