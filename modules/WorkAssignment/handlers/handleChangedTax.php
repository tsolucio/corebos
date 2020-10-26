<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ChangeTaxHandler extends VTEventHandler {
	public function handleEvent($name, $data) {
		require_once 'vtlib/Vtiger/Module.php';

		switch ($name) {
			case 'corebos.changestatus.tax':
				$this->handleChangeTaxStatus($data);
				break;
			case 'corebos.changelabel.tax':
				$this->handleChangeTaxLabel($data);
				break;
		}
	}

	private function handleChangeTaxStatus($data) {
		$inv_mod_name = basename(dirname(__FILE__, 2)); // Needs PHP 7
		$module = VTiger_Module::getInstance($inv_mod_name);
		$new_fldpresence = $data['status'] == 'disabled' ? 1 : 0;

		$amntfield = Vtiger_Field::getInstance('sum_' . $data['tax_name'], $module);
		$percfield = Vtiger_Field::getInstance($data['tax_name'] . '_perc', $module);
		$amntfield->presence = $percfield->presence = $new_fldpresence;

		$amntfield->save(false, true);
		$percfield->save(false, true);
	}

	private function handleChangeTaxLabel($data) {
		$inv_mod_name = basename(dirname(__FILE__, 2)); // Needs PHP 7
		$module = VTiger_Module::getInstance($inv_mod_name);
		$labelprefix = $data['tax_type'] == 'tax' ? '' : 'SH ';
		$taxnameprefix = $data['tax_type'] == 'tax' ? 'tax' : 'shtax';
		$taxname = $taxnameprefix . $data['tax_id'];

		$amntfield = Vtiger_Field::getInstance('sum_' . $taxname, $module);
		$percfield = Vtiger_Field::getInstance($taxname . '_perc', $module);

		$amntfield->label = $labelprefix . $data['new_label'];
		$percfield->label = $labelprefix . $data['new_label'] . ' (%)';

		$amntfield->save(false, true);
		$percfield->save(false, true);
	}
}