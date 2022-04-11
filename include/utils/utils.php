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
require_once 'include/utils/cbTerminate.php';
include_once 'include/fields/metainformation.php';
require_once 'include/utils/Session.php';
require_once 'include/utils/Request.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/cbSettings.php';
require_once 'include/utils/cbCache.php';
require_once 'include/integrations/cache/cache.php';
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
require_once 'include/fields/BooleanField.php';
require_once 'include/fields/FileField.php';
require_once 'include/fields/CurrencyField.php';
require_once 'data/CRMEntity.php';
require_once 'vtlib/Vtiger/Language.php';
require_once 'include/RelatedListView.php';

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
 * @param object smarty to load the variables, if empty it will only return the variables in an array
 * @return array with the variables
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
 * @param integer row
 * @param string first column
 * @param string last column
 * @return string fullname
 */
function return_name(&$row, $first_column, $last_column) {
	global $log;
	$log->debug('> return_name '.$row.','.$first_column.','.$last_column);
	$first_name = '';
	$last_name = '';
	$full_name = '';

	if (isset($row[$first_column])) {
		$first_name = stripslashes($row[$first_column]);
	}

	if (isset($row[$last_column])) {
		$last_name = stripslashes($row[$last_column]);
	}

	$full_name = $first_name;

	// If we have a first name and we have a last name
	if ($full_name != '' && $last_name != '') {
		// append a space, then the last name
		$full_name .= ' '.$last_name;
	} elseif ($last_name != '') { // If we have no first name, but we have a last name
		// append the last name without the space.
		$full_name .= $last_name;
	}

	$log->debug('< return_name');
	return $full_name;
}

/** Function to return language
 * @return string languages
 */
function get_languages() {
	global $log, $languages;
	$log->debug('>< get_languages');
	return $languages;
}

/** Function to return language
 * @param string key
 * @return string languages
 */
function get_language_display($key) {
	global $log, $languages;
	$log->debug('>< get_language_display '.$key);
	return $languages[$key];
}

/** Function returns the user array
 * @param string assigned_user_id
 * @return array user list
 */
function get_assigned_user_name($assigned_user_id) {
	global $log;
	$log->debug('> get_assigned_user_name '.$assigned_user_id);
	$user_list = get_user_array(false, '');
	if (isset($user_list[$assigned_user_id])) {
		$log->debug('< get_assigned_user_name');
		return $user_list[$assigned_user_id];
	}
	$log->debug('< get_assigned_user_name');
	return '';
}

/** Function returns the user key in user array
 * @param boolean add blank picklist entry
 * @param string user status to retrieve
 * @param string assigned_user id must always add this user
 * @param string sharing type: private
 * @return array user array
 */
