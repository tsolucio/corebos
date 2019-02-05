<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if (isset($_REQUEST['record'])) {
	$recordId = (int)($_REQUEST['record']);
	deleteAsociatedWorkflow($recordId);
}
require_once 'modules/Vtiger/Delete.php';

function deleteAsociatedWorkflow($recordId) {
	global $adb;
	$result = $adb->pquery('select workflowid from vtiger_cbpulse WHERE cbpulseid=?', array($recordId));
	$workflowId = (int)$result->fields['workflowid'];
	$delIns = new VTWorkflowManager($adb);
	$delIns->delete($workflowId);
}
?>