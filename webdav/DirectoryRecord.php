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

class DirectoryRecord extends Sabre\DAV\Collection {
	private $tabid;
	private $module;
	private $id;

	private function getID($string) {
		preg_match("/(.*)\[([0-9]*)\]/", $string, $treffer);
		return intval($treffer[2]);
	}

	public function getCRMID() {
		return $this->id;
	}

	public function __construct($tabid, $foldername) {
		$this->tabid = $tabid;
		$this->module = getTabModuleName($this->tabid);
		$this->id = $this->getId($foldername);
		$this->foldername = $foldername;
	}

	private function getDocumentQuery() {
		global $current_user;
		$sql = "select case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,'Documents' ActivityType,"
			.'vtiger_attachments.name as file_name,vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,vtiger_seattachmentsrel.attachmentsid attachmentsid,
			vtiger_notes.notesid crmid,vtiger_notes.notecontent description,vtiger_notes.*
			from vtiger_notes
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid and vtiger_crmentity.deleted=0
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid=vtiger_notes.notesid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_senotesrel.crmid
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid
			left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
			left join vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid';
		$instance = CRMEntity::getInstance('Documents');
		$sql .= $instance->getNonAdminAccessControlQuery('Documents', $current_user);
		return $sql;
	}

	public function getChildren() {
		global $adb;
		$sql = $this->getDocumentQuery().' where crm2.crmid='.$this->id;
		$result = $adb->query($sql);
		$files = array();
		while ($data = $adb->fetch_array($result)) {
			if ($data['filelocationtype'] == 'I') {
				$files[] = new CRMFile($data, 1);
			}
		}
		return $files;
	}

	public function getChild($name) {
		global $adb;
		$sql = $this->getDocumentQuery().' where vtiger_notes.filename=? AND crm2.crmid=?';
		$result = $adb->pquery($sql, array($name, $this->id));
		if ($adb->num_rows($result) == 0) {
			throw new Sabre\DAV\Exception\NotFound('File not found: '.$name);
		}
		return new CRMFile($adb->fetch_array($result), 1, $this->id, false);
	}

	public function childExists($name) {
		try {
			$this->getChild($name);
			return true;
		} catch (Sabre\DAV\Exception\NotFound $e) {
			return false;
		}
	}

	public function createFile($name, $data = null) {
		CRMFile::create($name, 1, $data, $this->id);
	}

	public function getData() {
		return array('foldername' => $this->foldername, 'id' => $this->id, 'filetype' => 'node/record');
	}

	public function getName() {
		return utf8_encode(html_entity_decode($this->foldername));
	}
}
