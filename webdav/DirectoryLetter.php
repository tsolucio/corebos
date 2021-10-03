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

class DirectoryLetter extends Sabre\DAV\Collection {
	private $tabid;
	private $module;
	private $letter;
	private $letterLength = 2;

	public function __construct($tabid, $letter) {
		$this->tabid = $tabid;
		$this->letter = $letter;
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
		if (strlen($this->letter) < $this->letterLength) {
			$letters = DirectoryModule::getLetterArray($this->getModule(), empty($this->letter) ? '' : $this->letter);
			$dirs = array();
			foreach ($letters as $letter) {
				$dirs[] = new DirectoryLetter($this->tabid, $letter);
			}
			return $dirs;
		}

		$queryGenerator = new QueryGenerator($this->getModule(), $current_user);
		$queryGenerator->initForDefaultCustomView();
		$queryGenerator->setFields(DirectoryModule::getQueryFields($this->getModule()));
		$queryGenerator->addUserSearchConditions(DirectoryModule::getSearchArray($this->getModule(), $this->letter));
		$query = $queryGenerator->getQuery();
		$records = $adb->query($query);
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
		return array('name' => $this->letter, 'letter' => $this->letter, 'filetype' => 'node/letter');
	}

	public function getName() {
		return strtoupper(html_entity_decode($this->letter));
	}
}
