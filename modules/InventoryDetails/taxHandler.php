<?php
/*************************************************************************************************
 * Copyright 2018 MajorLabel -- This file is a part of MajorLabel coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. MajorLabel reserves all rights not expressly
* granted by the License. coreBOS distributed by MajorLabel S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
class addTaxHandler extends VTEventHandler {

	public function handleEvent($eventName, $eventData) {

		if ($eventData['tax_type'] == 'tax') {
			require_once 'vtlib/Vtiger/Module.php';

			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$block = Vtiger_Block::getInstance('InventoryDetailsTaxBlock', $mod);

			$field = new Vtiger_Field();
			$field->label = $eventData['tax_label'];
			$field->name = 'id_tax' . $eventData['tax_id'] . '_perc';
			$field->column = 'id_tax' . $eventData['tax_id'] . '_perc';
			$field->columntype = 'DECIMAL(7,3)';
			$field->uitype = 9;
			$field->typeofdata = 'N~O';
			$block->addField($field);
			global $adb;
			$adb->query('ALTER TABLE vtiger_inventorydetails CHANGE id_tax'.$eventData['tax_id'].'_perc id_tax'.$eventData['tax_id']."_perc DECIMAL(7,3) NOT NULL DEFAULT '0'");
		}
	}
}

class changeStatusTaxHandler extends VTEventHandler {

	public function handleEvent($eventName, $eventData) {

		if ($eventData['tax_type'] == 'tax') {
			global $adb;
			require_once 'vtlib/Vtiger/Module.php';

			$r = $adb->pquery('SELECT taxid FROM vtiger_inventorytaxinfo WHERE taxname = ?', array($eventData['tax_name']));
			$taxid = $adb->query_result($r, 0, 'taxid');
			$fieldname = 'id_tax' . $taxid . '_perc';

			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$field = Vtiger_Field::getInstance($fieldname, $mod);
			$field->presence = $eventData['status'] == 'disabled' ? 1 : 2;
			$field->save();
		}
	}
}

class changeLabelTaxHandler extends VTEventHandler {

	public function handleEvent($eventName, $eventData) {

		if ($eventData['tax_type'] == 'tax') {
			global $adb;
			require_once 'vtlib/Vtiger/Module.php';

			$r = $adb->pquery('SELECT taxid FROM vtiger_inventorytaxinfo WHERE taxlabel = ?', array($eventData['new_label']));
			$taxid = $adb->query_result($r, 0, 'taxid');
			$fieldname = 'id_tax' . $taxid . '_perc';

			$r = $adb->pquery('SELECT presence FROM vtiger_field WHERE fieldname = ?', array($fieldname));
			$presence = $adb->query_result($r, 0, 'presence');

			$mod = Vtiger_Module::getInstance('InventoryDetails');
			$field = Vtiger_Field::getInstance($fieldname, $mod);
			$field->label = $eventData['new_label'];
			$field->presence = $presence;
			$field->save();
		}
	}
}