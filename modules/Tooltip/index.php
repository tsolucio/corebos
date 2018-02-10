<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
global $currentModule;
echo '<br><br>';
$smarty = new vtigerCRM_Smarty();
$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
$smarty->assign('ERROR_MESSAGE', getTranslatedString('TooltipInfo', $currentModule));
$smarty->display('applicationmessage.tpl');
?>
