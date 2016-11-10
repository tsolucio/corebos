<?php

global $log,$currentModule,$adb,$current_user;

$screen_values = json_decode($_REQUEST['structure'],true);
$products = array();

foreach ($screen_values as $sv_name => $sv) {
	if (strpos($sv_name, 'hdnProductId') !== false) {
		$i = substr($sv_name, -1);
		$qty_i = 'qty'.$i;
		$name_i = 'productName'.$i;
		
		$products[$i]['crmid'] = $sv;
		$products[$i]['qty'] = $screen_values[$qty_i];
		$products[$i]['name'] = $screen_values[$name_i];
	}
}

foreach ($products as $product) {
	$q = $adb->pquery("SELECT divisible FROM vtiger_products WHERE productid = ?", array($product['crmid']));
	if ($adb->query_result($q, 0, 'divisible') === '0') {
		$divisible = false;
	} else {
		$divisible = true;
	}
	if ( !$divisible && floatval($product['qty']) != intval($product['qty']) ) {
		$message = $product['name'].' '.getTranslatedString('DIVISIBLE_WARNING','Products');
		break;
	} else {
		$message = '%%%OK%%%';
	}
}

echo $message;