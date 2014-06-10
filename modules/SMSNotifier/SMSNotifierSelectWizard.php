<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('Smarty_setup.php');

include_once dirname(__FILE__) . '/SMSNotifier.php';

global $theme, $currentModule, $mod_strings, $app_strings, $current_user, $adb;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("IS_ADMIN", is_admin($current_user));

if(SMSNotifier::checkServer()) {
	
	$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
    $idstring = vtlib_purify($_REQUEST['idstring']);
	$idstring = trim($idstring, ';');
	$idlist = getSelectedRecords($_REQUEST,$_REQUEST['sourcemodule'],$idstring,$excludedRecords);//explode(';', $idstring);
	
	$sourcemodule = vtlib_purify($_REQUEST['sourcemodule']);
	
	$capturedFieldInfo = array();
	$capturedFieldNames = array();
	
	// Analyze the phone fields for the selected module.
	$phoneTypeFieldsResult = $adb->pquery("SELECT fieldid,fieldname,fieldlabel FROM vtiger_field WHERE uitype=11 AND tabid=? AND presence in (0,2)", array(getTabid($sourcemodule)));
	if($phoneTypeFieldsResult && $adb->num_rows($phoneTypeFieldsResult)) {
		while($resultrow = $adb->fetch_array($phoneTypeFieldsResult)) {
			$checkFieldPermission = getFieldVisibilityPermission( $sourcemodule, $current_user->id, $resultrow['fieldname'] );
			if($checkFieldPermission == '0') {
				$fieldlabel = getTranslatedString( $resultrow['fieldlabel'], $sourcemodule );
				$capturedFieldNames[] = $resultrow['fieldname'];
				$capturedFieldInfo[$resultrow['fieldid']] = array($fieldlabel => $resultrow['fieldname']);
			}
		}
	}
	// END
	
	$capturedFieldValues = array();
	
	// If single record is selected, good to show the numbers also in the wizard.
	if(count($idlist) === 1) {
		$focusInstance = CRMEntity::getInstance($sourcemodule);
		$focusInstance->retrieve_entity_info($idlist[0], $sourcemodule);
		foreach($capturedFieldNames as $fieldname) {
			if(isset($focusInstance->column_fields[$fieldname])) {
				$capturedFieldValues[$fieldname] = $focusInstance->column_fields[$fieldname];
			}
		}		
	}
	
	$smarty->assign('PHONEFIELDS', $capturedFieldInfo);
	$smarty->assign('FIELDVALUES', $capturedFieldValues);
	$smarty->assign('IDSTRING', $idstring);
	$smarty->assign('SOURCEMODULE', $sourcemodule);
    $smarty->assign('excludedRecords',$excludedRecords);
    $smarty->assign('VIEWID',$_REQUEST['viewname']);
    $smarty->assign('SEARCHURL',$_REQUEST['searchurl']);

	$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSNotifierSelectWizard.tpl'));
	
} else {
	$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSNotifierServerNotAvailable.tpl'));
}



?>
