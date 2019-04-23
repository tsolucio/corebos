<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
$ajaxaction = $_REQUEST['ajxaction'];

if ($ajaxaction == 'WIDGETADDCOMMENT') {
	global $current_user, $default_charset, $currentModule;
	$modObj = CRMEntity::getInstance($currentModule);
	list($void,$canaddcomments) = cbEventHandler::do_filter('corebos.filter.ModComments.canAdd', array(vtlib_purify($_REQUEST['parentid']), true));
	if (isPermitted($currentModule, 'CreateView', '') == 'yes' && $canaddcomments) {
		$modObj->column_fields['commentcontent'] = htmlentities($_REQUEST['comment'], ENT_QUOTES, $default_charset); // we don't clean to accept all characters in comment
		$modObj->column_fields['related_to'] = vtlib_purify($_REQUEST['parentid']);
		$modObj->column_fields['assigned_user_id'] = $current_user->id;
		$modObj->save($currentModule);

		if (empty($modObj->column_fields['smcreatorid'])) {
			$modObj->column_fields['smcreatorid'] = $current_user->id;
		}
		if (empty($modObj->column_fields['modifiedtime'])) {
			$modObj->column_fields['modifiedtime']= date('Y-m-d H:i:s');
		}

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
} else {
	require_once 'modules/Vtiger/DetailViewAjax.php';
}
?>
