<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the 'License'); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an 'AS IS' BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
////////////////////////
// NOTE: this code only works correctly for fields whose name is not repeated in any module, for example custom fields.
//   if you have a field called name or something similar that can be on a lot of modules you will get false positives
////////////////////////

$Vtiger_Utils_Log = false;
include_once 'vtlib/Vtiger/Module.php';
global $current_user, $adb;
$fldname = vtlib_purify($_REQUEST['fieldname']);
$modname = vtlib_purify($_REQUEST['modulename']);

function checkFieldUsage($fldname, $modname) {
	global $adb, $mod_strings;
	$mod = Vtiger_Module::getInstance($modname);
	$field = Vtiger_Field::getInstance($fldname, $mod);
	$found = false;
	$ret = '';
	$rdo = array(
		'found' => $found,
		'where' => array(),
		'message' => $ret,
	);
	// Workflow Conditions
	$crs = $adb->pquery('SELECT workflow_id,summary FROM `com_vtiger_workflows` WHERE test like ?', array('%'.$fldname.'%'));
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['wf_conditions_found'].$fnd['workflow_id']. ' / ' .$fnd['summary'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'workflow';
	} else {
		$ret .= $mod_strings['wf conditions'].'<br>';
	}

	// Workflow Tasks
	$crs = $adb->pquery('SELECT workflow_id,task_id,summary FROM `com_vtiger_workflowtasks` WHERE task like ?', array('%'.$fldname.'%'));
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['wf_tasks_found'].'('.$fnd['workflow_id'].') '.$mod_strings['LBL_TASK'].' ('.$fnd['task_id'].'): ';
			$ret .= $fnd['summary'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'workflowtasks';
	} else {
		$ret .= $mod_strings['wf_tasks'].'<br>';
	}

	// Custom View Columns
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['cv_column'].$fnd['viewname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'CustomView';
	} else {
		$ret .= $mod_strings['cv_column_nf'].'<br>';
	}

	// Custom View Conditions
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname FROM `vtiger_cvadvfilter` INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvadvfilter.cvid WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['cv_advfilter'].$fnd['viewname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'CustomViewConditions';
	} else {
		$ret .= $mod_strings['cv_advfilter_nf'].'<br>';
	}

	// Custom View Date Filters
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname FROM `vtiger_cvstdfilter` INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvstdfilter.cvid WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['cv_stdfilter'].$fnd['viewname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'CustomViewDateFilters';
	} else {
		$ret .= $mod_strings['cv_stdfilter_nf'].'<br>';
	}

	// Email Templates
	$crs = $adb->pquery('SELECT msgtemplateid,reference FROM `vtiger_msgtemplate` WHERE subject like ? or template like ?', array('%'.$fldname.'%', '%'.$fldname.'%'));
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['email_templates'].$fnd['msgtemplateid'].' :: '.$fnd['reference'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'MsgTemplate';
	} else {
		$ret .= $mod_strings['email_templates_nf'].'<br>';
	}

	// Report Fields
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM vtiger_selectcolumn
			INNER JOIN vtiger_report on vtiger_report.queryid=vtiger_selectcolumn.queryid
			WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['select_column'].$fnd['reportid'].' :: '.$fnd['reportname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'Reports';
	} else {
		$ret .= $mod_strings['select_column_nf'].'<br>';
	}

	// Report Date Filters
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportdatefilter`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportdatefilter.datefilterid
			WHERE datecolumnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['report_dtfilter'].$fnd['reportid'].' :: '.$fnd['reportname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'ReportsDateFilters';
	} else {
		$ret .= $mod_strings['report_dtfilter_nf'].'<br>';
	}

	// Report Group By
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportgroupbycolumn`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportgroupbycolumn.reportid
			WHERE sortcolname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['report'].$fnd['reportid'].' :: '.$fnd['reportname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'ReportsGroupBy';
	} else {
		$ret .= $mod_strings['report_nf'].'<br>';
	}

	// Report Sort By
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportsortcol`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsortcol.reportid
			WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['report_sort'].$fnd['reportid'].' :: '.$fnd['reportname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'ReportsSort';
	} else {
		$ret .= $mod_strings['report_sort_nf'].'<br>';
	}

	// Report Summary
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportsummary`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsummary.reportsummaryid
			WHERE columnname like ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.$mod_strings['report_summary'].$fnd['reportid'].' :: '.$fnd['reportname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'ReportsSummary';
	} else {
		$ret .= $mod_strings['report_summary_nf'].'<br>';
	}

	// Picklist Dependency
	$crs = $adb->pquery(
		'SELECT sourcefield,targetfield FROM vtiger_picklist_dependency WHERE sourcefield=? or targetfield=?',
		array($fldname, $fldname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.getTranslatedString('LBL_PICKLIST_DEPENDENCY_SETUP', 'PickList').' :: '.$fnd['sourcefield'].' - '.$fnd['targetfield'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'PickList';
	} else {
		$ret .= $mod_strings['picklist_dep_nf'].'<br>';
	}

	// Business Question
	$crs = $adb->pquery(
		'SELECT qname,cbquestionno FROM vtiger_cbquestion WHERE qcolumns LIKE ? OR qcondition LIKE ? OR orderby LIKE ? OR groupby LIKE ?',
		array('%'.$fldname.'%', '%'.$fldname.'%', '%'.$fldname.'%', '%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.getTranslatedString('cbQuestion', 'cbQuestion').' :: '.$fnd['cbquestionno'].' - '.$fnd['qname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'cbQuestion';
	} else {
		$ret .= $mod_strings['bquestion_nf'].'<br>';
	}

	// Business Map
	$crs = $adb->pquery(
		'SELECT mapname,mapnumber FROM vtiger_cbmap WHERE content LIKE ?',
		array('%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.getTranslatedString('cbMap', 'cbMap').' :: '.$fnd['mapnumber'].' - '.$fnd['mapname'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'cbMap';
	} else {
		$ret .= $mod_strings['bmap_nf'].'<br>';
	}

	// Tooltip: we skip this one because there is a DELETE CASCADE restriction that will eliminate the records

	// Calendar
	$crs = $adb->pquery(
		'SELECT userid,view FROM its4you_calendar4you_event_fields WHERE fieldname LIKE ?',
		array($fldname.':%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.getTranslatedString('cbCalendar', 'cbCalendar').' :: '.$fnd['view'].' - '.getUserFullName($fnd['userid']).'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'cbCalendar';
	} else {
		$ret .= $mod_strings['calendar_nf'].'<br>';
	}

	// Webforms
	$crs = $adb->pquery(
		'SELECT name
		FROM vtiger_webforms_field
		INNER JOIN vtiger_webforms on vtiger_webforms.id=vtiger_webforms_field.webformid
		WHERE fieldname LIKE ?',
		array($fldname.':%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= '<span style="color:red">'.getTranslatedString('Webforms', 'Webforms').' :: '.$fnd['name'].'</span><br>';
		}
		$found = true;
		$rdo['where'][] = 'Webforms';
	} else {
		$ret .= $mod_strings['webforms_nf'].'<br>';
	}

	// Lead Mapping
	if (in_array($modname, array('Contacts','Accounts','Potentials','Leads'))) {
		switch ($modname) {
			case 'Contacts':
				$searchon = 'contactfid';
				break;
			case 'Accounts':
				$searchon = 'accountfid';
				break;
			case 'Potentials':
				$searchon = 'potentialfid';
				break;
			case 'Leads':
				$searchon = 'leadfid';
				break;
		}
		$crs = $adb->pquery("SELECT 1 FROM `vtiger_convertleadmapping` WHERE $searchon = ?", array($field->id));
		if ($crs && $adb->num_rows($crs)>0) {
			$ret .= '<span style="color:red">'.$mod_strings['cl_mapping'].'</span><br>';
			$found = true;
			$rdo['where'][] = 'Leads';
		} else {
			$ret .= $mod_strings['cl_mapping_nf'].'<br>';
		}
	}
	$rdo['message'] = $ret;
	return $rdo;
}

if (empty($nooutput)) {
	$rdo = checkFieldUsage($fldname, $modname);
	echo $rdo['message'];
}
?>
