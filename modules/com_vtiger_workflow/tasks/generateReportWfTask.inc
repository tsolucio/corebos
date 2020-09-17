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
	public $caseType = '';
	public $reportName = '';
	public $questionName = '';
	public $reportFileType = '';
	public function getFieldNames() {

		return array('case_type', 'file_type', 'report_name', 'question_name');
	}

	public function after_retrieve() {
		global $adb, $current_user, $log;
		if (!empty($this->case_type)) {
			$this->caseType = $this->case_type;
		}
		if (!empty($this->report_name)) {
			$this->reportName = $this->report_name;
			if (!empty($this->file_type)) {
				$this->reportFileType = $this->file_type;
			}
		}
		if (!empty($this->question_name)) {
			$this->questionName = $this->question_name;
		}
	}

	public function doTask(&$entity) {
		require_once 'modules/Reports/Reports.php';
		require_once 'modules/Reports/ReportRun.php';
		require_once 'modules/cbQuestion/cbQuestion.php';
		global $adb, $current_user, $root_directory, $log;
		$path = '';
		$name = '';
		if ($this->case_type == 'report') {
			$reportdata = explode("$$", $this->report_name);
			$reportId = (int)$reportdata[0];
			$reportName = $reportdata[1];
			$now = date('YmdHis');
			$oReportRun = new ReportRun($reportId);
			$baseFileName = utf8_decode(preg_replace('/[^a-zA-Z0-9_\.\%\s]/', '', $reportName));
			$path = 'cache/';
			if (strpos($baseFileName, '%s')===false) {
				$baseFileName .= '_%s';
			} else {
				$spos = strpos($baseFileName, '%s');
				$baseFileName = substr($baseFileName, 0, $spos+2).str_replace('%s', '', substr($baseFileName, $spos+2));
			}
			$baseFileName = sprintf($baseFileName, $now);
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
			}
		}
		if ($this->case_type == 'question') {
			$questiondata = explode("$$", $this->question_name);
			$questionId = (int)$questiondata[0];
			$questionName = $questiondata[1];
			$res = cbQuestion::getFormattedAnswer((int)$questionId, array());
			if ($res) {
				$pos = strrpos($res, '/');
				$name = $pos === false ? $res : substr($res, $pos + 1);
				$path =str_replace($name, '', str_replace($root_directory, '', $res));
				$name = strrpos($name, '.') === false ? $name.'.csv' : $name;
			}
		}
		$entity->WorkflowContext['path'] = $path;
		$entity->WorkflowContext['filename'] = $name;
	}
}
?>