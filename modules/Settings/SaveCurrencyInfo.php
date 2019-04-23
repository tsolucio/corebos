<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
global $adb;
$currency_name = vtlib_purify($_REQUEST['currency_name']);
$currency_code= vtlib_purify($_REQUEST['currency_code']);
$currency_symbol= vtlib_purify($_REQUEST['currency_symbol']);
$conversion_rate= vtlib_purify($_REQUEST['conversion_rate']);
$currency_position= vtlib_purify($_REQUEST['currency_position']);
if (isset($_REQUEST['currency_status']) && $_REQUEST['currency_status'] != '') {
	$currency_status= vtlib_purify($_REQUEST['currency_status']);
} else {
	$currency_status= 'Active';
}
if (isset($_REQUEST['record']) && $_REQUEST['record'] !='') {
	$cur_status_res = $adb->pquery('select currency_status from vtiger_currency_info where id=?', array(vtlib_purify($_REQUEST['record'])));
	$old_cur_status = $adb->query_result($cur_status_res, 0, 'currency_status');

	if ($currency_status != $old_cur_status && $currency_status == 'Inactive') {
		$transfer_cur_id = vtlib_purify($_REQUEST['transfer_currency_id']);
		if ($transfer_cur_id != null) {
			transferCurrency(vtlib_purify($_REQUEST['record']), $transfer_cur_id);
		}
	}

	$sql = 'update vtiger_currency_info set currency_name =?, currency_code =?, currency_symbol =?, conversion_rate =?,currency_status=?,currency_position=? where id =?';
	$params = array($currency_name, $currency_code, $currency_symbol, $conversion_rate, $currency_status, $currency_position, vtlib_purify($_REQUEST['record']));
} else {
	$sql = 'insert into vtiger_currency_info values(?,?,?,?,?,?,?,?,?)';
	$newseq = $adb->getUniqueID('vtiger_currency_info');
	$params = array($newseq, $currency_name, $currency_code, $currency_symbol, $conversion_rate, $currency_status,'0','0', $currency_position);
}
$adb->pquery($sql, $params);
header('Location: index.php?module=Settings&action=CurrencyListView');
?>