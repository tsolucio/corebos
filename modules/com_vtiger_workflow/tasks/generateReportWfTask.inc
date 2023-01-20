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
	public $case_type;
	public $file_type;
	public $report_name;
	public $qnfield;
	public $qnfield_display;

	public function getFieldNames() {
		return array('case_type', 'file_type', 'report_name', 'qnfield', 'qnfield_display');
	}

	public function getContextVariables() {
		return array(
			'wfgenerated_returncontent' => array(
				'type' => '56',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_wfgenerated_returncontent',
			),
			'GenerateReportName' => array(
				'type' => '1',
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_GenerateReportName',
			),
			'GenerateQuestionName' => array(
				'type' => 1,
				'values' => '',
				'modules' => '',
				'massaction' => true,
				'description' => 'HELP_GenerateQuestionName',
			),
			'wfgenerated_file' => array(
				'type' => 'array',
				'values' => '',
				'modules' => '',
				'massaction' => false,
				'description' => 'HELP_wfgenerated_file',
			),
		);
	}

	public function doTask(&$entity) {
		global $adb, $logbg, $root_directory;
		$path = '';
		$name = '';
		$hascontent = 1;
		$returnContent = (empty($entity->WorkflowContext['wfgenerated_returncontent']) ? 0 : 1);
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
				$returnContent = 0;
				$fileName = $path.$baseFileName.'.pdf';
				$filePath = $root_directory.$fileName;
				$pdf = $oReportRun->getReportPDF(null);
				$pdf->Output($filePath, 'F');
				$name = $baseFileName.'.pdf';
			}
			if ($this->file_type == 'csv') {
				if ($returnContent) {
					$res = $oReportRun->writeReportToCSVVariable();
				} else {
					$fileName = $path.$baseFileName.'.csv';
					$filePath = $root_directory.$fileName;
					$oReportRun->writeReportToCSVFile($filePath, null);
					$name = $baseFileName.'.csv';
				}
			}
			if ($this->file_type  == 'excel') {
				$returnContent = 0;
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
			$res = $adb->pquery('SELECT qtype FROM vtiger_cbquestion WHERE cbquestionid=?', array($this->qnfield));
			if ($res && $adb->num_rows($res) > 0) {
				$qtype = $adb->query_result($res, 0, 'qtype');
			} else {
				$qtype = 'File';
			}
			$res = cbQuestion::getFormattedAnswer((int)$this->qnfield, $entity->WorkflowContext);
			if ($res && $qtype=='File') {
				$lines = file($res);
				if (count($lines) < 2) {
					$hascontent = 0;
				}
				$path_parts = pathinfo($res);
				$name = $path_parts['basename'];
				$path =str_replace($root_directory, '', $path_parts['dirname']).'/';
				if ($this->file_type == 'excel') {
					$returnContent = 0;
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
				} elseif ($this->file_type  == 'pdf') {
					$returnContent = 0;
				} else { // CSV
					$returnContent = 1;
					$res = file_get_contents($res);
				}
			} elseif (!empty($res)) {
				$hascontent = 1;
				$returnContent = 1;
			} else {
				$hascontent = 0;
			}
		}
		if (empty($entity->WorkflowContext['wfgenerated_file'])) {
			$entity->WorkflowContext['wfgenerated_file'] = array();
		}
		if ($returnContent) {
			$entity->WorkflowContext['wfgenerated_file'][] = array('path'=>$path, 'name'=>$name, 'hascontent' => $hascontent, 'content' => $res);
		} else {
			$entity->WorkflowContext['wfgenerated_file'][] = array('path'=>$path, 'name'=>$name, 'hascontent' => $hascontent);
		}
	}
}
?>