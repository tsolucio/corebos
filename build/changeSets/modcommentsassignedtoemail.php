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

class modcommentsassignedtoemail extends cbupdaterWorker {

	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$moduleInstance = Vtiger_Module::getInstance('ModComments');
			$block = Vtiger_Block::getInstance('LBL_OTHER_INFORMATION', $moduleInstance);
			$field = Vtiger_Field::getInstance('relatedassignedemail',$moduleInstance);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$modfield = new Vtiger_Field();
				$modfield->name = 'relatedassignedemail';
				$modfield->label = 'Related Assigned Email';
				$modfield->table = $moduleInstance->basetable;
				$modfield->column = 'relatedassignedemail';
				$modfield->columntype = 'varchar(254)';
				$modfield->typeofdata = 'E~O';
				$modfield->uitype = '13';
				$modfield->displaytype = 2; // read only
				$modfield->masseditable = '0';
				$block->addField($modfield);
			}
			$this->ExecuteQuery('UPDATE vtiger_modcomments
				INNER JOIN vtiger_crmentity on crmid=related_to
				INNER JOIN vtiger_users on id = smownerid
				SET relatedassignedemail = email1');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}