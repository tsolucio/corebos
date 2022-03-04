<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/utils/Session.php';
coreBOS_Session::init();
require_once 'include/CustomFieldUtil.php';
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/ListViewUtils.php';
require_once 'modules/CustomView/CustomView.php';

global $mod_strings,$app_strings,$theme,$adb,$current_user;

$theme_path='themes/'.$theme.'/';

$iCurRecord = vtlib_purify($_REQUEST['CurRecordId']);
$sModule = vtlib_purify($_REQUEST['CurModule']);

require_once 'data/CRMEntity.php';
$foc_obj = CRMEntity::getInstance($sModule);

$query = $adb->pquery('SELECT tablename,entityidfield, fieldname from vtiger_entityname WHERE modulename = ?', array($sModule));
$table_name = $adb->query_result($query, 0, 'tablename');
$field_name = $adb->query_result($query, 0, 'fieldname');
$id_field = $adb->query_result($query, 0, 'entityidfield');
$fieldname = explode(',', $field_name);
$fields_array = array($sModule=>$fieldname);
$id_array = array($sModule=>$id_field);
$tables_array = array($sModule=>$table_name);

$permittedFieldNameList = array();
foreach ($fieldname as $fieldName) {
	$checkForFieldAccess = $fieldName;
	// Handling case where fieldname in vtiger_entityname mismatches fieldname in vtiger_field
	if ($sModule == 'HelpDesk' && $checkForFieldAccess == 'title') {
		$checkForFieldAccess = 'ticket_title';
	} elseif ($sModule == 'Documents' && $checkForFieldAccess == 'title') {
		$checkForFieldAccess = 'notes_title';
	}
	if (getFieldVisibilityPermission($sModule, $current_user->id, $checkForFieldAccess) == '0') {
		$permittedFieldNameList[] = $fieldName;
	}
}

$cv = new CustomView();
$viewId = $cv->getViewId($sModule);
if (!empty($_SESSION[$sModule.'_DetailView_Navigation'.$viewId])) {
	$recordNavigationInfo = json_decode($_SESSION[$sModule.'_DetailView_Navigation'.$viewId], true);
	$recordList = array();
	$recordIndex = null;
	$recordPageMapping = array();
	foreach ($recordNavigationInfo as $start => $recordIdList) {
		foreach ($recordIdList as $index => $recordId) {
			if (!isRecordExists($recordId)) {
				continue;
			}
			$recordList[] = $recordId;
			$recordPageMapping[$recordId] = $start;
			if ($recordId == $iCurRecord) {
				$recordIndex = count($recordList)-1;
			}
		}
	}
} else {
	$recordList = array();
}
$output = '<section aria-describedby="dialog-body-id-114" aria-labelledby="dialog-heading-id-3" class="slds-popover slds-popover_walkthrough slds-nubbin_left" role="dialog">
				<a class="slds-button slds-button_icon slds-button_icon-small slds-float_right slds-popover__close slds-button_icon-inverse" href="javascript:fninvsh(\'lstRecordLayout\');" title="Close dialog">
					<svg class="slds-button__icon" aria-hidden="true">
						<use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
					</svg>
					<span class="slds-assistive-text">Close dialog</span>
				</a>
				<header class="slds-popover__header slds-p-vertical_medium">
				<h2 id="dialog-heading-id-3" class="slds-text-heading_medium">'.$app_strings['LBL_JUMP_To'].'</h2>
				</header>
				<div class="slds-popover__body" id="dialog-body-id-114">
					
						<p>'.getTranslatedString($sModule, $sModule).':</p>
						';


if (!empty($recordList)) {
	$displayRecordCount = 10;
	$count = count($recordList);
	$idListEndIndex = ($count < ($recordIndex+$displayRecordCount))? ($count+1) : ($recordIndex+$displayRecordCount+1);
	$idListStartIndex = $recordIndex-$displayRecordCount;
	if ($idListStartIndex < 0) {
		$idListStartIndex = 0;
	}
	$idsArray = array_slice($recordList, $idListStartIndex, ($idListEndIndex - $idListStartIndex));

	$selectColString = implode(',', $permittedFieldNameList).', '.$id_array[$sModule];
	$fieldQuery = "SELECT $selectColString from ".$tables_array[$sModule].' WHERE '.$id_array[$sModule].' IN ('. generateQuestionMarks($idsArray) .')';

	$fieldResult = $adb->pquery($fieldQuery, $idsArray);
	$numOfRows = $adb->num_rows($fieldResult);
	$recordNameMapping = array();
	for ($i=0; $i<$numOfRows; ++$i) {
		$recordId = $adb->query_result($fieldResult, $i, $id_array[$sModule]);
		$fieldValue = '';
		foreach ($permittedFieldNameList as $fieldName) {
			$fieldValue .= ' '.$adb->query_result($fieldResult, $i, $fieldName);
		}
		$fieldValue = textlength_check($fieldValue);
		$recordNameMapping[$recordId] = $fieldValue;
	}
	foreach ($idsArray as $id) {
		if ($id===$iCurRecord) {
			$output .= '<ul><li>'.$recordNameMapping[$id].'</li></ul>';
		} else {
			$output .= '<ul><li><a href="index.php?module='.$sModule.
				'&action=DetailView&record='.$id.'&start='.$recordPageMapping[$id].'">'.$recordNameMapping[$id].'</a></li></ul>';
		}
	}
}
$output .= '</div>';
$output .= '</section>';

echo $output;
?>