<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/utils/utils.php');

/* Function to get a list of modules that is supporting Customer Portal
 */
function cp_getPortalModuleinfo(){
 	global $adb;

	$query = $adb->query("SELECT vtiger_customerportal_tabs.*, vtiger_customerportal_prefs.prefvalue, vtiger_tab.name from vtiger_customerportal_tabs
							INNER JOIN vtiger_customerportal_prefs ON vtiger_customerportal_prefs.tabid = vtiger_customerportal_tabs.tabid and vtiger_customerportal_prefs.prefkey='showrelatedinfo'
							INNER JOIN vtiger_tab ON vtiger_customerportal_tabs.tabid = vtiger_tab.tabid and vtiger_tab.presence = 0 ORDER BY vtiger_customerportal_tabs.sequence");
	$rows = $adb->num_rows($query);
	for($i = 0;$i < $rows; $i++){
		$portalmodules[$i+1]['tabid']  = $adb->query_result($query,$i,'tabid');
		$portalmodules[$i+1]['visible']  = $adb->query_result($query,$i,'visible');
		$portalmodules[$i+1]['sequence'] = $i+1;
		$portalmodules[$i+1]['name'] = getTranslatedString($adb->query_result($query,$i,'name'));
		$portalmodules[$i+1]['value'] = $adb->query_result($query,$i,'prefvalue');
	}
	return $portalmodules;
}

/* Function to save Advanced info fro Customer Portal
 */
function cp_saveCustomerPortalSettings($input) {
	global $adb;
	$portalmodules = cp_getPortalModuleinfo();
	for($i=1;$i<=count($portalmodules);$i++) {
		$modules = str_replace(" ","_",$portalmodules[$i]['name']);
		$view = $input['view_'.$modules];
		$visible = $input['enable_disable_'.$modules];
		$sequence = $input['seq_'.$modules];
		$tabid = $portalmodules[$i]['tabid'];
		if($view == 'showall'){
			$adb->pquery("UPDATE vtiger_customerportal_prefs SET prefvalue = 1  WHERE prefkey = 'showrelatedinfo' and tabid = ?", array($tabid));
		}else {
			$adb->pquery("UPDATE vtiger_customerportal_prefs SET prefvalue = 0  WHERE prefkey = 'showrelatedinfo' and tabid = ?", array($tabid));
		}
		if($visible == 'on' ) {
			$updatevisibility = $adb->pquery("UPDATE vtiger_customerportal_tabs SET visible = 1 WHERE tabid = ?", array($tabid));
		} else {
			$updatevisibility = $adb->pquery("UPDATE vtiger_customerportal_tabs SET visible = 0 WHERE tabid = ?", array($tabid));
		}
		$adb->pquery("UPDATE vtiger_customerportal_tabs set sequence=? WHERE tabid = ?", array($sequence, $tabid));
	}

	//user update
	$userid = $input['userid'];
	$adb->pquery("UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = 'userid' and tabid = 0", array($userid));

	//update Group
	$defaultAssignee = $input['defaultAssignee'];
	$adb->pquery("UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = 'defaultAssignee' and tabid = 0", array($defaultAssignee));

}

/*	It gives you a list of users
 */
function cp_getUsers(){
	global $adb;
	$res = $adb->query("SELECT id,user_name,last_name,first_name from vtiger_users WHERE status='Active'");
	$norows = $adb->num_rows($res);
	$users = array();
	for($i = 0;$i < $norows;$i++) {
		$users[$i]['id'] = $adb->query_result($res,$i,'id');
		$users[$i]['name'] = getFullNameFromQResult($res, $i, 'Users');
	}
	return $users;
}

/* Function to get the customer portal user id
 */
function cp_getCurrentUser() {
	global $adb;
	$res = $adb->query("SELECT prefvalue FROM vtiger_customerportal_prefs WHERE prefkey = 'userid' AND tabid = 0");
	$userid = $adb->query_result($res,0,'prefvalue');
	if($userid != '') {
		return $userid;
	}
	return false;
}

/* Function to get the customer portal default assignee
 */
function cp_getCurrentDefaultAssignee() {
	global $adb;
	$res = $adb->query("SELECT prefvalue FROM vtiger_customerportal_prefs WHERE prefkey = 'defaultassignee' AND tabid = 0");
	$defaultassignee = $adb->query_result($res,0,'prefvalue');
	if($defaultassignee != '') {
		return $defaultassignee;
	}
	return false;
}

/*	It gives you a list of users Groups
 *
 */
function cp_getUserGroups() {
	global $adb;
	$res = $adb->query("SELECT groupid,groupname from vtiger_groups");
	$norows = $adb->num_rows($res);
	for($i = 0;$i < $norows;$i++) {
		$groups[$i]['groupid'] = $adb->query_result($res,$i,'groupid');
		$groups[$i]['groupname'] = $adb->query_result($res,$i,'groupname');
	}
	return $groups;
}
?>