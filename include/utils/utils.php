<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/Settings/configod.php';
require_once 'include/utils/Session.php';
require_once 'include/utils/Request.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/cbSettings.php';
require_once 'include/cbmqtm/cbmqtm_loader.php';
require_once 'include/events/include.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowManager.inc';
require_once 'modules/GlobalVariable/GlobalVariable.php';
require_once 'modules/cbMap/cbMap.php';
require_once 'include/ComboUtil.php';
require_once 'include/utils/ListViewUtils.php';
require_once 'include/utils/EditViewUtils.php';
require_once 'include/utils/DetailViewUtils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'include/utils/InventoryUtils.php';
require_once 'include/utils/SearchUtils.php';
require_once 'include/DatabaseUtil.php';
require_once 'include/events/SqlResultIterator.inc';
require_once 'include/events/cbEventHandler.php';
require_once 'include/fields/DateTimeField.php';
require_once 'include/fields/CurrencyField.php';
require_once 'data/CRMEntity.php';
require_once 'vtlib/Vtiger/Language.php';

// Constants to be defined here

// For Customview status.
define('CV_STATUS_DEFAULT', 0);
define('CV_STATUS_PRIVATE', 1);
define('CV_STATUS_PENDING', 2);
define('CV_STATUS_PUBLIC', 3);

// For Restoration.
define('RB_RECORD_DELETED', 'delete');
define('RB_RECORD_INSERTED', 'insert');
define('RB_RECORD_UPDATED', 'update');

/** Function to load global browser variables for javascript
 * @param smarty object to load the variables, if empty it will only return the variables in an array
 * @returns array with the variables
 */
function getBrowserVariables(&$smarty) {
	global $currentModule,$current_user,$default_charset,$theme,$adb,$current_language;
	$vars = array();
	$vars['gVTModule'] = $currentModule;
	$vars['gVTTheme']  = $theme;
	$vars['gVTUserID'] = $current_user->id;
	$vars['default_charset'] = $default_charset;
	$vars['userDateFormat'] = $current_user->date_format;
	$vars['userHourFormat'] = ($current_user->hour_format=='24' ? '24' : 'am/pm');
	$sql = 'SELECT dayoftheweek FROM its4you_calendar4you_settings WHERE userid=?';
	$result = $adb->pquery($sql, array($current_user->id));
	if ($result && $adb->num_rows($result)>0) {
		$fDOW = $adb->query_result($result, 0, 0);
		$vars['userFirstDOW'] = ($fDOW=='Monday' ? 1 : 0);
	} else {
		$vars['userFirstDOW'] = 0;
	}
	if (isset($current_user->currency_grouping_separator) && $current_user->currency_grouping_separator == '') {
		$vars['userCurrencySeparator'] = ' ';
	} else {
		$vars['userCurrencySeparator'] = html_entity_decode($current_user->currency_grouping_separator, ENT_QUOTES, $default_charset);
	}
	if (isset($current_user->currency_decimal_separator) && $current_user->currency_decimal_separator == '') {
		$vars['userDecimalSeparator'] = ' ';
	} else {
		$vars['userDecimalSeparator'] = html_entity_decode($current_user->currency_decimal_separator, ENT_QUOTES, $default_charset);
	}
	if (isset($current_user->no_of_currency_decimals) && $current_user->no_of_currency_decimals == '') {
		$vars['userNumberOfDecimals'] = '2';
	} else {
		$vars['userNumberOfDecimals'] = html_entity_decode($current_user->no_of_currency_decimals, ENT_QUOTES, $default_charset);
	}
	$swmd5file = file_get_contents('include/sw-precache/service-worker.md5');
	$swmd5 = substr($swmd5file, 0, strpos($swmd5file, ' '));
	$corebos_browsertabID = (empty($_COOKIE['corebos_browsertabID']) ? '' : $_COOKIE['corebos_browsertabID']);
	if ($smarty) {
		$smarty->assign('GVTMODULE', $vars['gVTModule']);
		$smarty->assign('THEME', $vars['gVTTheme']);
		$smarty->assign('DEFAULT_CHARSET', $vars['default_charset']);
		$smarty->assign('CURRENT_USER_ID', $vars['gVTUserID']);
		$smarty->assign('USER_DATE_FORMAT', $vars['userDateFormat']);
		$smarty->assign('USER_HOUR_FORMAT', $vars['userHourFormat']);
		$smarty->assign('USER_FIRST_DOW', $vars['userFirstDOW']);
		$smarty->assign('USER_CURRENCY_SEPARATOR', $vars['userCurrencySeparator']);
		$smarty->assign('USER_DECIMAL_FORMAT', $vars['userDecimalSeparator']);
		$smarty->assign('USER_NUMBER_DECIMALS', $vars['userNumberOfDecimals']);
		$smarty->assign('USER_LANGUAGE', $current_language);
		$smarty->assign('SW_MD5', $swmd5);
		$smarty->assign('corebos_browsertabID', $corebos_browsertabID);
	}
}

/** Function to return a full name
  * @param $row -- row:: Type integer
  * @param $first_column -- first column:: Type string
  * @param $last_column -- last column:: Type string
  * @returns $fullname -- fullname:: Type string
*/
function return_name(&$row, $first_column, $last_column) {
	global $log;
	$log->debug("Entering return_name(".$row.",".$first_column.",".$last_column.") method ...");
	$first_name = "";
	$last_name = "";
	$full_name = "";

	if (isset($row[$first_column])) {
		$first_name = stripslashes($row[$first_column]);
	}

	if (isset($row[$last_column])) {
		$last_name = stripslashes($row[$last_column]);
	}

	$full_name = $first_name;

	// If we have a first name and we have a last name
	if ($full_name != "" && $last_name != "") {
		// append a space, then the last name
		$full_name .= " ".$last_name;
	} // If we have no first name, but we have a last name
	elseif ($last_name != "") {
		// append the last name without the space.
		$full_name .= $last_name;
	}

	$log->debug('Exiting return_name method ...');
	return $full_name;
}

/** Function to return language
  * @returns $languages -- languages:: Type string
*/
function get_languages() {
	global $log, $languages;
	$log->debug('Entering/Exiting get_languages() method ...');
	return $languages;
}

/** Function to return language
  * @param $key -- key:: Type string
  * @returns $languages -- languages:: Type string
*/
function get_language_display($key) {
	global $log, $languages;
	$log->debug('Entering/Exiting get_language_display('.$key.') method ...');
	return $languages[$key];
}

/** Function returns the user array
 * @param $assigned_user_id -- assigned_user_id:: Type string
 * @returns $user_list -- user list:: Type array
*/
function get_assigned_user_name($assigned_user_id) {
	global $log;
	$log->debug("Entering get_assigned_user_name(".$assigned_user_id.") method ...");
	$user_list = get_user_array(false, "");
	if (isset($user_list[$assigned_user_id])) {
		$log->debug("Exiting get_assigned_user_name method ...");
		return $user_list[$assigned_user_id];
	}
	$log->debug("Exiting get_assigned_user_name method ...");
	return "";
}

