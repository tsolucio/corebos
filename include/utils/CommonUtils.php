<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/utils/utils.php';
require_once 'include/utils/VTCacheUtils.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/utils/RecurringType.php';
require_once 'include/utils/EmailTemplate.php';
require_once 'include/QueryGenerator/QueryGenerator.php';
require_once 'include/ListView/ListViewController.php';

/**
 * Check if user object belongs to a system admin.
 */
function is_admin($user) {
	global $log;
	if (empty($user)) {
		return false;
	}
	$log->debug("Entering is_admin(" . $user->user_name . ") method ...");

	if ($user->is_admin == 'on') {
		$log->debug("Exiting is_admin method ...");
		return true;
	} else {
		$log->debug("Exiting is_admin method ...");
		return false;
	}
}

/**
 * Check if user id belongs to a system admin.
 */
function is_adminID($userID) {
	global $log;
	if (empty($userID) || !is_numeric($userID)) {
		return false;
	}
	$log->debug('Entering is_adminID(' . $userID . ') method ...');
	$is_admin = false;
	if (file_exists('user_privileges/user_privileges_' . $userID . '.php')) {
		require 'user_privileges/user_privileges_' . $userID . '.php';
	}
	$log->debug('Exiting is_adminID method ...');
	return ($is_admin == true);
}

/**
 * path is inside the application tree
 */
function isInsideApplication($path2check) {
	global $root_directory;
	$rp = realpath($path2check);
	$rt = rtrim($root_directory, '/').DIRECTORY_SEPARATOR;
	return (strpos($rp, $rt)===0);
}

/**
 * THIS FUNCTION IS DEPRECATED AND SHOULD NOT BE USED; USE get_select_options_with_id()
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 */
function get_select_options(&$option_list, $selected, $advsearch = 'false') {
	global $log;
	$log->debug("Entering and Exiting get_select_options (" . print_r($option_list, true) . "," . $selected . "," . $advsearch . ") method ...");
	return get_select_options_with_id($option_list, $selected, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values is an array of the datas
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 */
function get_select_options_with_id(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug('Entering/Exiting get_select_options_with_id (' . print_r($option_list, true) . ',' . print_r($selected_key, true) . ',' . $advsearch . ') method ...');
	return get_select_options_with_id_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

function get_select_options_with_value(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug('Entering and Exiting get_select_options_with_id (' . print_r($option_list, true) . ',' . $selected_key . ',' . $advsearch . ') method ...');
	return get_select_options_with_value_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_array(&$option_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug('Entering and Exiting get_select_options_array (' . print_r($option_list, true) . ',' . $selected_key . ',' . $advsearch . ') method ...');
	return get_options_array_seperate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The value is an array of data
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 */
function get_options_array_seperate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log,  $app_strings;
	$log->debug('Entering get_options_array_seperate_key ('.print_r($label_list, true).','.print_r($key_list, true).','.$selected_key.','.$advsearch.') method');
	if ($advsearch == 'true') {
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	} else {
		$select_options = "";
	}

	//for setting null selection values to human readable --None--
	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	$selected_key = (array)$selected_key;

	//create the type dropdown domain and set the selected value if $opp value already exists
	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		// the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
		// The reported bug was only happening with one of the users in the drop down.  It was being replaced by none.
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION " . $selected_string . "value='$html_value'>$label_list[$option_key]</OPTION>";
		$options[$html_value] = array($label_list[$option_key] => $selected_string);
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);

	$log->debug("Exiting get_options_array_seperate_key method ...");
	return $options;
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_with_id_separate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log, $app_strings;
	$log->debug("Entering get_select_options_with_id_separate_key() method ...");
	if ($advsearch == 'true') {
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	} else {
		$select_options = "";
	}

	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	$selected_key = (array)$selected_key;

	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected ';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION " . $selected_string . "value='$html_value'>$label_list[$option_key]</OPTION>";
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);
	$log->debug("Exiting get_select_options_with_id_separate_key method ...");
	return $select_options;
}

function get_select_options_with_value_separate_key(&$label_list, &$key_list, $selected_key, $advsearch = 'false') {
	global $log;
	$log->debug("Entering get_select_options_with_id_separate_key(" . $label_list . "," . $key_list . "," . $selected_key . "," . $advsearch . ") method ...");
	global $app_strings;
	if ($advsearch == 'true') {
		$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	} else {
		$select_options = "";
	}

	$pattern = "/'0?'></";
	$replacement = "''>" . $app_strings['LBL_NONE'] . "<";
	$selected_key = (array)$selected_key;

	foreach ($key_list as $option_key => $option_value) {
		$selected_string = '';
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key))) {
			$selected_string = 'selected ';
		}
		$select_options .= "\n<OPTION " . $selected_string . "value='$label_list[$option_key]'>$label_list[$option_key]</OPTION>";
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);
	$log->debug("Exiting get_select_options_with_id_separate_key method ...");
	return $select_options;
}

/**
 * Converts localized date format string to jscalendar format
 */
function parse_calendardate($local_format) {
	global $log, $current_user;
	$log->debug("Entering parse_calendardate(" . $local_format . ") method ...");
	if ($current_user->date_format == 'dd-mm-yyyy') {
		$dt_popup_fmt = "%d-%m-%Y";
	} elseif ($current_user->date_format == 'mm-dd-yyyy') {
		$dt_popup_fmt = "%m-%d-%Y";
	} elseif ($current_user->date_format == 'yyyy-mm-dd') {
		$dt_popup_fmt = "%Y-%m-%d";
	}
	$log->debug("Exiting parse_calendardate method ...");
	return $dt_popup_fmt;
}

/**
 * Rudimentary/Trusted input clean up for XSS
 * input values $string - string to be cleaned
 * returns the cleaned value in string fromat
 */
function from_html($string) {
	if (is_string($string)) {
		if (preg_match('/(script).*(\/script)/i', $string)) {
			$string = preg_replace(array('/<\s*script/', '/<\/\s*script\s*>/'), array('&lt;script', '&lt;/script&gt;', '&quot;'), $string);
			$string = str_replace('"', '&quot;', $string);
		}
	}
	return $string;
}

function fck_from_html($string) {
	return vtlib_purify($string);
}

/**
 * 	Function used to decodes the given single quote and double quote only. This function used for popup selection
 * 	@param string $string - string to be converted, $encode - flag to decode
 * 	@return string $string - the decoded value in string fromat where as only single and double quotes will be decoded
 */
function popup_from_html($string, $encode = true) {
	global $log;
	$log->debug("Entering popup_from_html(" . $string . "," . $encode . ") method ...");

	$popup_toHtml = array(
		'"' => '&quot;',
		"'" => '&#039;',
	);

	//if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
	if ($encode && is_string($string)) {
		$string = addslashes(str_replace(array_values($popup_toHtml), array_keys($popup_toHtml), $string));
	}

	$log->debug("Exiting popup_from_html method ...");
	return $string;
}

/** To get the Currency of the specified user
 * @param $id -- The user Id:: Type integer
 * @returns  currencyid :: Type integer
 */
function fetchCurrency($id) {
	global $log;
	$log->debug("Entering fetchCurrency(" . $id . ") method ...");

	// Lookup the information in cache
	$currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);

	if ($currencyinfo === false) {
		global $adb;
		$sql = "select currency_id from vtiger_users where id=?";
		$result = $adb->pquery($sql, array($id));
		$currencyid = $adb->query_result($result, 0, "currency_id");

		VTCacheUtils::updateUserCurrencyId($id, $currencyid);

		// Re-look at the cache for consistency
		$currencyinfo = VTCacheUtils::lookupUserCurrenyId($id);
	}

	$currencyid = $currencyinfo['currencyid'];
	$log->debug("Exiting fetchCurrency method ...");
	return $currencyid;
}

/** Function to get the Currency name from the vtiger_currency_info
 * @param $currencyid -- currencyid:: Type integer
 * @returns $currencyname -- Currency Name:: Type varchar
 *
 */
function getCurrencyName($currencyid, $show_symbol = true) {
	global $log;
	$log->debug('Entering getCurrencyName(' . $currencyid . ') method ...');

	// Look at cache first
	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);

	if ($currencyinfo === false) {
		global $adb;
		$sql1 = 'select * from vtiger_currency_info where id= ?';
		$result = $adb->pquery($sql1, array($currencyid));

		$resultinfo = $adb->fetch_array($result);

		// Update cache
		VTCacheUtils::updateCurrencyInfo(
			$currencyid,
			$resultinfo['currency_name'],
			$resultinfo['currency_code'],
			$resultinfo['currency_symbol'],
			$resultinfo['conversion_rate'],
			$resultinfo['currency_position']
		);

		// Re-look at the cache now
		$currencyinfo = VTCacheUtils::lookupCurrencyInfo($currencyid);
	}

	$currencyname = $currencyinfo['name'];
	$curr_symbol = $currencyinfo['symbol'];

	$log->debug('Exiting getCurrencyName method ...');
	if ($show_symbol) {
		return getTranslatedCurrencyString($currencyname) . ' : ' . $curr_symbol;
	} else {
		return $currencyname;
	}
	// NOTE: Without symbol the value could be used for filtering/lookup hence avoiding the translation
}

/**
 * Function to fetch the list of groups from group table
 * Takes no value as input
 * returns the query result set object
 */
function get_group_options() {
	global $log, $adb, $noof_group_rows;
	$log->debug('Entering get_group_options() method ...');
	$sql = 'select groupname,groupid from vtiger_groups';
	$result = $adb->pquery($sql, array());
	$noof_group_rows = $adb->num_rows($result);
	$log->debug('Exiting get_group_options method ...');
	return $result;
}

/**
 * Function to get the tabid
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */
function getTabid($module) {
	global $log;
	$log->debug("Entering getTabid(" . $module . ") method ...");

	// Lookup information in cache first
	$tabid = VTCacheUtils::lookupTabid($module);
	if ($tabid === false) {
		if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
			include 'tabdata.php';
			if (!empty($tab_info_array[$module])) {
				$tabid = $tab_info_array[$module];
			}
		}
		if ($tabid === false) {
			global $adb;
			$sql = "select tabid from vtiger_tab where name=?";
			$result = $adb->pquery($sql, array($module));
			if (!$result || $adb->num_rows($result)==0) {
				return null;
			}
			$tabid = $adb->query_result($result, 0, "tabid");
		}
		// Update information to cache for re-use
		VTCacheUtils::updateTabidInfo($tabid, $module);
	}
	$log->debug("Exiting getTabid method ...");
	return $tabid;
}

/**
 * Function to get the fieldid
 *
 * @param Integer $tabid
 * @param Boolean $onlyactive
 */
function getFieldid($tabid, $fieldname, $onlyactive = true) {
	// Look up information at cache first
	$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	if ($fieldinfo === false) {
		getColumnFields(getTabModuleName($tabid));
		$fieldinfo = VTCacheUtils::lookupFieldInfo($tabid, $fieldname);
	}

	// Get the field id based on required criteria
	$fieldid = false;

	if ($fieldinfo) {
		$fieldid = $fieldinfo['fieldid'];
		if ($onlyactive && !in_array($fieldinfo['presence'], array('0', '2'))) {
			$fieldid = false;
		}
	}
	return $fieldid;
}

function getFieldFromEditViewBlockArray($blocks, $fldlabel) {
	$result = array();
	if (is_array($blocks)) {
		$found = false;
		foreach ($blocks as $blklabel => $fieldarray) {
			foreach ($fieldarray as $key => $row) {
				if ($row[0][0][0]=='10') {
					$col0label = $row[0][1][0]['displaylabel'];
				} else {
					$col0label = $row[0][1][0];
				}
				if (!empty($row[1])>0 && count($row[1])>0) {
					if ($row[1][0][0]=='10') {
						$col1label = $row[1][1][0]['displaylabel'];
					} else {
						$col1label = $row[1][1][0];
					}
				} else {
					$col1label = '';
				}
				if ($col0label==$fldlabel) {
					$fieldkey = 0;
					$found = true;
				} elseif ($col1label==$fldlabel) {
					$fieldkey = 1;
					$found = true;
				}
				if ($found) {
					$result['block_label'] = $blklabel;
					$result['row_key'] = $key;
					$result['field_key'] = $fieldkey;
					break 2;
				}
			}
		}
	}
	return $result;
}

function getFieldFromDetailViewBlockArray($blocks, $fldlabel) {
	return getFieldFromBlockArray($blocks, $fldlabel);
}

function getFieldFromBlockArray($blocks, $fldlabel) {
	$result = array();
	if (is_array($blocks)) {
		$found = false;
		foreach ($blocks as $blklabel => $fieldarray) {
			foreach ($fieldarray as $key => $value) {
				$found = array_key_exists($fldlabel, $value);
				if ($found && is_array($value[$fldlabel]) && isset($value[$fldlabel]['value']) && isset($value[$fldlabel]['fldname'])) { // avoid false positives
					$result['block_label'] = $blklabel;
					$result['field_key'] = $key;
					break 2;
				} else {
					$found = false;
				}
			}
		}
	}
	return $result;
}

/**
 * Function to get the CustomViewName
 * Takes the input as $cvid - customviewid
 * returns the cvname string fromat
 */
function getCVname($cvid) {
	global $log, $adb;
	$log->debug("Entering getCVname($cvid)");
	$result = $adb->pquery('select viewname from vtiger_customview where cvid=?', array($cvid));
	$cvname = $adb->query_result($result, 0, 'viewname');
	$log->debug('Exiting getCVname');
	return $cvname;
}

/**
 * Function to get the ownedby value for the specified module
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */
function getTabOwnedBy($module) {
	global $log;
	$log->debug("Entering getTabid(" . $module . ") method ...");

	$tabid = getTabid($module);

	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) {
		include 'tabdata.php';
		$tab_ownedby = $tab_ownedby_array[$tabid];
	} else {
		$log->info("module is " . $module);
		global $adb;
		$sql = "select ownedby from vtiger_tab where name=?";
		$result = $adb->pquery($sql, array($module));
		$tab_ownedby = $adb->query_result($result, 0, "ownedby");
	}
	$log->debug("Exiting getTabid method ...");
	return $tab_ownedby;
}

/**
 * Function to get the module name/type of a given crmid
 * @param int $crmid  CRMID of the record we want to know the type
 * @return string module name of the crmid record
 */
function getSalesEntityType($crmid) {
	global $log, $adb;
	$log->debug("Entering getSalesEntityType($crmid) method ...");
	$sql = 'select setype from vtiger_crmentity where crmid=?';
	$result = $adb->pquery($sql, array($crmid));
	$parent_module = $adb->query_result($result, 0, 'setype');
	$log->debug('Exiting getSalesEntityType method ...');
	return $parent_module;
}

/**
 * Function to get the AccountName from an account id
 * Takes the input as $acount_id - account id
 * returns the account name in string format.
 */
function getAccountName($account_id) {
	global $log, $adb;
	$log->debug("Entering getAccountName($account_id) method ...");
	$accountname = '';
	if (!empty($account_id)) {
		$sql = 'select accountname from vtiger_account where accountid=?';
		$result = $adb->pquery($sql, array($account_id));
		$accountname = $adb->query_result($result, 0, 'accountname');
	}
	$log->debug('Exiting getAccountName method ...');
	return $accountname;
}

/**
 * Function to get the ProductName when a product id is given
 * Takes the input as $product_id - product id
 * returns the product name in string format.
 */
function getProductName($product_id) {
	global $log, $adb;
	$log->debug("Entering getProductName($product_id) method ...");
	$sql = 'select productname from vtiger_products where productid=?';
	$result = $adb->pquery($sql, array($product_id));
	$productname = $adb->query_result($result, 0, 'productname');
	$log->debug('Exiting getProductName method ...');
	return $productname;
}

/**
 * Function to get the Potentail Name when a potential id is given
 * Takes the input as $potential_id - potential id
 * returns the potential name in string format.
 */
