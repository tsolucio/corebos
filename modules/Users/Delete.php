<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$sql= 'delete from vtiger_salesmanactivityrel where smid=? and activityid = ?';
$adb->pquery($sql, array(vtlib_purify($_REQUEST['record']), vtlib_purify($_REQUEST['return_id'])));

if (isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Calendar') {
	$mode = '&activity_mode=Events';
}

$req = new Vtiger_Request();
$req->set('return_module', $_REQUEST['return_module']);
$req->set('return_action', $_REQUEST['return_action']);
$req->set('return_record', $_REQUEST['return_id']);
$req->set('return_relmodule', $_REQUEST['module']);
header('Location: index.php?' . $req->getReturnURL() . $mode);
?>