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
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Module.php';
global $current_user, $adb;
$fldname = vtlib_purify($_GET['fieldname']);
$modname = vtlib_purify($_GET['modulename']);
$mod = Vtiger_Module::getInstance($modname);
$field = Vtiger_Field::getInstance($fldname,$mod);

// Workflow Conditions
$crs = $adb->pquery('SELECT workflow_id,summary FROM `com_vtiger_workflows` WHERE test like ?', array('%'.$fldname.'%'));
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in workflow condtions: ';
		echo $fnd['workflow_id']. ' / ' .$fnd['summary'];
		echo '<br>';
	}
} else {
	echo 'Field not found in workflow condtions<br>';
}

// Workflow Tasks
$crs = $adb->pquery('SELECT workflow_id,task_id,summary FROM `com_vtiger_workflowtasks` WHERE task like  ?', array('%'.$fldname.'%'));
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in workflow ('.$fnd['workflow_id'].') task ('.$fnd['task_id'].'): ';
		echo $fnd['summary'];
		echo '<br>';
	}
} else {
	echo 'Field not found in workflow tasks<br>';
}

// Custom View Columns
$crs = $adb->pquery(
	'SELECT vtiger_customview.viewname FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in custom view columns: '.$fnd['viewname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in custom view columns<br>';
}

// Custom View Conditions
$crs = $adb->pquery(
	'SELECT vtiger_customview.viewname FROM `vtiger_cvadvfilter` INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvadvfilter.cvid WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in custom view conditions: '.$fnd['viewname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in custom view conditions<br>';
}

// Custom View Date Filters
$crs = $adb->pquery(
	'SELECT vtiger_customview.viewname FROM `vtiger_cvstdfilter` INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvstdfilter.cvid WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in custom view date conditions: '.$fnd['viewname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in custom view date conditions<br>';
}

// Email Templates
$crs = $adb->pquery('SELECT templateid,templatename FROM `vtiger_emailtemplates` WHERE subject like ? or body like ?', array('%'.$fldname.'%', '%'.$fldname.'%'));
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Email Template: '.$fnd['templateid'].' :: '.$fnd['templatename'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Email Templates<br>';
}

// Report Fields
$crs = $adb->pquery(
	'SELECT vtiger_report.reportid,vtiger_report.reportname
		FROM vtiger_selectcolumn
		INNER JOIN vtiger_report on vtiger_report.queryid=vtiger_selectcolumn.queryid
		WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Report (columns): '.$fnd['reportid'].' :: '.$fnd['reportname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Report Columns<br>';
}

// Report Date Filters
$crs = $adb->pquery(
	'SELECT vtiger_report.reportid,vtiger_report.reportname
		FROM `vtiger_reportdatefilter`
		INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportdatefilter.datefilterid
		WHERE datecolumnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Report (Date Filters): '.$fnd['reportid'].' :: '.$fnd['reportname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Report Date Filters<br>';
}

// Report Group By
$crs = $adb->pquery(
	'SELECT vtiger_report.reportid,vtiger_report.reportname
		FROM `vtiger_reportgroupbycolumn`
		INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportgroupbycolumn.reportid
		WHERE sortcolname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Report (Group By): '.$fnd['reportid'].' :: '.$fnd['reportname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Report Group By<br>';
}

// Report Sort By
$crs = $adb->pquery(
	'SELECT vtiger_report.reportid,vtiger_report.reportname
		FROM `vtiger_reportsortcol`
		INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsortcol.reportid
		WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Report (Sort By): '.$fnd['reportid'].' :: '.$fnd['reportname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Report Sort By<br>';
}

// Report Summary
$crs = $adb->pquery(
	'SELECT vtiger_report.reportid,vtiger_report.reportname
		FROM `vtiger_reportsummary`
		INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsummary.reportsummaryid
		WHERE columnname like ?',
	array('%'.$fldname.'%')
);
if ($crs and $adb->num_rows($crs)>0) {
	while ($fnd=$adb->fetch_array($crs)) {
		echo 'Field found in Report (Summary): '.$fnd['reportid'].' :: '.$fnd['reportname'];
		echo '<br>';
	}
} else {
	echo 'Field not found in Report Summary<br>';
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
		echo 'Field found in Lead Conversion Mapping<br>';
	} else {
		echo 'Field not found in Lead Conversion Mapping<br>';
	}
}
?>
