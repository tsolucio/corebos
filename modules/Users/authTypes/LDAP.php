<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/Users/authTypes/config.ldap.php';

function ldapConnectServer() {
	global $AUTH_LDAP_CFG;

	$conn = @ldap_connect($AUTH_LDAP_CFG['ldap_host'], $AUTH_LDAP_CFG['ldap_port']);
	@ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3); // Try version 3.  Will fail and default to v2.

	if (!empty($AUTH_LDAP_CFG['ldap_username'])) {
		if (!@ldap_bind($conn, $AUTH_LDAP_CFG['ldap_username'], $AUTH_LDAP_CFG['ldap_pass'])) {
			return null;
		}
	} else {
		if (!@ldap_bind($conn)) { //attempt an anonymous bind if no user/pass specified in config.php
			return null;
		}
	}
	return $conn;
}

/**
 * Function to authenticate users via LDAP
 *
 * @param string $authUser -  Username to authenticate
 * @param string $authPW - Cleartext password
 * @return NULL on failure, user's info (in an array) on bind
 */
function ldapAuthenticate($authUser, $authPW) {
	global $AUTH_LDAP_CFG;

	if (empty($authUser) || empty($authPW)) {
		return false;
	}

	$conn = ldapConnectServer();
	if ($conn == null) {
		return false;
	}

	$retval = false;
	$filter = $AUTH_LDAP_CFG['ldap_account'] . '=' . $authUser;
	$ident  = @ldap_search($conn, $AUTH_LDAP_CFG['ldap_basedn'], $filter);
	if ($ident) {
		$result = @ldap_get_entries($conn, $ident);
		if ($result[0]) {
			// dn is the LDAP path where the user was fond. This attribute is always returned.
			if (@ldap_bind($conn, $result[0]["dn"], $authPW)) {
				$retval = true;
			}
		}
		ldap_free_result($ident);
	}

	ldap_unbind($conn);
	return $retval;
}

// Search a user by the given filter and returns the attributes defined in the array $required
function ldapSearchUser($filter, $required) {
	global $AUTH_LDAP_CFG;

	$conn = ldapConnectServer();
	if ($conn == null) {
		return null;
	}

	$ident = @ldap_search($conn, $AUTH_LDAP_CFG['ldap_basedn'], $filter, $required);
	if ($ident) {
		$result = ldap_get_entries($conn, $ident);
		ldap_free_result($ident);
	}
	ldap_unbind($conn);

	return $result;
}

// Searches for a user's fullname
// returns a hashtable with Account => FullName of all matching users
function ldapSearchUserAccountAndName($user) {
	global $AUTH_LDAP_CFG;

	$fldaccount = strtolower($AUTH_LDAP_CFG['ldap_account']);
	$fldname    = strtolower($AUTH_LDAP_CFG['ldap_fullname']);
	$fldclass   = strtolower($AUTH_LDAP_CFG['ldap_objclass']);

	$usrfilter  = explode("|", $AUTH_LDAP_CFG['ldap_userfilter']);
	$user = preg_replace('/[^A-Za-z0-9 ]/', '', $user); // for security
	$required   = array($fldaccount,$fldname,$fldclass);
	$ldapArray  = ldapSearchUser("$fldname=*$user*", $required);

	// copy from LDAP specific array to a standardized hashtable
	// Skip Groups and Organizational Units. Copy only users.
	for ($i=0; $i<$ldapArray["count"]; $i++) {
		$isuser = false;
		foreach ($usrfilter as $filt) {
			if (in_array($filt, $ldapArray[$i][$fldclass])) {
				$isuser = true;
				break;
			}
		}
		if ($isuser) {
			$account = $ldapArray[$i][$fldaccount][0];
			$name    = $ldapArray[$i][$fldname]   [0];
			$userArray[$account] = $name;
		}
	}
	return $userArray;
}

// retrieve all requested LDAP values for the given user account
// $fields = array("ldap_forename", "ldap_email",...)
// returns a hashtable with "ldap_forename" => "John"
function ldapGetUserValues($account, $fields) {
	global $AUTH_LDAP_CFG;

	foreach ($fields as $key) {
		$required[] = $AUTH_LDAP_CFG[$key];
	}

	$filter = $AUTH_LDAP_CFG['ldap_account'] . "=" .$account;
	$ldapArray = ldapSearchUser($filter, $required);

	// copy from LDAP specific array to a standardized hashtable
	foreach ($fields as $key) {
		$attr  = strtolower($AUTH_LDAP_CFG[$key]);
		$value = $ldapArray[0][$attr][0];
		$valueArray[$key] = $value;
	}
	return $valueArray;
}

function makePass($name) {
	global $AUTH_LDAP_CFG;
//  If we had first/lastnames
//	$first = 'Joe';
//	$last = 'Blogs';
//	$name = $first . $last;

	if ($AUTH_LDAP_CFG['pass_password']  != 'generated') {
		return $AUTH_LDAP_CFG['pass_userpass'];
	} else {
		$pass = '';
		$number = strtolower($AUTH_LDAP_CFG['pass_usernumber']);
		$char   = strtolower($AUTH_LDAP_CFG['pass_userchar']);

		$name = strtolower($name);
		$name = preg_replace('/\s+/', '', $name); //remove whitespace
		$name = ucfirst($name); // Lowercase
		$name = substr($name, 0, 4); // First 4 letters

		$nameArr = str_split($name); // Split Letters to array
		$digitArr = str_split($number); // Split Numbers to array

		for ($i=0; $i< count($nameArr); $i++) {
			$pass = $pass . $nameArr[$i] . $digitArr[$i];
		}

		$pass = $pass . $char; // Add character
	}
	return $pass;
}
?>
