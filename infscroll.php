<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');

$page = $_REQUEST['page'];
$prs = $adb->pquery("select * from vtiger_account order by accountname limit $page,10",array());
$pcontent='';
while ($a = $adb->fetch_array($prs)) {
	$pcontent .= $a['accountname'].'<br>';
}
echo $pcontent;
