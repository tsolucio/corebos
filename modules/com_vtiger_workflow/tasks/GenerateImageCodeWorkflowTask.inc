<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/SMSNotifier/SMSNotifier.php';

class GenerateImageCodeWorkflowTask extends VTTask {
	public $taskSavedData = array();
	public $executeImmediately = true;
	public $queable = false;
	public $tcpdf1dEncodingTypes = array('EAN8' => 'EAN8','EAN13' => 'EAN13');

	public $tcpdf2dEncodingTypes = array('QRCODE,L' => 'QRCODE,L','QRCODE,M' => 'QRCODE M','QRCODE,Q' => 'QRCODE,Q','QRCODE,H' => 'QRCODE,H');

	public function getFieldNames() {
		return array('save_encoded_value', 'field_to_encode', 'encoding_type');
	}

	public function doTask(&$entity) {
		global $from_wf, $root_directory;
		$from_wf = true;
		$util = new VTWorkflowUtils();
		if (empty($entity->WorkflowContext['GenerateImageCode_ValueToEncode'])) {
			$fieldValueToEncode = $entity->data[$this->field_to_encode];
		} else {
			$fieldValueToEncode = $entity->WorkflowContext['GenerateImageCode_ValueToEncode'];
		}
		if (empty($entity->WorkflowContext['GenerateImageCode_EncodingType'])) {
			$encoding_type = $this->encoding_type;
		} else {
			$encoding_type = $entity->WorkflowContext['GenerateImageCode_EncodingType'];
		}
		$util->adminUser();
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$barcodeobj = null;

		require_once 'include/tcpdf/tcpdf_barcodes_2d.php';
		require_once 'include/tcpdf/tcpdf_barcodes_1d.php';

		if (array_key_exists($encoding_type, $this->tcpdf1dEncodingTypes)) {
			$barcodeobj = new TCPDFBarcode($fieldValueToEncode, $encoding_type);
			$width = 1;
			$height = 20;
		} elseif (array_key_exists($encoding_type, $this->tcpdf2dEncodingTypes)) {
			$barcodeobj = new TCPDF2DBarcode($fieldValueToEncode, $encoding_type);
			$width = 3;
			$height = 3;
		}
		if ($barcodeobj) {
			$data = $barcodeobj->getBarcodePngData($width, $height, array(0,0,0));

			$attachment_name = 'BARCODEname.png';
			$filepath = $root_directory.'cache/'.$attachment_name;
			file_put_contents($filepath, $data);
			$_FILES[$this->save_encoded_value] = array(
				'name' => $attachment_name,
				'type' => 'image/png',
				'tmp_name' => $filepath,
				'error' => 0,
				'size' => strlen($data)
			);

			$moduleInstance = CRMEntity::getInstance($moduleName);
			$moduleInstance->id = $recordId;
			$moduleInstance->mode = 'edit';
			$moduleInstance->DirectImageFieldValues[$this->save_encoded_value] = $attachment_name;
			$moduleInstance->retrieve_entity_info($recordId, $moduleName);
			$moduleInstance->insertIntoAttachment($recordId, $moduleName);
			unlink($filepath);
		}
		$util->revertUser();
	}

	public function after_retrieve() {
		$this->taskSavedData = array(
			'save_encoded_value' => $this->save_encoded_value,
			'field_to_encode' => $this->field_to_encode,
			'encoding_type' => $this->encoding_type );
	}
}
?>