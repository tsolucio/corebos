<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

if ($ajaxaction == 'LOADRELATEDLIST') {
	global $relationId;
	$relationId = vtlib_purify($_REQUEST['relation_id']);
	if (!empty($relationId) && ((int)$relationId) > 0) {
		$recordid = vtlib_purify($_REQUEST['record']);
		if (empty($_SESSION['rlvs'][$currentModule][$relationId]['currentRecord']) || $_SESSION['rlvs'][$currentModule][$relationId]['currentRecord'] != $recordid) {
			$resetCookie = true;
		} else {
			$resetCookie = false;
		}
		coreBOS_Session::set('rlvs^'.$currentModule.'^'.$relationId.'^currentRecord', $recordid);
		$actions = vtlib_purify($_REQUEST['actions']);
		$header = vtlib_purify($_REQUEST['header']);
		$modObj->id = $recordid;
		$relationInfo = getRelatedListInfoById($relationId);
		$relatedModule = getTabModuleName($relationInfo['relatedTabId']);
		$function_name = $relationInfo['functionName'];
		if (!method_exists($modObj, $function_name)) {
			@include_once 'modules/'.$relatedModule.'/'.$function_name.'.php';
			if (function_exists($function_name)) {
				$modObj->registerMethod($function_name);
			}
		}
		$relatedListData = $modObj->$function_name($recordid, getTabid($currentModule), $relationInfo['relatedTabId'], $actions);
		require_once 'Smarty_setup.php';
		global $theme, $mod_strings, $app_strings;
		$theme_path='themes/'.$theme.'/';
		$image_path=$theme_path.'images/';

		$smarty = new vtigerCRM_Smarty;
		$smarty->assign('RESET_COOKIE', $resetCookie);
		// vtlib customization: Related module could be disabled, check it
		if (is_array($relatedListData)) {
			if (($relatedModule == 'Contacts' || $relatedModule == 'Leads' || $relatedModule == 'Accounts') && $currentModule == 'Campaigns' && !$resetCookie) {
				// this logic is used for listview checkbox selection propagation.
				$checkedRecordIdString = (empty($_REQUEST[$relatedModule.'_all']) ?
					(empty($_COOKIE[$relatedModule.'_all']) ? '' : $_COOKIE[$relatedModule.'_all']) : $_REQUEST[$relatedModule.'_all']);
				$checkedRecordIdString = rtrim($checkedRecordIdString, ';');
				$checkedRecordIdList = explode(';', $checkedRecordIdString);
				$relatedListData['checked']=array();
				if (isset($relatedListData['entries'])) {
					foreach ($relatedListData['entries'] as $key => $val) {
						if (in_array($key, $checkedRecordIdList)) {
							$relatedListData['checked'][$key] = 'checked';
						} else {
							$relatedListData['checked'][$key] = '';
						}
					}
				}
				$smarty->assign('SELECTED_RECORD_LIST', $checkedRecordIdString);
			}
		}
		require_once 'include/ListView/RelatedListViewSession.php';
		RelatedListViewSession::addRelatedModuleToSession($relationId, $header);

		$smarty->assign('MOD', $mod_strings);
		$smarty->assign('APP', $app_strings);
		$smarty->assign('THEME', $theme);
		$smarty->assign('IMAGE_PATH', $image_path);
		$smarty->assign('ID', $recordid);
		$smarty->assign('MODULE', $currentModule);
		$smarty->assign('RELATED_MODULE', $relatedModule);
		$smarty->assign('HEADER', $header);
		$smarty->assign('RELATEDLISTDATA', $relatedListData);

		$smarty->display('RelatedListDataContents.tpl');
	}
} elseif ($ajaxaction == 'DISABLEMODULE') {
	$relationId = vtlib_purify($_REQUEST['relation_id']);
	if (!empty($relationId) && ((int)$relationId) > 0) {
		$header = vtlib_purify($_REQUEST['header']);
		require_once 'include/ListView/RelatedListViewSession.php';
		RelatedListViewSession::removeRelatedModuleFromSession($relationId, $header);
	}
	echo 'SUCCESS';
}
?>
