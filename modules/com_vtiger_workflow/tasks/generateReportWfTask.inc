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
require_once 'modules/Reports/Reports.php';
require_once 'modules/Reports/ReportRun.php';
require_once 'modules/cbQuestion/cbQuestion.php';

class generateReportWfTask extends VTTask {

	public $executeImmediately = true;
	public function getFieldNames() {
		return array('case_type', 'file_type', 'report_name', 'qnfield', 'qnfield_display');
	}

	public function doTask(&$entity) {
		global $adb, $root_directory;
		$path = '';
		$name = '';
		if ($this->case_type == 'report') {
			$reportdata = explode('$$', $this->report_name);
			$reportId = (int)$reportdata[0];
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
			$path = 'cache/';
			$oReportRun = new ReportRun($reportId);
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
			$res = cbQuestion::getFormattedAnswer((int)$this->qnfield, array());
			if ($res) {
				$path_parts = pathinfo($res);
				$name = $path_parts['basename'];
				$path =str_replace($root_directory, '', $path_parts['dirname']).'/';
			}
		}
		$entity->WorkflowContext['wfgenerated_file'] = array("path"=>$path, "name"=>$name);
	}
}
?>