/** Function returns the user key in user array
  * @param $add_blank -- boolean:: Type boolean
  * @param $status -- user status:: Type string
  * @param $assigned_user -- user id:: Type string
  * @param $private -- sharing type:: Type string
  * @returns $user_array -- user array:: Type array
*/
function get_user_array($add_blank = true, $status = "Active", $assigned_user = "", $private = "") {
	global $log, $current_user;
	$log->debug("Entering get_user_array(".$add_blank.",". $status.",".$assigned_user.",".$private.") method ...");
	if (isset($current_user) && $current_user->id != '') {
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	}
	static $user_array = null;
	$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';

	if ($user_array == null) {
		require_once 'include/database/PearDatabase.php';
		$db = PearDatabase::getInstance();
		$temp_result = array();
		// Including deleted users for now.
		if (empty($status)) {
			$query = "SELECT id, user_name from vtiger_users";
			$params = array();
		} else {
			$assignUP = GlobalVariable::getVariable('Application_Permit_Assign_Up', 0, $module, $current_user->id);
			if ($private == 'private' && empty($assignUP)) {
				$assignBrothers = GlobalVariable::getVariable('Application_Permit_Assign_SameRole', 0, $module, $current_user->id);
				$query = "select id as id,user_name as user_name,first_name,last_name
					from vtiger_users
					where id=? and status='Active'
					union
					select vtiger_user2role.userid as id,vtiger_users.user_name as user_name, vtiger_users.first_name as first_name, vtiger_users.last_name as last_name
					from vtiger_user2role
					inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
					inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
					where vtiger_role.parentrole like ? and status='Active'
					union select shareduserid as id,vtiger_users.user_name as user_name, vtiger_users.first_name as first_name, vtiger_users.last_name as last_name
					from vtiger_tmp_write_user_sharing_per
					inner join vtiger_users on vtiger_users.id=vtiger_tmp_write_user_sharing_per.shareduserid
					where status='Active' and vtiger_tmp_write_user_sharing_per.userid=? and vtiger_tmp_write_user_sharing_per.tabid=?";
				$params = array(
					$current_user->id,
					(isset($current_user_parent_role_seq) ? $current_user_parent_role_seq : '').(empty($assignBrothers) ? '::%' : '%'),
					$current_user->id,
					getTabid($module)
				);
			} else {
				$query = "SELECT id, user_name,first_name,last_name from vtiger_users WHERE status=?";
				$params = array($status);
			}
		}
		if (!empty($assigned_user)) {
			$query .= " OR id=?";
			$params[] = $assigned_user;
		}

		$query .= " order by user_name ASC";

		$result = $db->pquery($query, $params, true, "Error filling in user array: ");

		if ($add_blank==true) {
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while ($row = $db->fetchByAssoc($result)) {
			$temp_result[$row['id']] = getFullNameFromArray('Users', $row);
		}

		$user_array = $temp_result;
	}

	$log->debug("Exiting get_user_array method ...");
	return $user_array;
}

function get_group_array($add_blank = true, $status = "Active", $assigned_user = "", $private = "") {
	global $log, $current_user, $currentModule;
	$log->debug("Entering get_group_array(".$add_blank.",". $status.",".$assigned_user.",".$private.") method ...");
	$current_user_groups = array();
	$current_user_parent_role_seq = '';
	if (isset($current_user) && $current_user->id != '') {
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	}
	static $group_array = null;
	$module= (isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) : $currentModule);

	if ($group_array == null) {
		require_once 'include/database/PearDatabase.php';
		$db = PearDatabase::getInstance();
		$temp_result = array();
		// Including deleted vtiger_users for now.
		$log->debug('Sharing is Public. All users should be listed');
		$query = 'SELECT groupid, groupname from vtiger_groups';
		$params = array();
		$assignAllGroups = GlobalVariable::getVariable('Application_Permit_Assign_AllGroups', 0, $module, $current_user->id);
		if ($private == 'private' && $assignAllGroups==0) {
			$query .= ' WHERE groupid=?';
			$params = array( $current_user->id);

			if (count($current_user_groups) != 0) {
				$query .= ' OR vtiger_groups.groupid in ('.generateQuestionMarks($current_user_groups).')';
				$params[] = $current_user_groups;
			}
			$log->debug('Sharing is Private. Only the current user should be listed');
			$query .= ' union select vtiger_group2role.groupid as groupid,vtiger_groups.groupname as groupname
				from vtiger_group2role
				inner join vtiger_groups on vtiger_groups.groupid=vtiger_group2role.groupid
				inner join vtiger_role on vtiger_role.roleid=vtiger_group2role.roleid
				where vtiger_role.parentrole like ?';
			$params[] = $current_user_parent_role_seq.'::%';

			if (count($current_user_groups) != 0) {
				$query .= ' union select vtiger_groups.groupid as groupid,vtiger_groups.groupname as groupname
					from vtiger_groups
					inner join vtiger_group2rs on vtiger_groups.groupid=vtiger_group2rs.groupid
					where vtiger_group2rs.roleandsubid in ('.generateQuestionMarks($parent_roles).')';
				$params[] = $parent_roles;
			}

			$query .= ' union select sharedgroupid as groupid,vtiger_groups.groupname as groupname
				from vtiger_tmp_write_group_sharing_per
				inner join vtiger_groups on vtiger_groups.groupid=vtiger_tmp_write_group_sharing_per.sharedgroupid
				where vtiger_tmp_write_group_sharing_per.userid=?';
			$params[] = $current_user->id;

			$query .= ' and vtiger_tmp_write_group_sharing_per.tabid=?';
			$params[] = getTabid($module);
		}
		$query .= ' order by groupname ASC';

		$result = $db->pquery($query, $params, true, 'Error filling in user array: ');

		if ($add_blank==true) {
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while ($row = $db->fetchByAssoc($result)) {
			$temp_result[$row['groupid']] = $row['groupname'];
		}

		$group_array = $temp_result;
	}

	$log->debug('Exiting get_group_array method ...');
	return $group_array;
}

/** Function skips executing arbitary commands given in a string
  * @param $string -- string:: Type string
  * @param $maxlength -- maximun length:: Type integer
  * @returns $string -- escaped string:: Type string
*/
function clean($string, $maxLength) {
	global $log;
	$log->debug("Entering clean(".$string.",". $maxLength.") method ...");
	$string = substr($string, 0, $maxLength);
	$log->debug("Exiting clean method ...");
	return escapeshellcmd($string);
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 */
function safe_map($request_var, & $focus, $always_copy = false) {
	global $log;
	$log->debug("Entering safe_map(".$request_var.",".get_class($focus).",".$always_copy.") method ...");
	safe_map_named($request_var, $focus, $request_var, $always_copy);
	$log->debug("Exiting safe_map method ...");
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 */
function safe_map_named($request_var, & $focus, $member_var, $always_copy) {
	global $log;
	$log->debug("Entering safe_map_named(".$request_var.",".get_class($focus).",".$member_var.",".$always_copy.") method ...");
	if (isset($_REQUEST[$request_var]) && ($always_copy || is_null($focus->$member_var))) {
		$log->debug("safe map named called assigning '{$_REQUEST[$request_var]}' to $member_var");
		$focus->$member_var = $_REQUEST[$request_var];
	}
	$log->debug("Exiting safe_map_named method ...");
}

/**
 * @deprecated: use getTranslatedString
 */
function return_app_list_strings_language($language) {
	// function left with an empty value for backward compatibility
	return null;
}

/**
 * Retrieve the app_currency_strings for the required language.
 */
function return_app_currency_strings_language($language) {
	global $log, $app_currency_strings, $default_language, $log;
	$log->debug("Entering return_app_currency_strings_language(".$language.") method ...");
	// Backup the value first
	$temp_app_currency_strings = $app_currency_strings;
	@include "include/language/$language.lang.php";
	if (!isset($app_currency_strings)) {
		$log->warn("Unable to find the application language file for language: ".$language);
		require "include/language/$default_language.lang.php";
	}
	if (!isset($app_currency_strings)) {
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug('Exiting return_app_currency_strings_language method ...');
		return null;
	}
	$return_value = $app_currency_strings;

	// Restore the value back
	$app_currency_strings = $temp_app_currency_strings;

	$log->debug('Exiting return_app_currency_strings_language method ...');
	return $return_value;
}

/** This function retrieves an application language file and returns the array of strings included.
 * If you are using the current language, do not call this function unless you are loading it for the first time */
function return_application_language($language) {
	global $app_strings, $default_language, $log;
	$log->debug("Entering return_application_language(".$language.") method ...");
	$temp_app_strings = $app_strings;
	$languagefound = $language;
	checkFileAccessForInclusion("include/language/$language.lang.php");
	@include "include/language/$language.lang.php";
	if (!isset($app_strings)) {
		$log->warn("Unable to find the application language file for language: ".$language);
		require "include/language/$default_language.lang.php";
		$languagefound = $default_language;
	}

	if (!isset($app_strings)) {
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug('Exiting return_application_language method ...');
		return null;
	}

	if (file_exists("include/language/$languagefound.custom.php")) {
		@include "include/language/$languagefound.custom.php";
		$app_strings = array_merge($app_strings, $custom_strings);
	}
	$return_value = $app_strings;
	$app_strings = $temp_app_strings;

	$log->debug('Exiting return_application_language method ...');
	return $return_value;
}

/** This function retrieves a module's language file and returns the array of strings included.
 * If you are in the current module, do not call this function unless you are loading it for the first time */
function return_module_language($language, $module) {
	global $mod_strings, $default_language, $log;
	$log->debug("Entering return_module_language(".$language.",". $module.") method ...");
	static $cachedModuleStrings = array();

	if (!empty($cachedModuleStrings[$module])) {
		$log->debug('Exiting return_module_language method ...');
		return $cachedModuleStrings[$module];
	}

	$temp_mod_strings = $mod_strings;
	$languagefound = $language;
	@include "modules/$module/language/$language.lang.php";
	if (!isset($mod_strings)) {
		$log->warn("Unable to find the module language file for language: ".$language." and module: ".$module);
		if ($default_language == 'en_us') {
			require "modules/$module/language/$default_language.lang.php";
			$languagefound = $default_language;
		} else {
			@include "modules/$module/language/$default_language.lang.php";
			$languagefound = $default_language;
			if (!isset($mod_strings)) {
				if (file_exists("modules/$module/language/en_us.lang.php")) {
					require "modules/$module/language/en_us.lang.php";
				} else {
					$mod_strings = array();
				}
				$languagefound = 'en_us';
			}
		}
	}

	if (!isset($mod_strings)) {
		$log->fatal("Unable to load the module($module) language file for the selected language($language) or the default language($default_language)");
		$log->debug('Exiting return_module_language method ...');
		return null;
	}

	if (file_exists("modules/$module/language/$languagefound.custom.php")) {
		@include "modules/$module/language/$languagefound.custom.php";
		$mod_strings = array_merge($mod_strings, $custom_strings);
	}
	$return_value = $mod_strings;
	$mod_strings = $temp_mod_strings;

	$log->debug('Exiting return_module_language method ...');
	$cachedModuleStrings[$module] = $return_value;
	return $return_value;
}

/*This function returns the mod_strings for the given language and module: it does not update the current mod_strings contents */
function return_specified_module_language($language, $module) {
	global $log, $default_language;
	$languagefound = $language;
	@include "modules/$module/language/$language.lang.php";
	if (!isset($mod_strings)) {
		$log->warn("Unable to find the module language file for language: ".$language." and module: ".$module);
		require "modules/$module/language/$default_language.lang.php";
		$languagefound = $default_language;
	}

	if (!isset($mod_strings)) {
		$log->fatal("Unable to load the module($module) language file for the selected language($language) or the default language($default_language)");
		$log->debug('Exiting return_module_language method ...');
		return null;
	}

	if (file_exists("modules/$module/language/$languagefound.custom.php")) {
		@include "modules/$module/language/$languagefound.custom.php";
		$mod_strings = array_merge($mod_strings, $custom_strings);
	}
	$return_value = $mod_strings;

	$log->debug('Exiting return_module_language method ...');
	return $return_value;
}

/** If the session variable is defined and is not equal to "" then return it. Otherwise, return the default value. */
function return_session_value_or_default($varname, $default) {
	global $log;
	$log->debug("Entering return_session_value_or_default(".$varname.",". $default.") method ...");
	if (isset($_SESSION[$varname]) && $_SESSION[$varname] != "") {
		$log->debug("Exiting return_session_value_or_default method ...");
		return $_SESSION[$varname];
	}

	$log->debug("Exiting return_session_value_or_default method ...");
	return $default;
}

/**
  * Creates an array of where restrictions. These are used to construct a where SQL statement on the query
  * It looks for the variable in the $_REQUEST array. If it is set and is not "" it will create a where clause out of it.
  * @param &$where_clauses - The array to append the clause to
  * @param $variable_name - The name of the variable to look for an add to the where clause if found
  * @param $SQL_name - [Optional] If specified, this is the SQL column name that is used. If not specified, the $variable_name is used as the SQL_name.
  */
function append_where_clause(&$where_clauses, $variable_name, $SQL_name = null) {
	global $log;
	$log->debug("Entering append_where_clause(".$where_clauses.",".$variable_name.",".$SQL_name.") method ...");
	if ($SQL_name == null) {
		$SQL_name = $variable_name;
	}

	if (isset($_REQUEST[$variable_name]) && $_REQUEST[$variable_name] != '') {
		$where_clauses[] = "$SQL_name like '$_REQUEST[$variable_name]%'";
	}
	$log->debug("Exiting append_where_clause method ...");
}

/**
  * Generate the appropriate SQL based on the where clauses.
  * @param $where_clauses - An Array of individual where clauses stored as strings
  * @returns string where_clause - The final SQL where clause to be executed.
  */
function generate_where_statement($where_clauses) {
	global $log;
	$log->debug("Entering generate_where_statement(".$where_clauses.") method ...");
	$where = '';
	foreach ($where_clauses as $clause) {
		if ($where != '') {
			$where .= ' and ';
		}
		$where .= $clause;
	}
	$log->info("Here is the where clause for the list view: $where");
	$log->debug('Exiting generate_where_statement method ...');
	return $where;
}

/**
 * A temporary method of generating GUIDs of the correct format for our DB.
 * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
*/
function create_guid() {
	global $log;
	$log->debug("Entering create_guid() method ...");
	$microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = sprintf("%x", $a_dec* 1000000);
	$sec_hex = sprintf("%x", $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= create_guid_section(3);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= $sec_hex;
	$guid .= create_guid_section(6);

	$log->debug("Exiting create_guid method ...");
	return $guid;
}

/** Function to create guid section for a given character
 * @param $characters -- characters:: Type string
 * @returns $return -- integer:: Type integer``
 */
function create_guid_section($characters) {
	global $log;
	$log->debug("Entering create_guid_section(".$characters.") method ...");
	$return = "";
	for ($i=0; $i<$characters; $i++) {
		$return .= sprintf("%x", rand(0, 15));
	}
	$log->debug("Exiting create_guid_section method ...");
	return $return;
}

/** Function to ensure length
 * @param $string -- string:: Type string
 * @param $length -- length:: Type string
 */
function ensure_length(&$string, $length) {
	global $log;
	$log->debug("Entering ensure_length(".$string.",". $length.") method ...");
	$strlen = strlen($string);
	if ($strlen < $length) {
		$string = str_pad($string, $length, "0");
	} elseif ($strlen > $length) {
		$string = substr($string, 0, $length);
	}
	$log->debug("Exiting ensure_length method ...");
}
/*
function microtime_diff($a, $b) {
	global $log;
	$log->debug("Entering microtime_diff(".$a.",". $b.") method ...");
	list($a_dec, $a_sec) = explode(" ", $a);
	list($b_dec, $b_sec) = explode(" ", $b);
	$log->debug("Exiting microtime_diff method ...");
	return $b_sec - $a_sec + $b_dec - $a_dec;
}
 */

/**
 * Return an array of directory names.
 */
function get_themes() {
	global $log;
	$log->debug('Entering get_themes() method ...');
	$filelist = array();
	if ($dir = @opendir('./themes')) {
		while (($file = readdir($dir))) {
			if ($file != '..' && $file != '.' && is_dir('./themes/'.$file) && $file[0] != '.' && is_file("./themes/$file/style.css")) {
				$filelist[$file] = $file;
			}
		}
		closedir($dir);
	}
	ksort($filelist);
	$log->debug('Exiting get_themes method ...');
	return $filelist;
}

//this is an optimisation of the to_html function, here we make the decision
//decide once if we are going to convert things to html
// Alan Bell Libertus Solutions shared on vtiger CRM developer's list
function decide_to_html() {
	global $doconvert;
	$request = $_REQUEST;
	$chkvalue = array('action','search','module','file','submode');
	foreach ($chkvalue as $value) {
		$request[$value] = empty($request[$value]) ? '' : $request[$value];
	}
	$action = vtlib_purify($request['action']);
	$search = vtlib_purify($request['search']);
	$ajax_action = '';
	if ($request['module'] != 'Settings' && $request['file'] != 'ListView' && $request['module'] != 'Portal' && $request['module'] != 'Reports') {
		$ajax_action = $request['module'].'Ajax';
	}
	if ($action != 'CustomView' && $action != 'Export' && $action != $ajax_action && $action != 'LeadConvertToEntities' && $action != 'CreatePDF'
		&& $action != 'ConvertAsFAQ' && $request['module'] != 'Dashboard' && $action != 'CreateSOPDF' && $action != 'SendPDFMail' && (!isset($_REQUEST['submode']))
	) {
		$doconvert = true;
	} elseif ($search == true) {
		// Fix for tickets #4647, #4648. Conversion required in case of search results also.
		$doconvert = true;
	}
}
decide_to_html();//call the function once when loading

/** Function to convert the given string to html
* @param $string -- string:: Type string
* @returns $string -- string:: Type string
*/
function to_html($string) {
	global $doconvert,$default_charset;
	if ($doconvert == true) {
		list($cachedresult,$found) = VTCacheUtils::lookupCachedInformation('to_html::'.$string);
		if ($found) {
			return $cachedresult;
		}
		$key = $string;
		if ($default_charset == 'UTF-8') {
			$string = htmlentities($string, ENT_QUOTES, $default_charset);
		} else {
			$string = preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
		}
		VTCacheUtils::updateCachedInformation('to_html::'.$key, $string);
	}
	return $string;
}

/** Function to get the tablabel for a given id
 * @param $tabid -- tab id:: Type integer
 * @returns $string -- string:: Type string
*/
function getTabname($tabid) {
	global $log, $adb;
	$log->debug("Entering getTabname(".$tabid.") method ...");
	$sql = "select tablabel from vtiger_tab where tabid=?";
	$result = $adb->pquery($sql, array($tabid));
	$tabname = $adb->query_result($result, 0, "tablabel");
	$log->debug("Exiting getTabname method ...");
	return $tabname;
}

/** Function to get the tab module name for a given id
 * @param $tabid -- tab id:: Type integer
 * @returns $string -- string:: Type string
*/
function getTabModuleName($tabid) {
	global $log, $adb;
	$log->debug("Entering getTabModuleName($tabid) ...");

	// Lookup information in cache first
	$tabname = VTCacheUtils::lookupModulename($tabid);
	if ($tabname === false) {
		if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
			include 'tabdata.php';
			$tabname = array_search($tabid, $tab_info_array);
		}
		if ($tabname === false) {
			$sql = "select name from vtiger_tab where tabid=?";
			$result = $adb->pquery($sql, array($tabid));
			$tabname = $adb->query_result($result, 0, "name");
		}
		// Update information to cache for re-use
		VTCacheUtils::updateTabidInfo($tabid, $tabname);
	}
	$log->debug("Exiting getTabModuleName ($tabname) ...");
	return $tabname;
}

/** Function to get column fields for a given module
 * @param $module -- module:: Type string
 * @returns $column_fld -- column field :: Type array
*/
function getColumnFields($module) {
	global $log, $adb;
	$log->debug("Entering getColumnFields(".$module.") method ...");

	// Lookup in cache for information
	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

	if ($cachedModuleFields === false) {
		$tabid = getTabid($module);
		if ($module == 'Calendar') {
			$tabid = array('9','16');
		}

		// Let us pick up all the fields first so that we can cache information
		$sql = "SELECT tabid, fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata, presence
			FROM vtiger_field WHERE tabid in (" . generateQuestionMarks($tabid) . ")";

		$result = $adb->pquery($sql, array($tabid));
		$noofrows = $adb->num_rows($result);

		if ($noofrows) {
			while ($resultrow = $adb->fetch_array($result)) {
				// Update information to cache for re-use
				VTCacheUtils::updateFieldInfo(
					$resultrow['tabid'],
					$resultrow['fieldname'],
					$resultrow['fieldid'],
					decode_html($resultrow['fieldlabel']),
					$resultrow['columnname'],
					$resultrow['tablename'],
					$resultrow['uitype'],
					$resultrow['typeofdata'],
					$resultrow['presence']
				);
			}
		}

		// For consistency get information from cache
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
	}

	if ($module == 'Calendar') {
		$cachedEventsFields = VTCacheUtils::lookupFieldInfo_Module('Events');
		if ($cachedModuleFields == false) {
			$cachedModuleFields = $cachedEventsFields;
		} else {
			$cachedModuleFields = array_merge($cachedModuleFields, $cachedEventsFields);
		}
	}

	$column_fld = array();
	if ($cachedModuleFields) {
		foreach ($cachedModuleFields as $fieldinfo) {
			$column_fld[$fieldinfo['fieldname']] = '';
		}
	}

	$log->debug("Exiting getColumnFields method ...");
	return $column_fld;
}

/** Function to get a users's mail id
 * @param $userid -- userid :: Type integer
 * @returns $email -- email :: Type string
 */
function getUserEmail($userid) {
	global $log, $adb;
	$log->debug('Entering getUserEmail('.print_r($userid, true).') method ...');
	$email = '';
	if (!empty($userid) && is_numeric($userid)) {
		$sql = 'select email1 from vtiger_users where id=?';
		$userid = (array)$userid;
		$result = $adb->pquery($sql, $userid);
		if ($result && $adb->num_rows($result)>0) {
			$email = $adb->query_result($result, 0, 'email1');
		}
	}
	$log->debug('Exiting getUserEmail method ...');
	return $email;
}

/** Function to get a userid for outlook // outlook security
 * @param $username -- username :: Type string
 * @returns $user_id -- user id :: Type integer
*/
function getUserId_Ol($username) {
	global $log, $adb;
	$log->debug("Entering getUserId_Ol(".$username.") method ...");
	$sql = "select id from vtiger_users where user_name=?";
	$result = $adb->pquery($sql, array($username));
	$num_rows = $adb->num_rows($result);
	if ($num_rows > 0) {
		$user_id = $adb->query_result($result, 0, "id");
	} else {
		$user_id = 0;
	}
	$log->debug("Exiting getUserId_Ol method ...");
	return $user_id;
}

/** Function to get a action id for a given action name //outlook security
 * @param $action -- action name :: Type string
 * @returns $actionid -- action id :: Type integer
*/
function getActionid($action) {
	global $log, $adb;
	$log->debug("Entering getActionid(".$action.") method ...");
	$actionid = '';
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include 'tabdata.php';
		$actionid = (isset($action_id_array[$action]) ? $action_id_array[$action] : '');
	}
	if ($actionid == '') {
		$query="select actionid from vtiger_actionmapping where actionname=?";
		$result =$adb->pquery($query, array($action));
		$actionid=$adb->query_result($result, 0, 'actionid');
	}
	$log->debug('Exiting getActionid method: id selected is '.$actionid);
	return $actionid;
}

/** Function to get a action for a given action id
 * @param $action id -- action id :: Type integer
 * @returns $actionname-- action name :: Type string
*/
function getActionname($actionid) {
	global $log, $adb;
	$log->debug("Entering getActionname(".$actionid.") method ...");
	$actionname='';
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include 'tabdata.php';
		$actionname = (isset($action_name_array[$actionid]) ? $action_name_array[$actionid] : '');
	}
	if ($actionname == '') {
		$query="select actionname from vtiger_actionmapping where actionid=? and securitycheck=0";
		$result =$adb->pquery($query, array($actionid));
		$actionname=$adb->query_result($result, 0, "actionname");
	}
	$log->debug("Exiting getActionname method ...");
	return $actionname;
}

/** Function to get a assigned user id for a given entity
 * @param $record -- entity id :: Type integer
 * @returns $user_id -- user id :: Type integer
*/
function getUserId($record) {
	global $log, $adb;
	$log->debug("Entering getUserId(".$record.") method ...");
	$userrs = $adb->pquery('select smownerid from vtiger_crmentity where crmid = ?', array($record));
	$user_id = $adb->query_result($userrs, 0, 'smownerid');
	$log->debug("Exiting getUserId method ...");
	return $user_id;
}

/** Function to get a user id or group id for a given entity
 * @param $record -- entity id :: Type integer
 * @returns $ownerArr -- owner id :: Type array
*/
function getRecordOwnerId($record) {
	global $log, $adb;
	$log->debug("Entering getRecordOwnerId(".$record.") method ...");
	$ownerArr=array();
	$query="select smownerid from vtiger_crmentity where crmid = ?";
	$result=$adb->pquery($query, array($record));
	if ($adb->num_rows($result) > 0) {
		$ownerId=$adb->query_result($result, 0, 'smownerid');
		$sql_result = $adb->pquery("select count(*) as count from vtiger_users where id = ?", array($ownerId));
		if ($adb->query_result($sql_result, 0, 'count') > 0) {
			$ownerArr['Users'] = $ownerId;
		} else {
			$ownerArr['Groups'] = $ownerId;
		}
	}
	$log->debug("Exiting getRecordOwnerId method ...");
	return $ownerArr;
}

/** Function to insert value to profile2field table
  * @param $profileid -- profileid :: Type integer
*/
function insertProfile2field($profileid) {
	global $log, $adb;
	$log->debug("Entering insertProfile2field(".$profileid.") method ...");

	$adb->database->SetFetchMode(ADODB_FETCH_ASSOC);
	$fld_result = $adb->pquery(
		'select tabid, fieldid from vtiger_field where generatedtype=1 and displaytype in (1,2,3) and vtiger_field.presence in (0,2) and tabid != 29',
		array()
	);
	$num_rows = $adb->num_rows($fld_result);
	for ($i=0; $i<$num_rows; $i++) {
		$tab_id = $adb->query_result($fld_result, $i, 'tabid');
		$field_id = $adb->query_result($fld_result, $i, 'fieldid');
		$params = array($profileid, $tab_id, $field_id, 0, 0, 'B');
		$adb->pquery('insert into vtiger_profile2field values (?,?,?,?,?,?)', $params);
	}
	$log->debug("Exiting insertProfile2field method ...");
}

/** Function to insert into default org field */
function insert_def_org_field() {
	global $log, $adb;
	$log->debug('Entering insert_def_org_field() method ...');
	$adb->database->SetFetchMode(ADODB_FETCH_ASSOC);
	$fld_result = $adb->pquery(
		'select tabid, fieldid from vtiger_field where generatedtype=1 and displaytype in (1,2,3) and vtiger_field.presence in (0,2) and tabid != 29',
		array()
	);
	$num_rows = $adb->num_rows($fld_result);
	for ($i=0; $i<$num_rows; $i++) {
		$tab_id = $adb->query_result($fld_result, $i, 'tabid');
		$field_id = $adb->query_result($fld_result, $i, 'fieldid');
		$params = array($tab_id, $field_id, 0, 0);
		$adb->pquery('insert into vtiger_def_org_field values (?,?,?,?)', $params);
	}
	$log->debug('Exiting insert_def_org_field() method ...');
}

/** Function to insert value to profile2field table
 * @param $fld_module -- field module :: Type string
 * @param $profileid -- profileid :: Type integer
 * @returns $result -- result :: Type string
 */
function getProfile2FieldList($fld_module, $profileid) {
	global $log, $adb;
	$log->debug("Entering getProfile2FieldList(".$fld_module.",". $profileid.") method ...");
	$tabid = getTabid($fld_module);
	$query = 'select vtiger_profile2field.visible,vtiger_field.*
		from vtiger_profile2field
		inner join vtiger_field on vtiger_field.fieldid=vtiger_profile2field.fieldid
		where vtiger_profile2field.profileid=? and vtiger_profile2field.tabid=? and vtiger_field.presence in (0,1,2)';
	$result = $adb->pquery($query, array($profileid, $tabid));
	$log->debug('Exiting getProfile2FieldList method ...');
	return $result;
}

/** Function to insert value to profile2fieldPermissions table
 * @param $fld_module -- field module :: Type string
 * @param $profileid -- profileid :: Type integer
 * @returns $return_data -- return_data :: Type string
 */
function getProfile2FieldPermissionList($fld_module, $profileid) {
	global $log;
	$log->debug("Entering getProfile2FieldPermissionList(".$fld_module.",". $profileid.") method ...");

	// Cache information to re-use
	static $_module_fieldpermission_cache = array();

	if (!isset($_module_fieldpermission_cache[$fld_module])) {
		$_module_fieldpermission_cache[$fld_module] = array();
	}

	// Lookup cache first
	$return_data = VTCacheUtils::lookupProfile2FieldPermissionList($fld_module, $profileid);

	if ($return_data === false) {
		global $adb;
		$tabid = getTabid($fld_module);

		$query = 'SELECT vtiger_profile2field.visible, vtiger_profile2field.readonly, vtiger_field.fieldlabel, vtiger_field.uitype,
			vtiger_field.fieldid, vtiger_field.displaytype, vtiger_field.typeofdata
			FROM vtiger_profile2field INNER JOIN vtiger_field ON vtiger_field.fieldid=vtiger_profile2field.fieldid
			WHERE vtiger_profile2field.profileid=? and vtiger_profile2field.tabid=? and vtiger_field.presence in (0,2)';

		$qparams = array($profileid, $tabid);
		$result = $adb->pquery($query, $qparams);
		$return_data = array();
		while ($row = $adb->fetch_array($result)) {
			$return_data[]=array(
				$row['fieldlabel'],
				$row['visible'], // From vtiger_profile2field.visible
				$row['uitype'],
				$row['readonly'],
				$row['fieldid'],
				$row['displaytype'],
				$row['typeofdata'],
			);
		}

		// Update information to cache for re-use
		VTCacheUtils::updateProfile2FieldPermissionList($fld_module, $profileid, $return_data);
	}
	$log->debug("Exiting getProfile2FieldPermissionList method ...");
	return $return_data;
}

/** Function to insert value to profile2fieldPermissions table
 * @param $fld_module -- field module :: Type string
 * @param $profileid -- profileid :: Type integer
 * @returns $return_data -- return_data :: Type string
 */
function getProfile2ModuleFieldPermissionList($fld_module, $profileid) {
	global $log, $adb;
	$log->debug("Entering getProfile2ModuleFieldPermissionList(".$fld_module.",". $profileid.") method ...");

	// Cache information to re-use
	static $_module_fieldpermission_cache = array();

	if (!isset($_module_fieldpermission_cache[$fld_module])) {
		$_module_fieldpermission_cache[$fld_module] = array();
	}

	$return_data = array();

	$tabid = getTabid($fld_module);

	$query = 'SELECT vtiger_profile2tab.tabid, vtiger_profile2tab.permissions, vtiger_field.fieldlabel, vtiger_field.uitype,
		vtiger_field.fieldid, vtiger_field.displaytype, vtiger_field.typeofdata
		FROM vtiger_profile2tab INNER JOIN vtiger_field ON vtiger_field.tabid=vtiger_profile2tab.tabid
		WHERE vtiger_profile2tab.profileid=? AND vtiger_profile2tab.tabid=? AND vtiger_field.presence in (0,2)';
	$qparams = array($profileid, $tabid);
	$result = $adb->pquery($query, $qparams);

	for ($i=0; $i<$adb->num_rows($result); $i++) {
		$fieldid = $adb->query_result($result, $i, "fieldid");
		$checkentry = $adb->pquery('SELECT 1 FROM vtiger_profile2field WHERE profileid=? AND tabid=? AND fieldid =?', array($profileid,$tabid,$fieldid));
		$visible_value = 0;
		$readOnlyValue = 0;
		if ($adb->num_rows($checkentry) == 0) {
			$sql11='INSERT INTO vtiger_profile2field VALUES(?,?,?,?,?,?)';
			$adb->pquery($sql11, array($profileid, $tabid, $fieldid,$visible_value, $readOnlyValue, 'B'));
		}

		$sql = 'SELECT vtiger_profile2field.visible, vtiger_profile2field.readonly, summary FROM vtiger_profile2field WHERE fieldid=? AND tabid=? AND profileid=?';
		$params = array($fieldid,$tabid,$profileid);
		$res = $adb->pquery($sql, $params);

		$return_data[] = array(
			$adb->query_result($result, $i, 'fieldlabel'),
			$adb->query_result($res, 0, 'visible'), // From vtiger_profile2field.visible
			$adb->query_result($result, $i, 'uitype'),
			$adb->query_result($res, 0, 'readonly'), // From vtiger_profile2field.readonly
			$adb->query_result($result, $i, 'fieldid'),
			$adb->query_result($result, $i, 'displaytype'),
			$adb->query_result($result, $i, 'typeofdata'),
			$adb->query_result($res, 0, 'summary') // From vtiger_profile2field.summary
		);
	}

	$log->debug("Exiting getProfile2ModuleFieldPermissionList method ...");
	return $return_data;
}

/** Function to getProfile2allfieldsListinsert value to profile2fieldPermissions table
 * @param $mod_array -- mod_array :: Type string
 * @param $profileid -- profileid :: Type integer
 * @returns $profilelist -- profilelist :: Type string
 */
function getProfile2AllFieldList($mod_array, $profileid) {
	global $log;
	$log->debug("Entering getProfile2AllFieldList({modules}, $profileid) method ...");
	$profilelist=array();
	foreach ($mod_array as $key => $value) {
		$profilelist[$key]=getProfile2ModuleFieldPermissionList($key, $profileid);
	}
	$log->debug('Exiting getProfile2AllFieldList method ...');
	return $profilelist;
}

/** Function to getdefaultfield organisation list for a given module
 * @param $fld_module -- module name :: Type string
 * @returns $result -- string :: Type object
 */
function getDefOrgFieldList($fld_module) {
	global $log, $adb;
	$log->debug("Entering getDefOrgFieldList(".$fld_module.") method ...");

	$tabid = getTabid($fld_module);

	$query = 'select vtiger_def_org_field.visible,vtiger_field.*
		from vtiger_def_org_field
		inner join vtiger_field on vtiger_field.fieldid=vtiger_def_org_field.fieldid
		where vtiger_def_org_field.tabid=? and vtiger_field.presence in (0,2)';
	$qparams = array($tabid);
	$result = $adb->pquery($query, $qparams);
	$log->debug('Exiting getDefOrgFieldList method ...');
	return $result;
}

/** Function to getQuickCreate for a given tabid
 * @param $tabid -- tab id :: Type string
 * @param $actionid -- action id :: Type integer
 * @returns $QuickCreateForm -- QuickCreateForm :: Type boolean
 */
function getQuickCreate($tabid, $actionid) {
	global $log;
	$log->debug("Entering getQuickCreate(".$tabid.",".$actionid.") method ...");
	$module=getTabModuleName($tabid);
	$actionname=getActionname($actionid);
	$QuickCreateForm= 'true';

	$perr=isPermitted($module, $actionname);
	if ($perr == 'no') {
		$QuickCreateForm= 'false';
	}
	$log->debug("Exiting getQuickCreate method ...");
	return $QuickCreateForm;
}

/** Function to get unitprice for a given product id
 * @param $productid -- product id :: Type integer
 * @returns $up -- up :: Type string
 */
function getUnitPrice($productid, $module = 'Products') {
	global $log, $adb;
	$log->debug("Entering getUnitPrice($productid,$module) method ...");

	if ($module == 'Services') {
		$query = 'select unit_price from vtiger_service where serviceid=?';
	} else {
		$query = 'select unit_price from vtiger_products where productid=?';
	}
	$result = $adb->pquery($query, array($productid));
	$unitpice = $adb->query_result($result, 0, 'unit_price');
	$log->debug('Exiting getUnitPrice method ...');
	return $unitpice;
}

/** Function to upload product image file
 * @param $mode -- mode :: Type string
 * @param $id -- id :: Type integer
 * @returns $ret_array -- return array:: Type array
 * @deprecated
 */
function upload_product_image_file($mode, $id) {
	global $log, $root_directory;
	$log->debug("Entering upload_product_image_file(".$mode.",".$id.") method ...");
	$uploaddir = $root_directory .'/cache/';

	$file_path_name = $_FILES['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = $_REQUEST['imagename_hidden'];
	} else {
		//allowed file pathname like UTF-8 Character
		$file_name = ltrim(basename(" ".$file_path_name)); // basename($file_path_name);
	}
	$file_name = $id.'_'.$file_name;
	//$filetype= $_FILES['imagename']['type'];
	$filesize = $_FILES['imagename']['size'];

	$ret_array = array();

	if ($filesize > 0) {
		if (move_uploaded_file($_FILES['imagename']['tmp_name'], $uploaddir.$file_name)) {
			$upload_status = 'yes';
			$ret_array['status'] = $upload_status;
			$ret_array['file_name'] = $file_name;
		} else {
			$errorCode = $_FILES['imagename']['error'];
			$upload_status = 'no';
			$ret_array['status'] = $upload_status;
			$ret_array['errorcode'] = $errorCode;
		}
	} else {
		$upload_status = 'no';
		$ret_array['status'] = $upload_status;
	}
	$log->debug('Exiting upload_product_image_file method ...');
	return $ret_array;
}

/** Function to upload product image file
 * @param $id -- id :: Type integer
 * @param $deleted_array -- images to be deleted :: Type array
 * @returns $imagename -- imagelist:: Type array
 */
function getProductImageName($id, $deleted_array = '') {
	global $log, $adb;
	$log->debug("Entering getProductImageName(".$id.",".$deleted_array."='') method ...");
	$image_array=array();
	$query = "select imagename from vtiger_products where productid=?";
	$result = $adb->pquery($query, array($id));
	$image_name = $adb->query_result($result, 0, "imagename");
	$image_array=explode("###", $image_name);
	if ($deleted_array!='') {
		$resultant_image = array();
		$resultant_image=array_merge(array_diff($image_array, $deleted_array));
		$imagelists=implode('###', $resultant_image);
		$retval = $imagelists;
	} else {
		$retval = $image_name;
	}
	$log->debug("Exiting getProductImageName method ...");
	return $retval;
}

/** Function to get Contact images
 * @param $id -- id :: Type integer
 * @returns $imagename -- imagename:: Type string
 */
function getContactImageName($id) {
	global $log, $adb;
	$log->debug("Entering getContactImageName(".$id.") method ...");
	$query = "select imagename from vtiger_contactdetails where contactid=?";
	$result = $adb->pquery($query, array($id));
	$image_name = $adb->query_result($result, 0, "imagename");
	$log->debug("Inside getContactImageName. The image_name is ".$image_name);
	$log->debug("Exiting getContactImageName method ...");
	return $image_name;
}

/** Function to update sub total in inventory
 * @param $module -- module name :: Type string
 * @param $tablename -- tablename :: Type string
 * @param $colname -- colname :: Type string
 * @param $colname1 -- coluname1 :: Type string
 * @param $entid_fld -- entity field :: Type string
 * @param $entid -- entid :: Type integer
 * @param $prod_total -- totalproduct :: Type integer
 */
function updateSubTotal($module, $tablename, $colname, $colname1, $entid_fld, $entid, $prod_total) {
	global $log, $adb;
	$log->debug("Entering updateSubTotal(".$module.",".$tablename.",".$colname.",".$colname1.",".$entid_fld.",".$entid.",".$prod_total.") method ...");
	//getting the subtotal
	$query = "select ".$colname.",".$colname1." from ".$tablename." where ".$entid_fld."=?";
	$result1 = $adb->pquery($query, array($entid));
	$subtot = $adb->query_result($result1, 0, $colname);
	$subtot_upd = $subtot - $prod_total;

	$gdtot = $adb->query_result($result1, 0, $colname1);
	$gdtot_upd = $gdtot - $prod_total;

	//updating the subtotal
	$sub_query = "update $tablename set $colname=?, $colname1=? where $entid_fld=?";
	$adb->pquery($sub_query, array($subtot_upd, $gdtot_upd, $entid));
	$log->debug("Exiting updateSubTotal method ...");
}

/** Function to get Inventory Total
 * @param $return_module -- return module :: Type string
 * @param $id -- entity id :: Type integer
 * @returns $total -- total:: Type integer
 * *** FUNCTION NOT USED IN THE APPLICATION > left only in case it is used by some extension
 */
function getInventoryTotal($return_module, $id) {
	global $log, $adb;
	$log->debug('Entering getInventoryTotal('.$return_module.','.$id.') method ...');
	if ($return_module == 'Potentials') {
		$query ='select vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_seproductsrel.*
			from vtiger_products
			inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid
			where crmid=?';
	} elseif ($return_module == 'Products') {
		$query='select vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_crmentity.*
			from vtiger_products
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid
			where vtiger_crmentity.deleted=0 and productid=?';
	}
	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	$total=0;
	for ($i=1; $i<=$num_rows; $i++) {
		$unitprice=$adb->query_result($result, $i-1, 'unit_price');
		$qty=$adb->query_result($result, $i-1, 'quantity');
		$listprice=$adb->query_result($result, $i-1, 'listprice');
		if ($listprice == '') {
			$listprice = $unitprice;
		}
		if ($qty =='') {
			$qty = 1;
		}
		$total = $total+($qty*$listprice);
	}
	$log->debug('Exiting getInventoryTotal method ...');
	return $total;
}

/** Function to update product quantity
 * @param $product_id -- product id :: Type integer
 * @param $upd_qty -- quantity :: Type integer
 */
function updateProductQty($product_id, $upd_qty) {
	global $log, $adb;
	$log->debug("Entering updateProductQty(".$product_id.",". $upd_qty.") method ...");
	$query= "update vtiger_products set qtyinstock=? where productid=?";
	$adb->pquery($query, array($upd_qty, $product_id));
	$log->debug("Exiting updateProductQty method ...");
}

/** Function to get account information
 * @param $parent_id -- parent id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function get_account_info($parent_id) {
	global $log, $adb;
	$log->debug('Entering get_account_info('.$parent_id.') method ...');
	$query = 'select related_to from vtiger_potential where potentialid=?';
	$result = $adb->pquery($query, array($parent_id));
	$accountid=$adb->query_result($result, 0, 'related_to');
	$log->debug('Exiting get_account_info method ...');
	return $accountid;
}

function getFolderSize($dir) {
	$size = 0;
	foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
		$size += is_file($each) ? filesize($each) : getFolderSize($each);
	}
	return $size;
}

/** Function to get email text field
 * @param $module -- module name :: Type name
 * @param $id -- entity id :: Type integer
 * @returns $hidden -- hidden:: Type string
 */
//Added to get the parents list as hidden for Emails -- 09-11-2005
function getEmailParentsList($module, $id, $focus = false) {
	global $log, $adb;
	$log->debug("Entering getEmailParentsList(".$module.",".$id.") method ...");
	// If the information is not sent then read it
	if ($focus === false) {
		if ($module == 'Contacts') {
			$focus = new Contacts();
		}
		if ($module == 'Leads') {
			$focus = new Leads();
		}
		$focus->retrieve_entity_info($id, $module);
	}

	$fieldid = 0;
	$fieldname = 'email';
	if ($focus->column_fields['email'] == '' && !empty($focus->column_fields['secondaryemail'])) {
		$fieldname='secondaryemail';
	}
	$res = $adb->pquery('select fieldid from vtiger_field where tabid = ? and fieldname= ? and vtiger_field.presence in (0,2)', array(getTabid($module), $fieldname));
	$fieldid = $adb->query_result($res, 0, 'fieldid');

	$hidden  = '<input type="hidden" name="emailids" value="'.$id.'@'.$fieldid.'|">';
	$hidden .= '<input type="hidden" name="pmodule" value="'.$module.'">';

	$log->debug('Exiting getEmailParentsList method ...');
	return $hidden;
}

/** This Function returns the current status of the specified Purchase Order.
 * The following is the input parameter for the function
 *  $po_id --> Purchase Order Id, Type:Integer
 */
function getPoStatus($po_id) {
	global $log, $adb;
	$log->debug("Entering getPoStatus(".$po_id.") method ...");
	$sql = "select postatus from vtiger_purchaseorder where purchaseorderid=?";
	$result = $adb->pquery($sql, array($po_id));
	$po_status = $adb->query_result($result, 0, "postatus");
	$log->debug("Exiting getPoStatus method ...");
	return $po_status;
}

/** This Function adds the specified product quantity to the Product Quantity in Stock in the Warehouse
 * The following is the input parameter for the function:
 *  $productId --> ProductId, Type:Integer
 *  $qty --> Quantity to be added, Type:Integer
 */
function addToProductStock($productId, $qty) {
	global $log, $adb;
	$log->debug("Entering addToProductStock(".$productId.",".$qty.") method ...");
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck + $qty;
	$sql = "UPDATE vtiger_products set qtyinstock=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting addToProductStock method ...");
}

