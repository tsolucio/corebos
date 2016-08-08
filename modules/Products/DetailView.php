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
require_once('user_privileges/default_module_view.php');

global $mod_strings, $app_strings, $currentModule, $current_user, $theme, $log, $singlepane_view;

$smarty = new vtigerCRM_Smarty();

require_once 'modules/Vtiger/DetailView.php';

//Added to display the Tax informations
$tax_details = getTaxDetailsForProduct($focus->id);

for($i=0;$i<count($tax_details);$i++)
{
	$tax_details[$i]['percentage'] = getProductTaxPercentage($tax_details[$i]['taxname'],$focus->id);
}
$smarty->assign('TAX_DETAILS', $tax_details);

$price_details = getPriceDetailsForProduct($focus->id, $focus->unit_price, 'available_associated', $currentModule);
$smarty->assign('PRICE_DETAILS', $price_details);

$smarty->display('Inventory/InventoryDetailView.tpl');
?>