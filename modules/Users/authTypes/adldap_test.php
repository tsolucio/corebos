<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once(dirname(FILE) . '/adLDAP.php');

try {
	$adldap = new adLDAP();
}

catch (adLDAPException $e) {
	echo $e;
	exit();
}
$authUser = $adldap->authenticate('user-to-authenticate', 'users-password');
if ($authUser == true) {
	echo "User authenticated successfully";
}
else {
	// getLastError is not needed, but may be helpful for finding out why:
	echo "\n";
	echo $adldap->getLastError();
	echo "\n";
	echo "User authentication unsuccessful";
}

echo "\n";
$result=$adldap->user()->infoCollection('ldap', array("*"));
echo "User:\n";
echo $result->displayName;
echo "Mail:\n";
echo $result->mail;
?>