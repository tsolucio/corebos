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

if(isPermitted('Invoice','CreateView',$record) == 'yes')
	$smarty->assign('CONVERTINVOICE','permitted');
$smarty->assign('CONVERTMODE','sotoinvoice');
//Get the associated Products and then display above Terms and Conditions
$smarty->assign('ASSOCIATED_PRODUCTS',getDetailAssociatedProducts($currentModule,$focus));
$smarty->assign('CREATEPDF','permitted');
$salesorder_no = getModuleSequenceNumber($currentModule,$record);
$smarty->assign('SO_NO',$salesorder_no);

$smarty->display('Inventory/InventoryDetailView.tpl');
?>