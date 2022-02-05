<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class addFieldsToCyP extends cbupdaterWorker {
	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$modname = 'CobroPago';
			$module = Vtiger_Module::getInstance($modname);
			if ($module) {
				$block = Vtiger_Block::getInstance('LBL_COBROPAGO_INFORMATION', $module);
				$fld_ref = Vtiger_Field::getInstance('reference', $module);
				$this->ExecuteQuery("UPDATE vtiger_field SET typeofdata='V~O' WHERE fieldid={$fld_ref->id}");
				$this->ExecuteQuery("UPDATE vtiger_field SET sequence=sequence+1 WHERE block={$block->id} AND sequence>1");
				$field = Vtiger_Field::getInstance('cyp_no', $module);
				if (!$field) {
					$field1 = new Vtiger_Field();
					$field1->name = 'cyp_no';
					$field1->label= 'CyP No';
					$field1->column = 'cyp_no';
					$field1->columntype = 'VARCHAR(50)';
					$field1->sequence = 2;
					$field1->uitype = 4;
					$field1->typeofdata = 'V~M';
					$field1->displaytype = 1;
					$field1->presence = 0;
					$block->addField($field1);
				}
				$fld_due = Vtiger_Field::getInstance('duedate', $module);
				$qry = "SELECT sequence FROM vtiger_field WHERE fieldid={$fld_due->id}";
				$res = $adb->query($qry);
				$seq = $adb->query_result($res, 0, 'sequence');
				$this->ExecuteQuery("UPDATE vtiger_field SET sequence=sequence+1 WHERE block={$block->id} AND sequence>$seq");
				$field = Vtiger_Field::getInstance('paymentdate', $module);
				if (!$field) {
					$field1 = new Vtiger_Field();
					$field1->name = 'paymentdate';
					$field1->label= 'PaymentDate';
					$field1->column = 'paymentdate';
					$field1->columntype = 'DATE';
					$field1->sequence = $seq+1;
					$field1->uitype = 5;
					$field1->typeofdata = 'D~O';
					$field1->displaytype = 1;
					$field1->presence = 0;
					$block->addField($field1);
				}
				$res_ui4 = $adb->pquery("SELECT * FROM vtiger_field WHERE tabid=? AND uitype=? AND fieldname<>?", array($module->id,'4','cyp_no'));
				if ($adb->num_rows($res_ui4)!=0) {
					$fld_ui4_id = $adb->query_result($res_ui4, 0, 'fieldid');
					$fld_ui4_name = $adb->query_result($res_ui4, 0, 'fieldname');
					$fld_ui4_colname = $adb->query_result($res_ui4, 0, 'columnname');
					$this->ExecuteQuery("UPDATE vtiger_field SET uitype=? WHERE fieldid=?", array('1',$fld_ui4_id));
				}
				$res = $adb->query("SELECT * FROM vtiger_modentity_num WHERE semodule='CobroPago'");
				if ($adb->num_rows($res)==0) {
					$focus = CRMEntity::getInstance($modname);
					$focus->setModuleSeqNumber('configure', $modname, 'PAY-', '0000001');
					$focus->updateMissingSeqNumber($modname);
				} elseif (!empty($fld_ui4_colname)) {
					$this->ExecuteQuery("UPDATE vtiger_cobropago SET cyp_no=$fld_ui4_colname");
					//Workflow, copy CyP No to Reference
					$vtWorkFlow = new VTWorkflowManager($adb);
					$invWorkFlow = $vtWorkFlow->newWorkFlow('CobroPago');
					$invWorkFlow->description = "Number to Reference";
					$invWorkFlow->executionCondition = 3;
					$invWorkFlow->defaultworkflow = 1;
					$vtWorkFlow->save($invWorkFlow);

					$tm = new VTTaskManager($adb);
					$task = $tm->createTask('VTUpdateFieldsTask', $invWorkFlow->id);
					$task->active=true;
					$task->summary = "Number to Reference";
					$task->field_value_mapping = '[{"fieldname":"'.$fld_ui4_name.'","valuetype":"fieldname","value":"cyp_no "}]';
					$tm->saveTask($task);
				}
				$this->ExecuteQuery("UPDATE vtiger_entityname SET fieldname=CONCAT(fieldname,',cyp_no') WHERE tabid={$module->id}");
				$this->ExecuteQuery("UPDATE vtiger_cobropago SET paymentdate=duedate");
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			} else {
				$this->sendMsgError('Changeset '.get_class($this).' <strong>NOT applied!</strong>  Payment module not found.');
			}
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		}
		$this->finishExecution();
	}
}
?>
