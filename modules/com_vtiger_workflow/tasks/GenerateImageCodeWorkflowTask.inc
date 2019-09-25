<?php
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/SMSNotifier/SMSNotifier.php';
include 'vtlib/Vtiger/module.php';

class GenerateImageCodeWorkflowTask extends VTTask {
	public $taskSavedData = array();
	public $executeImmediately = true;
	public $tcpdf1dEncodingTypes = array('EAN8' => 'EAN8','EAN13' => 'EAN13');

	public $tcpdf2dEncodingTypes = array('QRCODE,L' => 'QRCODE,L','QRCODE,M' => 'QRCODE M','QRCODE,Q' => 'QRCODE,Q','QRCODE,H' => 'QRCODE,H');

	public function getFieldNames() {
		return array('save_encoded_value', 'field_to_encode', 'encoding_type');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $from_wf, $logbg, $root_directory;
		$from_wf = true;
		$util = new VTWorkflowUtils();
		$fieldValueToEncode = $entity->data[$this->field_to_encode];
		$util->adminUser();
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$barcodeobj = null;

		require_once 'include/Webservices/Revise.php';
		require_once 'include/tcpdf/tcpdf_barcodes_2d.php';
		require_once 'include/tcpdf/tcpdf_barcodes_1d.php';

		if (array_key_exists($this->encoding_type, $this->tcpdf1dEncodingTypes)) {
			$barcodeobj = new TCPDFBarcode(
				$fieldValueToEncode,
				$this->encoding_type
			);
		} elseif (array_key_exists($this->encoding_type, $this->tcpdf2dEncodingTypes)) {
			$barcodeobj = new TCPDF2DBarcode(
				$fieldValueToEncode,
				$this->encoding_type
			);
		}

		$data = $barcodeobj->getBarcodePngData(3, 3, array(0,128,0));

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

	public function after_retrieve() {
		global $adb, $current_user;
		$this->taskSavedData = array(
			'save_encoded_value' => $this->save_encoded_value,
			'field_to_encode' => $this->field_to_encode,
			'encoding_type' => $this->encoding_type );
	}
}
?>