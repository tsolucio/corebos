<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'modules/Reports/ReportRun.php';

class moveCalendarReport2cbCalendar extends cbupdaterWorker {

	function applyChange() {
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			global $adb;
			$contacts = Vtiger_Module::getInstance('Contacts');
			$reps = $adb->query("select reportmodulesid,secondarymodules from vtiger_reportmodules where primarymodule='Calendar' or secondarymodules like '%Calendar%'");
			while ($rep = $adb->fetch_array($reps)) {
				$repid = $rep['reportmodulesid'];
				$this->ExecuteQuery("update vtiger_reportmodules set primarymodule='cbCalendar' where primarymodule='Calendar' and reportmodulesid=?",array($repid));
				$this->ExecuteQuery("update vtiger_reportmodules set secondarymodules=replace(secondarymodules,'Calendar','cbCalendar') where secondarymodules like '%Calendar%' and reportmodulesid=?",array($repid));
				$this->ExecuteQuery("update vtiger_selectcolumn set columnname=replace(columnname,'Calendar','cbCalendar') where queryid=?",array($repid));
				$this->ExecuteQuery("update vtiger_selectcolumn set columnname='vtiger_activity:rel_id:cbCalendar_Related_To:rel_id:I'
					where columnname='vtiger_seactivityrel:crmid:cbCalendar_Related_To:parent_id:V' and queryid=?",array($repid));
				$secmods = $rep['secondarymodules'];
				$hasContacts = (strpos($secmods, 'Contacts') !== false);
				$repcols = $adb->pquery("select * from vtiger_selectcolumn where queryid=? and columnname like 'vtiger_contactdetailscbCalendar%'",array($repid));
				while ($ctocol = $adb->fetch_array($repcols)) {
					if ($hasContacts) {
						$col = explode(':',$ctocol['columnname']);
						$field = Vtiger_Field::getInstance($col[3],$contacts);
						$fieldlabel1 = str_replace(' ','_',$field->label);
						$fieldlabel1 = ReportRun::replaceSpecialChar($fieldlabel1);
						$ftype = explode('~', $field->typeofdata);
						$optionvalue = 'vtiger_contactdetails:'.$field->column.':Contacts_'.$fieldlabel1.':'.$field->name.':'.$ftype[0];
						$this->ExecuteQuery('update vtiger_selectcolumn set columnname=? where columnindex=? and queryid=?',
							array($optionvalue,$ctocol['columnindex'],$repid));
					} else {
						$this->ExecuteQuery("update vtiger_selectcolumn set columnname='vtiger_activity:cto_id:cbCalendar_Contact_Name:cto_id:I'
							where columnindex=? and queryid=?",array($ctocol['columnindex'],$repid));
					}
				}
				$this->ExecuteQuery("update vtiger_selectcolumn set columnname='vtiger_activity:dtstart:cbCalendar_Start_Date_and_Time:dtstart:DT'
					where columnname='vtiger_activity:date_start:cbCalendar_Start_Date_and_Time:date_start:DT' and queryid=?",array($repid));
				$this->ExecuteQuery("update vtiger_relcriteria set columnname=replace(columnname,'Calendar','cbCalendar') where queryid=?",array($repid));
				$this->ExecuteQuery("update vtiger_reportdatefilter set datecolumnname=replace(datecolumnname,'Calendar','cbCalendar') where datefilterid=?",array($repid));
				$this->ExecuteQuery("update vtiger_reportgroupbycolumn set sortcolname=replace(sortcolname,'Calendar','cbCalendar') where reportid=?",array($repid));
				$this->ExecuteQuery("update vtiger_reportsortcol set columnname=replace(columnname,'Calendar','cbCalendar') where reportid=?",array($repid));
			}
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}
?>
