<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class NewTaxHandler extends VTEventHandler {
	public function handleEvent($name, $data) {
		require_once 'vtlib/Vtiger/Module.php';

		$inv_mod_name = basename(dirname(__FILE__, 2)); // Needs PHP 7
		$blocklabel = $data['tax_type'] == 'tax' ? 'LBL_BLOCK_TAXES' : 'LBL_BLOCK_SH_TAXES';
		$taxname_prefix = $data['tax_type'] == 'tax' ? 'tax' : 'shtax';
		$labelprefix = $data['tax_type'] == 'tax' ? '' : 'SH ';

		$module = VTiger_Module::getInstance($inv_mod_name);
		$block = Vtiger_Block::getInstance($blocklabel, $module);

		$field = new Vtiger_Field();
		$field->name = 'sum_' . $taxname_prefix . $data['tax_id'];
		$field->label= $labelprefix . $data['tax_label'];
		$field->column = 'sum_' . $taxname_prefix . $data['tax_id'];
		$field->columntype = 'DECIMAL(25,6)';
		$field->uitype = 7;
		$field->typeofdata = 'NN~O';
		$field->displaytype = 2;
		$field->presence = 0;
		$block->addField($field);

		$field = new Vtiger_Field();
		$field->name = $taxname_prefix . $data['tax_id'] . '_perc';
		$field->label= $labelprefix . $data['tax_label'] . ' (%)';
		$field->column = $taxname_prefix . $data['tax_id'] . '_perc';
		$field->columntype = 'DECIMAL(7,3)';
		$field->uitype = 7;
		$field->typeofdata = 'N~O';
		$field->displaytype = 2;
		$field->presence = 0;
		$block->addField($field);
	}
}