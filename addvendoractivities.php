<?
include_once('vtlib/Vtiger/Module.php');
$moduleInstance = Vtiger_Module::getInstance('Vendors');
$qtModule = Vtiger_Module::getInstance('Calendar');
$relationLabel = 'Activities';
$moduleInstance->setRelatedList($qtModule , $relationLabel, Array("ADD"),'get_activities');

$relationLabel = 'Activities History';
$moduleInstance->setRelatedList($qtModule , $relationLabel, Array("ADD"),'get_history');
echo 'Relation Added SAuccessfully ';
?>