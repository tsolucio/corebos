<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'include/database/PearDatabase.php';
require_once 'include/utils/utils.php';
require_once 'include/utils/GetUserGroups.php';
include 'config.inc.php';
global $log;

/** To retreive the mail server info resultset for the specified user
  * @param $user -- The user object:: Type Object
  * @returns  the mail server info resultset
 */
function getMailServerInfo($user) {
	global $log, $adb;
	$log->debug("Entering getMailServerInfo(".$user->user_name.") method ...");
	$sql = "select * from vtiger_mail_accounts where status=1 and user_id=?";
	$result = $adb->pquery($sql, array($user->id));
	$log->debug("Exiting getMailServerInfo method ...");
	return $result;
}

/** To get the Role of the specified user
  * @param $userid -- The user Id:: Type integer
  * @returns roleid :: Type String
 */
function fetchUserRole($userid) {
	global $log, $adb;
	$log->debug("Entering fetchUserRole(".$userid.") method ...");
	$key = 'fetchUserRole' . $userid;
	list($roleid,$cached) = VTCacheUtils::lookupCachedInformation($key);
	if ($cached) {
		return $roleid;
	}
	$sql = 'select roleid from vtiger_user2role where userid=?';
	$result = $adb->pquery($sql, array($userid));
	$roleid = $adb->query_result($result, 0, 'roleid');
	VTCacheUtils::updateCachedInformation($key, $roleid);
	$log->debug('Exiting fetchUserRole method ...');
	return $roleid;
}

/** Deprecated. Function to be replaced by getUserProfile()
 * Should be done accross the product
 */
function fetchUserProfileId($userid) {
	global $log;
	$log->debug("Entering fetchUserProfileId(".$userid.") method ...");

	// Look up information in cache first
	$profileid = VTCacheUtils::lookupUserProfileId($userid);

	if ($profileid === false) {
		global $adb;

		$query  = "SELECT profileid FROM vtiger_role2profile WHERE roleid=(SELECT roleid FROM vtiger_user2role WHERE userid=?)";
		$result = $adb->pquery($query, array($userid));

		if ($result && $adb->num_rows($result)) {
			$profileid = $adb->query_result($result, 0, 'profileid');
			// TODO: What if there are multiple profile to one role?
		}

		// Update information to cache for re-use
		VTCacheUtils::updateUserProfileId($userid, $profileid);
	}
	$log->debug('Exiting fetchUserProfileId method ...');
	return $profileid;
}

/** Function to get the lists of groupids releated with an user
 * This function accepts the user id as arguments and
 * returns the groupids related with the user id
 * as a comma seperated string
*/
function fetchUserGroupids($userid) {
	global $log;
	$log->debug("Entering fetchUserGroupids($userid) method ...");
	$focus = new GetUserGroups();
	$focus->getAllUserGroups($userid);
	//Asha: Remove implode if not required and if so, also remove explode functions used at the recieving end of this function
	$groupidlists = implode(',', $focus->user_groups);
	$log->debug('Exiting fetchUserGroupids method ...');
	return $groupidlists;
}

/** Function to get all the vtiger_tab permission for the specified vtiger_profile
  * @param $profileid -- Profile Id:: Type integer
  * @returns  TabPermission Array in the following format:
  * $tabPermission = Array($tabid1=>permission,
  *                        $tabid2=>permission,
  *                                |
  *                        $tabidn=>permission)
 */
function getAllTabsPermission($profileid) {
	global $log,$adb;
	$log->debug("Entering getAllTabsPermission(".$profileid.") method ...");
	$sql = "select tabid, permissions from vtiger_profile2tab where profileid=?";
	$result = $adb->pquery($sql, array($profileid));
	$tab_perr_array = array();
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$tabid= $adb->query_result($result, $i, 'tabid');
		$tab_per= $adb->query_result($result, $i, 'permissions');
		$tab_perr_array[$tabid] = $tab_per;
	}
	$log->debug("Exiting getAllTabsPermission method ...");
	return $tab_perr_array;
}

/** Function to get all the tab permission for the specified profile other than tabid 15
  * @param $profileid -- Profile Id:: Type integer
  * @returns  TabPermission Array in the following format:
  * $tabPermission = Array($tabid1=>permission,
  *                        $tabid2=>permission,
  *                                |
  *                        $tabidn=>permission)
 */
function getTabsPermission($profileid) {
	global $log, $adb;
	$log->debug("Entering getTabsPermission(".$profileid.") method ...");
	$sql = 'select vtiger_profile2tab.tabid,vtiger_profile2tab.permissions from vtiger_profile2tab
		INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_profile2tab.tabid WHERE vtiger_profile2tab.profileid=? AND vtiger_tab.presence=0';
	$result = $adb->pquery($sql, array($profileid));
	$tab_perr_array = array();
	$num_rows = $adb->num_rows($result);
	for ($i = 0; $i < $num_rows; $i++) {
		$tabid = $adb->query_result($result, $i, 'tabid');
		$tab_per = $adb->query_result($result, $i, 'permissions');
		if ($tabid != 3 && $tabid != 16) {
			$tab_perr_array[$tabid] = $tab_per;
		}
	}
	$log -> debug("Exiting getTabsPermission method ...");
	return $tab_perr_array;
}

 /** Function to get all the vtiger_tab standard action permission for the specified vtiger_profile
  * @param $profileid -- Profile Id:: Type integer
  * @returns  Tab Action Permission Array in the following format:
  * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getTabsActionPermission($profileid) {
	global $log,$adb;
	$log->debug("Entering getTabsActionPermission(".$profileid.") method ...");
	$check = array();
	$temp_tabid = array();
	$sql1 = 'select tabid, operation, permissions from vtiger_profile2standardpermissions where profileid=? and tabid not in(16) order by(tabid)';
	$result1 = $adb->pquery($sql1, array($profileid));
	$num_rows1 = $adb->num_rows($result1);
	for ($i=0; $i<$num_rows1; $i++) {
		$tab_id = $adb->query_result($result1, $i, 'tabid');
		if (! in_array($tab_id, $temp_tabid)) {
			$temp_tabid[] = $tab_id;
			$access = array();
		}
		$action_id = $adb->query_result($result1, $i, 'operation');
		$per_id = $adb->query_result($result1, $i, 'permissions');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;
	}
	$log->debug("Exiting getTabsActionPermission method ...");
	return $check;
}

/** Function to get all the vtiger_tab utility action permission for the specified vtiger_profile
  * @param $profileid -- Profile Id:: Type integer
  * @returns  Tab Utility Action Permission Array in the following format:
  * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getTabsUtilityActionPermission($profileid) {
	global $log, $adb;
	$log->debug("Entering getTabsUtilityActionPermission(".$profileid.") method ...");
	$check = array();
	$temp_tabid = array();
	$sql1 = 'select tabid, activityid, permission from vtiger_profile2utility where profileid=? order by(tabid)';
	$result1 = $adb->pquery($sql1, array($profileid));
	$num_rows1 = $adb->num_rows($result1);
	for ($i=0; $i<$num_rows1; $i++) {
		$tab_id = $adb->query_result($result1, $i, 'tabid');
		if (! in_array($tab_id, $temp_tabid)) {
			$temp_tabid[] = $tab_id;
			$access = array();
		}
		$action_id = $adb->query_result($result1, $i, 'activityid');
		$per_id = $adb->query_result($result1, $i, 'permission');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;
	}
	$log->debug("Exiting getTabsUtilityActionPermission method ...");
	return $check;
}

 /**This Function returns the Default Organization Sharing Action Array for all modules whose sharing actions are editable
  * The returned array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>Sharing Action Id,
  *            |
  *      tabidn=>Sharing Acion Id)
  */
function getDefaultSharingEditAction() {
	global $log,$adb;
	$log->debug('Entering getDefaultSharingEditAction() method ...');
	//retreiving the standard permissions
	$copy = array();
	$sql= 'select tabid,permission from vtiger_def_org_share where editstatus=0';
	$result = $adb->pquery($sql, array());
	while ($permissionRow=$adb->fetch_array($result)) {
		$copy[$permissionRow['tabid']] = $permissionRow['permission'];
	}
	$log->debug('Exiting getDefaultSharingEditAction method ...');
	return $copy;
}

/**This Function returns the Default Organization Sharing Action Array for modules with edit status in (0,1)
  * The returned array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>Sharing Action Id,
  *            |
  *      tabidn=>Sharing Acion Id)
  */
function getDefaultSharingAction() {
	global $log, $adb;
	$log->debug('Entering getDefaultSharingAction() method ...');
	//retrieve standard permissions
	$copy = array();
	$sql= 'select tabid, permission from vtiger_def_org_share where editstatus in(0,1)';
	$result = $adb->pquery($sql, array());
	while ($permissionRow=$adb->fetch_array($result)) {
		$copy[$permissionRow['tabid']] = $permissionRow['permission'];
	}
	$log->debug('Exiting getDefaultSharingAction method ...');
	return $copy;
}

/**This Function returns the Default Organisation Sharing Action Array for all modules
  * The result array will be in the following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>SharingAction Id,
  *            |
  *            |
  *            |
  *      tabid3=>SharingAcion Id)
  */
function getAllDefaultSharingAction() {
	global $log,$adb;
	$log->debug('Entering getAllDefaultSharingAction() method ...');
	$copy=array();
	//retreiving the standard permissions
	$sql= 'select tabid, permission from vtiger_def_org_share';
	$result = $adb->pquery($sql, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$tabid=$adb->query_result($result, $i, 'tabid');
		$permission=$adb->query_result($result, $i, 'permission');
		$copy[$tabid]=$permission;
	}
	$log->debug('Exiting getAllDefaultSharingAction method ...');
	return $copy;
}

/** Function to create the role
 * @param $roleName -- Role Name:: Type varchar
 * @param $parentRoleId -- Parent Role Id:: Type varchar
 * @param $roleProfileArray -- Profile to be associated with this role:: Type Array
 * @returns  the Rold Id :: Type varchar
 */
function createRole($roleName, $parentRoleId, $roleProfileArray) {
	global $log,$adb;
	$log->debug('Entering createRole('.$roleName.','.$parentRoleId.','.print_r($roleProfileArray, true).') method ...');
	$parentRoleDetails=getRoleInformation($parentRoleId);
	$parentRoleInfo=$parentRoleDetails[$parentRoleId];
	$roleid_no=$adb->getUniqueId("vtiger_role");
	$roleId='H'.$roleid_no;
	$parentRoleHr=$parentRoleInfo[1];
	$parentRoleDepth=$parentRoleInfo[2];
	$nowParentRoleHr=$parentRoleHr.'::'.$roleId;
	$nowRoleDepth=$parentRoleDepth + 1;

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates($roleId);

	//Inserting role into db
	$query="insert into vtiger_role values(?,?,?,?)";
	$qparams = array($roleId,$roleName,$nowParentRoleHr,$nowRoleDepth);
	$adb->pquery($query, $qparams);

	//Inserting into vtiger_role2profile vtiger_table
	foreach ($roleProfileArray as $profileId) {
		if ($profileId != '') {
			insertRole2ProfileRelation($roleId, $profileId);
		}
	}
	$log->debug("Exiting createRole method ...");
	return $roleId;
}

/** Function to update the role
 * @param $roleName -- Role Name:: Type varchar
 * @param $roleId -- Role Id:: Type varchar
 * @param $roleProfileArray -- Profile to be associated with this role:: Type Array
 */
function updateRole($roleId, $roleName, $roleProfileArray) {
	global $log,$adb;
	$log->debug('Entering updateRole('.$roleId.','.$roleName.','.print_r($roleProfileArray, true).') method ...');

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates($roleId);

	$sql1 = "update vtiger_role set rolename=? where roleid=?";
	$adb->pquery($sql1, array($roleName, $roleId));
	//Updating the Role2Profile relation
	$sql2 = "delete from vtiger_role2profile where roleId=?";
	$adb->pquery($sql2, array($roleId));

	foreach ($roleProfileArray as $profileId) {
		if ($profileId != '') {
			insertRole2ProfileRelation($roleId, $profileId);
		}
	}
	$log->debug("Exiting updateRole method ...");
}

/** Function to add the role to profile relation
 * @param $profileId -- Profile Id:: Type integer
 * @param $roleId -- Role Id:: Type varchar
 */
function insertRole2ProfileRelation($roleId, $profileId) {
	global $log,$adb;
	$log->debug('Entering insertRole2ProfileRelation('.$roleId.','.$profileId.') method ...');
	$query='insert into vtiger_role2profile values(?,?)';
	$qparams = array($roleId,$profileId);
	$adb->pquery($query, $qparams);
	$log->debug('Exiting insertRole2ProfileRelation method ...');
}

/** Function to get the roleid from rolename
 * @param $rolename -- Role Name:: Type varchar
 * @returns Role Id:: Type varchar
 */
function fetchRoleId($rolename) {
	global $log, $adb;
	$log->debug('Entering fetchRoleId('.$rolename.') method ...');
	$sqlfetchroleid = 'select roleid from vtiger_role where rolename=?';
	$resultroleid = $adb->pquery($sqlfetchroleid, array($rolename));
	$role_id = $adb->query_result($resultroleid, 0, 'roleid');
	$log->debug('Exiting fetchRoleId method ...');
	return $role_id;
}

/** Function to update user to role mapping based on the userid
 * @param $roleid -- Role Id:: Type varchar
 * @param $userid User Id:: Type integer
 */
function updateUser2RoleMapping($roleid, $userid) {
	global $log, $adb;
	$log->debug('Entering updateUser2RoleMapping('.$roleid.','.$userid.') method ...');
	//Check if row already exists
	$resultcheck = $adb->pquery('select userid from vtiger_user2role where userid=? limit 1', array($userid));
	if ($adb->num_rows($resultcheck) == 1) {
		$adb->pquery('delete from vtiger_user2role where userid=?', array($userid));
	}
	$adb->pquery('insert into vtiger_user2role(userid,roleid) values(?,?)', array($userid, $roleid));
	$log->debug('Exiting updateUser2RoleMapping method ...');
}

/** Function to update user to group mapping based on the userid
 * @param $groupname -- Group Name:: Type varchar
 * @param $userid User Id:: Type integer
 */
function updateUsers2GroupMapping($groupname, $userid) {
	global $log,$adb;
	$log->debug('Entering updateUsers2GroupMapping('.$groupname.','.$userid.') method ...');
	$adb->pquery('delete from vtiger_users2group where userid = ?', array($userid));
	$adb->pquery('insert into vtiger_users2group(groupname,userid) values(?,?)', array($groupname, $userid));
	$log->debug('Exiting updateUsers2GroupMapping method ...');
}

/** Function to add user to role mapping
 * @param $roleid -- Role Id:: Type varchar
 * @param $userid User Id:: Type integer
 */
function insertUser2RoleMapping($roleid, $userid) {
	global $log,$adb;
	$log->debug('Entering insertUser2RoleMapping('.$roleid.','.$userid.') method ...');
	$sql = 'insert into vtiger_user2role(userid,roleid) values(?,?)';
	$params = array($userid, $roleid);
	$adb->pquery($sql, $params);
	$log->debug('Exiting insertUser2RoleMapping method ...');
}

/** Function to add user to group mapping
 * @param $groupname -- Group Name:: Type varchar
 * @param $userid User Id:: Type integer
 */
function insertUsers2GroupMapping($groupname, $userid) {
	global $log,$adb;
	$log->debug('Entering insertUsers2GroupMapping('.$groupname.','.$userid.') method ...');
	$sql = 'insert into vtiger_users2group(groupname,userid) values(?,?)';
	$params = array($groupname, $userid);
	$adb->pquery($sql, $params);
	$log->debug('Exiting insertUsers2GroupMapping method ...');
}

/** Function to get the word template resultset
 * @param $module -- Module Name:: Type varchar
 * @returns Type:: resultset
 */
function fetchWordTemplateList($module) {
	global $log,$adb;
	$log->debug('Entering fetchWordTemplateList('.$module.') method ...');
	$sql_word = 'select templateid, filename from vtiger_wordtemplates where module =?';
	$result=$adb->pquery($sql_word, array($module));
	$log->debug('Exiting fetchWordTemplateList method ...');
	return $result;
}

/** Function to get the email template iformation
 * @param $templateName -- Template Name:: Type varchar
 * @returns Type:: resultset
 */
function fetchEmailTemplateInfo($templateName) {
	global $log,$adb;
	$log->debug('Entering fetchEmailTemplateInfo('.$templateName.') method ...');
	$sql= 'select * from vtiger_emailtemplates where templatename=?';
	$result = $adb->pquery($sql, array($templateName));
	$log->debug('Exiting fetchEmailTemplateInfo method ...');
	return $result;
}

