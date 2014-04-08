<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/Import/readers/FileReader.php';

class Import_CSV_Reader extends Import_File_Reader {

	public function getFirstRowData($hasHeader=true) {
		global $default_charset;

		$fileHandler = $this->getFileHandler();

		$headers = array();
		$firstRowData = array();
		$currentRow = 0;
		while($data = fgetcsv($fileHandler, 0, $this->userInputObject->get('delimiter'))) {
			if($currentRow == 0 || ($currentRow == 1 && $hasHeader)) {
				if($hasHeader && $currentRow == 0) {
					foreach($data as $key => $value) {
						$headers[$key] = $this->convertCharacterEncoding($value, $this->userInputObject->get('file_encoding'), $default_charset);
					}
				} else {
					foreach($data as $key => $value) {
						$firstRowData[$key] = $this->convertCharacterEncoding($value, $this->userInputObject->get('file_encoding'), $default_charset);
					}
					break;
				}
			}
			$currentRow++;
		}

		if($hasHeader) {
			$noOfHeaders = count($headers);
			$noOfFirstRowData = count($firstRowData);
			// Adjust first row data to get in sync with the number of headers
			if($noOfHeaders > $noOfFirstRowData) {
				$firstRowData = array_merge($firstRowData, array_fill($noOfFirstRowData, $noOfHeaders-$noOfFirstRowData, ''));
			} elseif($noOfHeaders < $noOfFirstRowData) {
				$firstRowData = array_slice($firstRowData, 0, count($headers), true);
			}
			$rowData = array_combine($headers, $firstRowData);
		} else {
			$rowData = $firstRowData;
		}

		unset($fileHandler);
		return $rowData;
	}

	public function read() {
		global $default_charset;

		$fileHandler = $this->getFileHandler();
		$status = $this->createTable();
		if(!$status) {
			return false;
		}

		$fieldMapping = $this->userInputObject->get('field_mapping');

		$i=-1;
		while($data = fgetcsv($fileHandler, 0, $this->userInputObject->get('delimiter'))) {
			$i++;
			if($this->userInputObject->get('has_header') && $i == 0) continue;
			$mappedData = array();
			$allValuesEmpty = true;
			foreach($fieldMapping as $fieldName => $index) {
				$fieldValue = $data[$index];
				$mappedData[$fieldName] = $fieldValue;
				if($this->userInputObject->get('file_encoding') != $default_charset) {
					$mappedData[$fieldName] = $this->convertCharacterEncoding($fieldValue, $this->userInputObject->get('file_encoding'), $default_charset);
				}
				if(!empty($fieldValue)) $allValuesEmpty = false;
			}
			if($allValuesEmpty) continue;
			$fieldNames = array_keys($mappedData);
			$fieldValues = array_values($mappedData);
			$this->addRecordToDB($fieldNames, $fieldValues);
		}
		unset($fileHandler);
	}
}
?>
