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
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/UserInfoUtil.php';
global $mod_strings, $adb, $theme, $app_strings, $default_charset;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty=new vtigerCRM_Smarty;
$result = $adb->pquery('select * from vtiger_currency_info where deleted=0', array());
$temprow = $adb->fetch_array($result);
$cnt=1;
$currency = array();
do {
	$currency_element = array();
	$currency_element['id'] = $temprow['id'];
	$currency_element['name'] = $temprow['currency_name'];
	$currency_element['code'] = $temprow['currency_code'];
	$currency_element['symbol'] = $temprow['currency_symbol'];
	$currency_element['position'] = $temprow['currency_position'];
	$currency_element['crate'] = $temprow['conversion_rate'];
	$currency_element['status'] = $temprow['currency_status'];
	if ($temprow["defaultid"] != '-11') {
		$currency_element['name']  = '<a href=index.php?module=Settings&action=CurrencyEditView&record='.$temprow['id'].'&detailview=detail_view>';
		$currency_element['name'] .= getTranslatedCurrencyString($temprow['currency_name']).'</a>';
		$currency_element['tool']  = '<a href=index.php?module=Settings&action=CurrencyEditView&record='.$temprow['id']
			.'><span class="slds-icon_container slds-icon_container_circle slds-icon-action-edit" title="'.getTranslatedString('LBL_EDIT_BUTTON')
			.'"><svg class="slds-icon slds-icon_xx-small" aria-hidden="true"><use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#edit"></use></svg>'
			.'<span class="slds-assistive-text">'.getTranslatedString('LBL_EDIT_BUTTON').'</span></span></a>';
		$currency_element['tool'] .= '&nbsp;&nbsp;<a style="cursor:pointer;" onClick="fnvshobj(this,\'currencydiv\');';
		$currency_element['tool'] .= 'deleteCurrency(\''.$temprow['id'].'\');"<span class="slds-icon_container slds-icon_container_circle slds-icon-action-delete"'
			.' title="'.getTranslatedString('LBL_DELETE').'"><svg class="slds-icon  slds-icon_xx-small" aria-hidden="true">'
			.'<use xlink:href="include/LD/assets/icons/action-sprite/svg/symbols.svg#delete"></use></svg><span class="slds-assistive-text">'
			.getTranslatedString('LBL_DELETE').'</span></span></a>';
	} else {
		$currency_element['tool']= '';
	}
	$currency[] = $currency_element;
	$cnt++;
} while ($temprow = $adb->fetch_array($result));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
$smarty->assign('SINGLE_MOD', getTranslatedString('SINGLE_'.$currentModule));
$smarty->assign('CURRENCY_LIST', $currency);
$smarty->assign('CRON_TASK', Vtiger_Cron::getInstance('UpdateExchangeRate'));
if (!empty($_REQUEST['ajax'])) {
	$smarty->display('CurrencyListViewEntries.tpl');
} else {
	$smarty->display('CurrencyListView.tpl');
}
?>
