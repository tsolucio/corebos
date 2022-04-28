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

class DirectoryFolder extends Sabre\DAV\Collection {
	private $folderid;
	private $foldername;

	public function __construct($name, $folderid) {
		$this->foldername = $name;
		$this->folderid = intval($folderid);
	}

	private function getDocumentQuery() {
		global $current_user;
		$sql = "select distinct vtiger_notes.*, vtiger_crmentity.*, vtiger_notescf.* from vtiger_notes
			inner join vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_notes.notesid
			inner join vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid=vtiger_crmentity.crmid OR vtiger_crmentityrel.crmid=vtiger_crmentity.crmid)
			inner join vtiger_notescf ON vtiger_notescf.notesid = vtiger_notes.notesid
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
			left join vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			left join vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted=0 and (vtiger_crmentityrel.crmid=? OR vtiger_crmentityrel.relcrmid=?) ";
		$instance = CRMEntity::getInstance('Documents');
		$sql .= $instance->getNonAdminAccessControlQuery('Documents', $current_user);
		return $sql;
	}

	public function getChildren() {
		global $adb;
		$query = $this->getDocumentQuery();
		$records = $adb->pquery($query, array($this->folderid, $this->folderid));
		while ($row = $adb->fetch_array($records)) {
			if ($row['filelocationtype'] == 'I') {
				$folder[] = new CRMFile($row, $this->folderid);
			}
		}
		$result = $adb->pquery('SELECT documentfoldersid, foldername FROM vtiger_documentfolders INNER JOIN vtiger_crmentity ON crmid=documentfoldersid WHERE parentfolder=? AND deleted=0', array($this->folderid));
		if ($adb->num_rows($result) > 0) {
			while ($row = $adb->fetch_array($result)) {
				$folder[] = new DirectoryFolder($row['foldername'], $row['documentfoldersid']);
			}
		}
		if (empty($folder)) {
			return array();
		}
		return $folder;
	}

	public function createDirectory($name) {
		include_once 'modules/DocumentFolders/DocumentFolders.php';
		if (!DocumentFolders::createFolder(htmlentities($name), $this->folderid)) {
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to create Subdrectories');
		}
	}

	public function getChild($filename) {
		global $adb;
		$filename = html_entity_decode(html_entity_decode($filename));
		$query = $this->getDocumentQuery().' and (vtiger_notes.filename=? OR vtiger_attachments.name=?)';
		$records = $adb->pquery($query, array($this->folderid, $this->folderid, $filename, $filename));
		if ($adb->num_rows($records) == 0) {
			$result = $adb->pquery('SELECT documentfoldersid, foldername FROM vtiger_documentfolders INNER JOIN vtiger_crmentity ON crmid=documentfoldersid WHERE foldername=? AND parentfolder=?', array($filename, $this->folderid));
			if ($adb->num_rows($result) > 0) {
				$row = $adb->fetch_array($result);
				$this->folderid = $row['documentfoldersid'];
				$this->foldername = $row['foldername'];
				return new DirectoryFolder($row['foldername'], $row['documentfoldersid']);
			}
			throw new Sabre\DAV\Exception\NotFound('File not found: ' . $filename);
		}
		$row = $adb->fetch_array($records);
		return new CRMFile($row, $this->folderid);
	}

	public function childExists($name) {
		try {
			$this->getChild($name);
			return true;
		} catch (Sabre\DAV\Exception\NotFound $e) {
			return false;
		}
	}

	public function setName($name) {
		global $adb;
		if (!empty($this->folderid) && $this->folderid > 1) {
			$adb->pquery('UPDATE vtiger_documentfolders SET foldername=? WHERE documentfoldersid=?', array($name, intval($this->folderid)));
			return true;
		}
		throw new Sabre\DAV\Exception\Forbidden('Permission denied to rename default directory');
	}

	public function delete() {
		global $adb;
		$adb->pquery('update vtiger_crmentity set deleted=1 where crmid=?', array($this->folderid));
	}

	public function createFile($name, $data = null) {
		CRMFile::create($name, $this->folderid, $data);
	}

	public function getFolderId() {
		return $this->folderid;
	}

	public function getData() {
		return array('folderid' => $this->folderid, 'foldername' => $this->foldername, 'filetype' => 'node/directory', 'module' => 'Documents');
	}

	public function getName() {
		return html_entity_decode($this->foldername, ENT_COMPAT, 'UTF-8');
	}
}
