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

class addcbquestionfieldsandactions extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$module = 'cbQuestion';
			$modobj = Vtiger_Module::getInstance($module);
			if ($modobj) {
				$wasActive = vtlib_isModuleActive($module);
				if (!$wasActive) {
					vtlib_toggleModuleAccess($module, true);
				}
				$modobj = Vtiger_Module::getInstance($module);
				if ($modobj) {
					$block = new Vtiger_Block();
					$block->label = 'SQLQuery';
					$block->sequence = 1;
					$modobj->addBlock($block);
				}
				$fields = array(
					'cbQuestion' => array(
						'LBL_cbQuestion_INFORMATION' => array(
							'sqlquery' => array(
								'columntype'=>'varchar(3)',
								'typeofdata'=>'C~O',
								'uitype'=>'56',
								'displaytype'=>'1',
								'label'=>'SQLQuery',
								'massedit' => 0,
							),
							'mviewcron' => array(
								'columntype'=>'varchar(3)',
								'typeofdata'=>'C~O',
								'uitype'=>'56',
								'displaytype'=>'1',
								'label'=>'MViewCron',
								'massedit' => 0,
							),
							'uniqueid' => array(
								'columntype'=>'varchar(85)',
								'typeofdata'=>'V~O',
								'uitype'=>'1',
								'displaytype'=>'1',
								'label'=>'uniqueid', // optional, if empty fieldname will be used
								'massedit' => 0,
							),
							'mviewwf' => array(
								'columntype'=>'varchar(3)',
								'typeofdata'=>'C~O',
								'uitype'=>'56',
								'displaytype'=>'1',
								'label'=>'MViewWF',
								'massedit' => 0,
							),
						)
					),
				);
				$this->massCreateFields($fields);
				$fields = array(
					'cbQuestion' => array(
						'LBL_cbQuestion_INFORMATION' => array(
							'qname',
							'cbquestionno',
							'qtype',
							'qstatus',
							'sqlquery',
							'qcollection',
							'qmodule',
							'qpagesize',
							'uniqueid',
							'mviewcron',
							'cbmapid',
							'mviewwf',
						),
					),
				);
				$this->orderFieldsInBlocks($fields);
				Vtiger_Cron::register(
					'MaterializedViewSync',
					'modules/cbQuestion/cron/mview.php',
					43200,
					'cbQuestion',
					Vtiger_Cron::$STATUS_DISABLED,
					0,
					'Sync all active materialized views.'
				);
				$tabid = getTabid($module);
				$actions = array(
					array(
						'menutype' => 'subheader',
						'title' => 'cbqActionHeader',
						'href' => 'ACTIONSUBHEADER',
						'icon' => '',
					),
					array(
						'menutype' => 'item',
						'title' => 'Test SQL',
						'href' => 'javascript:cbqtestsql($RECORD$);',
						'icon' => '{"library":"custom", "icon":"custom102"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Create Map',
						'href' => 'javascript:cbqcreatemap($RECORD$);',
						'icon' => '{"library":"utility", "icon":"add"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Create View',
						'href' => 'javascript:cbqcreateview($RECORD$);',
						'icon' => '{"library":"utility", "icon":"add"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Create MView',
						'href' => 'javascript:cbqcreatemview($RECORD$);',
						'icon' => '{"library":"utility", "icon":"add"}',
					),
					array(
						'menutype' => 'subheader',
						'title' => 'MView',
						'href' => 'ACTIONSUBHEADER',
						'icon' => '',
					),
					array(
						'menutype' => 'item',
						'title' => 'Remove MView',
						'href' => 'javascript:cbqremovemview($RECORD$);',
						'icon' => '{"library":"utility", "icon":"delete"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Add MView Cron',
						'href' => 'javascript:cbqaddmviewcron($RECORD$);',
						'icon' => '{"library":"utility", "icon":"add"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Del MView Cron',
						'href' => 'javascript:cbqdelmviewcron($RECORD$);',
						'icon' => '{"library":"utility", "icon":"delete"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Add MView Workflow',
						'href' => 'javascript:cbqaddmviewwf($RECORD$);',
						'icon' => '{"library":"utility", "icon":"add"}',
					),
					array(
						'menutype' => 'item',
						'title' => 'Del MView Workflow',
						'href' => 'javascript:cbqdelmviewwf($RECORD$);',
						'icon' => '{"library":"utility", "icon":"delete"}',
					),
				);
				foreach ($actions as $action) {
					BusinessActions::addLink($tabid, 'DETAILVIEWBASIC', $action['title'], $action['href'], $action['icon'], 0, null, true, 0);
				}
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