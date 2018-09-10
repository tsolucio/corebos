<?php
@error_reporting(0);
@ini_set('display_errors', 'off');
@set_time_limit(0);
@ini_set('memory_limit', '1024M');

if (count($argv)==3) {
	$module = $argv[1];
	$type = $argv[2];
	require_once 'vtlib/Vtiger/Module.php';
	require_once 'vtlib/Vtiger/Package.php';
	global $current_user,$adb;
	$Vtiger_Utils_Log = false;  // Turn off debugging level
	$current_user = Users::getActiveAdminUser();
	$package = new Vtiger_Package();
	$tabrs = $adb->pquery('select count(*) from vtiger_tab where name=?', array($module));
	if ($tabrs && $adb->query_result($tabrs, 0, 0)==1) { // it exists already so we are updating
		if (strtolower($type)=='language') {  // just copy files and activate
			vtlib_toggleModuleAccess($module, true);
		} else {
			$moduleInstance = Vtiger_Module::getInstance($module);
			$package->loadManifestFromFile('modules/'.$module.'/manifest.xml');
			$rdo = $package->update_Module($moduleInstance);
		}
		echo "Module updated: $module \n";
	} else {
		if (strtolower($type)=='language') {
			$rdo = $package->importManifest('include/language/'.$module.'.manifest.xml');
		} else {
			$rdo = $package->importManifest('modules/'.$module.'/manifest.xml');
		}
		echo "Module installed: $module \n";
	}
} else {
	echo "\n Incorrect amount of parameters \n";
}
?>
