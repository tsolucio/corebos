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

class cbupdater_blockperspective extends cbupdaterWorker {
	
	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$moduleInstance = Vtiger_Module::getInstance('cbupdater');
			$block = Vtiger_Block::getInstance('LBL_cbupdater_INFORMATION', $moduleInstance);
			// fields
			$fieldp = Vtiger_Field::getInstance('perspective',$moduleInstance);
			if ($fieldp) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($fieldp->id));
			} else {
				$fieldp = new Vtiger_Field();
				$fieldp->name = 'perspective';
				$fieldp->label = 'perspective';
				$fieldp->table ='vtiger_cbupdater';
				$fieldp->column = 'perspective';
				$fieldp->columntype = 'varchar(3)';
				$fieldp->typeofdata = 'C~O';
				$fieldp->uitype = '56';
				$fieldp->sequence = 9;
				$fieldp->masseditable = '0';
				$block->addField($fieldp);
			}
			$fieldb = Vtiger_Field::getInstance('blocked',$moduleInstance);
			if ($fieldb) {
				$this->ExecuteQuery('update vtiger_field set presence=2 where fieldid=?',array($fieldb->id));
			} else {
				$fieldb = new Vtiger_Field();
				$fieldb->name = 'blocked';
				$fieldb->label = 'blocked';
				$fieldb->table ='vtiger_cbupdater';
				$fieldb->column = 'blocked';
				$fieldb->columntype = 'varchar(3)';
				$fieldb->typeofdata = 'C~O';
				$fieldb->uitype = '56';
				$fieldb->sequence = 11;
				$fieldb->masseditable = '0';
				$block->addField($fieldb);
			}
			$this->ExecuteQuery("update vtiger_cbupdater set perspective='0' where perspective is null");
			$this->ExecuteQuery("update vtiger_cbupdater set blocked='0' where blocked is null");
			$field = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set sequence=10 where fieldid=?',array($field->id));
			$field = Vtiger_Field::getInstance('createdtime',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set sequence=12 where fieldid=?',array($field->id));
			$field = Vtiger_Field::getInstance('modifiedtime',$moduleInstance);
			$this->ExecuteQuery('update vtiger_field set sequence=13 where fieldid=?',array($field->id));
			$rdo = $adb->pquery('select count(*) from vtiger_execstate where execstate=?',array('Continuous'));
			if ($rdo and $adb->query_result($rdo,0,0)==0) {
				$field = Vtiger_Field::getInstance('execstate',$moduleInstance);
				$field->setPicklistValues(array('Continuous'));
			}
			// filters
			$field0 = Vtiger_Field::getInstance('cbupd_no',$moduleInstance);
			$field1 = Vtiger_Field::getInstance('execdate',$moduleInstance);
			$field2 = Vtiger_Field::getInstance('author',$moduleInstance);
			$field3 = Vtiger_Field::getInstance('filename',$moduleInstance);
			$field4 = Vtiger_Field::getInstance('execstate',$moduleInstance);
			$field5 = Vtiger_Field::getInstance('systemupdate',$moduleInstance);
			$field6 = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
			
			// Continuous
			$rdo = $adb->query("SELECT count(*) FROM vtiger_customview WHERE viewname = 'Continuous' and entitytype = 'cbupdater'");
			if ($rdo and $adb->query_result($rdo,0,0)==0) {
				$filterInstance = new Vtiger_Filter();
				$filterInstance->name = 'Continuous';
				$filterInstance->isdefault = false;
				$moduleInstance->addFilter($filterInstance);
				$filterInstance->addField($field0,0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3)->addField($field4, 4)->addField($field5, 5)->addField($field6, 6);
				$filterInstance->addRule($field4, 'EQUALS', 'Continuous', 0);
			}
			// blocked
			$rdo = $adb->query("SELECT count(*) FROM vtiger_customview WHERE viewname = 'blocked' and entitytype = 'cbupdater'");
			if ($rdo and $adb->query_result($rdo,0,0)==0) {
				$filterInstance = new Vtiger_Filter();
				$filterInstance->name = 'blocked';
				$filterInstance->isdefault = false;
				$moduleInstance->addFilter($filterInstance);
				$filterInstance->addField($field0,0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3)->addField($field4, 4)->addField($field5, 5)->addField($field6, 6);
				$filterInstance->addRule($fieldb, 'EQUALS', '1', 0);
			}
			$rdo = $adb->query("SELECT count(*) FROM vtiger_customview WHERE viewname = 'perspective' and entitytype = 'cbupdater'");
			if ($rdo and $adb->query_result($rdo,0,0)==0) {
				// perspective
				$filterInstance = new Vtiger_Filter();
				$filterInstance->name = 'perspective';
				$filterInstance->isdefault = false;
				$moduleInstance->addFilter($filterInstance);
				$filterInstance->addField($field0,0)->addField($field1, 1)->addField($field2, 2)->addField($field3, 3)->addField($field4, 4)->addField($field5, 5)->addField($field6, 6);
				$filterInstance->addRule($fieldp, 'EQUALS', '1', 0);
			}
			// Now that we have continuos execution we set webservice updater to this state
			$this->ExecuteQuery('update vtiger_cbupdater set execstate=? where filename=? and classname=?',array('Continuous','coreboscp_rest','coreboscp_rest'));
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}