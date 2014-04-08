<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/utils/VtlibUtils.php');
require_once('vtlib/Vtiger/Cron.php');
global $adb;
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
    $cronTask = Vtiger_Cron::getInstanceById($_REQUEST['record']);
    $cronTask->updateStatus($_REQUEST['status']);
    if($_REQUEST['timevalue'] != '') {

        if($_REQUEST['time'] == 'min') {

            $time = $_REQUEST['timevalue']*60;
        }
        else {
            $time = $_REQUEST['timevalue']*60*60;
        }
        $cronTask->updateFrequency($time);
    }
}
$loc = "Location: index.php?action=CronTasksAjax&file=ListCronJobs&module=CronTasks&directmode=ajax";
header($loc);
?>
