<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Module.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $adb;

$emm = new VTEntityMethodManager($adb);
$emm->addEntityMethod("Accounts", "Update Contact Assigned To", "include/wfMethods/updateContactAssignedTo.php", "updateContactAssignedTo");
echo 'add Workflow Custom Function complete!';
?>
