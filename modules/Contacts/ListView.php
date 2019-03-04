<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'modules/Vtiger/ListView.php';
require_once 'modules/Contacts/connectors/Oauth2.php';
global $current_user;

$oauth2 = new Google_Oauth2_Connector('Contacts');
$hasToken=false;
if ($oauth2->hasStoredToken()) {
	$hasToken=true;
}
$smarty->assign('hasToken', $hasToken);
$smarty->display('modules/Contacts/GoogleContacts.tpl');
?>
