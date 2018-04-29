<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/*
 * Check for image existence in themes or use the common one.
 */
// Let us create cache to improve performance
if (!isset($__cache_vtiger_imagepath)) {
	$__cache_vtiger_imagepath = array();
}
function vtiger_imageurl($imagename, $themename) {
	global $__cache_vtiger_imagepath;
	if (isset($__cache_vtiger_imagepath[$imagename]) && $__cache_vtiger_imagepath[$imagename]) {
		$imagepath = $__cache_vtiger_imagepath[$imagename];
	} else {
		$imagepath = false;
		// Check in theme specific folder
		if (file_exists("themes/$themename/images/$imagename")) {
			$imagepath = "themes/$themename/images/$imagename";
		} elseif (file_exists("themes/images/$imagename")) {
			// Search in common image folder
			$imagepath = "themes/images/$imagename";
		} else {
			// Not found anywhere? Return whatever is sent
			$imagepath = $imagename;
		}
		$__cache_vtiger_imagepath[$imagename] = $imagepath;
	}
	return $imagepath;
}

/**
 * Get module name by id.
 */
function vtlib_getModuleNameById($tabid) {
	global $adb;
	$sqlresult = $adb->pquery('SELECT name FROM vtiger_tab WHERE tabid = ?', array($tabid));
	if ($adb->num_rows($sqlresult)) {
		return $adb->query_result($sqlresult, 0, 'name');
	}
	return null;
}

/**
 * Get module names for which sharing access can be controlled.
 * NOTE: Ignore the standard modules which is already handled.
 */
function vtlib_getModuleNameForSharing() {
	$std_modules = array('Calendar','Leads','Accounts','Contacts','Potentials',
		'HelpDesk','Campaigns','Quotes','PurchaseOrder','SalesOrder','Invoice','Events');
	return getSharingModuleList($std_modules);
}

/**
 * Cache the module active information for performance
 */
$__cache_module_activeinfo = array();

/**
 * Fetch module active information at one shot, but return all the information fetched.
 */
function vtlib_prefetchModuleActiveInfo($force = true) {
	global $__cache_module_activeinfo;

	// Look up if cache has information
	$tabrows = VTCacheUtils::lookupAllTabsInfo();

	// Initialize from DB if cache information is not available or force flag is set
	if ($tabrows === false || $force) {
		global $adb;
		$tabres = $adb->query('SELECT * FROM vtiger_tab');
		$tabrows = array();
		if ($tabres) {
			while ($tabresrow = $adb->fetch_array($tabres)) {
				$tabrows[] = $tabresrow;
				$__cache_module_activeinfo[$tabresrow['name']] = $tabresrow['presence'];
			}
			// Update cache for further re-use
			VTCacheUtils::updateAllTabsInfo($tabrows);
		}
	}

	return $tabrows;
}

/**
 * Check if module is set active (or enabled)
 */
function vtlib_isModuleActive($module) {
	global $__cache_module_activeinfo;

	if (in_array($module, vtlib_moduleAlwaysActive())) {
		return true;
	}

	if (!isset($__cache_module_activeinfo[$module])) {
		include 'tabdata.php';
		$presence = isset($tab_info_array[$module])? 0: 1;
		$__cache_module_activeinfo[$module] = $presence;
	} else {
		$presence = $__cache_module_activeinfo[$module];
	}

	$active = false;
	if ($presence != 1) {
		$active = true;
	}

	return $active;
}

/**
 * Recreate user privileges files.
 */
function vtlib_RecreateUserPrivilegeFiles() {
	global $adb;
	$userres = $adb->query('SELECT id FROM vtiger_users WHERE deleted = 0');
	if ($userres && $adb->num_rows($userres)) {
		while ($userrow = $adb->fetch_array($userres)) {
			createUserPrivilegesfile($userrow['id']);
		}
	}
}

/**
 * Get list module names which are always active (cannot be disabled)
 */
function vtlib_moduleAlwaysActive() {
	$modules = array (
		'CustomView', 'Settings', 'Users', 'Migration', 'Utilities', 'Import', 'com_vtiger_workflow', 'PickList',
	);
	return $modules;
}

/**
 * Toggle the module (enable/disable)
 */
