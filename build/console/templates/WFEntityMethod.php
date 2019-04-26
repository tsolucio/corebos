<?php
require_once 'include/utils/utils.php';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';

$emm = new VTEntityMethodManager($adb);
$emm->addEntityMethod("MODULE", "DESC", "PATH", "FUNCTION_NAME");

//CREATE WF