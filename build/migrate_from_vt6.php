<?php
$moduleTitle="TSolucio::coreBOS Customizations: migrate from vtiger CRM 6.x";
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>';
echo '</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">';
echo '<table width=100% border=0><tr><td align=left>';
echo '</td><td align=center style="background-image: url(\'vtlogowmg.png\'); background-repeat: no-repeat; background-position: center;">';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="corebos.org"><img src="include/install/images/app_logo.png" alt="coreBOS" title="coreBOS" border=0></a>';
echo '</td></tr></table>';
echo '<hr style="height: 1px">';

// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
include_once 'vtlib/Vtiger/Cron.php';
require_once('modules/com_vtiger_workflow/include.inc');
require_once('modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc');
require_once('modules/com_vtiger_workflow/VTEntityMethodManager.inc');
require_once('include/Webservices/Utils.php');
@include_once('include/events/include.inc');
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');

$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(1); // admin
if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
	$current_language = $_SESSION['authenticated_user_language'];
} else {
	if(!empty($current_user->language)) {
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

function ExecuteQuery($query,$params=array()) {
	global $adb,$log;
	global $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;

	$status = $adb->pquery($query,$params);
	$query_count++;
	if(is_object($status)) {
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

function deleteWorkflow($wfid) {
	ExecuteQuery("DELETE FROM com_vtiger_workflowtasks WHERE workflow_id = $wfid");
	ExecuteQuery("DELETE FROM com_vtiger_workflows WHERE workflow_id = $wfid");
}

echo "<table width=80% align=center border=1>";

$force = (isset($_REQUEST['force']) ? 1 : 0);

$cver = vtws_getVtigerVersion();
if ($cver=='6.1.0' or $force) {
	putMsg('<h2>** Starting Migration from 6.1 **</h2>');
	include 'build/migrate6/migrate_from_vt61.php';
}

$cver = vtws_getVtigerVersion();
if ($cver=='6.0.0' or $force) {
	putMsg('<h2>** Starting Migration from 6.0 **</h2>');
	include 'build/migrate6/migrate_from_vt60.php';
}

ExecuteQuery("DELETE FROM vtiger_def_org_share WHERE vtiger_def_org_share.tabid not in (select tabid from vtiger_tab)");
ExecuteQuery("update vtiger_users set theme='softed'");
ExecuteQuery("update vtiger_version set old_version='5.4.0', current_version='5.5.0' where id=1");

// Recalculate permissions  RecalculateSharingRules
RecalculateSharingRules();


?>
</table>
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
	<?php
		foreach($failure_query_array as $failed_query)
			echo '<br><font color="red">'.$failed_query.'</font>';
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
