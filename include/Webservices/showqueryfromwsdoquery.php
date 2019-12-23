<?php
function showqueryfromwsdoquery($query) {
	global $adb, $log, $current_user;
	include_once "include/Webservices/QueryParser.php";
	include_once "include/Webservices/VtigerModuleOperation.php";
	$webserviceObject = VtigerWebserviceObject::fromName($adb, 'Accounts');
	$vtModuleOperation = new VtigerModuleOperation($webserviceObject, $current_user, $adb, $log);
	$querynormal = $vtModuleOperation->wsVTQL2SQL("$query", $meta, $queryRelatedModules);
	return $querynormal;
}