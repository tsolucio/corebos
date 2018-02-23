<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$contactid = $accountid = '';
//adding support for uitype 10
if (!empty($_REQUEST['contact_id'])) {
	$contactid = vtlib_purify($_REQUEST['contact_id']);
	$_REQUEST['related_to'] = $contactid;
} elseif (!empty($_REQUEST['account_id'])) {
	$accountid = vtlib_purify($_REQUEST['account_id']);
	$_REQUEST['related_to'] = $accountid;
}

require_once 'modules/Vtiger/EditView.php';

//needed when creating a new opportunity with a default account/contact value passed in
if (isset($_REQUEST['accountname']) && is_null($focus->accountname)) {
	$focus->accountname = vtlib_purify($_REQUEST['accountname']);
}
$smarty->assign('CONTACT_ID', $contactid);
$smarty->assign('ACCOUNT_ID', $accountid);

$smarty->display('salesEditView.tpl');
?>