<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Package.php';

global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');
$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
$package = new Vtiger_Package();
//$rdo = $package->importManifest('modules/cbupdater/manifest.xml');
//$rdo = $package->importManifest('modules/Webforms/manifest.xml');
//$package->initImport('TSEmail_540.zip', true);
$rdo = $package->importManifest('include/language/it_it.manifest.xml');
?>
