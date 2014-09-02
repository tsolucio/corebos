<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Event.php';
include_once('modules/LoggingConf/LoggingUtils.php');

global $adb;
 
$tabids=explode('-',  $_REQUEST['tabidvalues']);

foreach($tabids as $tabid)
{
$query=$adb->query("insert into vtiger_loggingconfiguration(tabid) values ($tabid)  ");
$moduleName=getTabname($tabid);
$moduleInstance = Vtiger_Module::getInstance($moduleName);
$moduleInstanceLog=  Vtiger_Module::getInstance('Entitylog');
$field7 = Vtiger_Field::getInstance("relatedto",$moduleInstanceLog);
$field7->setRelatedModules(Array($moduleName));

    Vtiger_Event::register($moduleInstance, 'vtiger.entity.beforesave', 'HistoryLogHandler', 'include/utils/HLogHandler.php');
    Vtiger_Event::register($moduleInstance, 'vtiger.entity.aftersave', 'HistoryLogHandler', 'include/utils/HLogHandler.php');

    $newdtid = $adb->getUniqueID("vtiger_relatedlists");
    if ($adb->pquery('insert into vtiger_relatedlists values (?,?,?,?,?,?,?,?)', array($newdtid, $moduleInstance->id, 0, 'get_log_history', 1, 'History Log', 0, ''))) {
        echo "Setting Related list ...DONE";
    } else {
        echo "Setting Related list ... NOT DONE";
    }

}
?>
