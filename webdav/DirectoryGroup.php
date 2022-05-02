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

class DirectoryGroup extends Sabre\DAV\Collection {
	private $module;
	private $mode;

	public function __construct($module = false, $mode = 'default') {
		$this->module = $module;
		$this->mode = $mode;
	}

	private function getCode($string) {
		preg_match('/(.*)\[(.*)-(.*)-?(.*)?\]/', $string, $treffer);
		return array(isset($treffer[2]) ? $treffer[2] : '0', isset($treffer[3]) ? $treffer[3] : 0);
	}

	public function getModuleFolder() {
		global $adb;
		$mods = array();
		$sql = 'SELECT vtiger_tab.tabid,vtiger_tab.name,vtiger_tab.tablabel
			FROM vtiger_relatedlists
			LEFT JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_relatedlists.tabid
			WHERE related_tabid=8 and isentitytype=1';
		$records = $adb->query($sql);
		while ($row = $adb->fetch_array($records)) {
			$mods[] = new DirectoryGroup(getTranslatedString($row['tablabel'], $row['name']).' [M-'.$row['tabid'].']');
		}
		return $mods;
	}

	public function getChildren() {
		if ($this->module === false) {
			return array(new DirectoryGroup(getTranslatedString('LBL_MODULE').' [A-0]'), new DirectoryGroup(getTranslatedString('Documents', 'Documents').' [B-0]'));
		}
		$code = $this->getCode($this->module);
		switch ($code[0]) {
			case 'A':
				return $this->getModuleFolder();
				break;
			case 'B':
				return $this->getDocumentFolder();
				break;
			case 'M':
				return $this->getModuleFolders($code[1], isset($code[2]) ? $code[2] : false);
				break;
			default:
				break;
		}
	}

	public function getDocumentFolder() {
		global $adb;
		$folder = array();
		$records = $adb->query('select * from vtiger_documentfolders inner join vtiger_crmentity on crmid=documentfoldersid where deleted=0 and parentfolder=0 order by sequence');
		while ($row = $adb->fetch_array($records)) {
			$folder[] = new DirectoryFolder($row['foldername'], $row['documentfoldersid']);
		}
		return $folder;
	}

	public function createDirectory($name) {
		include_once 'modules/DocumentFolders/DocumentFolders.php';
		if (!DocumentFolders::createFolder($name)) {
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to create Subdrectories');
		}
	}

	public function setName($name) {
	}

	public function getChild($name) {
		global $adb;

		$code = $this->getCode($name);

		switch ($code[0]) {
			case 'A': // Module overview
				return new DirectoryGroup($name, 'module');
				break;
			case 'M': // Module overview
				return new DirectoryModule($code[1]);
				break;
			case 'B': // Document overview
				return new DirectoryGroup($name, 'folder');
				break;
			case 'D': // Document overview
				return new DirectoryGroup($name, $this->module);
				break;
			default:
				break;
		}
		if (!empty($this->mode) && $this->mode == 'folder') {
			$result = $adb->pquery('SELECT documentfoldersid, foldername FROM vtiger_documentfolders INNER JOIN vtiger_crmentity ON crmid=documentfoldersid WHERE foldername=? AND deleted=0', array($name));
			if ($adb->num_rows($result) > 0) {
				$row = $adb->fetch_array($result);
				return new DirectoryFolder($row['foldername'], $row['documentfoldersid']);
			} else {
				throw new Sabre\DAV\Exception\NotFound('Folder not found: ' . $name);
			}
		}
		if (!empty($this->folderid)) {
			return $this->getFile($this->folderid, $name);
		}
		return false;
	}

	public function createFile($name, $data = null) {
		// as
	}

	public function childExists($name) {
		if ($this->mode == 'folder') {
			global $adb;
			$records = $adb->pquery('select 1 from vtiger_documentfolders innter join vtiger_crmentity on crmid=documentfoldersid WHERE deleted=0 and foldername=?'. array($name));
			return ($adb->num_rows($records) > 0);
		}
		return true;
	}

	public function getData() {
		return array('module' => $this->module, 'mode' => $this->mode, 'filetype' => 'node/group');
	}

	public function getName() {
		return $this->module;
	}
}
