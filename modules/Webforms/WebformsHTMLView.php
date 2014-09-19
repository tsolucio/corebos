<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('modules/Webforms/Webforms.php');
require_once('modules/Webforms/model/WebformsModel.php');
require_once('Smarty_setup.php');
require_once 'config.inc.php';

Webforms::checkAdminAccess($current_user);

$webformModel=Webforms_Model::retrieveWithId($_REQUEST['id']);
$webformFields=$webformModel->getFields();

$smarty = new vtigerCRM_Smarty();

$smarty->assign('ACTIONPATH',$site_URL);
$smarty->assign('WEBFORM',new Webforms());
$smarty->assign('WEBFORMMODEL',$webformModel);
$smarty->assign('WEBFORMFIELDS',$webformFields);
$smarty->assign('LANGUAGE',$current_language);
$smarty->display(vtlib_getModuleTemplate($currentModule,'HTMLView.tpl'));
?>