/** Function to substitute the tokens in the specified file
 * @param $templateName -- Template Name:: Type varchar
 * @param $globals
 * @deprecated
 */
function substituteTokens($filename, $globals) {
	return '';
}

/** Function to get the role name from the roleid
 * @param $roleid -- Role Id:: Type varchar
 * @returns $rolename -- Role Name:: Type varchar
 */
function getRoleName($roleid) {
	global $log, $adb;
	$log->debug('Entering getRoleName('.$roleid.') method ...');
	$sql1 = 'select rolename from vtiger_role where roleid=?';
	$result = $adb->pquery($sql1, array($roleid));
	$rolename = $adb->query_result($result, 0, 'rolename');
	$log->debug('Exiting getRoleName method ...');
	return $rolename;
}

/** Function to get the profile name from the profileid
 * @param $profileid -- Profile Id:: Type integer
 * @returns $rolename -- Role Name:: Type varchar
 */
function getProfileName($profileid) {
	global $log, $adb;
	$log->debug('Entering getProfileName('.$profileid.') method ...');
	$sql1 = 'select profilename from vtiger_profile where profileid=?';
	$result = $adb->pquery($sql1, array($profileid));
	$profilename = $adb->query_result($result, 0, 'profilename');
	$log->debug('Exiting getProfileName method ...');
	return $profilename;
}

/** Function to get the vtiger_profile Description from the vtiger_profileid
 * @param $profileid -- Profile Id:: Type integer
 * @returns $rolename -- Role Name:: Type varchar
 */
function getProfileDescription($profileid) {
	global $log, $adb;
	$log->debug('Entering getProfileDescription('.$profileid.') method ...');
	$sql1 = 'select description from vtiger_profile where profileid=?';
	$result = $adb->pquery($sql1, array($profileid));
	$profileDescription = $adb->query_result($result, 0, 'description');
	$log->debug('Exiting getProfileDescription method ...');
	return $profileDescription;
}

function leadCanBeConverted($leadid) {
	static $leadCanBeConverted = null;
	if (is_null($leadCanBeConverted)) {
		global $current_user;
		require_once 'modules/Leads/ConvertLeadUI.php';
		$uiinfo = new ConvertLeadUI($leadid, $current_user);
		$leadCanBeConverted =
			isPermitted('Leads', 'EditView', $leadid) == 'yes'
			&& isPermitted('Leads', 'ConvertLead') == 'yes'
			&& (isPermitted('Accounts', 'CreateView') == 'yes' || isPermitted('Contacts', 'CreateView') == 'yes')
			&& (vtlib_isModuleActive('Contacts') || vtlib_isModuleActive('Accounts'))
			&& !isLeadConverted($leadid)
			&& ($uiinfo->getCompany() != null || $uiinfo->isModuleActive('Contacts') == true);
	}
	return $leadCanBeConverted;
}

/** This function is a wrapper that extends the permissions system with a hook to specific functionality **/
function isPermitted($module, $actionname, $record_id = '') {
// 	global $current_user, $adb;
// 	$lastModified = '';
// 	if (!empty($record_id)) {
// 		if (strpos($record_id, 'x')>0) { // is webserviceid
// 			list($void,$record_id) = explode('x', $record_id);
// 		}
// 		$rs = $adb->pquery("select date_format(modifiedtime,'%Y%m%d%H%i%s') from vtiger_crmentity where crmid=?", array($record_id));
// 		$lastModified = $adb->query_result($rs, 0, 0);
// 	}
// 	$key = "ispt:$module%$actionname%$record_id%" . $current_user->id . "%$lastModified";
// 	if (!coreBOS_Settings::settingExists($key)) {
// 		coreBOS_Settings::delSettingStartsWith("ispt:$module%$actionname%$record_id%" . $current_user->id);
		$permission = _vtisPermitted($module, $actionname, $record_id);
		list($permission, $unused1, $unused2, $unused3) = cbEventHandler::do_filter('corebos.permissions.ispermitted', array($permission,$module,$actionname,$record_id));
// 		coreBOS_Settings::setSetting($key, $permission);
// 	}
// 	return coreBOS_Settings::getSetting($key, 'no');
	return $permission;
}

/** Function to check if the currently logged in user is permitted to perform the specified action
 * @param $module -- Module Name:: Type varchar
 * @param $actionname -- Action Name:: Type varchar
 * @param $recordid -- Record Id:: Type integer
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function _vtisPermitted($module, $actionname, $record_id = '') {
	global $log, $adb, $current_user, $seclog;
	$log->debug("Entering isPermitted(".$module.",".$actionname.",".$record_id.") method ...");
	if (strpos($record_id, 'x')>0) { // is webserviceid
		list($void,$record_id) = explode('x', $record_id);
	}
	if (!empty($record_id) && $module != getSalesEntityType($record_id)) {
		$record_id = '';
	}
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	$parenttab = empty($_REQUEST['parenttab']) ? '' : vtlib_purify($_REQUEST['parenttab']);
	$permission = "no";
	if (($module == 'Users' || $module == 'Home' || $module == 'Utilities') && $parenttab != 'Settings') {
		//These modules dont have security right now
		$permission = "yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	//Checking the Access for the Settings Module
	if ($module == 'Settings' || $parenttab == 'Settings') {
		if (!$is_admin) {
			$permission = "no";
		} else {
			$permission = "yes";
		}
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}

	//Checking whether the user is admin
	if ($is_admin) {
		$permission ="yes";
		$log->debug("Exiting isPermitted method ...");
		return $permission;
	}
	//Retreiving the Tabid and Action Id
	$tabid = getTabid($module);
	$actionid=getActionid($actionname);
	//If no actionid, then allow action is vtiger_tab permission is available
	if ($actionid === '') {
		if ($profileTabsPermission[$tabid] ==0) {
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
		} else {
			$permission ="no";
		}
		return $permission;
	}

	$action = getActionname($actionid);
	//Checking for view all permission
	if ($profileGlobalPermission[1] ==0 || $profileGlobalPermission[2] ==0) {
		if ($actionid == 3 || $actionid == 4) {
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}
	//Checking for edit all permission
	if ($profileGlobalPermission[2] ==0) {
		if ($actionid == 3 || $actionid == 4 || $actionid ==0 || $actionid ==1) {
			$permission = 'yes';
			$log->debug('Exiting isPermitted method ...');
			return $permission;
		}
	}
	//Checking for vtiger_tab permission
	if (!is_null($tabid) && isset($profileTabsPermission[$tabid]) && $profileTabsPermission[$tabid] != 0) {
		$permission = 'no';
		$log->debug('Exiting isPermitted method ...');
		return $permission;
	}
	if (!isset($profileActionPermission[$tabid][$actionid]) && ($action == 'Export' || $action == 'Import')) {
		return 'no';
	}
	//Checking for Action Permission
	if (!isset($profileActionPermission[$tabid][$actionid]) || (strlen($profileActionPermission[$tabid][$actionid]) <  1 && $profileActionPermission[$tabid][$actionid] == '')) {
		$permission = 'yes';
		$log->debug('Exiting isPermitted method ...');
		return $permission;
	}

	if ($profileActionPermission[$tabid][$actionid] != 0 && $profileActionPermission[$tabid][$actionid] != '') {
		$permission = 'no';
		$log->debug('Exiting isPermitted method ...');
		return $permission;
	}
	//Checking and returning true if recorid is null
	if ($record_id == '') {
		$permission = 'yes';
		$log->debug('Exiting isPermitted method ...');
		return $permission;
	}

	//If modules is Faq or PriceBook then no sharing
	if ($record_id != '') {
		if (getTabOwnedBy($module) == 1) {
			$permission = 'yes';
			$log->debug('Exiting isPermitted method ...');
			return $permission;
		}
	}

	//Retreiving the RecordOwnerId
	$recOwnType='';
	$recOwnId='';
	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$recOwnType=$type;
		$recOwnId=$id;
	}
	//Retreiving the default Organisation sharing Access
	$others_permission_id = $defaultOrgSharingPermission[$tabid];

	if ($recOwnType == 'Users') {
		$wfs = new VTWorkflowManager($adb);
		$racbr = $wfs->getRACRuleForRecord($module, $record_id);
		//Checking if the Record Owner is the current User
		if ($current_user->id == $recOwnId) {
			if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
				|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname, true))
			) {
				$permission = 'yes';
			} else {
				$permission = 'no';
			}
			$log->debug('Exiting isPermitted method ...');
			return $permission;
		}
		//Checking if the Record Owner is the Subordinate User
		foreach ($subordinate_roles_users as $roleid => $userids) {
			if (in_array($recOwnId, $userids)) {
				$permission = 'yes';
				$log->debug('Exiting isPermitted method ...');
				return $permission;
			}
		}
		if ($racbr!==false && $racbr->hasDetailViewPermissionTo($actionname, false)) {
			$log->debug('Exiting isPermitted method via RAC User...');
			return 'yes';
		}
	} elseif ($recOwnType == 'Groups') {
		//Checking if the record owner is the current user's group
		if (in_array($recOwnId, $current_user_groups)) {
			$wfs = new VTWorkflowManager($adb);
			$racbr = $wfs->getRACRuleForRecord($module, $record_id);
			if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
				|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname))
			) {
				$permission = 'yes';
			} else {
				$permission = 'no';
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	}

	//Checking for Default Org Sharing permission
	if ($others_permission_id == 0) {
		if ($actionid == 1 || $actionid == 0) {
			if ($module == 'Calendar') {
				if ($recOwnType == 'Users') {
					$permission = isCalendarPermittedBySharing($record_id);
				} else {
					$permission='no';
				}
			} else {
				$permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		} elseif ($actionid == 2) {
			$permission = "no";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		} else {
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	} elseif ($others_permission_id == 1) {
		if ($actionid == 2) {
			$permission = "no";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		} else {
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	} elseif ($others_permission_id == 2) {
		$wfs = new VTWorkflowManager($adb);
		$racbr = $wfs->getRACRuleForRecord($module, $record_id);
		if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
			|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname))
		) {
			$permission = "yes";
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		}
	} elseif ($others_permission_id == 3) {
		if ($actionid == 3 || $actionid == 4) {
			if ($module == 'Calendar' || $module == 'cbCalendar') {
				if ($recOwnType == 'Users') {
					$permission = isCalendarPermittedBySharing($record_id);
				} else {
					$permission='no';
				}
			} else {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($module, $record_id);
				if ($racbr) {
					if ($actionid == 3 && !$racbr->hasListViewPermissionTo('retrieve')) {
						return 'no';
					} elseif ($actionid == 4 && !$racbr->hasDetailViewPermissionTo('retrieve')) {
						return 'no';
					}
				}
				$permission = isReadPermittedBySharing($module, $tabid, $actionid, $record_id);
			}
			$log->debug("Exiting isPermitted method ...");
			return $permission;
		} elseif ($actionid ==0 || $actionid ==1) {
			if ($module == 'Calendar') {
				$permission='no';
			} else {
				$wfs = new VTWorkflowManager($adb);
				$racbr = $wfs->getRACRuleForRecord($module, $record_id);
				if ($racbr) {
					if ($actionid == 0 && !$racbr->hasDetailViewPermissionTo('create')) {
						return 'no';
					} elseif ($actionid == 1 && !$racbr->hasDetailViewPermissionTo('update')) {
						return 'no';
					}
				}
				$permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
			}
			$log->debug('Exiting isPermitted method ...');
			return $permission;
		} elseif ($actionid ==2) {
				$permission ='no';
				return $permission;
		} else {
			$permission = 'yes';
			$log->debug('Exiting isPermitted method ...');
			return $permission;
		}
	} else {
		$permission = 'yes';
	}

	$log->debug('Exiting isPermitted method ...');
	return $permission;
}

/** Function to check if the currently logged in user has Read Access due to Sharing for the specified record
 * @param $module -- Module Name:: Type varchar
 * @param $actionid -- Action Id:: Type integer
 * @param $recordid -- Record Id:: Type integer
 * @param $tabid -- Tab Id:: Type integer
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadPermittedBySharing($module, $tabid, $actionid, $record_id) {
	global $log, $current_user;
	$log->debug("Entering isReadPermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
	if (! in_array($module, $sharingModuleList)) {
		$sharePer='no';
		return $sharePer;
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module.'_share_read_permission';
	$read_per_arr=$$varname;
	if ($ownertype == 'Users') {
		//Checking the Read Sharing Permission Array in Role Users
		$read_role_per=$read_per_arr['ROLE'];
		foreach ($read_role_per as $roleid => $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}
		}
		//Checking the Read Sharing Permission Array in Groups Users
		$read_grp_per=$read_per_arr['GROUP'];
		foreach ($read_grp_per as $grpid => $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug("Exiting isReadPermittedBySharing method ...");
				return $sharePer;
			}
		}
	} elseif ($ownertype == 'Groups') {
		$read_grp_per=$read_per_arr['GROUP'];
		if (array_key_exists($ownerid, $read_grp_per)) {
			$sharePer='yes';
			$log->debug("Exiting isReadPermittedBySharing method ...");
			return $sharePer;
		}
	}
	//Checking for the Related Sharing Permission
	$relatedModuleArray = (isset($related_module_share[$tabid]) ? $related_module_share[$tabid] : '');
	if (is_array($relatedModuleArray)) {
		foreach ($relatedModuleArray as $parModId) {
			$parRecordOwner=getParentRecordOwner($tabid, $parModId, $record_id);
			if (count($parRecordOwner) > 0) {
				$parModName=getTabname($parModId);
				$rel_var=$parModName."_".$module."_share_read_permission";
				$read_related_per_arr=$$rel_var;
				$rel_owner_type='';
				$rel_owner_id='';
				foreach ($parRecordOwner as $rel_type => $rel_id) {
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if ($rel_owner_type=='Users') {
					//Checking in Role Users
					$read_related_role_per=$read_related_per_arr['ROLE'];
					foreach ($read_related_role_per as $roleid => $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug("Exiting isReadPermittedBySharing method ...");
							return $sharePer;
						}
					}
					//Checking in Group Users
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					foreach ($read_related_grp_per as $grpid => $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug("Exiting isReadPermittedBySharing method ...");
							return $sharePer;
						}
					}
				} elseif ($rel_owner_type=='Groups') {
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					if (array_key_exists($rel_owner_id, $read_related_grp_per)) {
						$sharePer='yes';
						$log->debug("Exiting isReadPermittedBySharing method ...");
						return $sharePer;
					}
				}
			}
		}
	}
	$log->debug("Exiting isReadPermittedBySharing method ...");
	return $sharePer;
}

/** Function to check if the currently logged in user has Write Access due to Sharing for the specified record
 * @param $module -- Module Name:: Type varchar
 * @param $actionid -- Action Id:: Type integer
 * @param $recordid -- Record Id:: Type integer
 * @param $tabid -- Tab Id:: Type integer
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id) {
	global $log, $current_user;
	$log->debug("Entering isReadWritePermittedBySharing(".$module.",".$tabid.",".$actionid.",".$record_id.") method ...");
	require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
	if (! in_array($module, $sharingModuleList)) {
		$sharePer='no';
		return $sharePer;
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$ownertype=$type;
		$ownerid=$id;
	}

	$varname=$module."_share_write_permission";
	$write_per_arr=$$varname;

	if ($ownertype == 'Users') {
		//Checking the Write Sharing Permission Array in Role Users
		$write_role_per=$write_per_arr['ROLE'];
		foreach ($write_role_per as $roleid => $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}
		}
		//Checking the Write Sharing Permission Array in Groups Users
		$write_grp_per=$write_per_arr['GROUP'];
		foreach ($write_grp_per as $grpid => $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug("Exiting isReadWritePermittedBySharing method ...");
				return $sharePer;
			}
		}
	} elseif ($ownertype == 'Groups') {
		$write_grp_per=$write_per_arr['GROUP'];
		if (array_key_exists($ownerid, $write_grp_per)) {
			$sharePer='yes';
			$log->debug("Exiting isReadWritePermittedBySharing method ...");
			return $sharePer;
		}
	}
	//Checking for the Related Sharing Permission
	$relatedModuleArray = (isset($related_module_share[$tabid]) ? $related_module_share[$tabid] : null);
	if (is_array($relatedModuleArray)) {
		foreach ($relatedModuleArray as $parModId) {
			$parRecordOwner=getParentRecordOwner($tabid, $parModId, $record_id);
			if (count($parRecordOwner) > 0) {
				$parModName=getTabname($parModId);
				$rel_var=$parModName."_".$module."_share_write_permission";
				$write_related_per_arr=$$rel_var;
				$rel_owner_type='';
				$rel_owner_id='';
				foreach ($parRecordOwner as $rel_type => $rel_id) {
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if ($rel_owner_type=='Users') {
					//Checking in Role Users
					$write_related_role_per=$write_related_per_arr['ROLE'];
					foreach ($write_related_role_per as $roleid => $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug("Exiting isReadWritePermittedBySharing method ...");
							return $sharePer;
						}
					}
					//Checking in Group Users
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					foreach ($write_related_grp_per as $grpid => $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug("Exiting isReadWritePermittedBySharing method ...");
							return $sharePer;
						}
					}
				} elseif ($rel_owner_type=='Groups') {
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					if (array_key_exists($rel_owner_id, $write_related_grp_per)) {
						$sharePer='yes';
						$log->debug("Exiting isReadWritePermittedBySharing method ...");
						return $sharePer;
					}
				}
			}
		}
	}

	$log->debug("Exiting isReadWritePermittedBySharing method ...");
	return $sharePer;
}

/** Function to check if the outlook user is permitted to perform the specified action
 * @param $module -- Module Name:: Type varchar
 * @param $actionname -- Action Name:: Type varchar
 * @param $recordid -- Record Id:: Type integer
 * @returns yes or no. If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function isAllowed_Outlook($module, $action, $user_id, $record_id) {
	global $log;
	$log->debug("Entering isAllowed_Outlook(".$module.",".$action.",".$user_id.",".$record_id.") method ...");

	$permission = "no";
	if ($module == 'Users' || $module == 'Home' ||  $module == 'Settings' || $module == 'Calendar') {
		//These modules do not have security
		$permission = "yes";
	} else {
		global $current_user;
		$tabid = getTabid($module);
		$actionid = getActionid($action);
		$profile_id = fetchUserProfileId($user_id);
		$tab_per_Data = getAllTabsPermission($profile_id);

		$permissionData = getTabsActionPermission($profile_id);
		$defSharingPermissionData = getDefaultSharingAction();
		$others_permission_id = $defSharingPermissionData[$tabid];

		//Checking whether this vtiger_tab is allowed
		if ($tab_per_Data[$tabid] == 0) {
			$permission = 'yes';
			//Checking whether this action is allowed
			if ($permissionData[$tabid][$actionid] == 0) {
				$permission = 'yes';
				$rec_owner_id = '';
				if ($record_id != '' && $module != 'Faq') {
					$rec_owner_id = getUserId($record_id);
				}

				if ($record_id != '' && $others_permission_id != '' && $module != 'Faq' && $rec_owner_id != 0) {
					if ($rec_owner_id != $current_user->id) {
						if ($others_permission_id == 0) {
							if ($action == 'EditView' || $action == 'CreateView' || $action == 'Delete') {
								$permission = "no";
							} else {
								$permission = "yes";
							}
						} elseif ($others_permission_id == 1) {
							if ($action == 'Delete') {
								$permission = "no";
							} else {
								$permission = "yes";
							}
						} elseif ($others_permission_id == 2) {
							$permission = "yes";
						} elseif ($others_permission_id == 3) {
							if ($action == 'DetailView' || $action == 'EditView' || $action == 'CreateView' || $action == 'Delete') {
								$permission = "no";
							} else {
								$permission = "yes";
							}
						}
					} else {
						$permission = "yes";
					}
				}
			} else {
				$permission = "no";
			}
		} else {
			$permission = "no";
		}
	}
	$log->debug("Exiting isAllowed_Outlook method ...");
	return $permission;
}

/** Function to get the Profile Global Information for the specified vtiger_profileid
 * @param $profileid -- Profile Id:: Type integer
 * @returns Profile Gloabal Permission Array in the following format:
 * $profileGloblaPermisson=Array($viewall_actionid=>permission, $editall_actionid=>permission)
 */