function vtlib_toggleModuleAccess($module, $enable_disable, $noevents = false) {
	global $adb, $__cache_module_activeinfo;

	include_once 'vtlib/Vtiger/Module.php';

	$event_type = false;

	if ($enable_disable === true) {
		$enable_disable = 0;
		$event_type = Vtiger_Module::EVENT_MODULE_ENABLED;
	} elseif ($enable_disable === false) {
		$enable_disable = 1;
		$event_type = Vtiger_Module::EVENT_MODULE_DISABLED;
	}

	$adb->pquery('UPDATE vtiger_tab set presence = ? WHERE name = ?', array($enable_disable,$module));

	$__cache_module_activeinfo[$module] = $enable_disable;

	create_tab_data_file();
	create_parenttab_data_file();
	vtlib_RecreateUserPrivilegeFiles();

	if (!$noevents) {
		Vtiger_Module::fireEvent($module, $event_type);
	}
}

/**
 * Get list of module with current status which can be controlled.
 */
function vtlib_getToggleModuleInfo() {
	global $adb;

	$modinfo = array();
	$sqlresult = $adb->query(
		"SELECT name, presence, customized, isentitytype
		FROM vtiger_tab
		WHERE name NOT IN ('Users','Calendar') AND presence IN (0,1) ORDER BY name"
	);
	$num_rows  = $adb->num_rows($sqlresult);
	for ($idx = 0; $idx < $num_rows; ++$idx) {
		$module = $adb->query_result($sqlresult, $idx, 'name');
		$presence=$adb->query_result($sqlresult, $idx, 'presence');
		$customized=$adb->query_result($sqlresult, $idx, 'customized');
		$isentitytype=$adb->query_result($sqlresult, $idx, 'isentitytype');
		$hassettings=file_exists("modules/$module/Settings.php");
		$modinfo[$module] = array('customized'=>$customized, 'presence'=>$presence, 'hassettings'=>$hassettings, 'isentitytype' => $isentitytype );
	}
	uksort($modinfo, function ($a, $b) {
		return (strtolower(getTranslatedString($a, $a)) < strtolower(getTranslatedString($b, $b))) ? -1 : 1;
	});
	return $modinfo;
}

/**
 * Get list of language and its current status.
 */
function vtlib_getToggleLanguageInfo() {
	global $adb;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	$langinfo = array();
	$sqlresult = $adb->query('SELECT * FROM vtiger_language');
	if ($sqlresult) {
		for ($idx = 0; $idx < $adb->num_rows($sqlresult); ++$idx) {
			$row = $adb->fetch_array($sqlresult);
			$langinfo[$row['prefix']] = array('label'=>$row['label'], 'active'=>$row['active'] ,'id'=>$row['id']);
		}
	}
	$adb->dieOnError = $old_dieOnError;
	return $langinfo;
}

/**
 * Toggle the language (enable/disable)
 */
function vtlib_toggleLanguageAccess($langprefix, $enable_disable) {
	global $adb;

	// The table might not exists!
	$old_dieOnError = $adb->dieOnError;
	$adb->dieOnError = false;

	if ($enable_disable === true) {
		$enable_disable = 1;
	} elseif ($enable_disable === false) {
		$enable_disable = 0;
	}

	$adb->pquery('UPDATE vtiger_language set active = ? WHERE prefix = ?', array($enable_disable, $langprefix));

	$adb->dieOnError = $old_dieOnError;
}

/**
 * Get help information set for the module fields.
 */
function vtlib_getFieldHelpInfo($module) {
	global $adb;
	$fieldhelpinfo = array();
	if (in_array('helpinfo', $adb->getColumnNames('vtiger_field'))) {
		$result = $adb->pquery('SELECT fieldname,helpinfo FROM vtiger_field WHERE tabid=?', array(getTabid($module)));
		if ($result && $adb->num_rows($result)) {
			while ($fieldrow = $adb->fetch_array($result)) {
				$helpinfo = decode_html($fieldrow['helpinfo']);
				if (!empty($helpinfo)) {
					$fieldhelpinfo[$fieldrow['fieldname']] = getTranslatedString($helpinfo, $module);
				}
			}
		}
	}
	return $fieldhelpinfo;
}

/**
 * @deprecated: the variables have been moved to each module
 */
function vtlib_setup_modulevars($module, $focus) {
	// left here for backward compatibility
}
/**
 * @deprecated: the variables have been moved to each module
 */
function __vtlib_get_modulevar_value($module, $varname) {
	// left here for backward compatibility
	return null;
}

/**
 * @deprecated: use 'SINGLE_' or cbtranslation
 */
function vtlib_tosingular($text) {
	$lastpos = strripos($text, 's');
	if ($lastpos == strlen($text)-1) {
		return substr($text, 0, -1);
	}
	return $text;
}

/**
 * Get picklist values that is accessible by all roles.
 */
