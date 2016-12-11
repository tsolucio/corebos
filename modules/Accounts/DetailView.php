<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log;

$smarty = new vtigerCRM_Smarty();

$smarty->assign('USE_ASTERISK', get_use_asterisk($current_user->id));
if(useInternalMailer() == 1)
	$smarty->assign('INT_MAILER','true');

if(isPermitted('Emails','EditView','') == 'yes') {
	$vtwsObject = VtigerWebserviceObject::fromName($adb, $currentModule);
	$vtwsCRMObjectMeta = new VtigerCRMObjectMeta($vtwsObject, $current_user);
	$emailFields = $vtwsCRMObjectMeta->getEmailFields();

	$smarty->assign('SENDMAILBUTTON','permitted');
	$emails=array();
	foreach($emailFields as $key => $value){
		$emails[]=$value;
	}
	$smarty->assign('EMAILS', $emails);
	$cond="LTrim('%s') !=''";
	$condition=array();
	foreach($emails as $key => $value){
		$condition[]=sprintf($cond,$value);
	}
	$condition_str=implode('||',$condition);
	$js="if(".$condition_str."){fnvshobj(this,'sendmail_cont');sendmail('".$currentModule."',".vtlib_purify($_REQUEST['record']).");}else{OpenCompose('','create');}";
	$smarty->assign('JS',$js);
}

if(isPermitted('Accounts','Merge','') == 'yes') {
	require("user_privileges/user_privileges_".$current_user->id.".php");
	require_once('include/utils/UserInfoUtil.php');
	$wordTemplateResult = fetchWordTemplateList('Accounts');
	$tempCount = $adb->num_rows($wordTemplateResult);
	$tempVal = $adb->fetch_array($wordTemplateResult);
	$optionString = array();
	for($templateCount=0;$templateCount<$tempCount;$templateCount++) {
		$optionString[$tempVal['templateid']]=$tempVal['filename'];
		$tempVal = $adb->fetch_array($wordTemplateResult);
	}
	if($is_admin)
		$smarty->assign('MERGEBUTTON','permitted');
	elseif($tempCount >0)
		$smarty->assign('MERGEBUTTON','permitted');
	$smarty->assign('TEMPLATECOUNT',$tempCount);
	$smarty->assign('WORDTEMPLATEOPTIONS',$app_strings['LBL_SELECT_TEMPLATE_TO_MAIL_MERGE']);
	$smarty->assign('TOPTIONS',$optionString);
}

require_once 'modules/Vtiger/DetailView.php';

$smarty->display('DetailView.tpl');
?>