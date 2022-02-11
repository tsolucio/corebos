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
 *************************************************************************************************
 *  Module       : WEBDAV
 *************************************************************************************************/

class DirectoryModuleList extends Sabre\DAV\Collection {
	private $tabid;
	private $module;

	public function __construct($tabid) {
		$this->tabid = $tabid;
	}

	private function getModule() {
		if (!empty($this->module)) {
			return $this->module;
		}
		$this->module = getTabModuleName($this->tabid);
		return $this->module;
	}

	public function getChildren() {
		global $adb, $current_user;
		$queryGenerator = new QueryGenerator($this->getModule(), $current_user);
		$queryGenerator->initForDefaultCustomView();
		$queryGenerator->setFields(DirectoryModule::getQueryFields($this->getModule()));
		$query = $queryGenerator->getQuery();
		$records = $adb->query($query. ' limit 100');
		$folder = array();
		while ($row = $adb->fetch_array($records)) {
			$folder[] = new DirectoryRecord($this->tabid, DirectoryModule::getFolderName($this->getModule(), $row));
		}
		return $folder;
	}

	public function childExists($name) {
		return true;
	}

	public function getData() {
		return array('name' => $this->getModule(), 'letter' => '', 'filetype' => 'node/directory');
	}

	public function getName() {
		return $this->getModule();
	}
}
