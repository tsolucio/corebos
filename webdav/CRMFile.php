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

class CRMFile extends Sabre\DAV\File {

	private $data;
	private $recordid;
	private $filepath;
	private $putAction = 'overwrite'; // overwrite | update

	public function __construct($data, $folderid, $recordid = false, $create = false) {
		global $adb;
		$this->data = $data;
		if (!empty($this->data['file_name'])) {
			$this->data['filename'] = $this->data['file_name'];
		}
		$this->recordid = $recordid;
		if (empty($this->data['folderid'])) {
			$this->data['folderid'] = $folderid;
		}
		$this->data['filename'] = html_entity_decode(html_entity_decode($this->data['filename']));
		$result = $adb->pquery('SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?', array($this->data['notesid']));
		$fileid = $adb->query_result($result, 0, 'attachmentsid');
		$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid=?', array($fileid));
		$filepath = '../'.$adb->query_result($pathQuery, 0, 'path');
		$saved_filename = $fileid.'_'.utf8_encode(html_entity_decode(html_entity_decode($this->data['filename'])));
		$this->filepath = realpath(dirname(__FILE__).'/'.$filepath.$saved_filename);
	}

	public function getName() {
		return utf8_encode(html_entity_decode(html_entity_decode($this->data['filename'], ENT_COMPAT, 'UTF-8')));
	}

	public function setName($name) {
		global $adb;
		$name = self::rewriteFilename($name);
		$adb->pquery('UPDATE vtiger_notes SET filename=?, title=? WHERE notesid=?', array($name, $name, $this->data['notesid']));
		$result = $adb->pquery('SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?', array($this->data['notesid']));
		$fileid = $adb->query_result($result, 0, 'attachmentsid');
		$adb->pquery('UPDATE vtiger_attachments SET name=? WHERE attachmentsid=?', array($name, $fileid));
		$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid=?', array($fileid));
		$filepath = '../'.$adb->query_result($pathQuery, 0, 'path');
		$saved_filename = $fileid.'_'.utf8_encode(html_entity_decode(html_entity_decode($this->data['filename'])));
		$new_saved_filename = $fileid.'_'.utf8_encode(html_entity_decode(html_entity_decode($name)));
		$path = realpath(dirname(__FILE__).'/'.$filepath.$saved_filename);
		$new_path = (dirname(__FILE__).'/'.$filepath.$new_saved_filename);
		rename($path, $new_path);
	}

	public function delete() {
		$document = new Documents();
		$document->trash('Documents', $this->data['notesid']);
	}

	public static function rewriteFilename($filename) {
		return preg_replace_callback(
			"(\\\x[0-9A-F]{2})",
			function ($coincidencias) {
				return chr(hexdec($coincidencias[0]));
			},
			$filename
		);
		//return preg_replace("#(\\\x[0-9A-F]{2})#e", "chr(hexdec('\\1'))", $filename);
		//return strtr($filename, ["\xC4"=>"�","\xC6"=>"�","\xD6"=>"�","\xDC"=>"�","\xDE"=>"�","\xDF"=>"ss","\xE4"=>"�","\xE6"=>"�","\xF6"=>"�","\xFC"=>"�","\xFE"=>"�"]);
	}

	public static function create($filename, $folderid, $data, $recordid = false) {
		global $log, $current_user, $upload_badext;
		$filename = self::rewriteFilename($filename);
		$tmpFile = tempnam('/tmp', 'WEBDAV');
		if (is_resource($data)) {
			rewind($data);
		}
		$filesize = file_put_contents($tmpFile, $data);
		if ($filesize==0) {
			return true;
		}
		$log->debug('CREATE- '. $filename.' - '.$filesize.' Byte');
		$finfo = finfo_open(FILEINFO_MIME);
		unset($_FILES);
		$f=array(
			'name'=>sanitizeUploadFileName($filename, $upload_badext),
			'type'=>finfo_file($finfo, $tmpFile),
			'tmp_name'=>$tmpFile,
			'error'=>0,
			'size'=>$filesize,
		);
		$_FILES['file0'] = $f;
		$document = new Documents();
		$document->column_fields['notes_title'] = $filename;
		$document->column_fields['filename'] = sanitizeUploadFileName($filename, $upload_badext);
		$document->column_fields['filesize'] = empty($filesize) ? 1 : $filesize;
		$document->column_fields['filetype'] = $f['type'];
		$document->column_fields['filestatus'] = 1;
		$document->column_fields['filelocationtype'] = 'I';
		$document->column_fields['fileversion'] = 1;
		$document->column_fields['folderid'] = $folderid;
		$document->column_fields['assigned_user_id'] = $current_user->id;
		if (!empty($recordid)) {
			$document->parentid = $recordid;
		}
		$document->save('Documents');
		$document->save_related_module('Documents', $document->id, 'DocumentFolders', $folderid);
		unset($_FILES);
	}

