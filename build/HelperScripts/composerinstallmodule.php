<?php
@error_reporting(0);
@ini_set('display_errors', 'off');
@set_time_limit(0);
@ini_set('memory_limit','1024M');

if (count($argv)==3) {
	$module = $argv[1];
	$type = $argv[2];
	require_once('vtlib/Vtiger/Module.php');
	require_once('vtlib/Vtiger/Package.php');
	global $current_user,$adb;
	$Vtiger_Utils_Log = false;  // Turn off debugging level
	$current_user = new Users();
	$current_user->retrieveCurrentUserInfoFromFile(1); // admin
	$package = new Vtiger_Package();
	$tabrs = $adb->pquery('select count(*) from vtiger_tab where name=?',array($module));
	if ($tabrs and $adb->query_result($tabrs, 0,0)==1) {
		vtlib_toggleModuleAccess($module,true);
		echo "Module activated: $module \n";
	} else {
		if (strtolower($type)=='language')
			$rdo = $package->importManifest('include/language/'.$module.'.manifest.xml');
		else
			$rdo = $package->importManifest('modules/'.$module.'/manifest.xml');
		echo "Module installed: $module \n";
	}
} else {
	echo "\n Incorrect amount of parameters \n";
}
?>