/** This Function adds the specified product quantity to the Product Quantity in Demand in the Warehouse
 * @param int $productId - ProductId
 * @param int $qty - Quantity to be added
 */
function addToProductDemand($productId, $qty) {
	global $log, $adb;
	$log->debug("Entering addToProductDemand(".$productId.",".$qty.") method ...");
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck + $qty;
	$sql = "UPDATE vtiger_products set qtyindemand=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting addToProductDemand method ...");
}

/** This Function subtract the specified product quantity to the Product Quantity in Stock in the Warehouse
 * @param int $productId - ProductId
 * @param int $qty - Quantity to be subtracted
 */
function deductFromProductStock($productId, $qty) {
	global $log, $adb;
	$log->debug("Entering deductFromProductStock(".$productId.",".$qty.") method ...");
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck - $qty;
	$sql = "UPDATE vtiger_products set qtyinstock=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting deductFromProductStock method ...");
}

/**	This Function subtract the specified product quantity to the Product Quantity in Demand in the Warehouse
 *	@param int $productId - ProductId
 *	@param int $qty - Quantity to be subtract
 */
function deductFromProductDemand($productId, $qty) {
	global $log, $adb;
	$log->debug("Entering deductFromProductDemand(".$productId.",".$qty.") method ...");
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck - $qty;
	$sql = "UPDATE vtiger_products set qtyindemand=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting deductFromProductDemand method ...");
}

/** This Function returns the current product quantity in stock.
 * The following is the input parameter for the function:
 *  $product_id --> ProductId, Type:Integer
 */
function getProductQtyInStock($product_id) {
	global $log, $adb;
	$log->debug("Entering getProductQtyInStock(".$product_id.") method ...");
	$query1 = "select qtyinstock from vtiger_products where productid=?";
	$result=$adb->pquery($query1, array($product_id));
	$qtyinstck= $adb->query_result($result, 0, "qtyinstock");
	$log->debug("Exiting getProductQtyInStock method ...");
	return $qtyinstck;
}

/**	This Function returns the current product quantity in demand.
 *	@param int $product_id - ProductId
 *	@return int $qtyInDemand - Quantity in Demand of a product
 */
function getProductQtyInDemand($product_id) {
	global $log, $adb;
	$log->debug("Entering getProductQtyInDemand(".$product_id.") method ...");
	$query1 = "select qtyindemand from vtiger_products where productid=?";
	$result = $adb->pquery($query1, array($product_id));
	$qtyInDemand = $adb->query_result($result, 0, "qtyindemand");
	$log->debug("Exiting getProductQtyInDemand method ...");
	return $qtyInDemand;
}

/** Function to seperate the Date and Time
  * This function accepts a sting with date and time and
  * returns an array of two elements.The first element
  * contains the date and the second one contains the time
  */
function getDateFromDateAndtime($date_time) {
	global $log;
	$log->debug("Entering getDateFromDateAndtime(".$date_time.") method ...");
	$result = explode(" ", $date_time);
	$log->debug("Exiting getDateFromDateAndtime method ...");
	return $result;
}

/** Function to get header for block in edit/create and detailview
  * @param $header_label -- header label :: Type string
  * @returns $output -- output:: Type string
  */
function getBlockTableHeader($header_label) {
	global $log, $mod_strings;
	$log->debug("Entering getBlockTableHeader(".$header_label.") method ...");
	$label = $mod_strings[$header_label];
	$output = $label;
	$log->debug("Exiting getBlockTableHeader method ...");
	return $output;
}

/**     Function to get the vtiger_table name from 'field' vtiger_table for the input vtiger_field based on the module
 *      @param  : string $module - current module value
 *      @param  : string $fieldname - vtiger_fieldname to which we want the vtiger_tablename
 *      @return : string $tablename - vtiger_tablename in which $fieldname is a column, which is retrieved from 'field' vtiger_table per $module basis
 */
function getTableNameForField($module, $fieldname) {
	global $log, $adb;
	$log->debug("Entering getTableNameForField(".$module.",".$fieldname.") method ...");
	$tabid = getTabid($module);
	//Asha
	if ($module == 'Calendar') {
		$tabid = array('9','16');
	}
	$sql = "select tablename from vtiger_field where tabid in (". generateQuestionMarks($tabid) .") and vtiger_field.presence in (0,2) and columnname like ?";
	$res = $adb->pquery($sql, array($tabid, '%'.$fieldname.'%'));

	$tablename = '';
	if ($adb->num_rows($res) > 0) {
		$tablename = $adb->query_result($res, 0, 'tablename');
	}

	$log->debug("Exiting getTableNameForField method ...");
	return $tablename;
}

/** Function to get the module name of a 'field'
 * @param  : int $fieldid - fieldid
 * @return : string modulename - module name of the fieldid
 */