function vtlib_getPicklistValues_AccessibleToAll($field_columnname) {
	global $adb;

	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = "vtiger_$columnname";

	// Gather all the roles (except H1 which is organization role)
	$roleres = $adb->query("SELECT roleid FROM vtiger_role WHERE roleid != 'H1'");
	$roleresCount= $adb->num_rows($roleres);
	$allroles = array();
	if ($roleresCount) {
		for ($index = 0; $index < $roleresCount; ++$index) {
			$allroles[] = $adb->query_result($roleres, $index, 'roleid');
		}
	}
	sort($allroles);

	// Get all the picklist values associated to roles (except H1 - organization role).
	$picklistres = $adb->query(
		"SELECT $columnname as pickvalue, roleid FROM $tablename
		INNER JOIN vtiger_role2picklist ON $tablename.picklist_valueid=vtiger_role2picklist.picklistvalueid
		WHERE roleid != 'H1'"
	);

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistval_roles = array();
	if ($picklistresCount) {
		for ($index = 0; $index < $picklistresCount; ++$index) {
			$picklistval = $adb->query_result($picklistres, $index, 'pickvalue');
			$pickvalroleid=$adb->query_result($picklistres, $index, 'roleid');
			$picklistval_roles[$picklistval][] = $pickvalroleid;
		}
	}
	// Collect picklist value which is associated to all the roles.
	$allrolevalues = array();
	foreach ($picklistval_roles as $picklistval => $pickvalroles) {
		sort($pickvalroles);
		$diff = array_diff($pickvalroles, $allroles);
		if (empty($diff)) {
			$allrolevalues[] = $picklistval;
		}
	}

	return $allrolevalues;
}

/**
 * Get all picklist values for a non-standard picklist type.
 */
function vtlib_getPicklistValues($field_columnname) {
	global $adb;

	$columnname =  $adb->sql_escape_string($field_columnname);
	$tablename = "vtiger_$columnname";

	$picklistres = $adb->query("SELECT $columnname as pickvalue FROM $tablename");

	$picklistresCount = $adb->num_rows($picklistres);

	$picklistvalues = array();
	if ($picklistresCount) {
		for ($index = 0; $index < $picklistresCount; ++$index) {
			$picklistvalues[] = $adb->query_result($picklistres, $index, 'pickvalue');
		}
	}
	return $picklistvalues;
}

/**
 * Check for custom module by its name.
 */
function vtlib_isCustomModule($moduleName) {
	$moduleFile = "modules/$moduleName/$moduleName.php";
	if (file_exists($moduleFile)) {
		if (function_exists('checkFileAccessForInclusion')) {
			checkFileAccessForInclusion($moduleFile);
		}
		include_once $moduleFile;
		$focus = new $moduleName();
		return (isset($focus->IsCustomModule) && $focus->IsCustomModule);
	}
	return false;
}

/**
 * Check for entity module by its name.
 */
function vtlib_isEntityModule($moduleName) {
	global $adb;
	$rsent = $adb->pquery('select isentitytype from vtiger_tab where name=?', array($moduleName));
	if ($rsent && $adb->num_rows($rsent)>0) {
		if ($adb->query_result($rsent, 0, 0)=='1') {
			return true;
		}
	}
	return false;
}

/**
 * Get module specific smarty template path.
 */
function vtlib_getModuleTemplate($module, $templateName) {
	return ("modules/$module/$templateName");
}

/**
 * Check if give path is writeable.
 */
function vtlib_isWriteable($path) {
	if (is_dir($path)) {
		return vtlib_isDirWriteable($path);
	} else {
		return is_writable($path);
	}
}

/**
 * Check if given directory is writeable.
 * NOTE: The check is made by trying to create a random file in the directory.
 */
function vtlib_isDirWriteable($dirpath) {
	if (is_dir($dirpath)) {
		do {
			$tmpfile = 'vtiger' . time() . '-' . rand(1, 1000) . '.tmp';
			// Continue the loop unless we find a name that does not exists already.
			$usefilename = "$dirpath/$tmpfile";
			if (!file_exists($usefilename)) {
				break;
			}
		} while (true);
		$fh = @fopen($usefilename, 'a');
		if ($fh) {
			fclose($fh);
			unlink($usefilename);
			return true;
		}
	}
	return false;
}

/** HTML Purifier global instance */
$__htmlpurifier_instance = false;
/**
 * Purify (Cleanup) malicious snippets of code from the input
 *
 * @param String $value
 * @param Boolean $ignore Skip cleaning of the input
 * @return String
 */
