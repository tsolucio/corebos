<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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

/**
 * Check if a field is being used in various parts of the application
 * false positives may be returned if the fieldname is present in various modules as we inform if we can't be sure the fieldname found belongs to the module or not
 * @param string $fldname name of the field to search for
 * @param string $modname module name the field is in
 * @return array with the elements:
 *   'found' => boolean if found or not,
 *   'where' => array of places the field was found,
 *   'message' => a formatted string with the result of the process
 */
function checkFieldUsage($fldname, $modname) {
	global $adb, $current_language;
	$spanStart = '<span style="color:red">';
	$spanEnd = '</span><br>';
	$i18n = return_module_language($current_language, 'Settings');
	$mod = Vtiger_Module::getInstance($modname);
	$tabid = $mod->getId();
	$field = Vtiger_Field::getInstance($fldname, $mod);
	$ret = '';
	$rdo = array(
		'found' => false,
		'where' => array(),
		'message' => $ret,
	);
	// Workflow Conditions
	$crs = $adb->pquery('SELECT workflow_id,summary FROM `com_vtiger_workflows` WHERE test like ? and module_name=?', array('%'.$fldname.'%', $modname));
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['wf_conditions_found'].$fnd['workflow_id']. ' / ' .$fnd['summary'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'workflow';
	} else {
		$ret .= $i18n['wf conditions'].'<br>';
	}

	// Workflow Tasks
	$crs = $adb->pquery(
		'SELECT com_vtiger_workflowtasks.workflow_id,com_vtiger_workflowtasks.task_id,com_vtiger_workflowtasks.summary
		FROM com_vtiger_workflowtasks
		INNER JOIN com_vtiger_workflows ON com_vtiger_workflowtasks.workflow_id=com_vtiger_workflows.workflow_id
		WHERE com_vtiger_workflowtasks.task like ? and com_vtiger_workflows.module_name=?',
		array('%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['wf_tasks_found'].'('.$fnd['workflow_id'].') '.$i18n['LBL_TASK'].' ('.$fnd['task_id'].'): ';
			$ret .= $fnd['summary'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'workflowtasks';
	} else {
		$ret .= $i18n['wf_tasks'].'<br>';
	}

	// Custom View Columns
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname
		FROM vtiger_cvcolumnlist
		INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid
		WHERE vtiger_cvcolumnlist.columnname like ? and vtiger_customview.entitytype=?',
		array('%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['cv_column'].$fnd['viewname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'CustomView';
	} else {
		$ret .= $i18n['cv_column_nf'].'<br>';
	}

	// Custom View Conditions
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname
		FROM vtiger_cvadvfilter
		INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvadvfilter.cvid
		WHERE vtiger_cvadvfilter.columnname like ? and vtiger_customview.entitytype=?',
		array('%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['cv_advfilter'].$fnd['viewname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'CustomViewConditions';
	} else {
		$ret .= $i18n['cv_advfilter_nf'].'<br>';
	}

	// Custom View Date Filters
	$crs = $adb->pquery(
		'SELECT vtiger_customview.viewname
		FROM vtiger_cvstdfilter
		INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvstdfilter.cvid
		WHERE vtiger_cvstdfilter.columnname like ? and vtiger_customview.entitytype=?',
		array('%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['cv_stdfilter'].$fnd['viewname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'CustomViewDateFilters';
	} else {
		$ret .= $i18n['cv_stdfilter_nf'].'<br>';
	}

	// Email Templates
	$crs = $adb->pquery(
		'SELECT msgtemplateid,reference
		FROM vtiger_msgtemplate
		WHERE (subject like ? or template like ?) and (msgt_module=? or msgt_module="" or msgt_module is null)',
		array('%'.$fldname.'%', '%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['email_templates'].$fnd['msgtemplateid'].' :: '.$fnd['reference'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'MsgTemplate';
	} else {
		$ret .= $i18n['email_templates_nf'].'<br>';
	}

	// Report Fields
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM vtiger_selectcolumn
			INNER JOIN vtiger_report on vtiger_report.queryid=vtiger_selectcolumn.queryid
			INNER JOIN vtiger_reportmodules on vtiger_report.queryid=vtiger_reportmodules.reportmodulesid
			WHERE columnname like ? and (primarymodule=? or secondarymodules like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['select_column'].$fnd['reportid'].' :: '.$fnd['reportname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'Reports';
	} else {
		$ret .= $i18n['select_column_nf'].'<br>';
	}

	// Report Date Filters
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportdatefilter`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportdatefilter.datefilterid
			INNER JOIN vtiger_reportmodules on vtiger_report.queryid=vtiger_reportmodules.reportmodulesid
			WHERE datecolumnname like ? and (primarymodule=? or secondarymodules like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['report_dtfilter'].$fnd['reportid'].' :: '.$fnd['reportname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'ReportsDateFilters';
	} else {
		$ret .= $i18n['report_dtfilter_nf'].'<br>';
	}

	// Report Group By
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportgroupbycolumn`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportgroupbycolumn.reportid
			INNER JOIN vtiger_reportmodules on vtiger_report.queryid=vtiger_reportmodules.reportmodulesid
			WHERE sortcolname like ? and (primarymodule=? or secondarymodules like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['report'].$fnd['reportid'].' :: '.$fnd['reportname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'ReportsGroupBy';
	} else {
		$ret .= $i18n['report_nf'].'<br>';
	}

	// Report Sort By
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportsortcol`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsortcol.reportid
			INNER JOIN vtiger_reportmodules on vtiger_report.queryid=vtiger_reportmodules.reportmodulesid
			WHERE columnname like ? and (primarymodule=? or secondarymodules like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['report_sort'].$fnd['reportid'].' :: '.$fnd['reportname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'ReportsSort';
	} else {
		$ret .= $i18n['report_sort_nf'].'<br>';
	}

	// Report Summary
	$crs = $adb->pquery(
		'SELECT vtiger_report.reportid,vtiger_report.reportname
			FROM `vtiger_reportsummary`
			INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsummary.reportsummaryid
			INNER JOIN vtiger_reportmodules on vtiger_report.queryid=vtiger_reportmodules.reportmodulesid
			WHERE columnname like ? and (primarymodule=? or secondarymodules like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.$i18n['report_summary'].$fnd['reportid'].' :: '.$fnd['reportname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'ReportsSummary';
	} else {
		$ret .= $i18n['report_summary_nf'].'<br>';
	}

	// Picklist Dependency
	$crs = $adb->pquery(
		'SELECT sourcefield,targetfield FROM vtiger_picklist_dependency WHERE (sourcefield=? or targetfield=?) and tabid=?',
		array($fldname, $fldname, $tabid)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.getTranslatedString('LBL_PICKLIST_DEPENDENCY_SETUP', 'PickList').' :: '.$fnd['sourcefield'].' - '.$fnd['targetfield'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'PickList';
	} else {
		$ret .= $i18n['picklist_dep_nf'].'<br>';
	}

	// Business Question
	$crs = $adb->pquery(
		'SELECT qname,cbquestionno
		FROM vtiger_cbquestion
		WHERE (qcolumns LIKE ? OR qcondition LIKE ? OR orderby LIKE ? OR groupby LIKE ?) and (qmodule=? or qmodule="" or qmodule is null)',
		array('%'.$fldname.'%', '%'.$fldname.'%', '%'.$fldname.'%', '%'.$fldname.'%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.getTranslatedString('cbQuestion', 'cbQuestion').' :: '.$fnd['cbquestionno'].' - '.$fnd['qname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'cbQuestion';
	} else {
		$ret .= $i18n['bquestion_nf'].'<br>';
	}

	// Business Map
	$crs = $adb->pquery(
		'SELECT mapname,mapnumber FROM vtiger_cbmap WHERE content LIKE ? and (targetname=? or targetname="" or targetname is null or content like ?)',
		array('%'.$fldname.'%', $modname, '%'.$modname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.getTranslatedString('cbMap', 'cbMap').' :: '.$fnd['mapnumber'].' - '.$fnd['mapname'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'cbMap';
	} else {
		$ret .= $i18n['bmap_nf'].'<br>';
	}

	// Tooltip: we skip this one because there is a DELETE CASCADE restriction that will eliminate the records

	// Calendar
	$crs = $adb->pquery(
		'SELECT userid,view
		FROM its4you_calendar4you_event_fields
		INNER JOIN vtiger_field ON vtiger_field.fieldid=SUBSTRING_INDEX(its4you_calendar4you_event_fields.fieldname, ":", -1)
		WHERE its4you_calendar4you_event_fields.fieldname LIKE ? and tabid=?',
		array($fldname.':%', $tabid)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.getTranslatedString('cbCalendar', 'cbCalendar').' :: '.$fnd['view'].' - '.getUserFullName($fnd['userid']).$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'cbCalendar';
	} else {
		$ret .= $i18n['calendar_nf'].'<br>';
	}

	// Calendar Modules Status
	$crs = $adb->pquery(
		'SELECT distinct module,field FROM its4you_calendar_modulestatus WHERE field=? and module=?',
		array($fldname, $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		$ret .= $spanStart.getTranslatedString('cbCalendar', 'cbCalendar').' Module Status</span><br>';
		$rdo['found'] = true;
		$rdo['where'][] = 'Calendar Modules Status';
	} else {
		$ret .= $i18n['calendar_nf'].'<br>';
	}

	// Calendar Modules Fields
	$crs = $adb->pquery(
		'SELECT module,userid FROM its4you_calendar_modulefields WHERE module=? and (start_field=? or end_field=? or subject_fields like ?)',
		array($modname, $fldname, $fldname, '%'.$fldname.'%')
	);
	if ($crs && $adb->num_rows($crs)>0) {
		$ret .= $spanStart.getTranslatedString('cbCalendar', 'cbCalendar').' Module Fields</span><br>';
		$rdo['found'] = true;
		$rdo['where'][] = 'Calendar Modules Fields';
	} else {
		$ret .= $i18n['calendar_nf'].'<br>';
	}

	// Webforms
	$crs = $adb->pquery(
		'SELECT name
		FROM vtiger_webforms_field
		INNER JOIN vtiger_webforms on vtiger_webforms.id=vtiger_webforms_field.webformid
		WHERE fieldname LIKE ? and targetmodule=?',
		array($fldname.':%', $modname)
	);
	if ($crs && $adb->num_rows($crs)>0) {
		while ($fnd=$adb->fetch_array($crs)) {
			$ret .= $spanStart.getTranslatedString('Webforms', 'Webforms').' :: '.$fnd['name'].$spanEnd;
		}
		$rdo['found'] = true;
		$rdo['where'][] = 'Webforms';
	} else {
		$ret .= $i18n['webforms_nf'].'<br>';
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
			$ret .= $spanStart.$i18n['cl_mapping'].$spanEnd;
			$rdo['found'] = true;
			$rdo['where'][] = 'Leads';
		} else {
			$ret .= $i18n['cl_mapping_nf'].'<br>';
		}
	}
	$rdo['message'] = $ret;
	return $rdo;
}
?>
