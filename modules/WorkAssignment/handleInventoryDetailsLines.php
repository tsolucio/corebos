<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class handleInventoryDetailsLines extends VTEventHandler {
	public function handleEvent($eventName, $entityData) {
		// We don't check the modulename since this
		// event is limited to WorkAssignment already.
		self::saveAggregation($entityData);

		require_once 'modules/InventoryDetails/InventoryDetails.php';
		$idfocus = new InventoryDetails();
		foreach ($_REQUEST['idlines'] as $lineseq => $line) {
			if ((int)$line['crmid'] == 0) {
				self::saveNewIDLine($entityData, $line, $idfocus, $lineseq);
			} else {
				self::saveExistingIDLine($entityData, $line, $idfocus, $lineseq);
			}
		}
		foreach ($_REQUEST['deletelines'] as $linecrmid => $crmid) {
			$idfocus->trash('InventoryDetails', $linecrmid);
		}
	}

	private static function saveNewIDLine($entityData, $line, $idfocus, $lineseq) {
		$idfocus->mode = 'create';
		$acc_con_fldnames = self::getAccConFieldNames();
		foreach ($line as $fldname => $fldval) {
			$idfocus->column_fields[$fldname] = $fldval;
		}
		$idfocus->column_fields['related_to'] = $entityData->getId();
		$idfocus->column_fields['account_id'] = $_REQUEST[$acc_con_fldnames['a']];
		$idfocus->column_fields['contact_id'] = $_REQUEST[$acc_con_fldnames['c']];
		$idfocus->column_fields['vendor_id'] = $_REQUEST['vendor_id'];
		$idfocus->column_fields['sequence_no'] = $lineseq;
		$idfocus->column_fields['discount_percent'] = $line['discount_type'] == 'p' ? $line['discount_amount'] : 0;
		$idfocus->column_fields['discount_amount'] = $line['discount_type'] == 'd' ? $line['discount_amount'] : 0;
		$idfocus->column_fields['total_stock'] = $line['qtyinstock'];

		self::sanitizeAndSaveIdLine($idfocus);
	}

	private static function saveExistingIDLine($entityData, $line, $idfocus, $lineseq) {
		$idfocus->retrieve_entity_info($line['crmid'], 'InventoryDetails');
		$idfocus->mode = 'edit';
		$idfocus->id = $line['crmid'];
		$acc_con_fldnames = self::getAccConFieldNames();
		foreach ($line as $fldname => $fldval) {
			$idfocus->column_fields[$fldname] = $fldval;
		}
		$idfocus->column_fields['account_id'] = $_REQUEST[$acc_con_fldnames['a']];
		$idfocus->column_fields['contact_id'] = $_REQUEST[$acc_con_fldnames['c']];
		$idfocus->column_fields['vendor_id'] = $_REQUEST['vendor_id'];
		$idfocus->column_fields['sequence_no'] = $lineseq;
		$idfocus->column_fields['discount_percent'] = $line['discount_type'] == 'p' ? $line['discount_amount'] : 0;
		$idfocus->column_fields['discount_amount'] = $line['discount_type'] == 'd' ? $line['discount_amount'] : 0;

		self::sanitizeAndSaveIdLine($idfocus);
	}

	private static function sanitizeAndSaveIdLine($focus) {
		global $current_user;
		$handler = vtws_getModuleHandlerFromName('InventoryDetails', $current_user);
		$meta = $handler->getMeta();
		$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);
		$focus->save('InventoryDetails');
		return $focus;
	}

	private static function saveAggregation($entityData) {
		global $adb;
		$modname = $entityData->getModuleName();
		$modid = $entityData->getId();
		include_once 'modules/' . $modname . '/' . $modname . '.php';
		$focus = new $modname();
		$q = "UPDATE {$focus->table_name} SET ";

		foreach ($_REQUEST['aggr_fields'] as $fldname => $fldval) {
			$q .= "{$fldname} = '{$fldval}',";
		}
		$q = rtrim($q, ',');
		$q .= "WHERE {$focus->table_index} = {$modid}";
		$adb->query($q);
	}

	private static function getAccConFieldNames() {
		if (!isset($_REQUEST['account_id'])) {
			return array('a' => 'accid', 'c' => 'ctoid');
		} else {
			return array('a' => 'account_id', 'c' => 'contact_id');
		}
	}
}