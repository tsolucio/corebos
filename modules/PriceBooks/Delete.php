<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $currentModule;
$focus = CRMEntity::getInstance($currentModule);

$record = vtlib_purify($_REQUEST['record']);
$module = vtlib_purify($_REQUEST['module']);
$return_module = vtlib_purify($_REQUEST['return_module']);
$return_action = vtlib_purify($_REQUEST['return_action']);
$return_id = vtlib_purify($_REQUEST['return_id']);
$parenttab = getParentTab();

//Added to fix 4600
$url = getBasic_Advance_SearchURL();


//Added to delete the pricebook from Product related list
if($_REQUEST['record'] != '' && $_REQUEST['return_id'] != '' && $_REQUEST['module'] == 'PriceBooks' 
	&& ($_REQUEST['return_module'] == 'Products' || $_REQUEST['return_module'] == 'Services'))
{
	$pricebookid = $_REQUEST['record'];
	$productid = $_REQUEST['return_id'];
	$adb->pquery("delete from vtiger_pricebookproductrel where pricebookid=? and productid=?", array($pricebookid, $productid));
}

if($_REQUEST['module'] == $_REQUEST['return_module'])
	$focus->mark_deleted($_REQUEST['record']);

header("Location: index.php?module=".vtlib_purify($_REQUEST['return_module'])."&action=".vtlib_purify($_REQUEST['return_action'])."&record=".vtlib_purify($_REQUEST['return_id'])."&parenttab=$parenttab$url");
?>