<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class inventoryproductstockcontrol extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$emm = new VTEntityMethodManager($adb);
			// Adding EntityMethod for Updating Products data after updating PurchaseOrder
			$emm->addEntityMethod("PurchaseOrder", "UpdateInventory", "include/InventoryHandler.php", "handleInventoryProductRel");
			// Creating Workflow for Updating Inventory Stock on PO
			$vtWorkFlow = new VTWorkflowManager($adb);
			$invWorkFlow = $vtWorkFlow->newWorkFlow("PurchaseOrder");
			$invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
			$invWorkFlow->description = "UpdateInventoryProducts On Every Save";
			$invWorkFlow->defaultworkflow = 1;
			$vtWorkFlow->save($invWorkFlow);

			$tm = new VTTaskManager($adb);
			$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
			$task->active=true;
			$task->methodName = "UpdateInventory";
			$task->summary="Update product stock";
			$tm->saveTask($task);
			// add Cancel status to Invoice and SO for stock control
			$moduleInstance = Vtiger_Module::getInstance('Invoice');
			$field = Vtiger_Field::getInstance('invoicestatus', $moduleInstance);
			if ($field) {
				$field->setPicklistValues(array('Cancel'));
			}
			$this->sendMsg('Changeset '.get_class($this).' applied! Add Workflow Custom Function complete!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}

	public function isApplied() {
		$done = parent::isApplied();
		if (!$done) {
			global $adb;
			$result = $adb->pquery("SELECT * FROM com_vtiger_workflowtasks_entitymethod where module_name = 'PurchaseOrder' and method_name= 'UpdateInventory'",array());
			$done = ($result && $adb->num_rows($result)==1);
			$result = $adb->pquery("SELECT `workflow_id` FROM `com_vtiger_workflows` WHERE `module_name` = 'PurchaseOrder' and `summary`='UpdateInventoryProducts On Every Save'",array());
			$done = ($done && $result && $adb->num_rows($result)==1);
		}
		return $done;
	}
}