function get_user_array($add_blank = true, $status = 'Active', $assigned_user = '', $private = '') {
	global $log, $current_user;
	$log->debug('> get_user_array '.$add_blank.','. $status.','.$assigned_user.','.$private);
	if (isset($current_user) && $current_user->id != '') {
		$userprivs = $current_user->getPrivileges();
		$current_user_parent_role_seq = $userprivs->getParentRoleSequence();
	} else {
		$current_user_parent_role_seq = '';
	}
	static $user_array = null;
	$module = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';

	if ($user_array == null) {
		$db = PearDatabase::getInstance();
		$temp_result = array();
		$userOrder = GlobalVariable::getVariable('Application_User_SortBy', 'user_name ASC', $module, $current_user->id);
		// Including deleted users for now.
		if (empty($status)) {
			$query = 'SELECT id, user_name,ename from vtiger_users';
			$params = array();
		} else {
			$assignUP = GlobalVariable::getVariable('Application_Permit_Assign_Up', 0, $module, $current_user->id);
			if ($private == 'private' && empty($assignUP)) {
				if ($userOrder != 'DO NOT SORT') {
					$orderFields = preg_replace('/ asc\s*$| asc\s*,| desc\s*$| desc\s*,/i', ',', $userOrder);
					$orderFields = preg_replace('/\s*/', '', $orderFields);
					$orderFields = str_replace(array('user_name,','first_name,','last_name,'), '', $orderFields);
					$orderFields = str_replace(array('user_name','first_name','last_name'), '', $orderFields);
					$orderFields = str_replace(',,', ',', $orderFields);
					$orderFields = trim($orderFields, ',');
					if (strlen($orderFields)>1) {
						$orderFields .= ',';
					}
				} else {
					$orderFields = '';
				}
				$assignBrothers = GlobalVariable::getVariable('Application_Permit_Assign_SameRole', 0, $module, $current_user->id);
				$query = "select $orderFields id as id,user_name as user_name,first_name,last_name,ename
					from vtiger_users
					where id=? and status='Active'
					union
					select $orderFields vtiger_user2role.userid as id,vtiger_users.user_name as user_name,vtiger_users.first_name as first_name,vtiger_users.last_name as last_name,ename
					from vtiger_user2role
					inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
					inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
					where vtiger_role.parentrole like ? and status='Active'
					union select $orderFields shareduserid as id,vtiger_users.user_name as user_name, vtiger_users.first_name as first_name, vtiger_users.last_name as last_name,ename
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
				$query = 'SELECT id, user_name,first_name,last_name,ename from vtiger_users WHERE status=?';
				$params = array($status);
			}
		}
		if (!empty($assigned_user)) {
			$query .= ' OR id=?';
			$params[] = $assigned_user;
		}

		if ($userOrder != 'DO NOT SORT') {
			$orderByCol = $db->convert2Sql('?', array($userOrder));
			if (strpos($query, 'union')) {
				$query = 'SELECT * FROM ('.$query.') AS USRSEL order by '.str_replace("'", '', $orderByCol);
			} else {
				$query .= ' order by '.str_replace("'", '', $orderByCol);
			}
		}

		$result = $db->pquery($query, $params, true, 'Error filling in user array');

		if ($add_blank) {
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while ($row = $db->fetchByAssoc($result)) {
			$temp_result[$row['id']] = getFullNameFromArray('Users', $row);
		}

		$user_array = $temp_result;
	}

	$log->debug('< get_user_array');
	return $user_array;
}

function get_group_array($add_blank = true, $status = 'Active', $assigned_user = '', $private = '', $force = false) {
	global $log, $current_user, $currentModule;
	$log->debug('> get_group_array '.$add_blank.','. $status.','.$assigned_user.','.$private);
	if (isset($current_user) && $current_user->id != '') {
		$userprivs = $current_user->getPrivileges();
		$current_user_parent_role_seq = $userprivs->getParentRoleSequence();
		$current_user_groups = $userprivs->getGroups();
		$parent_roles = $userprivs->getParentRoles();
	} else {
		$current_user_parent_role_seq = '';
		$current_user_groups = array();
		$parent_roles = array();
	}
	static $group_array = null;
	$module= (isset($_REQUEST['module']) ? vtlib_purify($_REQUEST['module']) : $currentModule);

	if ($group_array == null || $force) {
		$db = PearDatabase::getInstance();
		$temp_result = array();
		// Sharing is Public. All users should be listed
		$query = 'SELECT groupid, groupname from vtiger_groups'; // Sharing is Public. All users should be listed
		$params = array();
		$assignAllGroups = GlobalVariable::getVariable('Application_Permit_Assign_AllGroups', 0, $module, $current_user->id);
		if ($private == 'private' && $assignAllGroups==0) {
			$query .= ' WHERE groupid=?';
			$params = array( $current_user->id);

			if (count($current_user_groups) != 0) {
				$query .= ' OR vtiger_groups.groupid in ('.generateQuestionMarks($current_user_groups).')';
				$params[] = $current_user_groups;
			}
			// Sharing is Private. Only the current user should be listed
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

		if ($add_blank) {
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while ($row = $db->fetchByAssoc($result)) {
			$temp_result[$row['groupid']] = $row['groupname'];
		}

		$group_array = $temp_result;
	}

	$log->debug('< get_group_array');
	return $group_array;
}

/** Function skips executing arbitary commands given in a string
 * @param string string
 * @param integer maximun length
 * @return string escaped string
 */
function clean($string, $maxLength) {
	global $log;
	$log->debug('> clean '.$string.','. $maxLength);
	$string = substr($string, 0, $maxLength);
	$log->debug('< clean');
	return escapeshellcmd($string);
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 */
function safe_map($request_var, &$focus, $always_copy = false) {
	global $log;
	$log->debug('> safe_map '.$request_var.','.get_class($focus).','.$always_copy);
	safe_map_named($request_var, $focus, $request_var, $always_copy);
	$log->debug('< safe_map');
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 */
function safe_map_named($request_var, &$focus, $member_var, $always_copy) {
	global $log;
	$log->debug('> safe_map_named '.$request_var.','.get_class($focus).','.$member_var.','.$always_copy);
	if (isset($_REQUEST[$request_var]) && ($always_copy || is_null($focus->$member_var))) {
		$focus->$member_var = $_REQUEST[$request_var];
	}
	$log->debug('< safe_map_named');
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
	$log->debug('> return_app_currency_strings_language '.$language);
	// Backup the value first
	$temp_app_currency_strings = $app_currency_strings;
	@include "include/language/$language.lang.php";
	if (!isset($app_currency_strings)) {
		$log->fatal('Unable to find the application language file for language: '.$language);
		require "include/language/$default_language.lang.php";
	}
	if (!isset($app_currency_strings)) {
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug('< return_app_currency_strings_language');
		return null;
	}
	$return_value = $app_currency_strings;

	// Restore the value back
	$app_currency_strings = $temp_app_currency_strings;

	$log->debug('< return_app_currency_strings_language');
	return $return_value;
}

/** This function retrieves an application language file and returns the array of strings included.
 * If you are using the current language, do not call this function unless you are loading it for the first time */
function return_application_language($language) {
	global $app_strings, $default_language, $log;
	$log->debug('> return_application_language '.$language);
	$temp_app_strings = $app_strings;
	$languagefound = $language;
	checkFileAccessForInclusion("include/language/$language.lang.php");
	@include "include/language/$language.lang.php";
	if (!isset($app_strings)) {
		$log->fatal('Unable to find the application language file for language: '.$language);
		require "include/language/$default_language.lang.php";
		$languagefound = $default_language;
	}

	if (!isset($app_strings)) {
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug('< return_application_language');
		return null;
	}

	if (file_exists("include/language/$languagefound.custom.php")) {
		@include "include/language/$languagefound.custom.php";
		$app_strings = array_merge($app_strings, $custom_strings);
	}
	$return_value = $app_strings;
	$app_strings = $temp_app_strings;

	$log->debug('< return_application_language');
	return $return_value;
}

/** This function retrieves a module's language file and returns the array of strings included.
 * If you are in the current module, do not call this function unless you are loading it for the first time */
function return_module_language($language, $module) {
	global $mod_strings, $default_language, $log;
	$log->debug('> return_module_language '.$language.','. $module);
	static $cachedModuleStrings = array();

	if (!empty($cachedModuleStrings[$module.$language])) {
		$log->debug('< return_module_language');
		return $cachedModuleStrings[$module.$language];
	}

	$temp_mod_strings = $mod_strings;
	$languagefound = $language;
	@include "modules/$module/language/$language.lang.php";
	if (!isset($mod_strings)) {
		$log->fatal('Unable to find the module language file for language: '.$language.' and module: '.$module);
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
		$log->debug('< return_module_language');
		return null;
	}

	if (file_exists("modules/$module/language/$languagefound.custom.php")) {
		@include "modules/$module/language/$languagefound.custom.php";
		$mod_strings = $custom_strings + $mod_strings;
	}
	$return_value = $mod_strings;
	$mod_strings = $temp_mod_strings;

	$log->debug('< return_module_language');
	$cachedModuleStrings[$module.$language] = $return_value;
	return $return_value;
}

/** This function returns the mod_strings for the given language and module: it does not update the current mod_strings contents */
function return_specified_module_language($language, $module) {
	global $log, $default_language;
	$log->debug('> return_specified_module_language '.$language.','. $module);
	$languagefound = $language;
	@include "modules/$module/language/$language.lang.php";
	if (!isset($mod_strings)) {
		$log->fatal('Unable to find the module language file for language: '.$language.' and module: '.$module);
		require "modules/$module/language/$default_language.lang.php";
		$languagefound = $default_language;
	}

	if (!isset($mod_strings)) {
		$log->fatal("Unable to load the module($module) language file for the selected language($language) or the default language($default_language)");
		$log->debug('< return_specified_module_language');
		return null;
	}

	if (file_exists("modules/$module/language/$languagefound.custom.php")) {
		@include "modules/$module/language/$languagefound.custom.php";
		$mod_strings = array_merge($mod_strings, $custom_strings);
	}
	$return_value = $mod_strings;

	$log->debug('< return_specified_module_language');
	return $return_value;
}

/** If the session variable is defined and is not equal to '' then return it. Otherwise, return the default value. */
function return_session_value_or_default($varname, $default) {
	global $log;
	$log->debug('> return_session_value_or_default '.$varname.','. $default);
	if (isset($_SESSION[$varname]) && $_SESSION[$varname] != '') {
		$log->debug('< return_session_value_or_default');
		return $_SESSION[$varname];
	}

	$log->debug('< return_session_value_or_default');
	return $default;
}

/**
 * Creates an array of where restrictions. These are used to construct a where SQL statement on the query
 * It looks for the variable in the $_REQUEST array. If it is set and is not '' it will create a where clause out of it
 * @param array to append the clause to
 * @param string name of the variable to look for and add to the where clause if found
 * @param string [Optional] If specified, this is the SQL column name that is used. If not specified, the $variable_name is used as the SQL_name
 */
function append_where_clause(&$where_clauses, $variable_name, $SQL_name = null) {
	global $log;
	$log->debug('> append_where_clause '.$where_clauses.','.$variable_name.','.$SQL_name);
	if ($SQL_name == null) {
		$SQL_name = $variable_name;
	}

	if (isset($_REQUEST[$variable_name]) && $_REQUEST[$variable_name] != '') {
		$where_clauses[] = "$SQL_name like '$_REQUEST[$variable_name]%'";
	}
	$log->debug('< append_where_clause');
}

/**
 * Generate the appropriate SQL based on the where clauses
 * @param array of individual where clauses stored as strings
 * @return string the final SQL where clause to be executed
 */
function generate_where_statement($where_clauses) {
	global $log;
	$log->debug('> generate_where_statement '.$where_clauses);
	$where = '';
	foreach ($where_clauses as $clause) {
		if ($where != '') {
			$where .= ' and ';
		}
		$where .= $clause;
	}
	$log->debug('< generate_where_statement: '.$where);
	return $where;
}

/**
 * A temporary method of generating GUIDs of the correct format for our DB.
 * @return string contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
 */
function create_guid() {
	global $log;
	$log->debug('> create_guid');
	$microTime = microtime();
	list($a_dec, $a_sec) = explode(' ', $microTime);

	$dec_hex = sprintf('%x', $a_dec* 1000000);
	$sec_hex = sprintf('%x', $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = '';
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

	$log->debug('< create_guid');
	return $guid;
}

/** Function to create guid section for a UUID
 * @param integer number of hexadecimal characters to return
 * @return string concatenation of the given number of hexadecimal random integers
 */
function create_guid_section($characters) {
	global $log;
	$log->debug('> create_guid_section '.$characters);
	$return = '';
	for ($i=0; $i<$characters; $i++) {
		$return .= sprintf('%x', rand(0, 15));
	}
	$log->debug('< create_guid_section');
	return $return;
}

/** Function to ensure length: the given string will be cut at the given length or padded with zeros to the given length
 * @param string the string we need to control the length of
 * @param integer length the string must have
 * @return void the given string will be modified directly (passed by reference)
 */
function ensure_length(&$string, $length) {
	global $log;
	$log->debug('> ensure_length '.$string.','. $length);
	$strlen = strlen($string);
	if ($strlen < $length) {
		$string = str_pad($string, $length, '0');
	} elseif ($strlen > $length) {
		$string = substr($string, 0, $length);
	}
	$log->debug('< ensure_length');
}

/**
 * Return an array of directory names.
 */
function get_themes() {
	global $log;
	$log->debug('> get_themes');
	$filelist = array();
	if ($dir = @opendir('./themes')) {
		while ($file = readdir($dir)) {
			if ($file != '..' && $file != '.' && is_dir('./themes/'.$file) && $file[0] != '.' && is_file("./themes/$file/style.css")) {
				$filelist[$file] = $file;
			}
		}
		closedir($dir);
	}
	ksort($filelist);
	$log->debug('< get_themes');
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
	if ($request['file']!='ActivityReminderCallbackAjax' && $request['module']!='Settings' && $request['file']!='ListView'
		&& $request['module']!='Portal' && $request['module']!='Reports'
	) {
		$ajax_action = $request['module'].'Ajax';
	}
	if (($action != 'CustomView' && $action != 'Export' && $action != $ajax_action && $action != 'LeadConvertToEntities' && $action != 'CreatePDF'
		&& $action != 'ConvertAsFAQ' && $request['module'] != 'Dashboard' && $action != 'CreateSOPDF' && $action != 'SendPDFMail' && !isset($_REQUEST['submode']))
		|| $search
	) {
		$doconvert = true;
	}
}
decide_to_html();//call the function once when loading

/** Function to convert the given string to html
 * @param string
 * @return string
 */
function to_html($string) {
	global $doconvert,$default_charset;
	if ($doconvert) {
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

/** Function to get the tab label for a given id
 * @param integer tab id
 * @return string module label
 */
function getTabname($tabid) {
	global $log, $adb;
	$log->debug('> getTabname '.$tabid);
	$result = $adb->pquery('select tablabel from vtiger_tab where tabid=?', array($tabid));
	$tabname = $adb->query_result($result, 0, 'tablabel');
	$log->debug('< getTabname');
	return $tabname;
}

/** Function to get the tab module name for a given id
 * @param integer tab id
 * @return string module name
 */
function getTabModuleName($tabid) {
	global $log, $adb;
	$log->debug('> getTabModuleName '.$tabid);

	// Lookup information in cache first
	$tabname = VTCacheUtils::lookupModulename($tabid);
	if ($tabname === false) {
		$result = $adb->pquery('select name from vtiger_tab where tabid=?', array($tabid));
		$tabname = $adb->query_result($result, 0, 'name');
		// Update information to cache for re-use
		VTCacheUtils::updateTabidInfo($tabid, $tabname);
	}
	$log->debug('< getTabModuleName '.$tabname);
	return $tabname;
}

/** Function to get column fields for a given module
 * @param string module name
 * @return array column fields
 */
function getColumnFields($module) {
	global $log, $adb;
	$log->debug('> getColumnFields '.$module);

	// Lookup in cache for information
	$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);

	if ($cachedModuleFields === false) {
		$tabid = getTabid($module);
		// Let us pick up all the fields first so that we can cache information
		$sql = 'SELECT tabid, fieldname, fieldid, fieldlabel, columnname, tablename, uitype, typeofdata, presence, defaultvalue, generatedtype FROM vtiger_field WHERE tabid=?';
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
					$resultrow['presence'],
					$resultrow['defaultvalue'],
					$resultrow['generatedtype']
				);
			}
		}

		// For consistency get information from cache
		$cachedModuleFields = VTCacheUtils::lookupFieldInfo_Module($module);
	}

	$column_fld = array();
	if ($cachedModuleFields) {
		foreach ($cachedModuleFields as $fieldinfo) {
			$column_fld[$fieldinfo['fieldname']] = '';
		}
	}

	$log->debug('< getColumnFields');
	return $column_fld;
}

/** Function to get a users email
 * @param integer user ID
 * @return string email
 */
function getUserEmail($userid) {
	global $log, $adb;
	$log->debug('> getUserEmail', (array)$userid);
	$email = '';
	if (!empty($userid) && is_numeric($userid)) {
		$userid = (array)$userid;
		$result = $adb->pquery('select email1 from vtiger_users where id=?', $userid);
		if ($result && $adb->num_rows($result)>0) {
			$email = $adb->query_result($result, 0, 'email1');
		}
	}
	$log->debug('< getUserEmail');
	return $email;
}

/** Function to get a userid for outlook // outlook security
 * @param string username
 * @return integer user id
 */
function getUserId_Ol($username) {
	global $log, $adb;
	$log->debug('> getUserId_Ol '.$username);
	$result = $adb->pquery('select id from vtiger_users where user_name=?', array($username));
	$num_rows = $adb->num_rows($result);
	if ($num_rows > 0) {
		$user_id = $adb->query_result($result, 0, 'id');
	} else {
		$user_id = 0;
	}
	$log->debug('< getUserId_Ol');
	return $user_id;
}

/** Function to get an action ID from an action name
 * @param string action name
 * @return integer action id
 */
function getActionid($action) {
	global $log, $adb;
	$log->debug('> getActionid '.$action);
	$result = $adb->pquery('select actionid from vtiger_actionmapping where actionname=?', array($action));
	$actionid = $adb->query_result($result, 0, 'actionid');
	$log->debug('< getActionid '.$actionid);
	return $actionid;
}

/** Function to get the action name from an action ID
 * @param integer action id
 * @return string action name if securitycheck=0, empty if not
 */
function getActionname($actionid) {
	global $log, $adb;
	if ($actionid==='') {
		$log->debug('>< getActionname empty');
		return '';
	}
	$log->debug('> getActionname '.$actionid);
	$result = $adb->pquery('select actionname from vtiger_actionmapping where actionid=? and securitycheck=0', array($actionid));
	$actionname = $adb->query_result($result, 0, 'actionname');
	$log->debug('< getActionname');
	return $actionname;
}

/** Function to get a assigned user id for a given entity
 * @param integer entity id
 * @return integer user id
 */
function getUserId($record) {
	global $log, $adb, $currentModule;
	$log->debug('> getUserId '.$record);
	$mod = CRMEntity::getInstance($currentModule);
	$userrs = $adb->pquery('select smownerid from '.$mod->crmentityTable.' where crmid = ?', array($record));
	$user_id = $adb->query_result($userrs, 0, 'smownerid');
	$log->debug('< getUserId');
	return $user_id;
}

/** Function to get a user id or group id for a given entity
 * @param integer entity id
 * @return array owner id
 */
function getRecordOwnerId($record) {
	global $log, $adb;
	$log->debug('> getRecordOwnerId '.$record);
	$ownerArr=array();
	$recModule = getSalesEntityType($record);
	if (empty($recModule)) {
		$log->debug('< getRecordOwnerId record not found');
		return $ownerArr;
	}
	$mod = CRMEntity::getInstance($recModule);
	$result=$adb->pquery('select smownerid from '.$mod->crmentityTable.' where crmid=?', array($record));
	if ($adb->num_rows($result) > 0) {
		$ownerId=$adb->query_result($result, 0, 'smownerid');
		$sql_result = $adb->query('select 1 from vtiger_users where id='.$ownerId);
		if ($adb->num_rows($sql_result) > 0) {
			$ownerArr['Users'] = $ownerId;
		} else {
			$ownerArr['Groups'] = $ownerId;
		}
	}
	$log->debug('< getRecordOwnerId');
	return $ownerArr;
}

/** Function to insert value to profile2field table
 * @param integer profileid
 */
function insertProfile2field($profileid) {
	global $log, $adb;
	$log->debug('> insertProfile2field '.$profileid);

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
	$log->debug('< insertProfile2field');
}

/** Function to insert into default org field */
function insert_def_org_field() {
	global $log, $adb;
	$log->debug('> insert_def_org_field');
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
	$log->debug('< insert_def_org_field');
}

/** Function to insert value to profile2field table
 * @param string field module
 * @param integer profileid
 * @return string result
 */
function getProfile2FieldList($fld_module, $profileid) {
	global $log, $adb;
	$log->debug('> getProfile2FieldList '.$fld_module.','. $profileid);
	$tabid = getTabid($fld_module);
	$query = 'select vtiger_profile2field.visible,vtiger_field.*
		from vtiger_profile2field
		inner join vtiger_field on vtiger_field.fieldid=vtiger_profile2field.fieldid
		where vtiger_profile2field.profileid=? and vtiger_profile2field.tabid=? and vtiger_field.presence in (0,1,2)';
	$result = $adb->pquery($query, array($profileid, $tabid));
	$log->debug('< getProfile2FieldList');
	return $result;
}

/** Function to insert value to profile2fieldPermissions table
 * @param string field module
 * @param integer profileid
 * @return array return_data
 */
function getProfile2FieldPermissionList($fld_module, $profileid) {
	global $log;
	$log->debug('> getProfile2FieldPermissionList '.$fld_module.','. $profileid);

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
	$log->debug('< getProfile2FieldPermissionList');
	return $return_data;
}

/** Function to insert value to profile2fieldPermissions table
 * @param string field module
 * @param integer profileid
 * @return string return_data
 */
function getProfile2ModuleFieldPermissionList($fld_module, $profileid) {
	global $log, $adb;
	$log->debug('> getProfile2ModuleFieldPermissionList '.$fld_module.','. $profileid);

	$tabid = getTabid($fld_module);
	$query = 'SELECT vtiger_profile2tab.tabid, vtiger_profile2tab.permissions, vtiger_field.fieldlabel, vtiger_field.uitype,
		vtiger_field.fieldid, vtiger_field.displaytype, vtiger_field.typeofdata
		FROM vtiger_profile2tab INNER JOIN vtiger_field ON vtiger_field.tabid=vtiger_profile2tab.tabid
		WHERE vtiger_profile2tab.profileid=? AND vtiger_profile2tab.tabid=? AND vtiger_field.presence in (0,2)';
	$result = $adb->pquery($query, array($profileid, $tabid));

	$inssql = 'INSERT INTO vtiger_profile2field VALUES(?,?,?,?,?,?)';
	$selsql = 'SELECT vtiger_profile2field.visible, vtiger_profile2field.readonly, summary FROM vtiger_profile2field WHERE fieldid=? AND tabid=? AND profileid=?';
	$chksql = 'SELECT 1 FROM vtiger_profile2field WHERE profileid=? AND tabid=? AND fieldid =?';
	$return_data = array();
	while ($row = $adb->fetch_array($result)) {
		$checkentry = $adb->pquery($chksql, array($profileid, $tabid, $row['fieldid']));
		if ($adb->num_rows($checkentry) == 0) {
			$adb->pquery($inssql, array($profileid, $tabid, $row['fieldid'], 0, 0, 'B'));
		}
		$res = $adb->pquery($selsql, array($row['fieldid'], $tabid, $profileid));
		$moreinfo = $adb->fetch_array($res);
		$return_data[] = array(
			$row['fieldlabel'],
			$moreinfo['visible'], // From vtiger_profile2field.visible
			$row['uitype'],
			$moreinfo['readonly'], // From vtiger_profile2field.readonly
			$row['fieldid'],
			$row['displaytype'],
			$row['typeofdata'],
			$moreinfo['summary'], // From vtiger_profile2field.summary
		);
	}
	$log->debug('< getProfile2ModuleFieldPermissionList');
	return $return_data;
}

/** Function to getProfile2allfieldsListinsert value to profile2fieldPermissions table
 * @param string mod_array
 * @param integer profileid
 * @return string profilelist
 */
function getProfile2AllFieldList($mod_array, $profileid) {
	global $log;
	$log->debug('> getProfile2AllFieldList');
	$profilelist=array();
	foreach ($mod_array as $key => $value) {
		$profilelist[$key]=getProfile2ModuleFieldPermissionList($key, $profileid);
	}
	$log->debug('< getProfile2AllFieldList');
	return $profilelist;
}

/** Function to getdefaultfield organisation list for a given module
 * @param string module name
 * @return object
 */
function getDefOrgFieldList($fld_module) {
	global $log, $adb;
	$log->debug('> getDefOrgFieldList '.$fld_module);

	$tabid = getTabid($fld_module);

	$query = 'select vtiger_def_org_field.visible,vtiger_field.*
		from vtiger_def_org_field
		inner join vtiger_field on vtiger_field.fieldid=vtiger_def_org_field.fieldid
		where vtiger_def_org_field.tabid=? and vtiger_field.presence in (0,2)';
	$qparams = array($tabid);
	$result = $adb->pquery($query, $qparams);
	$log->debug('< getDefOrgFieldList');
	return $result;
}

/** Function to getQuickCreate for a given tabid
 * @param string tab id
 * @param integer action id
 * @return boolean QuickCreateForm
 */
function getQuickCreate($tabid, $actionid) {
	global $log;
	$log->debug('> getQuickCreate '.$tabid.','.$actionid);
	$module=getTabModuleName($tabid);
	$actionname=getActionname($actionid);
	$QuickCreateForm= 'true';

	$perr=isPermitted($module, $actionname);
	if ($perr == 'no') {
		$QuickCreateForm= 'false';
	}
	$log->debug('< getQuickCreate');
	return $QuickCreateForm;
}

/** Function to get unitprice for a given product id
 * @param integer product id
 * @return string unit price
 */
function getUnitPrice($productid, $module = 'Products') {
	global $log, $adb;
	$log->debug('> getUnitPrice '.$productid.','.$module);

	if ($module == 'Services') {
		$query = 'select unit_price from vtiger_service where serviceid=?';
	} else {
		$query = 'select unit_price from vtiger_products where productid=?';
	}
	$result = $adb->pquery($query, array($productid));
	$unitpice = $adb->query_result($result, 0, 'unit_price');
	$log->debug('< getUnitPrice');
	return $unitpice;
}

/** Function to upload product image file
 * @param string mode
 * @param integer id
 * @return array return array
 * @deprecated
 */
function upload_product_image_file($mode, $id) {
	global $log, $root_directory;
	$log->debug('> upload_product_image_file '.$mode.','.$id);
	$uploaddir = $root_directory .'/cache/';

	$file_path_name = $_FILES['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = $_REQUEST['imagename_hidden'];
	} else {
		//allowed file pathname like UTF-8 Character
		$file_name = ltrim(basename(' '.$file_path_name)); // basename($file_path_name);
	}
	$file_name = $id.'_'.$file_name;
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
	$log->debug('< upload_product_image_file');
	return $ret_array;
}

/** Function to upload product image file
 * @param integer product crmid
 * @param array images to be deleted
 * @return array image list
 */
function getProductImageName($id, $deleted_array = array()) {
	global $log, $adb;
	$log->debug('> getProductImageName '.$id);
	$image_array=array();
	$result = $adb->pquery('select imagename from vtiger_products where productid=?', array($id));
	$image_name = $adb->query_result($result, 0, 'imagename');
	$image_array=explode('###', $image_name);
	if (count($deleted_array)>0) {
		$resultant_image = array();
		$resultant_image=array_merge(array_diff($image_array, $deleted_array));
		$imagelists=implode('###', $resultant_image);
		$retval = $imagelists;
	} else {
		$retval = $image_name;
	}
	$log->debug('< getProductImageName');
	return $retval;
}

/** Function to get Contact images
 * @param integer id
 * @return string imagename
 */
function getContactImageName($id) {
	global $log, $adb;
	$log->debug('> getContactImageName '.$id);
	$result = $adb->pquery('select imagename from vtiger_contactdetails where contactid=?', array($id));
	$image_name = $adb->query_result($result, 0, 'imagename');
	$log->debug('< getContactImageName');
	return $image_name;
}

/** Function to update sub total in inventory
 * @param string module name
 * @param string table name
 * @param string column name
 * @param string column name 1
 * @param string entity field
 * @param integer entity id
 * @param integer total product
 */
function updateSubTotal($module, $tablename, $colname, $colname1, $entid_fld, $entid, $prod_total) {
	global $log, $adb;
	$log->debug('> updateSubTotal '.$module.','.$tablename.','.$colname.','.$colname1.','.$entid_fld.','.$entid.','.$prod_total);
	//getting the subtotal
	$query = 'select '.$colname.','.$colname1.' from '.$tablename.' where '.$entid_fld.'=?';
	$result1 = $adb->pquery($query, array($entid));
	$subtot = $adb->query_result($result1, 0, $colname);
	$subtot_upd = $subtot - $prod_total;

	$gdtot = $adb->query_result($result1, 0, $colname1);
	$gdtot_upd = $gdtot - $prod_total;

	//updating the subtotal
	$sub_query = "update $tablename set $colname=?, $colname1=? where $entid_fld=?";
	$adb->pquery($sub_query, array($subtot_upd, $gdtot_upd, $entid));
	$log->debug('< updateSubTotal');
}

/** Function to get Inventory Total
 * @param string return module
 * @param integer entity id
 * @return integer total
 * *** FUNCTION NOT USED IN THE APPLICATION > left only in case it is used by some extension
 */
function getInventoryTotal($return_module, $id) {
	global $log, $adb;
	$log->debug('> getInventoryTotal '.$return_module.','.$id);
	if ($return_module == 'Potentials') {
		$query ='select vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_seproductsrel.*
			from vtiger_products
			inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid
			where crmid=?';
	} elseif ($return_module == 'Products') {
		$mod = CRMEntity::getInstance($return_module);
		$query='select vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_crmentity.*
			from vtiger_products
			inner join '.$mod->crmentityTable.' as vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid
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
	$log->debug('< getInventoryTotal');
	return $total;
}

/** Function to update product quantity
 * @param integer product id
 * @param integer quantity
 */
function updateProductQty($product_id, $upd_qty) {
	global $log, $adb;
	$log->debug('> updateProductQty '.$product_id.','. $upd_qty);
	$adb->pquery('update vtiger_products set qtyinstock=? where productid=?', array($upd_qty, $product_id));
	$log->debug('< updateProductQty');
}

/** Function to get account information
 * @param integer parent id
 * @return integer accountid
 */
function get_account_info($parent_id) {
	global $log, $adb;
	$log->debug('> get_account_info '.$parent_id);
	$result = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($parent_id));
	$accountid=$adb->query_result($result, 0, 'related_to');
	$log->debug('< get_account_info');
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
 * @param string module name
 * @param integer entity id
 * @return string hidden
 */
//Added to get the parents list as hidden for Emails -- 09-11-2005
function getEmailParentsList($module, $id, $focus = false) {
	global $log, $adb;
	$log->debug('> getEmailParentsList '.$module.','.$id);
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

	$log->debug('< getEmailParentsList');
	return $hidden;
}

/** This Function returns the current status of the specified Purchase Order.
 *  @param integer Purchase Order Id
 */
function getPoStatus($po_id) {
	global $log, $adb;
	$log->debug('> getPoStatus '.$po_id);
	$result = $adb->pquery('select postatus from vtiger_purchaseorder where purchaseorderid=?', array($po_id));
	$po_status = $adb->query_result($result, 0, 'postatus');
	$log->debug('< getPoStatus');
	return $po_status;
}

/** This Function adds the specified product quantity to the Product Quantity in Stock in the Warehouse
 * @param integer Product Id
 * @param integer Quantity to be added
 */
function addToProductStock($productId, $qty) {
	global $log, $adb;
	$log->debug('> addToProductStock '.$productId.','.$qty);
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck + $qty;
	$adb->pquery('UPDATE vtiger_products set qtyinstock=? where productid=?', array($updQty, $productId));
	$log->debug('< addToProductStock');
}

/** This Function adds the specified product quantity to the Product Quantity in Demand in the Warehouse
 * @param integer Product Id
 * @param integer Quantity to be added
 */
function addToProductDemand($productId, $qty) {
	global $log, $adb;
	$log->debug('> addToProductDemand '.$productId.','.$qty);
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck + $qty;
	$adb->pquery('UPDATE vtiger_products set qtyindemand=? where productid=?', array($updQty, $productId));
	$log->debug('< addToProductDemand');
}

/** This Function subtract the specified product quantity to the Product Quantity in Stock in the Warehouse
 * @param integer Product Id
 * @param integer Quantity to be subtracted
 */
function deductFromProductStock($productId, $qty) {
	global $log, $adb;
	$log->debug('> deductFromProductStock '.$productId.','.$qty);
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck - $qty;
	$adb->pquery('UPDATE vtiger_products set qtyinstock=? where productid=?', array($updQty, $productId));
	$log->debug('< deductFromProductStock');
}

/** This Function subtract the specified product quantity to the Product Quantity in Demand in the Warehouse
 * @param integer Product Id
 * @param integer Quantity to be subtract
 */
function deductFromProductDemand($productId, $qty) {
	global $log, $adb;
	$log->debug('> deductFromProductDemand '.$productId.','.$qty);
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck - $qty;
	$adb->pquery('UPDATE vtiger_products set qtyindemand=? where productid=?', array($updQty, $productId));
	$log->debug('< deductFromProductDemand');
}

/** This Function returns the current product quantity in stock
 *  @param integer Product Id
 *  @return integer quantity in stock
 */
function getProductQtyInStock($product_id) {
	global $log, $adb;
	$log->debug('> getProductQtyInStock '.$product_id);
	$result=$adb->pquery('select qtyinstock from vtiger_products where productid=?', array($product_id));
	$qtyinstck= $adb->query_result($result, 0, 'qtyinstock');
	$log->debug('< getProductQtyInStock');
	return $qtyinstck;
}

/** This Function returns the current product quantity in demand.
 * @param integer Product Id
 * @return integer Quantity in Demand of a product
 */
function getProductQtyInDemand($product_id) {
	global $log, $adb;
	$log->debug('> getProductQtyInDemand '.$product_id);
	$result = $adb->pquery('select qtyindemand from vtiger_products where productid=?', array($product_id));
	$qtyInDemand = $adb->query_result($result, 0, 'qtyindemand');
	$log->debug('< getProductQtyInDemand');
	return $qtyInDemand;
}

/** Function to seperate the Date and Time
 * @param string with date and time
 * @return array of two elements.The first element contains the date and the second one contains the time
 */
function getDateFromDateAndtime($date_time) {
	global $log;
	$log->debug('> getDateFromDateAndtime '.$date_time);
	$result = explode(' ', $date_time);
	$log->debug('< getDateFromDateAndtime');
	return $result;
}

/** Function to get header for block in edit/create and detailview
 * @param string header label
 * @return string output
 */
function getBlockTableHeader($header_label) {
	global $log, $mod_strings;
	$log->debug('> getBlockTableHeader '.$header_label);
	$label = $mod_strings[$header_label];
	$output = $label;
	$log->debug('< getBlockTableHeader');
	return $output;
}

/** Function to get the table name from 'field' table for the input field based on the module
 * @param string current module value
 * @param string fieldname to which we want the tablename
 * @return string tablename in which $fieldname is a column, which is retrieved from 'field' table per $module basis
 */
function getTableNameForField($module, $fieldname) {
	global $log, $adb;
	$log->debug('> getTableNameForField '.$module.','.$fieldname);
	$tabid = getTabid($module);
	$sql = 'select tablename from vtiger_field where tabid=? and vtiger_field.presence in (0,2) and columnname=?';
	$res = $adb->pquery($sql, array($tabid, $fieldname));

	$tablename = '';
	if ($adb->num_rows($res) > 0) {
		$tablename = $adb->query_result($res, 0, 'tablename');
	}

	$log->debug('< getTableNameForField');
	return $tablename;
}

/** Function to get the module name of a 'field'
 * @param integer field id
 * @return string module name of the field
 */
function getModuleForField($fieldid) {
	global $log, $adb;
	$log->debug('> getModuleForField '.$fieldid);
	if ($fieldid == -1) {
		return 'Users';
	}
	$sql = 'SELECT vtiger_tab.name
		FROM vtiger_field
		INNER JOIN vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
		WHERE fieldid=?';
	$res = $adb->pquery($sql, array($fieldid));

	$modname = '';
	if ($adb->num_rows($res) > 0) {
		$modname = $adb->query_result($res, 0, 'name');
	}

	$log->debug('< getModuleForField');
	return $modname;
}

/** Function to get parent record owner
 * @param integer tabid
 * @param integer parent module id
 * @param integer record id
 * @return array parentRecOwner
 */
function getParentRecordOwner($tabid, $parModId, $record_id) {
	global $log;
	$log->debug('> getParentRecordOwner '.$tabid.','.$parModId.','.$record_id);
	$parentRecOwner=array();
	$parentTabName=getTabname($parModId);
	$relTabName=getTabname($tabid);
	$fn_name='get'.$relTabName.'Related'.$parentTabName;
	$ent_id=$fn_name($record_id);
	if ($ent_id != '') {
		$parentRecOwner=getRecordOwnerId($ent_id);
	}
	$log->debug('< getParentRecordOwner');
	return $parentRecOwner;
}

/** Function to get potential related accounts
 * @param integer record id
 * @return integer accountid
 */
function getPotentialsRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getPotentialsRelatedAccounts '.$record_id);
	$result=$adb->pquery('select related_to from vtiger_potential where potentialid=?', array($record_id));
	$accountid=$adb->query_result($result, 0, 'related_to');
	$log->debug('< getPotentialsRelatedAccounts');
	return $accountid;
}

/** Function to get email related accounts
 * @param integer record id
 * @return integer accountid
 */
function getEmailsRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getEmailsRelatedAccounts '.$record_id);
	$mod = CRMEntity::getInstance('Emails');
	$query = "select vtiger_seactivityrel.crmid
		from vtiger_seactivityrel
		inner join ".$mod->crmentityTable." as vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid
		where vtiger_crmentity.setype='Accounts' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'crmid');
	$log->debug('< getEmailsRelatedAccounts');
	return $accountid;
}

/** Function to get email related Leads
 * @param integer record id
 * @return integer leadid
 */
function getEmailsRelatedLeads($record_id) {
	global $log, $adb;
	$log->debug('> getEmailsRelatedLeads '.$record_id);
	$mod = CRMEntity::getInstance('Emails');
	$query = "select vtiger_seactivityrel.crmid
		from vtiger_seactivityrel
		inner join ".$mod->crmentityTable." as vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid
		where vtiger_crmentity.setype='Leads' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$leadid=$adb->query_result($result, 0, 'crmid');
	$log->debug('< getEmailsRelatedLeads');
	return $leadid;
}

/** Function to get HelpDesk related Accounts
 * @param integer record id
 * @return integer accountid
 */
function getHelpDeskRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getHelpDeskRelatedAccounts '.$record_id);
	$mod = CRMEntity::getInstance('HelpDesk');
	$query="select parent_id
		from vtiger_troubletickets
		inner join ".$mod->crmentityTable." as vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.parent_id
		where ticketid=? and vtiger_crmentity.setype='Accounts'";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result, 0, 'parent_id');
	$log->debug('< getHelpDeskRelatedAccounts');
	return $accountid;
}

/** Function to get Quotes related Accounts
 * @param integer record id
 * @return integer accountid
 */
function getQuotesRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getQuotesRelatedAccounts '.$record_id);
	$result=$adb->pquery('select accountid from vtiger_quotes where quoteid=?', array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug('< getQuotesRelatedAccounts');
	return $accountid;
}

/** Function to get Quotes related Potentials
 * @param integer record id
 * @return integer potential id
 */
function getQuotesRelatedPotentials($record_id) {
	global $log, $adb;
	$log->debug('> getQuotesRelatedPotentials '.$record_id);
	$result=$adb->pquery('select potentialid from vtiger_quotes where quoteid=?', array($record_id));
	$potid=$adb->query_result($result, 0, 'potentialid');
	$log->debug('< getQuotesRelatedPotentials');
	return $potid;
}

/** Function to get Quotes related Potentials
 * @param integer record id
 * @return integer accountid
 */
function getSalesOrderRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getSalesOrderRelatedAccounts '.$record_id);
	$result=$adb->pquery('select accountid from vtiger_salesorder where salesorderid=?', array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug('< getSalesOrderRelatedAccounts');
	return $accountid;
}

/** Function to get SalesOrder related Potentials
 * @param integer record id
 * @return integer potential id
 */
function getSalesOrderRelatedPotentials($record_id) {
	global $log, $adb;
	$log->debug('> getSalesOrderRelatedPotentials '.$record_id);
	$result=$adb->pquery('select potentialid from vtiger_salesorder where salesorderid=?', array($record_id));
	$potid=$adb->query_result($result, 0, 'potentialid');
	$log->debug('< getSalesOrderRelatedPotentials');
	return $potid;
}

/** Function to get SalesOrder related Quotes
 * @param integer record id
 * @return integer quote id
 */
function getSalesOrderRelatedQuotes($record_id) {
	global $log, $adb;
	$log->debug('> getSalesOrderRelatedQuotes '.$record_id);
	$result=$adb->pquery('select quoteid from vtiger_salesorder where salesorderid=?', array($record_id));
	$qtid=$adb->query_result($result, 0, 'quoteid');
	$log->debug('< getSalesOrderRelatedQuotes');
	return $qtid;
}

/** Function to get Invoice related Accounts
 * @param $record_id -- record id :: Type integer
 * @return $accountid -- accountid:: Type integer
 */
function getInvoiceRelatedAccounts($record_id) {
	global $log, $adb;
	$log->debug('> getInvoiceRelatedAccounts '.$record_id);
	$result=$adb->pquery('select accountid from vtiger_invoice where invoiceid=?', array($record_id));
	$accountid=$adb->query_result($result, 0, 'accountid');
	$log->debug('< getInvoiceRelatedAccounts');
	return $accountid;
}

/** Function to get Invoice related SalesOrder
 * @param integer record id
 * @return integer salesorder id
 */
function getInvoiceRelatedSalesOrder($record_id) {
	global $log, $adb;
	$log->debug('> getInvoiceRelatedSalesOrder '.$record_id);
	$result=$adb->pquery('select salesorderid from vtiger_invoice where invoiceid=?', array($record_id));
	$soid=$adb->query_result($result, 0, 'salesorderid');
	$log->debug('< getInvoiceRelatedSalesOrder');
	return $soid;
}

/** Function to get Days and Dates in between the dates specified
 * @param string start date
 * @param string end date
 * @return integer number of days in given range
 */
function get_days_n_dates($st, $en) {
	global $log;
	$log->debug('> get_days_n_dates '.$st.','.$en);
	$stdate_arr=explode('-', $st);
	$endate_arr=explode('-', $en);

	$dateDiff = mktime(0, 0, 0, $endate_arr[1], $endate_arr[2], $endate_arr[0]) - mktime(0, 0, 0, $stdate_arr[1], $stdate_arr[2], $stdate_arr[0]);//get dates difference

	$days = floor($dateDiff/60/60/24)+1; //to calculate no of. days
	for ($i=0; $i<$days; $i++) {
		$day_date[] = date('Y-m-d', mktime(0, 0, 0, date("$stdate_arr[1]"), (date("$stdate_arr[2]")+($i)), date("$stdate_arr[0]")));
	}
	if (!isset($day_date)) {
		$day_date=0;
	}
	$nodays_dates=array($days,$day_date);
	$log->debug('< get_days_n_dates');
	return $nodays_dates; //passing no of days , days in between the days
}

/** Function to get the start and end dates based upon the given period
 * @param string date period specification: tweek, lweek, tmon, lmon
 * @return array start, end, type, width, height
 */
function start_end_dates($period) {
	global $log;
	$log->debug('> start_end_dates '.$period);
	$st_thisweek= date('Y-m-d', mktime(0, 0, 0, date('n'), (date('j')-date('w')), date('Y')));
	if ($period=='tweek') {
		$st_date= date('Y-m-d', mktime(0, 0, 0, date('n'), (date('j')-date('w')), date('Y')));
		$end_date = date('Y-m-d', mktime(0, 0, 0, date('n'), (date('j')-1), date('Y')));
		$st_week= date('w', mktime(0, 0, 0, date('n'), date('j'), date('Y')));
		if ($st_week==0) {
			$start_week=explode('-', $st_thisweek);
			$st_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-7), date("$start_week[0]")));
			$end_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-1), date("$start_week[0]")));
		}
		$period_type='week';
		$width='360';
	} elseif ($period=='lweek') {
		$start_week=explode('-', $st_thisweek);
		$st_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-7), date("$start_week[0]")));
		$end_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-1), date("$start_week[0]")));
		$st_week= date('w', mktime(0, 0, 0, date('n'), date('j'), date('Y')));
		if ($st_week==0) {
			$start_week=explode('-', $st_thisweek);
			$st_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-14), date("$start_week[0]")));
			$end_date = date('Y-m-d', mktime(0, 0, 0, date("$start_week[1]"), (date("$start_week[2]")-8), date("$start_week[0]")));
		}
		$period_type='week';
		$width='360';
	} elseif ($period=='tmon') {
		$period_type='month';
		$width='840';
		$st_date = date('Y-m-d', mktime(0, 0, 0, date('m'), '01', date('Y')));
		$end_date = date('Y-m-t');
	} elseif ($period=='lmon') {
		$st_date=date('Y-m-d', mktime(0, 0, 0, date('n')-1, date('1'), date('Y')));
		$end_date = date('Y-m-d', mktime(0, 0, 1, date('n'), 0, date('Y')));
		$period_type='month';
		$start_month=date('d', mktime(0, 0, 0, date('n'), date('j'), date('Y')));
		if ($start_month==1) {
			$st_date=date('Y-m-d', mktime(0, 0, 0, date('n')-2, date('1'), date('Y')));
			$end_date = date('Y-m-d', mktime(0, 0, 1, date('n')-1, 0, date('Y')));
		}
		$width='840';
	} else {
		$curr_date=date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
		$today_date=explode('-', $curr_date);
		$lastday_date=date('Y-m-d', mktime(0, 0, 0, date("$today_date[1]"), date("$today_date[2]")-1, date("$today_date[0]")));
		$st_date=$lastday_date;
		$end_date=$lastday_date;
		$period_type='yday';
		$width='250';
	}
	if ($period_type=='yday') {
		$height='160';
	} else {
		$height='250';
	}
	$datevalues=array($st_date,$end_date,$period_type,$width,$height);
	$log->debug('< start_end_dates');
	return $datevalues;
}

