<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';

class generateReportWfTask extends VTTask {

	public $executeImmediately = true;
	public function getFieldNames() {
		return array('case_type', 'file_type', 'report_name', 'qnfield', 'qnfield_display', 'generate_filename', 'saveto_doc', 'doc_content');
	}

	public function doTask(&$entity) {
		require_once 'modules/Reports/Reports.php';
		require_once 'modules/Reports/ReportRun.php';
		require_once 'modules/cbQuestion/cbQuestion.php';
		global $adb, $current_user, $root_directory, $log;
		$path = '';
		$name = '';
		$type = '';
		if ($this->case_type == 'report') {
			$reportdata = explode("$$", $this->report_name);
			$reportId = (int)$reportdata[0];
			$baseFileName = '';
			if ($this->generate_filename == '1' && !empty($entity->WorkflowContext['GenerateReportName'])) {
				$baseFileName = $entity->WorkflowContext['GenerateReportName'];
			} else {
				$reportName = $reportdata[1];
				$now = date('YmdHis');
				$baseFileName = utf8_decode(preg_replace('/[^a-zA-Z0-9_\.\%\s]/', '', $reportName));
				if (strpos($baseFileName, '%s')===false) {
					$baseFileName .= '_%s';
				} else {
					$spos = strpos($baseFileName, '%s');
					$baseFileName = substr($baseFileName, 0, $spos+2).str_replace('%s', '', substr($baseFileName, $spos+2));
				}
				$baseFileName = sprintf($baseFileName, $now);
			}
			$path = 'cache/';
			$oReportRun = new ReportRun($reportId);
			$type = $this->file_type;
			if ($this->file_type == 'pdf') {
				$fileName = $path.$baseFileName.'.pdf';
				$filePath = $root_directory.$fileName;
				$pdf = $oReportRun->getReportPDF(null);
				$pdf->Output($filePath, 'F');
				$name = $baseFileName.'.pdf';
			}
			if ($this->file_type == 'csv') {
				$fileName = $path.$baseFileName.'.csv';
				$filePath = $root_directory.$fileName;
				$oReportRun->writeReportToCSVFile($filePath, null);
				$name = $baseFileName.'.csv';
			}
			if ($this->file_type  == 'excel') {
				$fileName = $path.$baseFileName.'.xls';
				$filePath = $root_directory.$fileName;
				$oReportRun->writeReportToExcelFile($filePath, null);
				$name = $baseFileName.'.xls';
				$type = 'xls';
			}
		}
		if ($this->case_type == 'question') {
			$res = cbQuestion::getFormattedAnswer((int)$this->qnfield, array());
			if ($res) {
				$path_parts = pathinfo($res);
				$name = $path_parts['basename'];
				$path =str_replace($root_directory, '', $path_parts['dirname']).'/';
				$type = 'csv';
			}
		}
		$entity->WorkflowContext['wfgenerated_file'] = array("path"=>$path, "name"=>$name);
		if ($this->saveto_doc == '1') {
			$res = $adb->pquery('SELECT folderid FROM vtiger_attachmentsfolder Limit 1', array());
			$folderid = ($res && $adb->num_rows($res) > 0) ? $adb->query_result($res, 0, 'folderid') : '';
			$filesize = filesize($root_directory.$path.$name);
			$dataArr = array(
				'assigned_user_id'=>vtws_getEntityId('Users').'x'.$current_user->id,
				'notes_title' => $name,
				'filename'=>$name,
				'filetype'=>$type,
				'filesize'=>$filesize,
				'filelocationtype'=>'I',
				'filedownloadcount'=> 0,
				'filestatus'=>1,
				'template'=>0,
				'mergetemplate'=>0,
				'description'=>$this->doc_content,
				'notecontent' => $this->doc_content,
				'folderid' => vtws_getEntityId('DocumentFolders').'x'.$folderid,
			);
			$this->createDocumentfromGeneratedReport($dataArr, $current_user);
		}
	}

	public function createDocumentfromGeneratedReport($dataArr, $user) {
		include_once 'include/Webservices/Create.php';
		try {
			$res = vtws_create('Documents', $dataArr, $user);
		} catch (Exception $e) {
		}
		return;
	}
}
?>