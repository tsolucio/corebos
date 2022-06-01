<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$schannualdates = DateTimeField::convertToDBFormat($_REQUEST['schdate']);
$_REQUEST['schannualdates'] = json_encode(array($schannualdates));
$fmt = (date('a', strtotime($_REQUEST['schtime'])));
$_REQUEST['schtime'] = DateTimeField::formatDatebaseTimeString($_REQUEST['schtime'], $fmt);
$_REQUEST['schdayofmonth'] = isset($_REQUEST['schdayofmonth']) ? json_encode($_REQUEST['schdayofmonth']) : '';
$schdayofweek = array();
if (isset($_REQUEST['sun_flag']) && $_REQUEST['sun_flag'] != null) {
	$schdayofweek[] = 1;
}
if (isset($_REQUEST['mon_flag']) && $_REQUEST['mon_flag'] != null) {
	$schdayofweek[] = 2;
}
if (isset($_REQUEST['tue_flag']) && $_REQUEST['tue_flag'] != null) {
	$schdayofweek[] = 3;
}
if (isset($_REQUEST['wed_flag']) && $_REQUEST['wed_flag'] != null) {
	$schdayofweek[] = 4;
}
if (isset($_REQUEST['thu_flag']) && $_REQUEST['thu_flag'] != null) {
	$schdayofweek[] = 5;
}
if (isset($_REQUEST['fri_flag']) && $_REQUEST['fri_flag'] != null) {
	$schdayofweek[] = 6;
}
if (isset($_REQUEST['sat_flag']) && $_REQUEST['sat_flag'] != null) {
	$schdayofweek[] = 7;
}
$_REQUEST['schdayofweek'] = json_encode($schdayofweek);
require_once 'modules/Vtiger/Save.php';
?>
