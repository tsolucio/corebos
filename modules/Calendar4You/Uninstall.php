<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ******************************************************************************* */

require_once('Smarty_setup.php');
require_once("include/utils/utils.php");

global $currentModule;
$CreditNotes4You = CRMEntity::getInstance($currentModule);

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("MODULE", $currentModule);

$smarty->display(vtlib_getModuleTemplate($currentModule, 'Uninstall.tpl'));
