<?php
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Module.php');
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');
$fldname = $_GET['empid'];
?>


<?php

// Workflow Conditions
       $crs = $adb->query("SELECT workflow_id,summary
                FROM `com_vtiger_workflows`
                WHERE test like '%$fldname%'");
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {

        echo $fnd['workflow_id']. " / " .$fnd['summary'];
        echo "<br>";
                }
            } else {

                echo "Field not found in workflow condtions<br>";

            }


            // Workflow Tasks
            $crs = $adb->query("SELECT workflow_id,task_id,summary
                FROM `com_vtiger_workflowtasks`
                WHERE task like '%$fldname%'");
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
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
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo "Field found in Report (Summary): ".$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo "<br>";
                }
            } else {
                echo "Field not found in Report Summary<br>";
            }




?>

