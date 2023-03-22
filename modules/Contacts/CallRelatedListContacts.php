<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if ($record != '') {
	$focus->name = $focus->column_fields['firstname'].' '.$focus->column_fields['lastname'];
} else {
	$focus->name = '';
}
$sql = $adb->pquery('select accountid from vtiger_contactdetails where contactid=?', array($focus->id));
$accountid = $adb->query_result($sql, 0, 'accountid');
if ($accountid == 0) {
	$accountid='';
}
$smarty->assign('accountid', $accountid);
$smarty->assign('NAME', $focus->name);
$parent_email = getEmailParentsList('Contacts', $record, $focus);
$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
$smarty->assign('EMAIL', $focus->column_fields['email']);
$smarty->assign('SECONDARY_EMAIL', $focus->column_fields['secondaryemail']);
?>