function getModuleForField($fieldid) {
	global $log, $adb;
	$log->debug("Entering getModuleForField($fieldid) method ...");
	if ($fieldid == -1) {
		return 'Users';
	}
	$sql = 'SELECT vtiger_tab.name
		FROM vtiger_field
		INNER JOIN vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
		WHERE fieldid = ?';
	$res = $adb->pquery($sql, array($fieldid));

	$modname = '';
	if ($adb->num_rows($res) > 0) {
		$modname = $adb->query_result($res, 0, 'name');
	}

	$log->debug('Exiting getModuleForField method ...');
	return $modname;
}

/** Function to get parent record owner
  * @param $tabid -- tabid :: Type integer
  * @param $parModId -- parent module id :: Type integer
  * @param $record_id -- record id :: Type integer
  * @returns $parentRecOwner -- parentRecOwner:: Type integer
  */
function getParentRecordOwner($tabid, $parModId, $record_id) {
	global $log;
	$log->debug("Entering getParentRecordOwner(".$tabid.",".$parModId.",".$record_id.") method ...");
	$parentRecOwner=array();
	$parentTabName=getTabname($parModId);
	$relTabName=getTabname($tabid);
	$fn_name="get".$relTabName."Related".$parentTabName;
	$ent_id=$fn_name($record_id);
	if ($ent_id != '') {
		$parentRecOwner=getRecordOwnerId($ent_id);
	}
	$log->debug("Exiting getParentRecordOwner method ...");
	return $parentRecOwner;
}

/** Function to get potential related accounts
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getPotentialsRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug("Entering getPotentialsRelatedAccounts(".$record_id.") method ...");
	$query="select related_to from vtiger_potential where potentialid=?";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'related_to');
	$log->debug("Exiting getPotentialsRelatedAccounts method ...");
	return $accountid;
}

/** Function to get email related accounts
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getEmailsRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('Entering getEmailsRelatedAccounts('.$record_id.') method ...');
	$query = "select vtiger_seactivityrel.crmid
		from vtiger_seactivityrel
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid
		where vtiger_crmentity.setype='Accounts' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'crmid');
	$log->debug('Exiting getEmailsRelatedAccounts method ...');
	return $accountid;
}
/** Function to get email related Leads
 * @param $record_id -- record id :: Type integer
 * @returns $leadid -- leadid:: Type integer
 */
function getEmailsRelatedLeads($record_id) {
	global $log, $adb;
	$log->debug('Entering getEmailsRelatedLeads('.$record_id.') method ...');
	$query = "select vtiger_seactivityrel.crmid
		from vtiger_seactivityrel
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid
		where vtiger_crmentity.setype='Leads' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$leadid=$adb->query_result($result, 0, 'crmid');
	$log->debug('Exiting getEmailsRelatedLeads method ...');
	return $leadid;
}

/** Function to get HelpDesk related Accounts
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getHelpDeskRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug("Entering getHelpDeskRelatedAccounts($record_id) method ...");
	$query="select parent_id
		from vtiger_troubletickets
		inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.parent_id
		where ticketid=? and vtiger_crmentity.setype='Accounts'";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'parent_id');
	$log->debug('Exiting getHelpDeskRelatedAccounts method ...');
	return $accountid;
}

/** Function to get Quotes related Accounts
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getQuotesRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug("Entering getQuotesRelatedAccounts(".$record_id.") method ...");
	$query="select accountid from vtiger_quotes where quoteid=?";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug("Exiting getQuotesRelatedAccounts method ...");
	return $accountid;
}

/** Function to get Quotes related Potentials
 * @param $record_id -- record id :: Type integer
 * @returns $potid -- potid:: Type integer
 */
function getQuotesRelatedPotentials($record_id) {
	global $log, $adb;
	$log->debug('Entering getQuotesRelatedPotentials('.$record_id.') method ...');
	$result=$adb->pquery('select potentialid from vtiger_quotes where quoteid=?', array($record_id));
	$potid=$adb->query_result($result, 0, 'potentialid');
	$log->debug('Exiting getQuotesRelatedPotentials method ...');
	return $potid;
}

/** Function to get Quotes related Potentials
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getSalesOrderRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug("Entering getSalesOrderRelatedAccounts(".$record_id.") method ...");
	$query="select accountid from vtiger_salesorder where salesorderid=?";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug("Exiting getSalesOrderRelatedAccounts method ...");
	return $accountid;
}

/** Function to get SalesOrder related Potentials
 * @param $record_id -- record id :: Type integer
 * @returns $potid -- potid:: Type integer
 */
function getSalesOrderRelatedPotentials($record_id) {
	global $log, $adb;
	$log->debug("Entering getSalesOrderRelatedPotentials(".$record_id.") method ...");
	$query="select potentialid from vtiger_salesorder where salesorderid=?";
	$result=$adb->pquery($query, array($record_id));
	$potid=$adb->query_result($result, 0, 'potentialid');
	$log->debug("Exiting getSalesOrderRelatedPotentials method ...");
	return $potid;
}

/** Function to get SalesOrder related Quotes
 * @param $record_id -- record id :: Type integer
 * @returns $qtid -- qtid:: Type integer
 */
function getSalesOrderRelatedQuotes($record_id) {
	global $log, $adb;
	$log->debug("Entering getSalesOrderRelatedQuotes(".$record_id.") method ...");
	$query="select quoteid from vtiger_salesorder where salesorderid=?";
	$result=$adb->pquery($query, array($record_id));
	$qtid=$adb->query_result($result, 0, 'quoteid');
	$log->debug("Exiting getSalesOrderRelatedQuotes method ...");
	return $qtid;
}

/** Function to get Invoice related Accounts
 * @param $record_id -- record id :: Type integer
 * @returns $accountid -- accountid:: Type integer
 */
function getInvoiceRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug("Entering getInvoiceRelatedAccounts(".$record_id.") method ...");
	$query="select accountid from vtiger_invoice where invoiceid=?";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug("Exiting getInvoiceRelatedAccounts method ...");
	return $accountid;
}

/** Function to get Invoice related SalesOrder
 * @param $record_id -- record id :: Type integer
 * @returns $soid -- soid:: Type integer
 */
function getInvoiceRelatedSalesOrder($record_id) {
	global $log, $adb;
	$log->debug("Entering getInvoiceRelatedSalesOrder(".$record_id.") method ...");
	$query="select salesorderid from vtiger_invoice where invoiceid=?";
	$result=$adb->pquery($query, array($record_id));
	$soid=$adb->query_result($result, 0, 'salesorderid');
	$log->debug("Exiting getInvoiceRelatedSalesOrder method ...");
	return $soid;
}

/** Function to get Days and Dates in between the dates specified
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */
function get_days_n_dates($st, $en) {
	global $log;
	$log->debug("Entering get_days_n_dates(".$st.",".$en.") method ...");
	$stdate_arr=explode("-", $st);
	$endate_arr=explode("-", $en);

	$dateDiff = mktime(0, 0, 0, $endate_arr[1], $endate_arr[2], $endate_arr[0]) - mktime(0, 0, 0, $stdate_arr[1], $stdate_arr[2], $stdate_arr[0]);//get dates difference

	$days = floor($dateDiff/60/60/24)+1; //to calculate no of. days
	for ($i=0; $i<$days; $i++) {
		$day_date[] = date("Y-m-d", mktime(0, 0, 0, date("$stdate_arr[1]"), (date("$stdate_arr[2]")+($i)), date("$stdate_arr[0]")));
	}
	if (!isset($day_date)) {
		$day_date=0;
	}
	$nodays_dates=array($days,$day_date);
	$log->debug("Exiting get_days_n_dates method ...");
	return $nodays_dates; //passing no of days , days in between the days
}

/** Function to get the start and End Dates based upon the period which we give
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */
function start_end_dates($period) {
	global $log;
	$log->debug("Entering start_end_dates(".$period.") method ...");
	$st_thisweek= date("Y-m-d", mktime(0, 0, 0, date("n"), (date("j")-date("w")), date("Y")));
	if ($period=="tweek") {
		$st_date= date("Y-m-d", mktime(0, 0, 0, date("n"), (date("j")-date("w")), date("Y")));
		$end_date = date("Y-m-d", mktime(0, 0, 0, date("n"), (date("j")-1), date("Y")));
		$st_week= date("w", mktime(0, 0, 0, date("n"), date("j"), date("Y")));
		if ($st_week==0) {
			$start_week=explode("-", $st_thisweek);
			$st_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-7), date("$start_week[0]")));
			$end_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-1), date("$start_week[0]")));
		}
		$period_type="week";
		$width="360";
	} elseif ($period=="lweek") {
		$start_week=explode("-", $st_thisweek);
		$st_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-7), date("$start_week[0]")));
		$end_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-1), date("$start_week[0]")));
		$st_week= date("w", mktime(0, 0, 0, date("n"), date("j"), date("Y")));
		if ($st_week==0) {
			$start_week=explode("-", $st_thisweek);
			$st_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-14), date("$start_week[0]")));
			$end_date = date("Y-m-d", mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-8), date("$start_week[0]")));
		}
		$period_type="week";
		$width="360";
	} elseif ($period=="tmon") {
		$period_type="month";
		$width="840";
		$st_date = date("Y-m-d", mktime(0, 0, 0, date("m"), "01", date("Y")));
		$end_date = date("Y-m-t");
	} elseif ($period=="lmon") {
		$st_date=date("Y-m-d", mktime(0, 0, 0, date("n")-1, date("1"), date("Y")));
		$end_date = date("Y-m-d", mktime(0, 0, 1, date("n"), 0, date("Y")));
		$period_type="month";
		$start_month=date("d", mktime(0, 0, 0, date("n"), date("j"), date("Y")));
		if ($start_month==1) {
			$st_date=date("Y-m-d", mktime(0, 0, 0, date("n")-2, date("1"), date("Y")));
			$end_date = date("Y-m-d", mktime(0, 0, 1, date("n")-1, 0, date("Y")));
		}
		$width="840";
	} else {
		$curr_date=date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		$today_date=explode("-", $curr_date);
		$lastday_date=date("Y-m-d", mktime(0, 0, 0, date("$today_date[1]"), date("$today_date[2]")-1, date("$today_date[0]")));
		$st_date=$lastday_date;
		$end_date=$lastday_date;
		$period_type="yday";
		$width="250";
	}
	if ($period_type=="yday") {
		$height="160";
	} else {
		$height="250";
	}
	$datevalues=array($st_date,$end_date,$period_type,$width,$height);
	$log->debug("Exiting start_end_dates method ...");
	return $datevalues;
}

