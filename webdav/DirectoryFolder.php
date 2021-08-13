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
		$sql = "select vtiger_senotesrel.crmid,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,
			'Documents' ActivityType,vtiger_attachments.name as file_name, vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,
			vtiger_seattachmentsrel.attachmentsid attachmentsid, vtiger_notes.notesid crmid, vtiger_notes.notecontent description,vtiger_notes.*
			from vtiger_notes
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
			left join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
			left join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.crmid
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
			left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
			left join vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid";
		$instance = CRMEntity::getInstance('Documents');
		$sql .= $instance->getNonAdminAccessControlQuery('Documents', $current_user);
		return $sql;
	}

	public function getChildren() {
		global $adb;
		$query = $this->getDocumentQuery().' where vtiger_notes.folderid=?';
		$records = $adb->pquery($query, array($this->folderid));
		while ($row = $adb->fetch_array($records)) {
			if ($row['filelocationtype'] == 'I') {
				$folder[] = new CRMFile($row, $this->folderid);
			}
		}
		if (empty($folder)) {
			return array();
		}
		return $folder;
	}

	public function createDirectory($name) {
		include_once 'modules/Documents/Documents.php';
		if (!Documents::createFolder(htmlentities($name))) {
			throw new Sabre\DAV\Exception\Forbidden('Permission denied to create Subdrectories');
		}
	}

	public function getChild($filename) {
		global $adb;
		$filename = html_entity_decode(html_entity_decode($filename));
		$query = $this->getDocumentQuery().' where vtiger_notes.folderid=? AND (filename=? OR vtiger_attachments.name=?)';
		$records = $adb->pquery($query, array($this->folderid, $filename, $filename));
		if ($adb->num_rows($records) == 0) {
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
			$adb->pquery('UPDATE vtiger_attachmentsfolder SET foldername=? WHERE folderid=?', array($name, intval($this->folderid)));
			return true;
		}
		throw new Sabre\DAV\Exception\Forbidden('Permission denied to rename default directory');
	}

	public function createFile($name, $data = null) {
		CRMFile::create($name, $this->folderid, $data);
	}

	public function getFolderId() {
		return $this->folderid;
	}

	public function getData() {
		return array('folderid' => $this->folderid, 'foldername' => $this->foldername, 'filetype' => 'node/directory');
	}

	public function getName() {
		return html_entity_decode($this->foldername, ENT_COMPAT, 'UTF-8');
	}
}
