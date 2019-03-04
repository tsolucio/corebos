<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

$module_import_step = vtlib_purify($_REQUEST['module_import']);

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

if ($module_import_step == 'Step2') {
	if (!is_dir($modulemanager_uploaddir)) {
		mkdir($modulemanager_uploaddir);
	}
	$uploadfile = 'usermodule_' . time() . '.zip';
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";
	checkFileAccess($modulemanager_uploaddir);

	if ($_REQUEST['installtype'] == 'file') {
		if (!move_uploaded_file($_FILES['module_zipfile']['tmp_name'], $uploadfilename)) {
			$smarty->assign('MODULEIMPORT_FAILED', 'true');
			$uploadfilename = null;
		}
	} else {
		$url = $_REQUEST['module_url'];
		if (!preg_match('%^\w+://%', $url)) {
			$smarty->assign('MODULEIMPORT_FAILED', 'true');
			$uploadfilename = null;
		} else {
			if (!preg_match('/.zip$/', $url)) {
				$url = rtrim($url, '/');
				$url .= '/archive/master.zip';
			}
			$input = fopen($url, 'r');
			if (!file_put_contents($uploadfilename, $input)) {
				$smarty->assign('MODULEIMPORT_FAILED', 'true');
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
			if (!empty($newEntryName)) {
				$za->renameIndex($i, $newEntryName);
			} else {
				$za->deleteIndex($i);
			}
		}
		@$za->close();

		$package = new Vtiger_Package();
		$moduleimport_name = $package->getModuleNameFromZip($uploadfilename);

		if ($moduleimport_name == null) {
			$smarty->assign('MODULEIMPORT_FAILED', 'true');
			$smarty->assign('MODULEIMPORT_FILE_INVALID', 'true');
		} else {
			$smarty->assign('MODULEIMPORT_FAILED', '');
			$smarty->assign('MODULEIMPORT_FILE_INVALID', '');

			if (!$package->isLanguageType() && !$package->isModuleBundle()) {
				$moduleInstance = Vtiger_Module::getInstance($moduleimport_name);
				$moduleimport_exists=($moduleInstance)? 'true' : 'false';
				$moduleimport_dir_name="modules/$moduleimport_name";
				$moduleimport_dir_exists= (is_dir($moduleimport_dir_name)? 'true' : 'false');

				$smarty->assign('MODULEIMPORT_EXISTS', $moduleimport_exists);
				$smarty->assign('MODULEIMPORT_DIR', $moduleimport_dir_name);
				$smarty->assign('MODULEIMPORT_DIR_EXISTS', $moduleimport_dir_exists);
			}

			$moduleimport_dep_vtversion = $package->getDependentVtigerVersion();
			$moduleimport_license = $package->getLicense();

			$smarty->assign('MODULEIMPORT_FILE', $uploadfile);
			$smarty->assign('MODULEIMPORT_TYPE', $package->type());
			$smarty->assign('MODULEIMPORT_NAME', $moduleimport_name);
			$smarty->assign('MODULEIMPORT_DEP_VTVERSION', $moduleimport_dep_vtversion);
			$smarty->assign('MODULEIMPORT_LICENSE', $moduleimport_license);
		}
	}
} elseif ($module_import_step == 'Step3') {
	$uploadfile = basename(vtlib_purify($_REQUEST['module_import_file']));
	$uploadfilename = "$modulemanager_uploaddir/$uploadfile";
	checkFileAccess($uploadfilename);

	//$overwritedir = ($_REQUEST['module_dir_overwrite'] == 'true')? true : false;
	$overwritedir = false; // Disallowing overwrites through Module Manager UI

	$importtype = $_REQUEST['module_import_type'];
	if (strtolower($importtype) == 'language') {
		$package = new Vtiger_Language();
	} else {
		$package = new Vtiger_Package();
	}
	$Vtiger_Utils_Log = true;
	ob_start();
	$package->import($uploadfilename, $overwritedir);
	unlink($uploadfilename);
	$importinfo = ob_get_clean();
	$smarty->assign('MODULEIMPORT_INFO', $importinfo);
}

$smarty->display("Settings/ModuleManager/ModuleImport$module_import_step.tpl");
?>