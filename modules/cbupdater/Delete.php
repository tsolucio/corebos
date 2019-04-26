<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;

$module = urlencode(vtlib_purify($_REQUEST['module']));
$return_module = urlencode(vtlib_purify($_REQUEST['return_module']));
$return_action = urlencode(vtlib_purify($_REQUEST['return_action']));
if (isset($_REQUEST['return_id'])) {
	$return_id = urlencode(vtlib_purify($_REQUEST['return_id']));
} else {
	$return_id = (isset($_REQUEST['record']) ? urlencode(vtlib_purify($_REQUEST['record'])) : 0);
}
$url = getBasic_Advance_SearchURL();
header("Location: index.php?module=$return_module&action=$return_action&record=$return_id&relmodule=$module".$url);
?>