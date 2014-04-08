<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Import/resources/Utils.php';

class Import_File_Reader {

	var $status='success';
	var $numberOfRecordsRead = 0;
	var $errorMessage='';
	var $user;
	var $userInputObject;

	public function  __construct($userInputObject, $user) {
		$this->userInputObject = $userInputObject;
		$this->user = $user;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function getNumberOfRecordsRead() {
		return $this->numberOfRecordsRead;
	}

	public function hasHeader() {
		if($this->userInputObject->get('has_header') == 'on'
				|| $this->userInputObject->get('has_header') == 1
				|| $this->userInputObject->get('has_header') == true) {
			return true;
		}
		return false;
	}

	public function getFirstRowData($hasHeader=true) {
		return null;
	}

	public function getFilePath() {
		return Import_Utils::getImportFilePath($this->user);
	}

	public function getFileHandler() {
		$filePath = $this->getFilePath();
		if(!file_exists($filePath)) {
			$this->status = 'failed';
			$this->errorMessage = "ERR_FILE_DOESNT_EXIST";
			return false;
		}

		$fileHandler = fopen($filePath, 'r');
		if(!$fileHandler) {
			$this->status = 'failed';
			$this->errorMessage = "ERR_CANT_OPEN_FILE";
			return false;
		}
		return $fileHandler;
	}

	public function convertCharacterEncoding($value, $fromCharset, $toCharset) {
		if (function_exists("mb_convert_encoding")) {
			$value = mb_convert_encoding($value, $toCharset, $fromCharset);
		} else {
			$value = iconv($toCharset, $fromCharset, $value);
		}
		return $value;
	}

	public function read() {
		// Sub-class need to implement this
	}

	public function deleteFile() {
		$filePath = $this->getFilePath();
		@unlink($filePath);
	}

	public function createTable() {
		$adb = PearDatabase::getInstance();

		$tableName = Import_Utils::getDbTableName($this->user);
		$fieldMapping = $this->userInputObject->get('field_mapping');

		$columnsListQuery = 'id INT PRIMARY KEY AUTO_INCREMENT, status INT DEFAULT 0, recordid INT';
		foreach($fieldMapping as $fieldName => $index) {
			$columnsListQuery .= ','.$fieldName.' TEXT';
		}
		$createTableQuery = 'CREATE TABLE '. $tableName . ' ('.$columnsListQuery.')';
		$adb->query($createTableQuery);
		return true;
	}

	public function addRecordToDB($columnNames, $fieldValues) {
		$adb = PearDatabase::getInstance();

		$tableName = Import_Utils::getDbTableName($this->user);
		$adb->pquery('INSERT INTO '.$tableName.' ('. implode(',', $columnNames).') VALUES ('. generateQuestionMarks($fieldValues) .')', $fieldValues);
		$this->numberOfRecordsRead++;
	}
}
?>
