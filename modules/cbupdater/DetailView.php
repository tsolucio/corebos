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

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log, $singlepane_view;

$smarty = new vtigerCRM_Smarty();

include('modules/cbupdater/forcedButtons.php');

require_once 'modules/Vtiger/DetailView.php';

$smarty->assign('EDIT_PERMISSION', 'no');
$singlepane_view = 'true';
$smarty->assign('SinglePane_View', $singlepane_view);
$smarty->assign('EDIT_DUPLICATE', 'notpermitted');
$smarty->assign('DELETE', 'notpermitted');

$smarty->display('DetailView.tpl');
?>