<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$modObj = CRMEntity::getInstance($currentModule);
$ajaxaction = $_REQUEST["ajxaction"];

if($ajaxaction == 'WIDGETADDCOMMENT') {
	global $current_user, $default_charset;
	list($void,$canaddcomments) = cbEventHandler::do_filter('corebos.filter.ModComments.canAdd', array(vtlib_purify($_REQUEST['parentid']), true));
	if (isPermitted($currentModule, 'CreateView', '') == 'yes' and $canaddcomments) {
		$modObj->column_fields['commentcontent'] = htmlentities($_REQUEST['comment'], ENT_QUOTES, $default_charset); // we don't clean this one to accept all characters in comment
		$modObj->column_fields['related_to'] = vtlib_purify($_REQUEST['parentid']);
		$modObj->column_fields['assigned_user_id'] = $current_user->id;
		$modObj->save($currentModule);
	
		if(empty($modObj->column_fields['smcreatorid'])) $modObj->column_fields['smcreatorid'] = $current_user->id;
		if(empty($modObj->column_fields['modifiedtime'])) $modObj->column_fields['modifiedtime']= date('Y-m-d H:i:s');
		
		//update modifiedtime related module with modcomments modifiedtime
		global $adb;
		$adb->query("update vtiger_crmentity set modifiedtime ='".$modObj->column_fields['modifiedtime']."' where crmid =".$modObj->column_fields['related_to']);
		//end update
		$widgetInstance = $modObj->getWidget('DetailViewBlockCommentWidget');
		$modObj->column_fields['commentcontent'] = htmlentities($modObj->column_fields['commentcontent'], ENT_QUOTES, $default_charset);
		echo ':#:SUCCESS'. $widgetInstance->processItem($modObj->getAsCommentModel($modObj->column_fields));
	} else {
		echo ':#:FAILURE';
	}
}

else if($ajaxaction == 'DETAILVIEW') {
	$crmid = vtlib_purify($_REQUEST['recordid']);
	$fieldname = vtlib_purify($_REQUEST['fldName']);
	$fieldvalue = utf8RawUrlDecode($_REQUEST['fieldValue']);
	if($crmid != '') {
		$modObj->retrieve_entity_info($crmid, $currentModule);
		$modObj->column_fields[$fieldname] = $fieldvalue;
		$modObj->id = $crmid;
		$modObj->mode = 'edit';
		list($saveerror,$errormessage,$error_action,$returnvalues) = $modObj->preSaveCheck($_REQUEST);
		if ($saveerror) { // there is an error so we report error
			echo ':#:ERR'.$errormessage;
		} else {
			$modObj->save($currentModule);
			if ($modObj->id != '') {
				echo ':#:SUCCESS';
			} else {
				echo ':#:FAILURE';
			}
		}
	} else {
		echo ':#:FAILURE';
	}
} elseif ($ajaxaction == "LOADRELATEDLIST" || $ajaxaction == "DISABLEMODULE") {
	require_once 'include/ListView/RelatedListViewContents.php';
}
?>