function getProfileGlobalPermission($profileid) {
	global $log, $adb;
	$log->debug('Entering getProfileGlobalPermission('.$profileid.') method ...');
	$copy=array();
	$sql = 'select globalactionid, globalactionpermission from vtiger_profile2globalpermissions where profileid=?';
	$result = $adb->pquery($sql, array($profileid));
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$act_id = $adb->query_result($result, $i, 'globalactionid');
		$per_id = $adb->query_result($result, $i, 'globalactionpermission');
		$copy[$act_id] = $per_id;
	}
	$log->debug('Exiting getProfileGlobalPermission method ...');
	return $copy;
}

/** Function to get the Profile Tab Permissions for the specified vtiger_profileid
 * @param $profileid -- Profile Id:: Type integer
 * @returns Profile Tabs Permission Array in the following format:
 * $profileTabPermisson=Array($tabid1=>permission, $tabid2=>permission,........., $tabidn=>permission)
 */
function getProfileTabsPermission($profileid) {
	global $log, $adb;
	$log->debug('Entering getProfileTabsPermission('.$profileid.') method ...');
	$sql = 'select tabid, permissions from vtiger_profile2tab where profileid=?';
	$result = $adb->pquery($sql, array($profileid));
	$num_rows = $adb->num_rows($result);
	$copy=array();
	for ($i=0; $i<$num_rows; $i++) {
		$tab_id = $adb->query_result($result, $i, 'tabid');
		$per_id = (integer)$adb->query_result($result, $i, 'permissions');
		$copy[$tab_id] = $per_id;
	}
	$log->debug('Exiting getProfileTabsPermission method ...');
	return $copy;
}

/** Function to get the Profile Action Permissions for the specified vtiger_profileid
 * @param $profileid -- Profile Id:: Type integer
 * @returns Profile Tabs Action Permission Array in the following format:
 *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                                |
 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileActionPermission($profileid) {
	global $log, $adb;
	$log->debug("Entering getProfileActionPermission(".$profileid.") method ...");
	$check = array();
	$temp_tabid = array();
	$sql1 = "select * from vtiger_profile2standardpermissions where profileid=?";
	$result1 = $adb->pquery($sql1, array($profileid));
	$num_rows1 = $adb->num_rows($result1);
	for ($i=0; $i<$num_rows1; $i++) {
		$tab_id = $adb->query_result($result1, $i, 'tabid');
		if (! in_array($tab_id, $temp_tabid)) {
			$temp_tabid[] = $tab_id;
			$access = array();
		}
		$action_id = $adb->query_result($result1, $i, 'operation');
		$per_id = $adb->query_result($result1, $i, 'permissions');
		$access[$action_id] = $per_id;
		$check[$tab_id] = $access;
	}
	$log->debug("Exiting getProfileActionPermission method ...");
	return $check;
}

/** Function to get the Standard and Utility Profile Action Permissions for the specified vtiger_profileid
  * @param $profileid -- Profile Id:: Type integer
  * @returns Profile Tabs Action Permission Array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileAllActionPermission($profileid) {
	global $log;
	$log->debug("Entering getProfileAllActionPermission(".$profileid.") method ...");
	$actionArr=getProfileActionPermission($profileid);
	$utilArr=getTabsUtilityActionPermission($profileid);
	foreach ($utilArr as $tabid => $act_arr) {
		$act_tab_arr = isset($actionArr[$tabid]) ? $actionArr[$tabid] : array();
		foreach ($act_arr as $utilid => $util_perr) {
			$act_tab_arr[$utilid]=$util_perr;
		}
		$actionArr[$tabid]=$act_tab_arr;
	}
	$log->debug("Exiting getProfileAllActionPermission method ...");
	return $actionArr;
}

/** Function to create vtiger_profile
  * @param $profilename -- Profile Name:: Type varchar
  * @param $parentProfileId -- Profile Id:: Type integer
 */
function createProfile($profilename, $parentProfileId, $description) {
	global $log, $adb;
	$log->debug("Entering createProfile(".$profilename.",".$parentProfileId.",".$description.") method ...");
	//Inserting values into Profile Table
	$sql1 = "insert into vtiger_profile values(?,?,?)";
	$params1 = array('', $profilename, $description);
	$adb->pquery($sql1, $params1);

	//Retreiving the vtiger_profileid
	$sql2 = "select max(profileid) as current_id from vtiger_profile";
	$result2 = $adb->pquery($sql2, array());
	$current_profile_id = $adb->query_result($result2, 0, 'current_id');

	//Inserting values into vtiger_profile2globalpermissions
	$sql3 = "select * from vtiger_profile2globalpermissions where profileid=?";
	$params3 = array($parentProfileId);
	$result3= $adb->pquery($sql3, $params3);
	$p2tab_rows = $adb->num_rows($result3);
	for ($i=0; $i<$p2tab_rows; $i++) {
		$act_id=$adb->query_result($result3, $i, 'globalactionid');
		$permissions=$adb->query_result($result3, $i, 'globalactionpermission');
		$sql4="insert into vtiger_profile2globalpermissions values(?,?,?)";
		$params4 = array($current_profile_id, $act_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2tab vtiger_table
	$sql3 = "select * from vtiger_profile2tab where profileid=?";
	$params3 = array($parentProfileId);
	$result3= $adb->pquery($sql3, $params3);
	$p2tab_rows = $adb->num_rows($result3);
	for ($i=0; $i<$p2tab_rows; $i++) {
		$tab_id=$adb->query_result($result3, $i, 'tabid');
		$permissions=$adb->query_result($result3, $i, 'permissions');
		$sql4="insert into vtiger_profile2tab values(?,?,?)";
		$params4 = array($current_profile_id, $tab_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2standard vtiger_table
	$sql6 = "select * from vtiger_profile2standardpermissions where profileid=?";
	$params6 = array($parentProfileId);
	$result6= $adb->pquery($sql6, $params6);
	$p2per_rows = $adb->num_rows($result6);
	for ($i=0; $i<$p2per_rows; $i++) {
		$tab_id=$adb->query_result($result6, $i, 'tabid');
		$action_id=$adb->query_result($result6, $i, 'operation');
		$permissions=$adb->query_result($result6, $i, 'permissions');
		$sql7="insert into vtiger_profile2standardpermissions values(?,?,?,?)";
		$params7 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql7, $params7);
	}

	//Inserting values into Profile2Utility vtiger_table
	$sql8 = "select * from vtiger_profile2utility where profileid=?";
	$params8 = array($parentProfileId);
	$result8= $adb->pquery($sql8, $params8);
	$p2util_rows = $adb->num_rows($result8);
	for ($i=0; $i<$p2util_rows; $i++) {
		$tab_id=$adb->query_result($result8, $i, 'tabid');
		$action_id=$adb->query_result($result8, $i, 'activityid');
		$permissions=$adb->query_result($result8, $i, 'permission');
		$sql9="insert into vtiger_profile2utility values(?,?,?,?)";
		$params9 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql9, $params9);
	}

	//Inserting values into Profile2field vtiger_table
	$sql10 = "select tabid, fieldid, visible, readonly from vtiger_profile2field where profileid=?";
	$params10 = array($parentProfileId);
	$result10= $adb->pquery($sql10, $params10);
	$p2field_rows = $adb->num_rows($result10);
	$sql11='insert into vtiger_profile2field values(?,?,?,?,?,?)';
	for ($i=0; $i<$p2field_rows; $i++) {
		$tab_id=$adb->query_result($result10, $i, 'tabid');
		$fieldid=$adb->query_result($result10, $i, 'fieldid');
		$permissions=$adb->query_result($result10, $i, 'visible');
		$readonly=$adb->query_result($result10, $i, 'readonly');
		$params11 = array($current_profile_id, $tab_id, $fieldid, $permissions ,$readonly, 'B');
		$adb->pquery($sql11, $params11);
	}
	$log->debug("Exiting createProfile method ...");
}

/** Function to delete vtiger_profile
 * @param $transfer_profileid -- Profile Id to which the existing vtiger_role2profile relationships are to be transferred :: Type varchar
 * @param $prof_id -- Profile Id to be deleted:: Type integer
 */
function deleteProfile($prof_id, $transfer_profileid = '') {
	global $log, $adb;
	$log->debug('Entering deleteProfile('.$prof_id.','.$transfer_profileid.') method ...');
	//delete from vtiger_profile2global permissions
	$sql4 = 'delete from vtiger_profile2globalpermissions where profileid=?';
	$adb->pquery($sql4, array($prof_id));

	//deleting from vtiger_profile 2 vtiger_tab;
	$sql4 = 'delete from vtiger_profile2tab where profileid=?';
	$adb->pquery($sql4, array($prof_id));

	//deleting from vtiger_profile2standardpermissions vtiger_table
	$sql5 = 'delete from vtiger_profile2standardpermissions where profileid=?';
	$adb->pquery($sql5, array($prof_id));

	//deleting from vtiger_profile2field
	$sql6 ='delete from vtiger_profile2field where profileid=?';
	$adb->pquery($sql6, array($prof_id));

	//deleting from vtiger_profile2utility
	$sql7 ='delete from vtiger_profile2utility where profileid=?';
	$adb->pquery($sql7, array($prof_id));

	//updating vtiger_role2profile
	if (isset($transfer_profileid) && $transfer_profileid != '') {
		$sql8 = "select roleid from vtiger_role2profile where profileid=?";
		$result = $adb->pquery($sql8, array($prof_id));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$roleid=$adb->query_result($result, $i, 'roleid');
			$sql = "select profileid from vtiger_role2profile where roleid=?";
			$profresult=$adb->pquery($sql, array($roleid));
			$num=$adb->num_rows($profresult);
			if ($num>1) {
				$sql10="delete from vtiger_role2profile where roleid=? and profileid=?";
				$adb->pquery($sql10, array($roleid, $prof_id));
			} else {
				$sql8 = "update vtiger_role2profile set profileid=? where profileid=? and roleid=?";
				$adb->pquery($sql8, array($transfer_profileid, $prof_id, $roleid));
			}
		}
	}

	//delete from vtiger_profile vtiger_table;
	$sql9 = 'delete from vtiger_profile where profileid=?';
	$adb->pquery($sql9, array($prof_id));
	$log->debug('Exiting deleteProfile method ...');
}

/** Function to get all  the role information
 * @returns array $allRoleDetailArray -- array will contain the details of all the roles. RoleId will be the key
 */
function getAllRoleDetails() {
	global $log, $adb;
	$log->debug('Entering getAllRoleDetails() method ...');
	$role_det = array();
	$query = 'select * from vtiger_role';
	$result = $adb->pquery($query, array());
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$each_role_det = array();
		$roleid=$adb->query_result($result, $i, 'roleid');
		$rolename=$adb->query_result($result, $i, 'rolename');
		$roledepth=$adb->query_result($result, $i, 'depth');
		$sub_roledepth=$roledepth + 1;
		$parentrole=$adb->query_result($result, $i, 'parentrole');
		$sub_role='';

		//getting the immediate subordinates
		$query1='select roleid from vtiger_role where parentrole like ? and depth=?';
		$res1 = $adb->pquery($query1, array($parentrole."::%", $sub_roledepth));
		$num_roles = $adb->num_rows($res1);
		if ($num_roles > 0) {
			for ($j=0; $j<$num_roles; $j++) {
				if ($j == 0) {
					$sub_role .= $adb->query_result($res1, $j, 'roleid');
				} else {
					$sub_role .= ','.$adb->query_result($res1, $j, 'roleid');
				}
			}
		}
		$each_role_det[]=$rolename;
		$each_role_det[]=$roledepth;
		$each_role_det[]=$sub_role;
		$role_det[$roleid]=$each_role_det;
	}
	$log->debug('Exiting getAllRoleDetails method ...');
	return $role_det;
}

/** Function to get all the profile information
 * @returns array $allProfileInfoArray -- Array will contain the details of all the profiles. Profile ID will be the key
 */
function getAllProfileInfo() {
	global $log, $adb;
	$log->debug('Entering getAllProfileInfo() method ...');
	$query='select * from vtiger_profile';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$prof_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$profileid=$adb->query_result($result, $i, 'profileid');
		$profilename=$adb->query_result($result, $i, 'profilename');
		$prof_details[$profileid]=$profilename;
	}
	$log->debug('Exiting getAllProfileInfo method ...');
	return $prof_details;
}

/** Function to get the role information of the specified role
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleInfoArray-- RoleInfoArray in the following format:
 *       array($roleId=>array($rolename, $parentrole, $roledepth, $immediateParent));
 */
