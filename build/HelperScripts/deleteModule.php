<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>coreBOS Utility loader</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $adb;

$modname = 'CalendarSync';
$module = Vtiger_Module::getInstance($modname);

if ($module) {
	$ev = new VTEventsManager($adb);
	$ev->unregisterHandler('CalendarSyncHandler');
	$module->deleteRelatedLists();
	$module->deleteLinks();
	$module->deinitWebservice();
	$module->delete();
	echo "<b>Module $modname EXTERMINATED!</b><br>";
} else {
	echo "<b>Failed to find $modname module.</b><br>";
}

echo '</body></html>';

?>
