<?php
/*+********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/utils/UserInfoUtil.php');

global $adb;
$del_id = vtlib_purify($_REQUEST['delete_group_id']);
$assignType = vtlib_purify($_REQUEST['assigntype']);

if ($assignType == 'T') {
	$transferId = vtlib_purify($_REQUEST['transfer_group_id']);
} elseif ($assignType == 'U') {
	$transferId = vtlib_purify($_REQUEST['transfer_user_id']);
}

deleteGroup($del_id,$transferId);

header("Location: index.php?action=listgroups&module=Settings&parenttab=Settings");
?>