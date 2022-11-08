<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/upsert.php';
global $adb, $log, $current_user;
error_reporting(E_ALL);
ini_set('display_errors', 'on');
set_time_limit(0);
ini_set('memory_limit', '1024M');

$usr = new Users();
$current_user = Users::getActiveAdminUser();
$roleid = $current_user->roleid;
$subrole = getRoleSubordinates($roleid);
$uservalues = array_merge($subrole, array($roleid));
$subrole = implode('|##|', $uservalues);
$userWSID = vtws_getEntityId('Users').'x';
$cvrs = $adb->query('select * from vtiger_customview');
while ($cv = $adb->fetch_array($cvrs)) {
	$default_values =  array(
		'cvid' => $cv['cvid'],
		'cvcreate' => '0',
		'cvretrieve' => '1',
		'cvupdate' => '1',
		'cvdelete' => '1',
		'cvdefault' => $cv['setdefault'],
		'cvapprove' =>'0',
		'setpublic' => $cv['status'] == CV_STATUS_PENDING ? 1 : 0,
		'mandatory' => '0',
		'module_list' => $cv['entitytype'],
		'assigned_user_id' => $userWSID.$cv['userid'],
		'cvrole' => $subrole
	);
	$searchOn = 'cvid';
	$updatedfields = 'cvid';
	vtws_upsert('cbCVManagement', $default_values, $searchOn, $updatedfields, $current_user);
	echo 'Processed custom view: '.$cv['cvid']."\n";
}
?>