function getPotentialName($potential_id) {
	global $log, $adb;
	$log->debug("Entering getPotentialName($potential_id) method ...");
	$potentialname = '';
	if ($potential_id != '') {
		$sql = 'select potentialname from vtiger_potential where potentialid=?';
		$result = $adb->pquery($sql, array($potential_id));
		$potentialname = $adb->query_result($result, 0, 'potentialname');
	}
	$log->debug('Exiting getPotentialName method ...');
	return $potentialname;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */
function getContactName($contact_id) {
	global $log, $adb, $current_user;
	$log->debug("Entering getContactName($contact_id) method ...");
	$contact_name = '';
	if ($contact_id != '') {
		$sql = 'select firstname, lastname from vtiger_contactdetails where contactid=?';
		$result = $adb->pquery($sql, array($contact_id));
		$firstname = $adb->query_result($result, 0, 'firstname');
		$lastname = $adb->query_result($result, 0, 'lastname');
		$contact_name = $lastname;
		if (getFieldVisibilityPermission('Contacts', $current_user->id, 'firstname') == '0') {
			$contact_name .= ' ' . $firstname;
		}
	}
	$log->debug('Exiting getContactName method ...');
	return $contact_name;
}

/**
 * Function to get the Contact Name when a contact id is given
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */
function getLeadName($lead_id) {
	global $log, $adb, $current_user;
	$log->debug("Entering getLeadName($lead_id) method ...");
	$lead_name = '';
	if ($lead_id != '') {
		$sql = 'select firstname, lastname from vtiger_leaddetails where leadid=?';
		$result = $adb->pquery($sql, array($lead_id));
		$firstname = $adb->query_result($result, 0, 'firstname');
		$lastname = $adb->query_result($result, 0, 'lastname');
		$lead_name = $lastname;
		if (getFieldVisibilityPermission('Leads', $current_user->id, 'firstname') == '0') {
			$lead_name .= ' ' . $firstname;
		}
	}
	$log->debug('Exiting getLeadName method ...');
	return $lead_name;
}

/**
 * Function to get the Full Name of a Contact/Lead when a query result and the row count are given
 * Takes the input as $result - Query Result, $row_count - Count of the Row, $module - module name
 * returns the Contact Name in string format.
 */
function getFullNameFromQResult($result, $row_count, $module) {
	global $log, $adb;
	$log->debug('Entering getFullNameFromQResult');

	$rowdata = $adb->query_result_rowdata($result, $row_count);
	$entity_field_info = getEntityFieldNames($module);
	$fieldsName = $entity_field_info['fieldname'];
	$name = '';
	if ($rowdata != '' && count($rowdata) > 0) {
		$name = getEntityFieldNameDisplay($module, $fieldsName, $rowdata);
	}
	$name = textlength_check($name);
	return $name;
}

function getFullNameFromArray($module, $fieldValues) {
	$entityInfo = getEntityFieldNames($module);
	$fieldsName = $entityInfo['fieldname'];
	return getEntityFieldNameDisplay($module, $fieldsName, $fieldValues);
}

/**
 * Function to get the Campaign Name when a campaign id is given
 * Takes the input as $campaign_id - campaign id
 * returns the Campaign Name in string format.
 */
function getCampaignName($campaign_id) {
	global $log, $adb;
	$log->debug('Entering getCampaignName(' . $campaign_id . ') method ...');
	$sql = 'select campaignname from vtiger_campaign where campaignid=?';
	$result = $adb->pquery($sql, array($campaign_id));
	$campaign_name = $adb->query_result($result, 0, 'campaignname');
	$log->debug('Exiting getCampaignName method ...');
	return $campaign_name;
}

/**
 * Function to get the Vendor Name when a vendor id is given
 * Takes the input as $vendor_id - vendor id
 * returns the Vendor Name in string format.
 */
function getVendorName($vendor_id) {
	global $log;
	$log->debug("Entering getVendorName(" . $vendor_id . ") method ...");
	$log->info("in getVendorName " . $vendor_id);
	global $adb;
	$sql = "select vendorname from vtiger_vendor where vendorid=?";
	$result = $adb->pquery($sql, array($vendor_id));
	$vendor_name = $adb->query_result($result, 0, "vendorname");
	$log->debug("Exiting getVendorName method ...");
	return $vendor_name;
}

/**
 * Function to get the Quote Name when a quote id is given
 * Takes the input as $quote_id - quote id
 * returns the Quote Name in string format.
 */
function getQuoteName($quote_id) {
	global $log;
	$log->debug("Entering getQuoteName(" . $quote_id . ") method ...");
	$log->info("in getQuoteName " . $quote_id);
	global $adb;
	if ($quote_id != null && $quote_id != '') {
		$sql = "select subject from vtiger_quotes where quoteid=?";
		$result = $adb->pquery($sql, array($quote_id));
		$quote_name = $adb->query_result($result, 0, "subject");
	} else {
		$log->debug("Quote Id is empty.");
		$quote_name = '';
	}
	$log->debug("Exiting getQuoteName method ...");
	return $quote_name;
}

/**
 * Function to get the PriceBook Name when a pricebook id is given
 * Takes the input as $pricebook_id - pricebook id
 * returns the PriceBook Name in string format.
 */
function getPriceBookName($pricebookid) {
	global $log;
	$log->debug("Entering getPriceBookName(" . $pricebookid . ") method ...");
	$log->info("in getPriceBookName " . $pricebookid);
	global $adb;
	$sql = "select bookname from vtiger_pricebook where pricebookid=?";
	$result = $adb->pquery($sql, array($pricebookid));
	$pricebook_name = $adb->query_result($result, 0, "bookname");
	$log->debug("Exiting getPriceBookName method ...");
	return $pricebook_name;
}

/** This Function returns the Purchase Order Name.
 * The following is the input parameter for the function
 *  $po_id --> Purchase Order Id, Type:Integer
 */
function getPoName($po_id) {
	global $log;
	$log->debug("Entering getPoName(" . $po_id . ") method ...");
	$log->info("in getPoName " . $po_id);
	global $adb;
	$sql = "select subject from vtiger_purchaseorder where purchaseorderid=?";
	$result = $adb->pquery($sql, array($po_id));
	$po_name = $adb->query_result($result, 0, "subject");
	$log->debug("Exiting getPoName method ...");
	return $po_name;
}

/**
 * Function to get the Sales Order Name when a salesorder id is given
 * Takes the input as $salesorder_id - salesorder id
 * returns the Salesorder Name in string format.
 */
function getSoName($so_id) {
	global $log;
	$log->debug("Entering getSoName(" . $so_id . ") method ...");
	$log->info("in getSoName " . $so_id);
	global $adb;
	$sql = "select subject from vtiger_salesorder where salesorderid=?";
	$result = $adb->pquery($sql, array($so_id));
	$so_name = $adb->query_result($result, 0, "subject");
	$log->debug("Exiting getSoName method ...");
	return $so_name;
}

/**
 * Function to get the Group Information for a given groupid
 * Takes the input $id - group id and $module - module name
 * returns the group information in an array format.
 */
function getGroupName($groupid) {
	global $adb, $log;
	$log->debug("Entering getGroupName(" . $groupid . ") method ...");
	$group_info = array();
	$log->info("in getGroupName, entityid is " . $groupid);
	if ($groupid != '') {
		$sql = "select groupname,groupid from vtiger_groups where groupid = ?";
		$result = $adb->pquery($sql, array($groupid));
		$group_info[] = decode_html($adb->query_result($result, 0, "groupname"));
		$group_info[] = $adb->query_result($result, 0, "groupid");
	}
	$log->debug("Exiting getGroupName method ...");
	return $group_info;
}

/**
 * Get the username by giving the user id.   This method expects the user id
 */
function getUserName($userid) {
	global $adb, $log;
	$log->debug("Entering getUserName(" . $userid . ") method ...");
	$log->info("in getUserName " . $userid);

	if ($userid != '') {
		$sql = "select user_name from vtiger_users where id=?";
		$result = $adb->pquery($sql, array($userid));
		$user_name = $adb->query_result($result, 0, "user_name");
	}
	$log->debug("Exiting getUserName method ...");
	return $user_name;
}

/**
 * Get the user full name by giving the user id.   This method expects the user id
 */
function getUserFullName($userid) {
	global $log;
	$log->debug("Entering getUserFullName($userid)");
	$user_name = '';
	if ($userid != '') {
		if (strpos($userid, 'x')) {
			list($wsid,$userid) = explode('x', $userid);
		}
		$displayValueArray = getEntityName('Users', $userid);
		if (!empty($displayValueArray)) {
			foreach ($displayValueArray as $value) {
				$user_name = $value;
			}
		}
	}
	$log->debug('Exiting getUserFullName');
	return $user_name;
}

/** Function to get related To name with id */
function getParentName($parent_id) {
	if (empty($parent_id) || $parent_id == 0) {
		return '';
	}
	if (strpos($parent_id, 'x')) {
		list($wsid,$parent_id) = explode('x', $parent_id);
	}
	$seType = getSalesEntityType($parent_id);
	$entityNames = getEntityName($seType, $parent_id);
	return $entityNames[$parent_id];
}

/*
 * Return account/contact crmid of any given entityid
 * @param: crmid/webserviceid of the record we need to get the related account/contact
 * @param: Accounts | Contacts related entity to return
 * @returns: crmid of the account/contact related to the entityid
 */
function getRelatedAccountContact($entityid, $module = '') {
	global $adb, $current_user;
	if ($module=='' || ($module!='Accounts' && $module!='Contacts')) {
		if (GlobalVariable::getVariable('Application_B2B', '1')) {
			$module = 'Accounts';
		} else {
			$module = 'Contacts';
		}
	}
	if (strpos($entityid, 'x')>0 && !is_numeric($entityid)) {
		list($ent,$crmid) = explode('x', $entityid);
	} else {
		$crmid = $entityid;
	}
	$acid = 0;
	if (is_numeric($crmid)) {
		$setype = getSalesEntityType($crmid);
		switch ($setype) {
			case 'Accounts':
				$acid = $crmid;
				break;
			case 'Contacts':
				if ($module=='Contacts') {
					$acid = $crmid;
				} else {
					$rspot = $adb->pquery('select accountid from vtiger_contactdetails where contactid=?', array($crmid));
					$acid = $adb->query_result($rspot, 0, 'accountid');
				}
				break;
			case 'Potentials':
				$rspot = $adb->pquery("select related_to from vtiger_potential where potentialid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'related_to');
				break;
			case 'HelpDesk':
				$rspot = $adb->pquery("select parent_id from vtiger_troubletickets where ticketid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'parent_id');
				break;
			case 'Quotes':
				$rspot = $adb->pquery("select accountid,contactid from vtiger_quotes where quoteid=?", array($crmid));
				if ($module=='Accounts') {
					$acid = $adb->query_result($rspot, 0, 'accountid');
				} else {
					$acid = $adb->query_result($rspot, 0, 'contactid');
				}
				break;
			case 'SalesOrder':
				$rspot = $adb->pquery("select accountid,contactid from vtiger_salesorder where salesorderid=?", array($crmid));
				if ($module=='Accounts') {
					$acid = $adb->query_result($rspot, 0, 'accountid');
				} else {
					$acid = $adb->query_result($rspot, 0, 'contactid');
				}
				break;
			case 'PurchaseOrder':
				$rspot = $adb->pquery("select contactid from vtiger_purchaseorder where purchaseorderid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'contactid');
				break;
			case 'Invoice':
				$rspot = $adb->pquery("select accountid,contactid from vtiger_invoice where invoiceid=?", array($crmid));
				if ($module=='Accounts') {
					$acid = $adb->query_result($rspot, 0, 'accountid');
				} else {
					$acid = $adb->query_result($rspot, 0, 'contactid');
				}
				break;
			case 'InventoryDetails':
				$rspot = $adb->pquery("select account_id,contact_id from vtiger_inventorydetails where inventorydetailsid=?", array($crmid));
				if ($module=='Accounts') {
					$acid = $adb->query_result($rspot, 0, 'account_id');
				} else {
					$acid = $adb->query_result($rspot, 0, 'contact_id');
				}
				break;
			case 'ServiceContracts':
				$rspot = $adb->pquery("select sc_related_to from vtiger_servicecontracts where servicecontractsid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'sc_related_to');
				break;
			case 'Assets':
				$rspot = $adb->pquery("select account from vtiger_assets where assetsid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'account');
				break;
			case 'ProjectMilestone':
				$rspot = $adb->pquery("select linktoaccountscontacts
				from vtiger_project
				inner join vtiger_projectmilestone on vtiger_project.projectid = vtiger_projectmilestone.projectid
				where projectmilestoneid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'linktoaccountscontacts');
				break;
			case 'ProjectTask':
				$rspot = $adb->pquery("select linktoaccountscontacts
				from vtiger_project
				inner join vtiger_projecttask on vtiger_project.projectid = vtiger_projecttask.projectid
				where projecttaskid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'linktoaccountscontacts');
				break;
			case 'Project':
				$rspot = $adb->pquery("select linktoaccountscontacts from vtiger_project where projectid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'linktoaccountscontacts');
				break;
			case 'CobroPago':
				$rspot = $adb->pquery("select parent_id from vtiger_cobropago where cobropagoid=?", array($crmid));
				$acid = $adb->query_result($rspot, 0, 'parent_id');
				break;
			case 'Calendar':
			case 'Events':
				if ($module=='Accounts') {
					$rspot = $adb->pquery("select crmid from vtiger_seactivityrel where activityid=?", array($crmid));
					if ($rspot && $adb->num_rows($rspot)>0) {
						$acid = $adb->query_result($rspot, 0, 'crmid');
					}
				} else {
					$rspot = $adb->pquery("select contactid from vtiger_cntactivityrel where activityid=?", array($crmid));
					if ($rspot && $adb->num_rows($rspot)>0) {
						$acid = $adb->query_result($rspot, 0, 'contactid');
					}
				}
				break;
			default:  // we look for uitype 10
				$rsfld = $adb->pquery('SELECT fieldname from vtiger_fieldmodulerel
					INNER JOIN vtiger_field on vtiger_field.fieldid=vtiger_fieldmodulerel.fieldid
					WHERE module=? and relmodule=?', array($setype,$module));
				if ($rsfld && $adb->num_rows($rsfld)>0) {
					$fname = $adb->query_result($rsfld, 0, 'fieldname');
					$queryGenerator = new QueryGenerator($setype, $current_user);
					$queryGenerator->setFields(array($fname));
					$queryGenerator->addCondition('id', $crmid, 'e');
					$query = $queryGenerator->getQuery();
					$rspot = $adb->pquery($query, array());
					$acid = $adb->query_result($rspot, 0, $fname);
				}
		}
	}
	if ($acid!=0) {
		$actype = getSalesEntityType($acid);
		if ($actype != $module) {
			$acid=0;
		}
	}
	return $acid;
}

/**
 * Creates and returns database query. To be used for search and other text links.   This method expects the module object.
 * param $focus - the module object contains the column_fields
 */
function getURLstring($focus) {
	global $log;
	$log->debug("Entering getURLstring(" . get_class($focus) . ") method ...");
	$qry = "";
	foreach ($focus->column_fields as $fldname => $val) {
		if (isset($_REQUEST[$fldname]) && $_REQUEST[$fldname] != '') {
			if ($qry == '') {
				$qry = "&" . $fldname . "=" . vtlib_purify($_REQUEST[$fldname]);
			} else {
				$qry .="&" . $fldname . "=" . vtlib_purify($_REQUEST[$fldname]);
			}
		}
	}
	if (isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] != '') {
		$qry .="&current_user_only=" . vtlib_purify($_REQUEST['current_user_only']);
	}
	if (isset($_REQUEST['advanced']) && $_REQUEST['advanced'] == 'true') {
		$qry .="&advanced=true";
	}

	if ($qry != '') {
		$qry .="&query=true";
	}
	$log->debug("Exiting getURLstring method ...");
	return $qry;
}

/**
 * This function returns the date in user specified format.
 * limitation is that mm-dd-yyyy and dd-mm-yyyy will be considered same by this API.
 * As in the date value is on mm-dd-yyyy and user date format is dd-mm-yyyy then the mm-dd-yyyy
 * value will be return as the API will be considered as considered as in same format.
 * this due to the fact that this API tries to consider the where given date is in user date
 * format. we need a better gauge for this case.
 * @global Users $current_user
 * @param string $cur_date_val the date which should a changed to user date format.
 * @return string display date
 */
function getValidDisplayDate($cur_date_val) {
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if ($dat_fmt == '') {
		$dat_fmt = 'dd-mm-yyyy';
	}
	if (!empty($cur_date_val)) {
		$date_value = explode(' ', $cur_date_val);
		list($y, $m, $d) = explode('-', $date_value[0]);
		list($fy, $fm, $fd) = explode('-', $dat_fmt);
		if ((strlen($fy) == 4 && strlen($y) == 4) || (strlen($fd) == 4 && strlen($d) == 4)) {
			return "$y-$m-$d";
		}
	}
	$date = new DateTimeField($cur_date_val);
	return $date->getDisplayDate();
}

function getNewDisplayDate() {
	global $log, $current_user;
	$log->debug('Entering/Exiting getNewDisplayDate()');
	$date = new DateTimeField(null);
	return $date->getDisplayDate($current_user);
}

function getNewDisplayTime() {
	global $log, $current_user;
	$log->debug('Entering/Exiting getNewDisplayTime()');
	$date = new DateTimeField(null);
	return $date->getDisplayTime($current_user);
}

/** This function returns the default currency information.
 * Takes no param, return type array.
 */
function getDisplayCurrency() {
	global $log, $adb;
	$log->debug('Entering getDisplayCurrency()');
	$curr_array = array();
	$result = $adb->pquery('select id, currency_name, currency_symbol from vtiger_currency_info where currency_status=? and deleted=0', array('Active'));
	$num_rows = $adb->num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {
		$curr_id = $adb->query_result($result, $i, "id");
		$curr_name = $adb->query_result($result, $i, "currency_name");
		$curr_symbol = $adb->query_result($result, $i, "currency_symbol");
		$curr_array[$curr_id] = $curr_name . ' : ' . $curr_symbol;
	}
	$log->debug('Exiting getDisplayCurrency');
	return $curr_array;
}

/** This function returns the amount converted to dollar.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertToDollar($amount, $crate) {
	global $log;
	$log->debug('Entering/Exiting convertToDollar(' . $amount . ',' . $crate . ') method ...');
	return (empty($crate) ? $amount : $amount / $crate);
}

/** This function returns the amount converted from dollar.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertFromDollar($amount, $crate) {
	global $log;
	$log->debug('Entering/Exiting convertFromDollar(' . $amount . ',' . $crate . ') method ...');
	return round($amount * $crate, 2);
}

/** This function returns the amount converted from master currency.
 * param $amount - amount to be converted.
 * param $crate - conversion rate.
 */
function convertFromMasterCurrency($amount, $crate) {
	global $log;
	$log->debug('Entering/Exiting convertFromMasterCurrency(' . $amount . ',' . $crate . ') method ...');
	return $amount * $crate;
}

/** This function returns the conversion rate and currency symbol
 * in array format for a given id.
 * param $id - currency id.
 */
function getCurrencySymbolandCRate($id) {
	global $log;
	$log->debug("Entering getCurrencySymbolandCRate($id)");

	// To initialize the currency information in cache
	getCurrencyName($id);

	$currencyinfo = VTCacheUtils::lookupCurrencyInfo($id);

	$rate_symbol['rate'] = $currencyinfo['rate'];
	$rate_symbol['symbol'] = $currencyinfo['symbol'];
	$rate_symbol['position'] = $currencyinfo['position'];

	$log->debug('Exiting getCurrencySymbolandCRate');
	return $rate_symbol;
}

/** This function returns the default terms and condition from the database.
 * @param string module name
 * @return string
 */
function getTermsandConditions($module = '') {
	global $log, $adb, $currentModule;
	if (empty($module)) {
		$module = $currentModule;
	}
	$log->debug("Entering getTermsandConditions($module) method ...");
	if (vtlib_isModuleActive('cbTermConditions')) {
		$result = $adb->pquery('select tandc
			from vtiger_cbtandc
			inner join vtiger_crmentity on crmid=cbtandcid
			where deleted=0 and formodule=? and isdefault=?
			limit 1', array($module,'1'));
		if ($result && $adb->num_rows($result)>0) {
			$tandc = $adb->query_result($result, 0, 'tandc');
		} else {
			$tandc = '';
		}
	} else {
		$result = $adb->pquery('select tandc from vtiger_inventory_tandc limit 1', array());
		if ($result && $adb->num_rows($result)>0) {
			$tandc = $adb->query_result($result, 0, 'tandc');
		} else {
			$tandc = '';
		}
	}
	$log->debug('Exiting getTermsandConditions');
	return $tandc;
}

/**
 * Create select options in a dropdown list. To be used inside a reminder select statement in an activity form.
 * param $start - start value
 * param $end - end value
 * param $fldname - field name
 * param $selvalue - selected value
 */
function getReminderSelectOption($start, $end, $fldname, $selvalue = '', $class = '') {
	global $log;
	$log->debug("Entering getReminderSelectOption($start,$end,$fldname,$selvalue)");
	$def_sel = '';
	$OPTION_FLD = '<SELECT name=' . $fldname . (!empty($class)?" class='$class' ":'') . '>';
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $selvalue) {
			$def_sel = 'SELECTED';
		}
		$OPTION_FLD .= '<OPTION VALUE=' . $i . ' ' . $def_sel . '>' . $i . "</OPTION>\n";
		$def_sel = '';
	}
	$OPTION_FLD .= '</SELECT>';
	$log->debug('Exiting getReminderSelectOption');
	return $OPTION_FLD;
}

/** This function returns the List price of a given product in a given price book.
 * param $productid - product id.
 * param $pbid - pricebook id.
 */
function getListPrice($productid, $pbid) {
	global $log;
	$log->debug("Entering getListPrice(" . $productid . "," . $pbid . ") method ...");
	$log->info("in getListPrice productid " . $productid);

	global $adb;
	$query = "select listprice from vtiger_pricebookproductrel where pricebookid=? and productid=?";
	$result = $adb->pquery($query, array($pbid, $productid));
	$lp = $adb->query_result($result, 0, 'listprice');
	$log->debug("Exiting getListPrice method ...");
	return $lp;
}

/** This function returns a string with removed new line character, single quote, and back slash double quoute.
 * param $str - string to be converted.
 */
function br2nl($str) {
	global $log;
	$log->debug("Entering br2nl(" . $str . ") method ...");
	$str = preg_replace("/\r/", "\\r", $str);
	$str = preg_replace("/\n/", "\\n", $str);
	$str = preg_replace("/'/", " ", $str);
	$str = preg_replace("/\"/", " ", $str);
	$log->debug("Exiting br2nl method ...");
	return $str;
}

/** This function returns a text, which escapes the html encode for link tag/ a href tag
 * param $text - string/text
 */
function make_clickable($text) {
	global $log;
	$log->debug("Entering make_clickable(" . $text . ") method ...");
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
	// pad it with a space so we can match things at the start of the 1st line.
	$ret = ' ' . $text;

	// matches an "xxxx://yyyy" URL at the start of a line, or after a space.
	// xxxx can only be alpha characters.
	// yyyy is anything up to the first space, newline, comma, double quote or <
	$ret = preg_replace("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

	// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
	// Must contain at least 2 dots. xxxx contains either alphanum, or "-"
	// zzzz is optional.. will contain everything up to the first space, newline,
	// comma, double quote or <.
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

	// matches an email@domain type address at the start of a line, or after a space.
	// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

	// Remove our padding..
	$ret = substr($ret, 1);

	//remove comma, fullstop at the end of url
	$ret = preg_replace("#,\"|\.\"|\)\"|\)\.\"|\.\)\"#", "\"", $ret);

	$log->debug("Exiting make_clickable method ...");
	return($ret);
}

/**
 * This function returns the blocks and its related information for given module.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create),$mode - edit, $col_fields - * column_fields/
 * This function returns an array
 */
function getBlocks($module, $disp_view, $mode, $col_fields = '', $info_type = '') {
	global $log, $adb, $current_user;
	$log->debug("Entering getBlocks(" . $module . "," . $disp_view . "," . $mode . "," . print_r($col_fields, true) . "," . $info_type . ") method ...");
	$tabid = getTabid($module);
	$getBlockInfo = array();
	$query = "select blockid,blocklabel,display_status,isrelatedlist from vtiger_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);
	$blockid_list = array();
	for ($i = 0; $i < $noofrows; $i++) {
		$blockid = $adb->query_result($result, $i, 'blockid');
		$blockid_list[] = $blockid;
		$block_label[$blockid] = $adb->query_result($result, $i, 'blocklabel');
		$isrelatedlist = $adb->query_result($result, $i, 'isrelatedlist');
		if (!is_null($isrelatedlist) && $isrelatedlist != 0) {
			$sLabelVal = $block_label[$blockid];
		} else {
			$sLabelVal = getTranslatedString($block_label[$blockid], $module);
		}
		$aBlockStatus[$sLabelVal] = $adb->query_result($result, $i, 'display_status');
	}
	if ($mode == 'edit') {
		$display_type_check = 'vtiger_field.displaytype = 1';
	} elseif ($mode == 'mass_edit') {
		$display_type_check = 'vtiger_field.displaytype = 1 AND vtiger_field.masseditable NOT IN (0,2)';
	} else {
		$display_type_check = 'vtiger_field.displaytype in (1,5)';
	}

	// Retrieve the profile list from database
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';

	$selectSql = 'vtiger_field.tablename,'
		. 'vtiger_field.columnname,'
		. 'vtiger_field.uitype,'
		. 'vtiger_field.fieldname,'
		. 'vtiger_field.fieldid,'
		. 'vtiger_field.fieldlabel,'
		. 'vtiger_field.maximumlength,'
		. 'vtiger_field.block,'
		. 'vtiger_field.generatedtype,'
		. 'vtiger_field.tabid,'
		. 'vtiger_field.defaultvalue,'
		. 'vtiger_field.typeofdata,'
		. 'vtiger_field.sequence,'
		. 'vtiger_field.displaytype';

	if ($disp_view == "detail_view") {
		if ($is_admin == true || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == 'Emails') {
			$uniqueFieldsRestriction = 'vtiger_field.fieldid IN
				(select max(vtiger_field.fieldid) from vtiger_field where vtiger_field.tabid=? GROUP BY vtiger_field.columnname)';
			$sql = "SELECT distinct $selectSql, '0' as readonly
				FROM vtiger_field WHERE $uniqueFieldsRestriction AND vtiger_field.block IN (".
				generateQuestionMarks($blockid_list) . ') AND vtiger_field.displaytype IN (1,2,4,5) and vtiger_field.presence in (0,2) ORDER BY block,sequence';
			$params = array($tabid, $blockid_list);
		} elseif ($profileGlobalPermission[1] == 0) { // view all
			$profileList = getCurrentUserProfileList();
			$sql = "SELECT distinct $selectSql, vtiger_profile2field.readonly
				FROM vtiger_field
				INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
				WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ') AND vtiger_field.displaytype IN (1,2,4,5) and '.
					'vtiger_field.presence in (0,2) AND vtiger_profile2field.profileid IN (' . generateQuestionMarks($profileList) . ') ORDER BY block,sequence';
			$params = array($tabid, $blockid_list, $profileList);
		} else {
			$profileList = getCurrentUserProfileList();
			$sql = "SELECT distinct $selectSql, vtiger_profile2field.readonly
				FROM vtiger_field
				INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
				INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
				WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ') AND vtiger_field.displaytype IN (1,2,4,5) and '.
					'vtiger_field.presence in (0,2) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ('.
					generateQuestionMarks($profileList) . ") ORDER BY block,sequence";
			$params = array($tabid, $blockid_list, $profileList);
		}
		$result = $adb->pquery($sql, $params);

		// Added to unset the previous record's related listview session values
		coreBOS_Session::delete('rlvs');

		$getBlockInfo = getDetailBlockInformation($module, $result, $col_fields, $tabid, $block_label);
	} else {
		if ($info_type != '') {
			if ($is_admin == true || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == "Emails") {
				$sql = "SELECT $selectSql, vtiger_field.readonly
					FROM vtiger_field
					WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND info_type = ? and ".
						'vtiger_field.presence in (0,2) ORDER BY block,sequence';
				$params = array($tabid, $blockid_list, $info_type);
			} else {
				$profileList = getCurrentUserProfileList();
				$sql = "SELECT distinct $selectSql, vtiger_field.readonly
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND info_type = ? AND ".
						'vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ('.
						generateQuestionMarks($profileList) . ') and vtiger_field.presence in (0,2) ORDER BY block,sequence';
				$params = array($tabid, $blockid_list, $info_type, $profileList);
			}
		} else {
			if ($is_admin == true || $profileGlobalPermission[2] == 0 || $module == 'Users' || $module == "Emails") {
				$sql = "SELECT $selectSql, vtiger_field.readonly
					FROM vtiger_field
					WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check and ".
						'vtiger_field.presence in (0,2) ORDER BY block,sequence';
				$params = array($tabid, $blockid_list);
			} else {
				$profileList = getCurrentUserProfileList();
				$sql = "SELECT distinct $selectSql, vtiger_field.readonly
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_field.block IN (" . generateQuestionMarks($blockid_list) . ") AND $display_type_check AND ".
						'vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ('.
						generateQuestionMarks($profileList) . ') and vtiger_field.presence in (0,2) ORDER BY block,sequence';
				$params = array($tabid, $blockid_list, $profileList);
			}
		}
		$result = $adb->pquery($sql, $params);
		$getBlockInfo = getBlockInformation($module, $result, $col_fields, $tabid, $block_label, $mode);
	}
	if (count($getBlockInfo) > 0) {
		foreach ($getBlockInfo as $label => $contents) {
			if (empty($getBlockInfo[$label])) {
				unset($getBlockInfo[$label]);
			}
		}
	}
	coreBOS_Session::set('BLOCKINITIALSTATUS', $aBlockStatus);
	$log->debug('Exiting getBlocks');
	return $getBlockInfo;
}

/**
 * This function returns the customized blocks and its template.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create)
 * This function returns an array
 */
function getCustomBlocks($module, $disp_view) {
	global $log, $adb;
	$log->debug("Entering getCustomBlocks(" . $module . "," . $disp_view . ") method ...");
	$tabid = getTabid($module);
	$query = "select blockid,blocklabel,isrelatedlist from vtiger_blocks where tabid=? and $disp_view=0 and visible = 0 order by sequence";
	$result = $adb->pquery($query, array($tabid));
	$noofrows = $adb->num_rows($result);
	$block_list = array();
	$block_label = array();
	for ($i = 0; $i < $noofrows; $i++) {
		$hasrelatedlist = $adb->query_result($result, $i, 'isrelatedlist');
		$blockid = $adb->query_result($result, $i, "blockid");
		$block_label[$blockid] = $adb->query_result($result, $i, "blocklabel");
		$sLabelVal = getTranslatedString($block_label[$blockid], $module);
		$block_list[] = $sLabelVal;
		$inlineEditBlock = "modules/$module/{$block_label[$blockid]}_edit.tpl";
		$inlineDetailBlock = "modules/$module/{$block_label[$blockid]}_detail.tpl";
		if (($disp_view == 'edit_view' || $disp_view == 'create' || $disp_view == 'create_view') && file_exists("Smarty/templates/$inlineEditBlock")) {
			$block_list[$sLabelVal] = array('custom' => true, 'relatedlist' => false, 'tpl' => $inlineEditBlock);
		} elseif ($disp_view == 'detail_view' && file_exists("Smarty/templates/$inlineDetailBlock")) {
			$block_list[$sLabelVal] = array('custom' => true, 'relatedlist' => false, 'tpl' => $inlineDetailBlock);
		} elseif ($hasrelatedlist>0) {
			$block_list[$sLabelVal] = array('custom' => false, 'relatedlist' => true, 'tpl' => '');
		} else {
			$block_list[$sLabelVal] = array('custom' => false, 'relatedlist' => false, 'tpl' => '');
		}
	}
	return $block_list;
}

/**
 * This function is used to get the display type.
 * Takes the input parameter as $mode
 * This returns string type value
 */
function getView($mode) {
	global $log;
	$log->debug('Entering/Exiting getView(' . $mode . ')');
	return ($mode == 'edit' ? 'edit_view' : 'create_view');
}

/**
 * This function is used to get the blockid of the block for a given module.
 * Takes the input parameter as $tabid - module tabid and $label - block label
 * This returns integer type value
 */
function getBlockId($tabid, $label) {
	global $log, $adb;
	$log->debug('Entering getBlockId(' . $tabid . ',' . $label . ') method ...');
	$blockid = '';
	$result = $adb->pquery('select blockid from vtiger_blocks where tabid=? and blocklabel = ?', array($tabid, $label));
	if ($adb->num_rows($result) == 1) {
		$blockid = $adb->query_result($result, 0, 'blockid');
	}
	$log->debug('Exiting getBlockId method ...');
	return $blockid;
}

/**
 * This function is used to get the Parent and Child tab relation array.
 * Takes no parameter and get the data from parent_tabdata.php and tabdata.php
 * This returns array type value
 * @deprecated
 */
function getHeaderArray() {
	return array();
}

/**
 * This function is used to get the Parent Tab name for a given parent tab id.
 * Takes the input parameter as $parenttabid - Parent tab id
 * This returns value string type
 * @deprecated
 */
function getParentTabName($parenttabid) {
	return 'ptab';
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes the input parameter as $module - module name
 * This returns value string type
 * @deprecated
 */
function getParentTabFromModule($module) {
	return $module;
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes no parameter but gets the parenttab value from form request
 * This returns value string type
 * @deprecated
 */
function getParentTab() {
	return 'ptab';
}

/**
 * This function is used to get the days in between the current time and the modified time of an entity .
 * Takes the input parameter as $id - crmid  it will calculate the number of days in between the
 * the current time and the modified time from the vtiger_crmentity table and return the result as a string.
 * The return format is updated <No of Days> day ago <(date when updated)>
 */
function updateInfo($id) {
	global $log, $adb, $app_strings;
	$log->debug("Entering updateInfo(" . $id . ") method ...");
	$query = 'SELECT modifiedtime, modifiedby FROM vtiger_crmentity WHERE crmid = ?';
	$result = $adb->pquery($query, array($id));
	$modifiedtime = $adb->query_result($result, 0, 'modifiedtime');
	$modifiedby_id = $adb->query_result($result, 0, 'modifiedby');
	$modifiedby = $app_strings['LBL_BY'] . getOwnerName($modifiedby_id);
	$date = new DateTimeField($modifiedtime);
	$modifiedtime = DateTimeField::convertToDBFormat($date->getDisplayDate());
	$current_time = date('Y-m-d H:i:s');
	$values = explode(' ', $modifiedtime);
	$date_info = explode('-', $values[0]);
	//$time_info = explode(':', $values[1]);
	$date = $date_info[2] . ' ' . $app_strings[date("M", mktime(0, 0, 0, $date_info[1], $date_info[2], $date_info[0]))] . ' ' . $date_info[0];
	$time_modified = strtotime($modifiedtime);
	$time_now = strtotime($current_time);
	$days_diff = (int) (($time_now - $time_modified) / (60 * 60 * 24));
	if ($days_diff == 0) {
		$update_info = $app_strings['LBL_UPDATED_TODAY'] . " (" . $date . ")" . ' ' . $modifiedby;
	} elseif ($days_diff == 1) {
		$update_info = $app_strings['LBL_UPDATED'] . " " . $days_diff . " " . $app_strings['LBL_DAY_AGO'] . " (" . $date . ")" . ' ' . $modifiedby;
	} else {
		$update_info = $app_strings['LBL_UPDATED'] . " " . $days_diff . " " . $app_strings['LBL_DAYS_AGO'] . " (" . $date . ")" . ' ' . $modifiedby;
	}
	$log->debug("Exiting updateInfo method ...");
	return $update_info;
}

/**
 * This function is used to get the Product Images for the given Product
 * It accepts the product id as argument and returns the Images with the script for
 * rotating the product Images
 */
function getProductImages($id) {
	global $adb, $log;
	$log->debug("Entering getProductImages(" . $id . ") method ...");
	$query = 'select imagename from vtiger_products where productid=?';
	$result = $adb->pquery($query, array($id));
	$imagename = $adb->query_result($result, 0, 'imagename');

	if ($imagename != '') {
		$script = implode(',', array_map(
			function ($val) {
				return "\"$val\"";
			},
			explode('###', $imagename)
		));
		$log->debug('Exiting getProductImages method ...');
		return "<script>var ProductImages = new Array($script);</script>";
	}
	$log->debug('Exiting getProductImages method ...');
	return '';
}

/**
 * This function is used to save the Images
 * It acceps the File lists,modulename,id and the mode as arguments
 * It returns the array details of the upload
 * @deprecated
 */
function SaveImage($files, $module, $id, $mode) {
	global $log, $root_directory;
	$log->debug("Entering SaveImage($files, $module, $id, $mode)");
	$uploaddir = $root_directory . 'cache/' . $module . '/'; //set this to which location you need to give the contact image
	$log->info('The Location to Save the Contact Image is ' . $uploaddir);
	$file_path_name = $files['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = vtlib_purify($_REQUEST['imagename_hidden']);
	} else {
		//allowed filename like UTF-8 Character
		$file_name = ltrim(basename(' ' . $file_path_name)); // basename($file_path_name);
	}
	$image_error = 'false';
	$saveimage = 'true';
	if ($file_name != '') {
		$log->debug('Contact Image is given for uploading');
		$image_name_val = file_exist_fn($file_name, 0);

		$errormessage = '';

		$move_upload_status = move_uploaded_file($files['imagename']['tmp_name'], $uploaddir . $image_name_val);
		$image_error = 'false';

		//if there is an error in the uploading of image

		$filetype = $files['imagename']['type'];
		$filesize = $files['imagename']['size'];

		$filetype_array = explode('/', $filetype);
		//$file_type_val_image = strtolower($filetype_array[0]);
		$file_type_val = strtolower($filetype_array[1]);
		$log->info('File type of the Contact Image: ' . $file_type_val);
		//checking the uploaded image is if an image type or not
		if (!$move_upload_status) { //if any error during file uploading
			$log->debug('Error is present in uploading Contact Image.');
			$errorCode = $files['imagename']['error'];
			if ($errorCode == 4) {
				$errormessage = 'no-image';
				$saveimage = 'false';
				$image_error = 'true';
			} elseif ($errorCode == 2) {
				$errormessage = 2;
				$saveimage = 'false';
				$image_error = 'true';
			} elseif ($errorCode == 3) {
				$errormessage = 3;
				$saveimage = 'false';
				$image_error = 'true';
			}
		} else {
			$log->debug('Successfully uploaded the Contact Image.');
			if ($filesize != 0) {
				$validExtension = array('jpeg', 'png', 'jpg', 'pjpeg', 'x-png', 'gif');
				if (in_array($file_type_val, $validExtension)) { //Checking whether the file is an image or not
					$saveimage = 'true';
					$image_error = 'false';
				} else {
					$savelogo = 'false';
					$image_error = 'true';
					$errormessage = 'image';
				}
			} else {
				$savelogo = 'false';
				$image_error = 'true';
				$errormessage = 'invalid';
			}
		}
	} else { //if image is not given
		$log->debug('Contact Image is not given for uploading.');
		if ($mode == 'edit' && $image_error == 'false') {
			if ($module = 'contact') {
				$image_name_val = getContactImageName($id);
			} elseif ($module = 'user') {
				$image_name_val = getUserImageName($id);
			}
			$saveimage = 'true';
		} else {
			$image_name_val = '';
		}
	}
	$return_value = array(
		'imagename' => $image_name_val,
		'imageerror' => $image_error,
		'errormessage' => $errormessage,
		'saveimage' => $saveimage,
		'mode' => $mode,
	);
	$log->debug('Exiting SaveImage');
	return $return_value;
}

/**
 * This function is used to generate file name if more than one image with same name is added to a given Product.
 * Param $filename - product file name
 * Param $exist - number time the file name is repeated.
 */
function file_exist_fn($filename, $exist) {
	global $log, $uploaddir;
	$log->debug("Entering file_exist_fn($filename, $exist)");

	if (!isset($exist)) {
		$exist = 0;
	}
	$filename_path = $uploaddir . $filename;
	if (file_exists($filename_path)) { //Checking if the file name already exists in the directory
		if ($exist != 0) {
			$explode_name = explode('_', $filename);
			$implode_array = array();
			for ($j = 1, $jMax = count($explode_name); $j < $jMax; $j++) {
				$implode_array[] = $explode_name[$j];
			}
			$implode_name = implode('_', $implode_array);
		} else {
			$implode_name = $filename;
		}
		$exist++;
		$filename_val = $exist . '_' . $implode_name;
		$testfilename = file_exist_fn($filename_val, $exist);
		if ($testfilename != '') {
			$log->debug('Exiting file_exist_fn');
			return $testfilename;
		}
	} else {
		$log->debug('Exiting file_exist_fn');
		return $filename;
	}
}

/**
 * This function is used get the User Count.
 * @return array which has the total users, total admin users, and the total non admin users
 */
function UserCount() {
	global $log, $adb;
	$log->debug('Entering UserCount() method ...');
	$result = $adb->pquery('select count(*) as n from vtiger_users where deleted = 0', array());
	$user_count = $adb->query_result($result, 0, 'n');
	$result = $adb->pquery("select count(*) as n from vtiger_users where deleted = 0 AND is_admin != 'on'", array());
	$nonadmin_count = $adb->query_result($result, 0, 'n');
	$count = array('user' => $user_count, 'admin' => $user_count - $nonadmin_count, 'nonadmin' => $nonadmin_count);
	$log->debug('Exiting UserCount method ...');
	return $count;
}

/**
 * This function is used to create folders recursively.
 * Param $dir - directory name
 * Param $mode - directory access mode
 * Param $recursive - create directory recursive, default true
 */
function mkdirs($dir, $mode = 0777, $recursive = true) {
	global $log;
	$log->debug("Entering mkdirs(" . $dir . "," . $mode . "," . $recursive . ") method ...");
	if (is_null($dir) || $dir === "") {
		$log->debug("Exiting mkdirs method ...");
		return false;
	}
	if (is_dir($dir) || $dir === "/") {
		$log->debug("Exiting mkdirs method ...");
		return true;
	}
	if (mkdirs(dirname($dir), $mode, $recursive)) {
		$log->debug("Exiting mkdirs method ...");
		return mkdir($dir, $mode);
	}
	$log->debug("Exiting mkdirs method ...");
	return false;
}

/**
 * This function is used to set the Object values from the REQUEST values.
 * @param  object reference $focus - reference of the object
 */
function setObjectValuesFromRequest($focus) {
	global $log;
	$moduleName = get_class($focus);
	$log->debug("Entering setObjectValuesFromRequest($moduleName) method ...");
	if (isset($_REQUEST['record']) && (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit')) {
		$focus->id = $_REQUEST['record'];
	}
	if (isset($_REQUEST['mode'])) {
		$focus->mode = $_REQUEST['mode'];
	}
	foreach ($focus->column_fields as $fieldname => $val) {
		if (isset($_REQUEST[$fieldname])) {
			if (is_array($_REQUEST[$fieldname])) {
				$value = $_REQUEST[$fieldname];
			} else {
				$value = trim($_REQUEST[$fieldname]);
			}
			$focus->column_fields[$fieldname] = $value;
		} elseif (isset($_REQUEST[$fieldname.'_hidden'])) {
			$value = trim($_REQUEST[$fieldname.'_hidden']);
			$focus->column_fields[$fieldname] = $value;
		}
	}
	if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'EditView' || $_REQUEST['action'] == 'EventEditView')) {
		$cbfrommodule = $moduleName;
		$cbfrom = CRMEntity::getInstance($cbfrommodule);
		$bmapname = $moduleName.'2'.$moduleName;
		$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
		if (!empty($_REQUEST['cbfromid'])) {
			$cbfromid = vtlib_purify($_REQUEST['cbfromid']);
			$cbfrommodule = getSalesEntityType($cbfromid);
			$bmapname = $cbfrommodule.'2'.$moduleName;
			$cbfrom = CRMEntity::getInstance($cbfrommodule);
			$cbfrom->retrieve_entity_info($cbfromid, $cbfrommodule);
			$cbMapidFromid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname));
			if ($cbMapidFromid) {
				$cbMapid = $cbMapidFromid;
			}
		}
		if ($cbMapid) {
			$cbMap = cbMap::getMapByID($cbMapid);
			$focus->column_fields = $cbMap->Mapping($cbfrom->column_fields, $focus->column_fields);

			if (isset($focus->column_fields['cbcustominfo1'])) {
				$_REQUEST['cbcustominfo1'] = $focus->column_fields['cbcustominfo1'];
			}

			if (isset($focus->column_fields['cbcustominfo2'])) {
				$_REQUEST['cbcustominfo2'] = $focus->column_fields['cbcustominfo2'];
			}
		}
	}
	$focus = cbEventHandler::do_filter('corebos.filter.editview.setObjectValues', $focus);
	$log->debug("Exiting setObjectValuesFromRequest method ...");
}

/**
 * Function to write the tabid and name to a flat file tabdata.php so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */
function create_tab_data_file() {
	global $log;
	$log->debug("Entering create_tab_data_file() method ...");
	$log->info("creating vtiger_tabdata file");
	global $adb;
	//$sql = "select * from vtiger_tab";
	// vtlib customization: Disabling the tab item based on presence
	$sql = "select * from vtiger_tab where presence in (0,2)";
	// END

	$result = $adb->pquery($sql, array());
	$num_rows = $adb->num_rows($result);
	$result_array = array();
	$seq_array = array();
	$ownedby_array = array();

	for ($i = 0; $i < $num_rows; $i++) {
		$tabid = $adb->query_result($result, $i, 'tabid');
		$tabname = $adb->query_result($result, $i, 'name');
		$presence = $adb->query_result($result, $i, 'presence');
		$ownedby = $adb->query_result($result, $i, 'ownedby');
		$result_array[$tabname] = $tabid;
		$seq_array[$tabid] = $presence;
		$ownedby_array[$tabid] = $ownedby;
	}

	//Constructing the actionname=>actionid array
	$actionid_array = array();
	$sql1 = "select * from vtiger_actionmapping";
	$result1 = $adb->pquery($sql1, array());
	$num_seq1 = $adb->num_rows($result1);
	for ($i = 0; $i < $num_seq1; $i++) {
		$actionname = $adb->query_result($result1, $i, 'actionname');
		$actionid = $adb->query_result($result1, $i, 'actionid');
		$actionid_array[$actionname] = $actionid;
	}

	//Constructing the actionid=>actionname array with securitycheck=0
	$actionname_array = array();
	$sql2 = "select * from vtiger_actionmapping where securitycheck=0";
	$result2 = $adb->pquery($sql2, array());
	$num_seq2 = $adb->num_rows($result2);
	for ($i = 0; $i < $num_seq2; $i++) {
		$actionname = $adb->query_result($result2, $i, 'actionname');
		$actionid = $adb->query_result($result2, $i, 'actionid');
		$actionname_array[$actionid] = $actionname;
	}

	$filename = 'tabdata.php';
	VTCacheUtils::emptyTabidInfo();
	if (function_exists('opcache_invalidate')) {
		opcache_invalidate('tabdata.php', true);
	}

	if (file_exists($filename)) {
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, 'w+')) {
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once 'modules/Users/CreateUserPrivilegeFile.php';
			$newbuf = '';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_info_array=" . constructArray($result_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_seq_array=" . constructArray($seq_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$tab_ownedby_array=" . constructArray($ownedby_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_id_array=" . constructSingleStringKeyAndValueArray($actionid_array) . ";\n";
			$newbuf .= "\n";
			$newbuf .= "\$action_name_array=" . constructSingleStringValueArray($actionname_array) . ";\n";
			$newbuf .= "?>";
			fputs($handle, $newbuf);
			fclose($handle);
		} else {
			echo "The file $filename is not writable";
		}
	} else {
		echo "The file $filename does not exist";
		$log->debug("Exiting create_tab_data_file method ...");
		return;
	}
}

/**
 * Function to write the parenttabid and name to a flat file parent_tabdata.php so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 * @deprecated
 */
function create_parenttab_data_file() {
	return null;
}

/**
 * This function is used to get the all the modules that have Quick Create Feature.
 * Returns Tab Name and Tablabel.
 */
function getQuickCreateModules() {
	global $log, $adb;
	$log->debug('Entering getQuickCreateModules()');

	$qc_query = "select distinct vtiger_tab.tablabel,vtiger_tab.name
		from vtiger_field
		inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid 
		where quickcreate in (0,2) and vtiger_tab.presence != 1 and vtiger_tab.name != 'Calendar' and vtiger_tab.name != 'Events'";

	$result = $adb->pquery($qc_query, array());
	$noofrows = $adb->num_rows($result);
	$return_qcmodule = array();
	for ($i = 0; $i < $noofrows; $i++) {
		$tablabel = $adb->query_result($result, $i, 'tablabel');

		$tabname = $adb->query_result($result, $i, 'name');
		$tablabel = getTranslatedString("SINGLE_$tabname", $tabname);
		if (isPermitted($tabname, 'CreateView', '') == 'yes') {
			$return_qcmodule[] = $tablabel;
			$return_qcmodule[] = $tabname;
		}
	}
	if (count($return_qcmodule) > 0) {
		$return_qcmodule = array_chunk($return_qcmodule, 2);
	}

	$log->debug('Exiting getQuickCreateModules');
	return $return_qcmodule;
}

/**
 * This function is used to get the Quick create form field parameters for a given module.
 * Param $module - module name
 * returns the value in array format
 */
function QuickCreate($module) {
	global $log, $adb, $current_user;
	$log->debug("Entering QuickCreate($module)");

	$tabid = getTabid($module);

	//Adding Security Check
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';
	if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
		$quickcreate_query = "select *
			from vtiger_field
			where (quickcreate in (0,2) or typeofdata like '%~M%') and tabid = ? and vtiger_field.presence in (0,2) and displaytype != 2 order by quickcreatesequence";
		$params = array($tabid);
	} else {
		$profileList = getCurrentUserProfileList();
		$quickcreate_query = 'SELECT distinct vtiger_field.*
			FROM vtiger_field
			INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
			INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
			WHERE vtiger_field.tabid=? AND quickcreate in (0,2) AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly = 0 AND '.
				'vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (' . generateQuestionMarks($profileList) . ') and '.
				'vtiger_field.presence in (0,2) and displaytype != 2 ORDER BY quickcreatesequence';
		$params = array($tabid, $profileList);
	}
	$result = $adb->pquery($quickcreate_query, $params);
	$noofrows = $adb->num_rows($result);
	$fieldName_array = array();
	for ($i = 0; $i < $noofrows; $i++) {
		//$fieldtablename = $adb->query_result($result, $i, 'tablename');
		$uitype = $adb->query_result($result, $i, 'uitype');
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$maxlength = $adb->query_result($result, $i, 'maximumlength');
		$generatedtype = $adb->query_result($result, $i, 'generatedtype');
		$typeofdata = $adb->query_result($result, $i, 'typeofdata');
		$defaultvalue = $adb->query_result($result, $i, 'defaultvalue');
		$col_fields[$fieldname] = $defaultvalue;

		//to get validationdata
		$fldLabel_array = array();
		$fldLabel_array[getTranslatedString($fieldlabel)] = $typeofdata;
		$fieldName_array[$fieldname] = $fldLabel_array;

		// These fields should not be shown in the UI as they are already shown as part of other fields, but are required for validation.
		if (($fieldname == 'time_start' || $fieldname == 'time_end') && $module!='Timecontrol') {
			continue;
		}

		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields, $generatedtype, $module, '', $typeofdata);
		$qcreate_arr[] = $custfld;
	}
	for ($i = 0, $j = 0, $iMax = count($qcreate_arr); $i < $iMax; $j++) {
		$key1 = $qcreate_arr[$i];
		if (isset($qcreate_arr[$i + 1]) && is_array($qcreate_arr[$i + 1]) && ($key1[0][0]!=19 && $key1[0][0]!=20)) {
			$key2 = $qcreate_arr[$i + 1];
		} else {
			$key2 = array();
		}
		if ($key1[0][0]!=19 && $key1[0][0]!=20) {
			$return_data[$j] = array(0 => $key1, 1 => $key2);
			$i+=2;
		} else {
			$return_data[$j] = array(0 => $key1);
			$i++;
		}
	}
	$form_data['form'] = $return_data;
	$form_data['data'] = $fieldName_array;
	$log->debug("Exiting QuickCreate method ..." . print_r($form_data, true));
	return $form_data;
}

function getUserslist($setdefval = true, $selecteduser = '') {
	global $log, $current_user, $module;
	$log->debug('Entering getUserslist()');
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';
	require 'user_privileges/sharing_privileges_' . $current_user->id . '.php';
	$tabid = getTabid($module);
	if ($is_admin==false && $profileGlobalPermission[2]==1 && (is_null($tabid) || $defaultOrgSharingPermission[$tabid]==3 || $defaultOrgSharingPermission[$tabid]==0)) {
		$user_array = get_user_array(false, "Active", $current_user->id, 'private');
	} else {
		$user_array = get_user_array(false, "Active", $current_user->id);
	}
	$users_combo = get_select_options_array($user_array, $current_user->id);
	$change_owner = '';
	foreach ($users_combo as $userid => $value) {
		foreach ($value as $username => $selected) {
			if ($setdefval == false) {
				$change_owner .= "<option value=$userid>" . $username . "</option>";
			} elseif (is_numeric($selecteduser)) {
				$change_owner .= "<option value=$userid ". ($userid==$selecteduser ? 'selected' : '') .">" . $username . "</option>";
			} else {
				$change_owner .= "<option value=$userid $selected>" . $username . "</option>";
			}
		}
	}
	$log->debug('Exiting getUserslist');
	return $change_owner;
}

function getGroupslist() {
	global $log, $adb, $module, $current_user;
	$log->debug("Entering getGroupslist() method ...");
	require 'user_privileges/user_privileges_' . $current_user->id . '.php';
	require 'user_privileges/sharing_privileges_' . $current_user->id . '.php';

	//Commented to avoid security check for groups
	$tabid = getTabid($module);
	if ($is_admin==false && $profileGlobalPermission[2]==1 && ($defaultOrgSharingPermission[$tabid]==3 || $defaultOrgSharingPermission[$tabid]==0)) {
		$result = get_current_user_access_groups($module);
	} else {
		$result = get_group_options();
	}
	$groups_combo = array();
	if ($result) {
		$nameArray = $adb->fetch_array($result);
	}
	if (!empty($nameArray)) {
		if ($is_admin == false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[$tabid] == 3 || $defaultOrgSharingPermission[$tabid] == 0)) {
			$group_array = get_group_array(false, "Active", $current_user->id, 'private');
		} else {
			$group_array = get_group_array(false, "Active", $current_user->id);
		}
		$groups_combo = get_select_options_array($group_array, $current_user->id);
	}
	$change_groups_owner = '';
	if (count($groups_combo) > 0) {
		foreach ($groups_combo as $groupid => $value) {
			foreach ($value as $groupname => $selected) {
				$change_groups_owner .= "<option value=$groupid $selected >" . $groupname . "</option>";
			}
		}
	}
	$log->debug('Exiting getGroupslist');
	return $change_groups_owner;
}

/**
 * 	Function to Check for Security whether the Buttons are permitted in List/Edit/Detail View of all Modules
 * 	@param string $module -- module name
 * 	Returns an array with permission as Yes or No
 * */
function Button_Check($module) {
	global $log;
	$log->debug("Entering Button_Check($module)");
	$permit_arr = array(
		'EditView' => '',
		'CreateView' => '',
		'index' => '',
		'Import' => '',
		'Export' => '',
		'Merge' => '',
		'DuplicatesHandling' => '');

	foreach ($permit_arr as $action => $perr) {
		$tempPer = isPermitted($module, $action, '');
		$permit_arr[$action] = $tempPer;
	}
	$permit_arr['Calendar'] = isPermitted('Calendar', 'index', '');
	$permit_arr['moduleSettings'] = isModuleSettingPermitted($module);
	$log->debug('Exiting Button_Check');
	return $permit_arr;
}

/**
 * 	Function to Check whether the User is allowed to delete a particular record from listview of each module using
 * 	mass delete button.
 * 	@param string $module -- module name
 * 	@param array $ids_list -- Record id
 * 	Returns the Record Names of each module that is not permitted to delete
 * */
function getEntityName($module, $ids_list) {
	global $log;
	$log->debug("Entering getEntityName($module)");
	if ($module == 'Events') {
		$module = 'Calendar';
	}
	if ($module != '') {
		$ids_list = (array)$ids_list;
		if (count($ids_list) <= 0) {
			return array();
		}
		$entityDisplay = array();
		$entity_field_info = getEntityFieldNames($module);
		//$tableName = $entity_field_info['tablename'];
		$fieldsName = $entity_field_info['fieldname'];
		//$moduleName = $entity_field_info['modulename'];
		//$entityIdField = $entity_field_info['entityidfield'];
		$entity_FieldValue = getEntityFieldValues($entity_field_info, $ids_list);

		foreach ($entity_FieldValue as $entityInfo) {
			foreach ($entityInfo as $key => $entityName) {
				$fieldValues = $entityName;
				$entityDisplay[$key] = getEntityFieldNameDisplay($module, $fieldsName, $fieldValues);
			}
		}
		return $entityDisplay;
	}
	$log->debug('Exiting getEntityName');
}

/*
 * @deprecated
 */
function getAllParenttabmoduleslist() {
	return array();
}

/**
 * 	This function is used to decide the File Storage Path in where we will upload the file in the server.
 * 	return string $filepath  - filepath inwhere the file should be stored in the server will be return
 */
function decideFilePath() {
	global $log;
	$log->debug('Entering decideFilePath()');

	$filepath = GlobalVariable::getVariable('Application_Storage_Directory', 'storage/');
	if (substr($filepath, -1)!='/') {
		$filepath.='/';
	}

	$saveStrategy = GlobalVariable::getVariable('Application_Storage_SaveStrategy', 'dates');
	switch (strtolower($saveStrategy)) {
		case 'crmid':
			// CRMID in folder
			if (isset($_REQUEST['return_id'])) {
				$retid = vtlib_purify($_REQUEST['return_id']);
				if (is_numeric($retid) && $retid > 0 && $retid < 100000000000) {
					$filepath .= $retid . '/';
				}
			}

			if (!is_dir($filepath)) {
				//create new folder
				mkdir($filepath);
			}
			$log->debug("Strategy CRMID filepath=\"$filepath\"");
			break;
		case 'dates':
		default:
			$year = date('Y');
			$month = date('F');
			$day = date('j');
			$week = '';

			if (!is_dir($filepath . $year)) {
				//create new folder
				@mkdir($filepath . $year);
			}

			if (!is_dir($filepath . $year . '/' . $month)) {
				//create new folder
				@mkdir($filepath . "$year/$month");
			}

			if ($day > 0 && $day <= 7) {
				$week = 'week1';
			} elseif ($day > 7 && $day <= 14) {
				$week = 'week2';
			} elseif ($day > 14 && $day <= 21) {
				$week = 'week3';
			} elseif ($day > 21 && $day <= 28) {
				$week = 'week4';
			} else {
				$week = 'week5';
			}
			$ymw = "$year/$month/$week";
			if (!is_dir($filepath . $ymw)) {
				//create new folder
				@mkdir($filepath . $ymw);
			}

			$filepath = $filepath . $ymw . '/';
			$log->debug("Year=$year & Month=$month & week=$week && filepath=\"$filepath\"");
			break;
	}
	$log->debug('Exiting decideFilePath');
	return $filepath;
}

/**
 * 	This function is used to check whether the attached file is a image file or not
 * 	@param array $file_details  - files array which contains all the uploaded file details
 * 	return string $save_image - true or false. if the image can be uploaded then true will return otherwise false.
 */
function validateImageFile($file_details) {
	global $log, $app_strings;
	$log->debug('Entering validateImageFile('.print_r($file_details, true).')');

	$file_type_details = explode('/', $file_details['type']);
	$filetype = $file_type_details['1'];

	if (!empty($filetype)) {
		$filetype = strtolower($filetype);
	}
	if ($filetype == 'jpeg' || $filetype == 'png' || $filetype == 'jpg' || $filetype == 'pjpeg' || $filetype == 'x-png' || $filetype == 'gif' || $filetype == 'bmp') {
		$saveimage = 'true';
	} else {
		$saveimage = 'false';
		$imgtypeerror = coreBOS_Session::get('image_type_error');
		coreBOS_Session::set('image_type_error', $imgtypeerror."<br> &nbsp;&nbsp;<b>" . $file_details['name'] . "</b>" . $app_strings['MSG_IS_NOT_UPLOADED']);
		$log->debug("Invalid Image type == $filetype");
	}

	$log->debug("Exiting validateImageFile. saveimage=$saveimage");
	return $saveimage;
}


/**
 * Validate image metadata.
 * @param mixed $data
 * @return bool
 */
function validateImageMetadata($data) {
	if (is_array($data)) {
		foreach ($data as $value) {
			if (!validateImageMetadata($value)) {
				return false;
			}
		}
	} else {
		if (preg_match('/(<\?php?(.*?))/i', $data) === 1
			|| preg_match('/(<?script(.*?)language(.*?)=(.*?)"(.*?)php(.*?)"(.*?))/i', $data) === 1
			|| stripos($data, '<?=') !== false
			|| stripos($data, '<%=') !== false
			|| stripos($data, '<? ') !== false
			|| stripos($data, '<% ') !== false
		) {
			return false;
		}
	}
	return true;
}

/**
 * 	This function is used to check whether the attached file has not malicious code injected
 * 	@param string $filename - files array which contains all the uploaded file details
 * 	return bool - true or false. if the image can be uploaded then true will return otherwise false.
 */
function validateImageContents($filename) {

	// Check for php code injection
	$contents = file_get_contents($filename);
	if (preg_match('/(<\?php?(.*?))/si', $contents) === 1
		|| preg_match('/(<?script(.*?)language(.*?)=(.*?)"(.*?)php(.*?)"(.*?))/si', $contents) === 1
		|| stripos($contents, '<?=') !== false
		|| stripos($contents, '<%=') !== false
		|| stripos($contents, '<? ') !== false
		|| stripos($contents, '<% ') !== false
	) {
		return false;
	}

	if (function_exists('mime_content_type')) {
		$mimeType = mime_content_type($filename);
	} elseif (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		$mimeType = finfo_file($finfo, $filename);
		finfo_close($finfo);
	} else {
		$mimeType = 'application/octet-stream';
	}

	if (function_exists('exif_read_data')
		&& ($mimeType === 'image/jpeg' || $mimeType === 'image/tiff')
		&& in_array(exif_imagetype($filename), array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM))
	) {
		$imageSize = getimagesize($filename, $imageInfo);
		if ($imageSize
			&& (empty($imageInfo['APP1']) || strpos($imageInfo['APP1'], 'Exif') === 0)
			&& ($exifdata = exif_read_data($filename))
			&& !validateImageMetadata($exifdata)
		) {
			return false;
		}
	}

	if (stripos('<?xpacket', $contents) !== false) {
		return false;
	}

	return true;
}

/**
 * 	This function is used to get the Email Template Details like subject and content for particular template.
 * 	@param integer $templateid  - Template Id for an Email Template
 * 	return array $returndata - Returns Subject, Body of Template of the the particular email template.
 */
function getTemplateDetails($templateid) {
	global $adb, $log;
	$log->debug("Entering into getTemplateDetails($templateid) method ...");
	$returndata = array();
	$result = $adb->pquery('select * from vtiger_emailtemplates where templateid=? or templatename=?', array($templateid,$templateid));
	if ($result && $adb->num_rows($result)>0) {
		$returndata[] = $templateid;
		$returndata[] = $adb->query_result($result, 0, 'body');
		$returndata[] = $adb->query_result($result, 0, 'subject');
		$returndata[] = $adb->query_result($result, 0, 'sendemailfrom');
	}
	$log->debug("Exiting from getTemplateDetails($templateid) method ...");
	return $returndata;
}

/**
 * 	This function is used to merge the Template Details with the email description
 *  @param string $description  -body of the mail(ie template)
 * 	@param integer $tid  - Id of the entity
 *  @param string $parent_type - module of the entity
 * 	return string $description - Returns description, merged with the input template.
 */
function getMergedDescription($description, $id, $parent_type) {
	global $adb, $log, $current_user;
	$log->debug("Entering getMergedDescription ($id, $parent_type)...");
	$token_data_pair = explode('$', $description);
	if (empty($parent_type)) {
		$parent_type = getSalesEntityType($id);
	}
	if (empty($parent_type) || empty($id)) {
		$log->debug('Exiting from getMergedDescription due to no record information ...');
		return $description;
	}
	if (strpos($id, 'x')>0) {
		list($wsid, $id) = explode('x', $id);
	}
	if ($parent_type != 'Users') {
		$emailTemplate = new EmailTemplate($parent_type, $description, $id, $current_user);
		$description = $emailTemplate->getProcessedDescription();
	}
	$token_data_pair = explode('$', $description);
	$fields = array();
	for ($i = 1, $iMax = count($token_data_pair); $i < $iMax; $i+=2) {
		if (strpos($token_data_pair[$i], '-') === false) {
			continue;
		}
		$module = explode('-', $token_data_pair[$i]);
		$fields[$module[0]][] = $module[1];
	}
	if (isset($fields['custom']) && is_array($fields['custom']) && count($fields['custom']) > 0) {
		// Custom date & time fields
		$description = getMergedDescriptionCustomVars($fields, $description);
	}
	if ($parent_type == 'Users' && isset($fields['users']) && is_array($fields['users'])) {
		$columnfields = implode(',', $fields['users']);
		$query = "select $columnfields from vtiger_users where id=?";
		$result = $adb->pquery($query, array($id));
		foreach ($fields['users'] as $columnname) {
			$token_data = '$users-'.$columnname.'$';
			$description = str_replace($token_data, $adb->query_result($result, 0, $columnname), $description);
		}
	}
	$log->debug('Exiting from getMergedDescription');
	return $description;
}

/* Function to merge the custom date & time fields in email templates */
function getMergedDescriptionCustomVars($fields, $description) {
	global $current_language;
	$lang = return_module_language($current_language, 'Reports');
	foreach ($fields['custom'] as $columnname) {
		$token_data = '$custom-' . $columnname . '$';
		$token_value = '';
		switch ($columnname) {
			case 'currentdate':
				$dtformat = GlobalVariable::getVariable('EMail_CustomCurrentDate_Format', '');
				if ($dtformat=='') {
					$mes = date('m')-1;
					$mesi18n = $lang['MONTH_STRINGS'][$mes];
					$token_value = $mesi18n.date(' j, Y');
				} else {
					$token_value = date($dtformat);
				}
				break;
			case 'currenttime':
				$token_value = date('G:i:s T');
				break;
		}
		$description = str_replace($token_data, $token_value, $description);
	}
	return $description;
}

/** 	Function used to retrieve a single field value from database
 * 	@param string $tablename - tablename from which we will retrieve the field value
 * 	@param string $fieldname - fieldname to which we want to get the value from database
 * 	@param string $idname	 - idname which is the name of the entity id in the table like, inoviceid, quoteid, etc.,
 * 	@param int    $id	 - entity id
 * 	return string $fieldval  - field value of the needed fieldname from database will be returned
 */
function getSingleFieldValue($tablename, $fieldname, $idname, $id) {
	global $log, $adb;
	$log->debug("Entering into function getSingleFieldValue($tablename, $fieldname, $idname, $id)");
	$rs = $adb->pquery("select $fieldname from $tablename where $idname = ?", array($id));
	$fieldval = $adb->query_result($rs, 0, $fieldname);
	$log->debug("Exit from function getSingleFieldValue. return value ==> \"$fieldval\"");
	return $fieldval;
}

/** 	Function used to retrieve the announcements from database
 * 	The function accepts no argument and returns the announcements
 * 	return string $announcement  - List of announments for the CRM users
 */
function get_announcements() {
	global $default_charset, $currentModule;
	$announcement = GlobalVariable::getVariable('Application_Announcement', '', $currentModule);
	if ($announcement != '') {
		$announcement = html_entity_decode($announcement, ENT_QUOTES, $default_charset);
		$announcement = vtlib_purify($announcement);
	}
	return $announcement;
}

/**
 *  Function to get recurring info depending on the recurring type
 *  return  $recurObj       - Object of class RecurringType
 */
function getrecurringObjValue() {
	$recurring_data = array();
	if (isset($_REQUEST['recurringtype']) && $_REQUEST['recurringtype'] != null && $_REQUEST['recurringtype'] != '--None--') {
		if (!empty($_REQUEST['date_start'])) {
			$startDate = $_REQUEST['date_start'];
		}
		if (!empty($_REQUEST['calendar_repeat_limit_date'])) {
			$endDate = $_REQUEST['calendar_repeat_limit_date'];
		} elseif (isset($_REQUEST['due_date']) && $_REQUEST['due_date'] != null) {
			$endDate = $_REQUEST['due_date'];
		}
		if (!empty($_REQUEST['time_start'])) {
			$startTime = $_REQUEST['time_start'];
		}
		if (!empty($_REQUEST['time_end'])) {
			$endTime = $_REQUEST['time_end'];
		}

		$recurring_data['startdate'] = $startDate;
		$recurring_data['starttime'] = $startTime;
		$recurring_data['enddate'] = $endDate;
		$recurring_data['endtime'] = $endTime;

		$recurring_data['type'] = $_REQUEST['recurringtype'];
		if ($_REQUEST['recurringtype'] == 'Weekly') {
			if (isset($_REQUEST['sun_flag']) && $_REQUEST['sun_flag'] != null) {
				$recurring_data['sun_flag'] = true;
			}
			if (isset($_REQUEST['mon_flag']) && $_REQUEST['mon_flag'] != null) {
				$recurring_data['mon_flag'] = true;
			}
			if (isset($_REQUEST['tue_flag']) && $_REQUEST['tue_flag'] != null) {
				$recurring_data['tue_flag'] = true;
			}
			if (isset($_REQUEST['wed_flag']) && $_REQUEST['wed_flag'] != null) {
				$recurring_data['wed_flag'] = true;
			}
			if (isset($_REQUEST['thu_flag']) && $_REQUEST['thu_flag'] != null) {
				$recurring_data['thu_flag'] = true;
			}
			if (isset($_REQUEST['fri_flag']) && $_REQUEST['fri_flag'] != null) {
				$recurring_data['fri_flag'] = true;
			}
			if (isset($_REQUEST['sat_flag']) && $_REQUEST['sat_flag'] != null) {
				$recurring_data['sat_flag'] = true;
			}
		} elseif ($_REQUEST['recurringtype'] == 'Monthly') {
			if (isset($_REQUEST['repeatMonth']) && $_REQUEST['repeatMonth'] != null) {
				$recurring_data['repeatmonth_type'] = $_REQUEST['repeatMonth'];
			}
			if ($recurring_data['repeatmonth_type'] == 'date') {
				if (isset($_REQUEST['repeatMonth_date']) && $_REQUEST['repeatMonth_date'] != null) {
					$recurring_data['repeatmonth_date'] = $_REQUEST['repeatMonth_date'];
				} else {
					$recurring_data['repeatmonth_date'] = 1;
				}
			} elseif ($recurring_data['repeatmonth_type'] == 'day') {
				$recurring_data['repeatmonth_daytype'] = $_REQUEST['repeatMonth_daytype'];
				switch ($_REQUEST['repeatMonth_day']) {
					case 0:
						$recurring_data['sun_flag'] = true;
						break;
					case 1:
						$recurring_data['mon_flag'] = true;
						break;
					case 2:
						$recurring_data['tue_flag'] = true;
						break;
					case 3:
						$recurring_data['wed_flag'] = true;
						break;
					case 4:
						$recurring_data['thu_flag'] = true;
						break;
					case 5:
						$recurring_data['fri_flag'] = true;
						break;
					case 6:
						$recurring_data['sat_flag'] = true;
						break;
				}
			}
		}
		if (isset($_REQUEST['repeat_frequency']) && $_REQUEST['repeat_frequency'] != null) {
			$recurring_data['repeat_frequency'] = $_REQUEST['repeat_frequency'];
		}
		return RecurringType::fromUserRequest($recurring_data);
	}
}

/** 	Function used to get the translated string to the input string
 * 	@param string $str - input string which we want to translate
 * 	@return string $str - translated string, if the translated string is available then the translated string other wise original string will be returned
 */
function getTranslatedString($str, $module = '') {
	global $app_strings, $mod_strings, $current_language;
	$temp_mod_strings = ($module != '' ) ? return_module_language($current_language, $module) : $mod_strings;
	$trans_str = (!empty($temp_mod_strings[$str]) ? $temp_mod_strings[$str] : (!empty($app_strings[$str]) ? $app_strings[$str] : $str));
	return $trans_str;
}

/**
 * Get translated currency name string.
 * @param String $str - input currency name
 * @return String $str - translated currency name
 */
function getTranslatedCurrencyString($str) {
	global $app_currency_strings;
	if (isset($app_currency_strings) && isset($app_currency_strings[$str])) {
		return $app_currency_strings[$str];
	}
	return $str;
}

/** 	function used to get the list of importable fields
 * 	@param string $module - module name
 * 	@return array $fieldslist - array with list of fieldnames and the corresponding translated fieldlabels.
 *  The return array will be in the format of [fieldname]=>[fieldlabel] where as the fieldlabel will be translated
 */
function getImportFieldsList($module) {
	global $adb, $log;
	$log->debug("Entering into function getImportFieldsList($module)");

	$tabid = getTabid($module);

	//Here we can add special cases for module basis, ie., if we want the fields of display type 3, we can add
	$displaytype = " displaytype=1 and vtiger_field.presence in (0,2) ";

	$fieldnames = "";
	//For module basis we can add the list of fields for Import mapping
	if ($module == "Leads" || $module == "Contacts") {
		$fieldnames = " fieldname='salutationtype' ";
	}

	//Form the where condition based on tabid , displaytype and extra fields
	$where = " WHERE tabid=? and ( $displaytype ";
	$params = array($tabid);
	if ($fieldnames != "") {
		$where .= " or $fieldnames ";
	}
	$where .= ')';

	//Get the list of fields and form as array with [fieldname] => [fieldlabel]
	$query = "SELECT fieldname, fieldlabel FROM vtiger_field $where";
	$result = $adb->pquery($query, $params);
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$fieldname = $adb->query_result($result, $i, 'fieldname');
		$fieldlabel = $adb->query_result($result, $i, 'fieldlabel');
		$fieldslist[$fieldname] = getTranslatedString($fieldlabel, $module);
	}

	$log->debug("Exit from function getImportFieldsList($module)");
	return $fieldslist;
}

/**     Function to get all the comments for a troubleticket
 *     @param int $ticketid -- troubleticket id
 *     return all the comments as a sequencial string which are related to this ticket
 * */
function getTicketComments($ticketid) {
	global $log;
	$log->debug("Entering getTicketComments(" . $ticketid . ") method ...");
	global $adb;

	$moduleName = getSalesEntityType($ticketid);
	$commentlist = '';
	$sql = 'select comments from vtiger_ticketcomments where ticketid=? order by createdtime';
	$result = $adb->pquery($sql, array($ticketid));
	for ($i = 0; $i < $adb->num_rows($result); $i++) {
		$comment = $adb->query_result($result, $i, 'comments');
		if ($comment != '') {
			$commentlist .= '<br><br>' . $comment;
		}
	}
	if ($commentlist != '') {
		$commentlist = '<br><br>' . getTranslatedString('The comments are', $moduleName) . ' : ' . $commentlist;
	}

	$log->debug('Exiting getTicketComments method ...');
	return $commentlist;
}

/**
 * This function is used to get a random password.
 * @return a random password with alpha numeric characters of length 8
 */
function makeRandomPassword() {
	global $log;
	$log->debug('Entering makeRandomPassword()');
	$salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
	srand((double) microtime() * 1000000);
	$i = 0;
	$pass = '';
	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	$log->debug('Exiting makeRandomPassword');
	return $pass;
}

/**
 * Function to get the UItype for a field by the fieldname.
 * Takes the input as $module - module name,and fieldname of the field
 * returns the uitype, integer type
 */
function getUItypeByFieldName($module, $fieldname) {
	global $log, $adb;
	$log->debug('Entering getUItypeByFieldName(' . $module . ')');
	$tabIdList = array();
	//To find tabid for this module
	$tabIdList[] = getTabid($module);
	if ($module == 'Calendar') {
		$tabIdList[] = getTabid('Events');
	}
	$sql = 'select uitype from vtiger_field where tabid IN (' . generateQuestionMarks($tabIdList) . ') and fieldname=?';
	$result = $adb->pquery($sql, array($tabIdList, $fieldname));
	$uitype = $adb->query_result($result, 0, 'uitype');
	$log->debug('Exiting getUItypeByFieldName');
	return $uitype;
}

/**
 * Function to get the UItype for a field.
 * Takes the input as $module - module name,and columnname of the field
 * returns the uitype, integer type
 */
function getUItype($module, $columnname) {
	global $log, $adb;
	$log->debug("Entering getUItype(" . $module . ") method ...");
	$tabIdList = array();
	//To find tabid for this module
	$tabIdList[] = getTabid($module);
	if ($module == 'Calendar') {
		$tabIdList[] = getTabid('Events');
	}
	$sql = 'select uitype from vtiger_field where tabid IN (' . generateQuestionMarks($tabIdList) . ') and columnname=?';
	$result = $adb->pquery($sql, array($tabIdList, $columnname));
	$uitype = $adb->query_result($result, 0, "uitype");
	$log->debug("Exiting getUItype method ...");
	return $uitype;
}

// This function looks like not used anymore. May have to be removed
function is_emailId($entity_id) {
	global $log, $adb;
	$log->debug("Entering is_EmailId(" . $entity_id . ") method");

	$module = getSalesEntityType($entity_id);
	if ($module == 'Contacts') {
		$sql = 'select email,secondaryemail
			from vtiger_contactdetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			where contactid=?';
		$result = $adb->pquery($sql, array($entity_id));
		$email1 = $adb->query_result($result, 0, 'email');
		$email2 = $adb->query_result($result, 0, 'secondaryemail');
		if ($email1 != '' || $email2 != '') {
			$check_mailids = 'true';
		} else {
			$check_mailids = 'false';
		}
	} elseif ($module == 'Leads') {
		$sql = 'select email,secondaryemail
			from vtiger_leaddetails
			inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			where leadid=?';
		$result = $adb->pquery($sql, array($entity_id));
		$email1 = $adb->query_result($result, 0, 'email');
		$email2 = $adb->query_result($result, 0, 'secondaryemail');
		if ($email1 != '' || $email2 != '') {
			$check_mailids = 'true';
		} else {
			$check_mailids = 'false';
		}
	}
	$log->debug('Exiting is_EmailId() method ...');
	return $check_mailids;
}

/**
 * This function is used to get cvid of default "all" view for any module.
 * @return a cvid of a module
 */
function getCvIdOfAll($module) {
	global $adb, $log;
	$log->debug("Entering getCvIdOfAll($module)");
	$qry_res = $adb->pquery("select cvid from vtiger_customview where viewname='All' and entitytype=?", array($module));
	$cvid = $adb->query_result($qry_res, 0, "cvid");
	$log->debug("Exiting getCvIdOfAll($module)");
	return $cvid;
}

/** gives the option  to display  the tagclouds or not for the current user
 * * @param $id -- user id:: Type integer
 * * @returns true or false in $tag_cloud_view
 * * Added to provide User based Tagcloud
 * */
function getTagCloudView($id = "") {
	global $log, $adb;
	$log->debug("Entering in function getTagCloudView($id)");
	if ($id == '') {
		$tag_cloud_status = 1;
	} else {
		$query = "select visible from vtiger_homestuff where userid=? and stufftype='Tag Cloud'";
		$tagcloudstatusrs = $adb->pquery($query, array($id));
		$tag_cloud_status = $adb->query_result($tagcloudstatusrs, 0, 'visible');
	}

	if ($tag_cloud_status == 0) {
		$tag_cloud_view = 'true';
	} else {
		$tag_cloud_view = 'false';
	}

	$log->debug("Exiting from function getTagCloudView($id)");
	return $tag_cloud_view;
}

/** Stores the option in database to display  the tagclouds or not for the current user
 * * @param $id -- user id:: Type integer
 * * Added to provide User based Tagcloud
 * */
function SaveTagCloudView($id = "") {
	global $log, $adb;
	$log->debug("Entering in function SaveTagCloudView($id)");
	$tag_cloud_status = vtlib_purify($_REQUEST['tagcloudview']);

	if ($tag_cloud_status == "true") {
		$tag_cloud_view = 0;
	} else {
		$tag_cloud_view = 1;
	}

	if (!empty($id)) {
		$query = "update vtiger_homestuff set visible = ? where userid=? and stufftype='Tag Cloud'";
		$adb->pquery($query, array($tag_cloud_view, $id));
	}

	if (!empty($id) && !empty($_REQUEST['showtagas'])) {
		$tag_cloud_showas = vtlib_purify($_REQUEST['showtagas']);
		$query = 'update vtiger_users set showtagas = ? where id=?';
		$adb->pquery($query, array($tag_cloud_showas, $id));
	}
	$log->debug("Exiting from function SaveTagCloudView($id)");
}

/** retrieve show tag cloud as for given user
 ** @param $id -- user id:: Type integer
 ** @returns show tag cloud type
 **/
function getTagCloudShowAs($id) {
	global $log, $adb;
	$log->debug("Entering in function getTagCloudShowAs($id)");
	if (empty($id)) {
		$tag_cloud_status = 'hring';
	} else {
		$query = 'select showtagas from vtiger_users where id=?';
		$rsusr = $adb->pquery($query, array($id));
		if ($rsusr) {
			$tag_cloud_status = $adb->query_result($rsusr, 0, 0);
		} else {
			$tag_cloud_status = 'hring';
		}
	}
	$log->debug("Exiting from function getTagCloudShowAs($id)");
	return $tag_cloud_status;
}

/**     function used to change the Type of Data for advanced filters in custom view and Reports
 * *     @param string $table_name - tablename value from field table
 * *     @param string $column_nametable_name - columnname value from field table
 * *     @param string $type_of_data - current type of data of the field. It is to return the same TypeofData
 * *            if the  field is not matched with the $new_field_details array.
 * *     return string $type_of_data - If the string matched with the $new_field_details array then the Changed
 * *	       typeofdata will return, else the same typeofdata will return.
 * *
 * *     EXAMPLE: If you have a field entry like this:
 * *
 * * 		fieldlabel         | typeofdata | tablename            | columnname       |
 * *	        -------------------+------------+----------------------+------------------+
 * *		Potential Name     | I~O        | vtiger_quotes        | potentialid      |
 * *
 * *     Then put an entry in $new_field_details  like this:
 * *
 * *				"vtiger_quotes:potentialid"=>"V",
 * *
 * *	Now in customview and report's advance filter this field's criteria will be show like string.
 * *
 * */
function ChangeTypeOfData_Filter($table_name, $column_name, $type_of_data) {
	$field = $table_name . ':' . $column_name;
	//Add the field details in this array if you want to change the advance filter field details

	$new_field_details = array(
		//Contacts Related Fields
		'vtiger_contactdetails:accountid' => 'V',
		'vtiger_contactsubdetails:birthday' => 'D',
		'vtiger_contactdetails:email' => 'V',
		'vtiger_contactdetails:secondaryemail' => 'V',
		//Potential Related Fields
		'vtiger_potential:campaignid' => 'V',
		//Account Related Fields
		'vtiger_account:parentid' => 'V',
		'vtiger_account:email1' => 'V',
		'vtiger_account:email2' => 'V',
		//Lead Related Fields
		'vtiger_leaddetails:email' => 'V',
		'vtiger_leaddetails:secondaryemail' => 'V',
		//Documents Related Fields
		'vtiger_senotesrel:crmid' => 'V',
		//Calendar Related Fields
		'vtiger_seactivityrel:crmid' => 'V',
		'vtiger_seactivityrel:contactid' => 'V',
		'vtiger_recurringevents:recurringtype' => 'V',
		//HelpDesk Related Fields
		'vtiger_troubletickets:parent_id' => 'V',
		'vtiger_troubletickets:product_id' => 'V',
		//Product Related Fields
		'vtiger_products:discontinued' => 'C',
		'vtiger_products:vendor_id' => 'V',
		'vtiger_products:parentid' => 'V',
		//Faq Related Fields
		'vtiger_faq:product_id' => 'V',
		//Vendor Related Fields
		'vtiger_vendor:email' => 'V',
		//Quotes Related Fields
		'vtiger_quotes:potentialid' => 'V',
		'vtiger_quotes:inventorymanager' => 'V',
		'vtiger_quotes:accountid' => 'V',
		//Purchase Order Related Fields
		'vtiger_purchaseorder:vendorid' => 'V',
		'vtiger_purchaseorder:contactid' => 'V',
		//SalesOrder Related Fields
		'vtiger_salesorder:potentialid' => 'V',
		'vtiger_salesorder:quoteid' => 'V',
		'vtiger_salesorder:contactid' => 'V',
		'vtiger_salesorder:accountid' => 'V',
		//Invoice Related Fields
		'vtiger_invoice:salesorderid' => 'V',
		'vtiger_invoice:contactid' => 'V',
		'vtiger_invoice:accountid' => 'V',
		//Campaign Related Fields
		'vtiger_campaign:product_id' => 'V',
		//Related List Entries(For Report Module)
		'vtiger_activityproductrel:activityid' => 'V',
		'vtiger_activityproductrel:productid' => 'V',
		'vtiger_campaigncontrel:campaignid' => 'V',
		'vtiger_campaigncontrel:contactid' => 'V',
		'vtiger_campaignleadrel:campaignid' => 'V',
		'vtiger_campaignleadrel:leadid' => 'V',
		'vtiger_cntactivityrel:contactid' => 'V',
		'vtiger_cntactivityrel:activityid' => 'V',
		'vtiger_contpotentialrel:contactid' => 'V',
		'vtiger_contpotentialrel:potentialid' => 'V',
		'vtiger_crmentitynotesrel:crmid' => 'V',
		'vtiger_crmentitynotesrel:notesid' => 'V',
		'vtiger_leadacctrel:leadid' => 'V',
		'vtiger_leadacctrel:accountid' => 'V',
		'vtiger_leadcontrel:leadid' => 'V',
		'vtiger_leadcontrel:contactid' => 'V',
		'vtiger_leadpotrel:leadid' => 'V',
		'vtiger_leadpotrel:potentialid' => 'V',
		'vtiger_pricebookproductrel:pricebookid' => 'V',
		'vtiger_pricebookproductrel:productid' => 'V',
		'vtiger_seactivityrel:crmid' => 'V',
		'vtiger_seactivityrel:activityid' => 'V',
		'vtiger_senotesrel:crmid' => 'V',
		'vtiger_senotesrel:notesid' => 'V',
		'vtiger_seproductsrel:crmid' => 'V',
		'vtiger_seproductsrel:productid' => 'V',
		'vtiger_seticketsrel:crmid' => 'V',
		'vtiger_seticketsrel:ticketid' => 'V',
		'vtiger_vendorcontactrel:vendorid' => 'V',
		'vtiger_vendorcontactrel:contactid' => 'V',
		'vtiger_pricebook:currency_id' => 'V',
	);

	//If the Fields details does not match with the array, then we return the same typeofdata
	if (isset($new_field_details[$field])) {
		$type_of_data = $new_field_details[$field];
	}
	return $type_of_data;
}

/** Returns the URL for Basic and Advance Search
 */
function getBasic_Advance_SearchURL() {
	$url = '';
	if (!isset($_REQUEST['searchtype'])) {
		return $url;
	}
	$url .= (isset($_REQUEST['query'])) ? '&query=' . urlencode(vtlib_purify($_REQUEST['query'])) : '';
	$url .= (isset($_REQUEST['searchtype'])) ? '&searchtype=' . urlencode(vtlib_purify($_REQUEST['searchtype'])) : '';
	if ($_REQUEST['searchtype'] == 'BasicSearch') {
		$url .= (isset($_REQUEST['search_field'])) ? '&search_field=' . urlencode(vtlib_purify($_REQUEST['search_field'])) : '';
		$url .= (isset($_REQUEST['search_text'])) ? '&search_text=' . to_html(vtlib_purify($_REQUEST['search_text'])) : '';
		$url .= (isset($_REQUEST['type'])) ? '&type=' . vtlib_purify($_REQUEST['type']) : '';
	} elseif ($_REQUEST['searchtype'] == 'advance') {
		$count = empty($_REQUEST['search_cnt']) ? 0 : vtlib_purify($_REQUEST['search_cnt']);
		for ($i = 0; $i < $count; $i++) {
			$url .= (isset($_REQUEST['Fields' . $i])) ? '&Fields' . $i . '=' . stripslashes(str_replace("'", "", vtlib_purify($_REQUEST['Fields' . $i]))) : '';
			$url .= (isset($_REQUEST['Condition' . $i])) ? '&Condition' . $i . '=' . vtlib_purify($_REQUEST['Condition' . $i]) : '';
			$url .= (isset($_REQUEST['Srch_value' . $i])) ? '&Srch_value' . $i . '=' . to_html(vtlib_purify($_REQUEST['Srch_value' . $i])) : '';
		}
		$url .= (isset($_REQUEST['search_cnt'])) ? '&search_cnt=' . urlencode(vtlib_purify($_REQUEST['search_cnt'])) : '';
		$url .= (isset($_REQUEST['matchtype'])) ? '&matchtype=' . vtlib_purify($_REQUEST['matchtype']) : '';
	}
	return $url;
}

/** Function To create Email template variables dynamically */
function getEmailTemplateVariables($modules_list = null) {
	global $adb;

	if (is_null($modules_list)) {
		$modules_list = array('Accounts', 'Contacts', 'Leads', 'Users', 'HelpDesk');
	}

	foreach ($modules_list as $module) {
		$allFields = array();
		if ($module == 'Calendar') {
			$focus = new Activity();
		} else {
			$focus = CRMEntity::getInstance($module);
		}
		$tabid = getTabid($module);
		//many to many relation information field campaignrelstatus(this is the column name of the field) has block set to '0', which should be ignored.
		$result = $adb->pquery(
			'select fieldlabel,columnname from vtiger_field where tabid=? and vtiger_field.presence in (0,2) and displaytype in (1,2,3,4) and block!=0',
			array($tabid)
		);
		$norows = $adb->num_rows($result);
		if ($norows > 0) {
			$i18nModule = getTranslatedString($module, $module);
			$allFields[] = array($i18nModule . ': ' . $i18nModule . 'ID', '$' . strtolower($module) . '-' . $focus->table_index . '$');
			for ($i = 0; $i < $norows; $i++) {
				$allFields[] = array(
					$i18nModule . ': ' . getTranslatedString($adb->query_result($result, $i, 'fieldlabel'), $module),
					'$' . strtolower($module) . '-' . $adb->query_result($result, $i, 'columnname') . '$',
				);
			}
		}
		$allOptions[] = $allFields;
	}
	$allFields = array();
	$allFields[] = array(getTranslatedString('Current Date'), '$custom-currentdate$');
	$allFields[] = array(getTranslatedString('Current Time'), '$custom-currenttime$');
	$allFields[] = array(getTranslatedString('Image Field'), '${module}-{imagefield}_fullpath$');
	$allOptions[] = $allFields;
	return $allOptions;
}

/** Function to get picklist values for the given field that are accessible for the given role.
 *  @ param $tablename picklist fieldname.
 *  It gets the picklist values for the given fieldname
 *  	$fldVal = Array(0=>value,1=>value1,-------------,n=>valuen)
 *  @return Array of picklist values accessible by the user.
 */
function getPickListValues($tablename, $roleid) {
	global $adb;
	$query = "select $tablename
		from vtiger_$tablename
		inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$tablename.picklist_valueid
		where roleid=? and picklistid in (select picklistid from vtiger_picklist) order by sortid";
	$result = $adb->pquery($query, array($roleid));
	$fldVal = array();
	while ($row = $adb->fetch_array($result)) {
		$fldVal[] = $row[$tablename];
	}
	return $fldVal;
}

/** Function to check the file access is made within web root directory and whether it is not from unsafe directories */
function checkFileAccessForInclusion($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if (empty($use_root_directory)) {
		$use_root_directory = realpath(__DIR__ . '/../../.');
	}

	$unsafeDirectories = array('storage', 'cache', 'test', 'build', 'logs', 'backup', 'packages', 'schema');

	$realfilepath = realpath($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath = str_replace('\\', '/', $rootdirpath);

	$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
	$filePathParts = explode('/', $relativeFilePath);

	if (stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
		global $default_charset;
		echo 'Sorry! Attempt to access restricted file.<br>';
		echo 'We are looking for this file path: '.htmlspecialchars($filepath, ENT_QUOTES, $default_charset).'<br>';
		echo 'We are looking here:<br> Real file path: '.htmlspecialchars($realfilepath, ENT_QUOTES, $default_charset).'<br>';
		echo 'Root dir path: '.htmlspecialchars($rootdirpath, ENT_QUOTES, $default_charset).'<br>';
		die();
	}
}

/** Function to check the file deletion within the deletable (safe) directories*/
function checkFileAccessForDeletion($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if (empty($use_root_directory)) {
		$use_root_directory = realpath(__DIR__ . '/../../.');
	}

	$safeDirectories = array('storage', 'cache', 'test');

	$realfilepath = realpath($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath = str_replace('\\', '/', $rootdirpath);

	$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
	$filePathParts = explode('/', $relativeFilePath);

	if (stripos($realfilepath, $rootdirpath) !== 0 || !in_array($filePathParts[0], $safeDirectories)) {
		global $default_charset;
		echo 'Sorry! Attempt to access restricted file.<br>';
		echo 'We are looking for this file path: '.htmlspecialchars($filepath, ENT_QUOTES, $default_charset).'<br>';
		echo 'We are looking here:<br> Real file path: '.htmlspecialchars($realfilepath, ENT_QUOTES, $default_charset).'<br>';
		echo 'Root dir path: '.htmlspecialchars($rootdirpath, ENT_QUOTES, $default_charset).'<br>';
		die();
	}
}

/** Function to check the file access is made within web root directory. */
function checkFileAccess($filepath) {
	if (!isInsideApplication($filepath)) {
		global $default_charset;
		echo "Sorry! Attempt to access restricted file.<br>";
		echo 'We are looking for this file path: '.htmlspecialchars($filepath, ENT_QUOTES, $default_charset).'<br>';
		die();
	}
}

/**
 * function to return whether the file access is made within vtiger root directory and it exists.
 * @global String $root_directory vtiger root directory as given in config.inc.php file.
 * @param String $filepath relative path to the file which need to be verified
 * @return Boolean true if file is a valid file within vtiger root directory, false otherwise.
 * @deprecated
 */
function isFileAccessible($filepath) {
	return isInsideApplication($filepath);
}

/** Function to get the ActivityType for the given entity id
 *  @param entityid : Type Integer
 *  return the activity type for the given id
 */
function getActivityType($id) {
	global $adb;
	$quer = 'select activitytype from vtiger_activity where activityid=?';
	$res = $adb->pquery($quer, array($id));
	return $adb->query_result($res, 0, 'activitytype');
}

/** Function to get owner name either user or group */
function getOwnerName($id) {
	global $log;
	$log->debug("Entering getOwnerName( $id ) method ...");
	$oname = $id;
	if (is_numeric($id) && $id>0) {
		$ownerList = getOwnerNameList(array($id));
		if (isset($ownerList[$id])) {
			$oname = $ownerList[$id];
		}
	}
	return $oname;
}

/** Function to get owner name either user or group */
function getOwnerNameList($idList) {
	if (!is_array($idList) || count($idList) == 0) {
		return array();
	}

	$nameList = array();
	$db = PearDatabase::getInstance();
	$displayValueArray = getEntityName('Users', $idList);
	if (!empty($displayValueArray)) {
		foreach ($displayValueArray as $key => $value) {
			$nameList[$key] = $value;
		}
	}
	$groupIdList = array_diff($idList, array_keys($nameList));
	if (count($groupIdList) > 0) {
		$sql = 'select groupname,groupid from vtiger_groups where groupid in (' . generateQuestionMarks($groupIdList) . ')';
		$result = $db->pquery($sql, $groupIdList);
		$it = new SqlResultIterator($db, $result);
		foreach ($it as $row) {
			$nameList[$row->groupid] = $row->groupname;
		}
	}
	return $nameList;
}

/**
 * This function is used to get the blockid of the settings block for a given label.
 * @param $label - settings label
 * @return string type value
 */
function getSettingsBlockId($label) {
	global $log, $adb;
	$log->debug("Entering getSettingsBlockId(" . $label . ") method ...");

	$blockid = '';
	$query = "select blockid from vtiger_settings_blocks where label = ?";
	$result = $adb->pquery($query, array($label));
	$noofrows = $adb->num_rows($result);
	if ($noofrows == 1) {
		$blockid = $adb->query_result($result, 0, "blockid");
	}
	$log->debug("Exiting getSettingsBlockId method ...");
	return $blockid;
}

// Function to check if the logged in user is admin
// and if the module is an entity module
// and the module has a Settings.php file within it
function isModuleSettingPermitted($module) {
	if (file_exists("modules/$module/Settings.php") && isPermitted('Settings', 'index', '') == 'yes') {
		return 'yes';
	}
	return 'no';
}

/**
 * this function returns the entity field name for a given module; for e.g. for Contacts module it return concat(lastname, ' ', firstname)
 * @param string $module - the module name
 * @return string $fieldsname - the entity field name for the module
 */
function getEntityField($module, $fqn = false) {
	global $adb;
	$data = array();
	if (!empty($module)) {
		$query = 'select fieldname,tablename,entityidfield from vtiger_entityname where modulename = ?';
		$result = $adb->pquery($query, array($module));
		$fieldsname = $adb->query_result($result, 0, 'fieldname');
		$tablename = $adb->query_result($result, 0, 'tablename');
		$entityidfield = $adb->query_result($result, 0, 'entityidfield');
		if (!(strpos($fieldsname, ',') === false)) {
			$fieldlists = explode(',', $fieldsname);
			if ($fqn) {
				array_walk($fieldlists, function (&$elem, $key) use ($tablename) {
					$elem = $tablename.'.'.$elem;
				});
			}
			$fieldsname = 'concat(' . implode(",' ',", $fieldlists) . ')';
		} elseif ($fqn) {
			$fieldsname = $tablename.'.'.$fieldsname;
		}
	} else {
		$tablename  = $fieldsname = $entityidfield = '';
	}
	$data = array('tablename' => $tablename, 'fieldname' => $fieldsname, 'entityid' => $entityidfield);
	return $data;
}

/**
 * this function returns the entity information for a given module; for e.g. for Contacts module
 * it returns the information of tablename, modulename, fieldsname and id gets from vtiger_entityname
 * @param string $module - the module name
 * @return array $data - the entity information for the module
 */
function getEntityFieldNames($module) {
	$adb = PearDatabase::getInstance();
	$data = array();
	if (!empty($module)) {
		$result = $adb->pquery('select fieldname,modulename,tablename,entityidfield from vtiger_entityname where modulename=?', array($module));
		$fieldsName = $adb->query_result($result, 0, 'fieldname');
		$tableName = $adb->query_result($result, 0, 'tablename');
		$entityIdField = $adb->query_result($result, 0, 'entityidfield');
		$moduleName = $adb->query_result($result, 0, 'modulename');
		if (!(strpos($fieldsName, ',') === false)) {
			$fieldsName = explode(',', $fieldsName);
		}
	} else {
		$fieldsName = '';
		$tableName = '';
		$entityIdField = '';
		$moduleName = '';
	}
	$data = array('tablename' => $tableName, 'modulename' => $moduleName, 'fieldname' => $fieldsName, 'entityidfield' => $entityIdField);
	return $data;
}

/**
 * this function returns the fieldsname and its values in a array for the given ids
 * @param1 array $entity_field_info - field information having modulename, tablename, fieldname, recordid
 * @param2 array $ids_list - record ids
 * @return array $entity_info - array of fieldname and its value with key as record id
 */
function getEntityFieldValues($entity_field_info, $ids_list) {
	global $adb;
	$tableName = $entity_field_info['tablename'];
	$fieldsName = $entity_field_info['fieldname'];
	//$moduleName = $entity_field_info['modulename'];
	$entityIdField = $entity_field_info['entityidfield'];
	if (is_array($fieldsName)) {
		$fieldsNameString = implode(',', $fieldsName);
	} else {
		$fieldsNameString = $fieldsName;
	}
	$params1 = (array)$ids_list;
	$query1 = "SELECT $fieldsNameString,$entityIdField FROM $tableName WHERE $entityIdField IN (" . generateQuestionMarks($params1) . ')';
	$result = $adb->pquery($query1, $params1);
	$numrows = $adb->num_rows($result);
	$entity_info = array();
	for ($i = 0; $i < $numrows; $i++) {
		if (is_array($fieldsName)) {
			for ($j = 0, $jMax = count($fieldsName); $j < $jMax; $j++) {
				$entity_id = $adb->query_result($result, $i, $entityIdField);
				$entity_info[$i][$entity_id][$fieldsName[$j]] = $adb->query_result($result, $i, $fieldsName[$j]);
			}
		} else {
			$entity_id = $adb->query_result($result, $i, $entityIdField);
			$entity_info[$i][$entity_id][$fieldsName] = $adb->query_result($result, $i, $fieldsName);
		}
	}
	return $entity_info;
}

/**
 * this function returns the entity field name for a given module; for e.g. for Contacts module it return concat(lastname, ' ', firstname)
 * @param string $module - name of the module
 * @param array $fieldsName - fieldname with respect to module (ex : 'Accounts' - 'accountname', 'Contacts' - 'lastname','firstname')
 * @param array $fieldValues - array of fieldname and its value
 * @return string $fieldConcatName - the entity field name for the module
 */
function getEntityFieldNameDisplay($module, $fieldsName, $fieldValues) {
	global $current_user;
	if (!is_array($fieldsName)) {
		return $fieldValues[$fieldsName];
	} else {
		$accessibleFieldNames = array();
		foreach ($fieldsName as $field) {
			if ($module == 'Users' || getColumnVisibilityPermission($current_user->id, $field, $module) == '0') {
				$accessibleFieldNames[] = $fieldValues[$field];
			}
		}
		if (count($accessibleFieldNames) > 0) {
			return implode(' ', $accessibleFieldNames);
		}
	}
	return '';
}

function vt_suppressHTMLTags($string) {
	return preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
}

function vt_hasRTE() {
	return GlobalVariable::getVariable('Application_Use_RTE', 1);
}

function getNameInDisplayFormat($input, $dispFormat = "lf") {
	if (empty($dispFormat)) {
		$dispFormat = "lf";
	}
	$dispFormat = strtolower($dispFormat);
	$formattedNameList = array();
	$strLength = strlen($dispFormat);
	for ($i = 0; $i < $strLength; $i++) {
		$pos = strpos($dispFormat, $dispFormat[$i]);
		if ($pos !== false) {
			$formattedNameList[$pos] = $input[$dispFormat[$i]];
		}
	}
	return $formattedNameList;
}

function concatNamesSql($string) {
	return 'CONCAT(' . $string . ')';
}

function joinName($input, $glue = ' ') {
	return implode($glue, $input);
}

function getSqlForNameInDisplayFormat($input, $module, $glue = ' ') {
	$entity_field_info = getEntityFieldNames($module);
	//$tableName = $entity_field_info['tablename'];
	$fieldsName = $entity_field_info['fieldname'];
	if (is_array($fieldsName)) {
		foreach ($fieldsName as $value) {
			$formattedNameList[] = $input[$value];
		}
		$formattedNameListString = implode(",'" . $glue . "',", $formattedNameList);
	} else {
		$formattedNameListString = $input[$fieldsName];
	}
	return concatNamesSql($formattedNameListString);
}

function getModuleSequenceNumber($module, $recordId) {
	global $adb;
	switch ($module) {
		case "Invoice":
			$res = $adb->pquery('SELECT invoice_no FROM vtiger_invoice WHERE invoiceid = ?', array($recordId));
			$moduleSeqNo = $adb->query_result($res, 0, 'invoice_no');
			break;
		case "PurchaseOrder":
			$res = $adb->pquery('SELECT purchaseorder_no FROM vtiger_purchaseorder WHERE purchaseorderid = ?', array($recordId));
			$moduleSeqNo = $adb->query_result($res, 0, 'purchaseorder_no');
			break;
		case "Quotes":
			$res = $adb->pquery('SELECT quote_no FROM vtiger_quotes WHERE quoteid = ?', array($recordId));
			$moduleSeqNo = $adb->query_result($res, 0, 'quote_no');
			break;
		case "SalesOrder":
			$res = $adb->pquery('SELECT salesorder_no FROM vtiger_salesorder WHERE salesorderid = ?', array($recordId));
			$moduleSeqNo = $adb->query_result($res, 0, 'salesorder_no');
			break;
	}
	return $moduleSeqNo;
}

/* tries to return info@your_domain email for return path
 * but it doesn't get it right because the only way to get a TLD is comparing against a list of existing ones
 * not used anywhere anymore due to RFC5321 section 4.4
 * @deprecated
 */
function getReturnPath($host, $from_email) {
	return '';
}

function picklistHasDependency($keyfldname, $modulename) {
	global $adb;
	$tabid = getTabid($modulename);
	$result = $adb->pquery(
		'SELECT tabid FROM vtiger_picklist_dependency WHERE tabid = ? AND (sourcefield = ? OR targetfield = ?) limit 1',
		array($tabid, $keyfldname, $keyfldname)
	);
	return ($adb->num_rows($result) > 0);
}

function fetch_logo($type) {
	$companyDetails = retrieveCompanyDetails();
	switch ($type) {
		case 1:
			$logoname = decode_html($companyDetails['companylogo']);
			break;
		case 2:
			$logoname = decode_html($companyDetails['applogo']);
			break;
		case 3:
			$logoname = decode_html($companyDetails['favicon']);
			break;
		default:
			$logoname = 'test/logo/app-logo.jpg';
	}
	return $logoname;
}

/* added to get mail info for portal user
 * type argument included when addin customizable tempalte for sending portal login details
 * @deprecated
 */
function getmail_contents_portalUser($request_array, $password, $type = '') {
	global $mod_strings ,$adb;

	//$subject = $mod_strings['Customer Portal Login Details'];

	// id is hardcoded: it is for support start notification in vtiger_notificationscheduler
	$query='SELECT subject,body FROM vtiger_emailtemplates WHERE templateid=10';

	$result = $adb->pquery($query, array());
	$body=$adb->query_result($result, 0, 'body');
	$contents=$body;
	$contents = str_replace('$contact_name$', $request_array['first_name']." ".$request_array['last_name'], $contents);
	$contents = str_replace('$login_name$', $request_array['username'], $contents);
	$contents = str_replace('$password$', $password, $contents);
	$contents = str_replace('$URL$', $request_array['portal_url'], $contents);
	$contents = str_replace('$support_team$', $mod_strings['Support Team'], $contents);
	$contents = str_replace('$logo$', '<img src="cid:logo" />', $contents);

	if ($type == 'LoginDetails') {
		$temp=$contents;
		$value['subject']=$adb->query_result($result, 0, 'subject');
		$value['body']=$temp;
		return $value;
	}

	return $contents;
}

/**
 * To get the modules allowed for global search this function returns all the
 * modules which supports global search as an array in the following structure
 * array($module_name1=>$object_name1,$module_name2=>$object_name2,$module_name3=>$object_name3,$module_name4=>$object_name4,-----)
 */
function getSearchModulesCommon($filter = array()) {
	global $adb;
	// Ignore disabled administrative modules
	$doNotSearchThese = array('Dashboard','Home','Calendar','Events','Rss','Reports','Portal','Users','ConfigEditor','Import','MailManager','Mobile','ModTracker',
		'PBXManager','VtigerBackup','WSAPP','cbupdater','CronTasks','RecycleBin','Tooltip','Webforms','Calendar4You','GlobalVariable','cbMap','evvtMenu','cbAuditTrail',
		'cbLoginHistory','cbtranslation','BusinessActions','cbCVManagement');
	$doNotSearchTheseTabids = array();
	foreach ($doNotSearchThese as $mname) {
		$tabid = getTabid($mname);
		if (!empty($tabid)) {
			$doNotSearchTheseTabids[] = $tabid;
		}
	}
	$sql = 'select distinct vtiger_field.tabid,name
		from vtiger_field
		inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid
		where vtiger_tab.tabid not in ('.generateQuestionMarks($doNotSearchTheseTabids).') and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
	$result = $adb->pquery($sql, array($doNotSearchTheseTabids));
	$return_arr = array();
	while ($module_result = $adb->fetch_array($result)) {
		$modulename = $module_result['name'];
		// Do we need to filter the module selection?
		if (!empty($filter) && is_array($filter) && !in_array($modulename, $filter)) {
			continue;
		}
		$return_arr[$modulename] = $modulename;
	}
	return $return_arr;
}
?>
