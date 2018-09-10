<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/../api/ws/Controller.php';

class crmtogo_UI_DownLoadFile extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		$record = $request->get('record');
		$response = new crmtogo_API_Response();
		$operation = $request->getOperation();
		if ($operation == 'downloadFile') {
			$this->updateDownloadCount($record);
			$response->setResult($this->downloadFile($record));
		} else {
			$response->setError(8001, 'Wrong function call for file download');
		}
		return $response;
	}

	public function downloadFile($recordid) {
		$fileDetails = $this->getFileDetails($recordid);
		$fileContent = false;

		if (!empty($fileDetails)) {
			$filePath = $fileDetails['path'];
			$fileName = $fileDetails['name'];

			$fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8');
			$savedFile = $fileDetails['attachmentsid'].'_'.$fileName;

			$fileSize = filesize($filePath.$savedFile);
			$fileSize = $fileSize + ($fileSize % 1024);

			if (fopen($filePath.$savedFile, 'r')) {
				$fileContent = fread(fopen($filePath.$savedFile, 'r'), $fileSize);
				header('Pragma: no-cache');
				header('Content-Type: '.$fileDetails['type']);
				header('Content-Disposition: attachment;filename="' . $fileName . '"');
			}
		}
		echo $fileContent;
	}

	public function getFileDetails($recordid) {
		$file ['id'] = $recordid;
		$fieleinfos = crmtogo_WS_Utils::getDetailedDocumentInformation($file);
		$filedetails['path'] = $fieleinfos['attachmentinfo']['path'];
		$filedetails['type'] = $fieleinfos['filetype'];
		$filedetails['name'] = $fieleinfos['filename'];
		$filedetails['attachmentsid'] = $fieleinfos['attachmentinfo']['attachmentsid'];
		return $filedetails;
	}

	public function updateDownloadCount($documentid) {
		$db = PearDatabase::getInstance();
		$documentid = explode('x', $documentid);
		$notesId = $documentid[1];
		$result = $db->pquery('SELECT filedownloadcount FROM vtiger_notes WHERE notesid = ?', array($notesId));
		$downloadCount = $db->query_result($result, 0, 'filedownloadcount') + 1;
		$db->pquery('UPDATE vtiger_notes SET filedownloadcount = ? WHERE notesid = ?', array($downloadCount, $notesId));
	}
}
?>