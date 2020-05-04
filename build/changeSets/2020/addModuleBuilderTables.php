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

class addModuleBuilderTables extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_name` (
				  `moduleid` int(11) NOT NULL,
				  `modulebuilderid` int(11) NOT NULL,
				  `date` date NOT NULL,
				  `completed` varchar(10) NOT NULL,
				  `userid` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder` (
				  `modulebuilderid` int(11) NOT NULL,
				  `modulebuilder_name` varchar(50) NOT NULL,
				  `modulebuilder_label` varchar(50) NOT NULL,
				  `modulebuilder_parent` varchar(50) NOT NULL,
				  `status` varchar(10) NOT NULL DEFAULT "active"
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_actions` (
				  `actionsid` int(11) NOT NULL,
				  `actionname` varchar(50) NOT NULL,
				  `status` varchar(50) NOT NULL,
				  `moduleid` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_blocks` (
				  `blocksid` int(11) NOT NULL,
				  `blocks_label` varchar(100) NOT NULL,
				  `moduleid` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_customview` (
				  `customviewid` int(11) NOT NULL,
				  `viewname` varchar(100) NOT NULL,
				  `setdefault` varchar(10) NOT NULL DEFAULT "false",
				  `setmetrics` varchar(10) NOT NULL DEFAULT "false",
				  `fields` text NOT NULL,
				  `moduleid` int(11) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_fields` (
				  `fieldsid` int(11) NOT NULL,
				  `blockid` int(11) NOT NULL,
				  `fieldname` varchar(100) NOT NULL,
				  `uitype` int(11) NOT NULL,
				  `columnname` varchar(100) NOT NULL,
				  `tablename` varchar(100) NOT NULL,
				  `generatedtype` int(11) NOT NULL,
				  `fieldlabel` varchar(100) NOT NULL,
				  `readonly` int(11) NOT NULL,
				  `presence` int(11) NOT NULL,
				  `selected` int(11) NOT NULL,
				  `sequence` int(11) NOT NULL,
				  `maximumlength` int(11) NOT NULL,
				  `typeofdata` varchar(100) NOT NULL,
				  `quickcreate` int(11) NOT NULL,
				  `quickcreatesequence` int(11) NOT NULL,
				  `displaytype` int(11) NOT NULL,
				  `info_type` varchar(10) NOT NULL,
				  `helpinfo` text NOT NULL,
				  `masseditable` int(11) NOT NULL,
				  `entityidentifier` varchar(10) NOT NULL DEFAULT "no",
				  `entityidfield` varchar(100) NOT NULL,
				  `entityidcolumn` varchar(100) NOT NULL,
				  `relatedmodules` varchar(100) NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_name` ADD PRIMARY KEY (`moduleid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder` ADD PRIMARY KEY (`modulebuilderid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_actions` ADD PRIMARY KEY (`actionsid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_blocks` ADD PRIMARY KEY (`blocksid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_customview` ADD PRIMARY KEY (`customviewid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_fields` ADD PRIMARY KEY (`fieldsid`)'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_name` MODIFY `moduleid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_actions` MODIFY `actionsid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder` MODIFY `modulebuilderid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_blocks` MODIFY `blocksid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_customview` MODIFY `customviewid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->ExecuteQuery(
				'ALTER TABLE `vtiger_modulebuilder_fields` MODIFY `fieldsid` int(11) NOT NULL AUTO_INCREMENT'
			);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}