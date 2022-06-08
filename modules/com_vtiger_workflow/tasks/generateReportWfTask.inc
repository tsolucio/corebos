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
require_once 'modules/Reports/Reports.php';
require_once 'modules/Reports/ReportRun.php';
require_once 'modules/cbQuestion/cbQuestion.php';

class generateReportWfTask extends VTTask {
	public $executeImmediately = true;
	public $queable = true;

	public function getFieldNames() {
		return array('case_type', 'file_type', 'report_name', 'qnfield', 'qnfield_display');
	}

	public function doTask(&$entity) {
		global $adb, $logbg, $root_directory;
		$path = '';
		$name = '';
		$hascontent = 1;
		if ($this->case_type == 'report') {
			$reportdata = explode('$$', $this->report_name);
			$reportId = (int)$reportdata[0];
			$reportName = $reportdata[1];
			$baseFileName = '';
			if (!empty($entity->WorkflowContext['GenerateReportName'])) {
				$repname = $entity->WorkflowContext['GenerateReportName'];
				$result = $adb->pquery('SELECT reportid FROM vtiger_report WHERE reportname=? limit 1', array($repname));
				if ($result && $adb->num_rows($result) > 0) {
					$reportId = $adb->query_result($result, 0, 'reportid');
					$reportName = $repname;
				}
			}
			$now = date('YmdHis');
			$baseFileName = utf8_decode(preg_replace('/[^a-zA-Z0-9_\.\%]/', '', $reportName));
			if (strpos($baseFileName, '%s')===false) {
				$baseFileName .= '_%s';
			} else {
				$baseFileName = suppressAllButFirst('%s', $baseFileName);
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
				$oReportRun->writeReportToExcelFile($filePath, null, $entity->getModuleName());
				$name = $baseFileName.'.xls';
			}
			if ($oReportRun->number_of_rows <= 0) {
				$hascontent = 0;
			}
		}
		if ($this->case_type == 'question') {
			if (!empty($entity->WorkflowContext['GenerateQuestionName'])) {
				$qnName = $entity->WorkflowContext['GenerateQuestionName'];
				$res = $adb->pquery('SELECT cbquestionid FROM vtiger_cbquestion WHERE qname=? limit 1', array($qnName));
				if ($res && $adb->num_rows($res) > 0) {
					$this->qnfield = $adb->query_result($res, 0, 'cbquestionid');
				}
			}
			$res = cbQuestion::getFormattedAnswer((int)$this->qnfield, $entity->WorkflowContext);
			if ($res) {
				$lines = file($res);
				if (count($lines) < 2) {
					$hascontent = 0;
				}
				$path_parts = pathinfo($res);
				$name = $path_parts['basename'];
				$path =str_replace($root_directory, '', $path_parts['dirname']).'/';
				if ($this->file_type  == 'excel') {
					$csvpath = $path.$name;
					$xelpath = $path.$name.'.xls';
					require_once 'include/PhpSpreadsheet/autoload.php';
					try {
						$workbookReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv');
						$delim = cbQuestion::getQnDelimeterProperty((int)$this->qnfield);
						$workbookReader->setDelimiter($delim);
						$objPHPExcel = $workbookReader->load($res);
						$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
						$objWriter->save($xelpath);
						$name .= '.xls';
						unlink($csvpath);
					} catch (Exception $e) {
						$logbg->debug('generateReportWfTask > '. $e->getMessage());
					}
				}
			}
		}
		if (empty($entity->WorkflowContext['wfgenerated_file'])) {
			$entity->WorkflowContext['wfgenerated_file'] = array();
		}
		$entity->WorkflowContext['wfgenerated_file'][] = array('path'=>$path, 'name'=>$name, 'hascontent' => $hascontent);
	}
}
?>