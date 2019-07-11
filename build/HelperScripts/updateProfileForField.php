<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');

$mods2update = array('cbCompany');
foreach ($mods2update as $module) {
	$mod = Vtiger_Module::getInstance($module);
	$fields = Vtiger_Field::getAllForModule($mod);
	foreach ($fields as $field) {
		Vtiger_Profile::initForField($field);
	}
}
?>