function getRoleInformation($roleid) {
	global $log, $adb;
	$log->debug("Entering getRoleInformation(".$roleid.") method ...");
	$query = 'select * from vtiger_role where roleid=?';
	$result = $adb->pquery($query, array($roleid));
	$rolename=$adb->query_result($result, 0, 'rolename');
	$parentrole=$adb->query_result($result, 0, 'parentrole');
	$roledepth=$adb->query_result($result, 0, 'depth');
	$parentRoleArr=explode('::', $parentrole);
	$immediateParent = (count($parentRoleArr)>1) ? $parentRoleArr[count($parentRoleArr)-2] : null;
	$roleDet=array();
	$roleDet[]=$rolename;
	$roleDet[]=$parentrole;
	$roleDet[]=$roledepth;
	$roleDet[]=$immediateParent;
	$roleInfo=array();
	$roleInfo[$roleid]=$roleDet;
	$log->debug("Exiting getRoleInformation method ...");
	return $roleInfo;
}

/** Function to get the role related profiles
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleProfiles-- Role Related Profile Array in the following format:
 *       $roleProfiles=Array($profileId1=>$profileName,$profileId2=>$profileName,........,$profileIdn=>$profileName));
 */
function getRoleRelatedProfiles($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleRelatedProfiles(".$roleId.") method ...");
	$query = 'select vtiger_role2profile.*,vtiger_profile.profilename
		from vtiger_role2profile
		inner join vtiger_profile on vtiger_profile.profileid=vtiger_role2profile.profileid
		where roleid=?';
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedProfiles=array();
	for ($i=0; $i<$num_rows; $i++) {
		$roleRelatedProfiles[$adb->query_result($result, $i, 'profileid')]=$adb->query_result($result, $i, 'profilename');
	}
	$log->debug('Exiting getRoleRelatedProfiles method...');
	return $roleRelatedProfiles;
}

/** Function to get the role related users
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleUsers-- Role Related User Array in the following format:
 *       $roleUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleUsers($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleUsers(".$roleId.") method ...");
	$roleRelatedUsers = VTCacheUtils::lookupRole_RelatedUsers($roleId);
	if ($roleRelatedUsers === false) {
		$query = 'select vtiger_user2role.*,vtiger_users.* from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid where roleid=?';
		$result = $adb->pquery($query, array($roleId));
		$num_rows=$adb->num_rows($result);
		$roleRelatedUsers=array();
		for ($i=0; $i<$num_rows; $i++) {
			$roleRelatedUsers[$adb->query_result($result, $i, 'userid')]=getFullNameFromQResult($result, $i, 'Users');
		}
		VTCacheUtils::updateRole_RelatedUsers($roleId, $roleRelatedUsers);
	}
	$log->debug('Exiting getRoleUsers method...');
	return $roleRelatedUsers;
}

/** Function to get the role related user ids
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleUserIds-- Role Related User Array in the following format:
 *       $roleUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleUserIds($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleUserIds(".$roleId.") method ...");
	$query = 'select vtiger_user2role.*,vtiger_users.user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid where roleid=?';
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=array();
	for ($i=0; $i<$num_rows; $i++) {
		$roleRelatedUsers[]=$adb->query_result($result, $i, 'userid');
	}
	$log->debug('Exiting getRoleUserIds method...');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate users
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubUsers-- Role and Subordinates Related Users Array in the following format:
 *       $roleSubUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleAndSubordinateUsers($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleAndSubordinateUsers(".$roleId.") method ...");
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	$query = 'select vtiger_user2role.userid,vtiger_users.user_name
		from vtiger_user2role
		inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
		inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
		where vtiger_role.parentrole like ?';
	$result = $adb->pquery($query, array($parentRole.'%'));
	$roleRelatedUsers=array();
	while ($row = $adb->fetch_array($result)) {
		$roleRelatedUsers[ $row['userid'] ] = $row['user_name'];
	}
	$log->debug('Exiting getRoleAndSubordinateUsers method...');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate user ids
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubUserIds-- Role and Subordinates Related Users Array in the following format:
 *       $roleSubUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleAndSubordinateUserIds($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleAndSubordinateUserIds(".$roleId.") method ...");
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	$query = 'select vtiger_user2role.userid
		from vtiger_user2role
		inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid
		inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
		where vtiger_role.parentrole like ?';
	$result = $adb->pquery($query, array($parentRole.'%'));
	$roleRelatedUsers=array();
	while ($row = $adb->getNextRow($result, false)) {
		$roleRelatedUsers[] = $row[0];
	}
	$log->debug('Exiting getRoleAndSubordinateUserIds method...');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate Information for the specified roleId
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubInfo-- Role and Subordinates Information array in the following format:
 *       $roleSubInfo=Array($roleId1=>Array($rolename,$parentrole,$roledepth,$immediateParent), $roleId2=>Array($rolename,$parentrole,$roledepth,$immediateParent),.....);
 */
function getRoleAndSubordinatesInformation($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleAndSubordinatesInformation(".$roleId.") method ...");
	static $roleInfoCache = array();
	if (!empty($roleInfoCache[$roleId])) {
		return $roleInfoCache[$roleId];
	}
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];
	$query='select * from vtiger_role where parentrole like ? order by parentrole asc';
	$result=$adb->pquery($query, array($roleParentSeq.'%'));
	$num_rows=$adb->num_rows($result);
	$roleInfo=array();
	for ($i=0; $i<$num_rows; $i++) {
		$roleid=$adb->query_result($result, $i, 'roleid');
		$rolename=$adb->query_result($result, $i, 'rolename');
		$roledepth=$adb->query_result($result, $i, 'depth');
		$parentrole=$adb->query_result($result, $i, 'parentrole');
		$roleDet=array();
		$roleDet[]=$rolename;
		$roleDet[]=$parentrole;
		$roleDet[]=$roledepth;
		$roleInfo[$roleid]=$roleDet;
	}
	$roleInfoCache[$roleId] = $roleInfo;
	$log->debug('Exiting getRoleAndSubordinatesInformation method...');
	return $roleInfo;
}

/** Function to get the role and subordinate role ids
 * @param $roleid -- RoleId :: Type varchar
 * @returns $roleSubRoleIds-- Role and Subordinates RoleIds in an Array in the following format:
 *       $roleSubRoleIds=Array($roleId1,$roleId2,........,$roleIdn);
 */
function getRoleAndSubordinatesRoleIds($roleId) {
	global $log, $adb;
	$log->debug("Entering getRoleAndSubordinatesRoleIds(".$roleId.") method ...");
	$roleDetails=getRoleInformation($roleId);
	$roleInfo=$roleDetails[$roleId];
	$roleParentSeq=$roleInfo[1];
	$query='select roleid from vtiger_role where parentrole like ? order by parentrole asc';
	$result=$adb->pquery($query, array($roleParentSeq.'%'));
	$num_rows=$adb->num_rows($result);
	$roleInfo=array();
	for ($i=0; $i<$num_rows; $i++) {
		$roleid=$adb->query_result($result, $i, 'roleid');
		$roleInfo[]=$roleid;
	}
	$log->debug('Exiting getRoleAndSubordinatesRoleIds method...');
	return $roleInfo;
}

/** Function to get the role and subordinate hierarchy
 * @returns $roleSubRoleIds-- Role and Subordinates RoleIds in an Array tree of hierarchy dependencies
 */
function getRoleAndSubordinatesHierarchy() {
	global $adb;
	$hr_res = $adb->pquery('select * from vtiger_role order by parentrole asc', array());
	$num_rows = $adb->num_rows($hr_res);
	$hrarray = array();
	for ($l=0; $l<$num_rows; $l++) {
		$roleid = $adb->query_result($hr_res, $l, 'roleid');
		$parent = $adb->query_result($hr_res, $l, 'parentrole');
		$temp_list = explode('::', $parent);
		$size = count($temp_list);
		$i=0;
		$k=array();
		$y=$hrarray;
		if (count($hrarray) == 0) {
			$hrarray[$temp_list[0]]= array();
		} else {
			while ($i<$size-1) {
				$y=$y[$temp_list[$i]];
				$k[$temp_list[$i]] = $y;
				$i++;
			}
			$y[$roleid] = array();
			$k[$roleid] = array();
			//Reversing the Array
			$rev_temp_list=array_reverse($temp_list);
			$j=0;
			//Now adding this into the main array
			foreach ($rev_temp_list as $value) {
				if ($j == $size-1) {
					$hrarray[$value]=$k[$value];
				} else {
					$k[$rev_temp_list[$j+1]][$value]=$k[$value];
				}
				$j++;
			}
		}
	}
	return $hrarray;
}

/** Function to get delete the spcified role
 * @param $roleid -- RoleId :: Type varchar
 * @param $transferRoleId -- RoleId to which users of the role that is being deleted are transferred:: Type varchar
 */
function deleteRole($roleId, $transferRoleId) {
	global $log, $adb;
	$log->debug("Entering deleteRole(".$roleId.",".$transferRoleId.") method ...");
	$roleInfo=getRoleAndSubordinatesInformation($roleId);
	foreach ($roleInfo as $roleid => $roleDetArr) {
		$sql1 = 'update vtiger_user2role set roleid=? where roleid=?';
		$adb->pquery($sql1, array($transferRoleId, $roleid));
		//Deleteing from vtiger_role2profile vtiger_table
		$sql2 = 'delete from vtiger_role2profile where roleid=?';
		$adb->pquery($sql2, array($roleid));
		//delete handling for vtiger_groups
		$sql10 = 'delete from vtiger_group2role where roleid=?';
		$adb->pquery($sql10, array($roleid));
		$sql11 = 'delete from vtiger_group2rs where roleandsubid=?';
		$adb->pquery($sql11, array($roleid));
		//delete handling for sharing rules
		deleteRoleRelatedSharingRules($roleid);
		//delete from vtiger_role vtiger_table;
		$sql9 = 'delete from vtiger_role where roleid=?';
		$adb->pquery($sql9, array($roleid));
	}
	$log->debug('Exiting deleteRole method ...');
}

/** Function to delete the role related sharing rules
 * @param $roleid -- RoleId :: Type varchar
 */
function deleteRoleRelatedSharingRules($roleId) {
	global $log, $adb;
	$log->debug("Entering deleteRoleRelatedSharingRules(".$roleId.") method ...");
	$dataShareTableColArr=array(
		'vtiger_datashare_grp2role'=>'to_roleid',
		'vtiger_datashare_grp2rs'=>'to_roleandsubid',
		'vtiger_datashare_role2group'=>'share_roleid',
		'vtiger_datashare_role2role'=>'share_roleid::to_roleid',
		'vtiger_datashare_role2rs'=>'share_roleid::to_roleandsubid',
		'vtiger_datashare_rs2grp'=>'share_roleandsubid',
		'vtiger_datashare_rs2role'=>'share_roleandsubid::to_roleid',
		'vtiger_datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid',
	);
	foreach ($dataShareTableColArr as $tablename => $colname) {
		$colNameArr=explode('::', $colname);
		$query='select shareid from '.$tablename.' where '.$colNameArr[0].'=?';
		$params = array($roleId);
		if (count($colNameArr) >1) {
			$query .=' or '.$colNameArr[1].'=?';
			$params[] = $roleId;
		}
		$result=$adb->pquery($query, $params);
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$shareid=$adb->query_result($result, $i, 'shareid');
			deleteSharingRule($shareid);
		}
	}
	$log->debug('Exiting deleteRoleRelatedSharingRules method...');
}

/** Function to delete the group related sharing rules
 * @param $roleid -- RoleId :: Type varchar
 */
function deleteGroupRelatedSharingRules($grpId) {
	global $log, $adb;
	$log->debug("Entering deleteGroupRelatedSharingRules(".$grpId.") method ...");
	$dataShareTableColArr=array(
		'vtiger_datashare_grp2grp'=>'share_groupid::to_groupid',
		'vtiger_datashare_grp2role'=>'share_groupid',
		'vtiger_datashare_grp2rs'=>'share_groupid',
		'vtiger_datashare_role2group'=>'to_groupid',
		'vtiger_datashare_rs2grp'=>'to_groupid');
	foreach ($dataShareTableColArr as $tablename => $colname) {
		$colNameArr=explode('::', $colname);
		$query='select shareid from '.$tablename.' where '.$colNameArr[0].'=?';
		$params = array($grpId);
		if (count($colNameArr) >1) {
			$query .=' or '.$colNameArr[1].'=?';
			$params[] = $grpId;
		}
		$result=$adb->pquery($query, $params);
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$shareid=$adb->query_result($result, $i, 'shareid');
			deleteSharingRule($shareid);
		}
	}
	$log->debug('Exiting deleteGroupRelatedSharingRules method...');
}

/** Function to get userid and username of all users
  * @returns $userArray -- User Array in the following format:
  * $userArray=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getAllUserName() {
	global $log, $adb;
	$log->debug('Entering getAllUserName() method ...');
	$query='select * from vtiger_users where deleted=0';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$user_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$userid=$adb->query_result($result, $i, 'id');
		$username=getFullNameFromQResult($result, $i, 'Users');
		$user_details[$userid]=$username;
	}
	$log->debug('Exiting getAllUserName method ...');
	return $user_details;
}

/** Function to get groupid and groupname of all groups
  * @returns $grpArray -- Group Array in the following format:
  * $grpArray=Array($grpid1=>$grpname, $grpid2=>$grpname,............,$grpidn=>$grpname);
 */
function getAllGroupName() {
	global $log, $adb;
	$log->debug("Entering getAllGroupName() method ...");
	$query="select groupid, groupname from vtiger_groups";
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$group_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$grpid=$adb->query_result($result, $i, 'groupid');
		$grpname=$adb->query_result($result, $i, 'groupname');
		$group_details[$grpid]=$grpname;
	}
	$log->debug("Exiting getAllGroupName method ...");
	return $group_details;
}

/** Function to get groupid and groupname of all for the given groupid
 * @returns $grpArray -- Group Array in the following format:
 * $grpArray=Array($grpid1=>$grpname);
 */
function getGroupDetails($id) {
	global $log, $adb;
	$log->debug('Entering getAllGroupDetails() method ...');
	$query='select * from vtiger_groups where groupid = ?';
	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	if ($num_rows < 1) {
		return null;
	}
	$group_details=array();
	$grpid=$adb->query_result($result, 0, 'groupid');
	$grpname=$adb->query_result($result, 0, 'groupname');
	$grpdesc=$adb->query_result($result, 0, 'description');
	$group_details=array($grpid,$grpname,$grpdesc);
	$log->debug('Exiting getAllGroupDetails method ...');
	return $group_details;
}

/** Function to get group information of all groups
 * @returns $grpInfoArray -- Group Informaton array in the following format:
 * $grpInfoArray=Array($grpid1=>Array($grpname,description) $grpid2=>Array($grpname,description),............,$grpidn=>Array($grpname,description));
 */
function getAllGroupInfo() {
	global $log, $adb;
	$log->debug("Entering getAllGroupInfo() method ...");
	$query='select * from vtiger_groups';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$group_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$grpInfo=array();
		$grpid=$adb->query_result($result, $i, 'groupid');
		$grpname=$adb->query_result($result, $i, 'groupname');
		$description=$adb->query_result($result, $i, 'description');
		$grpInfo[0]=$grpname;
		$grpInfo[1]=$description;
		$group_details[$grpid]=$grpInfo;
	}
	$log->debug('Exiting getAllGroupInfo method ...');
	return $group_details;
}

/** Function to create a group
 * @param $groupName -- Group Name :: Type varchar
 * @param $groupMemberArray -- Group Members (Groups,Roles,RolesAndsubordinates,Users)
 * @param $groupName -- Group Name :: Type varchar
 * @returns $groupId -- Group Id :: Type integer
 */
function createGroup($groupName, $groupMemberArray, $description) {
	global $log, $adb;
	$log->debug("Entering createGroup(".$groupName.",".print_r($groupMemberArray, true).",".$description.") method ...");
	$groupId=$adb->getUniqueId("vtiger_users");
	//Insert into group vtiger_table
	$query = 'insert into vtiger_groups values(?,?,?)';
	$adb->pquery($query, array($groupId, $groupName, $description));

	//Insert Group to Group Relation
	$groupArray=$groupMemberArray['groups'];
	$roleArray=$groupMemberArray['roles'];
	$rsArray=$groupMemberArray['rs'];
	$userArray=$groupMemberArray['users'];

	foreach ($groupArray as $group_id) {
		insertGroupToGroupRelation($groupId, $group_id);
	}

	//Insert Group to Role Relation
	foreach ($roleArray as $roleId) {
		insertGroupToRoleRelation($groupId, $roleId);
	}

	//Insert Group to RoleAndSubordinate Relation
	foreach ($rsArray as $rsId) {
		insertGroupToRsRelation($groupId, $rsId);
	}

	//Insert Group to Role Relation
	foreach ($userArray as $userId) {
		insertGroupToUserRelation($groupId, $userId);
	}
	$log->debug("Exiting createGroup method ...");
	return $groupId;
}

