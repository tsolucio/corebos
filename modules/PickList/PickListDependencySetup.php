<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';
require_once 'modules/PickList/DependentPickListUtils.php';

global $app_strings, $current_language, $currentModule, $theme, $current_user;

$smarty = new vtigerCRM_Smarty;
$smarty->assign("APP", $app_strings);		//the include language files

if (!is_admin($current_user)) {
	$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
	die;
}

$smarty->assign('MOD', return_module_language($current_language, 'Settings'));	//the settings module language file
$smarty->assign('MOD_PICKLIST', return_module_language($current_language, 'PickList'));	//the picklist module language files

$fld_module = (!empty($_REQUEST['moduleName']) ? vtlib_purify($_REQUEST['moduleName']) : '');
$temp_module_strings = return_module_language($current_language, $fld_module);

$modules = Vtiger_DependencyPicklist::getDependentPickListModules();
$smarty->assign('MODULE_LISTS', $modules);

$smarty->assign('MODULE', $fld_module);
$smarty->assign('PICKLIST_MODULE', 'PickList');
$smarty->assign('THEME', $theme);

$subMode = (isset($_REQUEST['submode']) ? vtlib_purify($_REQUEST['submode']) : '');
$smarty->assign('SUBMODE', $subMode);

if (isset($_REQUEST['directmode']) && $_REQUEST['directmode'] == 'ajax') {
	if ($subMode == 'getpicklistvalues') {
		$fieldName = vtlib_purify($_REQUEST['fieldname']);
		$fieldValues = getAllPickListValues($fieldName);
		$picklistValues = array();
		for ($i=0; $i<count($fieldValues); ++$i) {
			$picklistValues[$fieldValues[$i]] = getTranslatedString($fieldValues[$i], $fld_module);
		}
		echo json_encode($picklistValues);
	} elseif ($subMode == 'editdependency') {
		$sourceField = (isset($_REQUEST['sourcefield']) ? vtlib_purify($_REQUEST['sourcefield']) : '');
		$targetField = (isset($_REQUEST['targetfield']) ? vtlib_purify($_REQUEST['targetfield']) : '');

		$cyclicDependencyExists = Vtiger_DependencyPicklist::checkCyclicDependency($fld_module, $sourceField, $targetField);

		if ($cyclicDependencyExists) {
			$smarty->assign('RETURN_URL', 'index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings&moduleName='.$fld_module);
			$smarty->display("modules/PickList/PickListDependencyCyclicError.tpl");
		} else {
			$available_module_picklist = Vtiger_DependencyPicklist::getAvailablePicklists($fld_module);
			$smarty->assign('ALL_LISTS', $available_module_picklist);
			$dependencyMap = array();
			if (!empty($sourceField) && !empty($targetField)) {
				$sourceFieldValues = array();
				$targetFieldValues = getAllPickListValues($targetField);

				foreach (getAllPickListValues($sourceField) as $key => $value) {
					$sourceFieldValues[$value] = $value;
				}

				$smarty->assign('SOURCE_VALUES', $sourceFieldValues);
				$smarty->assign('TARGET_VALUES', $targetFieldValues);

				$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
				$smarty->assign('DEPENDENT_PICKLISTS', $dependentPicklists);

				$dependencyMap = Vtiger_DependencyPicklist::getPickListDependency($fld_module, $sourceField, $targetField);
			} else {
				$smarty->assign('SOURCE_VALUES', array());
				$smarty->assign('TARGET_VALUES', array());
				$smarty->assign('DEPENDENT_PICKLISTS', array());
			}
			$smarty->assign('DEPENDENCY_MAP', $dependencyMap);

			$smarty->display('modules/PickList/PickListDependencyContents.tpl');
		}
	} else {
		if ($subMode == 'savedependency') {
			$dependencyMapping = vtlib_purify($_REQUEST['dependencymapping']);
			$dependencyMappingData = json_decode($dependencyMapping, true);
			Vtiger_DependencyPicklist::savePickListDependencies($fld_module, $dependencyMappingData);
		} elseif ($subMode == 'deletedependency') {
			$sourceField = (isset($_REQUEST['sourcefield']) ? vtlib_purify($_REQUEST['sourcefield']) : '');
			$targetField = (isset($_REQUEST['targetfield']) ? vtlib_purify($_REQUEST['targetfield']) : '');
			Vtiger_DependencyPicklist::deletePickListDependencies($fld_module, $sourceField, $targetField);
		}
		$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
		$smarty->assign('DEPENDENT_PICKLISTS', $dependentPicklists);
		$smarty->display('modules/PickList/PickListDependencyList.tpl');
	}
} else {
	$dependentPicklists = Vtiger_DependencyPicklist::getDependentPicklistFields($fld_module);
	$smarty->assign('DEPENDENT_PICKLISTS', $dependentPicklists);
	$smarty->display('modules/PickList/PickListDependencySetup.tpl');
}
?>