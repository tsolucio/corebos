<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class addcbquestionfilterformatfield extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$module = 'cbQuestion';
			$moduleInstance = Vtiger_Module::getInstance($module);
			if ($moduleInstance) {
				$wasActive = vtlib_isModuleActive($module);
				if (!$wasActive) {
					vtlib_toggleModuleAccess($module, true);
				}
				$fields = array(
					'cbQuestion' => array(
						'LBL_cbQuestion_INFORMATION' => array(
							'condfilterformat' => array(
								'columntype'=>'varchar(3)',
								'typeofdata'=>'C~O',
								'uitype'=>'56',
								'displaytype'=>'1',
								'label'=>'condfilterformat',
								'massedit' => 1,
							),
						)
					),
				);
				$this->massCreateFields($fields);
				if (!$wasActive) {
					vtlib_toggleModuleAccess($module, false);
				}
				$this->sendMsg('Changeset '.get_class($this).' applied!');
				$this->markApplied();
			} else {
				$this->sendMsgError('Changeset '.get_class($this).' could not be applied yet. Please launch again.');
			}
		}
		$this->finishExecution();
	}
}