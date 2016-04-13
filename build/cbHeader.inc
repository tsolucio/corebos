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

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
require_once('config.inc.php');
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
global $current_user,$adb;
set_time_limit(0);
ini_set('memory_limit','1024M');

$current_user = new Users();
$current_user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
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

set_time_limit(0);
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

function deletePicklistValues($values,$tableName,$moduleName) {
	global $adb, $default_charset;
	$tabid = getTabid($moduleName);
	for($i=0;$i<count($values);$i++){
		$sql = "select picklist_valueid from vtiger_$tableName where $tableName=?";
		$result = $adb->pquery($sql, array($values[$i]));
		$origPicklistID = $adb->query_result($result, 0, "picklist_valueid");
	
		$values[$i] = Array('encodedValue'=>htmlentities($values[$i], ENT_QUOTES, $default_charset),'rawValue'=>$values[$i]);
		$sql = "delete from vtiger_$tableName where $tableName=?";
		$adb->pquery($sql, array($values[$i]['encodedValue']));
		$sql = "delete from vtiger_role2picklist where picklistvalueid=?";
		$adb->pquery($sql, array($origPicklistID));
		$sql = "DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?";
		$adb->pquery($sql, array($values[$i]['encodedValue'], $tableName, $tabid));
	}
}
function deleteAllPicklistValues($tableName,$moduleName) {
	global $adb, $default_charset;
	$tabid = getTabid($moduleName);
	$result = $adb->query("select picklist_valueid,$tableName from vtiger_$tableName");
	while ($plrow=$adb->fetch_array($result)) {
		$origPicklistID = $plrow['picklist_valueid'];
		$value = htmlentities($plrow[$tableName], ENT_QUOTES, $default_charset);
		$sql = "delete from vtiger_role2picklist where picklistvalueid=?";
		$adb->pquery($sql, array($origPicklistID));
		$sql = "DELETE FROM vtiger_picklist_dependency WHERE sourcevalue=? AND sourcefield=? AND tabid=?";
		$adb->pquery($sql, array($value, $tableName, $tabid));
	}
	$adb->query("delete from vtiger_$tableName");  // delete all entries
}

// Change block/field order and show/hide state
function setBlocksAndFields($moduleName, $blocks) {
	global $adb;
	$module = VTiger_Module::getInstance($moduleName);
	// Move all fields to position 1000
	$adb->query("update vtiger_field set sequence=1000 where tabid={$module->id}");
	$blockIndex = 0;
	foreach($blocks as $blockLabel=>$fields) {
		$blockIndex++;
		// Show block and set order
		$block = VTiger_Block::getInstance($blockLabel, $module);
		$adb->query("update vtiger_blocks set sequence={$blockIndex} where blockid={$block->id}");
		$fieldIndex = 0;
		foreach($fields as $fieldLabel) {
			$fieldIndex++;
			// Set block and order of field
			$field = VTiger_Field::getInstance($fieldLabel, $module);
			$adb->query("update vtiger_field set block={$block->id}, sequence={$fieldIndex} where fieldid={$field->id}");
		}
	}
	// Hide all fields at position 1000
	$adb->query("update vtiger_field set presence=1 where tabid={$module->id} and sequence=1000");
}

function setQuickCreateFields($moduleName,$qcfields) {
	global $adb;
	$module = VTiger_Module::getInstance($moduleName);
	$order = 1;
	foreach ($qcfields as $fldname) {
		$field = VTiger_Field::getInstance($fldname, $module);
		$adb->query("UPDATE vtiger_field SET quickcreate=0,quickcreatesequence=$order WHERE fieldid={$field->id}");
		$order++;
	}
}

?>
<table width=80% align=center border=1>
