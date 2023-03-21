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
	return getSharingModuleList(array('Leads','Accounts','Contacts','Potentials','HelpDesk','Campaigns','Quotes','PurchaseOrder','SalesOrder','Invoice'));
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
	global $__cache_module_activeinfo, $adb;
	if (is_numeric($module) || empty($module)) {
		return false;
	}
	if (in_array($module, vtlib_moduleAlwaysActive())) {
		return true;
	}

	if (!isset($__cache_module_activeinfo[$module])) {
		$tabid = getTabId($module);
		if (!is_null($tabid)) {
			$result = $adb->pquery('select presence from vtiger_tab where tabid=?', array($tabid));
			$presence = $adb->query_result($result, 0, 'presence');
			$__cache_module_activeinfo[$module] = $presence;
		} else {
			$presence = 1;
		}
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
	require_once 'modules/Users/UserPrivilegesWriter.php';
	UserPrivilegesWriter::flushAllPrivileges();
}

/**
 * Get list module names which are always active (cannot be disabled)
 */
function vtlib_moduleAlwaysActive() {
	return array (
		'CustomView', 'Settings', 'Users', 'Migration', 'Utilities', 'Import', 'com_vtiger_workflow', 'PickList',
	);
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
		$enable_disable_BA = 1;
		$event_type = Vtiger_Module::EVENT_MODULE_ENABLED;
	} elseif ($enable_disable === false) {
		$enable_disable = 1;
		$enable_disable_BA = 0;
		$event_type = Vtiger_Module::EVENT_MODULE_DISABLED;
	}

	$adb->pquery('UPDATE vtiger_tab set presence=? WHERE name=?', array($enable_disable,$module));
	$adb->pquery(
		'UPDATE vtiger_businessactions set active=? WHERE linkurl RLIKE "[^a-zA-Z0-9_.]'.$module.'[^a-zA-Z0-9_.]" OR linkurl RLIKE "[^a-zA-Z0-9_.]'.$module.'$" OR linkurl RLIKE "^'.$module.'[^a-zA-Z0-9_.]"',
		array($enable_disable_BA)
	);

	$__cache_module_activeinfo[$module] = $enable_disable;

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
		WHERE name NOT IN ('Users') AND presence IN (0,1) ORDER BY name"
	);
	$num_rows = $adb->num_rows($sqlresult);
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
				$helpinfo = trim(decode_html($fieldrow['helpinfo']));
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

	$columnname = $adb->sql_escape_string($field_columnname);
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

	$columnname = $adb->sql_escape_string($field_columnname);
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
	return ($rsent && $adb->num_rows($rsent)>0 && $rsent->fields['isentitytype']=='1');
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
 * @param mixed value(s) to clean
 * @param boolean ignore skip cleaning of the input
 * @return mixed sanitized
 */
function vtlib_purify($input, $ignore = false) {
	global $__htmlpurifier_instance, $root_directory, $default_charset;

	static $purified_cache = array();

	if (!is_array($input)) {
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
		if (!$__htmlpurifier_instance) {
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
				if (strpos($value, '<a') !== false && strpos($value, 'javascript') !== false) {
					$dom = new DOMDocument;
					$dom->loadHTML($value, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
					foreach ($dom->getElementsByTagName('a') as $node) {
						if ($node->hasAttribute('href') && !filter_var($node->getAttribute('href'), FILTER_VALIDATE_URL) !== false) {
							$node->removeAttribute('href');
							$value = trim($dom->saveHTML(), "\n");
						}
					}
				}
			}
		}
	}
	if (is_array($value)) {
		$value = changeHTMLAmpersandsInArray($value);
	} elseif (is_string($value)) {
		$value = str_replace('&amp;', '&', $value);
	}
	if (!is_array($input)) {
		$purified_cache[$md5OfInput] = $value;
	}
	return $value;
}

function changeHTMLAmpersandsInArray($v) {
	if (is_array($v)) {
		return array_map('changeHTMLAmpersandsInArray', $v);
	}
	return str_replace('&amp;', '&', $v);
}

/**
 * Process the UI Widget requested
 * @param Vtiger_Link $widgetLinkInfo
 * @param Smarty Context
 * @return
 */
function vtlib_process_widget($widgetLinkInfo, $context = false) {
	$linkurl = trim($widgetLinkInfo->linkurl);
	if (preg_match("/^block:\/\/(.*)/", $linkurl, $matches) || preg_match("/^top:\/\/(.*)/", $linkurl, $matches)) {
		$widgetInfo = explode(':', $matches[1]);
		$widgetControllerClass = $widgetInfo[0];
		$widgetControllerClassFile = $widgetInfo[1];
		if (!class_exists($widgetControllerClass)) {
			checkFileAccessForInclusion($widgetControllerClassFile);
			include_once $widgetControllerClassFile;
		}
		if (class_exists($widgetControllerClass)) {
			$widgetControllerInstance = new $widgetControllerClass;
			$widgetInstance = $widgetControllerInstance->getWidget($widgetLinkInfo->linklabel);
			if ($widgetInstance) {
				if (isset($widgetInfo[2])) {
					parse_str($widgetInfo[2], $widgetContext);
					if (!$context) {
						$context = [];
					}
					$context = array_merge($context, $widgetContext);
				}
				$context['BusinessActionInformation'] = json_encode($widgetLinkInfo);
				return $widgetInstance->process($context);
			}
		}
	}
	return '';
}

function vtlib_module_icon($modulename) {
	if (file_exists("modules/$modulename/$modulename.png")) {
		return "modules/$modulename/$modulename.png";
	}
	return 'modules/Vtiger/Vtiger.png';
}

/**
 * Function to return the valid SQL input
 * @param string SQL
 * @param boolean Skip the check if string is empty
 * @return string sanitized SQL or false
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
