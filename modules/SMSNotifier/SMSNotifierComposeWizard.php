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

global $theme, $currentModule, $mod_strings, $app_strings, $current_user;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty();
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", $mod_strings);

$excludedRecords=vtlib_purify($_REQUEST['excludedRecords']);
$idstring = vtlib_purify($_REQUEST['idstring']);
$idstring = trim($idstring, ';');
$idlist = getSelectedRecords($_REQUEST,$_REQUEST['sourcemodule'],$idstring,$excludedRecords);//explode(';', $idstring);

$sourcemodule = vtlib_purify($_REQUEST['sourcemodule']);

$phonefields = vtlib_purify($_REQUEST['phonefields']);
$phonefields = trim($phonefields, ';');

$smarty->assign('PHONEFIELDS', $phonefields);
$smarty->assign('IDSTRING', $idstring);
$smarty->assign('SOURCEMODULE', $sourcemodule);
$smarty->assign('excludedRecords',$excludedRecords);
$smarty->assign('VIEWID',$_REQUEST['viewname']);
$smarty->assign('SEARCHURL',$_REQUEST['searchurl']);

$smarty->display(vtlib_getModuleTemplate($currentModule, 'SMSNotifierComposeWizard.tpl'));

?>