/** Function to insert group to group relation
 * @param $groupId -- Group Id :: Type integer
 * @param $containsGroupId -- Group Id :: Type integer
 */
function insertGroupToGroupRelation($groupId, $containsGroupId) {
	global $log, $adb;
	$log->debug("Entering insertGroupToGroupRelation(".$groupId.",".$containsGroupId.") method ...");
	$query='insert into vtiger_group2grouprel values(?,?)';
	$adb->pquery($query, array($groupId, $containsGroupId));
	$log->debug('Exiting insertGroupToGroupRelation method ...');
}

/** Function to insert group to role relation
 * @param $groupId -- Group Id :: Type integer
 * @param $roleId -- Role Id :: Type varchar
 */
function insertGroupToRoleRelation($groupId, $roleId) {
	global $log, $adb;
	$log->debug("Entering insertGroupToRoleRelation(".$groupId.",".$roleId.") method ...");
	$query='insert into vtiger_group2role values(?,?)';
	$adb->pquery($query, array($groupId, $roleId));
	$log->debug('Exiting insertGroupToRoleRelation method ...');
}

/** Function to insert group to role & subordinate relation
 * @param $groupId -- Group Id :: Type integer
 * @param $rsId -- Role Sub Id :: Type varchar
 */
function insertGroupToRsRelation($groupId, $rsId) {
	global $log, $adb;
	$log->debug("Entering insertGroupToRsRelation(".$groupId.",".$rsId.") method ...");
	$query='insert into vtiger_group2rs values(?,?)';
	$adb->pquery($query, array($groupId, $rsId));
	$log->debug('Exiting insertGroupToRsRelation method ...');
}

/** Function to insert group to user relation
 * @param $groupId -- Group Id :: Type integer
 * @param $userId -- User Id :: Type varchar
 */
function insertGroupToUserRelation($groupId, $userId) {
	global $log, $adb;
	$log->debug("Entering insertGroupToUserRelation(".$groupId.",".$userId.") method ...");
	$query='insert into vtiger_users2group values(?,?)';
	$adb->pquery($query, array($groupId, $userId));
	$log->debug('Exiting insertGroupToUserRelation method ...');
}

/** Function to get the group Information of the specified group
 * @param $groupId -- Group Id :: Type integer
 * @returns Group Detail Array in the following format:
 *   $groupDetailArray=Array($groupName,$description,$groupMembers);
 */
function getGroupInfo($groupId) {
	global $log, $adb;
	$log->debug("Entering getGroupInfo(".$groupId.") method ...");
	$groupDetailArr=array();
	$groupMemberArr=array();
	//Retreving the group Info
	$query="select * from vtiger_groups where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$groupName=$adb->query_result($result, 0, 'groupname');
	$description=$adb->query_result($result, 0, 'description');

	//Retreving the Group RelatedMembers
	$groupMemberArr=getGroupMembers($groupId);
	$groupDetailArr[]=$groupName;
	$groupDetailArr[]=$description;
	$groupDetailArr[]=$groupMemberArr;

	//Returning the Group Detail Array
	$log->debug("Exiting getGroupInfo method ...");
	return $groupDetailArr;
}

/** Function to fetch the group name of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Name :: Type varchar
 */
function fetchGroupName($groupId) {
	global $log, $adb;
	$log->debug("Entering fetchGroupName(".$groupId.") method ...");
	//Retreving the group Info
	$query='select groupname from vtiger_groups where groupid=?';
	$result = $adb->pquery($query, array($groupId));
	$groupName=decode_html($adb->query_result($result, 0, 'groupname'));
	$log->debug('Exiting fetchGroupName method ...');
	return $groupName;
}

/** Function to fetch the group members of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Member Array in the follwing format:
  *  $groupMemberArray=Array([groups]=>Array(groupid1,groupid2,groupid3,.....,groupidn),
  *                          [roles]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [rs]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [users]=>Array(useridd1,userid2,userid3,.....,groupidn))
 */
function getGroupMembers($groupId) {
	global $log;
	$log->debug("Entering getGroupMembers(".$groupId.") method ...");
	$groupMemberArr=array();
	$roleGroupArr=getGroupRelatedRoles($groupId);
	$rsGroupArr=getGroupRelatedRoleSubordinates($groupId);
	$groupGroupArr=getGroupRelatedGroups($groupId);
	$userGroupArr=getGroupRelatedUsers($groupId);

	$groupMemberArr['groups']=$groupGroupArr;
	$groupMemberArr['roles']=$roleGroupArr;
	$groupMemberArr['rs']=$rsGroupArr;
	$groupMemberArr['users']=$userGroupArr;

	$log->debug("Exiting getGroupMembers method ...");
	return($groupMemberArr);
}

/** Function to get the group related roles of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Role Array in the follwing format:
  *  $groupRoles=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoles($groupId) {
	global $log, $adb;
	$log->debug("Entering getGroupRelatedRoles(".$groupId.") method ...");
	$roleGroupArr=array();
	$query='select roleid from vtiger_group2role where groupid=?';
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$roleId=$adb->query_result($result, $i, 'roleid');
		$roleGroupArr[]=$roleId;
	}
	$log->debug("Exiting getGroupRelatedRoles method ...");
	return $roleGroupArr;
}

/** Function to get the group related roles and subordinates of the specified group
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Roles & Subordinate Array in the follwing format:
  *  $groupRoleSubordinates=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoleSubordinates($groupId) {
	global $log, $adb;
	$log->debug("Entering getGroupRelatedRoleSubordinates(".$groupId.") method ...");
	$rsGroupArr=array();
	$query="select roleandsubid from vtiger_group2rs where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$roleSubId=$adb->query_result($result, $i, 'roleandsubid');
		$rsGroupArr[]=$roleSubId;
	}
	$log->debug("Exiting getGroupRelatedRoleSubordinates method ...");
	return $rsGroupArr;
}

/** Function to get the group related groups
  * @param $groupId -- Group Id :: Type integer
  * @returns Group Related Groups Array in the follwing format:
  *  $groupGroups=Array(grpid1,grpid2,grpid3,.....,grpidn);
 */
function getGroupRelatedGroups($groupId) {
	global $log, $adb;
	$log->debug("Entering getGroupRelatedGroups(".$groupId.") method ...");
	$groupGroupArr=array();
	$query="select containsgroupid from vtiger_group2grouprel where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$relGroupId=$adb->query_result($result, $i, 'containsgroupid');
		$groupGroupArr[]=$relGroupId;
	}
	$log->debug("Exiting getGroupRelatedGroups method ...");
	return $groupGroupArr;
}

/** Function to get the group related users
  * @param $userId -- User Id :: Type integer
  * @returns Group Related Users Array in the follwing format:
  *  $groupUsers=Array(userid1,userid2,userid3,.....,useridn);
 */
function getGroupRelatedUsers($groupId) {
	global $log, $adb;
	$log->debug("Entering getGroupRelatedUsers(".$groupId.") method ...");
	$userGroupArr=array();
	$query="select userid from vtiger_users2group where groupid=?";
	$result = $adb->pquery($query, array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$userId=$adb->query_result($result, $i, 'userid');
		$userGroupArr[]=$userId;
	}
	$log->debug("Exiting getGroupRelatedUsers method ...");
	return $userGroupArr;
}

/** Function to update the group
  * @param $groupId -- Group Id :: Type integer
  * @param $groupName -- Group Name :: Type varchar
  * @param $groupMemberArray -- Group Members Array :: Type array
  * @param $description -- Description :: Type text
 */
function updateGroup($groupId, $groupName, $groupMemberArray, $description) {
	global $log, $adb;
	$log->debug("Entering updateGroup(".$groupId.",".$groupName.",".print_r($groupMemberArray, true).",".$description.") method ...");
	$query="update vtiger_groups set groupname=?, description=? where groupid=?";
	$adb->pquery($query, array($groupName, $description, $groupId));

	//Deleting the Group Member Relation
	deleteGroupRelatedGroups($groupId);
	deleteGroupRelatedRoles($groupId);
	deleteGroupRelatedRolesAndSubordinates($groupId);
	deleteGroupRelatedUsers($groupId);

	//Inserting the Group Member Entries
	$groupArray=$groupMemberArray['groups'];
	$roleArray=$groupMemberArray['roles'];
	$rsArray=$groupMemberArray['rs'];
	$userArray=$groupMemberArray['users'];

	foreach ($groupArray as $group_id) {
		insertGroupToGroupRelation($groupId, $group_id);
	}

	//Insert Group to Role Relation
	foreach ($roleArray as $roleId) {
		insertGroupToRoleRelation($groupId, $roleId);
	}

	//Insert Group to RoleAndSubordinate Relation
	foreach ($rsArray as $rsId) {
		insertGroupToRsRelation($groupId, $rsId);
	}

	//Insert Group to Role Relation
	foreach ($userArray as $userId) {
		insertGroupToUserRelation($groupId, $userId);
	}
	$log->debug("Exiting updateGroup method ...");
}

/** Function to delete the specified group
  * @param $groupId -- Group Id :: Type integer
  * @param $transferId --  Id of the group/user to which record ownership is to be transferred:: Type integer
 */
function deleteGroup($groupId, $transferId) {
	global $log, $adb;
	$log->debug("Entering deleteGroup($groupId) method ...");
	$em = new VTEventsManager($adb);
	// Initialize Event trigger cache
	$em->initTriggerCache();
	$entityData = array();
	$entityData['groupid'] = $groupId;
	$entityData['transferToId'] = $transferId;
	$em->triggerEvent('vtiger.entity.beforegroupdelete', $entityData);
	tranferGroupOwnership($groupId, $transferId);
	deleteGroupRelatedSharingRules($groupId);
	$adb->pquery('delete from vtiger_groups where groupid=?', array($groupId));
	deleteGroupRelatedGroups($groupId);
	deleteGroupRelatedRoles($groupId);
	deleteGroupReportRelations($groupId);
	deleteGroupRelatedRolesAndSubordinates($groupId);
	deleteGroupRelatedUsers($groupId);
	$log->debug('Exiting deleteGroup method ...');
}

/** Function to transfer the ownership of records owned by a particular group to the specified group
  * @param $groupId -- Group Id of the group which's record ownership has to be transferred:: Type integer
  * @param $transferId --  Id of the group/user to which record ownership is to be transferred:: Type integer
 */
function tranferGroupOwnership($groupId, $transferId) {
	global $log, $adb;
	$log->debug("Entering tranferGroupOwnership($groupId) method ...");
	$adb->pquery('update vtiger_crmentity set smownerid=? where smownerid=?', array($transferId, $groupId));
	if (Vtiger_Utils::CheckTable('vtiger_customerportal_prefs')) {
		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($transferId, 'defaultassignee', $groupId);
		$adb->pquery($query, $params);
		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($transferId, 'userid', $groupId);
		$adb->pquery($query, $params);
	}
	$log->debug('Exiting tranferGroupOwnership method ...');
}

/** Function to delete group to group relation of the  specified group
 * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedGroups($groupId) {
	global $log, $adb;
	$log->debug("Entering deleteGroupRelatedGroups($groupId) method ...");
	$adb->pquery('delete from vtiger_group2grouprel where groupid=?', array($groupId));
	$log->debug('Exiting deleteGroupRelatedGroups method ...');
}

/** Function to delete group to role relation of the  specified group
 * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedRoles($groupId) {
	global $log, $adb;
	$log->debug("Entering deleteGroupRelatedRoles($groupId) method ...");
	$adb->pquery('delete from vtiger_group2role where groupid=?', array($groupId));
	$log->debug('Exiting deleteGroupRelatedRoles method ...');
}

/** Function to delete group to role and subordinates relation of the  specified group
 * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedRolesAndSubordinates($groupId) {
	global $log, $adb;
	$log->debug("Entering deleteGroupRelatedRolesAndSubordinates($groupId) method ...");
	$adb->pquery('delete from vtiger_group2rs where groupid=?', array($groupId));
	$log->debug('Exiting deleteGroupRelatedRolesAndSubordinates method ...');
}

/** Function to delete group to user relation of the  specified group
 * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupRelatedUsers($groupId) {
	global $log, $adb;
	$log->debug("Entering deleteGroupRelatedUsers($groupId) method ...");
	$adb->pquery('delete from vtiger_users2group where groupid=?', array($groupId));
	$log->debug('Exiting deleteGroupRelatedUsers method ...');
}

/** This function returns the Default Organisation Sharing Action Name
  * @param $share_action_id -- It takes the Default Organisation Sharing ActionId as input :: Type Integer
  * @returns The sharing Action Name :: Type Varchar
  */
function getDefOrgShareActionName($share_action_id) {
	global $log, $adb;
	$log->debug("Entering getDefOrgShareActionName($share_action_id) method ...");
	$result=$adb->pquery('select share_action_name from vtiger_org_share_action_mapping where share_action_id=?', array($share_action_id));
	$share_action_name=$adb->query_result($result, 0, 'share_action_name');
	$log->debug('Exiting getDefOrgShareActionName method ...');
	return $share_action_name;
}

/** This function returns the Default Organisation Sharing Action Array for the specified Module
  * It takes the module tabid as input and constructs the array.
  * The output array consists of the 'Default Organisation Sharing Id'=>'Default Organisation Sharing Action' mapping for all the sharing actions available for the module
  * The output Array will be in the following format:
  *    Array = (Default Org ActionId1=>Default Org ActionName1,
  *             Default Org ActionId2=>Default Org ActionName2,
  *                     |
  *                     |
  *              Default Org ActionIdn=>Default Org ActionNamen)
  */
function getModuleSharingActionArray($tabid) {
	global $log, $adb;
	$log->debug("Entering getModuleSharingActionArray($tabid) method ...");
	$share_action_arr=array();
	$query = 'select vtiger_org_share_action_mapping.share_action_name,vtiger_org_share_action2tab.share_action_id
		from vtiger_org_share_action2tab
		inner join vtiger_org_share_action_mapping on vtiger_org_share_action2tab.share_action_id=vtiger_org_share_action_mapping.share_action_id
		where vtiger_org_share_action2tab.tabid=?';
	$result=$adb->pquery($query, array($tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$share_action_name=$adb->query_result($result, $i, 'share_action_name');
		$share_action_id=$adb->query_result($result, $i, 'share_action_id');
		$share_action_arr[$share_action_id] = $share_action_name;
	}
	$log->debug('Exiting getModuleSharingActionArray method ...');
	return $share_action_arr;
}

/** This function adds a organisation level sharing rule for the specified Module
  * It takes the following input parameters:
  * 	$tabid -- Module tabid - Datatype::Integer
  * 	$shareEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$toEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$shareEntityId -- The id of the group,role,rs,user to be shared
  * 	$toEntityId -- The id of the group,role,rs,user to which the specified entity is to be shared
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  * This function will return the shareid as output
  */
function addSharingRule($tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission) {
	global $log, $adb;
	$log->debug("Entering addSharingRule(".$tabid.",".$shareEntityType.",".$toEntityType.",".$shareEntityId.",".$toEntityId.",".$sharePermission.") method ...");
	$shareid=$adb->getUniqueId("vtiger_datashare_module_rel");

	if ($shareEntityType == 'groups' && $toEntityType == 'groups') {
		$type_string='GRP::GRP';
		$query = "insert into vtiger_datashare_grp2grp values(?,?,?,?)";
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'roles') {
		$type_string='GRP::ROLE';
		$query = "insert into vtiger_datashare_grp2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'rs') {
		$type_string='GRP::RS';
		$query = "insert into vtiger_datashare_grp2rs values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'groups') {
		$type_string='ROLE::GRP';
		$query = "insert into vtiger_datashare_role2group values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'roles') {
		$type_string='ROLE::ROLE';
		$query = "insert into vtiger_datashare_role2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'rs') {
		$type_string='ROLE::RS';
		$query = "insert into vtiger_datashare_role2rs values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'groups') {
		$type_string='RS::GRP';
		$query = "insert into vtiger_datashare_rs2grp values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'roles') {
		$type_string='RS::ROLE';
		$query = "insert into vtiger_datashare_rs2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'rs') {
		$type_string='RS::RS';
		$query = "insert into vtiger_datashare_rs2rs values(?,?,?,?)";
	}
	$query1 = "insert into vtiger_datashare_module_rel values(?,?,?)";
	$adb->pquery($query1, array($shareid, $tabid, $type_string));
	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);
	$log->debug("Exiting addSharingRule method ...");
	return $shareid;
}

