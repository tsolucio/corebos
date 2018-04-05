<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Users/authTypes/LDAP.php';
require_once 'modules/Users/authTypes/adLDAP.php';

switch ($_REQUEST['command']) {
	case 'LdapSearchUser':
		echo uiLDAPQuerySearchUser($_REQUEST['user']);
		break;

	case 'LdapSelectUser':
		echo uiLDAPQueryGetUserValues($_REQUEST['user']);
		break;
}

function uiLDAPQuerySearchUser($user) {
	global $mod_strings;

	if (empty($user)) {
		return '';
	}

	$authType = GlobalVariable::getVariable('User_AuthenticationType', 'SQL');
	switch (strtoupper($authType)) {
		case 'LDAP':
			$userArray = ldapSearchUserAccountAndName($user);
			break;

		case 'AD':
			return 'Error=Active Directory Query not yet implemented!';
	}

	if (empty($userArray)) {
		return "Error=".$mod_strings["LBL_NO_LDAP_MATCHES"];
	}

	if (count($userArray) == 1) {
		$accounts = array_keys($userArray);
		return uiLDAPQueryGetUserValues($accounts[0]);
	}

	asort($userArray);

	foreach ($userArray as $account => $fullname) {
		$sOpt .= "\n$account\t$fullname";
	}
	return "Options=\t-----" . $sOpt;
}

function uiLDAPQueryGetUserValues($account) {
	if (empty($account)) {
		return '';
	}

	// LDAP attributes --> HTML <INPUT> field names
	$fields['ldap_account']     = 'user_name';
	$fields['ldap_forename']    = 'first_name';
	$fields['ldap_lastname']    = 'last_name';
	$fields['ldap_fullname']    = '@dummy@';
	$fields['ldap_email']       = 'email1';
	$fields['ldap_tel_work']    = 'phone_work';
	$fields['ldap_department']  = 'department';
	$fields['ldap_description'] = 'description';
	$fields['ldap_street']      = 'address_street';
	$fields['ldap_city']        = 'address_city';

	$authType = GlobalVariable::getVariable('User_AuthenticationType', 'SQL');
	switch (strtoupper($authType)) {
		case 'LDAP':
			$valueArray = ldapGetUserValues($account, array_keys($fields));
			break;

		case 'AD':
			return 'Error=Active Directory Ajax not yet implemented!';
	}

	if (empty($valueArray)) {
		return '';
	}

	// Some users only have a fullname but the forename and/or lastname is not stored on the server.
	// In this case write the full name into the lastname field. The admin has to correct this manually.
	// It is not possible to do this automatically because a user may have two forenames and one lastname or vice versa!
	if (($valueArray['ldap_forename'] == "" || $valueArray['ldap_lastname'] == "") && $valueArray['ldap_fullname'] != "") {
		$valueArray['ldap_lastname'] = $valueArray['ldap_fullname'];
	}

	foreach ($fields as $key => $input) {
		$value = $valueArray[$key];
		$sVal .= "\n$input\t$value";
	}

	// Here we could check the password setup
	// either a stock password set in Global Config
	// Or a standard password geneneration policy
	// These could all be Globals - currently in config.php.ldap

	$passwd = makePass($valueArray['ldap_fullname']);

	// LDAP does not require to store a password, but it is a mandatory field -> store dummy password into mySql base

	$sVal .= "\nuser_password\t$passwd";
	$sVal .= "\nconfirm_password\t$passwd";

	return 'Values=' . $sVal;
}
?>
