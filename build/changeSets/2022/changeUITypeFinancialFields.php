<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class changeUITypeFinancialFields extends cbupdaterWorker {

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
				'pl_gross_total',
				'pl_dto_line',
				'pl_dto_total',
				'pl_dto_global',
				'pl_net_total',
				'sum_nettotal',
				'sum_taxtotal',
				'sum_taxtotalretention',
				'sum_tax1',
				'sum_tax2',
				'sum_tax3',
				'pl_sh_total',
				'pl_sh_tax',
				'pl_grand_total',
				'pl_adjustment',
			);
			$modules = array(
				'Invoice',
				'SalesOrder',
				'Quotes',
				'PurchaseOrder',
			);
			foreach ($modules as $mod) {
				$tabid = getTabid($mod);
				$rstax=$adb->query('select taxname,taxlabel from vtiger_inventorytaxinfo WHERE deleted=0');
				while ($tx=$adb->fetch_array($rstax)) {
					$ffields[] = 'sum_'.$tx['taxname'];
				}
				$adb->pquery(
					'update vtiger_field set uitype=72 where uitype=7 and tabid=? and columnname in ('.generateQuestionMarks($ffields).')',
					array_merge([$tabid], $ffields)
				);
				$this->sendMsg('Converted '.$mod.' fields.');
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
?>