	public function put($data) {
		global $log, $upload_badext;
		$tmpFile = tempnam('/tmp', 'WEBDAV');
		if (is_resource($data)) {
			rewind($data);
		}
		$filesize = file_put_contents($tmpFile, $data);
		if ($filesize==0) {
			return true;
		}
		$filename = $this->data['filename'];
		$log->debug('PUT - '.$filesize.' Byte ID '.$this->data['notesid']);

		$finfo = finfo_open(FILEINFO_MIME);
		$ftype = finfo_file($finfo, $tmpFile);
		$document = new Documents();
		$document->id = $this->data['notesid'];
		$document->mode = 'edit';
		$document->retrieve_entity_info($this->data['notesid'], 'Documents');
		$moduleHandlerrel = vtws_getModuleHandlerFromName('Documents', Users::getActiveAdminUser());
		$handlerMetarel = $moduleHandlerrel->getMeta();
		$document->column_fields = DataTransform::sanitizeRetrieveEntityInfo($document->column_fields, $handlerMetarel);
		$document->column_fields['filename'] = sanitizeUploadFileName($filename, $upload_badext);
		$document->column_fields['filesize'] = empty($filesize) ? 0 : $filesize;
		$document->column_fields['filetype'] = $ftype;
		$document->column_fields['filestatus'] = 1;
		$document->column_fields['filelocationtype'] = 'I';
		$document->column_fields['fileversion'] = $document->column_fields['fileversion']+1;
		unset($_FILES);
		if ($this->putAction=='update') {
			$_FILES['file0'] = array(
				'name'=>sanitizeUploadFileName($filename, $upload_badext),
				'type'=>$ftype,
				'tmp_name'=>$tmpFile,
				'error'=>0,
				'size'=>$filesize,
			);
		} elseif ($this->putAction=='overwrite') {
			@rename($tmpFile, $this->filepath);
		}
		$document->save('Documents');
		unset($_FILES);
		return true;
	}

	public function get() {
		global $log;
		if (empty($this->filepath)) {
			return false;
		}
		$file = fopen($this->filepath, 'r');
		if ($file) {
			return $file;
		} else {
			$log->debug('WEBDAV: unable to open file: '.$this->filepath);
		}
		return false;
	}

	public function getSize() {
		return filesize($this->filepath);
	}

	public function getContentType() {
		if (empty($this->filepath)) {
			return '';
		}
		$finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
		return finfo_file($finfo, $this->filepath);
	}

	public function getETag() {
		global $adb;
		$result = $adb->pquery('SELECT modifiedtime FROM vtiger_crmentity WHERE crmid=?', array($this->data['notesid']));
		$row = $adb->fetch_array($result);
		return '"' . md5($row['modifiedtime']) . '"';
	}

	public function getData() {
		return $this->data;
	}

	public function getLastModified() {
		global $adb;
		$result = $adb->pquery('SELECT modifiedtime FROM vtiger_crmentity WHERE crmid=?', array($this->data['notesid']));
		$row = $adb->fetch_array($result);
		return strtotime($row['modifiedtime']);
	}

	public function copyTo($directory) {
		global $adb, $log;

		if (!$directory instanceof Sabre\DAV\Collection) {
			$log->debug('WEBDAV: NO CORRECT DESTINATION');
			return false;
		}

		if ($directory instanceof DirectoryRecord) {
			$adb->pquery('INSERT INTO vtiger_senotesrel VALUES(?,?)', array($directory->getCRMID(), $this->data['notesid']));
		}

		if ($directory instanceof DirectoryFolder) {
			$result = $adb->pquery('SELECT attachmentsid FROM vtiger_seattachmentsrel WHERE crmid=?', array($this->data['notesid']));
			$fileid = $adb->query_result($result, 0, 'attachmentsid');

			$pathQuery = $adb->pquery('select path from vtiger_attachments where attachmentsid=?', array($fileid));
			$fpath = '../'.$adb->query_result($pathQuery, 0, 'path');

			$saved_filename = $fileid.'_'.$this->data['filename'];
			$path = realpath(dirname(__FILE__).'/'.$fpath.$saved_filename);

			$directory->createFile(html_entity_decode(html_entity_decode($this->data['filename'])), file_get_contents($path));
		}
	}

	public function moveTo($directory) {
		global $adb, $log;

		if (!$directory instanceof Sabre\DAV\Collection) {
			$log->debug('WEBDAV: NO CORRECT DESTINATION');
			return false;
		}

		if ($directory instanceof DirectoryFolder) {
			$log->debug('WEBDAV: MOVE TO FOLDER '.$this->data['notesid']);
			$adb->pquery('UPDATE vtiger_notes SET folderid=? WHERE notesid=?', array($directory->getFolderId(), $this->data['notesid']));
		}

		if ($directory instanceof DirectoryRecord) {
			$adb->pquery('DELETE FROM vtiger_senotesrel WHERE notesid=?', array($this->data['notesid']));
			$adb->pquery('INSERT INTO vtiger_senotesrel VALUES(?,?)', array($directory->getCRMID(), $this->data['notesid']));
		}
	}
}
