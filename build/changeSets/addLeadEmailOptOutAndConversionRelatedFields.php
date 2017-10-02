<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
class addLeadEmailOptOutAndConversionRelatedFields extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$modname = 'Leads';
			$module = Vtiger_Module::getInstance($modname);
			// Add Email Opt-out for Leads
			$block = Vtiger_Block::getInstance('LBL_LEAD_INFORMATION', $module);
			$field = Vtiger_Field::getInstance('emailoptout',$module);
			if ($field) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($field->id));
			} else {
				$leadsOptOutField = new Vtiger_Field();
				$leadsOptOutField->name = 'emailoptout';
				$leadsOptOutField->label = 'Email Opt Out';
				$leadsOptOutField->table = 'vtiger_leaddetails';
				$leadsOptOutField->column = $leadsOptOutField->name;
				$leadsOptOutField->columntype = 'VARCHAR(3)';
				$leadsOptOutField->uitype = 56;
				$leadsOptOutField->typeofdata = 'C~O';
				$leadsOptOutField->displaytype = 1;
				$leadsOptOutField->quickcreate = 0;
				$block->addField($leadsOptOutField);
				$this->ExecuteQuery('UPDATE vtiger_leaddetails SET emailoptout=0 WHERE emailoptout IS NULL', array());
			}
			// Conversion Related Fields
			$fieldLayout = array(
				'Accounts' => array(
					'LBL_ACCOUNT_INFORMATION' => array(
						'isconvertedfromlead' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>56,
							'label' => 'Is Converted From Lead',
							'displaytype'=>'2',
						),
						'convertedfromlead' => array(
							'columntype'=>'INT(11)',
							'typeofdata'=>'V~O',
							'uitype'=>10,
							'label' => 'Converted From Lead',
							'displaytype'=>'3',
							'mods'=>array('Leads'),
						),
					)),
				'Contacts' => array(
					'LBL_CONTACT_INFORMATION' => array(
						'isconvertedfromlead' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>56,
							'label' => 'Is Converted From Lead',
							'displaytype'=>'2',
						),
						'convertedfromlead' => array(
							'columntype'=>'INT(11)',
							'typeofdata'=>'V~O',
							'uitype'=>10,
							'label' => 'Converted From Lead',
							'displaytype'=>'3',
							'mods'=>array('Leads'),
						),
					)),
				'Potentials' => array(
					'LBL_OPPORTUNITY_INFORMATION' => array(
						'isconvertedfromlead' => array(
							'columntype'=>'varchar(3)',
							'typeofdata'=>'C~O',
							'uitype'=>56,
							'label' => 'Is Converted From Lead',
							'displaytype'=>'2',
						),
						'convertedfromlead' => array(
							'columntype'=>'INT(11)',
							'typeofdata'=>'V~O',
							'uitype'=>10,
							'label' => 'Converted From Lead',
							'displaytype'=>'3',
							'mods'=>array('Leads'),
						),
					)),
			);
			$this->massCreateFields($fieldLayout);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}

?>
