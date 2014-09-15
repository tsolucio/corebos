<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ******************************************************************************* */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Module.php');
$module = Vtiger_Module::getInstance('Calendar4You');
if ($module) {
    $module->delete();
    @shell_exec('rm -R modules/Calendar4You');
    @shell_exec('rm -R Smarty/templates/modules/Calendar4You');
}