/** Function to get the Graph and table format for a particular date based upon the period */
function Graph_n_table_format($period_type, $date_value) {
	global $log;
	$log->debug('> Graph_n_table_format '.$period_type.','.$date_value);
	$date_val=explode('-', $date_value);
	if ($period_type=='month') {  //to get the vtiger_table format dates
		$table_format=date('j', mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=date('D', mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
	} elseif ($period_type=='week') {
		$table_format=date('d/m', mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=date('D', mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
	} elseif ($period_type=='yday') {
		$table_format=date('j', mktime(0, 0, 0, date($date_val[1]), (date($date_val[2])), date($date_val[0])));
		$graph_format=$table_format;
	}
	$values=array($graph_format,$table_format);
	$log->debug('< Graph_n_table_format');
	return $values;
}

/** Function to get image count for a given product
 * @param integer product id
 * @return integer count
 */
function getImageCount($id) {
	global $log, $adb;
	$log->debug('> getImageCount '.$id);
	$image_lists=array();
	$result=$adb->pquery('select imagename from vtiger_products where productid=?', array($id));
	$imagename=$adb->query_result($result, 0, 'imagename');
	$image_lists=explode('###', $imagename);
	$log->debug('< getImageCount');
	return count($image_lists);
}

/** Function to get user image for a given user
 * @param integer user id
 * @return string image name
 */
function getUserImageName($id) {
	global $log, $adb;
	$log->debug('> getUserImageName '.$id);
	$result = $adb->pquery('select imagename from vtiger_users where id=?', array($id));
	$image_name = $adb->query_result($result, 0, 'imagename');
	$log->debug('< getUserImageName '.$image_name);
	return $image_name;
}

/** Function to get all user images for displaying it in listview
 * @return array image name
 */
function getUserImageNames() {
	global $log, $adb;
	$log->debug('> getUserImageNames');
	$result = $adb->pquery('select imagename from vtiger_users where deleted=0', array());
	$image_name=array();
	for ($i=0; $i<$adb->num_rows($result); $i++) {
		if ($adb->query_result($result, $i, 'imagename')!='') {
			$image_name[] = $adb->query_result($result, $i, 'imagename');
		}
	}
	if (!empty($image_name)) {
		$log->debug('< getUserImageNames');
		return $image_name;
	}
}

/** Function to remove the script tag in the contents
 */
function strip_selected_tags($text, $tags = array()) {
	$args = func_get_args();
	array_shift($args);
	$tags = func_num_args() > 2 ? array_diff($args, array($text)) : (array)$tags;
	foreach ($tags as $tag) {
		if (preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)) {
			$text = str_replace($found[0], $found[1], $text);
		}
	}
	return $text;
}

/** Function to check whether user has opted for internal mailer
 * @return boolean int mailer
 */
function useInternalMailer() {
	global $current_user,$adb;
	$rs = $adb->pquery('select int_mailer from vtiger_mail_accounts where user_id=?', array($current_user->id));
	return $adb->query_result($rs, 0, 'int_mailer');
}

/**
 * the function is like unescape in javascript added for picklist editor
 */
function utf8RawUrlDecode($source) {
	global $default_charset;
	$decodedStr = '';
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
				$entity = '&#'. $unicode . ';';
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
				$ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, '0'), 2, 10) + (($data - $a) / $p));
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
		'_picklist_' => array(15, 16, 63, 115, 357),
		'_users_list_' => array(52, 53, 77, 98, 101,),
	);
	return ($ui_type_arr[$reqtype] != null && in_array($uitype, $ui_type_arr[$reqtype]));
}