/** This function is to update the organisation level sharing rule
  * It takes the following input parameters:
  *     $shareid -- Id of the Sharing Rule to be updated
  * 	$tabid -- Module tabid - Datatype::Integer
  * 	$shareEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$toEntityType -- The Entity Type may be vtiger_groups,roles,rs and vtiger_users - Datatype::String
  * 	$shareEntityId -- The id of the group,role,rs,user to be shared
  * 	$toEntityId -- The id of the group,role,rs,user to which the specified entity is to be shared
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  * This function will return the shareid as output
  */
function updateSharingRule($shareid, $tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission) {
	global $log, $adb;
	$log->debug("Entering updateSharingRule($shareid, $tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission) method");
	$res=$adb->pquery('select * from vtiger_datashare_module_rel where shareid=?', array($shareid));
	$typestr=$adb->query_result($res, 0, 'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$adb->pquery('delete from '.$tabname.' where shareid=?', array($shareid));

	if ($shareEntityType == 'groups' && $toEntityType == 'groups') {
		$type_string='GRP::GRP';
		$query = "insert into vtiger_datashare_grp2grp values(?,?,?,?)";
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'roles') {
		$type_string='GRP::ROLE';
		$query = "insert into vtiger_datashare_grp2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'rs') {
		$type_string='GRP::RS';
		$query = "insert into vtiger_datashare_grp2rs values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'groups') {
		$type_string='ROLE::GRP';
		$query = "insert into vtiger_datashare_role2group values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'roles') {
		$type_string='ROLE::ROLE';
		$query = "insert into vtiger_datashare_role2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'rs') {
		$type_string='ROLE::RS';
		$query = "insert into vtiger_datashare_role2rs values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'groups') {
		$type_string='RS::GRP';
		$query = "insert into vtiger_datashare_rs2grp values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'roles') {
		$type_string='RS::ROLE';
		$query = "insert into vtiger_datashare_rs2role values(?,?,?,?)";
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'rs') {
		$type_string='RS::RS';
		$query = "insert into vtiger_datashare_rs2rs values(?,?,?,?)";
	}
	$query1 = "update vtiger_datashare_module_rel set relationtype=? where shareid=?";
	$adb->pquery($query1, array($type_string, $shareid));
	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);
	$log->debug("Exiting updateSharingRule method ...");
	return $shareid;
}

/** This function is to delete the organisation level sharing rule
  * It takes the following input parameters:
  *     $shareid -- Id of the Sharing Rule to be updated
  */
function deleteSharingRule($shareid) {
	global $log, $adb;
	$log->debug("Entering deleteSharingRule($shareid) method ...");
	$res=$adb->pquery('select * from vtiger_datashare_module_rel where shareid=?', array($shareid));
	$typestr=$adb->query_result($res, 0, 'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$adb->pquery("delete from $tabname where shareid=?", array($shareid));
	$adb->pquery('delete from vtiger_datashare_module_rel where shareid=?', array($shareid));
	//deleting the releated module sharing permission
	$adb->pquery('delete from vtiger_datashare_relatedmodule_permission where shareid=?', array($shareid));
	$log->debug('Exiting deleteSharingRule method ...');
}

/** Function get the Data Share Table and their columns
  * @returns -- Data Share Table and Column Array in the following format:
  *  $dataShareTableColArr=Array();
  */
function getDataShareTableandColumnArray() {
	global $log;
	$log->debug("Entering getDataShareTableandColumnArray() method ...");
	$dataShareTableColArr=array(
		'vtiger_datashare_grp2grp'=>'share_groupid::to_groupid',
		'vtiger_datashare_grp2role'=>'share_groupid::to_roleid',
		'vtiger_datashare_grp2rs'=>'share_groupid::to_roleandsubid',
		'vtiger_datashare_role2group'=>'share_roleid::to_groupid',
		'vtiger_datashare_role2role'=>'share_roleid::to_roleid',
		'vtiger_datashare_role2rs'=>'share_roleid::to_roleandsubid',
		'vtiger_datashare_rs2grp'=>'share_roleandsubid::to_groupid',
		'vtiger_datashare_rs2role'=>'share_roleandsubid::to_roleid',
		'vtiger_datashare_rs2rs'=>'share_roleandsubid::to_roleandsubid');
	$log->debug("Exiting getDataShareTableandColumnArray method ...");
	return $dataShareTableColArr;
}

/** Function get the Data Share Column Names for the specified Table Name
 *  @param $tableName -- DataShare Table Name :: Type Varchar
 *  @returns Column Name -- Type Varchar
 */
function getDSTableColumns($tableName) {
	global $log;
	$log->debug("Entering getDSTableColumns(".$tableName.") method ...");
	$dataShareTableColArr=getDataShareTableandColumnArray();
	$dsTableCols=$dataShareTableColArr[$tableName];
	$dsTableColsArr=explode('::', $dsTableCols);
	$log->debug("Exiting getDSTableColumns method ...");
	return $dsTableColsArr;
}

/** Function get the Data Share Table Names
 *  @returns the following Date Share Table Name Array:
 *  $dataShareTableColArr=Array();
 */
function getDataShareTableName() {
	global $log;
	$log->debug("Entering getDataShareTableName() method ...");
	$dataShareTableColArr=array(
		'GRP::GRP'=>'vtiger_datashare_grp2grp',
		'GRP::ROLE'=>'vtiger_datashare_grp2role',
		'GRP::RS'=>'vtiger_datashare_grp2rs',
		'ROLE::GRP'=>'vtiger_datashare_role2group',
		'ROLE::ROLE'=>'vtiger_datashare_role2role',
		'ROLE::RS'=>'vtiger_datashare_role2rs',
		'RS::GRP'=>'vtiger_datashare_rs2grp',
		'RS::ROLE'=>'vtiger_datashare_rs2role',
		'RS::RS'=>'vtiger_datashare_rs2rs');
	$log->debug("Exiting getDataShareTableName method ...");
	return $dataShareTableColArr;
}

/** Function to get the Data Share Table Name from the speciified type string
 *  @param $typeString -- Datashare Type Sting :: Type Varchar
 *  @returns Table Name -- Type Varchar
 */
function getDSTableNameForType($typeString) {
	global $log;
	$log->debug("Entering getDSTableNameForType(".$typeString.") method ...");
	$dataShareTableColArr=getDataShareTableName();
	$tableName=$dataShareTableColArr[$typeString];
	$log->debug("Exiting getDSTableNameForType method ...");
	return $tableName;
}

/** Function to get the Entity type from the specified DataShare Table Column Name
 *  @param $colname -- Datashare Table Column Name :: Type Varchar
 *  @returns The entity type. The entity type may be vtiger_groups or vtiger_roles or rs -- Type Varchar
 */
function getEntityTypeFromCol($colName) {
	global $log;
	$log->debug("Entering getEntityTypeFromCol(".$colName.") method ...");
	if ($colName == 'share_groupid' || $colName == 'to_groupid') {
		$entity_type='groups';
	} elseif ($colName =='share_roleid' || $colName =='to_roleid') {
		$entity_type='roles';
	} elseif ($colName == 'share_roleandsubid' || $colName == 'to_roleandsubid') {
		$entity_type='rs';
	}
	$log->debug("Exiting getEntityTypeFromCol method ...");
	return $entity_type;
}

/** Function to get the Entity Display Link
 *  @param $entityid -- Entity Id
 *  @params $entityType --  The entity type may be vtiger_groups or vtiger_roles or rs -- Type Varchar
 *  @returns the Entity Display link
 */
function getEntityDisplayLink($entityType, $entityid) {
	global $log;
	$log->debug("Entering getEntityDisplayLink(".$entityType.",".$entityid.") method ...");
	if ($entityType == 'groups') {
		$groupNameArr = getGroupInfo($entityid);
		$display_out="<a href='index.php?module=Settings&action=GroupDetailView&returnaction=OrgSharingDetailView&groupId=".$entityid."'>Group::".$groupNameArr[0].'</a>';
	} elseif ($entityType == 'roles') {
		$roleName=getRoleName($entityid);
		$display_out = "<a href='index.php?module=Settings&action=RoleDetailView&returnaction=OrgSharingDetailView&roleid=".$entityid."'>Role::".$roleName. "</a>";
	} elseif ($entityType == 'rs') {
		$roleName=getRoleName($entityid);
		$display_out="<a href='index.php?module=Settings&action=RoleDetailView&returnaction=OrgSharingDetailView&roleid=".$entityid."'>RoleAndSubordinate::".$roleName.'</a>';
	}
	$log->debug('Exiting getEntityDisplayLink method ...');
	return $display_out;
}

/** Function to get the Sharing rule Info
 *  @param $shareId -- Sharing Rule Id
 *  @returns Sharing Rule Information Array in the following format:
 *    $shareRuleInfoArr=Array($shareId, $tabid, $type, $share_ent_type, $to_ent_type, $share_entity_id, $to_entity_id,$permission);
 */
function getSharingRuleInfo($shareId) {
	global $log;
	$log->debug("Entering getSharingRuleInfo(".$shareId.") method ...");
	global $adb;
	$shareRuleInfoArr=array();
	$query="select tabid, relationtype from vtiger_datashare_module_rel where shareid=?";
	$result=$adb->pquery($query, array($shareId));
	//Retreving the Sharing Tabid
	$tabid=$adb->query_result($result, 0, 'tabid');
	$type=$adb->query_result($result, 0, 'relationtype');

	//Retreiving the Sharing Table Name
	$tableName=getDSTableNameForType($type);

	//Retreiving the Sharing Col Names
	$dsTableColArr=getDSTableColumns($tableName);
	$share_ent_col=$dsTableColArr[0];
	$to_ent_col=$dsTableColArr[1];

	//Retreiving the Sharing Entity Col Types
	$share_ent_type=getEntityTypeFromCol($share_ent_col);
	$to_ent_type=getEntityTypeFromCol($to_ent_col);

	//Retreiving the Value from Table
	$query1="select * from $tableName where shareid=?";
	$result1=$adb->pquery($query1, array($shareId));
	$share_id=$adb->query_result($result1, 0, $share_ent_col);
	$to_id=$adb->query_result($result1, 0, $to_ent_col);
	$permission=$adb->query_result($result1, 0, 'permission');

	//Constructing the Array
	$shareRuleInfoArr[]=$shareId;
	$shareRuleInfoArr[]=$tabid;
	$shareRuleInfoArr[]=$type;
	$shareRuleInfoArr[]=$share_ent_type;
	$shareRuleInfoArr[]=$to_ent_type;
	$shareRuleInfoArr[]=$share_id;
	$shareRuleInfoArr[]=$to_id;
	$shareRuleInfoArr[]=$permission;

	$log->debug("Exiting getSharingRuleInfo method ...");
	return $shareRuleInfoArr;
}

/** This function is to retreive the list of related sharing modules for the specifed module
  * It takes the following input parameters:
  *     $tabid -- The module tabid:: Type Integer
  */
function getRelatedSharingModules($tabid) {
	global $log;
	$log->debug("Entering getRelatedSharingModules(".$tabid.") method ...");
	global $adb;
	$relatedSharingModuleArray=array();
	$query="select * from vtiger_datashare_relatedmodules where tabid=?";
	$result=$adb->pquery($query, array($tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$ds_relmod_id=$adb->query_result($result, $i, 'datashare_relatedmodule_id');
		$rel_tabid=$adb->query_result($result, $i, 'relatedto_tabid');
		$relatedSharingModuleArray[$rel_tabid]=$ds_relmod_id;
	}
	$log->debug("Exiting getRelatedSharingModules method ...");
	return $relatedSharingModuleArray;
}

/** This function is to add the related module sharing permission for a particulare Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *     $tabid -- The module tabid:: Type Integer
  *     $relatedtabid -- The related module tabid:: Type Integer
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  */
function addRelatedModuleSharingPermission($shareid, $tabid, $relatedtabid, $sharePermission) {
	global $log, $adb;
	$log->debug('Entering addRelatedModuleSharingPermission('.$shareid.','.$tabid.','.$relatedtabid.','.$sharePermission.') method ...');
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid, $relatedtabid);
	$adb->pquery('insert into vtiger_datashare_relatedmodule_permission values(?,?,?)', array($shareid, $relatedModuleSharingId, $sharePermission));
	$log->debug('Exiting addRelatedModuleSharingPermission method ...');
}

/** This function is to update the related module sharing permission for a particulare Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *     $tabid -- The module tabid:: Type Integer
  *     $relatedtabid -- The related module tabid:: Type Integer
  * 	$sharePermisson -- This can have the following values:
  *                       0 - Read Only
  *                       1 - Read/Write
  */
function updateRelatedModuleSharingPermission($shareid, $tabid, $relatedtabid, $sharePermission) {
	global $log, $adb;
	$log->debug('Entering updateRelatedModuleSharingPermission('.$shareid.','.$tabid.','.$relatedtabid.','.$sharePermission.') method ...');
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid, $relatedtabid);
	$query='update vtiger_datashare_relatedmodule_permission set permission=? where shareid=? and datashare_relatedmodule_id=?';
	$adb->pquery($query, array($sharePermission, $shareid, $relatedModuleSharingId));
	$log->debug('Exiting updateRelatedModuleSharingPermission method ...');
}

/** This function is to retreive the Related Module Sharing Id
  * It takes the following input parameters:
  *     $tabid -- The module tabid:: Type Integer
  *     $related_tabid -- The related module tabid:: Type Integer
  * This function returns the Related Module Sharing Id
  */
function getRelatedModuleSharingId($tabid, $related_tabid) {
	global $log, $adb;
	$log->debug("Entering getRelatedModuleSharingId(".$tabid.",".$related_tabid.") method ...");
	$query="select datashare_relatedmodule_id from vtiger_datashare_relatedmodules where tabid=? and relatedto_tabid=?";
	$result=$adb->pquery($query, array($tabid, $related_tabid));
	$relatedModuleSharingId=$adb->query_result($result, 0, 'datashare_relatedmodule_id');
	$log->debug("Exiting getRelatedModuleSharingId method ...");
	return $relatedModuleSharingId;
}

/** This function is to retreive the Related Module Sharing Permissions for the specified Sharing Rule
  * It takes the following input parameters:
  *     $shareid -- The Sharing Rule Id:: Type Integer
  *This function will return the Related Module Sharing permissions in an Array in the following format:
  *     $PermissionArray=($relatedTabid1=>$sharingPermission1,
  *			  $relatedTabid2=>$sharingPermission2,
  *					|
  *                                     |
  *                       $relatedTabid-n=>$sharingPermission-n)
  */
