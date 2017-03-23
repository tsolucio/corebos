<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>TSolucio::coreBOS Tests</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>
</head><body style="font-size: 14px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Module.php');
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');
$current_user = Users::getActiveAdminUser();

$recs = $adb->query('SELECT max(crmid),setype FROM `vtiger_crmentity` where deleted=0 group by setype');
echo "<table border=1 width='80%' align='center'><tr><th>EntityID</th><th>Related Account</th><th>Related Contact</th></tr>";
while ($rec = $adb->fetch_row($recs)) {
	echo "<tr>";
	echo "<td><a href='index.php?module=".$rec['setype']."&action=DetailView&record=".$rec['crmid']."'>".$rec['crmid'].' ('.$rec['setype'].') </a></td>';
	$a = getRelatedAccountContact($rec['crmid'],'Accounts');
	echo "<td align='center'>";
	if ($a) {
		echo "<a href='index.php?module=Accounts&action=DetailView&record=$a'>".$a.'</a>';
	} else {
		echo "-";
	}
	echo "</td>";
	$c = getRelatedAccountContact($rec['crmid'],'Contacts');
	echo "<td align='center'>";
	if ($c) {
		echo "<a href='index.php?module=Contacts&action=DetailView&record=$c'>".$c.'</a>';
	} else {
		echo "-";
	}
	echo "</td>";
	echo "</tr>";
}
?>
