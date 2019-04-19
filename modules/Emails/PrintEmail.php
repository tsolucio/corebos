<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
require_once 'data/Tracker.php';
require_once 'modules/Emails/Emails.php';
require_once 'include/utils/utils.php';
require_once 'include/utils/UserInfoUtil.php';

global $mod_strings, $app_strings, $theme, $default_charset;

$focus = new Emails();
$smarty = new vtigerCRM_Smarty();
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('LBL_CHARSET', $default_charset);
if (isset($_REQUEST['record']) && $_REQUEST['record'] !='' && empty($_REQUEST['mailbox'])) {
	$focus->id = $_REQUEST['record'];
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($_REQUEST['record'], 'Emails');
	$focus->name = isset($focus->column_fields['name']) ? $focus->column_fields['name'] : $focus->column_fields['subject'];
	if (isset($_REQUEST['print']) && $_REQUEST['print'] !='') {
		$query = 'select idlists,from_email,to_email,cc_email,bcc_email from vtiger_emaildetails where emailid =?';
		$result = $adb->pquery($query, array($focus->id));
		$smarty->assign('FROM_MAIL', $adb->query_result($result, 0, 'from_email'));
		$to_email = vt_suppressHTMLTags(implode(',', json_decode($adb->query_result($result, 0, 'to_email'), true)));
		$smarty->assign('TO_MAIL', $to_email);
		$cc_add = vt_suppressHTMLTags(implode(',', json_decode($adb->query_result($result, 0, 'cc_email'), true)));
		$smarty->assign('CC_MAIL', $cc_add);
		$bcc_add = vt_suppressHTMLTags(implode(',', json_decode($adb->query_result($result, 0, 'bcc_email'), true)));
		$smarty->assign('BCC_MAIL', $bcc_add);
		$smarty->assign('SUBJECT', $focus->column_fields['subject']);
		$smarty->assign('DESCRIPTION', $focus->column_fields['description']);
	}
	$smarty->display('PrintEmail.tpl');
}
?>