/** Function to get the Graph and vtiger_table format for a particular date based upon the period
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */
function Graph_n_table_format($period_type, $date_value) {
	global $log;
	$log->debug("Entering Graph_n_table_format(".$period_type.",".$date_value.") method ...");
	$date_val=explode("-", $date_value);
	if ($period_type=="month") {  //to get the vtiger_table format dates
		$table_format=date("j", mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=date("D", mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
	} elseif ($period_type=="week") {
		$table_format=date("d/m", mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=date("D", mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
	} elseif ($period_type=="yday") {
		$table_format=date("j", mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=$table_format;
	}
	$values=array($graph_format,$table_format);
	$log->debug("Exiting Graph_n_table_format method ...");
	return $values;
}

/** Function to get image count for a given product
 * @param $id -- product id :: Type integer
 * @returns count -- count:: Type integer
 */
function getImageCount($id) {
	global $log, $adb;
	$log->debug("Entering getImageCount(".$id.") method ...");
	$image_lists=array();
	$query="select imagename from vtiger_products where productid=?";
	$result=$adb->pquery($query, array($id));
	$imagename=$adb->query_result($result, 0, 'imagename');
	$image_lists=explode("###", $imagename);
	$log->debug("Exiting getImageCount method ...");
	return count($image_lists);
}

/** Function to get user image for a given user
 * @param $id -- user id :: Type integer
 * @returns $image_name -- image name:: Type string
 */
function getUserImageName($id) {
	global $log, $adb;
	$log->debug("Entering getUserImageName(".$id.") method ...");
	$query = "select imagename from vtiger_users where id=?";
	$result = $adb->pquery($query, array($id));
	$image_name = $adb->query_result($result, 0, "imagename");
	$log->debug("Inside getUserImageName. The image_name is ".$image_name);
	$log->debug("Exiting getUserImageName method ...");
	return $image_name;
}

/** Function to get all user images for displaying it in listview
 * @returns $image_name -- image name:: Type array
 */
function getUserImageNames() {
	global $log, $adb;
	$log->debug("Entering getUserImageNames() method ...");
	$query = "select imagename from vtiger_users where deleted=0";
	$result = $adb->pquery($query, array());
	$image_name=array();
	for ($i=0; $i<$adb->num_rows($result); $i++) {
		if ($adb->query_result($result, $i, "imagename")!='') {
			$image_name[] = $adb->query_result($result, $i, "imagename");
		}
	}
	$log->debug("Inside getUserImageNames.");
	if (count($image_name) > 0) {
		$log->debug("Exiting getUserImageNames method ...");
		return $image_name;
	}
}

/**   Function to remove the script tag in the contents
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 */
function strip_selected_tags($text, $tags = array()) {
	$args = func_get_args();
	$text = array_shift($args);
	$tags = func_num_args() > 2 ? array_diff($args, array($text))  : (array)$tags;
	foreach ($tags as $tag) {
		if (preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)) {
			$text = str_replace($found[0], $found[1], $text);
		}
	}
	return $text;
}

/** Function to check whether user has opted for internal mailer
 * @returns $int_mailer -- int mailer:: Type boolean
 */
function useInternalMailer() {
	global $current_user,$adb;
	$rs = $adb->pquery('select int_mailer from vtiger_mail_accounts where user_id=?', array($current_user->id));
	return $adb->query_result($rs, 0, 'int_mailer');
}

/**
* the function is like unescape in javascript
* added by dingjianting on 2006-10-1 for picklist editor
*/
function utf8RawUrlDecode($source) {
	global $default_charset;
	$decodedStr = "";
	$pos = 0;
	$len = strlen($source);
	while ($pos < $len) {
		$charAt = substr($source, $pos, 1);
		if ($charAt == '%') {
			$pos++;
			$charAt = substr($source, $pos, 1);
			if ($charAt == 'u') {
				// we got a unicode character
				$pos++;
				$unicodeHexVal = substr($source, $pos, 4);
				$unicode = hexdec($unicodeHexVal);
				$entity = "&#". $unicode . ';';
				$decodedStr .= utf8_encode($entity);
				$pos += 4;
			} else {
				// we have an escaped ascii character
				$hexVal = substr($source, $pos, 2);
				$decodedStr .= chr(hexdec($hexVal));
				$pos += 2;
			}
		} else {
			$decodedStr .= $charAt;
			$pos++;
		}
	}
	if ($default_charset == 'UTF-8') {
		return html_to_utf8($decodedStr);
	} else {
		return $decodedStr;
	}
	//return html_to_utf8($decodedStr);
}

/**
*simple HTML to UTF-8 conversion:
*/
function html_to_utf8($data) {
	return preg_replace_callback("/\\&\\#(\d{3,10})\\;/", '_html_to_utf8', $data);
}

function decode_html($str) {
	global $default_charset;
	// Direct Popup action or Ajax Popup action should be treated the same.
	$request['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$request['file'] = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';
	if ($request['action'] == 'Popup' || $request['file'] == 'Popup') {
		return html_entity_decode($str);
	} else {
		return html_entity_decode($str, ENT_QUOTES, $default_charset);
	}
}

/**
 * Alternative decoding function which coverts irrespective of $_REQUEST values.
 * Useful in case of Popup (Listview etc...) where decode_html will not work as expected
 */
function decode_html_force($str) {
	global $default_charset;
	return html_entity_decode($str, ENT_QUOTES, $default_charset);
}

function popup_decode_html($str) {
	global $default_charset;
	$slashes_str = popup_from_html($str);
	$slashes_str = htmlspecialchars($slashes_str, ENT_QUOTES, $default_charset);
	return decode_html(br2nl($slashes_str));
}

function _html_to_utf8($data) {
	$data = $data[1];
	if ($data > 127) {
		$i = 5;
		while (($i--) > 0) {
			if ($data != ($a = $data % ($p = pow(64, $i)))) {
				$ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
				for ($i; $i > 0; $i--) {
					$ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
				}
				break;
			}
		}
	} else {
		$ret = "&#$data;";
	}
	return $ret;
}

// Return Question mark
function _questionify($v) {
	return '?';
}

/**
* Function to generate question marks for a given list of items
*/
function generateQuestionMarks($items_list) {
	// array_map will call the function specified in the first parameter for every element of the list in second parameter
	if (is_array($items_list)) {
		return implode(',', array_map('_questionify', $items_list));
	} else {
		return implode(',', array_map('_questionify', explode(',', $items_list)));
	}
}

/**
* Function to find the UI type of a field based on the uitype id
*/
function is_uitype($uitype, $reqtype) {
	$ui_type_arr = array(
		'_date_' => array(5, 6, 23, 70),
		'_picklist_' => array(15, 16, 52, 53, 54, 55, 62, 63, 66, 76, 77, 78, 80, 98, 101, 115, 357),
		'_users_list_' => array(52),
	);

	if ($ui_type_arr[$reqtype] != null) {
		if (in_array($uitype, $ui_type_arr[$reqtype])) {
			return true;
		}
	}
	return false;
}
/**
 * Function to escape quotes
 * @param $value - String in which single quotes have to be replaced.
 * @return Input string with single quotes escaped.
 */
function escape_single_quotes($value) {
	if (isset($value)) {
		$value = addslashes($value);
	}
	return $value;
}

/**
 * Function to format the input value for SQL like clause.
 * @param $str - Input string value to be formatted.
 * @param $flag - By default set to 0 (Will look for cases %string%).
 *                If set to 1 - Will look for cases %string.
 *                If set to 2 - Will look for cases string%.
 * @return String formatted as per the SQL like clause requirement
 */
function formatForSqlLike($str, $flag = 0, $is_field = false) {
	global $adb;
	if (isset($str)) {
		if ($is_field==false) {
			$str = str_replace('%', '\%', $str);
			$str = str_replace('_', '\_', $str);
			if ($flag == 0) {
				$str = '%'. $str .'%';
			} elseif ($flag == 1) {
				$str = '%'. $str;
			} elseif ($flag == 2) {
				$str = $str .'%';
			}
		} else {
			if ($flag == 0) {
				$str = 'concat("%",'. $str .',"%")';
			} elseif ($flag == 1) {
				$str = 'concat("%",'. $str .')';
			} elseif ($flag == 2) {
				$str = 'concat('. $str .',"%")';
			}
		}
	}
	return $adb->sql_escape_string($str);
}

/**
 * Get Current Module (global variable or from request)
 */
function getCurrentModule($perform_set = false) {
	global $currentModule,$root_directory;
	if (!empty($currentModule)) {
		return $currentModule;
	}

	// Do some security check and return the module information
	if (isset($_REQUEST['module'])) {
		$is_module = false;
		$module = vtlib_purify($_REQUEST['module']);
		$dir = @scandir($root_directory.'modules', SCANDIR_SORT_NONE);
		$temp_arr = array('.','..','Vtiger');
		$res_arr = @array_diff($dir, $temp_arr);
		if (!preg_match("/[\/.]/", $module)) {
			$is_module = @in_array($module, $res_arr);
		}

		if ($is_module) {
			if ($perform_set) {
				$currentModule = $module;
			}
			return $module;
		}
	}
	return null;
}

/**
 * Set the language strings.
 */
function setCurrentLanguage($active_module = null) {
	global $current_language, $default_language, $app_strings, $mod_strings, $currentModule;

	if ($active_module==null) {
		if (!isset($currentModule)) {
			$active_module = getCurrentModule();
		} else {
			$active_module = $currentModule;
		}
	}

	if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
		$current_language = $_SESSION['authenticated_user_language'];
	} else {
		$current_language = $default_language;
	}

	//set module and application string arrays based upon selected language
	if (!isset($app_strings)) {
		$app_strings = return_application_language($current_language);
	}
	if (!isset($mod_strings) && isset($active_module)) {
		$mod_strings = return_module_language($current_language, $active_module);
	}
}

/**	Function used to get all the picklists and their values for a module
	@param string $module - Module name to which the list of picklists and their values needed
	@return array $fieldlists - Array of picklists and their values
**/
function getAccessPickListValues($module) {
	global $adb, $log, $current_user;
	$log->debug("Entering into function getAccessPickListValues($module)");

	$id = getTabid($module);
	$query = "select fieldname,columnname,fieldid,fieldlabel,tabid,uitype
		from vtiger_field
		where tabid = ? and uitype in ('15','33','55') and vtiger_field.presence in (0,2)";
	$result = $adb->pquery($query, array($id));

	$roleid = $current_user->roleid;
	$subrole = getRoleSubordinates($roleid);

	if (count($subrole)> 0) {
		$roleids = $subrole;
		$roleids[] = $roleid;
	} else {
		$roleids = $roleid;
	}

	$temp_status = array();
	for ($i=0; $i < $adb->num_rows($result); $i++) {
		$fieldname = $adb->query_result($result, $i, "fieldname");
		if ($fieldname == 'firstname') {
			continue;
		}
		$fieldlabel = $adb->query_result($result, $i, "fieldlabel");
		$columnname = $adb->query_result($result, $i, "columnname");
		$tabid = $adb->query_result($result, $i, "tabid");
		$uitype = $adb->query_result($result, $i, "uitype");

		$keyvalue = $columnname;
		$fieldvalues = array();
		if (count($roleids) > 1) {
			$mulsel="select distinct $fieldname,sortid
				from vtiger_$fieldname
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid
				where roleid in (\"". implode($roleids, "\",\"") ."\") and picklistid in (select picklistid from vtiger_picklist) order by sortid asc";
		} else {
			$mulsel="select distinct $fieldname,sortid
				from vtiger_$fieldname
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid
				where roleid ='".$roleid."' and picklistid in (select picklistid from vtiger_picklist) order by sortid asc";
		}
		$mulselresult = $adb->query($mulsel);
		for ($j=0; $j < $adb->num_rows($mulselresult); $j++) {
			$fieldvalues[] = $adb->query_result($mulselresult, $j, $fieldname);
		}
		$field_count = count($fieldvalues);
		if ($uitype == 15 && $field_count > 0 && ($fieldname == 'taskstatus' || $fieldname == 'eventstatus')) {
			$temp_count =count($temp_status[$keyvalue]);
			if ($temp_count > 0) {
				for ($t=0; $t < $field_count; $t++) {
					$temp_status[$keyvalue][($temp_count+$t)] = $fieldvalues[$t];
				}
				$fieldvalues = $temp_status[$keyvalue];
			} else {
				$temp_status[$keyvalue] = $fieldvalues;
			}
		}
		if ($uitype == 33) {
			$fieldlists[1][$keyvalue] = $fieldvalues;
		} elseif ($uitype == 55 && $fieldname == 'salutationtype') {
			$fieldlists[$keyvalue] = $fieldvalues;
		} elseif ($uitype == 15) {
			$fieldlists[$keyvalue] = $fieldvalues;
		}
	}
	$log->debug("Exit from function getAccessPickListValues($module)");

	return $fieldlists;
}

/** Search for value in the picklist and return if it is present or not
 * @param string $value - value to search in the picklist
 * @param string $picklist_name - picklist name where we will search
 * @return boolean
 **/
function isValueInPicklist($value, $picklist_name) {
	$picklistvalues = vtlib_getPicklistValues($picklist_name);
	return in_array($value, $picklistvalues);
}

/** Function to convert a given time string to Minutes */
function ConvertToMinutes($time_string) {
	if (empty($time_string)) {
		return 0;
	}
	$interval = explode(' ', $time_string);
	$interval_minutes = (int)$interval[0];
	$interval_string = strtolower($interval[1]);
	if ($interval_string == 'hour' || $interval_string == 'hours') {
		$interval_minutes = $interval_minutes * 60;
	} elseif ($interval_string == 'day' || $interval_string == 'days') {
		$interval_minutes = $interval_minutes * 1440;
	}
	return $interval_minutes;
}

//added to find duplicates
/** To get the converted record values which have to be display in duplicates merging tpl*/
function getRecordValues($id_array, $module) {
	global $adb,$current_user, $app_strings;
	$tabid=getTabid($module);
	$query="select fieldname,fieldlabel,uitype
		from vtiger_field
		where tabid=? and fieldname not in ('createdtime','modifiedtime') and vtiger_field.presence in (0,2) and uitype not in('4')";
	$result=$adb->pquery($query, array($tabid));
	$no_rows=$adb->num_rows($result);

	$focus = new $module();
	if (isset($id_array) && $id_array !='') {
		foreach ($id_array as $value_pair['disp_value']) {
			$focus->id=$value_pair['disp_value'];
			$focus->retrieve_entity_info($value_pair['disp_value'], $module);
			$field_values[]=$focus->column_fields;
		}
	}

	$value_pair = array();
	$c = 0;
	for ($i=0; $i<$no_rows; $i++) {
		$fld_name=$adb->query_result($result, $i, 'fieldname');
		$fld_label=$adb->query_result($result, $i, 'fieldlabel');
		$ui_type=$adb->query_result($result, $i, 'uitype');

		if (getFieldVisibilityPermission($module, $current_user->id, $fld_name, 'readwrite') == '0') {
			$fld_array []= $fld_name;
			$record_values[$c][$fld_label] = array();
			//$ui_value[]=$ui_type;
			for ($j=0, $jMax = count($field_values); $j < $jMax; $j++) {
				if ($ui_type ==56) {
					if ($field_values[$j][$fld_name] == 0) {
						$value_pair['disp_value']=$app_strings['no'];
					} else {
						$value_pair['disp_value']=$app_strings['yes'];
					}
				} elseif ($ui_type == 51 || $ui_type == 50) {
					$entity_id=$field_values[$j][$fld_name];
					if ($module !='Products') {
						$entity_name=getAccountName($entity_id);
					} else {
						$entity_name=getProductName($entity_id);
					}
					$value_pair['disp_value']=$entity_name;
				} elseif ($ui_type == 53) {
					$owner_id=$field_values[$j][$fld_name];
					$ownername=getOwnerName($owner_id);
					$value_pair['disp_value']=$ownername;
				} elseif ($ui_type ==57) {
					$contact_id= $field_values[$j][$fld_name];
					$contactname = '';
					if ($contact_id != '') {
						$displayValueArray = getEntityName('Contacts', $contact_id);
						if (!empty($displayValueArray)) {
							foreach ($displayValueArray as $field_value) {
								$contactname = $field_value;
							}
						}
					}
					$value_pair['disp_value']=$contactname;
				} elseif ($ui_type == 52) {
					$user_id = $field_values[$j][$fld_name];
					$user_name=getUserFullName($user_id);
					$value_pair['disp_value']=$user_name;
				} elseif ($ui_type == 10) {
					$value_pair['disp_value'] = getRecordInfoFromID($field_values[$j][$fld_name]);
				} elseif ($ui_type == 5 || $ui_type == 6 || $ui_type == 23) {
					if ($field_values[$j][$fld_name] != '' && $field_values[$j][$fld_name]
							!= '0000-00-00') {
						$date = new DateTimeField($field_values[$j][$fld_name]);
						$value_pair['disp_value'] = $date->getDisplayDate();
						if (strpos($field_values[$j][$fld_name], ' ') > -1) {
							$value_pair['disp_value'] .= (' ' . $date->getDisplayTime());
						}
					} elseif ($field_values[$j][$fld_name] == '0000-00-00') {
						$value_pair['disp_value'] = '';
					} else {
						$value_pair['disp_value'] = $field_values[$j][$fld_name];
					}
				} elseif ($ui_type == '71' || $ui_type == '72') {
					$currencyField = new CurrencyField($field_values[$j][$fld_name]);
					if ($ui_type == '72') {
						$value_pair['disp_value'] = $currencyField->getDisplayValue(null, true);
					} else {
						$value_pair['disp_value'] = $currencyField->getDisplayValue();
					}
				} else {
					$value_pair['disp_value']=$field_values[$j][$fld_name];
				}
				$value_pair['org_value'] = $field_values[$j][$fld_name];

				$record_values[$c][$fld_label][] = $value_pair;
			}
			$c++;
		}
	}
	$parent_array[0]=$record_values;
	$parent_array[1]=$fld_array;
	$parent_array[2]=$fld_array;
	return $parent_array;
}

/** Get SQL to find duplicates in a particular module */
function getDuplicateQuery($module, $field_values, $ui_type_arr) {
	$tbl_col_fld = explode(",", $field_values);
	$i=0;
	foreach ($tbl_col_fld as $val) {
		list($tbl[$i], $cols[$i], $fields[$i]) = explode(".", $val);
		$tbl_cols[$i] = $tbl[$i]. "." . $cols[$i];
		$i++;
	}
	$table_cols = implode(",", $tbl_cols);
	$sec_parameter = getSecParameterforMerge($module);

	if ($module == 'Contacts') {
		$nquery = "SELECT vtiger_contactdetails.contactid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_contactdetails
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid
				INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
				INNER JOIN vtiger_contactsubdetails ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
				LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
				LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contactdetails.accountid
				LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				INNER JOIN (SELECT $table_cols
						FROM vtiger_contactdetails
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
						INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
						INNER JOIN vtiger_contactsubdetails ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
						LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
						LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contactdetails.accountid
						LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 $sec_parameter
						GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
					ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
								WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_contactdetails.contactid ASC";
	} elseif ($module == 'Accounts') {
		$nquery="SELECT vtiger_account.accountid AS recordid,
			vtiger_users_last_import.deleted,".$table_cols."
			FROM vtiger_account
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_account.accountid
			INNER JOIN vtiger_accountbillads ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
			INNER JOIN vtiger_accountshipads ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
			LEFT JOIN vtiger_accountscf ON vtiger_account.accountid=vtiger_accountscf.accountid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_account.accountid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			INNER JOIN (SELECT $table_cols
				FROM vtiger_account
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
				INNER JOIN vtiger_accountbillads ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
				INNER JOIN vtiger_accountshipads ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
				LEFT JOIN vtiger_accountscf ON vtiger_account.accountid=vtiger_accountscf.accountid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted=0 $sec_parameter
				GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
			ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
							WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_account.accountid ASC";
	} elseif ($module == 'Leads') {
		$val_conv = ((isset($_COOKIE['LeadConv']) && $_COOKIE['LeadConv'] == 'true') ? 1 : 0);
		$nquery = "SELECT vtiger_leaddetails.leadid AS recordid, vtiger_users_last_import.deleted,$table_cols
				FROM vtiger_leaddetails
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadsubdetails ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
				INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid
				LEFT JOIN vtiger_leadscf ON vtiger_leadscf.leadid=vtiger_leaddetails.leadid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_leaddetails.leadid
				INNER JOIN (SELECT $table_cols
						FROM vtiger_leaddetails
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid
						INNER JOIN vtiger_leadsubdetails ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid
						INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid
						LEFT JOIN vtiger_leadscf ON vtiger_leadscf.leadid=vtiger_leaddetails.leadid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted = $val_conv $sec_parameter
						GROUP BY $table_cols HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
				WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted = $val_conv $sec_parameter ORDER BY $table_cols,vtiger_leaddetails.leadid ASC";
	} elseif ($module == 'Products') {
		$nquery = "SELECT vtiger_products.productid AS recordid,
			vtiger_users_last_import.deleted,".$table_cols."
			FROM vtiger_products
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_products.productid
			LEFT JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			INNER JOIN (SELECT $table_cols
						FROM vtiger_products
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
						LEFT JOIN vtiger_productcf ON vtiger_productcf.productid = vtiger_products.productid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 $sec_parameter
						GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
			ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
							WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_products.productid ASC";
	} elseif ($module == "HelpDesk") {
		$nquery = "SELECT vtiger_troubletickets.ticketid AS recordid,
			vtiger_users_last_import.deleted,".$table_cols."
			FROM vtiger_troubletickets
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_ticketcf ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_attachments ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
			LEFT JOIN vtiger_ticketcomments ON vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid
			INNER JOIN (SELECT $table_cols FROM vtiger_troubletickets
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
						LEFT JOIN vtiger_ticketcf ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
						LEFT JOIN vtiger_attachments ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
						LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
						LEFT JOIN vtiger_ticketcomments ON vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_contactdetails contd ON contd.contactid = vtiger_troubletickets.parent_id
			WHERE vtiger_crmentity.deleted=0 $sec_parameter
						GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
			ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
							WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_troubletickets.ticketid ASC";
	} elseif ($module == "Potentials") {
		$nquery = "SELECT vtiger_potential.potentialid AS recordid,
			vtiger_users_last_import.deleted,".$table_cols."
			FROM vtiger_potential
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_potential.potentialid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			INNER JOIN (SELECT $table_cols
						FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
						LEFT JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 $sec_parameter
						GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
			ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
							WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_potential.potentialid ASC";
	} elseif ($module == "Vendors") {
		$nquery = "SELECT vtiger_vendor.vendorid AS recordid,
			vtiger_users_last_import.deleted,".$table_cols."
			FROM vtiger_vendor
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid=vtiger_vendor.vendorid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_vendor.vendorid
			INNER JOIN (SELECT $table_cols
						FROM vtiger_vendor
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
						LEFT JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid=vtiger_vendor.vendorid
						WHERE vtiger_crmentity.deleted=0
						GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
			ON ".get_on_clause($field_values, $ui_type_arr, $module) ."
							WHERE vtiger_crmentity.deleted=0 ORDER BY $table_cols,vtiger_vendor.vendorid ASC";
	} else {
		$modObj = CRMEntity::getInstance($module);
		if ($modObj != null && method_exists($modObj, 'getDuplicatesQuery')) {
			$nquery = $modObj->getDuplicatesQuery($module, $table_cols, $field_values, $ui_type_arr);
		}
	}
	return $nquery;
}

/** Function to return the duplicate records data as a formatted array */
function getDuplicateRecordsArr($module, $use_limit = true) {
	global $adb,$app_strings,$theme,$default_charset;
	$list_max_entries_per_page = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $module);
	$field_values_array=getFieldValues($module);
	$field_values=$field_values_array['fieldnames_list'];
	$fld_arr=$field_values_array['fieldnames_array'];
	$col_arr=$field_values_array['columnnames_array'];
	$fld_labl_arr=$field_values_array['fieldlabels_array'];
	$ui_type=$field_values_array['fieldname_uitype'];

	$dup_query = getDuplicateQuery($module, $field_values, $ui_type);
	// added for page navigation
	$dup_count_query = substr($dup_query, stripos($dup_query, 'FROM'), strlen($dup_query));
	$dup_count_query = "SELECT count(*) as count ".$dup_count_query;
	$count_res = $adb->query($dup_count_query);
	$no_of_rows = $adb->query_result($count_res, 0, 'count');

	if ($no_of_rows <= $list_max_entries_per_page) {
		coreBOS_Session::set('dup_nav_start'.$module, 1);
	} elseif (isset($_REQUEST['start']) && $_REQUEST['start']!='' && (empty($_SESSION['dup_nav_start'.$module]) || $_SESSION['dup_nav_start'.$module]!=$_REQUEST['start'])) {
		coreBOS_Session::set('dup_nav_start'.$module, ListViewSession::getRequestStartPage());
	}
	$start = (!empty($_SESSION['dup_nav_start'.$module]) ? $_SESSION['dup_nav_start'.$module] : 1);
	$navigation_array = getNavigationValues($start, $no_of_rows, $list_max_entries_per_page);
	$start_rec = $navigation_array['start'];
	//$end_rec = $navigation_array['end_val'];
	$navigationOutput = getTableHeaderNavigation($navigation_array, "", $module, "FindDuplicate", "");
	if ($start_rec == 0) {
		$limit_start_rec = 0;
	} else {
		$limit_start_rec = $start_rec -1;
	}
	if ($use_limit) {
		$dup_query .= " LIMIT $limit_start_rec, $list_max_entries_per_page";
	}

	$nresult=$adb->query($dup_query);
	$no_rows=$adb->num_rows($nresult);
	if ($no_rows == 0) {
		if ($_REQUEST['action'] == 'FindDuplicateRecords') {
			echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
			echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
				<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tbody><tr>
				<td rowspan='2' width='11%'><img src='" . vtiger_imageurl('empty.jpg', $theme) . "' ></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'>
					<span class='genHeaderSmall'>".$app_strings['LBL_NO_DUPLICATE']."</span>
				</td>
				</tr>
				<tr>
				<td class='small' align='right' nowrap='nowrap'>
				<a href='javascript:window.history.back();'>".$app_strings['LBL_GO_BACK']."</a><br></td>
				</tr>
				</tbody></table>
				</div>
				</td></tr></table>";
			exit();
		} else {
			echo "<br><br><table align='center' class='reportCreateBottom big' width='95%'><tr><td align='center'>".$app_strings['LBL_NO_DUPLICATE']."</td></tr></table>";
			die;
		}
	}

	$rec_cnt = 0;
	$temp = array();
	$sl_arr = array();
	$grp = 'group0';
	$gcnt = 0;
	$ii = 0; //ii'th record in group
	while ($rec_cnt < $no_rows) {
		$result = $adb->fetchByAssoc($nresult);
		//echo '<pre>';print_r($result);echo '</pre>';
		if ($rec_cnt != 0) {
			$sl_arr = array_slice($result, 2);
			array_walk($temp, 'setFormatForDuplicateCompare');
			array_walk($sl_arr, 'setFormatForDuplicateCompare');
			$arr_diff = array_diff($temp, $sl_arr);
			if (count($arr_diff) > 0) {
				$gcnt++;
				$temp = $sl_arr;
				$ii = 0;
			}
			$grp = "group".$gcnt;
		}
		$fld_values[$grp][$ii]['recordid'] = $result['recordid'];
		for ($k=0, $kMax = count($col_arr); $k< $kMax; $k++) {
			if ($rec_cnt == 0) {
				$temp[$fld_labl_arr[$k]] = html_entity_decode($result[$col_arr[$k]], ENT_QUOTES, $default_charset);
			}
			if ($ui_type[$fld_arr[$k]] == 56) {
				if ($result[$col_arr[$k]] == 0) {
					$result[$col_arr[$k]]=$app_strings['no'];
				} else {
					$result[$col_arr[$k]]=$app_strings['yes'];
				}
			}
			if ($ui_type[$fld_arr[$k]] ==57) {
				$contact_id= $result[$col_arr[$k]];
				if ($contact_id != '') {
					$parent_module = 'Contacts';
					$displayValueArray = getEntityName($parent_module, $contact_id);
					if (!empty($displayValueArray)) {
						foreach ($displayValueArray as $field_value) {
							$contactname = $field_value;
						}
					}
				}

				$result[$col_arr[$k]]=$contactname;
			}
			if ($ui_type[$fld_arr[$k]] == 15 || $ui_type[$fld_arr[$k]] == 16) {
				$result[$col_arr[$k]]=getTranslatedString($result[$col_arr[$k]], $module);
			}
			if ($ui_type[$fld_arr[$k]] == 33) {
				$fieldvalue = explode(' |##| ', $result[$col_arr[$k]]);
				$result[$col_arr[$k]] = array();
				foreach ($fieldvalue as $picklistValue) {
					$result[$col_arr[$k]][] = getTranslatedString($picklistValue, $module);
				}
				$result[$col_arr[$k]] = implode(', ', $result[$col_arr[$k]]);
			}
			if ($ui_type[$fld_arr[$k]] ==53 || $ui_type[$fld_arr[$k]] ==52) {
				if ($result[$col_arr[$k]] != '') {
					$owner=getOwnerName($result[$col_arr[$k]]);
				}
				$result[$col_arr[$k]]=$owner;
			}
			if ($ui_type[$fld_arr[$k]] ==50 || $ui_type[$fld_arr[$k]] ==51) {
				if ($module!='Products') {
					$entity_name=getAccountName($result[$col_arr[$k]]);
				} else {
					$entity_name=getProductName($result[$col_arr[$k]]);
				}
				if ($entity_name != '') {
					$result[$col_arr[$k]]=$entity_name;
				} else {
					$result[$col_arr[$k]]='';
				}
			}
			/*uitype 10 handling*/
			if ($ui_type[$fld_arr[$k]] == 10) {
				$result[$col_arr[$k]] = getRecordInfoFromID($result[$col_arr[$k]]);
			}
			if ($ui_type[$fld_arr[$k]] == 5 || $ui_type[$fld_arr[$k]] == 6 || $ui_type[$fld_arr[$k]] == 23) {
				if ($result[$col_arr[$k]] != '' && $result[$col_arr[$k]] != '0000-00-00') {
					$date = new DateTimeField($result[$col_arr[$k]]);
					$value = $date->getDisplayDate();
					if (strpos($result[$col_arr[$k]], ' ') > -1) {
						$value .= (' ' . $date->getDisplayTime());
					}
				} elseif ($result[$col_arr[$k]] == '0000-00-00') {
					$value = '';
				} else {
					$value = $result[$col_arr[$k]];
				}
				$result[$col_arr[$k]] = $value;
			}
			if ($ui_type[$fld_arr[$k]] == 71) {
				$result[$col_arr[$k]] = CurrencyField::convertToUserFormat($result[$col_arr[$k]]);
			}
			if ($ui_type[$fld_arr[$k]] == 72) {
				$result[$col_arr[$k]] = CurrencyField::convertToUserFormat($result[$col_arr[$k]], null, true);
			}

			$fld_values[$grp][$ii][$fld_labl_arr[$k]] = $result[$col_arr[$k]];
		}
		$fld_values[$grp][$ii]['Entity Type'] = $result['deleted'];
		$ii++;
		$rec_cnt++;
	}

	$gro='group';
	for ($i=0; $i<$no_rows; $i++) {
		if (empty($fld_values[$gro.$i])) {
			continue;
		}
		$dis_group[]=$fld_values[$gro.$i][0];
		//$count_group[$i]=count($fld_values[$gro.$i]);
		$new_group[]=$dis_group[$i];
	}
	$fld_nam=$new_group[0];
	$ret_arr[0]=$fld_values;
	$ret_arr[1]=$fld_nam;
	$ret_arr[2]=$ui_type;
	$ret_arr['navigation']=$navigationOutput;
	return $ret_arr;
}

/** Function to Delete Exact Duplicates */
function deleteExactDuplicates($dup_records, $module) {
	$dup_records_ids=array();
	$delete_fail_status=false;
	foreach ($dup_records as $records_group) {
		$record_position=0;
		foreach ($records_group as $records) {
			if ($record_position!=0) {
				array_push($dup_records_ids, $records['recordid']);
			}
			$record_position++;
		}
	}
	$focus = CRMEntity::getInstance($module);
	foreach ($dup_records_ids as $id) {
		if (isPermitted($module, 'Delete', $id) == 'yes') {
			$del_response=DeleteEntity($module, $module, $focus, $id, "");
			if ($del_response[0]) {
				$delete_fail_status = true;
			}
		} else {
			$delete_fail_status = true;
		}
	}
	return $delete_fail_status;
}

/** Function to get on clause criteria for sub tables like address tables to construct duplicate check query */
function get_special_on_clause($field_list) {
	$field_array = explode(",", $field_list);
	$ret_str = '';
	$sel_clause = '';
	$i=1;
	$cnt = count($field_array);
	$spl_chk = ($_REQUEST['modulename'] != '')?$_REQUEST['modulename']:$_REQUEST['module'];
	foreach ($field_array as $fld) {
		$sub_arr = explode(".", $fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];
		//$fld_name = $sub_arr[2];

		//need to handle aditional conditions with sub tables for further modules of duplicate check
		if ($tbl_name == 'vtiger_leadsubdetails' || $tbl_name == 'vtiger_contactsubdetails') {
			$tbl_alias = "subd";
		} elseif ($tbl_name == 'vtiger_leadaddress' || $tbl_name == 'vtiger_contactaddress') {
			$tbl_alias = "addr";
		} elseif ($tbl_name == 'vtiger_account' && $spl_chk == 'Contacts') {
			$tbl_alias = "acc";
		} elseif ($tbl_name == 'vtiger_accountbillads') {
			$tbl_alias = "badd";
		} elseif ($tbl_name == 'vtiger_accountshipads') {
			$tbl_alias = "sadd";
		} elseif ($tbl_name == 'vtiger_crmentity') {
			$tbl_alias = "crm";
		} elseif ($tbl_name == 'vtiger_customerdetails') {
			$tbl_alias = "custd";
		} elseif ($tbl_name == 'vtiger_contactdetails' && spl_chk == 'HelpDesk') {
			$tbl_alias = "contd";
		} elseif (stripos($tbl_name, 'cf') === (strlen($tbl_name) - strlen('cf'))) {
			$tbl_alias = "tcf"; // Custom Field Table Prefix to use in subqueries
		} else {
			$tbl_alias = "t";
		}

		$sel_clause .= $tbl_alias.".".$col_name.",";
		$ret_str .= " $tbl_name.$col_name = $tbl_alias.$col_name";
		if ($cnt != $i) {
			$ret_str .= " and ";
		}
		$i++;
	}
	$ret_arr['on_clause'] = $ret_str;
	$ret_arr['sel_clause'] = trim($sel_clause, ",");
	return $ret_arr;
}

/** Function to get on clause criteria for duplicate check queries */
function get_on_clause($field_list, $uitype_arr, $module) {
	$field_array = explode(",", $field_list);
	$ret_str = '';
	$i=1;
	foreach ($field_array as $fld) {
		$sub_arr = explode(".", $fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];
		//$fld_name = $sub_arr[2];

		$ret_str .= " ifnull($tbl_name.$col_name,'null') = ifnull(temp.$col_name,'null')";

		if (count($field_array) != $i) {
			$ret_str .= " and ";
		}
		$i++;
	}
	return $ret_str;
}

function elimina_acentos($cadena) {
	$tofind = utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊẼËèéêẽëÌÍĨÎÏìíîĩïÙÚÛŨÜúùûũüÿçÇºªñÑ");
	$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEEeeeeeIIIIIiiiiiUUUUUuuuuuycCoanN";
	return utf8_encode(strtr(utf8_decode($cadena), $tofind, $replac));
}

/** call back function to change the array values in to lower case */
function setFormatForDuplicateCompare(&$string) {
	global $default_charset;
	$string = html_entity_decode($string, ENT_QUOTES, $default_charset);
	$string = elimina_acentos(trim($string));
	$string = strtolower($string);
}

/** Function to get recordids for subquery where condition */
function get_subquery_recordids($sub_query) {
	global $adb;
	//need to update this module whenever duplicate check tool added for new modules
	$module_id_array = array(
		'Accounts' => 'accountid',
		'Contacts' => 'contactid',
		'Leads' => 'leadid',
		'Products' => 'productid',
		'HelpDesk' => 'ticketid',
		'Potentials' => 'potentialid',
		'Vendors' => 'vendorid',
	);
	$id = ($module_id_array[$_REQUEST['modulename']] != '')?$module_id_array[$_REQUEST['modulename']]:$module_id_array[$_REQUEST['module']];
	$sub_res = '';
	$sub_result = $adb->query($sub_query);
	$row_count = $adb->num_rows($sub_result);
	$sub_res = '';
	if ($row_count > 0) {
		while ($rows = $adb->fetchByAssoc($sub_result)) {
			$sub_res .= $rows[$id].",";
		}
		$sub_res = trim($sub_res, ",");
	} else {
		$sub_res .= "''";
	}
	return $sub_res;
}

/** Function to get tablename, columnname, fieldname, fieldlabel and uitypes of fields of merge criteria for a particular module*/
function getFieldValues($module) {
	global $adb,$current_user;

	//In future if we want to change a id mapping to name or other string then we can add that elements in this array.
	//$fld_table_arr = Array("vtiger_contactdetails.account_id"=>"vtiger_account.accountname");
	//$special_fld_arr = Array("account_id"=>"accountname");

	$fld_table_arr = array();
	$special_fld_arr = array();
	$tabid = getTabid($module);

	$fieldname_query="select fieldname,fieldlabel,uitype,tablename,columnname from vtiger_field where fieldid in
			(select fieldid from vtiger_user2mergefields WHERE tabid=? AND userid=? AND visible = ?) and vtiger_field.presence in (0,2)";
	$fieldname_result = $adb->pquery($fieldname_query, array($tabid, $current_user->id, 1));

	$field_num_rows = $adb->num_rows($fieldname_result);

	$fld_arr = array();
	$col_arr = array();
	for ($j=0; $j< $field_num_rows; $j ++) {
		$tablename = $adb->query_result($fieldname_result, $j, 'tablename');
		$column_name = $adb->query_result($fieldname_result, $j, 'columnname');
		$field_name = $adb->query_result($fieldname_result, $j, 'fieldname');
		$field_lbl = $adb->query_result($fieldname_result, $j, 'fieldlabel');
		$ui_type = $adb->query_result($fieldname_result, $j, 'uitype');
		$table_col = $tablename.".".$column_name;
		if (getFieldVisibilityPermission($module, $current_user->id, $field_name) == 0) {
			$fld_name = (!empty($special_fld_arr[$field_name]))?$special_fld_arr[$field_name]:$field_name;

			$fld_arr[] = $fld_name;
			$col_arr[] = $column_name;
			if (!empty($fld_table_arr[$table_col])) {
				$table_col = $fld_table_arr[$table_col];
			}

			$field_values_array['fieldnames_list'][] = $table_col . "." . $fld_name;
			$fld_labl_arr[]=$field_lbl;
			$uitype[$field_name]=$ui_type;
		}
	}
	$field_values_array['fieldnames_list']=implode(",", $field_values_array['fieldnames_list']);
	$field_values=implode(",", $fld_arr);
	$field_values_array['fieldnames']=$field_values;
	$field_values_array["fieldnames_array"]=$fld_arr;
	$field_values_array["columnnames_array"]=$col_arr;
	$field_values_array['fieldlabels_array']=$fld_labl_arr;
	$field_values_array['fieldname_uitype']=$uitype;

	return $field_values_array;
}

/** To get security parameter for a particular module */
function getSecParameterforMerge($module) {
	global $current_user;
	$tab_id = getTabid($module);
	$sec_parameter="";
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3) {
		$sec_parameter=getListViewSecurityParameter($module);
		if ($module == "Accounts") {
			$sec_parameter .= " AND (vtiger_crmentity.smownerid IN (".$current_user->id.")
					OR vtiger_crmentity.smownerid IN (
					SELECT vtiger_user2role.userid
					FROM vtiger_user2role
					INNER JOIN vtiger_users ON vtiger_users.id = vtiger_user2role.userid
					INNER JOIN vtiger_role ON vtiger_role.roleid = vtiger_user2role.roleid
					WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%')
					OR vtiger_crmentity.smownerid IN (
					SELECT shareduserid
					FROM vtiger_tmp_read_user_sharing_per
					WHERE userid=".$current_user->id."
					AND tabid=".$tab_id.")
					OR (vtiger_crmentity.smownerid in (0)
					AND (";

			if (count($current_user_groups) > 0) {
				$sec_parameter .= " vtiger_groups.groupname IN (
								SELECT groupname
								FROM vtiger_groups
								WHERE groupid IN (". implode(",", getCurrentUserGroupList()) .")) OR ";
			}
			$sec_parameter .= " vtiger_groups.groupname IN (
				SELECT vtiger_groups.groupname
				FROM vtiger_tmp_read_group_sharing_per
				INNER JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_tmp_read_group_sharing_per.sharedgroupid
				WHERE userid=".$current_user->id." AND tabid=".$tab_id.")))) ";
		}
	}
	return $sec_parameter;
}

// Update all the data refering to currency $old_cur to $new_cur
function transferCurrency($old_cur, $new_cur) {

	// Transfer User currency to new currency
	transferUserCurrency($old_cur, $new_cur);

	// Transfer Product Currency to new currency
	transferProductCurrency($old_cur, $new_cur);

	// Transfer PriceBook Currency to new currency
	transferPriceBookCurrency($old_cur, $new_cur);
}

// Function to transfer the users with currency $old_cur to $new_cur as currency
function transferUserCurrency($old_cur, $new_cur) {
	global $log, $adb, $current_user;
	$log->debug('Entering function transferUserCurrency...');
	$adb->pquery('update vtiger_users set currency_id=? where currency_id=?', array($new_cur, $old_cur));
	$current_user->retrieve_entity_info($current_user->id, 'Users');
	$log->debug('Exiting function transferUserCurrency...');
}

// Function to transfer the products with currency $old_cur to $new_cur as currency
function transferProductCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug('Entering function updateProductCurrency...');
	$prod_res = $adb->pquery('select productid from vtiger_products where currency_id = ?', array($old_cur));
	$numRows = $adb->num_rows($prod_res);
	$prod_ids = array();
	for ($i=0; $i<$numRows; $i++) {
		$prod_ids[] = $adb->query_result($prod_res, $i, 'productid');
	}
	if (count($prod_ids) > 0) {
		$prod_price_list = getPricesForProducts($new_cur, $prod_ids);
		$query = 'update vtiger_products set currency_id=?, unit_price=? where productid=?';
		foreach ($prod_ids as $product_id) {
			$adb->pquery($query, array($new_cur, $prod_price_list[$product_id], $product_id));
		}
	}
	$log->debug('Exiting function updateProductCurrency...');
}

// Function to transfer the pricebooks with currency $old_cur to $new_cur as currency
// and to update the associated products with list price in $new_cur currency
function transferPriceBookCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug('Entering function updatePriceBookCurrency...');
	$pb_res = $adb->pquery("select pricebookid from vtiger_pricebook where currency_id = ?", array($old_cur));
	$numRows = $adb->num_rows($pb_res);
	$pb_ids = array();
	for ($i=0; $i<$numRows; $i++) {
		$pb_ids[] = $adb->query_result($pb_res, $i, 'pricebookid');
	}

	if (count($pb_ids) > 0) {
		require_once 'modules/PriceBooks/PriceBooks.php';
		foreach ($pb_ids as $pb_id) {
			$focus = new PriceBooks();
			$focus->id = $pb_id;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($pb_id, 'PriceBooks');
			$focus->column_fields['currency_id'] = $new_cur;
			$focus->save('PriceBooks');
		}
	}

	$log->debug('Exiting function updatePriceBookCurrency...');
}

//functions for asterisk integration start
/**
 * this function returns the caller name based on the phone number that is passed to it
 * @param $from - the number which is calling
 * returns caller information in name(type) format :: for e.g. Mary Smith (Contact)
 * if no information is present in database, it returns :: Unknown Caller (Unknown)
 */
function getCallerName($from) {
	//information found
	$callerInfo = getCallerInfo($from);

	if ($callerInfo != false) {
		$callerName = decode_html($callerInfo['name']);
		$module = $callerInfo['module'];
		$callerModule = " (<a href='index.php?module=$module&action=index'>$module</a>)";
		$callerID = $callerInfo['id'];

		$caller =$caller."<a href='index.php?module=$module&action=DetailView&record=$callerID'>$callerName</a>$callerModule";
	} else {
		$caller = $caller."<br>
			<a target='_blank' href='index.php?module=Leads&action=EditView&phone=$from'>".getTranslatedString('LBL_CREATE_LEAD')."</a><br>
			<a target='_blank' href='index.php?module=Contacts&phone=$from'>".getTranslatedString('LBL_CREATE_CONTACT')."</a><br>
			<a target='_blank' href='index.php?module=Accounts&action=EditView&phone=$from'>".getTranslatedString('LBL_CREATE_ACCOUNT')."</a>";
	}
	return $caller;
}

/**
 * this function searches for a given number and returns the callerInfo in an array format
 * currently the search is made across only leads, accounts and contacts modules
 *
 * @param $number - the number whose information you want
 * @return array in format array(name=>callername, module=>module, id=>id);
 */
function getCallerInfo($number) {
	global $adb;
	if (empty($number)) {
		return false;
	}

	$fieldsString = GlobalVariable::getVariable('PBXManager_SearchOnlyOnTheseFields', '');
	if ($fieldsString != '') {
		$fieldsArray = explode(',', $fieldsString);
		foreach ($fieldsArray as $field) {
			$result = $adb->pquery("SELECT tabid, uitype FROM vtiger_field WHERE columnname = ?", array($field));
			for ($i = 0; $i< $adb->num_rows($result); $i++) {
				$module = vtlib_getModuleNameById($adb->query_result($result, $i, 0));
				$uitype = $adb->query_result($result, $i, 1);
				$focus = CRMEntity::getInstance($module);
				$query = $focus->buildSearchQueryForFieldTypes($uitype, $number);
				if (empty($query)) {
					continue;
				}

				$result = $adb->pquery($query, array());
				if ($adb->num_rows($result) > 0) {
					$callerName = $adb->query_result($result, 0, 'name');
					$callerID = $adb->query_result($result, 0, 'id');
					return array('name'=>$callerName, 'module'=>$module, 'id'=>$callerID);
				}
			}
		}
	} else {
		$name = array('Contacts', 'Accounts', 'Leads');
		foreach ($name as $module) {
			$focus = CRMEntity::getInstance($module);
			$query = $focus->buildSearchQueryForFieldTypes(11, $number);
			if (empty($query)) {
				return false;
			}

			$result = $adb->pquery($query, array());
			if ($adb->num_rows($result) > 0) {
				$callerName = $adb->query_result($result, 0, 'name');
				$callerID = $adb->query_result($result, 0, 'id');
				return array('name'=>$callerName, 'module'=>$module, 'id'=>$callerID);
			}
		}
	}

	return false;
}

/**
 * this function returns the tablename and primarykeys for a given module in array format
 * @param object $adb - peardatabase type object
 * @param string $module - module name for which you want the array
 * @return array(tablename1=>primarykey1,.....)
 */
function get_tab_name_index($adb, $module) {
	$tabid = getTabid($module);
	$sql = "select * from vtiger_tab_name_index where tabid = ?";
	$result = $adb->pquery($sql, array($tabid));
	$count = $adb->num_rows($result);
	$data = array();

	for ($i=0; $i<$count; $i++) {
		$tablename = $adb->query_result($result, $i, "tablename");
		$primaryKey = $adb->query_result($result, $i, "primarykey");
		$data[$tablename] = $primaryKey;
	}
	return $data;
}

/**
 * this function returns the value of use_asterisk from the database for the current user
 * @param string $id - the id of the current user
 */
function get_use_asterisk($id) {
	global $adb;
	if (!vtlib_isModuleActive('PBXManager') || isPermitted('PBXManager', 'index') == 'no') {
		return false;
	}
	$sql = "select * from vtiger_asteriskextensions where userid = ?";
	$result = $adb->pquery($sql, array($id));
	if ($adb->num_rows($result)>0) {
		$use_asterisk = $adb->query_result($result, 0, "use_asterisk");
		$asterisk_extension = $adb->query_result($result, 0, "asterisk_extension");
		if ($use_asterisk == 0 || empty($asterisk_extension)) {
			return 'false';
		} else {
			return 'true';
		}
	} else {
		return 'false';
	}
}

/**
 * this function adds a record to the callhistory module
 *
 * @param string $userExtension - the extension of the current user
 * @param string $callfrom - the caller number
 * @param string $callto - the called number
 * @param string $status - the status of the call (outgoing/incoming/missed)
 * @param object $adb - the peardatabase object
 */
function addToCallHistory($userExtension, $callfrom, $callto, $status, $adb, $useCallerInfo) {
	$sql = "select * from vtiger_asteriskextensions where asterisk_extension=?";
	$result = $adb->pquery($sql, array($userExtension));
	$userID = $adb->query_result($result, 0, "userid");
	if (empty($userID)) {
		// call to extension not configured in application > return NULL
		return;
	}
	$crmID = $adb->getUniqueID('vtiger_crmentity');
	$timeOfCall = date('Y-m-d H:i:s');

	$sql = "insert into vtiger_crmentity values (?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$params = array($crmID, $userID, $userID, 0, "PBXManager", "", $timeOfCall, $timeOfCall, null, null, 0, 1, 0);
	$adb->pquery($sql, $params);
	$unknownCaller = GlobalVariable::getVariable('PBX_Unknown_CallerID', 'Unknown', 'PBXManager');
	if (empty($callfrom)) {
		$callfrom = $unknownCaller;
	}
	if (empty($callto)) {
		$callto = $unknownCaller;
	}

	if ($status == 'outgoing') {
		//call is from user to record
		$sql = "select * from vtiger_asteriskextensions where asterisk_extension=?";
		$result = $adb->pquery($sql, array($callfrom));
		if ($adb->num_rows($result)>0) {
			$userid = $adb->query_result($result, 0, "userid");
			$callerName = getUserFullName($userid);
		}

		$receiver = $useCallerInfo;
		if (empty($receiver)) {
			$receiver = $unknownCaller;
		} else {
			$receiver = "<a href='index.php?module=".$receiver['module']."&action=DetailView&record=".$receiver['id']."'>".$receiver['name']."</a>";
		}
	} else {
		//call is from record to user
		$sql = "select * from vtiger_asteriskextensions where asterisk_extension=?";
		$result = $adb->pquery($sql, array($callto));
		if ($adb->num_rows($result)>0) {
			$userid = $adb->query_result($result, 0, "userid");
			$receiver = getUserFullName($userid);
		}
		$callerName = $useCallerInfo;
		if (empty($callerName)) {
			$callerName = $unknownCaller.' '.$callfrom;
		} else {
			$callerName = "<a href='index.php?module=".$callerName['module']."&action=DetailView&record=".$callerName['id']."'>".decode_html($callerName['name'])."</a>";
		}
	}

	$sql = "insert into vtiger_pbxmanager (pbxmanagerid,callfrom,callto,timeofcall,status)values (?,?,?,?,?)";
	$params = array($crmID, $callerName, $receiver, $timeOfCall, $status);
	$adb->pquery($sql, $params);
	return $crmID;
}
//functions for asterisk integration end

//functions for settings page
/**
 * this function returns the blocks for the settings page
 */
function getSettingsBlocks() {
	global $adb;
	$sql = "select blockid, label from vtiger_settings_blocks order by sequence";
	$result = $adb->query($sql);
	$count = $adb->num_rows($result);
	$blocks = array();

	if ($count>0) {
		for ($i=0; $i<$count; $i++) {
			$blockid = $adb->query_result($result, $i, "blockid");
			$label = $adb->query_result($result, $i, "label");
			$blocks[$blockid] = $label;
		}
	}
	return $blocks;
}

/**
 * this function returns the fields for the settings page
 */
function getSettingsFields() {
	global $adb;
	$sql = 'select * from vtiger_settings_field where blockid!=? and active=0 order by blockid,sequence';
	$result = $adb->pquery($sql, array(getSettingsBlockId('LBL_MODULE_MANAGER')));
	$count = $adb->num_rows($result);
	$fields = array();

	if ($count>0) {
		for ($i=0; $i<$count; $i++) {
			$blockid = $adb->query_result($result, $i, 'blockid');
			$iconpath = $adb->query_result($result, $i, 'iconpath');
			$description = $adb->query_result($result, $i, 'description');
			$linkto = $adb->query_result($result, $i, 'linkto');
			$action = getPropertiesFromURL($linkto, 'action');
			$module = getPropertiesFromURL($linkto, 'module');
			$name = $adb->query_result($result, $i, 'name');

			$fields[$blockid][] = array('icon'=>$iconpath, 'description'=>$description, 'link'=>$linkto, 'name'=>$name, 'action'=>$action, 'module'=>$module);
		}

		//add blanks for 4-column layout
		foreach ($fields as $blockid => &$field) {
			if (count($field)>0 && count($field)<4) {
				for ($i=count($field); $i<4; $i++) {
					$field[$i] = array('icon'=>'', 'description'=>'', 'link'=>'', 'name'=>'', 'action'=>'', 'module'=>'');
				}
			}
		}
	}
	return $fields;
}

/**
 * this function takes an url and returns the module name from it
 */
function getPropertiesFromURL($url, $action) {
	$result = array();
	preg_match("/$action=([^&]+)/", $url, $result);
	return $result[1];
}

//functions for settings page end

/* Function to get the name of the Field which is used for Module Specific Sequence Numbering, if any
 * @param module String - Module label
 * return Array - Field name and label are returned */
function getModuleSequenceField($module) {
	global $adb, $log;
	$log->debug("Entering function getModuleSequenceFieldName ($module)...");
	$field = null;

	if (!empty($module)) {
		// First look at the cached information
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

		if ($cachedModuleFields === false) {
			//uitype 4 points to Module Numbering Field
			$seqColRes = $adb->pquery(
				'SELECT fieldname, fieldlabel, columnname FROM vtiger_field WHERE uitype=? AND tabid=? and vtiger_field.presence in (0,2)',
				array('4', getTabid($module))
			);
			if ($adb->num_rows($seqColRes) > 0) {
				$fieldname = $adb->query_result($seqColRes, 0, 'fieldname');
				$columnname = $adb->query_result($seqColRes, 0, 'columnname');
				$fieldlabel = $adb->query_result($seqColRes, 0, 'fieldlabel');

				$field = array();
				$field['name'] = $fieldname;
				$field['column'] = $columnname;
				$field['label'] = $fieldlabel;
			}
		} else {
			foreach ($cachedModuleFields as $fieldinfo) {
				if ($fieldinfo['uitype'] == '4') {
					$field = array();

					$field['name'] = $fieldinfo['fieldname'];
					$field['column'] = $fieldinfo['columnname'];
					$field['label'] = $fieldinfo['fieldlabel'];

					break;
				}
			}
		}
	}

	$log->debug("Exiting getModuleSequenceFieldName...");
	return $field;
}

/* Function to get the Result of all the field ids allowed for Duplicates merging for specified tab/module (tabid) */
function getFieldsResultForMerge($tabid) {
	global $log, $adb;
	$log->debug("Entering getFieldsResultForMerge(".$tabid.") method ...");

	$nonmergable_tabids = array(29);

	if (in_array($tabid, $nonmergable_tabids)) {
		return null;
	}

	// List of Fields not allowed for Duplicates Merging based on the module (tabid) [tabid to fields mapping]
	$nonmergable_field_tab = array(
		4 => array('portal','imagename'),
		13 => array('update_log','filename','comments'),
	);

	$nonmergable_displaytypes = array(4);
	$nonmergable_uitypes = array('70','69','4');

	$sql = 'SELECT fieldid,typeofdata FROM vtiger_field WHERE tabid = ? and vtiger_field.presence in (0,2) AND block IS NOT NULL';
	$params = array($tabid);

	$where = '';

	if (isset($nonmergable_field_tab[$tabid]) && count($nonmergable_field_tab[$tabid]) > 0) {
		$where .= " AND fieldname NOT IN (". generateQuestionMarks($nonmergable_field_tab[$tabid]) .")";
		$params[] = $nonmergable_field_tab[$tabid];
	}

	if (count($nonmergable_displaytypes) > 0) {
		$where .= " AND displaytype NOT IN (". generateQuestionMarks($nonmergable_displaytypes) .")";
		$params[] = $nonmergable_displaytypes;
	}
	if (count($nonmergable_uitypes) > 0) {
		$where .= " AND uitype NOT IN ( ". generateQuestionMarks($nonmergable_uitypes) .")" ;
		$params[] = $nonmergable_uitypes;
	}

	if (trim($where) != '') {
		$sql .= $where;
	}

	$res = $adb->pquery($sql, $params);
	$log->debug("Exiting getFieldsResultForMerge method ...");
	return $res;
}

/* Function to get the related tables data
 * @param - $module - Primary module name
 * @param - $secmodule - Secondary module name
 * return Array $rel_array tables and fields to be compared are sent
 * */
function getRelationTables($module, $secmodule) {
	global $adb;
	$primary_obj = CRMEntity::getInstance($module);
	$secondary_obj = CRMEntity::getInstance($secmodule);

	if (method_exists($primary_obj, 'setRelationTables')) {
		$reltables = $primary_obj->setRelationTables($secmodule);
	}
	if (empty($reltables)) { // not predefined so we try uitype10
		$ui10_query = $adb->pquery(
			'SELECT vtiger_field.tabid AS tabid,vtiger_field.tablename AS tablename, vtiger_field.columnname AS columnname
				FROM vtiger_field
				INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid
				WHERE (vtiger_fieldmodulerel.module=? AND vtiger_fieldmodulerel.relmodule=?) OR (vtiger_fieldmodulerel.module=? AND vtiger_fieldmodulerel.relmodule=?)',
			array($module, $secmodule, $secmodule, $module)
		);
		if ($adb->num_rows($ui10_query)>0) {
			$ui10_tablename = $adb->query_result($ui10_query, 0, 'tablename');
			$ui10_columnname = $adb->query_result($ui10_query, 0, 'columnname');
			$ui10_tabid = $adb->query_result($ui10_query, 0, 'tabid');
			if ($primary_obj->table_name == $ui10_tablename) {
				$reltables = array($ui10_tablename=>array("".$primary_obj->table_index."","$ui10_columnname"));
			} elseif ($secondary_obj->table_name == $ui10_tablename) {
				$reltables = array(
					$ui10_tablename => array($ui10_columnname, $secondary_obj->table_index),
					$primary_obj->table_name => $primary_obj->table_index
				);
			} else {
				if (isset($secondary_obj->tab_name_index[$ui10_tablename])) {
					$rel_field = $secondary_obj->tab_name_index[$ui10_tablename];
					$reltables = array($ui10_tablename=>array($ui10_columnname, $rel_field), $primary_obj->table_name => $primary_obj->table_index);
				} else {
					$rel_field = $primary_obj->tab_name_index[$ui10_tablename];
					//$reltables = array($ui10_tablename=>array("$rel_field","$ui10_columnname"),"".$primary_obj->table_name."" => "".$primary_obj->table_index."");
					$reltables = array($ui10_tablename => array($rel_field, $ui10_columnname));
				}
			}
		} else {
			$reltables = '';
		}
	}
	if (is_array($reltables) && !empty($reltables)) {
		$rel_array = $reltables;
	} else {
		$rel_array = array("vtiger_crmentityrel"=>array("crmid","relcrmid"),"".$primary_obj->table_name."" => "".$primary_obj->table_index."");
	}
	return $rel_array;
}

/**
 * This function returns no value but handles the delete functionality of each entity.
 * Input Parameter are $module - module name, $return_module - return module name, $focus - module object, $record - entity id, $return_id - return entity id.
 */
function DeleteEntity($module, $return_module, $focus, $record, $return_id) {
	global $log;
	$log->debug("Entering DeleteEntity method ($module, $return_module, $record, $return_id)");
	if (!empty($record)) {
		$setype = getSalesEntityType($record);
		if ($setype != $module && !($module == 'cbCalendar' && $setype == 'Emails')) {
			return array(true,getTranslatedString('LBL_PERMISSION'));
		}
		if ($module != $return_module && !empty($return_module) && !empty($return_id)) {
			$focus->unlinkRelationship($record, $return_module, $return_id);
			$focus->trackUnLinkedInfo($return_module, $return_id, $module, $record);
			$log->debug('Exiting DeleteEntity method ...');
		} else {
			list($delerror,$errormessage) = $focus->preDeleteCheck();
			if (!$delerror) {
				$focus->trash($module, $record);
			}
			$log->debug('Exiting DeleteEntity method ...');
			return array($delerror,$errormessage);
		}
	}
}

/**
 * Function to related two records of different entity types
 */
function relateEntities($focus, $sourceModule, $sourceRecordId, $destinationModule, $destinationRecordIds) {
	$destinationRecordIds = (array)$destinationRecordIds;
	$data = array();
	$data['focus'] = $focus;
	$data['sourceModule'] = $sourceModule;
	$data['sourceRecordId'] = $sourceRecordId;
	$data['destinationModule'] = $destinationModule;
	foreach ($destinationRecordIds as $destinationRecordId) {
		$data['destinationRecordId'] = $destinationRecordId;
		cbEventHandler::do_action('corebos.entity.link.before', $data);
		$focus->save_related_module($sourceModule, $sourceRecordId, $destinationModule, $destinationRecordId);
		$focus->trackLinkedInfo($sourceModule, $sourceRecordId, $destinationModule, $destinationRecordId);
		cbEventHandler::do_action('corebos.entity.link.after', $data);
	}
}

/**
 * this function checks if a given column exists in a given table or not
 * @param string $columnName - the columnname
 * @param string $tableName - the tablename
 * @return boolean $status - true if column exists; false otherwise
 */
function columnExists($columnName, $tableName) {
	global $adb;
	$columnNames = $adb->getColumnNames($tableName);
	return in_array($columnName, $columnNames);
}

/**
 * this function accepts a potential id returns the module name and entity value for the related field
 * @param integer $id - the potential id
 * @return array $data - the related module name and field value
 */
function getRelatedInfo($id) {
	global $adb;
	$data = array();
	$result = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($id));
	if ($adb->num_rows($result)>0) {
		$relID = $adb->query_result($result, 0, "related_to");
		$result = $adb->pquery('select setype from vtiger_crmentity where crmid=?', array($relID));
		if ($adb->num_rows($result)>0) {
			$setype = $adb->query_result($result, 0, 'setype');
		}
		$data = array('setype'=>$setype, 'relID'=>$relID);
	}
	return $data;
}

/**
 * this function accepts an ID and returns the entity value for that id
 * @param integer $id - the crmid of the record
 * @return string $data - the entity name for the id
 */
function getRecordInfoFromID($id) {
	global $adb;
	$data = array();
	$result = $adb->pquery('select setype from vtiger_crmentity where crmid=?', array($id));
	if ($adb->num_rows($result)>0) {
		$setype = $adb->query_result($result, 0, 'setype');
		$data = getEntityName($setype, $id);
	}
	if (count($data)>0) {
		$data = array_values($data);
		$data = $data[0];
	} else {
		$data = '';
	}
	return $data;
}

/**
 * this function accepts a tabiD and returns the tablename, fieldname and fieldlabel of the first email field it finds
 * @param integer $tabid - the tabid of the module
 * @return array $fields - array of the email field's tablename, fieldname and fieldlabel or empty if not found
 */
function getMailFields($tabid) {
	global $adb;
	$fields = array();
	$result = $adb->pquery("SELECT tablename,fieldlabel,fieldname FROM vtiger_field WHERE tabid=? AND uitype='13'", array($tabid));
	if ($adb->num_rows($result)>0) {
		$tablename = $adb->query_result($result, 0, "tablename");
		$fieldname = $adb->query_result($result, 0, "fieldname");
		$fieldlabel = $adb->query_result($result, 0, "fieldlabel");
		$fields = array("tablename"=>$tablename,"fieldname"=>$fieldname,"fieldlabel"=>$fieldlabel);
	}
	return $fields;
}

/**
 * Function to check if a given record exists (not deleted)
 * @param integer $recordId - record id
 */
function isRecordExists($recordId) {
	global $adb;
	$users = $groups = false;
	if (strpos($recordId, 'x')) {
		list($moduleWS,$recordId) = explode('x', $recordId);
		$userWS = vtws_getEntityId('Users');
		$users = ($userWS==$moduleWS);
		$groupWS = vtws_getEntityId('Groups');
		$groups = ($groupWS==$moduleWS);
	}
	if ($users) {
		$query = 'SELECT id FROM vtiger_users where id=? AND deleted=0';
	} elseif ($groups) {
		$query = 'SELECT groupid FROM vtiger_groups where groupid=?';
	} else {
		$query = 'SELECT crmid FROM vtiger_crmentity where crmid=? AND deleted=0';
	}
	$result = $adb->pquery($query, array($recordId));
	if ($adb->num_rows($result)) {
		return true;
	}
	return false;
}

/** Function to set date values compatible to database (YY_MM_DD)
  * @param $value -- value :: Type string
  * @returns $insert_date -- insert_date :: Type string
  */
function getValidDBInsertDateValue($value) {
	global $log;
	$log->debug("Entering getValidDBInsertDateValue(".$value.") method ...");
	$value = trim($value);
	if (empty($value)) {
		return '';
	}
	$delim = array('/','.');
	$value = str_replace($delim, '-', $value);

	list($y,$m,$d) = explode('-', $value);
	if (strlen($y) == 1) {
		$y = '0'.$y;
	}
	if (strlen($m) == 1) {
		$m = '0'.$m;
	}
	if (strlen($d) == 1) {
		$d = '0'.$d;
	}
	$value = implode('-', array($y,$m,$d));

	if (preg_match('/^\d{2,4}[-][0-3]{1,2}?\d{1,2}[-]\d{2,4}$/', $value) == 0) {
		return '';
	}

	if (strlen($y)<4) {
		$insert_date = DateTimeField::convertToDBFormat($value);
	} else {
		$insert_date = $value;
	}

	$log->debug("Exiting getValidDBInsertDateValue method ...");
	return $insert_date;
}

function getValidDBInsertDateTimeValue($value) {
	$value = trim($value);
	$valueList = explode(' ', $value);
	if (count($valueList) == 2) {
		$dbDateValue = getValidDBInsertDateValue($valueList[0]);
		$dbTimeValue = $valueList[1];
		if (!empty($dbTimeValue) && strpos($dbTimeValue, ':') === false) {
			$dbTimeValue = $dbTimeValue.':';
		}
		$timeValueLength = strlen($dbTimeValue);
		if (!empty($dbTimeValue) && strrpos($dbTimeValue, ':') == ($timeValueLength-1)) {
			$dbTimeValue = $dbTimeValue.'00';
		}
		try {
			$dateTime = new DateTimeField($dbDateValue.' '.$dbTimeValue);
			return $dateTime->getDBInsertDateTimeValue();
		} catch (Exception $ex) {
			return '';
		}
	} elseif (count($valueList) == 1) {
		return getValidDBInsertDateValue($value);
	}
	return '';
}

/** Function to set the PHP memory limit to the specified value, if the memory limit set in the php.ini is less than the specified value
 * @param $newvalue -- Required Memory Limit
 */
function _phpset_memorylimit_MB($newvalue) {
	$current = @ini_get('memory_limit');
	if (preg_match("/(.*)M/", $current, $matches)) {
		// Check if current value is less then new value
		if ($matches[1] < $newvalue) {
			@ini_set('memory_limit', "{$newvalue}M");
		}
	}
}

/** Function to sanitize the upload file name when the file name is detected to have bad extensions
 * @param String -- $fileName - File name to be sanitized
 * @return String - Sanitized file name
 */
function sanitizeUploadFileName($fileName, $badFileExtensions) {

	$fileName = preg_replace('/\s+/', '_', $fileName);//replace space with _ in filename
	$fileName = rtrim($fileName, '\\/<>?*:"<>|');

	$fileNameParts = explode(".", $fileName);
	$countOfFileNameParts = count($fileNameParts);
	$badExtensionFound = false;

	for ($i=0; $i<$countOfFileNameParts; ++$i) {
		$partOfFileName = $fileNameParts[$i];
		if (in_array(strtolower($partOfFileName), $badFileExtensions)) {
			$badExtensionFound = true;
			$fileNameParts[$i] = $partOfFileName . 'file';
		}
	}

	$newFileName = implode(".", $fileNameParts);

	if ($badExtensionFound) {
		$newFileName .= ".txt";
	}
	return $newFileName;
}

/** Function to get the tab meta information for a given id
  * @param $tabId -- tab id :: Type integer
  * @returns $tabInfo -- array of preference name to preference value :: Type array
  */
function getTabInfo($tabId) {
	global $adb;

	$tabInfoResult = $adb->pquery('SELECT prefname, prefvalue FROM vtiger_tab_info WHERE tabid=?', array($tabId));
	$tabInfo = array();
	for ($i=0; $i<$adb->num_rows($tabInfoResult); ++$i) {
		$prefName = $adb->query_result($tabInfoResult, $i, 'prefname');
		$prefValue = $adb->query_result($tabInfoResult, $i, 'prefvalue');
		$tabInfo[$prefName] = $prefValue;
	}
}

/** Function to return block name
 * @param Integer -- $blockid
 * @return String - Block Name
 */
function getBlockName($blockid) {
	global $adb;
	$blockname = VTCacheUtils::lookupBlockLabelWithId($blockid);
	if ($blockname === false && !empty($blockid)) {
		$block_res = $adb->pquery('SELECT blocklabel FROM vtiger_blocks WHERE blockid = ?', array($blockid));
		if ($adb->num_rows($block_res)) {
			$blockname = $adb->query_result($block_res, 0, 'blocklabel');
		} else {
			$blockname = '';
		}
		VTCacheUtils::updateBlockLabelWithId($blockname, $blockid);
	}
	return $blockname;
}

function validateAlphaNumericInput($string) {
	preg_match('/^[\w _\-\/]+$/', $string, $matches);
	return !(count($matches) == 0);
}

function validateServerName($string) {
	preg_match('/^[\w\-\.\\/:]+$/', $string, $matches);
	return !(count($matches) == 0);
}

function validateEmailId($string) {
	preg_match('/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/', $string, $matches);
	return !(count($matches) == 0);
}

function str_rsplit($string, $splitLength) {
	$reverseString = strrev($string);
	$chunks = str_split($reverseString, $splitLength);
	return array_reverse($chunks);
}

//Function returns Email related Modules
function getEmailRelatedModules() {
	global $current_user;
	$handler = vtws_getModuleHandlerFromName('Emails', $current_user);
	$meta = $handler->getMeta();
	$moduleFields = $meta->getModuleFields();
	$fieldModel = $moduleFields['parent_id'];
	$relatedModules = $fieldModel->getReferenceList();
	foreach ($relatedModules as $key => $value) {
		if ($value == 'Users') {
			unset($relatedModules[$key]);
		}
	}
	return $relatedModules;
}

function hasEmailField($module) {
	global $adb;
	$querystr = 'SELECT fieldid FROM vtiger_field WHERE tabid=? and uitype=13 and vtiger_field.presence in (0,2)';
	$queryres = $adb->pquery($querystr, array(getTabid($module)));
	return (($queryres && $adb->num_rows($queryres)>0) || $module=='Campaigns');
}

function getFirstEmailField($module) {
	global $adb;
	$querystr = 'SELECT fieldname FROM vtiger_field WHERE tabid=? and uitype=13 and vtiger_field.presence in (0,2)';
	$queryres = $adb->pquery($querystr, array(getTabid($module)));
	if ($queryres && $adb->num_rows($queryres)>0) {
		$emailfield = $adb->query_result($queryres, 0, 0);
	} else {
		$emailfield = '';
	}
	return $emailfield;
}

function modulesWithEmailField() {
	global $adb;
	$querystr = 'SELECT distinct vtiger_tab.name
		FROM vtiger_field
		INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_field.tabid
		WHERE uitype=13 and vtiger_field.presence in (0,2)';
	$queryres = $adb->query($querystr);
	$emailmodules = array();
	while ($mod = $adb->fetch_array($queryres)) {
		$emailmodules[] = $mod['name'];
	}
	return $emailmodules;
}

function getInventoryModules() {
	return array('Invoice','Quotes','PurchaseOrder','SalesOrder','Issuecards', 'Receiptcards');
}

/**
 * Function to get the list of Contacts related to an activity
 * @param Integer $activityId
 * @return Array $contactsList - List of Contact ids, mapped to Contact Names
 */
function getActivityRelatedContacts($activityId) {
	$adb = PearDatabase::getInstance();

	$result = $adb->pquery('SELECT contactid FROM vtiger_cntactivityrel WHERE activityid=?', array($activityId));
	$noOfContacts = $adb->num_rows($result);
	$contactsList = array();
	for ($i = 0; $i < $noOfContacts; ++$i) {
		$contactId = $adb->query_result($result, $i, 'contactid');
		$displayValueArray = getEntityName('Contacts', $contactId);
		if (!empty($displayValueArray)) {
			foreach ($displayValueArray as $field_value) {
				$contact_name = $field_value;
			}
		} else {
			$contact_name='';
		}
		$contactsList[$contactId] = $contact_name;
	}
	return $contactsList;
}

function isLeadConverted($leadId) {
	$adb = PearDatabase::getInstance();
	$result = $adb->pquery('SELECT converted FROM vtiger_leaddetails WHERE converted = 1 AND leadid=?', array($leadId));
	return $result && $adb->num_rows($result) > 0;
}

function getSelectedRecords($input, $module, $idstring, $excludedRecords) {
	global $adb;

	if ($idstring == 'relatedListSelectAll') {
		$recordid = vtlib_purify($input['recordid']);
		if ($module == 'Accounts') {
			$result = getCampaignAccountIds($recordid);
		}
		if ($module == 'Contacts') {
			$result = getCampaignContactIds($recordid);
		}
		if ($module == 'Leads') {
			$result = getCampaignLeadIds($recordid);
		}
		$storearray = array();
		for ($i = 0; $i < $adb->num_rows($result); $i++) {
			$storearray[] = $adb->query_result($result, $i, 'id');
		}

		$excludedRecords=explode(';', $excludedRecords);
		$storearray=array_diff($storearray, $excludedRecords);
	} elseif ($module == 'Documents' && GlobalVariable::getVariable('Document_Folder_View', 1, 'Documents')) {
		if (isset($input['selectallmode']) && $input['selectallmode']=='true') {
			$result = getSelectAllQuery($input, $module);
			$storearray = array();
			$focus = CRMEntity::getInstance($module);

			for ($i = 0; $i < $adb->num_rows($result); $i++) {
				$storearray[] = $adb->query_result($result, $i, $focus->table_index);
			}

			$excludedRecords = explode(';', $excludedRecords);
			$storearray = array_diff($storearray, $excludedRecords);
			if ($idstring != 'all') {
				$storearray = array_merge($storearray, explode(';', $idstring));
			}
			$storearray = array_unique($storearray);
		} else {
			$storearray = explode(";", $idstring);
		}
	} elseif ($idstring == 'all') {
		$result = getSelectAllQuery($input, $module);
		$storearray = array();
		$focus = CRMEntity::getInstance($module);

		for ($i = 0; $i < $adb->num_rows($result); $i++) {
			$storearray[] = $adb->query_result($result, $i, $focus->table_index);
		}

		$excludedRecords = explode(';', $excludedRecords);
		$storearray = array_diff($storearray, $excludedRecords);
	} else {
		$storearray = explode(";", $idstring);
	}

	return $storearray;
}

function getSelectAllQuery($input, $module) {
	global $adb,$current_user;

	$viewid = vtlib_purify($input['viewname']);

	if ($module == "Calendar") {
		$listquery = getListQuery($module);
		$oCustomView = new CustomView($module);
		$query = $oCustomView->getModifiedCvListQuery($viewid, $listquery, $module);
		$where = '';
		if (isset($input['query']) && $input['query'] == 'true') {
			list($where, $ustring) = explode("#@@#", getWhereCondition($module, $input));
			if (isset($where) && $where != '') {
				$query .= " AND " .$where;
			}
		}
	} else {
		$queryGenerator = new QueryGenerator($module, $current_user);
		$queryGenerator->initForCustomViewById($viewid);

		if (isset($input['query']) && $input['query'] == 'true') {
			$queryGenerator->addUserSearchConditions($input);
		}
		$queryGenerator->setFields(array('id'));
		$query = $queryGenerator->getQuery();

		if ($module == 'Documents' && GlobalVariable::getVariable('Document_Folder_View', 1, 'Documents')) {
			$folderid = vtlib_purify($input['folderidstring']);
			$folderid = str_replace(';', ',', $folderid);
			$query .= " AND vtiger_notes.folderid in (".$folderid.")";
		}
	}

	return $adb->pquery($query, array());
}

function getCampaignAccountIds($id) {
	global $adb;
	$sql = "SELECT vtiger_account.accountid as id FROM vtiger_account
		INNER JOIN vtiger_campaignaccountrel ON vtiger_campaignaccountrel.accountid = vtiger_account.accountid
		LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
		WHERE vtiger_campaignaccountrel.campaignid = ? AND vtiger_crmentity.deleted=0";
	return $adb->pquery($sql, array($id));
}

function getCampaignContactIds($id) {
	global $adb;
	$sql = "SELECT vtiger_contactdetails.contactid as id FROM vtiger_contactdetails
		INNER JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.contactid = vtiger_contactdetails.contactid
		LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
		WHERE vtiger_campaigncontrel.campaignid = ? AND vtiger_crmentity.deleted=0";
	return $adb->pquery($sql, array($id));
}

function getCampaignLeadIds($id) {
	global $adb;
	$sql = "SELECT vtiger_leaddetails.leadid as id FROM vtiger_leaddetails
		INNER JOIN vtiger_campaignleadrel ON vtiger_campaignleadrel.leadid = vtiger_leaddetails.leadid
		LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
		WHERE vtiger_campaignleadrel.campaignid = ? AND vtiger_crmentity.deleted=0";
	return $adb->pquery($sql, array($id));
}

/** Function to get the difference between 2 datetime strings or millisecond values */
function dateDiff($d1, $d2) {
	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
	$d2 = (is_string($d2) ? strtotime($d2) : $d2);

	$diffSecs = abs($d1 - $d2);
	$baseYear = min(date("Y", $d1), date("Y", $d2));
	$diff = mktime(0, 0, $diffSecs, 1, 1, $baseYear);
	return array(
		"years" => date("Y", $diff) - $baseYear,
		"months_total" => (date("Y", $diff) - $baseYear) * 12 + date("n", $diff) - 1,
		"months" => date("n", $diff) - 1,
		"days_total" => floor($diffSecs / (3600 * 24)),
		"days" => date("j", $diff) - 1,
		"hours_total" => floor($diffSecs / 3600),
		"hours" => date("G", $diff),
		"minutes_total" => floor($diffSecs / 60),
		"minutes" => (int) date("i", $diff),
		"seconds_total" => $diffSecs,
		"seconds" => (int) date("s", $diff)
	);
}

/**
* Function to get the approximate difference between two date time values as string
*/
function dateDiffAsString($d1, $d2) {
	global $currentModule;

	$dateDiff = dateDiff($d1, $d2);

	$years = $dateDiff['years'];
	$months = $dateDiff['months'];
	$days = $dateDiff['days'];
	$hours = $dateDiff['hours'];
	$minutes = $dateDiff['minutes'];
	$seconds = $dateDiff['seconds'];

	if ($years > 0) {
		$diffString = "$years ".getTranslatedString('LBL_YEARS_AGO', $currentModule);
	} elseif ($months > 0) {
		$diffString = "$months ".getTranslatedString('LBL_MONTHS_AGO', $currentModule);
	} elseif ($days > 0) {
		$diffString = "$days ".getTranslatedString('LBL_DAYS_AGO', $currentModule);
	} elseif ($hours > 0) {
		$diffString = "$hours ".getTranslatedString('LBL_HOURS_AGO', $currentModule);
	} elseif ($minutes > 0) {
		$diffString = "$minutes ".getTranslatedString('LBL_MINUTES_AGO', $currentModule);
	} else {
		$diffString = "$seconds ".getTranslatedString('LBL_SECONDS_AGO', $currentModule);
	}
	return $diffString;
}

function getMinimumCronFrequency() {
	return GlobalVariable::getVariable('Application_Minimum_Cron_Frequency', 15);
}

/**
 * Function to get the details of the default company
 */
function retrieveCompanyDetails() {
	global $adb;
	$companyDetails = array();
	$query = $adb->pquery(
		'SELECT c.*,a.*
			FROM vtiger_cbcompany c
			JOIN vtiger_crmentity on vtiger_crmentity.crmid = c.cbcompanyid
			LEFT JOIN vtiger_seattachmentsrel s ON c.cbcompanyid = s.crmid
			LEFT JOIN vtiger_attachments a ON s.attachmentsid = a.attachmentsid
			WHERE c.defaultcompany = 1 and vtiger_crmentity.deleted = 0',
		array()
	);
	if ($query && $adb->num_rows($query) > 0) {
		$record = $adb->query_result($query, 0, 'cbcompanyid');
		$companyDetails['name']     = $companyDetails['companyname'] = $adb->query_result($query, 0, 'companyname');
		$companyDetails['website']  = $adb->query_result($query, 0, 'website');
		$companyDetails['email']  = $adb->query_result($query, 0, 'email');
		$companyDetails['siccode']  = $adb->query_result($query, 0, 'siccode');
		$companyDetails['accid']  = $adb->query_result($query, 0, 'accid');
		$companyDetails['address']  = $adb->query_result($query, 0, 'address');
		$companyDetails['city']     = $adb->query_result($query, 0, 'city');
		$companyDetails['state']    = $adb->query_result($query, 0, 'state');
		$companyDetails['country']  = $adb->query_result($query, 0, 'country');
		$companyDetails['postalcode'] = $adb->query_result($query, 0, 'postalcode');
		$companyDetails['code'] = $adb->query_result($query, 0, 'postalcode');
		$companyDetails['phone']    = $adb->query_result($query, 0, 'phone');
		$companyDetails['fax']      = $adb->query_result($query, 0, 'fax');
		for ($i=0; $i<$adb->num_rows($query); $i++) {
			$path           = $adb->query_result($query, $i, 'path');
			$attachmentsid  = $adb->query_result($query, $i, 'attachmentsid');
			$favicon        = decode_html($adb->query_result($query, $i, 'favicon'));
			$companylogo    = decode_html($adb->query_result($query, $i, 'companylogo'));
			$applogo        = decode_html($adb->query_result($query, $i, 'applogo'));
			$name           = $adb->query_result($query, $i, 'name'); // attachmentname
			if ($name == $favicon && !isset($companyDetails['favicon'])) {
				$companyDetails['favicon'] = $path.$attachmentsid.'_'.$favicon;
			}
			if ($name == $companylogo && !isset($companyDetails['companylogo'])) {
				$companyDetails['companylogo'] = $path.$attachmentsid.'_'.$companylogo;
			}
			if ($name == $applogo && !isset($companyDetails['applogo'])) {
				$companyDetails['applogo'] = $path.$attachmentsid.'_'.$applogo;
			}
		}
	} else {
		$companyDetails['name'] = $companyDetails['companyname'] = GlobalVariable::getVariable('Application_UI_Name', 'coreBOS');
	}
	$companyDetails = setDefaultCompanyParams($companyDetails);
	return $companyDetails;
}

/**
 * Function to set default company details if left empty
 */
function setDefaultCompanyParams($companyDetails) {
	$imageArray = array('companylogo','applogo');
	for ($i=0; $i<sizeof($imageArray); $i++) {
		$imagename = $imageArray[$i];
		if (empty($companyDetails[$imagename])) {
			$companyDetails[$imagename] = 'test/logo/noimageloaded.png';
		}
	}
	if (empty($companyDetails['favicon'])) {
		$companyDetails['favicon'] = 'themes/images/favicon.ico';
	}
	return $companyDetails;
}
?>
