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

class entitylogimport extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$toinstall = array('EntityLog');
			foreach ($toinstall as $module) {
				if ($this->isModuleInstalled($module)) {
					vtlib_toggleModuleAccess($module,true);
					$this->sendMsg("$module activated!");
				} else {
					$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
// Create module instance and save it first
$module = new Vtiger_Module();
$module->name = 'Entitylog';
$module->save();
$module->initWebservice();
//// Initialize all the tables required
$module->initTables();
/**
* Creates the following table:
* vtiger_payslip (payslipid INTEGER)
* vtiger_payslipcf(payslipid INTEGER PRIMARY KEY)
* vtiger_payslipgrouprel((payslipid INTEGER PRIMARY KEY, groupname VARCHAR(100))
*/
 //Add the module to the Menu (entry point from UI)
$menu = Vtiger_Menu::getInstance('Sales');
$menu->addModule($module);
// Add the basic module block
$block1 = new Vtiger_Block();
$block1->label = 'LBL_ENTITYLOG_INFORMATION';
$module->addBlock($block1);
// Add custom block (required to support Custom Fields)
$block2 = new Vtiger_Block();
$block2->label = 'LBL_CUSTOM_INFORMATION';
$module->addBlock($block2);

$block3 = new Vtiger_Block();
$block3->label = 'LBLDESCRIPTIONINFORMATION';
$module->addBlock($block3);
/** Create required fields and add to the block */

$field1 = new Vtiger_Field();
$field1->name = 'entitylogname';
$field1->label = 'Entitylog Name';
$field1->table ='vtiger_entitylog';
$field1->column = 'entitylogname';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';
$block1->addField($field1);
// Set at-least one field to identifier of module record
$module->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->name = 'relatedto';
$field2->label = 'Related to';
$field2->table ='vtiger_entitylog';
$field2->column = 'relatedto';
$field2->columntype = 'VARCHAR(100)';
$field2->uitype = 10;
$field2->typeofdata = 'V~O';
$block1->addField($field2);
$field2->setRelatedModules(Array('Project','HelpDesk'));

//
$field4=new Vtiger_Field();
$field4->name = 'user';
$field4->label = 'User';
$field4->column = 'user';
$field4->columntype = 'VARCHAR(100)';
$field4->uitype = 1;
$field4->typeofdata = 'V~O';
$block1->addField($field4);

$field8 = new Vtiger_Field();
$field8->name = 'tabid';
$field8->label= 'Related Module';
$field8->column = 'tabid';
$field8->columntype = 'INT(11)';
$field8->uitype = 1;
$field8->typeofdata = 'V~M';
$field8->presence = 2;
$block1->addField($field8);

$field9=new Vtiger_Field();
$field9->name = 'finalstate';
$field9->label = 'Final State';
$field9->column = 'finalstate';
$field9->columntype = 'VARCHAR(100)';
$field9->uitype = 1;
$field9->typeofdata = 'V~O';
$block1->addField($field9);
//
///** Common fields that should be in every module, linked to vtiger CRM core table
//*/
$field5 = new Vtiger_Field();
$field5->name = 'assigned_user_id';
$field5->label = 'Assigned To';
$field5->table = 'vtiger_crmentity';
$field5->column = 'smownerid';
$field5->uitype = 53;
$field5->typeofdata = 'V~M';
//
$block1->addField($field5);
$field6 = new Vtiger_Field();
$field6->name = 'CreatedTime';
$field6->label= 'Created Time';
$field6->table = 'vtiger_crmentity';
$field6->column = 'createdtime';
$field6->uitype = 70;
$field6->typeofdata = 'T~O';
$field6->displaytype= 2;
$block1->addField($field6);
//
$field7 = new Vtiger_Field();
$field7->name = 'ModifiedTime';
$field7->label= 'Modified Time';
$field7->table = 'vtiger_crmentity';
$field7->column = 'modifiedtime';
$field7->uitype = 70;
$field7->typeofdata = 'T~O';
$field7->displaytype= 2;
$block1->addField($field7);
//
$field31 = new Vtiger_Field();
$field31->name = 'description';
$field31->label= 'Description';
$field31->table = 'vtiger_crmentity';
$field31->column = 'description';
$field31->columntype = 'VARCHAR(256)';
$field31->uitype = 19;
$field31->presence = 0;
$field31->typeofdata = 'V~O';
$block3->addField($field31);
/** END */
// Create default custom filter (mandatory)
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);
// Add fields to the filter created
$filter1->addField($field1)->addField($field2, 1);
/** Set sharing access of this module */
$module->setDefaultSharing('Private');
/** Enable and Disable available tools */
$module->enableTools(Array('Import', 'Export'));
$module->disableTools('Merge');
				}
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			vtlib_toggleModuleAccess('EntityLog',false);
			$this->sendMsg('EntityLog deactivated!');
			$this->markUndone(false);
			$this->sendMsg('Changeset '.get_class($this).' undone!');
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied, it cannot be undone!');
		}
		$this->finishExecution();
	}
	
}