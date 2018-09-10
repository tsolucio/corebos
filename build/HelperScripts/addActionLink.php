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

$mod_acc = Vtiger_Module::getInstance('Accounts');
$mod_acc->addLink('DETAILVIEWBASIC', 'Add Ticket', 'index.php?module=HelpDesk&action=EditView&parent_id=$RECORD$');

?>