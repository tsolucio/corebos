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
global $mod_strings, $app_strings, $adb, $log, $theme;

$smarty = new vtigerCRM_Smarty;
$theme_path='themes/'.$theme.'/';
$image_path=$theme_path.'images/';

$tax_details = getAllTaxes();
$sh_tax_details = getAllTaxes('all', 'sh');
$getlist = false;
//To save the edited value
if (isset($_REQUEST['save_tax']) && $_REQUEST['save_tax'] == 'true') {
	$new_labels = $new_retentions = $new_percentages = array();
	for ($i=0; $i<count($tax_details); $i++) {
		$new_labels[$tax_details[$i]['taxid']] = vtlib_purify($_REQUEST[bin2hex($tax_details[$i]['taxlabel'])]);
		$retention = isset($_REQUEST[$tax_details[$i]['taxname'].'retention']) ? vtlib_purify($_REQUEST[$tax_details[$i]['taxname'].'retention']) : '';
		$retention = ($retention=='on' ? 1 : 0);
		$new_retentions[$tax_details[$i]['taxid']] = $retention;
		$new_percentages[$tax_details[$i]['taxid']] = vtlib_purify($_REQUEST[$tax_details[$i]['taxname']]);
	}
	updateTaxPercentages($new_percentages);
	updateTaxRetentions($new_retentions);
	echo updateTaxLabels($new_labels);
	$getlist = true;
} elseif (isset($_REQUEST['sh_save_tax']) && $_REQUEST['sh_save_tax'] == 'true') {
	for ($i=0; $i<count($sh_tax_details); $i++) {
		$new_labels[$sh_tax_details[$i]['taxid']] = vtlib_purify($_REQUEST[bin2hex($sh_tax_details[$i]['taxlabel'])]);
		$new_percentages[$sh_tax_details[$i]['taxid']] = vtlib_purify($_REQUEST[$sh_tax_details[$i]['taxname']]);
	}
	updateTaxPercentages($new_percentages, 'sh');
	echo updateTaxLabels($new_labels, 'sh');
	$getlist = true;
}

//To edit
if (isset($_REQUEST['edit_tax']) && $_REQUEST['edit_tax'] == 'true') {
	$smarty->assign("EDIT_MODE", 'true');
	$smarty->assign('SH_EDIT_MODE', 'false');
} elseif (isset($_REQUEST['sh_edit_tax']) && $_REQUEST['sh_edit_tax'] == 'true') {
	$smarty->assign('EDIT_MODE', 'false');
	$smarty->assign("SH_EDIT_MODE", 'true');
} else {
	$smarty->assign('EDIT_MODE', 'false');
	$smarty->assign('SH_EDIT_MODE', 'false');
}

//To add tax
if (isset($_REQUEST['add_tax_type']) && $_REQUEST['add_tax_type'] == 'true') {
	//Add the given tax name and value as a new tax type
	echo addTaxType(vtlib_purify($_REQUEST['addTaxLabel']), vtlib_purify($_REQUEST['addTaxValue']));
	$getlist = true;
} elseif (isset($_REQUEST['sh_add_tax_type']) && $_REQUEST['sh_add_tax_type'] == 'true') {
	echo addTaxType($_REQUEST['sh_addTaxLabel'], $_REQUEST['sh_addTaxValue'], 'sh');
	$getlist = true;
}

//To Disable ie., delete or enable
if (((isset($_REQUEST['disable']) && $_REQUEST['disable'] == 'true') || (isset($_REQUEST['enable']) && $_REQUEST['enable'] == 'true')) && !empty($_REQUEST['taxname'])) {
	if ($_REQUEST['disable'] == 'true') {
		changeDeleted(vtlib_purify($_REQUEST['taxname']), 1);
	} else {
		changeDeleted(vtlib_purify($_REQUEST['taxname']), 0);
	}
	$getlist = true;
} elseif (((isset($_REQUEST['sh_disable']) && $_REQUEST['sh_disable']=='true') || (isset($_REQUEST['sh_enable']) && $_REQUEST['sh_enable']=='true')) && !empty($_REQUEST['sh_taxname'])) {
	if ($_REQUEST['sh_disable'] == 'true') {
		changeDeleted(vtlib_purify($_REQUEST['sh_taxname']), 1, 'sh');
	} else {
		changeDeleted(vtlib_purify($_REQUEST['sh_taxname']), 0, 'sh');
	}
	$getlist = true;
}

//after done save or enable/disable or added new tax the list will be retrieved again from db
if ($getlist) {
	$tax_details = getAllTaxes();
	$sh_tax_details = getAllTaxes('all', 'sh');
}

$smarty->assign('TAX_COUNT', count($tax_details));
$smarty->assign('SH_TAX_COUNT', count($sh_tax_details));

