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
	$focus->firstname = $focus->column_fields['firstname'];
	$focus->lastname = $focus->column_fields['lastname'];
} else {
	$focus->firstname = '';
	$focus->lastname = '';
}
$smarty->assign('NAME', $focus->lastname.' '.$focus->firstname);
$parent_email = getEmailParentsList('Leads', $focus->id, $focus);
$smarty->assign('HIDDEN_PARENTS_LIST', $parent_email);
$smarty->assign('EMAIL', $focus->column_fields['email']);
$smarty->assign('SECONDARY_EMAIL', $focus->column_fields['secondaryemail']);
?>