/**
 * Function to escape quotes
 * @param string in which single quotes have to be replaced
 * @return string with single quotes escaped
 */
function escape_single_quotes($value) {
	if (isset($value)) {
		$value = addslashes($value);
	}
	return $value;
}

/**
 * Function to format the input value for SQL like clause.
 * @param string Input string value to be formatted.
 * @param integer By default set to 0 (Will look for cases %string%).
 *                If set to 1 - Will look for cases %string.
 *                If set to 2 - Will look for cases string%.
 * @return string formatted as per the SQL like clause requirement
 */
function formatForSqlLike($str, $flag = 0, $is_field = false) {
	global $adb;
	if (isset($str)) {
		if (!$is_field) {
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

/**
 * Function used to get all the picklists and their values for a module
 * @param string module name to which the list of picklists and their values needed
 * @return array array of picklists and their values
 */
function getAccessPickListValues($module) {
	global $adb, $log, $current_user;
	$log->debug('> getAccessPickListValues '.$module);

	$id = getTabid($module);
	$query = "select fieldname,columnname,fieldid,fieldlabel,tabid,uitype
		from vtiger_field
		where tabid = ? and uitype in ('15','33') and vtiger_field.presence in (0,2)";
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
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		if ($fieldname == 'firstname') {
			continue;
		}
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$columnname = $adb->query_result($result, $i, 'columnname');
		$tabid = $adb->query_result($result, $i, 'tabid');
		$uitype = $adb->query_result($result, $i, 'uitype');

		$keyvalue = $columnname;
		$fieldvalues = array();
		if (count($roleids) > 1) {
			$mulsel="select distinct $fieldname,sortid
				from vtiger_$fieldname
				inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid
				where roleid in (\"". implode("\",\"", $roleids) ."\") and picklistid in (select picklistid from vtiger_picklist) order by sortid asc";
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
		} elseif ($uitype == 15) {
			$fieldlists[$keyvalue] = $fieldvalues;
		}
	}
	$log->debug('< getAccessPickListValues');
	return $fieldlists;
}

/** Search for value in the picklist and return if it is present or not
 * @param string value to search in the picklist
 * @param string picklist name where we will search
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
		foreach ($id_array as $crmid) {
			$focus->id=$crmid;
			$focus->retrieve_entity_info($crmid, $module);
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
			for ($j=0, $jMax = count($field_values); $j < $jMax; $j++) {
				if ($ui_type ==56) {
					if ($field_values[$j][$fld_name] == 0) {
						$value_pair['disp_value']=$app_strings['no'];
					} else {
						$value_pair['disp_value']=$app_strings['yes'];
					}
				} elseif ($ui_type == 53) {
					$owner_id=$field_values[$j][$fld_name];
					$ownername=getOwnerName($owner_id);
					$value_pair['disp_value']=$ownername;
				} elseif ($ui_type == 52) {
					$user_id = $field_values[$j][$fld_name];
					$user_name=getUserFullName($user_id);
					$value_pair['disp_value']=$user_name;
				} elseif ($ui_type == 10) {
					$value_pair['disp_value'] = getRecordInfoFromID($field_values[$j][$fld_name]);
				} elseif ($ui_type == 5 || $ui_type == 6 || $ui_type == 23) {
					if ($field_values[$j][$fld_name] != '' && $field_values[$j][$fld_name] != '0000-00-00') {
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
	$tbl_col_fld = explode(',', $field_values);
	$i=0;
	$tbl = $cols = $fields = $tbl_cols = array();
	foreach ($tbl_col_fld as $val) {
		list($tbl[$i], $cols[$i], $fields[$i]) = explode('.', $val);
		$tbl_cols[$i] = $tbl[$i]. '.' . $cols[$i];
		$i++;
	}
	$table_cols = implode(',', $tbl_cols);
	$modObj = CRMEntity::getInstance($module);
	$nquery = '';
	if ($modObj != null && method_exists($modObj, 'getDuplicatesQuery')) {
		$nquery = $modObj->getDuplicatesQuery($module, $table_cols, $field_values, $ui_type_arr);
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
	$bmapname = $module.'_ListColumns';
	$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
	if ($cbMapid) {
		$cbMap = cbMap::getMapByID($cbMapid);
		$cbMapLC = $cbMap->ListColumns()->getDeduplcationFields();
	}
	$dup_query = getDuplicateQuery($module, $field_values, $ui_type);
	// added for page navigation
	$count_res = $adb->query(mkCountQuery($dup_query, false));
	$no_of_rows = $adb->query_result($count_res, 0, 'count');

	if ($no_of_rows <= $list_max_entries_per_page) {
		coreBOS_Session::set('dup_nav_start'.$module, 1);
	} elseif (isset($_REQUEST['start']) && $_REQUEST['start']!='' && (empty($_SESSION['dup_nav_start'.$module]) || $_SESSION['dup_nav_start'.$module]!=$_REQUEST['start'])) {
		coreBOS_Session::set('dup_nav_start'.$module, ListViewSession::getRequestStartPage());
	}
	$start = (!empty($_SESSION['dup_nav_start'.$module]) ? $_SESSION['dup_nav_start'.$module] : 1);
	$navigation_array = getNavigationValues($start, $no_of_rows, $list_max_entries_per_page);
	$start_rec = $navigation_array['start'];
	$navigationOutput = getTableHeaderNavigation($navigation_array, '', $module, 'FindDuplicate', '');
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
			$smarty = new vtigerCRM_Smarty();
			$smarty->assign('APP', $app_strings);
			$smarty->assign('ERROR_MESSAGE_CLASS', 'cb-alert-info');
			$smarty->assign('OPERATION_MESSAGE', $app_strings['LBL_NO_DUPLICATE']);
			$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
			exit();
		} else {
			echo "<br><br><table align='center' class='reportCreateBottom big' width='95%'><tr><td align='center'>".$app_strings['LBL_NO_DUPLICATE'].'</td></tr></table>';
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
			$grp = 'group'.$gcnt;
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
			if ($ui_type[$fld_arr[$k]] == 15 || $ui_type[$fld_arr[$k]] == 16) {
				$result[$col_arr[$k]]=getTranslatedString($result[$col_arr[$k]], $module);
			}
			if ($ui_type[$fld_arr[$k]] == 33) {
				$fieldvalue = explode(Field_Metadata::MULTIPICKLIST_SEPARATOR, $result[$col_arr[$k]]);
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
		if (isset($cbMapLC)) {
			$modObj = CRMEntity::getInstance($module);
			foreach ($cbMapLC['ListFields'] as $label => $field) {
				$modObj->retrieve_entity_info($result['recordid'], $module);
				$fld_values[$grp][$ii][$label] = $modObj->column_fields[$field];
			}
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
			$del_response=DeleteEntity($module, $module, $focus, $id, '');
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
	$field_array = explode(',', $field_list);
	$ret_str = '';
	$sel_clause = '';
	$i=1;
	$cnt = count($field_array);
	$spl_chk = ($_REQUEST['modulename'] != '')?$_REQUEST['modulename']:$_REQUEST['module'];
	foreach ($field_array as $fld) {
		$sub_arr = explode('.', $fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];

		//need to handle aditional conditions with sub tables for further modules of duplicate check
		if ($tbl_name == 'vtiger_leadsubdetails' || $tbl_name == 'vtiger_contactsubdetails') {
			$tbl_alias = 'subd';
		} elseif ($tbl_name == 'vtiger_leadaddress' || $tbl_name == 'vtiger_contactaddress') {
			$tbl_alias = 'addr';
		} elseif ($tbl_name == 'vtiger_account' && $spl_chk == 'Contacts') {
			$tbl_alias = 'acc';
		} elseif ($tbl_name == 'vtiger_accountbillads') {
			$tbl_alias = 'badd';
		} elseif ($tbl_name == 'vtiger_accountshipads') {
			$tbl_alias = 'sadd';
		} elseif ($tbl_name == 'vtiger_crmentity') {
			$tbl_alias = 'crm';
		} elseif ($tbl_name == 'vtiger_customerdetails') {
			$tbl_alias = 'custd';
		} elseif ($tbl_name == 'vtiger_contactdetails' && $spl_chk == 'HelpDesk') {
			$tbl_alias = 'contd';
		} elseif (stripos($tbl_name, 'cf') === (strlen($tbl_name) - strlen('cf'))) {
			$tbl_alias = 'tcf'; // Custom Field Table Prefix to use in subqueries
		} else {
			$tbl_alias = 't';
		}

		$sel_clause .= $tbl_alias.'.'.$col_name.',';
		$ret_str .= " $tbl_name.$col_name = $tbl_alias.$col_name";
		if ($cnt != $i) {
			$ret_str .= ' and ';
		}
		$i++;
	}
	$ret_arr['on_clause'] = $ret_str;
	$ret_arr['sel_clause'] = trim($sel_clause, ',');
	return $ret_arr;
}

/** Function to get on clause criteria for duplicate check queries */
function get_on_clause($field_list, $uitype_arr, $module) {
	$field_array = explode(',', $field_list);
	$ret_str = '';
	$i=1;
	foreach ($field_array as $fld) {
		$sub_arr = explode('.', $fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];

		$ret_str .= " ifnull($tbl_name.$col_name,'null') = ifnull(temp.$col_name,'null')";

		if (count($field_array) != $i) {
			$ret_str .= ' and ';
		}
		$i++;
	}
	return $ret_str;
}

function elimina_acentos($cadena) {
	$tofind = utf8_decode('ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊẼËèéêẽëÌÍĨÎÏìíîĩïÙÚÛŨÜúùûũüÿçÇºªñÑ');
	$replac = 'AAAAAAaaaaaaOOOOOOooooooEEEEEeeeeeIIIIIiiiiiUUUUUuuuuuycCoanN';
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
			$sub_res .= $rows[$id].',';
		}
		$sub_res = trim($sub_res, ',');
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

	$fieldname_query='select fieldname,fieldlabel,uitype,tablename,columnname from vtiger_field where fieldid in
		(select fieldid from vtiger_user2mergefields WHERE tabid=? AND userid=? AND visible=?) and vtiger_field.presence in (0,2)';
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
		$table_col = $tablename.'.'.$column_name;
		if (getFieldVisibilityPermission($module, $current_user->id, $field_name) == 0) {
			$fld_name = (!empty($special_fld_arr[$field_name]))?$special_fld_arr[$field_name]:$field_name;

			$fld_arr[] = $fld_name;
			$col_arr[] = $column_name;
			if (!empty($fld_table_arr[$table_col])) {
				$table_col = $fld_table_arr[$table_col];
			}

			$field_values_array['fieldnames_list'][] = $table_col . '.' . $fld_name;
			$fld_labl_arr[]=$field_lbl;
			$uitype[$field_name]=$ui_type;
		}
	}
	$field_values_array['fieldnames_list']=implode(',', $field_values_array['fieldnames_list']);
	$field_values=implode(',', $fld_arr);
	$field_values_array['fieldnames']=$field_values;
	$field_values_array['fieldnames_array']=$fld_arr;
	$field_values_array['columnnames_array']=$col_arr;
	$field_values_array['fieldlabels_array']=$fld_labl_arr;
	$field_values_array['fieldname_uitype']=$uitype;

	return $field_values_array;
}

/** To get security parameter for a particular module
 * @deprecated
 */
function getSecParameterforMerge($module) {
	global $current_user;
	$sec_parameter='';
	$userprivs = $current_user->getPrivileges();
	if (!$userprivs->hasGlobalReadPermission() && !$userprivs->hasModuleReadSharing(getTabid($module))) {
		$sec_parameter=getListViewSecurityParameter($module);
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
	$log->debug('> transferUserCurrency');
	$adb->pquery('update vtiger_users set currency_id=? where currency_id=?', array($new_cur, $old_cur));
	$current_user->retrieve_entity_info($current_user->id, 'Users');
	$log->debug('< transferUserCurrency');
}

// Function to transfer the products with currency $old_cur to $new_cur as currency
function transferProductCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug('> transferProductCurrency');
	$prod_res = $adb->pquery('select productid from vtiger_products where currency_id = ?', array($old_cur));
	$numRows = $adb->num_rows($prod_res);
	$prod_ids = array();
	for ($i=0; $i<$numRows; $i++) {
		$prod_ids[] = $adb->query_result($prod_res, $i, 'productid');
	}
	if (!empty($prod_ids)) {
		$prod_price_list = getPricesForProducts($new_cur, $prod_ids);
		$query = 'update vtiger_products set currency_id=?, unit_price=? where productid=?';
		foreach ($prod_ids as $product_id) {
			$adb->pquery($query, array($new_cur, $prod_price_list[$product_id], $product_id));
		}
	}
	$log->debug('< transferProductCurrency');
}

// Function to transfer the pricebooks with currency $old_cur to $new_cur as currency
// and to update the associated products with list price in $new_cur currency
function transferPriceBookCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug('> transferPriceBookCurrency');
	$pb_res = $adb->pquery('select pricebookid from vtiger_pricebook where currency_id=?', array($old_cur));
	$numRows = $adb->num_rows($pb_res);
	$pb_ids = array();
	for ($i=0; $i<$numRows; $i++) {
		$pb_ids[] = $adb->query_result($pb_res, $i, 'pricebookid');
	}

	if (!empty($pb_ids)) {
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

	$log->debug('< transferPriceBookCurrency');
}

//functions for asterisk integration start
/**
 * this function returns the caller name based on the phone number that is passed to it
 * @param string the number which is calling
 * @return string caller information in name(type) format :: for e.g. Mary Smith (Contact)
 * if no information is present in database, it returns :: Unknown Caller (Unknown)
 */
function getCallerName($from) {
	//information found
	$callerInfo = getCallerInfo($from);

	if ($callerInfo) {
		$callerName = decode_html($callerInfo['name']);
		$module = $callerInfo['module'];
		$callerModule = " (<a href='index.php?module=$module&action=index'>$module</a>)";
		$callerID = $callerInfo['id'];

		$caller = "<a href='index.php?module=$module&action=DetailView&record=$callerID'>$callerName</a>$callerModule";
	} else {
		$caller = "<br>
			<a target='_blank' href='index.php?module=Leads&action=EditView&phone=$from'>".getTranslatedString('LBL_CREATE_LEAD')."</a><br>
			<a target='_blank' href='index.php?module=Contacts&phone=$from'>".getTranslatedString('LBL_CREATE_CONTACT')."</a><br>
			<a target='_blank' href='index.php?module=Accounts&action=EditView&phone=$from'>".getTranslatedString('LBL_CREATE_ACCOUNT').'</a>';
	}
	return $caller;
}

/**
 * this function searches for a given number and returns the callerInfo in an array format
 * currently the search is made across only leads, accounts and contacts modules
 *
 * @param string the number whose information you want
 * @return array in format array(name=>callername, module=>module, id=>id);
 */
function getCallerInfo($number) {
	global $adb;
	if (empty($number)) {
		return false;
	}
	$pbxNumberSeparator = GlobalVariable::getVariable('PBX_callerNumberSeparator', '', 'PBXManager');
	if ($pbxNumberSeparator=='') {
		$numArray = (array)$number;
	} else {
		$numArray = explode($pbxNumberSeparator, $number);
	}
	$fieldsString = GlobalVariable::getVariable('PBX_SearchOnTheseFields', '', 'PBXManager');
	if ($fieldsString != '') {
		$fieldsArray = explode(',', $fieldsString);
		foreach ($numArray as $number) {
			foreach ($fieldsArray as $field) {
				$result = $adb->pquery('SELECT tabid, uitype FROM vtiger_field WHERE columnname=?', array($field));
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
		}
	}
	$name = array('Contacts', 'Accounts', 'Leads');
	foreach ($name as $module) {
		foreach ($numArray as $number) {
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
 * @param object peardatabase type object
 * @param string module name for which you want the array
 * @return array(tablename1=>primarykey1,.....)
 */
function get_tab_name_index($adb, $module) {
	$tabid = getTabid($module);
	$result = $adb->pquery('select * from vtiger_tab_name_index where tabid = ?', array($tabid));
	$count = $adb->num_rows($result);
	$data = array();

	for ($i=0; $i<$count; $i++) {
		$tablename = $adb->query_result($result, $i, 'tablename');
		$primaryKey = $adb->query_result($result, $i, 'primarykey');
		$data[$tablename] = $primaryKey;
	}
	return $data;
}

/**
 * this function returns the value of use_asterisk from the database for the current user
 * @param string the id of the current user
 */
function get_use_asterisk($id) {
	global $adb;
	if (!vtlib_isModuleActive('PBXManager') || isPermitted('PBXManager', 'index') == 'no') {
		return false;
	}
	$result = $adb->pquery('select * from vtiger_asteriskextensions where userid = ?', array($id));
	if ($adb->num_rows($result)>0) {
		$use_asterisk = $adb->query_result($result, 0, 'use_asterisk');
		$asterisk_extension = $adb->query_result($result, 0, 'asterisk_extension');
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
 * @param string the extension of the current user
 * @param string the caller number
 * @param string the called number
 * @param string the status of the call (outgoing/incoming/missed)
 * @param object the peardatabase object
 */
function addToCallHistory($userExtension, $callfrom, $callto, $status, $adb, $useCallerInfo, $pbxuuid) {
	$result = $adb->pquery('select userid from vtiger_asteriskextensions where asterisk_extension=?', array($userExtension));
	$userID = $adb->query_result($result, 0, 'userid');
	if (empty($userID)) {
		// call to extension not configured in application > return NULL
		return 0;
	}
	$crmID = $adb->getUniqueID('vtiger_crmentity');
	$timeOfCall = date('Y-m-d H:i:s');

	$adb->pquery(
		'insert into vtiger_crmentity values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
		array($crmID, $userID, $userID, 0, 'PBXManager', '', $timeOfCall, $timeOfCall, null, null, 0, 1, 0, $pbxuuid)
	);
	$adb->pquery('insert into vtiger_crmobject values (?,?,0,?,?,?)', array($crmID, $pbxuuid, 'PBXManager', $userID, $timeOfCall));
	$unknownCaller = GlobalVariable::getVariable('PBX_Unknown_CallerID', 'Unknown', 'PBXManager');
	if (empty($callfrom)) {
		$callfrom = $unknownCaller;
	}
	if (empty($callto)) {
		$callto = $unknownCaller;
	}

	$sql = 'select userid from vtiger_asteriskextensions where asterisk_extension=?';
	if ($status == 'outgoing') {
		//call is from user to record
		$result = $adb->pquery($sql, array($callfrom));
		if ($adb->num_rows($result)>0) {
			$userid = $adb->query_result($result, 0, 'userid');
			$callerName = getUserFullName($userid);
		}

		$receiver = $useCallerInfo;
		if (empty($receiver)) {
			$receiver = $unknownCaller;
		} else {
			$receiver = "<a href='index.php?module=".$receiver['module']."&action=DetailView&record=".$receiver['id']."'>".$receiver['name'].'</a>';
		}
	} else {
		//call is from record to user
		$result = $adb->pquery($sql, array($callto));
		if ($adb->num_rows($result)>0) {
			$userid = $adb->query_result($result, 0, 'userid');
			$receiver = getUserFullName($userid);
		}
		$callerName = $useCallerInfo;
		if (empty($callerName)) {
			$callerName = $unknownCaller.' '.$callfrom;
		} else {
			$callerName = "<a href='index.php?module=".$callerName['module'].'&action=DetailView&record='.$callerName['id']."'>".decode_html($callerName['name']).'</a>';
		}
	}

	$sql = 'insert into vtiger_pbxmanager (pbxmanagerid,callfrom,callto,timeofcall,status,pbxuuid) values (?,?,?,?,?,?)';
	$params = array($crmID, $callerName, $receiver, $timeOfCall, $status, $pbxuuid);
	$adb->pquery($sql, $params);
	cbEventHandler::do_action('corebos.pbxmanager.aftersave', $params);
	return $crmID;
}
//functions for asterisk integration end

//functions for settings page
/**
 * this function returns the blocks for the settings page
 */
function getSettingsBlocks() {
	global $adb;
	$result = $adb->query('select blockid, label from vtiger_settings_blocks order by sequence');
	$count = $adb->num_rows($result);
	$blocks = array();

	if ($count>0) {
		for ($i=0; $i<$count; $i++) {
			$blockid = $adb->query_result($result, $i, 'blockid');
			$label = $adb->query_result($result, $i, 'label');
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
			if (!empty($field) && count($field)<4) {
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

/** Function to get the name of the Field which is used for Module Specific Sequence Numbering, if any
 * @param string Module label
 * @return array Field name and label
 */
function getModuleSequenceField($module) {
	global $adb, $log;
	$log->debug('> getModuleSequenceField '.$module);
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

	$log->debug('< getModuleSequenceField');
	return $field;
}

/* Function to get the Result of all the field ids allowed for Duplicates merging for specified tab/module (tabid) */
function getFieldsResultForMerge($tabid) {
	global $log, $adb;
	$log->debug('> getFieldsResultForMerge '.$tabid);

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
		$where .= ' AND fieldname NOT IN ('. generateQuestionMarks($nonmergable_field_tab[$tabid]) .')';
		$params[] = $nonmergable_field_tab[$tabid];
	}

	if (count($nonmergable_displaytypes) > 0) {
		$where .= ' AND displaytype NOT IN ('. generateQuestionMarks($nonmergable_displaytypes) .')';
		$params[] = $nonmergable_displaytypes;
	}
	if (count($nonmergable_uitypes) > 0) {
		$where .= ' AND uitype NOT IN ('. generateQuestionMarks($nonmergable_uitypes) .')' ;
		$params[] = $nonmergable_uitypes;
	}

	if (trim($where) != '') {
		$sql .= $where;
	}

	$res = $adb->pquery($sql, $params);
	$log->debug('< getFieldsResultForMerge');
	return $res;
}

/** get the related tables data
 * @param string Primary module name
 * @param string Secondary module name
 * @return array tables and fields to be compared are sent
 */
function getRelationTables($module, $secmodule) {
	global $adb;
	if (!(vtlib_isModuleActive($module) && vtlib_isModuleActive($secmodule) && vtlib_isEntityModule($module) && vtlib_isEntityModule($secmodule))) {
		return '';
	}
	$primary_obj = CRMEntity::getInstance($module);
	$secondary_obj = CRMEntity::getInstance($secmodule);

	if (method_exists($primary_obj, 'setRelationTables')) {
		$reltables = $primary_obj->setRelationTables($secmodule);
	}
	if (empty($reltables)) { // not predefined so we try uitype10
		$ui10_query = $adb->pquery(
			'SELECT vtiger_field.tablename AS tablename, vtiger_field.columnname AS columnname
				FROM vtiger_field
				INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid
				WHERE (vtiger_fieldmodulerel.module=? AND vtiger_fieldmodulerel.relmodule=?) OR (vtiger_fieldmodulerel.module=? AND vtiger_fieldmodulerel.relmodule=?)
				ORDER BY vtiger_fieldmodulerel.sequence ASC',
			array($module, $secmodule, $secmodule, $module)
		);
		if ($adb->num_rows($ui10_query)>0) {
			$ui10_tablename = $adb->query_result($ui10_query, 0, 'tablename');
			$ui10_columnname = $adb->query_result($ui10_query, 0, 'columnname');
			if ($primary_obj->table_name == $ui10_tablename) {
				$reltables = array($ui10_tablename=>array($primary_obj->table_index, $ui10_columnname));
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
		$rel_array = array(
			'vtiger_crmentityrel' => array('crmid','relcrmid'),
			$primary_obj->table_name => $primary_obj->table_index
		);
	}
	return $rel_array;
}

/**
 * This function handles the delete functionality of each entity
 * @param string module name
 * @param string return module name
 * @param CRMEntity module object
 * @param integer entity id
 * @param integer return entity id
 */
function DeleteEntity($module, $return_module, $focus, $record, $return_id) {
	global $log;
	$log->debug("> DeleteEntity $module, $return_module, $record, $return_id");
	if (!empty($record)) {
		$setype = getSalesEntityType($record);
		if ($setype != $module && !($module == 'cbCalendar' && $setype == 'Emails')) {
			return array(true,getTranslatedString('LBL_PERMISSION'));
		}
		if ($module != $return_module && !empty($return_module) && !empty($return_id)) {
			$focus->unlinkRelationship($record, $return_module, $return_id);
			$focus->trackUnLinkedInfo($return_module, $return_id, $module, $record);
			$log->debug('< DeleteEntity');
		} else {
			list($delerror,$errormessage) = $focus->preDeleteCheck();
			if (!$delerror) {
				$focus->trash($module, $record);
			}
			$log->debug('< DeleteEntity');
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
 * @param string column name
 * @param string table name
 * @return boolean true if column exists; false otherwise
 */
function columnExists($columnName, $tableName) {
	global $adb;
	$columnNames = $adb->getColumnNames($tableName);
	return in_array($columnName, $columnNames);
}

/**
 * this function accepts a potential id returns the module name and entity value for the related field
 * @param integer potential id
 * @return array related module name and field value
 */
function getRelatedInfo($id) {
	global $adb;
	$data = array();
	$result = $adb->pquery('select related_to from vtiger_potential where potentialid=?', array($id));
	if ($adb->num_rows($result)>0) {
		$relID = $adb->query_result($result, 0, 'related_to');
		$result = $adb->pquery('select setype from vtiger_crmobject where crmid=?', array($relID));
		$data = array('setype'=>($adb->num_rows($result)>0 ? $result->fields['setype'] : ''), 'relID'=>$relID);
	}
	return $data;
}

/**
 * this function accepts an ID and returns the entity value for that id
 * @param integer crmid of the record
 * @return string entity name for the id
 */
function getRecordInfoFromID($id) {
	global $adb;
	$data = array();
	$result = $adb->pquery('select setype from vtiger_crmobject where crmid=?', array($id));
	if ($adb->num_rows($result)>0) {
		$data = getEntityName($result->fields['setype'], $id);
	}
	if (!empty($data)) {
		$data = array_values($data);
		$data = $data[0];
	} else {
		$data = '';
	}
	return $data;
}

/**
 * this function accepts a tabiD and returns the tablename, fieldname and fieldlabel of the first email field it finds
 * @param integer tabid of the module
 * @return array of the email field's tablename, fieldname and fieldlabel or empty if not found
 */
function getMailFields($tabid) {
	global $adb;
	$fields = array();
	$result = $adb->pquery("SELECT tablename,fieldlabel,fieldname FROM vtiger_field WHERE tabid=? AND uitype='13'", array($tabid));
	if ($adb->num_rows($result)>0) {
		$tablename = $adb->query_result($result, 0, 'tablename');
		$fieldname = $adb->query_result($result, 0, 'fieldname');
		$fieldlabel = $adb->query_result($result, 0, 'fieldlabel');
		$fields = array('tablename'=>$tablename, 'fieldname'=>$fieldname, 'fieldlabel'=>$fieldlabel);
	}
	return $fields;
}

/**
 * Function to check if a given record exists (not deleted)
 * @param integer record id
 */
function isRecordExists($recordId) {
	global $adb;
	$users = $groups = $currency = false;
	if (strpos($recordId, 'x')) {
		list($moduleWS,$recordId) = explode('x', $recordId);
		$userWS = vtws_getEntityId('Users');
		$users = ($userWS==$moduleWS);
		$groupWS = vtws_getEntityId('Groups');
		$groups = ($groupWS==$moduleWS);
		$currencyWS = vtws_getEntityId('Currency');
		$currency = ($currencyWS==$moduleWS);
	}
	if ($users) {
		$query = 'SELECT id FROM vtiger_users where id=? AND deleted=0';
	} elseif ($groups) {
		$query = 'SELECT groupid FROM vtiger_groups where groupid=?';
	} elseif ($currency) {
		$query = 'SELECT id FROM vtiger_currency_info where id=? AND deleted=0';
	} else {
		$query = 'SELECT crmid FROM vtiger_crmobject where crmid=? AND deleted=0';
	}
	$result = $adb->pquery($query, array($recordId));
	if ($adb->num_rows($result)) {
		return true;
	}
	return false;
}

/** Function to check if a number is an attachment ID
 * @param integer entity ID
 * @return boolean true if ID belongs to an attachment, false otherwise
  */
function is_attachmentid($id) {
	global $adb, $log;
	$log->debug('> is_attachmentid '.$id);
	$result = $adb->pquery(
		'SELECT attachmentsid FROM vtiger_attachments INNER JOIN vtiger_crmentity ON attachmentsid=crmid WHERE deleted=0 AND attachmentsid=?',
		array($id)
	);
	$log->debug('< is_attachmentid '.$id);
	return ($adb->num_rows($result) > 0);
}

/** Function to set date values compatible to database (YY_MM_DD)
 * @param string value
 * @return string insert_date
 */
function getValidDBInsertDateValue($value) {
	global $log;
	$log->debug('> getValidDBInsertDateValue '.$value);
	$value = trim($value);
	if (empty($value) || $value=='$') {
		return '';
	}
	$delim = array('/','.');
	$value = str_replace($delim, '-', $value);

	$dparts = explode('-', $value);
	if (count($dparts)!=3) {
		return '';
	}
	$y = $dparts[0];
	$m = $dparts[1];
	$d = $dparts[2];
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

	$log->debug('< getValidDBInsertDateValue');
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
 * @param string Required Memory Limit
 */
function _phpset_memorylimit_MB($newvalue) {
	$current = @ini_get('memory_limit');
	if (preg_match("/(.*)M/", $current, $matches) && $matches[1] < $newvalue) {
		// Check if current value is less then new value
		@ini_set('memory_limit', "{$newvalue}M");
	}
}

/** Function to sanitize the upload file name when the file name is detected to have bad extensions
 * @param string File name to be sanitized
 * @return string Sanitized file name
 */
function sanitizeUploadFileName($fileName, $badFileExtensions) {
	$fileName = preg_replace('/\s+/', '_', $fileName);//replace space with _ in filename
	$fileName = preg_replace('/\/+/', '_', $fileName);//replace / with _ in filename
	$fileName = rtrim($fileName, '\\<>?*:"<>|');

	$fileNameParts = explode('.', $fileName);
	$countOfFileNameParts = count($fileNameParts);
	$badExtensionFound = false;

	for ($i=0; $i<$countOfFileNameParts; ++$i) {
		$partOfFileName = $fileNameParts[$i];
		if (in_array(strtolower($partOfFileName), $badFileExtensions)) {
			$badExtensionFound = true;
			$fileNameParts[$i] = $partOfFileName . 'file';
		}
	}

	$newFileName = implode('.', $fileNameParts);

	if ($badExtensionFound) {
		$newFileName .= '.txt';
	}
	return $newFileName;
}

/** Function to get the tab meta information for a given id
 * @param integer tab id
 * @return array of preference name to preference value
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
 * @param integer block id
 * @return string Block Name
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
	return (count($matches) > 0);
}

function validateServerName($string) {
	preg_match('/^[\w\-\.\\/:]+$/', $string, $matches);
	return (count($matches) > 0);
}

function validateEmailId($string) {
	preg_match('/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/', $string, $matches);
	return (count($matches) > 0);
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
	return (($queryres && $adb->num_rows($queryres)>0) || $module=='Campaigns' || $module=='Faq');
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
	$otherIM = GlobalVariable::getVariable('Inventory_Other_Modules', '');
	$otherIM = explode(',', $otherIM);
	array_walk(
		$otherIM,
		function (&$val, $idx) {
			$val = trim($val);
		}
	);
	$nativeIM = array('Invoice','Quotes','PurchaseOrder','SalesOrder','Issuecards', 'Receiptcards');
	return array_merge($nativeIM, $otherIM);
}

/**
 * Function to get the list of Contacts related to an activity
 * @param integer activity Id
 * @return array List of Contact ids, mapped to Contact Names
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
			$storearray = explode(';', $idstring);
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
		$storearray = explode(';', $idstring);
	}

	return $storearray;
}

function getSelectAllQuery($input, $module) {
	global $adb,$current_user;

	$viewid = vtlib_purify($input['viewname']);

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
		$query .= ' AND vtiger_notes.folderid in ('.$folderid.')';
	}

	return $adb->pquery($query, array());
}

function getCampaignAccountIds($id) {
	global $adb;
	$mod = CRMEntity::getInstance('Accounts');
	$sql = 'SELECT vtiger_account.accountid as id FROM vtiger_account
		INNER JOIN vtiger_campaignaccountrel ON vtiger_campaignaccountrel.accountid = vtiger_account.accountid
		LEFT JOIN '.$mod->crmentityTable.' as vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
		WHERE vtiger_campaignaccountrel.campaignid = ? AND vtiger_crmentity.deleted=0';
	return $adb->pquery($sql, array($id));
}

function getCampaignContactIds($id) {
	global $adb;
	$mod = CRMEntity::getInstance('Contacts');
	$sql = 'SELECT vtiger_contactdetails.contactid as id FROM vtiger_contactdetails
		INNER JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.contactid = vtiger_contactdetails.contactid
		LEFT JOIN '.$mod->crmentityTable.' as vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
		WHERE vtiger_campaigncontrel.campaignid = ? AND vtiger_crmentity.deleted=0';
	return $adb->pquery($sql, array($id));
}

function getCampaignLeadIds($id) {
	global $adb;
	$mod = CRMEntity::getInstance('Leads');
	$sql = 'SELECT vtiger_leaddetails.leadid as id FROM vtiger_leaddetails
		INNER JOIN vtiger_campaignleadrel ON vtiger_campaignleadrel.leadid = vtiger_leaddetails.leadid
		LEFT JOIN '.$mod->crmentityTable.' as vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
		WHERE vtiger_campaignleadrel.campaignid = ? AND vtiger_crmentity.deleted=0';
	return $adb->pquery($sql, array($id));
}

/** Function to get the difference between 2 datetime strings or millisecond values */
function dateDiff($d1, $d2) {
	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
	$d2 = (is_string($d2) ? strtotime($d2) : $d2);

	$diffSecs = abs($d1 - $d2);
	$baseYear = min(date('Y', $d1), date('Y', $d2));
	$diff = mktime(0, 0, $diffSecs, 1, 1, $baseYear);
	return array(
		'years' => date('Y', $diff) - $baseYear,
		'months_total' => (date('Y', $diff) - $baseYear) * 12 + date('n', $diff) - 1,
		'months' => date('n', $diff) - 1,
		'days_total' => floor($diffSecs / (3600 * 24)),
		'days' => date('j', $diff) - 1,
		'hours_total' => floor($diffSecs / 3600),
		'hours' => date('G', $diff),
		'minutes_total' => floor($diffSecs / 60),
		'minutes' => (int) date('i', $diff),
		'seconds_total' => $diffSecs,
		'seconds' => (int) date('s', $diff)
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
	$mod = CRMEntity::getInstance('cbCompany');
	$query = $adb->pquery(
		'SELECT c.*,a.*
			FROM vtiger_cbcompany c
			JOIN '.$mod->crmentityTable.' as vtiger_crmentity on vtiger_crmentity.crmid = c.cbcompanyid
			LEFT JOIN vtiger_seattachmentsrel s ON c.cbcompanyid = s.crmid
			LEFT JOIN vtiger_attachments a ON s.attachmentsid = a.attachmentsid
			WHERE c.defaultcompany = 1 and vtiger_crmentity.deleted = 0',
		array()
	);
	if ($query && $adb->num_rows($query) > 0) {
		$companyDetails['name']     = $companyDetails['companyname'] = decode_html($adb->query_result($query, 0, 'companyname'));
		$companyDetails['website']  = $adb->query_result($query, 0, 'website');
		$companyDetails['email']  = $adb->query_result($query, 0, 'email');
		$companyDetails['siccode']  = $adb->query_result($query, 0, 'siccode');
		$companyDetails['accid']  = $adb->query_result($query, 0, 'accid');
		$companyDetails['address']  = decode_html($adb->query_result($query, 0, 'address'));
		$companyDetails['city']     = decode_html($adb->query_result($query, 0, 'city'));
		$companyDetails['state']    = decode_html($adb->query_result($query, 0, 'state'));
		$companyDetails['country']  = decode_html($adb->query_result($query, 0, 'country'));
		$companyDetails['postalcode'] = $companyDetails['code'] = decode_html($adb->query_result($query, 0, 'postalcode'));
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
				if (file_exists($path.$attachmentsid.'_'.$companylogo)) {
					$companyDetails['companylogo'] = $path.$attachmentsid.'_'.$companylogo;
				} else {
					$companyDetails['companylogo'] = 'themes/images/coreboslogo.png';
				}
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
	for ($i=0; $i<count($imageArray); $i++) {
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

/**
 * Function to validate mautic secret
 */
function validateMauticSecret($signedvalue, $signedkey, $input) {
	$headers = getallheaders();
	$receivedSignature = $headers['Webhook-Signature'];
	$computedSignature = base64_encode(hash_hmac('sha256', $input, $signedvalue, true));
	return ($receivedSignature === $computedSignature);
}
?>
