<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Vtiger/EditView.php';
//Show or not the Header to copy address to left or right
$smarty->assign('SHOW_COPY_ADDRESS', GlobalVariable::getVariable('Application_Show_Copy_Address', 1, $currentModule, $current_user->id));
$smarty->display('salesEditView.tpl');
?>