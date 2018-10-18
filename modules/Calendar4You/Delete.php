<?php
/*********************************************************************************
 * The content of this file is subject to the Calendar4You Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Calendar4You/Calendar4You.php';
require_once 'modules/Calendar4You/CalendarUtils.php';

global $currentModule, $current_user;

$Calendar4You = new Calendar4You();

$Calendar4You->GetDefPermission($current_user->id);

$delete_permissions = $Calendar4You->CheckPermissions('DELETE', $_REQUEST['record']);

if (!$delete_permissions) {
	NOPermissionDiv();
}

$currentModule = 'Calendar';
$focus = CRMEntity::getInstance($currentModule);

require_once 'include/logging.php';
$log = LoggerManager::getLogger('task_delete');
$url = getBasic_Advance_SearchURL();

if (!isset($_REQUEST['record'])) {
	die($mod_strings['ERR_DELETE_RECORD']);
}
DeleteEntity('Calendar', $_REQUEST['return_module'], $focus, $_REQUEST['record'], $_REQUEST['return_id']);
$url = 'Location: index.php?module='.vtlib_purify($_REQUEST['return_module']).'&action='.vtlib_purify($_REQUEST['return_action'])
	.'&record='.vtlib_purify($_REQUEST['return_id']).'&relmodule='.vtlib_purify($_REQUEST['module']).$url;
header($url);
?>