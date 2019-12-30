<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $adb;
if (!empty($_REQUEST['parent_id'])) {
	$sepi = getSalesEntityType($_REQUEST['parent_id']);
	if ($sepi!='Accounts' && $sepi!='Contacts') {
		$pid = vtlib_purify($_REQUEST['parent_id']);
		$_REQUEST['parent_id'] = getRelatedAccountContact($pid);
		if ($_REQUEST['parent_id']==0) {
			if (GlobalVariable::getVariable('Application_B2B', '1')) {
				$pidmodule = 'Contacts';
			} else {
				$pidmodule = 'Accounts';
			}
			$_REQUEST['parent_id'] = getRelatedAccountContact($pid, $pidmodule);
		}
	}
}

require_once 'modules/Vtiger/EditView.php';

if (isset($_REQUEST['product_id'])) {
	$smarty->assign('PRODUCTID', vtlib_purify($_REQUEST['product_id']));
}

if (!empty($_REQUEST['record'])) {
	//Added to display the ticket comments information
	$smarty->assign('COMMENT_BLOCK', $focus->getCommentInformation(vtlib_purify($_REQUEST['record'])));
}

$smarty->display('salesEditView.tpl');
?>
