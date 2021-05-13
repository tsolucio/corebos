<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

class databasechangesToMoveTocbCalendar3 extends cbupdaterWorker {

	public function applyChange() {
		global $adb;
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery("DELETE FROM vtiger_customview WHERE entitytype='Calendar';");
			$this->ExecuteQuery("update vtiger_homedefault set setype='cbCalendar' where setype='Calendar'", array());
			// Reports
			$reps = $adb->query("select reportmodulesid,secondarymodules from vtiger_reportmodules where primarymodule='Calendar' or secondarymodules like '%Calendar%'");
			while ($rep = $adb->fetch_array($reps)) {
				$repid = $rep['reportmodulesid'];
				$this->ExecuteQuery("update vtiger_reportmodules set primarymodule='cbCalendar' where primarymodule='Calendar' and reportmodulesid=?", array($repid));
				$this->ExecuteQuery(
					"update vtiger_reportmodules set secondarymodules=replace(secondarymodules,'Calendar','cbCalendar') where secondarymodules like '%Calendar%' and reportmodulesid=?",
					array($repid)
				);
				$this->ExecuteQuery(
					"update vtiger_reportmodules set secondarymodules=replace(secondarymodules,'cbcbCalendar','cbCalendar') where secondarymodules like '%Calendar%' and reportmodulesid=?",
					array($repid)
				);
				$this->ExecuteQuery("update vtiger_selectcolumn set columnname=replace(columnname,'Calendar','cbCalendar') where queryid=?", array($repid));
				$this->ExecuteQuery("update vtiger_selectcolumn set columnname=replace(columnname,'cbcbCalendar','cbCalendar') where queryid=?", array($repid));
				$this->ExecuteQuery(
					"update vtiger_selectcolumn set columnname='vtiger_activity:rel_id:cbCalendar_Related_To:rel_id:I'
						where columnname='vtiger_seactivityrel:crmid:cbCalendar_Related_To:parent_id:V' and queryid=?",
					array($repid)
				);
				$this->ExecuteQuery("update vtiger_relcriteria set columnname=replace(columnname,'Calendar','cbCalendar') where queryid=?", array($repid));
				$this->ExecuteQuery("update vtiger_relcriteria set columnname=replace(columnname,'cbcbCalendar','cbCalendar') where queryid=?", array($repid));
				$this->ExecuteQuery(
					"update vtiger_reportdatefilter set datecolumnname=replace(datecolumnname,'Calendar','cbCalendar') where datefilterid=?",
					array($repid)
				);
				$this->ExecuteQuery(
					"update vtiger_reportdatefilter set datecolumnname=replace(datecolumnname,'cbcbCalendar','cbCalendar') where datefilterid=?",
					array($repid)
				);
				$this->ExecuteQuery("update vtiger_reportgroupbycolumn set sortcolname=replace(sortcolname,'Calendar','cbCalendar') where reportid=?", array($repid));
				$this->ExecuteQuery("update vtiger_reportgroupbycolumn set sortcolname=replace(sortcolname,'cbcbCalendar','cbCalendar') where reportid=?", array($repid));
				$this->ExecuteQuery("update vtiger_reportsortcol set columnname=replace(columnname,'Calendar','cbCalendar') where reportid=?", array($repid));
				$this->ExecuteQuery("update vtiger_reportsortcol set columnname=replace(columnname,'cbcbCalendar','cbCalendar') where reportid=?", array($repid));
			}
			//////////////////////
			$modname = 'Calendar';
			if ($this->isModuleInstalled($modname)) {
				$module = Vtiger_Module::getInstance($modname);
				$module->delete();
				$this->sendMsg("<b>Module $modname EXTERMINATED! The calendar is DEAD, long live the Calendar!</b><br>");
			}
			//////////////////////
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied(false);
		}
		$this->finishExecution();
	}
}
