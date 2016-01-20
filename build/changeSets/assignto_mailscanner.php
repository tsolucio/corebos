<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class assignto_mailscanner extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset assignto_mailscanner already applied!');
		} else {
                        $mod_hd = Vtiger_Module::getInstance('HelpDesk');
                        $block_hd = Vtiger_Block::getInstance('LBL_TICKET_INFORMATION', $mod_hd);
                        $field1 = new Vtiger_Field();
                        $field1->name = 'from_mailscanner';
                        $field1->label= 'From mailscanner';
                        $field1->column = 'from_mailscanner';
                        $field1->columntype = 'VARCHAR(3)';
                        $field1->sequence = 1;
                        $field1->uitype = 56;
                        $field1->typeofdata = 'C~O';
                        $field1->displaytype = 3;
                        $field1->presence = 0;
                        $field1->readonly = 1;
                        $block_hd->addField($field1);
                        
                        $this->ExecuteQuery('ALTER TABLE vtiger_mailscanner_rules ADD assign_to INT(11)');
                         
			$this->sendMsg('Changeset assignto_mailscanner applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		$this->finishExecution();
	}
	
}