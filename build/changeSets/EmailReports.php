<?php
/*************************************************************************************************
 * Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class EmailReports extends cbupdaterWorker {
	
	function applyChange() {
		global $adb;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// Email Reporting - added default email reports.
			
			$sql = "INSERT INTO vtiger_reportfolder (FOLDERNAME,DESCRIPTION,STATE) VALUES(?,?,?)";
			$params = array('Email Reports', 'Email Reports', 'SAVED');
			$this->ExecuteQuery($sql, $params);
			
			$reportmodules = Array(
				Array('primarymodule' => 'Contacts', 'secondarymodule' => 'Emails'),
				Array('primarymodule' => 'Accounts', 'secondarymodule' => 'Emails'),
				Array('primarymodule' => 'Leads', 'secondarymodule' => 'Emails'),
				Array('primarymodule' => 'Vendors', 'secondarymodule' => 'Emails')
			);
			
			$reports = Array(
				Array('reportname' => 'Contacts Email Report',
					'reportfolder' => 'Email Reports',
					'description' => 'Emails sent to Contacts',
					'reporttype' => 'tabular',
					'sortid' => '', 'stdfilterid' => '', 'advfilterid' => '0'),
				Array('reportname' => 'Accounts Email Report',
					'reportfolder' => 'Email Reports',
					'description' => 'Emails sent to Organizations',
					'reporttype' => 'tabular',
					'sortid' => '', 'stdfilterid' => '', 'advfilterid' => '0'),
				Array('reportname' => 'Leads Email Report',
					'reportfolder' => 'Email Reports',
					'description' => 'Emails sent to Leads',
					'reporttype' => 'tabular',
					'sortid' => '', 'stdfilterid' => '', 'advfilterid' => '0'),
				Array('reportname' => 'Vendors Email Report',
					'reportfolder' => 'Email Reports',
					'description' => 'Emails sent to Vendors',
					'reporttype' => 'tabular',
					'sortid' => '', 'stdfilterid' => '', 'advfilterid' => '0')
			);
			
			$selectcolumns = Array(
				Array('vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V',
					'vtiger_contactdetails:email:Contacts_Email:email:E',
					'vtiger_activity:subject:Emails_Subject:subject:V',
					'vtiger_email_track:access_count:Emails_Access_Count:access_count:V'),
				Array('vtiger_account:accountname:Accounts_Account_Name:accountname:V',
					'vtiger_account:phone:Accounts_Phone:phone:V',
					'vtiger_account:email1:Accounts_Email:email1:E',
					'vtiger_activity:subject:Emails_Subject:subject:V',
					'vtiger_email_track:access_count:Emails_Access_Count:access_count:V'),
				Array('vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V',
					'vtiger_leaddetails:company:Leads_Company:company:V',
					'vtiger_leaddetails:email:Leads_Email:email:E',
					'vtiger_activity:subject:Emails_Subject:subject:V',
					'vtiger_email_track:access_count:Emails_Access_Count:access_count:V'),
				Array('vtiger_vendor:vendorname:Vendors_Vendor_Name:vendorname:V',
					'vtiger_vendor:glacct:Vendors_GL_Account:glacct:V',
					'vtiger_vendor:email:Vendors_Email:email:E',
					'vtiger_activity:subject:Emails_Subject:subject:V',
					'vtiger_email_track:access_count:Emails_Access_Count:access_count:V'),
			);
			
			$advfilters = Array(
				Array(
					Array(
						'columnname' => 'vtiger_email_track:access_count:Emails_Access_Count:access_count:V',
						'comparator' => 'n',
						'value' => ''
					)
				)
			);
			
			foreach ($reports as $key => $report) {
				$queryid = $this->insertSelectQuery();
				$sql = 'SELECT MAX(folderid) AS count FROM vtiger_reportfolder';
				$result = $adb->query($sql);
				$folderid = $adb->query_result($result, 0, 'count');
				$this->insertReports($queryid, $folderid, $report['reportname'], $report['description'], $report['reporttype']);
				$this->insertSelectColumns($queryid, $selectcolumns[$key]);
				$this->insertReportModules($queryid, $reportmodules[$key]['primarymodule'], $reportmodules[$key]['secondarymodule']);
				if(isset($advfilters[$report['advfilterid']])) {
					$this->insertAdvFilter($queryid, $advfilters[$report['advfilterid']]);
				}
			}
			//$this->ExecuteQuery("UPDATE vtiger_report SET sharingtype='Public'", array());
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			// undo your magic here
			$emrpts = $adb->query("SELECT REPORTID FROM vtiger_report WHERE REPORTNAME in ('Contacts Email Report','Accounts Email Report','Leads Email Report','Vendors Email Report')");
			while ($rpt = $adb->fetch_array($emrpts)) {
				$this->ExecuteQuery('delete from vtiger_relcriteria_grouping where QUERYID=?',array($rpt['REPORTID']));
				$this->ExecuteQuery('delete from vtiger_relcriteria where QUERYID=?',array($rpt['REPORTID']));
				$this->ExecuteQuery('delete from vtiger_reportmodules where REPORTMODULESID=?',array($rpt['REPORTID']));
				$this->ExecuteQuery('delete from vtiger_selectcolumn where QUERYID=?',array($rpt['REPORTID']));
				$this->ExecuteQuery('delete from vtiger_report where REPORTID=?',array($rpt['REPORTID']));
				$this->ExecuteQuery('delete from vtiger_selectquery where QUERYID=?',array($rpt['REPORTID']));
			}
			$this->ExecuteQuery("delete from vtiger_reportfolder where FOLDERNAME='Email Reports'");
			$this->sendMsg('Changeset '.get_class($this).' undone!');
			$this->markUndone();
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}

	function isApplied() {
		if (parent::isApplied()) return true;
		global $adb;
		$rse = $adb->query("SELECT count(*) FROM vtiger_reportfolder WHERE FOLDERNAME='Email Reports'");
		$emfld = $adb->query_result($rse,0,0);
		if ($emfld>0) return true;
		$rse = $adb->query("SELECT count(*) FROM vtiger_report WHERE REPORTNAME in ('Contacts Email Report','Accounts Email Report','Leads Email Report','Vendors Email Report')");
		$emrpt = $adb->query_result($rse,0,0);
		if ($emrpt>0) return true;
		return false;
	}

	function insertSelectQuery() {
		global $adb;
		$genQueryId = $adb->getUniqueID("vtiger_selectquery");
		if ($genQueryId != "") {
			$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
			$this->ExecuteQuery($iquerysql, array($genQueryId, 0, 0));
		}
		return $genQueryId;
	}
	function insertReports($queryid, $folderid, $reportname, $description, $reporttype) {
		if ($queryid != "") {
			$ireportsql = "insert into vtiger_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE) values (?,?,?,?,?,?,?)";
			$ireportparams = array($queryid, $folderid, $reportname, $description, $reporttype, $queryid, 'SAVED');
			$this->ExecuteQuery($ireportsql, $ireportparams);
		}
	}
	function insertSelectColumns($queryid, $columnname) {
		if ($queryid != "") {
			for ($i = 0; $i < count($columnname); $i++) {
				$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
				$this->ExecuteQuery($icolumnsql, array($queryid, $i, $columnname[$i]));
			}
		}
	}
	function insertReportModules($queryid, $primarymodule, $secondarymodule) {
		if ($queryid != "") {
			$ireportmodulesql = "insert into vtiger_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)";
			$this->ExecuteQuery($ireportmodulesql, array($queryid, $primarymodule, $secondarymodule));
		}
	}
	function insertAdvFilter($queryid, $filters) {
		if ($queryid != "") {
			$columnIndexArray = array();
			foreach ($filters as $i => $filter) {
				$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE) values (?,?,?,?,?)";
				$this->ExecuteQuery($irelcriteriasql, array($queryid, $i, $filter['columnname'], $filter['comparator'], $filter['value']));
				$columnIndexArray[] = $i;
			}
			$conditionExpression = implode(' and ', $columnIndexArray);
			$this->ExecuteQuery('INSERT INTO vtiger_relcriteria_grouping VALUES(?,?,?,?)', array(1, $queryid, '', $conditionExpression));
		}
	}
	
}