function vtlib_purify($input, $ignore = false) {
	global $__htmlpurifier_instance, $root_directory, $default_charset;

	static $purified_cache = array();

	if (!is_array($input)) {  // thank you Boris and Adam (from developers list)
		$md5OfInput = md5($input.($ignore?'T':'F'));
		if (array_key_exists($md5OfInput, $purified_cache)) {
			return $purified_cache[$md5OfInput];
		}
	}

	$use_charset = $default_charset;
	$use_root_directory = $root_directory;

	$value = $input;
	if (!$ignore) {
		// Initialize the instance if it has not yet done
		if ($__htmlpurifier_instance == false) {
			if (empty($use_charset)) {
				$use_charset = 'UTF-8';
			}
			if (empty($use_root_directory)) {
				$use_root_directory = __DIR__ . '/../..';
			}

			include_once 'include/htmlpurifier/library/HTMLPurifier.auto.php';

			$config = HTMLPurifier_Config::createDefault();
			$config->set('Core.Encoding', $use_charset);
			$config->set('Cache.SerializerPath', "$use_root_directory/cache");
			$config->set('Attr.AllowedFrameTargets', array('_blank', '_self', '_parent', '_top','_new','_newtc'));

			$__htmlpurifier_instance = new HTMLPurifier($config);
		}
		if ($__htmlpurifier_instance) {
			// Composite type
			if (is_array($input)) {
				$value = array();
				foreach ($input as $k => $v) {
					$value[$k] = vtlib_purify($v, $ignore);
				}
			} else { // Simple type
				$value = $__htmlpurifier_instance->purify($input);
			}
		}
	}
	$value = str_replace('&amp;', '&', $value);
	if (!is_array($input)) {
		$purified_cache[$md5OfInput] = $value;
	}
	return $value;
}

/**
 * Process the UI Widget requested
 * @param Vtiger_Link $widgetLinkInfo
 * @param Current Smarty Context $context
 * @return
 */
function vtlib_process_widget($widgetLinkInfo, $context = false) {
	if (preg_match("/^block:\/\/(.*)/", $widgetLinkInfo->linkurl, $matches)) {
		list($widgetControllerClass, $widgetControllerClassFile) = explode(':', $matches[1]);
		if (!class_exists($widgetControllerClass)) {
			checkFileAccessForInclusion($widgetControllerClassFile);
			include_once $widgetControllerClassFile;
		}
		if (class_exists($widgetControllerClass)) {
			$widgetControllerInstance = new $widgetControllerClass;
			$widgetInstance = $widgetControllerInstance->getWidget($widgetLinkInfo->linklabel);
			if ($widgetInstance) {
				return $widgetInstance->process($context);
			}
		}
	}
	return '';
}

function vtlib_module_icon($modulename) {
	if ($modulename == 'Events') {
		return 'modules/Calendar/Events.png';
	}
	if (file_exists("modules/$modulename/$modulename.png")) {
		return "modules/$modulename/$modulename.png";
	}
	return 'modules/Vtiger/Vtiger.png';
}

/**
 * Function to return the valid SQl input.
 * @param <String> $string
 * @param <Boolean> $skipEmpty Skip the check if string is empty.
 * @return <String> $string/false
 */
function vtlib_purifyForSql($string, $skipEmpty = true) {
	$pattern = '/^[_a-zA-Z0-9.]+$/';
	if ((empty($string) && $skipEmpty) || preg_match($pattern, $string)) {
		return $string;
	}
	return false;
}

function getvtlib_open_popup_window_function($popupmodule, $fldname, $basemodule) {
	if (file_exists('modules/'.$popupmodule.'/'.$popupmodule.'.php')) {
		include_once 'modules/'.$popupmodule.'/'.$popupmodule.'.php';
		$mod = new $popupmodule();
		if (method_exists($mod, 'getvtlib_open_popup_window_function')) {
			return $mod->getvtlib_open_popup_window_function($fldname, $basemodule);
		} elseif (file_exists('modules/'.$popupmodule.'/getvtlib_open_popup_window_function.php')) {
			@include_once 'modules/'.$popupmodule.'/getvtlib_open_popup_window_function.php';
			if (function_exists('__hook_getvtlib_open_popup_window_function')) {
				$mod->registerMethod('__hook_getvtlib_open_popup_window_function');
				return $mod->__hook_getvtlib_open_popup_window_function($fldname, $basemodule);
			}
		}
	}
	return 'vtlib_open_popup_window';
}
?>