if (count($tax_details) == 0) {
	$smarty->assign('TAX_COUNT', 0);
}
if (count($sh_tax_details) == 0) {
	$smarty->assign('SH_TAX_COUNT', 0);
}

$smarty->assign('TAX_VALUES', $tax_details);

$smarty->assign('SH_TAX_VALUES', $sh_tax_details);

$smarty->assign('MOD', return_module_language($current_language, 'Settings'));
$smarty->assign('THEME', $theme);
$smarty->assign('IMAGE_PATH', $image_path);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->display('Settings/TaxConfig.tpl');

/**	Function to update the list of Tax percentages for the passed tax types
 *	@param array $new_percentages - array of tax types and the values like [taxid]=new value ie., [1]=3.56, [2]=11.45
 *	@param string $sh - sh or empty, if sh passed then update will be done in shipping and handling related table
 *	@return void
 */
function updateTaxPercentages($new_percentages, $sh = '') {
	global $adb, $log;
	$log->debug('Entering into the function updateTaxPercentages');

	foreach ($new_percentages as $taxid => $new_val) {
		if ($new_val != '') {
			if ($sh != '' && $sh == 'sh') {
				$query = 'update vtiger_shippingtaxinfo set percentage=? where taxid=?';
			} else {
				$query = 'update vtiger_inventorytaxinfo set percentage =? where taxid=?';
			}
			$adb->pquery($query, array($new_val, $taxid));
		}
	}

	$log->debug('Exiting from the function updateTaxPercentages');
}

function updateTaxRetentions($retentions) {
	global $adb, $log;
	$log->debug('Entering into the function updateTaxRetentions');
	$query = 'update vtiger_inventorytaxinfo set retention=? where taxid=?';
	foreach ($retentions as $taxid => $new_val) {
		if ($new_val == 0 || $new_val == 1) {
			$adb->pquery($query, array($new_val, $taxid));
		}
	}
	$log->debug('Exiting from the function updateTaxRetentions');
}

/**	Function to update the list of Tax Labels for the taxes
 *	@param array $new_labels - array of tax types and the values like [taxid]=new label ie., [1]=aa, [2]=bb
 *	@param string $sh - sh or empty, if sh passed then update will be done in shipping and handling related table
 *	@return void
 */
function updateTaxLabels($new_labels, $sh = '') {
	global $adb, $log, $currentModule;
	$log->debug("Entering into the function updateTaxPercentages");

	$duplicateTaxLabels = 0;
	foreach ($new_labels as $taxid => $new_val) {
		if ($new_val != '') {
			//First we will check whether the tax is already available or not
			if ($sh != '' && $sh == 'sh') {
				$check_query = "select taxlabel from vtiger_shippingtaxinfo where taxlabel = ? and taxid != ?";
			} else {
				$check_query = "select taxlabel from vtiger_inventorytaxinfo where taxlabel = ? and taxid != ?";
			}
			$check_res = $adb->pquery($check_query, array($new_val, $taxid));

			if ($adb->num_rows($check_res) > 0) {
				$duplicateTaxLabels++;
				continue;
			}

			if ($sh != '' && $sh == 'sh') {
				$query = "update vtiger_shippingtaxinfo set taxlabel= ? where taxid=?";
			} else {
				$query = "update vtiger_inventorytaxinfo set taxlabel = ? where taxid=?";
			}
			$adb->pquery($query, array($new_val, $taxid));

			$event_data = array(
				'tax_type' => $sh == 'sh' ? 'sh' : 'tax',
				'tax_id' => $taxid,
				'new_label' => $new_val
			);
			cbEventHandler::do_action('corebos.changelabel.tax', $event_data);
		}
	}
	if ($duplicateTaxLabels > 0) {
		return "<font color='red'>".getTranslatedString('LBL_ERR_SOME_TAX_LABELS_ALREADY_EXISTS', $currentModule)."</font>";
	}

	$log->debug("Exiting from the function updateTaxPercentages");
}
/**	Function used to add the tax type which will do database alterations
 *	@param string $taxlabel - tax label name to be added
 *	@param string $taxvalue - tax value to be added
 *	@param string $sh - sh or empty , if sh passed then the tax will be added in shipping and handling related table
 *	@return void
 */
