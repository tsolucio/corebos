<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$module_export = vtlib_purify($_REQUEST['module_export']);
$fail = false;
if (empty($current_user)) {
	if (isset($_SESSION['authenticated_user_id']) && (isset($_SESSION['app_unique_key']) && $_SESSION['app_unique_key'] == $application_unique_key)) {
		$current_user = new Users();
		$result = $current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
		if ($result == null) {
			$fail = true;
		} elseif (!is_admin($current_user)) {
			$fail = true;
		}
	} else {
		$fail = true;
	}
} elseif (!is_admin($current_user)) {
	$fail = true;
}
if ($fail) {
	coreBOS_Session::destroy();
	header('Location: index.php?action=Login&module=Users');
}
require_once 'vtlib/Vtiger/Package.php';
require_once 'vtlib/Vtiger/Module.php';

$package = new Vtiger_Package();
$module = Vtiger_Module::getInstance($module_export);
if ($module) {
	if (isset($_REQUEST['manifestfs'])) {
		Vtiger_Package::packageFromFilesystem($module_export, false, true);
	} else {
		$package->export($module, '', "$module_export.zip", true);
	}
} else {
	global $adb,$vtiger_current_version;
	$lngrs = $adb->pquery('select * from vtiger_language where prefix=?', array($module_export));
	if ($lngrs && $adb->num_rows($lngrs)==1) { // we have a language file
		$lnginfo = $adb->fetch_array($lngrs);
		$lngxml = 'include/language/'.$lnginfo['prefix'].'.manifest.xml';
		if (!file_exists($lngxml)) {
			$mnf = fopen($lngxml, 'w');
			fwrite($mnf, "<?xml version='1.0'?>\n");
			fwrite($mnf, "<module>\n");
			fwrite($mnf, "<type>language</type>\n");
			fwrite($mnf, "<name>".$lnginfo['name']."</name>\n");
			fwrite($mnf, "<label>".$lnginfo['label']."</label>\n");
			fwrite($mnf, "<prefix>".$lnginfo['prefix']."</prefix>\n");
			fwrite($mnf, "<version>".$vtiger_current_version."</version>\n");
			fwrite($mnf, "<dependencies>\n");
			fwrite($mnf, "  <vtiger_version>".$vtiger_current_version."</vtiger_version>\n");
			fwrite($mnf, "  <vtiger_max_version>".$vtiger_current_version."</vtiger_max_version>\n");
			fwrite($mnf, "</dependencies>\n");
			fwrite($mnf, "</module>\n");
			fclose($mnf);
		}
		$package->languageFromFilesystem($lnginfo['prefix'], $lnginfo['name'], true);
	}
}
exit;
?>