function getRelatedModuleSharingPermission($shareid) {
	global $log, $adb;
	$log->debug("Entering getRelatedModuleSharingPermission($shareid) method ...");
	$relatedSharingModulePermissionArray=array();
	$query='select vtiger_datashare_relatedmodules.*,vtiger_datashare_relatedmodule_permission.permission
		from vtiger_datashare_relatedmodules
		inner join vtiger_datashare_relatedmodule_permission on vtiger_datashare_relatedmodule_permission.datashare_relatedmodule_id=vtiger_datashare_relatedmodules.datashare_relatedmodule_id
		where vtiger_datashare_relatedmodule_permission.shareid=?';
	$result=$adb->pquery($query, array($shareid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$relatedto_tabid=$adb->query_result($result, $i, 'relatedto_tabid');
		$permission=$adb->query_result($result, $i, 'permission');
		$relatedSharingModulePermissionArray[$relatedto_tabid]=$permission;
	}
	$log->debug('Exiting getRelatedModuleSharingPermission method ...');
	return $relatedSharingModulePermissionArray;
}

/** This function is to retreive the vtiger_profiles associated with the  the specified user
  * It takes the following input parameters:
  *     $userid -- The User Id:: Type Integer
  *This function will return the vtiger_profiles associated to the specified vtiger_users in an Array in the following format:
  *     $userProfileArray=(profileid1,profileid2,profileid3,...,profileidn);
  */
function getUserProfile($userId) {
	global $log, $adb;
	$log->debug("Entering getUserProfile(".$userId.") method ...");
	$key = 'getUserProfile' . $userId;
	list($profArr,$cached) = VTCacheUtils::lookupCachedInformation($key);
	if ($cached) {
		return $profArr;
	}
	$roleId=fetchUserRole($userId);
	$profArr=array();
	$sql1 = "select profileid from vtiger_role2profile where roleid=?";
	$result1 = $adb->pquery($sql1, array($roleId));
	$num_rows=$adb->num_rows($result1);
	for ($i=0; $i<$num_rows; $i++) {
		$profileid=  $adb->query_result($result1, $i, "profileid");
		$profArr[]=$profileid;
	}
	VTCacheUtils::updateCachedInformation($key, $profArr);
	$log->debug("Exiting getUserProfile method ...");
	return $profArr;
}

/** To retreive the global permission of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $gloabalPerrArray=(view all action id=>permission,
			   edit all action id=>permission)
  */
function getCombinedUserGlobalPermissions($userId) {
	global $log;
	$log->debug("Entering getCombinedUserGlobalPermissions(".$userId.") method ...");
	$profArr=getUserProfile($userId);
	$no_of_profiles = count($profArr);
	$userGlobalPerrArr=getProfileGlobalPermission($profArr[0]);
	if ($no_of_profiles != 1) {
		for ($i=1; $i<$no_of_profiles; $i++) {
			$tempUserGlobalPerrArr=getProfileGlobalPermission($profArr[$i]);
			foreach ($userGlobalPerrArr as $globalActionId => $globalActionPermission) {
				if ($globalActionPermission == 1) {
					$now_permission = $tempUserGlobalPerrArr[$globalActionId];
					if ($now_permission == 0) {
						$userGlobalPerrArr[$globalActionId]=$now_permission;
					}
				}
			}
		}
	}
	$log->debug("Exiting getCombinedUserGlobalPermissions method ...");
	return $userGlobalPerrArr;
}

/** To retreive the vtiger_tab permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $tabPerrArray=(tabid1=>permission,
  *			   tabid2=>permission)
  */
function getCombinedUserTabsPermissions($userId) {
	global $log;
	$log->debug("Entering getCombinedUserTabsPermissions(".$userId.") method ...");
	$profArr=getUserProfile($userId);
	$no_of_profiles = count($profArr);
	$userTabPerrArr=array();
	$userTabPerrArr=getProfileTabsPermission($profArr[0]);
	if ($no_of_profiles != 1) {
		for ($i=1; $i<$no_of_profiles; $i++) {
			$tempUserTabPerrArr=getProfileTabsPermission($profArr[$i]);
			foreach ($userTabPerrArr as $tabId => $tabPermission) {
				if ($tabPermission == 1) {
					$now_permission = $tempUserTabPerrArr[$tabId];
					if ($now_permission == 0) {
						$userTabPerrArr[$tabId]=$now_permission;
					}
				}
			}
		}
	}
	$homeTabid = getTabid('Home');
	if (!array_key_exists($homeTabid, $userTabPerrArr)) {
		$userTabPerrArr[$homeTabid] = 0;
	}
	$log->debug("Exiting getCombinedUserTabsPermissions method ...");
	return $userTabPerrArr;
}

/** To retreive the vtiger_tab acion permissions of the specifed user from the various vtiger_profiles associated with the user
  * @param $userid -- The User Id:: Type Integer
  * @returns  user global permission  array in the following format:
  *     $actionPerrArray=(tabid1=>permission,
  *			   tabid2=>permission);
 */
function getCombinedUserActionPermissions($userId) {
	global $log;
	$log->debug("Entering getCombinedUserActionPermissions(".$userId.") method ...");
	$profArr=getUserProfile($userId);
	$no_of_profiles = count($profArr);
	$actionPerrArr=array();
	$actionPerrArr=getProfileAllActionPermission($profArr[0]);
	if ($no_of_profiles != 1) {
		for ($i=1; $i<$no_of_profiles; $i++) {
			$tempActionPerrArr=getProfileAllActionPermission($profArr[$i]);
			foreach ($actionPerrArr as $tabId => $perArr) {
				foreach ($perArr as $actionid => $per) {
					if ($per == 1) {
						$now_permission = $tempActionPerrArr[$tabId][$actionid];
						if ($now_permission == 0) {
							$actionPerrArr[$tabId][$actionid]=$now_permission;
						}
					}
				}
			}
		}
	}
	$log->debug("Exiting getCombinedUserActionPermissions method ...");
	return $actionPerrArr;
}

/** To retreive the parent vtiger_role of the specified vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  parent vtiger_role array in the following format:
  *     $parentRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getParentRole($roleId) {
	global $log;
	$log->debug("Entering getParentRole(".$roleId.") method ...");
	$key = 'getParentRole' . $roleId;
	list($parentRoleArr,$cached) = VTCacheUtils::lookupCachedInformation($key);
	if ($cached) {
		return $parentRoleArr;
	}
	$roleInfo=getRoleInformation($roleId);
	$parentRole=$roleInfo[$roleId][1];
	$tempParentRoleArr=explode('::', $parentRole);
	$parentRoleArr=array();
	foreach ($tempParentRoleArr as $role_id) {
		if ($role_id != $roleId) {
			$parentRoleArr[]=$role_id;
		}
	}
	VTCacheUtils::updateCachedInformation($key, $parentRoleArr);
	$log->debug("Exiting getParentRole method ...");
	return $parentRoleArr;
}

/** To retreive the subordinate vtiger_roles of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleArray=(roleid1,roleid2,.......,roleidn);
 */
function getRoleSubordinates($roleId) {
	global $log;
	$log->debug("Entering getRoleSubordinates(".$roleId.") method ...");

	// Look at cache first for information
	$roleSubordinates = VTCacheUtils::lookupRoleSubordinates($roleId);

	if ($roleSubordinates === false) {
		global $adb;
		$roleDetails=getRoleInformation($roleId);
		$roleInfo=$roleDetails[$roleId];
		$roleParentSeq=$roleInfo[1];

		$query="select roleid from vtiger_role where parentrole like ? order by parentrole asc";
		$result=$adb->pquery($query, array($roleParentSeq."::%"));
		$num_rows=$adb->num_rows($result);
		$roleSubordinates=array();
		for ($i=0; $i<$num_rows; $i++) {
			$roleid=$adb->query_result($result, $i, 'roleid');
			$roleSubordinates[]=$roleid;
		}
		// Update cache for re-use
		VTCacheUtils::updateRoleSubordinates($roleId, $roleSubordinates);
	}

	$log->debug("Exiting getRoleSubordinates method ...");
	return $roleSubordinates;
}

/** To retreive the subordinate vtiger_roles and vtiger_users of the specified parent vtiger_role
  * @param $roleid -- The Role Id:: Type varchar
  * @returns  subordinate vtiger_role array in the following format:
  *     $subordinateRoleUserArray=(roleid1=>Array(userid1,userid2,userid3),
							   vtiger_roleid2=>Array(userid1,userid2,userid3)
								|
						|
				   vtiger_roleidn=>Array(userid1,userid2,userid3));
 */
function getSubordinateRoleAndUsers($roleId, $users = true) {
	global $log;
	$log->debug("Entering getSubordinateRoleAndUsers(".$roleId.") method ...");
	$subRoleAndUsers=array();
	$subordinateRoles=getRoleSubordinates($roleId);
	$userArray = array();
	foreach ($subordinateRoles as $subRoleId) {
		if ($users) {
			$userArray=getRoleUsers($subRoleId);
		}
		$subRoleAndUsers[$subRoleId]=$userArray;
	}
	$log->debug("Exiting getSubordinateRoleAndUsers method ...");
	return $subRoleAndUsers;
}

function getCurrentUserProfileList() {
	global $adb,$log,$current_user;
	$log->debug('Entering getCurrentUserProfileList() method ...');
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	$profList = array();
	$profListTypeNoMobile = array();
	foreach ($current_user_profiles as $profid) {
		$profilename = '';
		$resprofile = $adb->pquery("SELECT profilename FROM vtiger_profile WHERE profileid = ?", array($profid));
		$profilename = $adb->query_result($resprofile, 0, 'profilename');
		if (strpos($profilename, 'Mobile::') !== false) {
			if (defined('COREBOS_INSIDE_MOBILE')) {
				$profList[] = $profid;
			}
		} else {
			$profListTypeNoMobile[] = $profid;
			if (!defined('COREBOS_INSIDE_MOBILE')) {
				$profList[] = $profid;
			}
		}
	}
	//Check if profile list is empty, because not exist any profile with name Mobile::, to asign the normal profiles
	if (defined('COREBOS_INSIDE_MOBILE') && empty($profList)) {
		$profList = $profListTypeNoMobile;
	}
	$log->debug('Exiting getCurrentUserProfileList method ...');
	return $profList;
}

function getCurrentUserGroupList() {
	global $log,$current_user;
	$log->debug('Entering getCurrentUserGroupList() method ...');
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	$grpList= array();
	if (count($current_user_groups) > 0) {
		foreach ($current_user_groups as $grpid) {
			$grpList[] = $grpid;
		}
	}
	$log->debug('Exiting getCurrentUserGroupList method ...');
	return $grpList;
}

function getSubordinateUsersList() {
	global $log, $current_user;
	$log->debug("Entering getSubordinateUsersList() method ...");
	$user_array=array();
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';

	if (isset($subordinate_roles_users) && count($subordinate_roles_users) > 0) {
		foreach ($subordinate_roles_users as $userArray) {
			foreach ($userArray as $userid) {
				if (! in_array($userid, $user_array)) {
					$user_array[]=$userid;
				}
			}
		}
	}
	$subUserList = constructList($user_array, 'INTEGER');
	$log->debug("Exiting getSubordinateUsersList method ...");
	return $subUserList;
}

function getReadSharingUsersList($module) {
	global $log, $adb, $current_user;
	$log->debug("Entering getReadSharingUsersList(".$module.") method ...");
	$user_array=array();
	$tabid=getTabid($module);
	$query = "select shareduserid from vtiger_tmp_read_user_sharing_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$user_id=$adb->query_result($result, $i, 'shareduserid');
		$user_array[]=$user_id;
	}
	$shareUserList=constructList($user_array, 'INTEGER');
	$log->debug("Exiting getReadSharingUsersList method ...");
	return $shareUserList;
}

function getReadSharingGroupsList($module) {
	global $log, $adb, $current_user;
	$log->debug("Entering getReadSharingGroupsList(".$module.") method ...");
	$grp_array=array();
	$tabid=getTabid($module);
	$query = "select sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$grp_id=$adb->query_result($result, $i, 'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array, 'INTEGER');
	$log->debug("Exiting getReadSharingGroupsList method ...");
	return $shareGrpList;
}

function getWriteSharingGroupsList($module) {
	global $log, $adb, $current_user;
	$log->debug("Entering getWriteSharingGroupsList(".$module.") method ...");
	$grp_array=array();
	$tabid=getTabid($module);
	$query = "select sharedgroupid from vtiger_tmp_write_group_sharing_per where userid=? and tabid=?";
	$result=$adb->pquery($query, array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$grp_id=$adb->query_result($result, $i, 'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array, 'INTEGER');
	$log->debug("Exiting getWriteSharingGroupsList method ...");
	return $shareGrpList;
}

function constructList($array, $data_type) {
	global $log;
	$log->debug("Entering constructList(".print_r($array, true).",".$data_type.") method ...");
	$list= array();
	if (count($array) > 0) {
		$i=0;
		foreach ($array as $value) {
			if ($data_type == "INTEGER") {
				$list[] = $value;
			} elseif ($data_type == "VARCHAR") {
				$list[] = "'".$value."'";
			}
			$i++;
		}
	}
	$log->debug("Exiting constructList method ...");
	return $list;
}

function getListViewSecurityParameter($module) {
	global $log, $current_user;
	$log->debug("Entering getListViewSecurityParameter(".$module.") method ...");

	$tabid=getTabid($module);
	if ($current_user) {
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	}
	$sec_query = '';
	if ($module == 'Leads') {
		$sec_query .= " and (
						vtiger_crmentity.smownerid in($current_user->id)
						or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')
						or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")
						or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Accounts') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Contacts') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Potentials') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";
		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'HelpDesk') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") ";
		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Emails') {
		$sec_query .= " and vtiger_crmentity.smownerid=".$current_user->id." ";
	} elseif ($module == 'Calendar') {
		require_once 'modules/Calendar/CalendarCommon.php';
		$shared_ids = getSharedCalendarId($current_user->id);
		if (isset($shared_ids) && $shared_ids != '') {
			$condition = " or (vtiger_crmentity.smownerid in($shared_ids) and vtiger_activity.visibility = 'Public')";
		} else {
			$condition = '';
		}
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) $condition or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')";

		if (count($current_user_groups) > 0) {
			$sec_query .= " or ((vtiger_groups.groupid in (". implode(",", $current_user_groups) .")))";
		}
		$sec_query .= ")";
	} elseif ($module == 'Quotes') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding criteria for group sharing
		$sec_query .= " or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'PurchaseOrder') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'SalesOrder') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding criteria for group sharing
		$sec_query .= " or (";

		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Invoice') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";
		//Adding criteria for group sharing
		 $sec_query .= " or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'Campaigns') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";

		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'Documents') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'Products') {
		$sec_query .= " and (vtiger_crmentity.smownerid in($current_user->id) " .
				"or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') " .
				"or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";
		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} else {
		$modObj = CRMEntity::getInstance($module);
		$sec_query = $modObj->getListViewSecurityParameter($module);
	}
	$log->debug("Exiting getListViewSecurityParameter method ...");
	return $sec_query;
}

function getSecListViewSecurityParameter($module) {
	global $log, $current_user;
	$log->debug("Entering getSecListViewSecurityParameter($module) method ...");

	$tabid=getTabid($module);
	if ($current_user) {
		require 'user_privileges/user_privileges_'.$current_user->id.'.php';
		require 'user_privileges/sharing_privileges_'.$current_user->id.'.php';
	}

	if ($module == 'Leads') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";

		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Accounts') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Contacts') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Potentials') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or vtiger_potential.related_to in (select crmid from vtiger_crmentity where setype in ('Accounts', 'Contacts') and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid in (".getTabid('Accounts').", ".getTabid('Contacts').") and relatedtabid=".$tabid.")) ";

		if (vtlib_isModuleActive("Accounts")) {
			"or vtiger_potential.related_to in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Accounts' and vtiger_groups.groupid in (select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) ";
		}
		if (vtlib_isModuleActive("Contacts")) {
			"or vtiger_potential.related_to in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Contacts' and vtiger_groups.groupid in (select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Contacts')." and relatedtabid=".$tabid.")) ";
		}
		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'HelpDesk') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") ";

		if (vtlib_isModuleActive("Accounts")) {
			"or vtiger_troubletickets.parent_id in (select crmid from vtiger_crmentity where setype='Accounts' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) or vtiger_troubletickets.parent_id in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Accounts' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) ";
		}

		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Calendar') {
		require_once 'modules/Calendar/CalendarCommon.php';
		$shared_ids = getSharedCalendarId($current_user->id);
		if (isset($shared_ids) && $shared_ids != '') {
			$condition = " or (vtiger_crmentity$module.smownerid in($shared_ids) and vtiger_activity.visibility = 'Public')";
		} else {
			$condition = null;
		}
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) $condition or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%')";

		if (count($current_user_groups) > 0) {
			$sec_query .= " or ((vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .")))";
		}
		$sec_query .= ")";
	} elseif ($module == 'Quotes') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";

		//Adding criteria for account related quotes sharing
		if (vtlib_isModuleActive("Accounts")) {
			$sec_query .= " or vtiger_quotes.accountid in (select crmid from vtiger_crmentity where setype='Accounts' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) or vtiger_quotes.accountid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Accounts' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid."))";
		}
		//Adding criteria for potential related quotes sharing
		if (vtlib_isModuleActive("Potentials")) {
			$sec_query .= " or vtiger_quotes.potentialid in (select crmid from vtiger_crmentity where setype='Potentials' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Potentials')." and relatedtabid=".$tabid.")) or vtiger_quotes.potentialid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Potentials' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Potentials')." and relatedtabid=".$tabid."))";
		}

		//Adding criteria for group sharing
		$sec_query .= " or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'PurchaseOrder') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'SalesOrder') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";
		//Adding criteria for account related so sharing
		if (vtlib_isModuleActive("Accounts")) {
			$sec_query .= " or vtiger_salesorder.accountid in (select crmid from vtiger_crmentity where setype='Accounts' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) or vtiger_salesorder.accountid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Accounts' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid."))";
		}
		//Adding criteria for potential related so sharing
		if (vtlib_isModuleActive("Potentials")) {
			$sec_query .= " or vtiger_salesorder.potentialid in (select crmid from vtiger_crmentity where setype='Potentials' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Potentials')." and relatedtabid=".$tabid.")) or vtiger_salesorder.potentialid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Potentials' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Potentials')." and relatedtabid=".$tabid."))";
		}
		//Adding criteria for quotes related so sharing
		if (vtlib_isModuleActive("Quotes")) {
			$sec_query .= " or vtiger_salesorder.quoteid in (select crmid from vtiger_crmentity where setype='Quotes' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Quotes')." and relatedtabid=".$tabid.")) or vtiger_salesorder.quoteid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Quotes' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Quotes')." and relatedtabid=".$tabid."))";
		}
		//Adding crteria for group sharing
		$sec_query .= " or (";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid."))) ";
	} elseif ($module == 'Invoice') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")";
		//Adding criteria for account related invoice sharing
		if (vtlib_isModuleActive("Accounts")) {
			$sec_query .= " or vtiger_invoice.accountid in (select crmid from vtiger_crmentity where setype='Accounts' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid.")) or vtiger_invoice.accountid in (select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='Accounts' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('Accounts')." and relatedtabid=".$tabid."))";
		}
		//Adding criteria for salesorder related invoice sharing
		if (vtlib_isModuleActive("SalesOrder")) {
			$sec_query .= " or vtiger_invoice.salesorderid in (select crmid from vtiger_crmentity where setype='SalesOrder' and vtiger_crmentity.smownerid in(select shareduserid from vtiger_tmp_read_user_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('SalesOrder')." and relatedtabid=".$tabid.")) or vtiger_invoice.salesorderid in(select crmid from vtiger_crmentity inner join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where setype='SalesOrder' and vtiger_groups.groupid in(select vtiger_tmp_read_group_rel_sharing_per.sharedgroupid from vtiger_tmp_read_group_rel_sharing_per where userid=".$current_user->id." and tabid=".getTabid('SalesOrder')." and relatedtabid=".$tabid."))";
		}
		// Adding criteria for group sharing
		$sec_query .= " or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'Campaigns') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} elseif ($module == 'Documents') {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	} else {
		$sec_query .= " and (vtiger_crmentity$module.smownerid in($current_user->id) or vtiger_crmentity$module.smownerid in(select vtiger_user2role.userid from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like '".$current_user_parent_role_seq."::%') or vtiger_crmentity$module.smownerid in(select shareduserid from vtiger_tmp_read_user_sharing_per where userid=".$current_user->id." and tabid=".$tabid.") or ((";
		if (count($current_user_groups) > 0) {
			$sec_query .= " vtiger_groups$module.groupid in (". implode(",", $current_user_groups) .") or ";
		}
		$sec_query .= " vtiger_groups$module.groupid in(select vtiger_tmp_read_group_sharing_per.sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=".$current_user->id." and tabid=".$tabid.")))) ";
	}
	$log->debug('Exiting getSecListViewSecurityParameter method ...');
	return $sec_query;
}

