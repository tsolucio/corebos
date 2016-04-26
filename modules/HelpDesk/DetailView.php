<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');
require_once('user_privileges/default_module_view.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $singlepane_view;

$focus = CRMEntity::getInstance($currentModule);

$tool_buttons = Button_Check($currentModule);
$smarty = new vtigerCRM_Smarty();

$record = vtlib_purify($_REQUEST['record']);
$isduplicate = isset($_REQUEST['isDuplicate']) ? vtlib_purify($_REQUEST['isDuplicate']) : '';
$tabid = getTabid($currentModule);
$category = getParentTab($currentModule);

if($record != '') {
	$focus->id = $record;
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->name=$focus->column_fields['ticket_title'];
}
if($isduplicate == 'true') $focus->id = '';
$focus->preViewCheck($_REQUEST, $smarty);

// Identify this module as custom module.
$smarty->assign('CUSTOM_MODULE', false);

$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
// TODO: Update Single Module Instance name here.
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('THEME', $theme);
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);

$recordName = array_values(getEntityName($currentModule, $focus->id));
$recordName = $recordName[0];
$smarty->assign('NAME', $recordName);
$smarty->assign('UPDATEINFO',updateInfo($focus->id));

// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if ($mod_seq_field != null) {
	$mod_seq_id = $focus->column_fields[$mod_seq_field['name']];
} else {
	$mod_seq_id = $focus->id;
}
$smarty->assign('MOD_SEQ_ID', $mod_seq_id);

$validationArray = split_validationdataArray(getDBValidationData($focus->tab_name, $tabid));
$smarty->assign('VALIDATION_DATA_FIELDNAME',$validationArray['fieldname']);
$smarty->assign('VALIDATION_DATA_FIELDDATATYPE',$validationArray['datatype']);
$smarty->assign('VALIDATION_DATA_FIELDLABEL',$validationArray['fieldlabel']);

if(isPermitted('HelpDesk','Merge','') == 'yes') {
	require("user_privileges/user_privileges_".$current_user->id.".php");
	require_once('include/utils/UserInfoUtil.php');
	$wordTemplateResult = fetchWordTemplateList("HelpDesk");
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	for($templateCount=0;$templateCount<$tempCount;$templateCount++) {
		$optionString[$tempVal["templateid"]]=$tempVal["filename"];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	if($is_admin)
		$smarty->assign('MERGEBUTTON','permitted');
	elseif($tempCount >0)
		$smarty->assign('MERGEBUTTON','permitted');
	$smarty->assign('TEMPLATECOUNT',$tempCount);
	$smarty->assign('WORDTEMPLATEOPTIONS',$app_strings['LBL_SELECT_TEMPLATE_TO_MAIL_MERGE']);
	$smarty->assign('TOPTIONS',$optionString);
}
//Added code for Error display in sending mail to assigned to user when ticket is created or updated.
if($_REQUEST['mail_error'] != '') {
	require_once("modules/Emails/mail.php");
	$ticket_owner = getUserFullName($focus->column_fields['assigned_user_id']);
	$error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $app_strings['LBL_MAIL_NOT_SENT_TO_USER']. ' ' . $ticket_owner. '. ' .$app_strings['LBL_PLS_CHECK_EMAIL_N_SERVER'];
	echo $mod_strings['LBL_MAIL_SEND_STATUS'].' <b><font class="warning">'.$error_msg.'</font></b>';
}

//Added button for Convert the ticket to FAQ
if(isPermitted('Faq','CreateView','') == 'yes')
	$smarty->assign('CONVERTASFAQ','permitted');

$smarty->assign('TODO_PERMISSION',CheckFieldPermission('parent_id','Calendar'));
$smarty->assign('EVENT_PERMISSION',CheckFieldPermission('parent_id','Events'));
//Added to display the ticket comments information
$smarty->assign('COMMENT_BLOCK',$focus->getCommentInformation($record));
$smarty->assign('TICKETID', $record);
$smarty->assign('EDIT_PERMISSION', isPermitted($currentModule, 'EditView', $record));
$smarty->assign('CHECK', $tool_buttons);

if(PerformancePrefs::getBoolean('DETAILVIEW_RECORD_NAVIGATION', true) && isset($_SESSION[$currentModule.'_listquery'])){
	$recordNavigationInfo = ListViewSession::getListViewNavigation($focus->id);
	VT_detailViewNavigation($smarty,$recordNavigationInfo,$focus->id);
}

$smarty->assign('IS_REL_LIST', isPresentRelatedLists($currentModule));
$smarty->assign('SinglePane_View', $singlepane_view);

if($singlepane_view == 'true') {
	$related_array = getRelatedLists($currentModule,$focus);
	$smarty->assign("RELATEDLISTS", $related_array);

	require_once('include/ListView/RelatedListViewSession.php');
	if(!empty($_REQUEST['selected_header']) && !empty($_REQUEST['relation_id'])) {
		RelatedListViewSession::addRelatedModuleToSession(vtlib_purify($_REQUEST['relation_id']),
				vtlib_purify($_REQUEST['selected_header']));
	}
	$open_related_modules = RelatedListViewSession::getRelatedModulesFromSession();
	$smarty->assign("SELECTEDHEADERS", $open_related_modules);
}

if(isPermitted($currentModule, 'CreateView', $record) == 'yes')
	$smarty->assign('CREATE_PERMISSION', 'permitted');
if(isPermitted($currentModule, 'Delete', $record) == 'yes')
	$smarty->assign('DELETE', 'permitted');

$smarty->assign('BLOCKS', getBlocks($currentModule,'detail_view','',$focus->column_fields));

// Gather the custom link information to display
include_once('vtlib/Vtiger/Link.php');
$customlink_params = Array('MODULE'=>$currentModule, 'RECORD'=>$focus->id, 'ACTION'=>vtlib_purify($_REQUEST['action']));
$smarty->assign('CUSTOM_LINKS', Vtiger_Link::getAllByType(getTabid($currentModule), Array('DETAILVIEWBASIC','DETAILVIEW','DETAILVIEWWIDGET'), $customlink_params));

// Record Change Notification
$focus->markAsViewed($current_user->id);

$smarty->assign('DETAILVIEW_AJAX_EDIT', PerformancePrefs::getBoolean('DETAILVIEW_AJAX_EDIT', true));

$smarty->display('DetailView.tpl');
?>