<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class ModulesOnCalendar extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb,$log;
$its4you_calendar_modulestatus = array(
  array('module' => 'Project','status' => 'Planned','field' => 'projectstatus','value' => 'completed','operator' => 'n','glue' => ''),
  array('module' => 'Project','status' => 'Planned','field' => 'projectstatus','value' => 'delivered','operator' => 'n','glue' => 'AND'),
  array('module' => 'Project','status' => 'Planned','field' => 'projectstatus','value' => 'in progress','operator' => 'n','glue' => 'AND'),
  array('module' => 'Project','status' => 'Held','field' => 'projectstatus','value' => 'completed','operator' => 'e','glue' => ''),
  array('module' => 'Project','status' => 'Held','field' => 'projectstatus','value' => 'delivered','operator' => 'e','glue' => 'OR'),
  array('module' => 'Project','status' => 'Not Held','field' => 'projectstatus','value' => 'in progress','operator' => 'e','glue' => ''),
  array('module' => 'ProjectTask','status' => 'Planned','field' => 'projecttaskprogress','value' => '--none--','operator' => 'e','glue' => ''),
  array('module' => 'ProjectTask','status' => 'Held','field' => 'projecttaskprogress','value' => '100%','operator' => 'e','glue' => ''),
  array('module' => 'ProjectTask','status' => 'Not Held','field' => 'projecttaskprogress','value' => '100%','operator' => 'n','glue' => ''),
  array('module' => 'ProjectTask','status' => 'Not Held','field' => 'projecttaskprogress','value' => '--none--','operator' => 'n','glue' => 'AND'),
  array('module' => 'cbupdater','status' => 'Planned','field' => 'execstate','value' => 'Pending','operator' => 'e','glue' => ''),
  array('module' => 'cbupdater','status' => 'Held','field' => 'execstate','value' => 'Executed','operator' => 'e','glue' => ''),
  array('module' => 'cbupdater','status' => 'Not Held','field' => 'execstate','value' => 'Pending','operator' => 'e','glue' => ''),
  array('module' => 'Campaigns','status' => 'Held','field' => 'campaignstatus','value' => 'Completed','operator' => 'e','glue' => ''),
  array('module' => 'Campaigns','status' => 'Held','field' => 'campaignstatus','value' => 'Cancelled','operator' => 'e','glue' => 'OR'),
  array('module' => 'Campaigns','status' => 'Not Held','field' => 'campaignstatus','value' => 'Active','operator' => 'e','glue' => ''),
  array('module' => 'Campaigns','status' => 'Not Held','field' => 'campaignstatus','value' => 'Inactive','operator' => 'e','glue' => 'OR'),
  array('module' => 'Campaigns','status' => 'Planned','field' => 'campaignstatus','value' => '--None--','operator' => 'e','glue' => ''),
  array('module' => 'Campaigns','status' => 'Planned','field' => 'campaignstatus','value' => 'Planning','operator' => 'e','glue' => 'OR'),
  array('module' => 'CobroPago','status' => 'Planned','field' => 'paid','value' => '0','operator' => 'e','glue' => ''),
  array('module' => 'CobroPago','status' => 'Not Held','field' => 'paid','value' => '0','operator' => 'e','glue' => ''),
  array('module' => 'CobroPago','status' => 'Held','field' => 'paid','value' => '1','operator' => 'e','glue' => ''),
  array('module' => 'ServiceContracts','status' => 'Planned','field' => 'contract_status','value' => 'In Planning','operator' => 'e','glue' => ''),
  array('module' => 'ServiceContracts','status' => 'Held','field' => 'contract_status','value' => 'Complete','operator' => 'e','glue' => ''),
  array('module' => 'ServiceContracts','status' => 'Held','field' => 'contract_status','value' => 'Archived','operator' => 'e','glue' => 'OR'),
  array('module' => 'ServiceContracts','status' => 'Not Held','field' => 'contract_status','value' => 'In Progress','operator' => 'e','glue' => ''),
  array('module' => 'ServiceContracts','status' => 'Not Held','field' => 'contract_status','value' => 'On Hold','operator' => 'e','glue' => 'OR'),
  array('module' => 'Invoice','status' => 'Planned','field' => 'invoicestatus','value' => 'Approved','operator' => 'e','glue' => ''),
  array('module' => 'Invoice','status' => 'Planned','field' => 'invoicestatus','value' => 'Sent','operator' => 'e','glue' => 'OR'),
  array('module' => 'Invoice','status' => 'Not Held','field' => 'invoicestatus','value' => 'AutoCreated','operator' => 'e','glue' => ''),
  array('module' => 'Invoice','status' => 'Not Held','field' => 'invoicestatus','value' => 'Created','operator' => 'e','glue' => 'OR'),
  array('module' => 'Invoice','status' => 'Held','field' => 'invoicestatus','value' => 'Paid','operator' => 'e','glue' => ''),
  array('module' => 'Potentials','status' => 'Not Held','field' => 'sales_stage','value' => 'Closed Won','operator' => 'n','glue' => ''),
  array('module' => 'Potentials','status' => 'Not Held','field' => 'sales_stage','value' => 'Closed Lost','operator' => 'n','glue' => 'AND'),
  array('module' => 'Potentials','status' => 'Planned','field' => 'sales_stage','value' => 'Closed Won','operator' => 'n','glue' => ''),
  array('module' => 'Potentials','status' => 'Planned','field' => 'sales_stage','value' => 'Closed Lost','operator' => 'n','glue' => 'AND'),
  array('module' => 'Potentials','status' => 'Held','field' => 'sales_stage','value' => 'Closed Won','operator' => 'e','glue' => ''),
  array('module' => 'Potentials','status' => 'Held','field' => 'sales_stage','value' => 'Closed Lost','operator' => 'e','glue' => 'OR'),
  array('module' => 'PurchaseOrder','status' => 'Planned','field' => 'postatus','value' => 'Created','operator' => 'e','glue' => ''),
  array('module' => 'PurchaseOrder','status' => 'Planned','field' => 'postatus','value' => 'Approved','operator' => 'e','glue' => 'OR'),
  array('module' => 'PurchaseOrder','status' => 'Held','field' => 'postatus','value' => 'Delivered','operator' => 'e','glue' => ''),
  array('module' => 'PurchaseOrder','status' => 'Held','field' => 'postatus','value' => 'Received Shipment','operator' => 'e','glue' => 'OR'),
  array('module' => 'PurchaseOrder','status' => 'Not Held','field' => 'postatus','value' => 'Cancelled','operator' => 'e','glue' => ''),
  array('module' => 'SalesOrder','status' => 'Planned','field' => 'sostatus','value' => 'Created','operator' => 'e','glue' => ''),
  array('module' => 'SalesOrder','status' => 'Planned','field' => 'sostatus','value' => 'Approved','operator' => 'e','glue' => 'OR'),
  array('module' => 'SalesOrder','status' => 'Held','field' => 'sostatus','value' => 'Delivered','operator' => 'e','glue' => ''),
  array('module' => 'SalesOrder','status' => 'Not Held','field' => 'sostatus','value' => 'Cancelled','operator' => 'e','glue' => ''),
  array('module' => 'Quotes','status' => 'Held','field' => 'quotestage','value' => 'Delivered','operator' => 'e','glue' => ''),
  array('module' => 'Quotes','status' => 'Not Held','field' => 'quotestage','value' => 'Rejected','operator' => 'e','glue' => ''),
  array('module' => 'Quotes','status' => 'Planned','field' => 'quotestage','value' => 'Created','operator' => 'e','glue' => ''),
  array('module' => 'Quotes','status' => 'Planned','field' => 'quotestage','value' => 'Reviewed','operator' => 'e','glue' => 'OR'),
  array('module' => 'Quotes','status' => 'Planned','field' => 'quotestage','value' => 'Accepted','operator' => 'e','glue' => 'OR'),
  array('module' => 'Products','status' => 'Planned','field' => 'discontinued','value' => '1','operator' => 'e','glue' => ''),
  array('module' => 'Products','status' => 'Held','field' => 'discontinued','value' => '0','operator' => 'e','glue' => ''),
  array('module' => 'Services','status' => 'Planned','field' => 'discontinued','value' => '1','operator' => 'e','glue' => ''),
  array('module' => 'Services','status' => 'Held','field' => 'discontinued','value' => '0','operator' => 'e','glue' => ''),
  array('module' => 'Assets','status' => 'Held','field' => 'assetstatus','value' => 'Out-of-service','operator' => 'e','glue' => ''),
  array('module' => 'Assets','status' => 'Not Held','field' => 'assetstatus','value' => 'In Service','operator' => 'e','glue' => ''),
  array('module' => 'Timecontrol','status' => 'Held','field' => 'date_end','value' => '','operator' => 'n','glue' => ''),
  array('module' => 'Timecontrol','status' => 'Not Held','field' => 'date_end','value' => '','operator' => 'e','glue' => '')
);
$this->ExecuteQuery('CREATE TABLE IF NOT EXISTS `its4you_calendar_modulestatus` (
  `calmodstatus` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `field` varchar(50) NOT NULL,
  `value` varchar(250) NOT NULL,
  `operator` varchar(10) NOT NULL,
  `glue` varchar(3) NOT NULL,
  PRIMARY KEY (calmodstatus),
  index(`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$ins = 'insert into its4you_calendar_modulestatus (module,status,field,value,operator,glue) values(?,?,?,?,?,?)';
foreach ($its4you_calendar_modulestatus as $record) {
	$this->ExecuteQuery($ins,$record);
}

$its4you_calendar_modulefields = array(
  array('module' => 'Project','start_field' => 'startdate','end_field' => 'targetenddate','subject_fields' => 'progress,projecttype'),
  array('module' => 'ProjectTask','start_field' => 'startdate','end_field' => 'enddate','subject_fields' => 'Project.projectname,projecttaskname,projecttaskprogress'),
  array('module' => 'SalesOrder','start_field' => 'duedate','end_field' => 'duedate','subject_fields' => 'hdnGrandTotal'),
  array('module' => 'cbupdater','start_field' => 'execdate','end_field' => 'execdate','subject_fields' => 'classname,execstate'),
  array('module' => 'Campaigns','start_field' => 'closingdate','end_field' => 'closingdate','subject_fields' => 'campaignstatus'),
  array('module' => 'CobroPago','start_field' => 'duedate','end_field' => 'duedate','subject_fields' => 'amount'),
  array('module' => 'Contacts','start_field' => 'birthday','end_field' => 'birthday','subject_fields' => 'firstname'),
  array('module' => 'ServiceContracts','start_field' => 'start_date','end_field' => 'end_date','subject_fields' => 'used_units,total_units'),
  array('module' => 'Invoice','start_field' => 'duedate','end_field' => 'duedate','subject_fields' => 'hdnGrandTotal'),
  array('module' => 'ProjectMilestone','start_field' => 'projectmilestonedate','end_field' => 'projectmilestonedate','subject_fields' => 'Project.projectname'),
  array('module' => 'Potentials','start_field' => 'closingdate','end_field' => 'closingdate','subject_fields' => 'sales_stage'),
  array('module' => 'PurchaseOrder','start_field' => 'duedate','end_field' => 'duedate','subject_fields' => 'hdnGrandTotal'),
  array('module' => 'Quotes','start_field' => 'validtill','end_field' => 'validtill','subject_fields' => 'hdnGrandTotal'),
  array('module' => 'Products','start_field' => 'sales_start_date','end_field' => 'sales_end_date','subject_fields' => 'productcode'),
  array('module' => 'Services','start_field' => 'sales_start_date','end_field' => 'sales_end_date','subject_fields' => ''),
  array('module' => 'Assets','start_field' => 'dateinservice','end_field' => 'dateinservice','subject_fields' => 'serialnumber'),
);
$this->ExecuteQuery('CREATE TABLE IF NOT EXISTS `its4you_calendar_modulefields` (
  `calmodfields` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(19) NOT NULL,
  `module` varchar(50) NOT NULL,
  `start_field` varchar(30) NOT NULL,
  `start_time` varchar(30) NOT NULL,
  `end_field` varchar(30) NOT NULL,
  `end_time` varchar(30) NOT NULL,
  `subject_fields` varchar(250) NOT NULL,
  `color` varchar(50) NOT NULL,
   PRIMARY KEY (calmodfields),
  index(`userid`,`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

$ins = "insert into its4you_calendar_modulefields (userid,module,start_field,end_field,subject_fields,color,start_time,end_time) values(1,?,?,?,?,'','','')";
foreach ($its4you_calendar_modulefields as $record) {
	$this->ExecuteQuery($ins,$record);
}
// Now Timecontrol which is special
$ins = "insert into its4you_calendar_modulefields (userid,module,start_field,end_field,subject_fields,color,start_time,end_time)
	values(1,'Timecontrol','date_start','date_end','totaltime','','time_start','time_end')";
$this->ExecuteQuery($ins);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

}