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

require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Module.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit', '1024M');

$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
if (isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if (!empty($current_user->language)) {
		$current_language = $current_user->language;
	} else {
		$current_language = $default_language;
	}
}
$app_strings = return_application_language($current_language);

$query_count=0;
$success_query_count=0;
$failure_query_count=0;
$success_query_array=array();
$failure_query_array=array();

function ExecuteQuery($query) {
	global $adb,$log;
	global $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;

	$status = $adb->query($query);
	$query_count++;
	if (is_object($status)) {
		echo '
		<tr width="100%">
		<td width="10%">'.get_class($status).'</td>
		<td width="10%"><font color="green"> S </font></td>
		<td width="80%">'.$query.'</td>
		</tr>';
		$success_query_array[$success_query_count++] = $query;
		$log->debug("Query Success ==> $query");
	} else {
		echo '
		<tr width="100%">
		<td width="25%">'.$status.'</td>
		<td width="5%"><font color="red"> F </font></td>
		<td width="70%">'.$query.'</td>
		</tr>';
		$failure_query_array[$failure_query_count++] = $query;
		$log->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}
function putMsg($msg) {
	echo '<tr width="100%"><td colspan=3>'.$msg.'</td></tr>';
}

echo "<table width=80% align=center border=1>";

$package = new Vtiger_Package();
ob_start();
$rdo = $package->importManifest("modules/cbupdater/manifest.xml");
$out = ob_get_contents();
ob_end_clean();
putMsg($out);
if ($rdo) {
	putMsg("$module installed: <a href='index.php?module=cbupdater&action=getupdates'>proceed to the rest of the updates by clicking here</a>");
} else {
	putMsg("ERROR installing $module!");
}

?>
</table>
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
	<?php
	foreach ($failure_query_array as $failed_query) {
		  echo '<br><font color="red">'.$failed_query.'</font>';
	}
	?>
</div>
<br /><br />
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
   <tr>
	<td width="75%" align="right" nowrap>
		Total Number of queries executed : 
	</td>
	<td width="25%" align="left">
		<b><?php echo $query_count;?> </b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Successed : 
	</td>
	<td align="left">
		<b style="color:#006600;">
		<?php echo $success_query_count;?>
		</b>
	</td>
   </tr>
   <tr>
	<td align="right">
		Queries Failed : 
	</td>
	<td align="left">
		<b style="color:#FF0000;">
		<?php echo $failure_query_count ;?>
			</b>
	</td>
   </tr>
</table>
</body>
</html>
