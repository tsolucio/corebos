<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

// ----------- Configuration LDAP -------------
$AUTH_LDAP_CFG['ldap_host']     = 'localhost';	//system where ldap is running (e.g. ldap://localhost)
$AUTH_LDAP_CFG['ldap_port']     = '389';		//port of the ldap service

// The LDAP branch which stores the User Information
// This branch may have subfolders. PHP will search in all subfolders.
$AUTH_LDAP_CFG['ldap_basedn']   = 'ou=People,dc=localhost,dc=localdomain';

// The account on the LDAP server which has permissions to read the branch specified in ldap_basedn
$AUTH_LDAP_CFG['ldap_username'] = 'cn=admin,dc=localhost,dc=localdomain';   // set = NULL if not required
$AUTH_LDAP_CFG['ldap_pass']     = 'admin'; // set = NULL if not required

// Predefined LDAP fields (these settings work on Win 2003 Domain Controller)
$AUTH_LDAP_CFG['ldap_objclass']    = 'objectClass';
$AUTH_LDAP_CFG['ldap_account']     = 'cn';
$AUTH_LDAP_CFG['ldap_forename']    = 'givenName';
$AUTH_LDAP_CFG['ldap_lastname']    = 'sn';
$AUTH_LDAP_CFG['ldap_fullname']    = 'cn'; // or "name" or "displayName"
$AUTH_LDAP_CFG['ldap_email']       = 'mail';
$AUTH_LDAP_CFG['ldap_tel_work']    = 'telephoneNumber';
$AUTH_LDAP_CFG['ldap_department']  = 'physicalDeliveryOfficeName';
$AUTH_LDAP_CFG['ldap_description'] = 'description';
$AUTH_LDAP_CFG['ldap_street']      = 'street';
$AUTH_LDAP_CFG['ldap_city']        = 'l';

// Required to search users: the array defined in ldap_objclass must contain at least one of the following values
$AUTH_LDAP_CFG['ldap_userfilter']  = 'user|person|organizationalPerson|account';

// Default user pass
$AUTH_LDAP_CFG['pass_userpass']    = 'vtiger';

// Configurable to generate more secure password
// Take Full Name, remove space, lowercase, Capitalise first letter, take first four letters
// intersperse numbers, add char e.g. JoeBlogs = J0o0e0b0#
$AUTH_LDAP_CFG['pass_usernumber']  = '0000';
$AUTH_LDAP_CFG['pass_userchar']    = '#';
$AUTH_LDAP_CFG['pass_password']    = 'generated'; // NULL defaults to ldap_userpassword

// ------------ Configuration AD (Active Directory) --------------

$AUTH_LDAP_CFG['ad_accountSuffix'] = '@localhost.localdomain';
$AUTH_LDAP_CFG['ad_basedn']        = 'DC=localhost,DC=localdomain';
$AUTH_LDAP_CFG['ad_dc']            = array('dc.localhost.localdomain'); //array of domain controllers
$AUTH_LDAP_CFG['ad_username']      = null; //optional user/pass for searching
$AUTH_LDAP_CFG['ad_pass']          = null;
$AUTH_LDAP_CFG['ad_realgroup']     = true; //AD does not return the primary group.
		// Setting this to false will fudge "Domain Users" and is much faster.
		// True will resolve the real primary group, but may be resource intensive.

// #########################################################################
?>
