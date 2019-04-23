<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
function vtSortFieldsJson($request) {
	$moduleName = vtlib_purify($request['module_name']);
	checkFileAccessForInclusion("modules/$moduleName/$moduleName.php");
	require_once "modules/$moduleName/$moduleName.php";
	$focus = new $moduleName();
	echo json_encode($focus->sortby_fields);
}
vtSortFieldsJson($_REQUEST);
?>