function addTaxType($taxlabel, $taxvalue, $sh = '', $retention = 0) {
	global $adb, $log, $currentModule;
	$log->debug("Entering into function addTaxType($taxlabel, $taxvalue, $sh)");

	//First we will check whether the tax is already available or not
	if ($sh != '' && $sh == 'sh') {
		$check_query = "select taxlabel from vtiger_shippingtaxinfo where taxlabel=?";
	} else {
		$check_query = "select taxlabel from vtiger_inventorytaxinfo where taxlabel=?";
	}
	$check_res = $adb->pquery($check_query, array($taxlabel));

	if ($adb->num_rows($check_res) > 0) {
		return "<font color='red'>".getTranslatedString('LBL_ERR_TAX_LABEL_ALREADY_EXISTS', $currentModule)."</font>";
	}

	//if the tax is not available then add this tax.
	//Add this tax as a column in related table
	if ($sh != '' && $sh == 'sh') {
		$taxid = $adb->getUniqueID("vtiger_shippingtaxinfo");
		$taxname = "shtax".$taxid;
		$query = "alter table vtiger_inventoryshippingrel add column $taxname decimal(7,3) default NULL";

		$event_data = array(
			'tax_type' => 'sh',
			'tax_id' => $taxid,
			'tax_label' => $taxlabel,
			'tax_value' => $taxvalue
		);
	} else {
		$taxid = $adb->getUniqueID("vtiger_inventorytaxinfo");
		$taxname = "tax".$taxid;
		$query = "alter table vtiger_inventoryproductrel add column $taxname decimal(7,3) default NULL";

		$modules = array(
			array(
				'name' => 'Invoice',
				'table' => 'vtiger_invoice',
				'id' => 'invoiceid'
			),
			array(
				'name' => 'SalesOrder',
				'table' => 'vtiger_salesorder',
				'id' => 'salesorderid'
			),
			array(
				'name' => 'Quotes',
				'table' => 'vtiger_quotes',
				'id' => 'quoteid'
			),
			array(
				'name' => 'PurchaseOrder',
				'table' => 'vtiger_purchaseorder',
				'id' => 'purchaseorderid'
			)
		);
		$event_data = array(
			'tax_type' => 'tax',
			'tax_id' => $taxid,
			'tax_label' => $taxlabel,
			'tax_value' => $taxvalue
		);
		$Vtiger_Utils_Log = false;
		include_once 'vtlib/Vtiger/Module.php';
		foreach ($modules as $mod) {
			$mod_ent = VTiger_Module::getInstance($mod['name']);
			$block_ent = VTiger_Block::getInstance('LBL_'.$mod['name'].'_FINANCIALINFO', $mod_ent);
			$field1 = new Vtiger_Field();
			$field1->name = "sum_$taxname";
			$field1->label= $taxlabel;
			$field1->column = "sum_$taxname";
			$field1->columntype = 'DECIMAL(25,6)';
			$field1->uitype = 7;
			$field1->typeofdata = 'NN~O';
			$field1->displaytype = 2;
			$field1->presence = 0;
			$block_ent->addField($field1);
		}
	}

	cbEventHandler::do_action('corebos.add.tax', $event_data);

	$res = $adb->pquery($query, array());

	//if the tax is added as a column then we should add this tax in the list of taxes
	if ($res) {
		if ($sh != '' && $sh == 'sh') {
			$query1 = "insert into vtiger_shippingtaxinfo (taxid,taxname,taxlabel,percentage,deleted) values(?,?,?,?,?)";
			$params1 = array($taxid, $taxname, $taxlabel, $taxvalue, 0);
		} else {
			$query1 = "insert into vtiger_inventorytaxinfo (taxid,taxname,taxlabel,percentage,retention,deleted) values(?,?,?,?,?,?)";
			$params1 = array($taxid, $taxname, $taxlabel, $taxvalue, $retention, 0);
		}
		$res1 = $adb->pquery($query1, $params1);
	}

	$log->debug("Exit from function addTaxType($taxlabel, $taxvalue)");
	if ($res1) {
		return '';
	} else {
		return getTranslatedString('LBL_ERR_ADDTAX', 'Settings');
	}
}

/**	Function used to Enable or Disable the tax type
 *	@param string $taxname - taxname to enable or disble
 *	@param int $deleted - 0 or 1 where 0 to enable and 1 to disable
 *	@param string $sh - sh or empty, if sh passed then the enable/disable will be done in shipping and handling tax table ie.,vtiger_shippingtaxinfo
 *		 else this enable/disable will be done in Product tax table ie., in vtiger_inventorytaxinfo
 *	@return void
 */
function changeDeleted($taxname, $deleted, $sh = '') {
	global $log, $adb;
	$log->debug("Entering into function changeDeleted($taxname, $deleted, $sh)");

	if ($sh == 'sh') {
		$adb->pquery("update vtiger_shippingtaxinfo set deleted=? where taxname=?", array($deleted, $taxname));
	} else {
		$adb->pquery("update vtiger_inventorytaxinfo set deleted=? where taxname=?", array($deleted, $taxname));
	}
	$event_data = array(
		'tax_type' => $sh == 'sh' ? 'sh' : 'tax',
		'tax_name' => $taxname,
		'status' => $deleted == 1 ? 'disabled' : 'enabled'
	);
	cbEventHandler::do_action('corebos.changestatus.tax', $event_data);
	$log->debug("Exit from function changeDeleted($taxname, $deleted, $sh)");
}
?>