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

class addModuleBuilder extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$fieldid = $adb->getUniqueID('vtiger_settings_field');
			$this->ExecuteQuery(
				'INSERT INTO vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto, active, sequence) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
				array($fieldid, 3, 'LBL_MODULE_BUILDER', 'vtlib_modmng.gif', 'LBL_MODULE_BUILDER_DESCRIPTION', 'index.php?module=Settings&action=ModuleBuilder', 0, 1)
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_name` (
					`moduleid` int(20) NOT NULL AUTO_INCREMENT,
					`modulebuilderid` int(20) NOT NULL,
					`date` date NOT NULL,
					`completed` varchar(10) NOT NULL,
					`userid` int(20) NOT NULL,
					PRIMARY KEY (`moduleid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder` (
					`modulebuilderid` int(20) NOT NULL AUTO_INCREMENT,
					`modulebuilder_name` varchar(50) NOT NULL,
					`modulebuilder_label` varchar(50) NOT NULL,
					`modulebuilder_parent` varchar(50) NOT NULL,
					`status` varchar(10) NOT NULL DEFAULT "active",
					`icon` varchar(50) NOT NULL,
					`sharingaccess` varchar(20) NOT NULL,
					`merge` varchar(10) NOT NULL,
					`import` varchar(10) NOT NULL,
					`export` varchar(10) NOT NULL,
					PRIMARY KEY (`modulebuilderid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_blocks` (
					`blocksid` int(20) NOT NULL AUTO_INCREMENT,
					`blocks_label` varchar(100) NOT NULL,
					`moduleid` int(20) NOT NULL,
					PRIMARY KEY (`blocksid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_customview` (
					`customviewid` int(20) NOT NULL AUTO_INCREMENT,
					`viewname` varchar(100) NOT NULL,
					`setdefault` varchar(10) NOT NULL DEFAULT "false",
					`setmetrics` varchar(10) NOT NULL DEFAULT "false",
					`fields` text NOT NULL,
					`moduleid` int(20) NOT NULL,
					PRIMARY KEY (`customviewid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_fields` (
					`fieldsid` int(20) NOT NULL AUTO_INCREMENT,
					`blockid` int(20) NOT NULL,
					`moduleid` int(20) NOT NULL,
					`fieldname` varchar(100) NOT NULL,
					`uitype` int(11) NOT NULL,
					`columnname` varchar(100) NOT NULL,
					`tablename` varchar(100) NOT NULL,
					`fieldlabel` varchar(100) NOT NULL,
					`presence` int(11) NOT NULL,
					`sequence` int(11) NOT NULL,
					`typeofdata` varchar(100) NOT NULL,
					`quickcreate` int(11) NOT NULL,
					`displaytype` int(11) NOT NULL,
					`masseditable` int(11) NOT NULL,
					`entityidentifier` varchar(10) NOT NULL DEFAULT "no",
					`relatedmodules` varchar(100) NOT NULL,
					`picklistvalues` text NOT NULL,
					`fieldlength` varchar(5) NOT NULL,
					`generatedtype` varchar(1) NOT NULL,
					PRIMARY KEY (`fieldsid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->ExecuteQuery(
				'CREATE TABLE `vtiger_modulebuilder_relatedlists` (
					`relatedlistid` int(20) NOT NULL AUTO_INCREMENT,
					`function` varchar(100) NOT NULL,
					`label` varchar(100) NOT NULL,
					`actions` text NOT NULL,
					`relatedmodule` varchar(100) NOT NULL,
					`moduleid` int(20) NOT NULL,
					PRIMARY KEY (`relatedlistid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
			);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}