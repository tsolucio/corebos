<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class deleteInventoryDetailsHandler extends VTEventHandler {

	public function handleEvent($eventName, $data) {
		global $adb, $current_user;
		$moduleName = $data->getModuleName();

		$flag = GlobalVariable::getVariable('Inventory_Check_Invoiced_Lines', 0);

		if ($moduleName == 'InventoryDetails' && ($eventName == 'vtiger.entity.beforedelete' || $eventName == 'corebos.beforedelete.workflow') && $flag) {
			$recordId = $data->getId();
			$columnFields = $data->getData();
			if(!empty($columnFields['related_to'])){
				$relentity = VTEntityData::fromEntityId($adb, $columnFields['related_to']);
				if($relentity->getModuleName() == 'Invoice' && !empty($columnFields['rel_lineitem_id'])){
					$rel_invdet = $columnFields['rel_lineitem_id'];
					$sel_rel_rec_exists = 'SELECT vtiger_inventorydetails.inventorydetailsid FROM vtiger_inventorydetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_inventorydetails.inventorydetailsid WHERE deleted=0 AND vtiger_inventorydetails.lineitem_id=?';
					$rel_rec_exists = $adb->pquery($sel_rel_rec_exists, array($rel_invdet));
					if ($adb->num_rows($rel_rec_exists)>0) {
						$rel_id_focus = new InventoryDetails();
						$rel_id_focus->id = $adb->query_result($rel_rec_exists, 0, 0);
						$rel_id_focus->retrieve_entity_info($rel_id_focus->id, 'InventoryDetails');
						$rel_id_focus->mode = 'edit';
						$result_units = $rel_id_focus->column_fields['remaining_units'] + $columnFields['quantity'];
						if (($rel_id_focus->column_fields['quantity']>0 && $result_units<0) || ($rel_id_focus->column_fields['quantity']<0 && $result_units>0)) {
							$result_units = 0;
						}
						$rel_id_focus->column_fields['remaining_units'] = $result_units;
						$rel_id_focus->save('InventoryDetails');
					}
				}
			}
		}
	}
}
