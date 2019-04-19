<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'Smarty_setup.php';

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

include 'modules/cbupdater/forcedButtons.php';

require_once 'modules/Vtiger/DetailView.php';

$singlepane_view = 'true';
$smarty->assign('SinglePane_View', $singlepane_view);
$smarty->assign('TODO_PERMISSION', 'no');
$smarty->assign('EVENT_PERMISSION', 'no');
$smarty->assign('EDIT_PERMISSION', 'no');
$smarty->assign('CREATE_PERMISSION', 'no');
$smarty->assign('DELETE', 'notpermitted');
$smarty->assign('CONTACT_PERMISSION', 'notpermitted');
$smarty->assign('IS_REL_LIST', isPresentRelatedLists($currentModule));
$smarty->display('DetailView.tpl');
?>