function get_current_user_access_groups($module) {
	global $log,$adb,$noof_group_rows;
	$log->debug("Entering get_current_user_access_groups($module) method ...");
	$current_user_group_list=getCurrentUserGroupList();
	$sharing_write_group_list=getWriteSharingGroupsList($module);
	$query ="select groupname,groupid from vtiger_groups";
	$params = array();
	$result = null;
	if (count($current_user_group_list) > 0 && count($sharing_write_group_list) > 0) {
		$query .= ' where (groupid in ('. generateQuestionMarks($current_user_group_list) .') or groupid in ('. generateQuestionMarks($sharing_write_group_list) .'))';
		array_push($params, $current_user_group_list, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	} elseif (count($current_user_group_list) > 0) {
		$query .= " where groupid in (". generateQuestionMarks($current_user_group_list) .")";
		$params[] = $current_user_group_list;
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	} elseif (count($sharing_write_group_list) > 0) {
		$query .= " where groupid in (". generateQuestionMarks($sharing_write_group_list) .")";
		$params[] = $sharing_write_group_list;
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	$log->debug('Exiting get_current_user_access_groups method ...');
	return $result;
}
/** Function to get the Group Id for a given group groupname
 *  @param $groupname -- Groupname
 *  @returns Group Id -- Type Integer
 */
function getGrpId($groupname) {
	global $log, $adb;
	$log->debug("Entering getGrpId(".$groupname.") method ...");

	$result = $adb->pquery("select groupid from vtiger_groups where groupname=?", array($groupname));
	if ($result && $adb->num_rows($result)>0) {
		$groupid = $adb->query_result($result, 0, 'groupid');
	} else {
		$groupid = 0;
	}
	$log->debug("Exiting getGrpId method ...");
	return $groupid;
}

/** Function to check permission to access a vtiger_field for a given user
  * @param $fld_module -- Module :: Type String
  * @param $userid -- User Id :: Type integer
  * @param $fieldname -- Field Name :: Type varchar
  * @param $accessmode -- Access Mode :: Type varchar :: readonly or anything else
  * @returns 0 | 1 :: Type varchar if visible or not
 */
function getFieldVisibilityPermission($fld_module, $userid, $fieldname, $accessmode = 'readonly') {
	global $log,$adb, $current_user;
	$log->debug("Entering getFieldVisibilityPermission(".$fld_module.",". $userid.",". $fieldname.", $accessmode) method ...");

	// Check if field is in-active
	$fieldActive = isFieldActive($fld_module, $fieldname);
	if ($fieldActive == false) {
		return '1';
	}

	if (empty($userid)) {
		$userid = $current_user->id;
	}
	require 'user_privileges/user_privileges_'.$userid.'.php';

	/* Asha: Fix for ticket #4508. Users with View all and Edit all permission will also have visibility permission for all fields */
	if ($is_admin || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0) {
		$log->debug("Exiting getFieldVisibilityPermission method ...");
		return '0';
	} else {
		//get profile list using userid
		$profilelist = array();
		foreach ($current_user_profiles as $profid) {
			$profilelist[] = $profid;
		}

		//get tabid
		$tabid = getTabid($fld_module);

		if (count($profilelist) > 0) {
			if ($accessmode == 'readonly') {
				$query='SELECT vtiger_profile2field.visible
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
						AND vtiger_profile2field.profileid in ('. generateQuestionMarks($profilelist) .')
						AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2)
					GROUP BY vtiger_field.fieldid';
			} else {
				$query='SELECT vtiger_profile2field.visible
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0
						AND vtiger_profile2field.profileid in ('. generateQuestionMarks($profilelist) .')
						AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2)
					GROUP BY vtiger_field.fieldid';
			}
			$params = array($tabid, $profilelist, $fieldname);
		} else {
			if ($accessmode == 'readonly') {
				$query='SELECT vtiger_profile2field.visible
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0
						AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2)
					GROUP BY vtiger_field.fieldid';
			} else {
				$query='SELECT vtiger_profile2field.visible
					FROM vtiger_field
					INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid
					INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid
					WHERE vtiger_field.tabid=? AND vtiger_profile2field.visible=0 AND vtiger_profile2field.readonly=0 AND vtiger_def_org_field.visible=0
						AND vtiger_field.fieldname= ? and vtiger_field.presence in (0,2)
					GROUP BY vtiger_field.fieldid';
			}
			$params = array($tabid, $fieldname);
		}
		$result = $adb->pquery($query, $params);
		$log->debug('Exiting getFieldVisibilityPermission method ...');
		if ($adb->num_rows($result) == 0) {
			return '1';
		}
		return ($adb->query_result($result, 0, 'visible').'');
	}
}

/** Function to check permission to access the column for a given user
 * @param $userid -- User Id :: Type integer
 * @param $tablename -- tablename :: Type String
 * @param $columnname -- columnname :: Type String
 * @param $module -- Module Name :: Type varchar
 */
function getColumnVisibilityPermission($userid, $columnname, $module, $accessmode = 'readonly') {
	global $adb,$log;
	$log->debug("in function getcolumnvisibilitypermission $columnname -$userid");
	$tabid = getTabid($module);

	// Look at cache if information is available.
	$cacheFieldInfo = VTCacheUtils::lookupFieldInfoByColumn($tabid, $columnname);
	$fieldname = false;
	if ($cacheFieldInfo === false) {
		$res = $adb->pquery('select fieldname from vtiger_field where tabid=? and columnname=? and vtiger_field.presence in (0,2)', array($tabid, $columnname));
		$fieldname = $adb->query_result($res, 0, 'fieldname');
	} else {
		$fieldname = $cacheFieldInfo['fieldname'];
	}
	return getFieldVisibilityPermission($module, $userid, $fieldname, $accessmode);
}

/** Function to get the field access module array
  * @returns The field Access module Array :: Type Array
 */
function getFieldModuleAccessArray() {
	global $log, $adb;
	$log->debug("Entering getFieldModuleAccessArray() method ...");

	$fldModArr=array();
	$query = 'select distinct(name) from vtiger_profile2field inner join vtiger_tab on vtiger_tab.tabid=vtiger_profile2field.tabid';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$mod_name = $adb->query_result($result, $i, 'name');
		$fldModArr[$mod_name] = $mod_name;
	}
	$log->debug("Exiting getFieldModuleAccessArray method ...");
	return $fldModArr;
}

/** Function to get the module access array
  * @returns The Module Access Array :: Type Array
 */
function getModuleAccessArray() {
	global $log, $adb;
	$log->debug("Entering getModuleAccessArray() method ...");

	$fldModArr=array();
	$query = 'SELECT distinct(name) FROM vtiger_profile2field INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_profile2field.tabid WHERE vtiger_tab.presence IN (0, 2)';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$mod_name = $adb->query_result($result, $i, 'name');
		$fldModArr[$mod_name] = $mod_name;
	}
	$log->debug("Exiting getModuleAccessArray method ...");
	return $fldModArr;
}

/** Function to get the permitted module name Array with presence as 0
  * @returns permitted module name Array :: Type Array
 */
function getPermittedModuleNames() {
	global $log, $adb, $current_user;
	$log->debug('Entering getPermittedModuleNames() method ...');
	$permittedModules = array();
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	include 'tabdata.php';

	if (defined('COREBOS_INSIDE_MOBILE')) {
		if (isset($current_user_profiles)) {
			foreach ($current_user_profiles as $profid) {
				$profilename = '';
				$resprofile = $adb->pquery('SELECT profilename FROM vtiger_profile WHERE profileid = ?', array($profid));
				$profilename = $adb->query_result($resprofile, 0, 'profilename');
				if (strpos($profilename, 'Mobile::') !== false) {
					$profileTabsPermission=getProfileTabsPermission($profid);
				}
			}
		}
	}

	if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
		foreach ($tab_seq_array as $tabid => $seq_value) {
			if ($seq_value === 0 && isset($profileTabsPermission[$tabid]) && $profileTabsPermission[$tabid] === 0) {
				$permittedModules[]=getTabModuleName($tabid);
			}
		}
	} else {
		foreach ($tab_seq_array as $tabid => $seq_value) {
			if ($seq_value === 0) {
				$permittedModules[]=getTabModuleName($tabid);
			}
		}
	}
	$log->debug('Exiting getPermittedModuleNames method ...');
	return $permittedModules;
}

/** Function to get the permitted module id Array with presence as 0
 * @global Users $current_user
 * @return Array Array of accessible tabids.
 */
function getPermittedModuleIdList() {
	global $current_user;
	$permittedModules=array();
	require 'user_privileges/user_privileges_'.$current_user->id.'.php';
	include 'tabdata.php';

	if (defined('COREBOS_INSIDE_MOBILE')) {
		foreach ($current_user_profiles as $profid) {
			$profilename = '';
			$resprofile = $adb->pquery('SELECT profilename FROM vtiger_profile WHERE profileid = ?', array($profid));
			$profilename = $adb->query_result($resprofile, 0, 'profilename');
			if (strpos($profilename, 'Mobile::') !== false) {
				$profileTabsPermission=getProfileTabsPermission($profid);
			}
		}
	}

	if ($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1) {
		foreach ($tab_seq_array as $tabid => $seq_value) {
			if ($seq_value === 0 && isset($profileTabsPermission[$tabid]) && $profileTabsPermission[$tabid] === 0) {
				$permittedModules[]=($tabid);
			}
		}
	} else {
		foreach ($tab_seq_array as $tabid => $seq_value) {
			if ($seq_value === 0) {
				$permittedModules[]=($tabid);
			}
		}
	}
	$homeTabid = getTabid('Home');
	if (!in_array($homeTabid, $permittedModules)) {
		$permittedModules[] = $homeTabid;
	}
	return $permittedModules;
}

/** Function to recalculate the Sharing Rules for all the users
  * This function will recalculate all the sharing rules for all the users in the Organization and will write them in flat files
 */
function RecalculateSharingRules($roleId = 0) {
	global $log, $adb;
	$log->debug('Entering RecalculateSharingRules() method ...');
	require_once 'modules/Users/CreateUserPrivilegeFile.php';

	if (empty($roleId)) {
		$query='SELECT id FROM vtiger_users WHERE deleted=0';
		$result=$adb->pquery($query, array());
	} else {
		$query="SELECT id FROM vtiger_users usr INNER JOIN vtiger_user2role rol ON rol.userid = usr.id WHERE usr.deleted=0 AND usr.status='active' rol.roleid IN (?)";
		$result=$adb->pquery($query, array($roleId));
	}

	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$id=$adb->query_result($result, $i, 'id');
		createUserPrivilegesfile($id);
		createUserSharingPrivilegesfile($id);
	}
	$log->debug('Exiting RecalculateSharingRules method ...');
}

/** Function to get the list of module for which the user defined sharing rules can be defined
  * @returns Array:: Type array
  */
function getSharingModuleList($eliminateModules = false) {
	global $adb;

	$sharingModuleArray = array();

	if (empty($eliminateModules)) {
		$eliminateModules = array();
	}

	// Module that needs to be eliminated explicitly
	if (!in_array('Calendar', $eliminateModules)) {
		$eliminateModules[] = 'Calendar';
	}
	if (!in_array('Events', $eliminateModules)) {
		$eliminateModules[] = 'Events';
	}

	$query = "SELECT name FROM vtiger_tab WHERE presence=0 AND ownedby = 0 AND isentitytype = 1 AND name NOT IN('" . implode("','", $eliminateModules) . "')";

	$result = $adb->query($query);
	while ($resrow = $adb->fetch_array($result)) {
		$sharingModuleArray[] = $resrow['name'];
	}
	return $sharingModuleArray;
}

function isCalendarPermittedBySharing($recordId) {
	global $adb, $current_user;
	$permission = 'no';
	$query = "select 1
			from vtiger_sharedcalendar
			where sharedid=? and
			userid in (
				select smownerid as usrid
				from vtiger_activity
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				where vtiger_activity.activityid=? and visibility='Public' and smownerid !=0
			)
		UNION
			select 1 from vtiger_invitees where vtiger_invitees.activityid=? and inviteeid=?";
	$result = $adb->pquery($query, array($current_user->id, $recordId, $recordId, $current_user->id));
	if ($adb->num_rows($result) > 0) {
		$permission = 'yes';
	}
	return $permission;
}

/* Function to populate default entries for the picklist while creating a new role */
function insertRole2Picklist($roleid, $parentroleid) {
	global $adb,$log;
	$log->debug("Entering into the function insertRole2Picklist($roleid,$parentroleid)");
	$sql = "insert into vtiger_role2picklist select '".$roleid."',picklistvalueid,picklistid,sortid from vtiger_role2picklist where roleid=?";
	$adb->pquery($sql, array($parentroleid));
	$log->debug("Exiting from the function insertRole2Picklist($roleid,$parentroleid)");
}

/** Function to delete group to report relation of the  specified group
 * @param $groupId -- Group Id :: Type integer
 */
function deleteGroupReportRelations($groupId) {
	global $log;
	$log->debug("Entering deleteGroupReportRelations(".$groupId.") method ...");
	global $adb;
	$query="delete from vtiger_reportsharing where shareid=? and setype='groups'";
	$adb->pquery($query, array($groupId));
	$log->debug("Exiting deleteGroupReportRelations method ...");
}

/** Function to check if the field is Active
 *  @params  $modulename -- Module Name :: String Type
 *   		 $fieldname  -- Field Name  :: String Type
 */
function isFieldActive($modulename, $fieldname) {
	$fieldid = getFieldid(getTabid($modulename), $fieldname, true);
	return ($fieldid !== false);
}

/**
 * @param String $module - module name for which query needs to be generated.
 * @param Users $user - user for which query needs to be generated.
 * @return String Access control Query for the user.
 */
function getNonAdminAccessControlQuery($module, $user, $scope = '') {
	$instance = CRMEntity::getInstance($module);
	return $instance->getNonAdminAccessControlQuery($module, $user, $scope);
}

function appendFromClauseToQuery($query, $fromClause) {
	$query = preg_replace('/\s+/', ' ', $query);
	$condition = substr($query, strripos($query, ' where '), strlen($query));
	$newQuery = substr($query, 0, strripos($query, ' where '));
	$query = $newQuery.$fromClause.$condition;
	return $query;
}
?>
