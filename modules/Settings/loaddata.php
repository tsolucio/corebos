<!--*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************
 *  Module       : OOMerge GENDOC
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************-->


<?php
$Vtiger_Utils_Log = true;
require_once 'vtlib/Vtiger/Module.php';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');
$fldname = vtlib_purify($_GET['fieldname']);
$modname = vtlib_purify($_GET['modulename']);
?>


<?php

if (!empty($fldname) and !empty($modname)) {
    $mod = Vtiger_Module::getInstance($modname);
    if ($mod) {
        $field = Vtiger_Field::getInstance($fldname, $mod);
        if ($field) {

            // Workflow Conditions
            $crs = $adb->pquery('SELECT workflow_id,summary FROM `com_vtiger_workflows` WHERE test like ?', array('%'.$fldname.'%'));
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['wf_conditions_found'];
                    echo $fnd['workflow_id']. " / " .$fnd['summary'];
                    echo $mod_strings['break'];
                }
            } else {

                echo $mod_strings['wf conditions'];

            }


            // Workflow Tasks
            $crs = $adb->pquery(
                'SELECT workflow_id,task_id,summary
                FROM `com_vtiger_workflowtasks` WHERE task like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo  $mod_strings['wf_tasks_found']."(".$fnd['workflow_id'].") task (".$fnd['task_id']."): ";
                    echo $fnd['summary'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['wf_tasks'];
            }


                // Custom View Columns
            $crs = $adb->pquery(
                'SELECT vtiger_customview.viewname
                FROM `vtiger_cvcolumnlist`
                INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvcolumnlist.cvid 
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['cv_column'].$fnd['viewname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['cv_column_nf'];
            }

            // Custom View Conditions
            $crs = $adb->pquery(
                'SELECT vtiger_customview.viewname
                FROM `vtiger_cvadvfilter`
                INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvadvfilter.cvid
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['cv_advfilter'].$fnd['viewname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['cv_advfilter_nf'];
            }

            // Custom View Date Filters
            $crs = $adb->pquery(
                'SELECT vtiger_customview.viewname
                FROM `vtiger_cvstdfilter`
                INNER JOIN vtiger_customview on vtiger_customview.cvid=vtiger_cvstdfilter.cvid
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['cv_stdfilter'].$fnd['viewname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['cv_stdfilter_nf'];
            }

            // Email Templates
            $crs = $adb->pquery(
                'SELECT templateid,templatename
                FROM `vtiger_emailtemplates`
                WHERE subject like ? or body like ?', array('%'.$fldname.'%', '%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['email_templates'].$fnd['templateid'].' :: '.$fnd['templatename'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['email_templates_nf'];
            }

            // Report Fields
            $crs = $adb->pquery(
                'SELECT vtiger_report.reportid,vtiger_report.reportname
                FROM `vtiger_selectcolumn`
                INNER JOIN vtiger_report on vtiger_report.queryid=vtiger_selectcolumn.queryid
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['select_column'].$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['select_column_nf'];
            }

            // Report Date Filters
            $crs = $adb->pquery(
                'SELECT vtiger_report.reportid,vtiger_report.reportname
                FROM `vtiger_reportdatefilter`
                INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportdatefilter.datefilterid
                WHERE datecolumnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['report_dtfilter'].$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['report_dtfilter_nf'];
            }

            // Report Group By
            $crs = $adb->pquery(
                'SELECT vtiger_report.reportid,vtiger_report.reportname
                FROM `vtiger_reportgroupbycolumn`
                INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportgroupbycolumn.reportid
                WHERE sortcolname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['report'].$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['report_nf'];
            }

            // Report Sort By
            $crs = $adb->pquery(
                'SELECT vtiger_report.reportid,vtiger_report.reportname
                FROM `vtiger_reportsortcol`
                INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsortcol.reportid
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['report_sort'].$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['report_sort_nf'];
            }

            // Report Summary
            $crs = $adb->pquery(
                'SELECT vtiger_report.reportid,vtiger_report.reportname
                FROM `vtiger_reportsummary`
                INNER JOIN vtiger_report on vtiger_report.reportid=vtiger_reportsummary.reportsummaryid
                WHERE columnname like ?', array('%'.$fldname.'%')
            );
            if ($crs and $adb->num_rows($crs)>0) {
                while ($fnd=$adb->fetch_array($crs)) {
                    echo $mod_strings['report_summary'].$fnd['reportid'].' :: '.$fnd['reportname'];
                    echo $mod_strings['break'];
                }
            } else {
                echo $mod_strings['report_summary_nf'];
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
                $crs = $adb->pquery(
                    "SELECT 1
                    FROM `vtiger_convertleadmapping`
                    WHERE $searchon = ?", array($field->id)
                );
                if ($crs and $adb->num_rows($crs)>0) {
                    echo $mod_strings['cl_mapping'];
                } else {
                    echo $mod_strings['cl_mapping_nf'];
                }
            }
        } else {
            echo $mod_strings['fld_nf'];
        }
    } else {
        echo $mod_strings['mod_nf'];
    }
}


?>

