<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addFinancialFields extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$ffields = array(
				'pl_gross_total' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Gross Total',
				),
				'pl_dto_line' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Line Discount',
				),
				'pl_dto_global' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Global Discount',
				),
				'pl_dto_total' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Total Discount',
				),
				'pl_net_total' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Net Total (aGD)',
					'helpinfo' => 'aGD',
				),
				'pl_sh_total' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'SH Total',
				),
				'pl_sh_tax' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'SH Tax',
				),
				'pl_adjustment' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Final Adjustment',
				),
				'pl_grand_total' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Grand Total',
				),
				'sum_nettotal' => array(
					'columntype'=>'decimal(25,6)',
					'typeofdata'=>'NN~O',
					'uitype'=>'7',
					'displaytype'=>'2',
					'label'=>'Net Total (bGD)',
					'helpinfo' => 'bGD',
				),
			);
			$fieldLayout = array(
				'Invoice' => array(
					'LBL_Invoice_FINANCIALINFO' => $ffields
				),
				'SalesOrder' => array(
					'LBL_SalesOrder_FINANCIALINFO' => $ffields
				),
				'Quotes' => array(
					'LBL_Quotes_FINANCIALINFO' => $ffields
				),
				'PurchaseOrder' => array(
					'LBL_PurchaseOrder_FINANCIALINFO' => $ffields
				),
			);
			$this->massCreateFields($fieldLayout);
			$ffields = array(
				'pl_gross_total',
				'pl_dto_line',
				'pl_dto_total',
				'pl_dto_global',
				'pl_net_total',
				'sum_nettotal',
				'sum_taxtotal',
				'sum_tax1',
				'sum_taxtotalretention',
				'sum_tax2',
				'pl_sh_total',
				'sum_tax3',
				'pl_sh_tax',
				'pl_grand_total',
				'pl_adjustment',
			);
			$fieldLayout = array(
				'Invoice' => array(
					'LBL_Invoice_FINANCIALINFO' => $ffields
				),
				'SalesOrder' => array(
					'LBL_SalesOrder_FINANCIALINFO' => $ffields
				),
				'Quotes' => array(
					'LBL_Quotes_FINANCIALINFO' => $ffields
				),
				'PurchaseOrder' => array(
					'LBL_PurchaseOrder_FINANCIALINFO' => $ffields
				),
			);
			$this->orderFieldsInBlocks($fieldLayout);

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
			require_once 'include/setVAT.php';
			$util = new VTWorkflowUtils();
			$adminUser = $util->adminUser();
			foreach ($modules as $mod) {
				$rs = $adb->pquery(
					"select 1 from com_vtiger_workflows
					where (summary='Update Tax fields on every save' or summary='Update Financial fields on every save') and module_name=?",
					array($mod['name'])
				);
				if ($rs && $adb->num_rows($rs)==0) {
					$emm = new VTEntityMethodManager($adb);
					$emm->addEntityMethod($mod['name'], 'Set Financial Fields', 'include/setVAT.php', 'setVAT');
					// Creating Workflow for Updating Financial Fields
					$vtWorkFlow = new VTWorkflowManager($adb);
					$invWorkFlow = $vtWorkFlow->newWorkFlow($mod['name']);
					$invWorkFlow->description = 'Update Financial fields on every save';
					$invWorkFlow->test = '';
					$invWorkFlow->defaultworkflow = 1;
					$invWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
					$invWorkFlow->schtypeid = 0;
					$invWorkFlow->schtime = '00:00:00';
					$invWorkFlow->schdayofmonth = '';
					$invWorkFlow->schdayofweek = '';
					$invWorkFlow->schannualdates = '';
					$invWorkFlow->schminuteinterval = '';
					$vtWorkFlow->save($invWorkFlow);

					$tm = new VTTaskManager($adb);
					$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
					$task->active=true;
					$task->summary = 'Update Financial fields on every save';
					$task->methodName = 'Set Financial Fields';
					$tm->saveTask($task);
				}
				$module = VTiger_Module::getInstance($mod['name']);
				$block = Vtiger_Block::getInstance('LBL_'.$mod['name'].'_FINANCIALINFO', $module);
				if (!$block) {
					$block = new Vtiger_Block();
					$block->label = 'LBL_'.$mod['name'].'_FINANCIALINFO';
					$module->addBlock($block);
				}
				$rstax=$adb->query('select taxname,taxlabel from vtiger_inventorytaxinfo WHERE deleted=0');
				while ($tx=$adb->fetch_array($rstax)) {
					$field1 = new Vtiger_Field();
					$field1->name = 'sum_'.$tx['taxname'];
					$field1->label= $tx['taxlabel'];
					$field1->column = 'sum_'.$tx['taxname'];
					$field1->columntype = 'DECIMAL(25,6)';
					$field1->uitype = 7;
					$field1->typeofdata = 'NN~O';
					$field1->displaytype = 2;
					$field1->presence = 0;
					$block->addField($field1);
				}
				$field1 = new Vtiger_Field();
				$field1->name = 'sum_taxtotal';
				$field1->label= 'Total Tax';
				$field1->column = 'sum_taxtotal';
				$field1->columntype = 'DECIMAL(25,6)';
				$field1->uitype = 7;
				$field1->typeofdata = 'NN~O';
				$field1->displaytype = 2;
				$field1->presence = 0;
				$block->addField($field1);
				$field1 = new Vtiger_Field();
				$field1->name = 'sum_taxtotalretention';
				$field1->label= 'Total Tax Retention';
				$field1->column = 'sum_taxtotalretention';
				$field1->columntype = 'DECIMAL(25,6)';
				$field1->uitype = 7;
				$field1->typeofdata = 'NN~O';
				$field1->displaytype = 2;
				$field1->presence = 0;
				$block->addField($field1);
			}
			$haction = $_REQUEST['action'];
			$_REQUEST['action'] = 'Save';
			unset($_REQUEST['ajxaction']);
			$ffmodsdone = coreBOS_Settings::getSetting('addFFModsDone', '');
			$ffcrmiddone = coreBOS_Settings::getSetting('addFFcrmidDone', 0);
			foreach ($modules as $mod) {
				if (strpos($ffmodsdone, $mod['name'])>0) {
					continue;
				}
				$invModWSID = vtws_getEntityId($mod['name']);
				$entities=$adb->pquery(
					"select {$mod['id']}
						from {$mod['table']}
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = {$mod['table']}.{$mod['id']}
						WHERE deleted = 0 and crmid>?
						ORDER BY crmid",
					array($ffcrmiddone)
				);
				$this->sendMsg('Updating Financial fields for '.$mod['name']);
				while ($ent=$adb->fetch_array($entities)) {
					$id = $ent[$mod['id']];
					$entity = new VTWorkflowEntity($adminUser, $invModWSID.'x'.$id);
					$entity->moduleName = $mod['name'];
					setVAT($entity);
					coreBOS_Settings::setSetting('addFFcrmidDone', $id);
				}
				coreBOS_Settings::setSetting('addFFModsDone', $ffmodsdone.'_'.$mod['name']);
				coreBOS_Settings::setSetting('addFFcrmidDone', 0);
			}
			$_REQUEST['action'] = $haction;
			$util->revertUser();
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
			coreBOS_Settings::delSetting('addFFModsDone');
			coreBOS_Settings::delSetting('addFFcrmidDone');
		}
		$this->finishExecution();
	}
}
?>
