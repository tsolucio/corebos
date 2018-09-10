<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TSolucio::coreBOS Customizations</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');
$fldname = empty($_REQUEST['fieldname']) ? '' : vtlib_purify($_REQUEST['fieldname']);
$modname = empty($_REQUEST['modulename']) ? '' : vtlib_purify($_REQUEST['modulename']);
?>
<h2>Script that checks if a field is being used in the database.</h2>
<form submit="checkFieldUsage.php">
	Introduce field name: <input name="fieldname" value="<?php echo $fldname; ?>" /><br>
	Introduce module name: <input name="modulename" value="<?php echo $modname; ?>" /><br>
	<input type="submit" value=" CHECK " />
</form>
<?php
if (!empty($fldname) && !empty($modname)) {
	$mod = Vtiger_Module::getInstance($modname);
	if ($mod) {
		$field = Vtiger_Field::getInstance($fldname, $mod);
		if ($field) {
			// Workflow Conditions
			$crs = $adb->query("SELECT workflow_id,summary
				FROM `com_vtiger_workflows`
				WHERE test like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in workflow condtions: ";
					echo $fnd['workflow_id'].' :: '.$fnd['summary'];
					echo "<br>";
				}
			} else {
				echo "Field not found in workflow condtions<br>";
			}
			// Workflow Tasks
			$crs = $adb->query("SELECT workflow_id,task_id,summary
				FROM `com_vtiger_workflowtasks`
				WHERE task like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in workflow (".$fnd['workflow_id'].") task (".$fnd['task_id']."): ";
					echo $fnd['summary'];
					echo "<br>";
				}
			} else {
				echo "Field not found in workflow tasks<br>";
			}
			// Custom View Columns
			$crs = $adb->query("SELECT vtiger_customview.viewname
				FROM `vtiger_cvcolumnlist`
				INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in custom view columns: ".$fnd['viewname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in custom view columns<br>";
			}
			// Custom View Conditions
			$crs = $adb->query("SELECT vtiger_customview.viewname
				FROM `vtiger_cvadvfilter`
				INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvadvfilter.cvid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in custom view conditions: ".$fnd['viewname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in custom view conditions<br>";
			}
			// Custom View Date Filters
			$crs = $adb->query("SELECT vtiger_customview.viewname
				FROM `vtiger_cvstdfilter`
				INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvstdfilter.cvid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in custom view date conditions: ".$fnd['viewname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in custom view date conditions<br>";
			}
			// Email Templates
			$crs = $adb->query("SELECT templateid,templatename
				FROM `vtiger_emailtemplates`
				WHERE subject like '%$fldname%' or body like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Email Template: ".$fnd['templateid'].' :: '.$fnd['templatename'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Email Templates<br>";
			}
			// Report Fields
			$crs = $adb->query("SELECT vtiger_report.reportid,vtiger_report.reportname
				FROM `vtiger_selectcolumn`
				INNER JOIN vtiger_report on vtiger_report.queryid=vtiger_selectcolumn.queryid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Report (columns): ".$fnd['reportid'].' :: '.$fnd['reportname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Report Columns<br>";
			}
			// Report Date Filters
			$crs = $adb->query("SELECT vtiger_report.reportid,vtiger_report.reportname
				FROM `vtiger_reportdatefilter`
				INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportdatefilter.datefilterid
				WHERE datecolumnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Report (Date Filters): ".$fnd['reportid'].' :: '.$fnd['reportname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Report Date Filters<br>";
			}
			// Report Group By
			$crs = $adb->query("SELECT vtiger_report.reportid,vtiger_report.reportname
				FROM `vtiger_reportgroupbycolumn`
				INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportgroupbycolumn.reportid
				WHERE sortcolname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Report (Group By): ".$fnd['reportid'].' :: '.$fnd['reportname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Report Group By<br>";
			}
			// Report Sort By
			$crs = $adb->query("SELECT vtiger_report.reportid,vtiger_report.reportname
				FROM `vtiger_reportsortcol`
				INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsortcol.reportid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Report (Sort By): ".$fnd['reportid'].' :: '.$fnd['reportname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Report Sort By<br>";
			}
			// Report Summary
			$crs = $adb->query("SELECT vtiger_report.reportid,vtiger_report.reportname
				FROM `vtiger_reportsummary`
				INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsummary.reportsummaryid
				WHERE columnname like '%$fldname%'");
			if ($crs && $adb->num_rows($crs)>0) {
				while ($fnd=$adb->fetch_array($crs)) {
					echo "Field found in Report (Summary): ".$fnd['reportid'].' :: '.$fnd['reportname'];
					echo "<br>";
				}
			} else {
				echo "Field not found in Report Summary<br>";
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
				$crs = $adb->pquery("SELECT 1
					FROM `vtiger_convertleadmapping`
					WHERE $searchon = ?", array($field->id));
				if ($crs && $adb->num_rows($crs)>0) {
					echo "Field found in Lead Conversion Mapping<br>";
				} else {
					echo "Field not found in Lead Conversion Mapping<br>";
				}
			}
		} else {
			echo "<br><b>Field $fldname could not be found on module $modname!</b><br>";
		}
	} else {
		echo "<br><b>Module $modname could not be found!</b><br>";
	}
}
?>
