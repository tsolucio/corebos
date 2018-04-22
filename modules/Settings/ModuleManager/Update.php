<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$module_update_step = vtlib_purify($_REQUEST['module_update']);

require_once 'Smarty_setup.php';
require_once 'vtlib/Vtiger/Package.php';
require_once 'vtlib/Vtiger/Language.php';

global $mod_strings,$app_strings,$theme;
$smarty = new vtigerCRM_Smarty;
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");

global $modulemanager_uploaddir; // Defined in modules/Settings/ModuleManager.php

$target_modulename = isset($_REQUEST['target_modulename']) ? $_REQUEST['target_modulename'] : '';

if ($module_update_step == 'Step2') {
	if (!is_dir($modulemanager_uploaddir)) {
		mkdir($modulemanager_uploaddir);
	}
	$uploadfile = 'usermodule_' . time() . '.zip';
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";
	checkFileAccess($modulemanager_uploaddir);

	if ($_REQUEST['installtype'] == 'file') {
		if (!move_uploaded_file($_FILES['module_zipfile']['tmp_name'], $uploadfilename)) {
			$smarty->assign('MODULEUPDATE_FAILED', 'true');
			$uploadfilename = null;
		}
	} else {
		$url = $_REQUEST['module_url'];
		if (!preg_match('%^\w+://%', $url)) {
			$smarty->assign('MODULEUPDATE_FAILED', 'true');
			$uploadfilename = null;
		} else {
			if (!preg_match('/.zip$/', $url)) {
				$url = rtrim($url, '/');
				$url .= '/archive/master.zip';
			}
			$input = fopen($url, 'r');
			if (!file_put_contents($uploadfilename, $input)) {
				$smarty->assign('MODULEUPDATE_FAILED', 'true');
				$uploadfilename = null;
			}
		}
	}
	if ($uploadfilename) {
		// Check ZIP file contents for extra directory at the top
		$za = new ZipArchive();
		$za->open($uploadfilename);
		for ($i = 0; $i < $za->numFiles; $i++) {
			$entryName = $za->getNameIndex($i);
			$firstSlash = strpos($entryName, '/');
			if ($entryName === 'manifest.xml' || $entryName === './manifest.xml' || $firstSlash === false) {
				$za->unchangeAll();
				break;
			}
			$newEntryName = substr($entryName, $firstSlash + 1);
			if ($newEntryName !== false) {
				$za->renameIndex($i, $newEntryName);
			} else {
				$za->deleteIndex($i);
			}
		}
		$za->close();

		$package = new Vtiger_Package();
		$moduleupdate_name = $package->getModuleNameFromZip($uploadfilename);

		if ($moduleupdate_name == null) {
			$smarty->assign('MODULEUPDATE_FAILED', 'true');
			$smarty->assign('MODULEUPDATE_FILE_INVALID', 'true');
		} elseif (!$package->isLanguageType() && ($moduleupdate_name != $target_modulename)) {
			$smarty->assign('MODULEUPDATE_FAILED', 'true');
			$smarty->assign('MODULEUPDATE_NAME_MISMATCH', 'true');
		} elseif ($package->isLanguageType() && (trim($package->xpath_value('prefix')) != $target_modulename)) {
			$smarty->assign('MODULEUPDATE_FAILED', 'true');
			$smarty->assign('MODULEUPDATE_NAME_MISMATCH', 'true');
		} else {
			$moduleupdate_dep_vtversion = $package->getDependentVtigerVersion();
			$moduleupdate_license = $package->getLicense();
			$moduleupdate_version = $package->getVersion();

			if (!$package->isLanguageType()) {
				$moduleInstance = Vtiger_Module::getInstance($moduleupdate_name);
				$moduleupdate_exists=($moduleInstance)? 'true' : 'false';
				$moduleupdate_dir_name="modules/$moduleupdate_name";
				$moduleupdate_dir_exists= (is_dir($moduleupdate_dir_name)? 'true' : 'false');

				$smarty->assign('MODULEUPDATE_CUR_VERSION', ($moduleInstance? $moduleInstance->version : ''));
				$smarty->assign('MODULEUPDATE_NOT_EXISTS', !($moduleupdate_exists));
				$smarty->assign('MODULEUPDATE_DIR', $moduleupdate_dir_name);
				$smarty->assign('MODULEUPDATE_DIR_NOT_EXISTS', !($moduleupdate_dir_exists));

				// If version is matching, dis-allow migration
				if (version_compare($moduleupdate_version, $moduleInstance->version, '=')) {
					$smarty->assign('MODULEUPDATE_FAILED', 'true');
					$smarty->assign('MODULEUPDATE_SAME_VERSION', 'true');
				}
			}

			$smarty->assign('MODULEUPDATE_FILE', $uploadfile);
			$smarty->assign('MODULEUPDATE_TYPE', $package->type());
			$smarty->assign('MODULEUPDATE_NAME', $moduleupdate_name);
			$smarty->assign('MODULEUPDATE_DEP_VTVERSION', $moduleupdate_dep_vtversion);
			$smarty->assign('MODULEUPDATE_VERSION', $moduleupdate_version);
			$smarty->assign('MODULEUPDATE_LICENSE', $moduleupdate_license);
		}
	}
} elseif ($module_update_step == 'Step3') {
	$uploadfile = basename(vtlib_purify($_REQUEST['module_import_file']));
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";
	checkFileAccess($uploadfilename);

	//$overwritedir = ($_REQUEST['module_dir_overwrite'] == 'true')? true : false;
	$overwritedir = false; // Disallowing overwrites through Module Manager UI

	$updatetype = $_REQUEST['module_update_type'];
	if (strtolower($updatetype) == 'language') {
		$package = new Vtiger_Language();
	} else {
		$package = new Vtiger_Package();
	}
	$Vtiger_Utils_Log = true;
	ob_start();
	$package->update(Vtiger_Module::getInstance($target_modulename), $uploadfilename);
	unlink($uploadfilename);
	$updateinfo = ob_get_clean();
	$smarty->assign('MODULEUPDATE_INFO', $updateinfo);
}

$smarty->display("Settings/ModuleManager/ModuleUpdate$module_update_step.tpl");
?>