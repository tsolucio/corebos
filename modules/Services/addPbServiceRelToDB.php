<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require('user_privileges/default_module_view.php');
global $adb, $log;
$idlist = vtlib_purify($_POST['idlist']);
$returnmodule = urlencode(vtlib_purify($_REQUEST['return_module']));
$returnaction = urlencode(vtlib_purify($_REQUEST['return_action']));
$pricebook_id = vtlib_purify($_REQUEST['pricebook_id']);
$productid = vtlib_purify($_REQUEST['product_id']);
if(isset($_REQUEST['pricebook_id']) && $_REQUEST['pricebook_id']!='')
{
	$currency_id = getPriceBookCurrency($pricebook_id);
	$storearray = explode(";",$idlist);
	foreach($storearray as $id)
	{
		if($id != '') {
			$lp_name = $id.'_listprice';
			$list_price = $_REQUEST[$lp_name];
			// Updating the pricebook product rel table
			$log->info('Services :: Inserting services to price book');
			$query= "insert into vtiger_pricebookproductrel (pricebookid,productid,listprice,usedcurrency) values(?,?,?,?)";
			$adb->pquery($query, array($pricebook_id,$id,$list_price,$currency_id));
		}
	}
	if($singlepane_view == 'true')
		header('Location: index.php?module=PriceBooks&action=DetailView&record=' . urlencode($pricebook_id));
	else
		header('Location: index.php?module=PriceBooks&action=CallRelatedList&record=' . urlencode($pricebook_id));
}
elseif(isset($_REQUEST['product_id']) && $_REQUEST['product_id']!='')
{
	$storearray = explode(";",$idlist);
	foreach($storearray as $id)
	{
		if($id != '') {
			$currency_id = getPriceBookCurrency($id);
			$lp_name = $id.'_listprice';
			$list_price = $_REQUEST[$lp_name];
			// Updating the pricebook product rel table
			$log->info('Services :: Inserting PriceBooks to Service');
			$query= "insert into vtiger_pricebookproductrel (pricebookid,productid,listprice,usedcurrency) values(?,?,?,?)";
			$adb->pquery($query, array($id,$productid,$list_price,$currency_id));
		}
	}
	if($singlepane_view == 'true')
		header("Location: index.php?module=$returnmodule&action=DetailView&record=" . urlencode($productid));
	else
		header("Location: index.php?module=$returnmodule&action=CallRelatedList&record=" . urlencode($productid));
}

?>
