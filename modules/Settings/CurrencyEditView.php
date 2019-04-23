<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'Smarty_setup.php';
global $mod_strings,$app_strings,$adb,$theme,$default_charset;

$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';
$smarty=new vtigerCRM_Smarty;

$currency = '';
if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
	$tempid = vtlib_purify($_REQUEST['record']);

	// Get all the currencies
	$result = $adb->pquery('select * from vtiger_currency_info where deleted=0', array());
	// Check if the current currency status has to be disabled for the
	$result1 = $adb->pquery('select * from vtiger_users where currency_id=?', array($tempid));
	$noofrows = $adb->num_rows($result1);
	if ($noofrows != 0) {
		$disable_currency = 'disabled';
	} else {
		$disable_currency = '';
	}

	$other_currencies_list = array();
	while ($currencyResult = $adb->fetch_array($result)) {
		if ($currencyResult['id'] == $tempid) {
			$smarty->assign('STATUS_DISABLE', $disable_currency);
			$smarty->assign('CURRENCY_NAME', $currencyResult['currency_name']);
			$smarty->assign('CURRENCY_CODE', $currencyResult['currency_code']);
			$smarty->assign('CURRENCY_SYMBOL', decode_html($currencyResult['currency_symbol']));
			$smarty->assign('CURRENCY_POSITION', $currencyResult['currency_position']);
			$smarty->assign('CONVERSION_RATE', $currencyResult['conversion_rate']);
			$smarty->assign('CURRENCY_STATUS', $currencyResult['currency_status']);
			$currency = $currencyResult['currency_name'];
			if ($currencyResult['currency_status'] == 'Active') {
				$smarty->assign('ACTSELECT', 'selected');
				$smarty->assign('INACTSELECT', '');
			} else {
				$smarty->assign('ACTSELECT', '');
				$smarty->assign('INACTSELECT', 'selected');
			}
		} elseif ($currencyResult['currency_status'] == 'Active') {
			$cur_id = $currencyResult['id'];
			$other_currencies_list[$cur_id] = $currencyResult['currency_name'];
		}
	}
	$smarty->assign('OTHER_CURRENCIES', $other_currencies_list);
	$smarty->assign('ID', $tempid);
} else {
	$smarty->assign('ID', '');
	$smarty->assign('INACTSELECT', '');
	$smarty->assign('ACTSELECT', '');
	$smarty->assign('STATUS_DISABLE', '');
	$smarty->assign('CURRENCY_NAME', '');
	$smarty->assign('CURRENCY_CODE', '');
	$smarty->assign('CURRENCY_SYMBOL', '');
	$smarty->assign('CURRENCY_POSITION', '');
	$smarty->assign('CONVERSION_RATE', 1);
	$smarty->assign('CURRENCY_STATUS', 'Active');
}

$currencies_query = $adb->pquery('SELECT currency_name from vtiger_currency_info WHERE deleted=0 order by currency_name', array());
for ($index = 0; $index<$adb->num_rows($currencies_query); $index++) {
	$currencies_listed[] = decode_html($adb->query_result($currencies_query, $index, 'currency_name'));
}

$currencies_query = $adb->pquery('SELECT currency_name,currency_code,currency_symbol from vtiger_currencies order by currency_name', array());
for ($index = 0; $index<$adb->num_rows($currencies_query); $index++) {
	$currencyname = decode_html($adb->query_result($currencies_query, $index, 'currency_name'));
	$currencycode = decode_html($adb->query_result($currencies_query, $index, 'currency_code'));
	$currencysymbol = decode_html($adb->query_result($currencies_query, $index, 'currency_symbol'));
	$currencies[$currencyname] = array($currencycode,$currencysymbol);
}

$currencies_not_listed = array();
foreach ($currencies as $key => $value) {
	if (!in_array($key, $currencies_listed) || $key==$currency) {
		$currencies_not_listed[$key] = $value;
	}
}

$smarty->assign('CURRENCIES_ARRAY', json_encode($currencies_not_listed));

$positions = array();
$positionsrs = $adb->pquery('SELECT currency_symbol_placement from vtiger_currency_symbol_placement WHERE presence=1 order by sortorderid', array());
while ($pos=$adb->fetch_array($positionsrs)) {
	$positions[$pos['currency_symbol_placement']] = $pos['currency_symbol_placement'];
}
$smarty->assign('symbolpositionvalues', $positions);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('APP', $app_strings);
$smarty->assign('THEME', $theme);
$smarty->assign('CURRENCIES', $currencies_not_listed);
$smarty->assign('PARENTTAB', getParentTab());
$smarty->assign('MASTER_CURRENCY', $currency_name);
$smarty->assign('IMAGE_PATH', $image_path);

if (isset($_REQUEST['detailview']) && $_REQUEST['detailview'] != '') {
	$smarty->display('CurrencyDetailView.tpl');
} else {
	$smarty->display('CurrencyEditView.tpl');
}
?>