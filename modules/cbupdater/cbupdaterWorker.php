<?php
/*************************************************************************************************
* Copyright 2014 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************
*  Module       : cbupdater
*  Version      : 5.5.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

// Turn on debugging level
$Vtiger_Utils_Log = true;

require_once 'include/utils/utils.php';
include_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Package.php');
include_once('modules/cbupdater/cbupdater.php');
require_once("modules/com_vtiger_workflow/include.inc");
require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
global $current_user,$adb,$app_strings;
set_time_limit(0);
ini_set('memory_limit','1024M');

if (empty($current_user) or $current_user->id != 1) {
	$current_user = new Users();
	$current_user->retrieveCurrentUserInfoFromFile(1); // admin
}
if (empty($current_language)) {
	if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '') {
		$current_language = $_SESSION['authenticated_user_language'];
	} else {
		if(!empty($current_user->language)) {
			$current_language = $current_user->language;
		} else {
			$current_language = $default_language;
		}
	}
}
if (empty($app_strings)) $app_strings = return_application_language($current_language);

class cbupdaterWorker {
	var $cbupdid;
	var $cbupd_no;
	var $author = '';
	var $filename;
	var $classname;
	var $execstate = false;
	var $systemupdate = false;
	var $perspective = false;
	var $blocked = false;
	var $execdate;
	var $updError = false;
	var $query_count=0;
	var $success_query_count=0;
	var $failure_query_count=0;
	var $success_query_array=array();
	var $failure_query_array=array();
	
	function __construct() {
		global $adb,$log,$current_user;
		echo "<table width=80% align=center border=1>";
		$reflector = new ReflectionClass(get_class($this));
		$fname = basename($reflector->getFileName(),'.php');
		$cburs = $adb->pquery('select * from vtiger_cbupdater where filename=? and classname=?',
			array($fname,get_class($this)));
		if ($cburs and $adb->num_rows($cburs)>0) {  // it exists, we load it
			$cbu = $adb->fetch_array($cburs);
			$this->cbupdid = $cbu['cbupdaterid'];
			$this->cbupd_no = $cbu['cbupd_no'];
			$this->author = $cbu['author'];
			$this->filename = $cbu['filename'];
			$this->classname = $cbu['classname'];
			$this->execstate = $cbu['execstate'];
			$this->systemupdate = ($cbu['systemupdate']=='1' ? true : false);
			$this->perspective = ((isset($cbu['perspective']) and $cbu['perspective']=='1') ? true : false);
			$this->blocked = ((isset($cbu['blocked']) and $cbu['blocked']=='1') ? true : false);
			$this->execdate = $cbu['execdate'];
			$this->updError = false;
		} else {  // it doesn't exist, we fail because it MUST exist
			$this->sendError();
		}
	}
	
	function applyChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			// do your magic here
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
	
	function undoChange() {
		if ($this->isBlocked()) return true;
		if ($this->hasError()) $this->sendError();
		if ($this->isSystemUpdate()) {
			$this->sendMsg('Changeset '.get_class($this).' is a system update, it cannot be undone!');
		} else {
			if ($this->isApplied()) {
				// undo your magic here
				$this->sendMsg('Changeset '.get_class($this).' undone!');
				$this->markUndone();
			} else {
				$this->sendMsg('Changeset '.get_class($this).' not applied!');
			}
		}
		$this->finishExecution();
	}
	
	function isApplied() {
		return ($this->execstate=='Executed');
	}

	function isSystemUpdate() {
		return $this->systemupdate;
	}

	function isBlocked() {
		return $this->blocked;
	}
	
	function isContinuous() {
		return ($this->execstate=='Continuous');
	}
	
	function hasError() {
		return ($this->updError or empty($this->cbupdid));
	}
	
	function markApplied($stoponerror=true) {
		if ($this->isBlocked() or $this->isContinuous()) return true;
		if ($this->hasError() and $stoponerror) $this->sendError();
		global $adb,$log;
		$adb->pquery('update vtiger_cbupdater set execstate=?,execdate=CURDATE() where cbupdaterid=?', array('Executed',$this->cbupdid));
		$this->execstate = 'Executed';
	}
	
	function markUndone($stoponerror=true) {
		if ($this->isBlocked() or $this->isContinuous()) return true;
		if ($this->hasError() and $stoponerror) $this->sendError();
		global $adb,$log;
		$adb->pquery('update vtiger_cbupdater set execstate=?,execdate=NULL where cbupdaterid=?', array('Pending',$this->cbupdid));
		$this->execstate = 'Pending';
	}
	
	function ExecuteQuery($query,$params=array()) {
		global $adb,$log;
	
		$status = $adb->pquery($query,$params);
		$this->query_count++;
		if(is_object($status)) {
			echo '
		<tr width="100%">
		<td width="10%">'.get_class($status).'</td>
		<td width="10%"><span style="color:green"> S </span></td>
		<td width="80%">'.$query.'</td>
		</tr>';
			$success_query_array[$this->success_query_count++] = $query;
			$log->debug("Query Success ==> $query");
		} else {
			echo '
		<tr width="100%">
		<td width="25%">'.$status.'</td>
		<td width="5%"><span style="color:red"> F </span></td>
		<td width="70%">'.$query.'</td>
		</tr>';
			$this->failure_query_array[$this->failure_query_count++] = $query;
			$this->updError = true;
			$log->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
		}
	}
	
	function deleteWorkflow($wfid) {
		$this->ExecuteQuery("DELETE FROM com_vtiger_workflowtasks WHERE workflow_id=?",array($wfid));
		$this->ExecuteQuery("DELETE FROM com_vtiger_workflows WHERE workflow_id=?", array($wfid));
	}

	function installManifestModule($module) {
		$package = new Vtiger_Package();
		ob_start();
		$rdo = $package->importManifest("modules/$module/manifest.xml");
		$out = ob_get_contents();
		ob_end_clean();
		$this->sendMsg($out);
		if ($rdo) $this->sendMsg("$module installed!");
		else $this->sendMsgError("ERROR installing $module!");
	}
	
	function isModuleInstalled($module) {
		global $adb;
		$tabrs = $adb->pquery('select count(*) from vtiger_tab where name=?',array($module));
		return ($tabrs and $adb->query_result($tabrs, 0,0)==1);
	}
	
	function sendMsg($msg) {
		echo '<tr width="100%"><td colspan=3>'.$msg.'</td></tr>';
	}

	function sendMsgError($msg) {
		echo '<tr width="100%"><td colspan=3><span style="color:red">'.$msg.'</span></td></tr>';
		$this->updError = true;
	}
	
	function sendError() {
		$this->updError = true;
		echo '<tr width="100%"><td colspan=3<span style="color:red">ERROR: Class called without update record in application!!</span></td></tr></table>';
		die();
	}
	
	function finishExecution() {
		echo '</table>';
		if (count($this->failure_query_array)>0) {
			echo <<<EOT
<br /><br />
<b style="color:#FF0000">Failed Queries Log</b>
<div id="failedLog" style="border:1px solid #666666;width:90%;position:relative;height:200px;overflow:auto;left:5%;top:10px;">
EOT;
			foreach($this->failure_query_array as $failed_query)
				echo '<br><span style="color:red">'.$failed_query.'</span>';
			echo '</div>';
		}
		echo <<<EOT
<br /><br />
<table width="35%" border="0" cellpadding="5" cellspacing="0" align="center" class="small">
	<tr>
	<td width="75%" align="right" nowrap>Total Number of queries executed : </td>
	<td width="25%" align="left"><b>{$this->query_count}</b></td>
	</tr>
	<tr>
	<td align="right">Queries Successed : </td>
	<td align="left"><b style="color:#006600;">{$this->success_query_count}</b></td>
	</tr>
	<tr>
	<td align="right">Queries Failed : </td>
	<td align="left"><b style="color:#FF0000;">{$this->failure_query_count}</b></td>
	</tr>
</table>
EOT;
	}
}
?>