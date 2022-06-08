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
require_once 'include/DatabaseUtil.php';
include 'config.inc.php';
global $log;

/** To retrieve the mail server info resultset for the specified user
 * @param object user object
 * @return object mail server database information resultset
*/
function getMailServerInfo($user) {
	global $log, $adb;
	$log->debug('> getMailServerInfo '.$user->user_name);
	$result = $adb->pquery('select * from vtiger_mail_accounts where status=1 and user_id=?', array($user->id));
	$log->debug('< getMailServerInfo');
	return $result;
}

/** To get the Role of the specified user
  * @param integer user ID
  * @return string role ID
 */
function fetchUserRole($userid) {
	global $log, $adb;
	$log->debug('> fetchUserRole '.$userid);
	$key = 'fetchUserRole' . $userid;
	list($roleid,$cached) = VTCacheUtils::lookupCachedInformation($key);
	if ($cached) {
		return $roleid;
	}
	$result = $adb->pquery('select roleid from vtiger_user2role where userid=?', array($userid));
	$roleid = $adb->query_result($result, 0, 'roleid');
	VTCacheUtils::updateCachedInformation($key, $roleid);
	$log->debug('< fetchUserRole');
	return $roleid;
}

/** Function to be replaced by getUserProfile()
 * @deprecated
 */
function fetchUserProfileId($userid) {
	global $log;
	$log->debug('> fetchUserProfileId '.$userid);

	// Look up information in cache first
	$profileid = VTCacheUtils::lookupUserProfileId($userid);

	if ($profileid === false) {
		global $adb;

		$result = $adb->pquery('SELECT profileid FROM vtiger_role2profile WHERE roleid=(SELECT roleid FROM vtiger_user2role WHERE userid=?)', array($userid));

		if ($result && $adb->num_rows($result)) {
			$profileid = $adb->query_result($result, 0, 'profileid');
		}

		// Update information to cache for re-use
		VTCacheUtils::updateUserProfileId($userid, $profileid);
	}
	$log->debug('< fetchUserProfileId');
	return $profileid;
}

/** Function to get the lists of groupids related with a user
 * @param integer user id of the user we want to get the groups of
 * @return string comma-separated string of the groupids related with the user
*/
function fetchUserGroupids($userid) {
	global $log;
	$log->debug('> fetchUserGroupids '.$userid);
	$focus = new GetUserGroups();
	$focus->getAllUserGroups($userid);
	$log->debug('< fetchUserGroupids');
	return implode(',', $focus->user_groups);
}

/** Function to get all the vtiger_tab permission for the specified vtiger_profile
  * @param integer Profile Id
  * @return array TabPermission array in the following format:
  * $tabPermission = Array($tabid1=>permission,
  *                        $tabid2=>permission,
  *                                |
  *                        $tabidn=>permission)
 */
function getAllTabsPermission($profileid) {
	global $log,$adb;
	$log->debug('> getAllTabsPermission '.$profileid);
	$result = $adb->pquery('select tabid, permissions from vtiger_profile2tab where profileid=?', array($profileid));
	$tab_perr_array = array();
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$tabid= $adb->query_result($result, $i, 'tabid');
		$tab_per= $adb->query_result($result, $i, 'permissions');
		$tab_perr_array[$tabid] = $tab_per;
	}
	$log->debug('< getAllTabsPermission');
	return $tab_perr_array;
}

/** Function to get all the tab permission for the specified profile other than tabid 15
  * @param integer Profile Id
  * @return array TabPermission array in the following format:
  * $tabPermission = Array($tabid1=>permission,
  *                        $tabid2=>permission,
  *                                |
  *                        $tabidn=>permission)
 */
function getTabsPermission($profileid) {
	global $log, $adb;
	$log->debug('> getTabsPermission '.$profileid);
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
	$log->debug('< getTabsPermission');
	return $tab_perr_array;
}

 /** Function to get all the vtiger_tab standard action permission for the specified vtiger_profile
  * @param integer Profile Id
  * @return array Tab Action Permission array in the following format:
  * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getTabsActionPermission($profileid) {
	global $log,$adb;
	$log->debug('> getTabsActionPermission '.$profileid);
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
	$log->debug('< getTabsActionPermission');
	return $check;
}

/** Function to get all the vtiger_tab utility action permission for the specified vtiger_profile
  * @param integer Profile Id
  * @return array Tab Utility Action Permission array in the following format:
  * $tabPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getTabsUtilityActionPermission($profileid) {
	global $log, $adb;
	$log->debug('> getTabsUtilityActionPermission '.$profileid);
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
	$log->debug('< getTabsUtilityActionPermission');
	return $check;
}

/** This Function returns the Default Organization Sharing Action array for all modules whose sharing actions are editable
 * @return array with following format:
 * Arr=(tabid1=>Sharing Action Id,
 *      tabid2=>Sharing Action Id,
 *            |
 *      tabidn=>Sharing Acion Id)
 */
function getDefaultSharingEditAction() {
	global $log, $adb;
	$log->debug('> getDefaultSharingEditAction');
	$copy = array();
	$result = $adb->pquery('select tabid,permission from vtiger_def_org_share where editstatus=0', array());
	while ($permissionRow=$adb->fetch_array($result)) {
		$copy[$permissionRow['tabid']] = $permissionRow['permission'];
	}
	$log->debug('< getDefaultSharingEditAction');
	return $copy;
}

/** This Function returns the Default Organization Sharing Action array for modules with edit status in (0,1)
  * @return array with following format:
  * Arr=(tabid1=>Sharing Action Id,
  *      tabid2=>Sharing Action Id,
  *            |
  *      tabidn=>Sharing Acion Id)
  */
function getDefaultSharingAction() {
	global $log, $adb;
	$log->debug('> getDefaultSharingAction');
	//retrieve standard permissions
	$copy = array();
	$result = $adb->pquery('select tabid, permission from vtiger_def_org_share where editstatus in(0,1)', array());
	while ($permissionRow=$adb->fetch_array($result)) {
		$copy[$permissionRow['tabid']] = $permissionRow['permission'];
	}
	$log->debug('< getDefaultSharingAction');
	return $copy;
}

/** This Function returns the Default Organisation Sharing Action array for all modules
 * @return array with following format:
 * Arr=(tabid1=>Sharing Action Id,
 *      tabid2=>SharingAction Id,
 *            |
 *            |
 *            |
 *      tabid3=>SharingAcion Id)
 */
function getAllDefaultSharingAction() {
	global $log,$adb;
	$log->debug('> getAllDefaultSharingAction');
	$copy=array();
	$result = $adb->pquery('select tabid, permission from vtiger_def_org_share', array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$tabid=$adb->query_result($result, $i, 'tabid');
		$permission=$adb->query_result($result, $i, 'permission');
		$copy[$tabid]=$permission;
	}
	$log->debug('< getAllDefaultSharingAction');
	return $copy;
}

/** Function to create a role
 * @param string Role Name
 * @param string Parent Role ID
 * @param array Profile to be associated with this role
 * @return string Rold Id
 */
function createRole($roleName, $parentRoleId, $roleProfileArray) {
	global $log,$adb;
	$log->debug('> createRole', [$roleName, $parentRoleId, $roleProfileArray]);
	$parentRoleDetails=getRoleInformation($parentRoleId);
	$parentRoleInfo=$parentRoleDetails[$parentRoleId];
	$roleid_no=$adb->getUniqueId('vtiger_role');
	$roleId='H'.$roleid_no;
	$parentRoleHr=$parentRoleInfo[1];
	$parentRoleDepth=$parentRoleInfo[2];
	$nowParentRoleHr=$parentRoleHr.'::'.$roleId;
	$nowRoleDepth=$parentRoleDepth + 1;

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates($roleId);

	//Inserting role into db
	$qparams = array($roleId,$roleName,$nowParentRoleHr,$nowRoleDepth);
	$adb->pquery('insert into vtiger_role values(?,?,?,?)', $qparams);

	//Inserting into vtiger_role2profile vtiger_table
	foreach ($roleProfileArray as $profileId) {
		if ($profileId != '') {
			insertRole2ProfileRelation($roleId, $profileId);
		}
	}
	$log->debug('< createRole');
	return $roleId;
}

/** Function to update the role
 * @param string Role Name
 * @param string Role Id
 * @param array Profile to be associated with this role
 */
function updateRole($roleId, $roleName, $roleProfileArray) {
	global $log,$adb;
	$log->debug('> updateRole', [$roleId, $roleName, $roleProfileArray]);

	// Invalidate any cached information
	VTCacheUtils::clearRoleSubordinates($roleId);

	$adb->pquery('update vtiger_role set rolename=? where roleid=?', array($roleName, $roleId));
	//Updating the Role2Profile relation
	$adb->pquery('delete from vtiger_role2profile where roleId=?', array($roleId));

	foreach ($roleProfileArray as $profileId) {
		if ($profileId != '') {
			insertRole2ProfileRelation($roleId, $profileId);
		}
	}
	$log->debug('< updateRole');
}

/** Function to add the role to profile relation
 * @param integer Profile Id
 * @param string Role Id
 */
function insertRole2ProfileRelation($roleId, $profileId) {
	global $log, $adb;
	$adb->pquery('insert into vtiger_role2profile values(?,?)', array($roleId,$profileId));
	$log->debug('>< insertRole2ProfileRelation'.$roleId.','.$profileId);
}

/** Function to get the roleid from rolename
 * @param string Role Name
 * @return string Role Id
 */
function fetchRoleId($rolename) {
	global $log, $adb;
	$log->debug('> fetchRoleId '.$rolename);
	$resultroleid = $adb->pquery('select roleid from vtiger_role where rolename=?', array($rolename));
	$role_id = $adb->query_result($resultroleid, 0, 'roleid');
	$log->debug('< fetchRoleId');
	return $role_id;
}

/** Function to update user to role mapping based on the userid
 * @param string Role Id
 * @param integer User Id
 */
function updateUser2RoleMapping($roleid, $userid) {
	global $log, $adb;
	$log->debug('> updateUser2RoleMapping '.$roleid.','.$userid);
	//Check if row already exists
	$resultcheck = $adb->pquery('select userid from vtiger_user2role where userid=? limit 1', array($userid));
	if ($adb->num_rows($resultcheck) == 1) {
		$adb->pquery('delete from vtiger_user2role where userid=?', array($userid));
	}
	$adb->pquery('insert into vtiger_user2role(userid,roleid) values(?,?)', array($userid, $roleid));
	$log->debug('< updateUser2RoleMapping');
}

/** Function to update user to group mapping based on the userid
 * @param string Group Name
 * @param integer User Id
 */
function updateUsers2GroupMapping($groupname, $userid) {
	global $log, $adb;
	$log->debug('> updateUsers2GroupMapping '.$groupname.','.$userid);
	$adb->pquery('delete from vtiger_users2group where userid = ?', array($userid));
	$adb->pquery('insert into vtiger_users2group(groupname,userid) values(?,?)', array($groupname, $userid));
	$log->debug('< updateUsers2GroupMapping');
}

/** Function to add user to role mapping
 * @param string Role Id
 * @param integer User Id
 */
function insertUser2RoleMapping($roleid, $userid) {
	global $log, $adb;
	$log->debug('> insertUser2RoleMapping '.$roleid.','.$userid);
	$adb->pquery('insert into vtiger_user2role(userid,roleid) values(?,?)', array($userid, $roleid));
	$log->debug('< insertUser2RoleMapping');
}

/** Function to add user to group mapping
 * @param string Group Name
 * @param integer User Id
 */
function insertUsers2GroupMapping($groupname, $userid) {
	global $log, $adb;
	$log->debug('> insertUsers2GroupMapping '.$groupname.','.$userid);
	$adb->pquery('insert into vtiger_users2group(groupname,userid) values(?,?)', array($groupname, $userid));
	$log->debug('< insertUsers2GroupMapping');
}

/** Function to get the word template resultset
 * @param string Module Name
 * @return resultset
 */
function fetchWordTemplateList($module) {
	global $log, $adb;
	$log->debug('> fetchWordTemplateList '.$module);
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('Documents');
	$result=$adb->pquery(
		'select notesid, filename
			from vtiger_notes
			inner join '.$crmEntityTable.' on crmid=notesid
			where deleted=0 and template_for=?',
		array($module)
	);
	$log->debug('< fetchWordTemplateList');
	return $result;
}

/** Function to get the email template iformation
 * @param string Template Name
 * @return resultset
 */
function fetchEmailTemplateInfo($templateName, $desired_lang = null, $default_lang = null) {
	require_once 'modules/cbtranslation/cbtranslation.php';
	global $log, $adb, $current_user, $default_language;
	$log->debug('> fetchEmailTemplateInfo '.$templateName);
	if (empty($desired_lang)) {
		$desired_lang = cbtranslation::getShortLanguageName($current_user->language);
	}
	if (empty($default_lang)) {
		$default_lang = cbtranslation::getShortLanguageName($default_language);
	}
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('MsgTemplate');
	$sql = 'select * from vtiger_msgtemplate inner join '.$crmEntityTable.' on crmid=msgtemplateid where deleted=0 and reference=?';
	$result = $adb->pquery($sql.' and msgt_language=?', array($templateName, $desired_lang));
	if (!$result) {
		$result = $adb->pquery($sql.' and msgt_language=?', array($templateName, $default_lang));
	}
	if (!$result) {
		$result = $adb->pquery($sql, array($templateName));
	}

	$log->debug('< fetchEmailTemplateInfo');
	return $result;
}

/** Function to substitute the tokens in the specified file
 * @param string Template Name
 * @param array globals
 * @deprecated
 */
function substituteTokens($filename, $globals) {
	return '';
}

/** Function to get the role name from a role ID
 * @param string Role ID
 * @return string Role Name
 */
function getRoleName($roleid) {
	global $log, $adb;
	$log->debug('> getRoleName '.$roleid);
	$result = $adb->pquery('select rolename from vtiger_role where roleid=?', array($roleid));
	$rolename = $adb->query_result($result, 0, 'rolename');
	$log->debug('< getRoleName');
	return $rolename;
}

/** Function to get the profile name from a profile ID
 * @param integer Profile ID
 * @return string Role Name
 */
function getProfileName($profileid) {
	global $log, $adb;
	$log->debug('> getProfileName '.$profileid);
	$result = $adb->pquery('select profilename from vtiger_profile where profileid=?', array($profileid));
	$profilename = $adb->query_result($result, 0, 'profilename');
	$log->debug('< getProfileName');
	return $profilename;
}

/** Function to get the profile description of a profile ID
 * @param integer Profile ID
 * @return string Role Name
 */
function getProfileDescription($profileid) {
	global $log, $adb;
	$log->debug('> getProfileDescription '.$profileid);
	$result = $adb->pquery('select description from vtiger_profile where profileid=?', array($profileid));
	$profileDescription = $adb->query_result($result, 0, 'description');
	$log->debug('< getProfileDescription');
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
			&& ($uiinfo->getCompany() != null || $uiinfo->isModuleActive('Contacts'));
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
 * @param string Module Name
 * @param string Action Name
 * @param integer Record ID
 * @return string yes/no: If Yes means this action is allowed for the currently logged in user. If no means this action is not allowed for the currently logged in user
 */
function _vtisPermitted($module, $actionname, $record_id = '') {
	global $log, $adb, $current_user;
	if ($module=='com_vtiger_workflow') {
		$module='CronTasks';
	}
	$log->debug('> isPermitted '.$module.','.$actionname.','.$record_id);
	if (strpos($record_id, 'x')>0) { // is webserviceid
		list($void,$record_id) = explode('x', $record_id);
	}
	if (!empty($record_id) && $module != getSalesEntityType($record_id)) {
		$record_id = '';
	}
	$userprivs = $current_user->getPrivileges();
	$is_admin = is_admin($current_user);
	if ($is_admin) {
		$log->debug('< isPermitted administrator yes');
		return 'yes';
	}
	$permission = 'no';
	if ($module == 'Users' || $module == 'Home' || $module == 'Utilities') {
		//These modules dont have security right now
		$log->debug('< isPermitted module access yes');
		return 'yes';
	}

	//Checking the Access for the Settings Module
	if ($module == 'Settings') {
		if (!$is_admin) {
			$permission = 'no';
		} else {
			$permission = 'yes';
		}
		$log->debug('< isPermitted Settings '.$permission);
		return $permission;
	}

	$tabid = getTabid($module);
	$actionid=getActionid($actionname);
	// If no actionid, then allow action if tab permission is available
	if ($actionid === '') {
		if ($userprivs->hasModuleAccess($tabid)) {
			$permission = 'yes';
		} else {
			$permission ='no';
		}
		$log->debug('< isPermitted no actionid '.$permission);
		return $permission;
	}

	//Checking for view all permission
	if ($userprivs->hasGlobalReadPermission() && ($actionid == 3 || $actionid == 4)) {
		$log->debug('< isPermitted view all permission yes');
		return 'yes';
	}
	//Checking for edit all permission
	if ($userprivs->hasGlobalWritePermission() && ($actionid == 3 || $actionid == 4 || $actionid ==0 || $actionid ==1)) {
		$log->debug('< isPermitted edit all permission yes');
		return 'yes';
	}
	//Checking for tab permission
	if (!is_null($tabid) && !$userprivs->hasModuleAccess($tabid)) {
		$log->debug('< isPermitted tab permission no');
		return 'no';
	}
	//Checking for Action Permission
	$action = getActionname($actionid);
	$ternary = $userprivs->getModulePermission($tabid, $actionid);
	if (is_null($ternary) && ($action == 'Export' || $action == 'Import')) {
		// if permission is not defined we do not permit export/import
		$log->debug('< isPermitted profile action permission Export/Import');
		return 'no';
	}
	if (is_null($ternary) || $ternary === '') {
		if ($action=='DuplicatesHandling' && in_array($module, getInventoryModules())) {
			$permission = 'no';
		} else {
			$permission = 'yes';
		}
		$log->debug('< isPermitted profile action permission not set '.$permission);
		return $permission;
	}

	if ($ternary != 0 && $ternary != '') {
		$log->debug('< isPermitted profile action permission no');
		return 'no';
	}
	//Checking and returning true if recorid is null
	if ($record_id == '') {
		$log->debug('< isPermitted has permission on module and record is not set yes');
		return 'yes';
	}

	//If module is not shareable, everyone has access. Faq, PriceBook, among others
	if ($record_id != '' && getTabOwnedBy($module) == 1) {
		$log->debug('< isPermitted TabOwnedBy sharing disabled yes');
		return 'yes';
	}

	// has access to module, now let's check the record
	$recOwnType='';
	$recOwnId='';
	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$recOwnType=$type;
		$recOwnId=$id;
	}
	$CPUserLogin = $CPRecordRelatedToAC = false;
	if (!empty(coreBOS_Session::get('authenticatedUserIsPortalUser', false))) {
		$CPUserLogin = true;
		if (in_array($module, ['Products', 'Services', 'Faq', 'cbQuestion', 'cbMap'])) {
			$CPRecordRelatedToAC = true; // not related so we accept whatever the normal permission system says
		} else {
			$contactId = coreBOS_Session::get('authenticatedUserPortalContact', 0);
			if (!empty($contactId)) {
				if ($contactId!=getRelatedAccountContact($record_id, 'Contacts')) {
					$accountId = getSingleFieldValue('vtiger_contactdetails', 'accountid', 'contactid', $contactId);
					if (!empty($accountId) && $accountId==getRelatedAccountContact($record_id, 'Accounts')) {
						$CPRecordRelatedToAC = true;
					}
				} else {
					$CPRecordRelatedToAC = true;
				}
			}
		}
	}

	if ($recOwnType == 'Users') {
		$wfs = new VTWorkflowManager($adb);
		$racbr = $wfs->getRACRuleForRecord($module, $record_id);
		//Checking if the Record Owner is the current User
		if ($current_user->id == $recOwnId) {
			if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
				|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname, true))
			) {
				$permission = 'yes';
				if ($CPUserLogin && !$CPRecordRelatedToAC) {
					$permission = 'no';
				}
			} else {
				$permission = 'no';
			}
			$log->debug('< isPermitted is owner of record '.$permission);
			return $permission;
		}
		//Checking if the Record Owner is the Subordinate User
		foreach ($userprivs->getSubordinateRoles2Users() as $userids) {
			if (in_array($recOwnId, $userids)) {
				if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
					|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname, true))
				) {
					$permission = 'yes';
					if ($CPUserLogin && !$CPRecordRelatedToAC) {
						$permission = 'no';
					}
				} else {
					$permission = 'no';
				}
				$log->debug('< isPermitted owner is subordinate '.$permission);
				return $permission;
			}
		}
		if ($racbr!==false && $racbr->hasDetailViewPermissionTo($actionname, false)) {
			$log->debug('< isPermitted RAC User yes');
			return 'yes';
		}
	} elseif ($recOwnType == 'Groups') {
		//Checking if the record owner is the current user's group
		if (in_array($recOwnId, $userprivs->getGroups())) {
			$wfs = new VTWorkflowManager($adb);
			$racbr = $wfs->getRACRuleForRecord($module, $record_id);
			if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
				|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname))
			) {
				$permission = 'yes';
				if ($CPUserLogin && !$CPRecordRelatedToAC) {
					$permission = 'no';
				}
			} else {
				$permission = 'no';
			}
			$log->debug('< isPermitted current user in assigned group '.$permission);
			return $permission;
		}
	}

	//Checking for Default Org Sharing permission
	$others_permission_id = $userprivs->getModuleSharingPermission($tabid);
	if ($others_permission_id == UserPrivileges::SHARING_READONLY) {
		if ($actionid == 1 || $actionid == 0) {
			if ($module == 'cbCalendar') {
				if ($recOwnType == 'Users') {
					$permission = isCalendarPermittedBySharing($record_id);
				} else {
					$permission='no';
				}
			} else {
				$permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
			}
			if ($permission=='yes' && $CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing readonly: save and edit no, but special sharing permission are calculated '.$permission);
			return $permission;
		} elseif ($actionid == 2) {
			$log->debug('< isPermitted sharing readonly: delete no');
			return 'no';
		} else {
			$permission = 'yes';
			if ($CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing readonly: all other actions '.$permission);
			return $permission;
		}
	} elseif ($others_permission_id == UserPrivileges::SHARING_READWRITE) {
		if ($actionid == 2) {
			$log->debug('< isPermitted sharing readwrite: delete no');
			return 'no';
		} else {
			$permission = 'yes';
			if ($CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing readwrite: all other actions '.$permission);
			return $permission;
		}
	} elseif ($others_permission_id == UserPrivileges::SHARING_READWRITEDELETE) {
		$wfs = new VTWorkflowManager($adb);
		$racbr = $wfs->getRACRuleForRecord($module, $record_id);
		if (($actionname!='EditView' && $actionname!='Delete' && $actionname!='DetailView' && $actionname!='CreateView')
			|| (!$racbr || $racbr->hasDetailViewPermissionTo($actionname))
		) {
			$permission = 'yes';
			if ($CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing readwritedelete: all actions yes if RAC permits '.$permission);
			return $permission;
		}
	} elseif ($others_permission_id == UserPrivileges::SHARING_PRIVATE) {
		if ($actionid == 3 || $actionid == 4) {
			if ($module == 'cbCalendar') {
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
						$log->debug('< isPermitted sharing private: list view RAC no');
						return 'no';
					} elseif ($actionid == 4 && !$racbr->hasDetailViewPermissionTo('retrieve')) {
						$log->debug('< isPermitted sharing private: detail view RAC no');
						return 'no';
					}
				}
				$permission = isReadPermittedBySharing($module, $tabid, $actionid, $record_id);
			}
			if ($CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing private: view no, but special sharing permission are calculated '.$permission);
			return $permission;
		} elseif ($actionid ==0 || $actionid ==1) {
			$wfs = new VTWorkflowManager($adb);
			$racbr = $wfs->getRACRuleForRecord($module, $record_id);
			if ($racbr) {
				if ($actionid == 0 && !$racbr->hasDetailViewPermissionTo('create')) {
					$log->debug('< isPermitted sharing private: create RAC no');
					return 'no';
				} elseif ($actionid == 1 && !$racbr->hasDetailViewPermissionTo('update')) {
					$log->debug('< isPermitted sharing private: update RAC no');
					return 'no';
				}
			}
			$permission = isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id);
			if ($permission=='yes' && $CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing private: save and edit no, but special sharing permission are calculated '.$permission);
			return $permission;
		} elseif ($actionid ==2) {
			$log->debug('< isPermitted sharing private: delete no');
			return 'no';
		} else {
			$permission = 'yes';
			if ($CPUserLogin && !$CPRecordRelatedToAC) {
				$permission = 'no';
			}
			$log->debug('< isPermitted sharing private: all other actions '.$permission);
			return $permission;
		}
	} else {
		$permission = 'yes';
		if ($CPUserLogin && !$CPRecordRelatedToAC) {
			$permission = 'no';
		}
	}

	$log->debug('< isPermitted end '.$permission);
	return $permission;
}

/** Function to check if the currently logged in user has Read Access due to Sharing for the specified record
 * @param string Module Name
 * @param integer Action Id
 * @param integer Record Id
 * @param integer Tab Id
 * @return string 'yes' or 'no'. 'yes' means the action is allowed for the currently logged in user. 'no' means the action is not allowed for the currently logged in user
 */
function isReadPermittedBySharing($module, $tabid, $actionid, $record_id) {
	global $log, $current_user;
	$log->debug('> isReadPermittedBySharing '.$module.','.$tabid.','.$actionid.','.$record_id);
	$userprivs = $current_user->getPrivileges();
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
	if (! in_array($module, $sharingModuleList)) {
		return 'no';
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$ownertype=$type;
		$ownerid=$id;
	}

	$read_per_arr = $userprivs->getModuleSharingRules($module, 'read');
	if ($ownertype == 'Users') {
		//Checking the Read Sharing Permission Array in Role Users
		$read_role_per=$read_per_arr['ROLE'];
		foreach ($read_role_per as $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug('< isReadPermittedBySharing');
				return $sharePer;
			}
		}
		//Checking the Read Sharing Permission Array in Groups Users
		$read_grp_per=$read_per_arr['GROUP'];
		foreach ($read_grp_per as $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug('< isReadPermittedBySharing');
				return $sharePer;
			}
		}
	} elseif ($ownertype == 'Groups') {
		$read_grp_per=$read_per_arr['GROUP'];
		if (array_key_exists($ownerid, $read_grp_per)) {
			$sharePer='yes';
			$log->debug('< isReadPermittedBySharing');
			return $sharePer;
		}
	}
	//Checking for the Related Sharing Permission
	$relatedModuleArray = $userprivs->getRelatedSharedModules($tabid);
	if (is_array($relatedModuleArray)) {
		foreach ($relatedModuleArray as $parModId) {
			$parRecordOwner=getParentRecordOwner($tabid, $parModId, $record_id);
			if (count($parRecordOwner) > 0) {
				$parModName=getTabname($parModId);
				$read_related_per_arr = $userprivs->getRelatedModuleSharingRules($parModName, $module, 'read');
				$rel_owner_type='';
				$rel_owner_id='';
				foreach ($parRecordOwner as $rel_type => $rel_id) {
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if ($rel_owner_type=='Users') {
					//Checking in Role Users
					$read_related_role_per=$read_related_per_arr['ROLE'];
					foreach ($read_related_role_per as $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug('< isReadPermittedBySharing');
							return $sharePer;
						}
					}
					//Checking in Group Users
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					foreach ($read_related_grp_per as $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug('< isReadPermittedBySharing');
							return $sharePer;
						}
					}
				} elseif ($rel_owner_type=='Groups') {
					$read_related_grp_per=$read_related_per_arr['GROUP'];
					if (array_key_exists($rel_owner_id, $read_related_grp_per)) {
						$sharePer='yes';
						$log->debug('< isReadPermittedBySharing');
						return $sharePer;
					}
				}
			}
		}
	}
	$log->debug('< isReadPermittedBySharing');
	return $sharePer;
}

/** Function to check if the currently logged in user has Write Access due to Sharing for the specified record
 * @param string Module Name
 * @param integer Action Id
 * @param integer Record Id
 * @param integer Tab Id
 * @return 'yes' or 'no'. 'yes' means the action is allowed for the currently logged in user. 'no' means the action is not allowed for the currently logged in user
 */
function isReadWritePermittedBySharing($module, $tabid, $actionid, $record_id) {
	global $log, $current_user;
	$log->debug('> isReadWritePermittedBySharing '.$module.','.$tabid.','.$actionid.','.$record_id);
	$userprivs = $current_user->getPrivileges();
	$ownertype='';
	$ownerid='';
	$sharePer='no';

	$sharingModuleList=getSharingModuleList();
	if (! in_array($module, $sharingModuleList)) {
		return 'no';
	}

	$recordOwnerArr=getRecordOwnerId($record_id);
	foreach ($recordOwnerArr as $type => $id) {
		$ownertype=$type;
		$ownerid=$id;
	}

	$write_per_arr = $userprivs->getModuleSharingRules($module, 'write');

	if ($ownertype == 'Users') {
		//Checking the Write Sharing Permission Array in Role Users
		$write_role_per=$write_per_arr['ROLE'];
		foreach ($write_role_per as $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug('< isReadWritePermittedBySharing');
				return $sharePer;
			}
		}
		//Checking the Write Sharing Permission Array in Groups Users
		$write_grp_per=$write_per_arr['GROUP'];
		foreach ($write_grp_per as $userids) {
			if (in_array($ownerid, $userids)) {
				$sharePer='yes';
				$log->debug('< isReadWritePermittedBySharing');
				return $sharePer;
			}
		}
	} elseif ($ownertype == 'Groups') {
		$write_grp_per=$write_per_arr['GROUP'];
		if (array_key_exists($ownerid, $write_grp_per)) {
			$sharePer='yes';
			$log->debug('< isReadWritePermittedBySharing');
			return $sharePer;
		}
	}
	//Checking for the Related Sharing Permission
	$relatedModuleArray = $userprivs->getRelatedSharedModules($tabid);
	if (is_array($relatedModuleArray)) {
		foreach ($relatedModuleArray as $parModId) {
			$parRecordOwner=getParentRecordOwner($tabid, $parModId, $record_id);
			if (count($parRecordOwner) > 0) {
				$parModName=getTabname($parModId);
				$write_related_per_arr = $userprivs->getRelatedModuleSharingRules($parModName, $module, 'write');
				$rel_owner_type='';
				$rel_owner_id='';
				foreach ($parRecordOwner as $rel_type => $rel_id) {
					$rel_owner_type=$rel_type;
					$rel_owner_id=$rel_id;
				}
				if ($rel_owner_type=='Users') {
					//Checking in Role Users
					$write_related_role_per=$write_related_per_arr['ROLE'];
					foreach ($write_related_role_per as $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug('< isReadWritePermittedBySharing');
							return $sharePer;
						}
					}
					//Checking in Group Users
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					foreach ($write_related_grp_per as $userids) {
						if (in_array($rel_owner_id, $userids)) {
							$sharePer='yes';
							$log->debug('< isReadWritePermittedBySharing');
							return $sharePer;
						}
					}
				} elseif ($rel_owner_type=='Groups') {
					$write_related_grp_per=$write_related_per_arr['GROUP'];
					if (array_key_exists($rel_owner_id, $write_related_grp_per)) {
						$sharePer='yes';
						$log->debug('< isReadWritePermittedBySharing');
						return $sharePer;
					}
				}
			}
		}
	}

	$log->debug('< isReadWritePermittedBySharing');
	return $sharePer;
}

/** Function to check if the outlook user is permitted to perform the specified action
 * @param string Module Name
 * @param string Action Name
 * @param integer Record Id
 * @return 'yes' or 'no'. 'yes' means the action is allowed for the currently logged in user. 'no' means this action is not allowed for the currently logged in user
 */
function isAllowed_Outlook($module, $action, $user_id, $record_id) {
	global $log;
	$log->debug('> isAllowed_Outlook '.$module.','.$action.','.$user_id.','.$record_id);

	$permission = 'no';
	if ($module == 'Users' || $module == 'Home' || $module == 'Settings') {
		//These modules do not have security
		$permission = 'yes';
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
						if ($others_permission_id == UserPrivileges::SHARING_READONLY) {
							if ($action == 'EditView' || $action == 'CreateView' || $action == 'Delete') {
								$permission = 'no';
							} else {
								$permission = 'yes';
							}
						} elseif ($others_permission_id == UserPrivileges::SHARING_READWRITE) {
							if ($action == 'Delete') {
								$permission = 'no';
							} else {
								$permission = 'yes';
							}
						} elseif ($others_permission_id == UserPrivileges::SHARING_READWRITEDELETE) {
							$permission = 'yes';
						} elseif ($others_permission_id == UserPrivileges::SHARING_PRIVATE) {
							if ($action == 'DetailView' || $action == 'EditView' || $action == 'CreateView' || $action == 'Delete') {
								$permission = 'no';
							} else {
								$permission = 'yes';
							}
						}
					} else {
						$permission = 'yes';
					}
				}
			} else {
				$permission = 'no';
			}
		} else {
			$permission = 'no';
		}
	}
	$log->debug('< isAllowed_Outlook');
	return $permission;
}

/** Function to get the Profile Global Information for the specified profileid
 * @param integer Profile Id
 * @return array Profile Gloabal Permission array in the following format:
 * $profileGloblaPermisson=Array($viewall_actionid=>permission, $editall_actionid=>permission)
 */
function getProfileGlobalPermission($profileid) {
	global $log, $adb;
	$log->debug('> getProfileGlobalPermission '.$profileid);
	$copy=array();
	$sql = 'select globalactionid, globalactionpermission from vtiger_profile2globalpermissions where profileid=?';
	$result = $adb->pquery($sql, array($profileid));
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$act_id = $adb->query_result($result, $i, 'globalactionid');
		$per_id = $adb->query_result($result, $i, 'globalactionpermission');
		$copy[$act_id] = $per_id;
	}
	$log->debug('< getProfileGlobalPermission');
	return $copy;
}

/** Function to get the Profile Tab Permissions for the specified profileid
 * @param integer Profile Id
 * @return array Profile Tabs Permission array in the following format:
 * $profileTabPermisson=Array($tabid1=>permission, $tabid2=>permission,........., $tabidn=>permission)
 */
function getProfileTabsPermission($profileid) {
	global $log, $adb;
	$log->debug('> getProfileTabsPermission('.$profileid);
	$sql = 'select tabid, permissions from vtiger_profile2tab where profileid=?';
	$result = $adb->pquery($sql, array($profileid));
	$num_rows = $adb->num_rows($result);
	$copy=array();
	for ($i=0; $i<$num_rows; $i++) {
		$tab_id = $adb->query_result($result, $i, 'tabid');
		$per_id = (integer)$adb->query_result($result, $i, 'permissions');
		$copy[$tab_id] = $per_id;
	}
	$log->debug('< getProfileTabsPermission');
	return $copy;
}

/** Function to get the Profile Action Permissions for the specified profileid
 * @param integer Profile Id
 * @return array Profile Tabs Action Permission array in the following format:
 *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
 *                                |
 *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileActionPermission($profileid) {
	global $log, $adb;
	$log->debug('> getProfileActionPermission '.$profileid);
	$check = array();
	$temp_tabid = array();
	$result1 = $adb->pquery('select * from vtiger_profile2standardpermissions where profileid=?', array($profileid));
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
	$log->debug('< getProfileActionPermission');
	return $check;
}

/** Function to get the Standard and Utility Profile Action Permissions for the specified profileid
  * @param integer Profile Id
  * @return array Profile Tabs Action Permission array in the following format:
  *    $tabActionPermission = Array($tabid1=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                        $tabid2=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission),
  *                                |
  *                        $tabidn=>Array(actionid1=>permission, actionid2=>permission,...,actionidn=>permission))
 */
function getProfileAllActionPermission($profileid) {
	global $log;
	$log->debug('> getProfileAllActionPermission '.$profileid);
	$actionArr=getProfileActionPermission($profileid);
	$utilArr=getTabsUtilityActionPermission($profileid);
	foreach ($utilArr as $tabid => $act_arr) {
		$act_tab_arr = isset($actionArr[$tabid]) ? $actionArr[$tabid] : array();
		foreach ($act_arr as $utilid => $util_perr) {
			$act_tab_arr[$utilid]=$util_perr;
		}
		$actionArr[$tabid]=$act_tab_arr;
	}
	$log->debug('< getProfileAllActionPermission');
	return $actionArr;
}

/** Function to create profile
  * @param string Profile Name
  * @param integer Profile Id
 */
function createProfile($profilename, $parentProfileId, $description) {
	global $log, $adb;
	$log->debug('> createProfile '.$profilename.','.$parentProfileId.','.$description);
	//Inserting values into Profile Table
	$adb->pquery('insert into vtiger_profile values(?,?,?)', array('', $profilename, $description));

	$result2 = $adb->pquery('select max(profileid) as current_id from vtiger_profile', array());
	$current_profile_id = $adb->query_result($result2, 0, 'current_id');

	//Inserting values into vtiger_profile2globalpermissions
	$params3 = array($parentProfileId);
	$result3= $adb->pquery('select * from vtiger_profile2globalpermissions where profileid=?', $params3);
	$p2tab_rows = $adb->num_rows($result3);
	$sql4 = 'insert into vtiger_profile2globalpermissions values(?,?,?)';
	for ($i=0; $i<$p2tab_rows; $i++) {
		$act_id=$adb->query_result($result3, $i, 'globalactionid');
		$permissions=$adb->query_result($result3, $i, 'globalactionpermission');
		$params4 = array($current_profile_id, $act_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2tab vtiger_table
	$params3 = array($parentProfileId);
	$result3= $adb->pquery('select * from vtiger_profile2tab where profileid=?', $params3);
	$p2tab_rows = $adb->num_rows($result3);
	$sql4 = 'insert into vtiger_profile2tab values(?,?,?)';
	for ($i=0; $i<$p2tab_rows; $i++) {
		$tab_id=$adb->query_result($result3, $i, 'tabid');
		$permissions=$adb->query_result($result3, $i, 'permissions');
		$params4 = array($current_profile_id, $tab_id, $permissions);
		$adb->pquery($sql4, $params4);
	}

	//Inserting values into Profile2standard vtiger_table
	$params6 = array($parentProfileId);
	$result6 = $adb->pquery('select * from vtiger_profile2standardpermissions where profileid=?', $params6);
	$p2per_rows = $adb->num_rows($result6);
	$sql7 = 'insert into vtiger_profile2standardpermissions values(?,?,?,?)';
	for ($i=0; $i<$p2per_rows; $i++) {
		$tab_id=$adb->query_result($result6, $i, 'tabid');
		$action_id=$adb->query_result($result6, $i, 'operation');
		$permissions=$adb->query_result($result6, $i, 'permissions');
		$params7 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql7, $params7);
	}

	//Inserting values into Profile2Utility vtiger_table
	$params8 = array($parentProfileId);
	$result8= $adb->pquery('select * from vtiger_profile2utility where profileid=?', $params8);
	$p2util_rows = $adb->num_rows($result8);
	$sql9 = 'insert into vtiger_profile2utility values(?,?,?,?)';
	for ($i=0; $i<$p2util_rows; $i++) {
		$tab_id=$adb->query_result($result8, $i, 'tabid');
		$action_id=$adb->query_result($result8, $i, 'activityid');
		$permissions=$adb->query_result($result8, $i, 'permission');
		$params9 = array($current_profile_id, $tab_id, $action_id, $permissions);
		$adb->pquery($sql9, $params9);
	}

	//Inserting values into Profile2field vtiger_table
	$params10 = array($parentProfileId);
	$result10= $adb->pquery('select tabid, fieldid, visible, readonly from vtiger_profile2field where profileid=?', $params10);
	$p2field_rows = $adb->num_rows($result10);
	$sql11 = 'insert into vtiger_profile2field values(?,?,?,?,?,?)';
	for ($i=0; $i<$p2field_rows; $i++) {
		$tab_id=$adb->query_result($result10, $i, 'tabid');
		$fieldid=$adb->query_result($result10, $i, 'fieldid');
		$permissions=$adb->query_result($result10, $i, 'visible');
		$readonly=$adb->query_result($result10, $i, 'readonly');
		$params11 = array($current_profile_id, $tab_id, $fieldid, $permissions ,$readonly, 'B');
		$adb->pquery($sql11, $params11);
	}
	$log->debug('< createProfile');
}

/** Function to delete profile
 * @param string Profile Id to which the existing vtiger_role2profile relationships are to be transferred
 * @param integer Profile Id to be deleted
 */
function deleteProfile($prof_id, $transfer_profileid = '') {
	global $log, $adb;
	$log->debug('> deleteProfile '.$prof_id.','.$transfer_profileid);
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
		$sql8 = 'select roleid from vtiger_role2profile where profileid=?';
		$result = $adb->pquery($sql8, array($prof_id));
		$num_rows=$adb->num_rows($result);
		for ($i=0; $i<$num_rows; $i++) {
			$roleid=$adb->query_result($result, $i, 'roleid');
			$sql = 'select profileid from vtiger_role2profile where roleid=?';
			$profresult=$adb->pquery($sql, array($roleid));
			$num=$adb->num_rows($profresult);
			if ($num>1) {
				$sql10='delete from vtiger_role2profile where roleid=? and profileid=?';
				$adb->pquery($sql10, array($roleid, $prof_id));
			} else {
				$sql8 = 'update vtiger_role2profile set profileid=? where profileid=? and roleid=?';
				$adb->pquery($sql8, array($transfer_profileid, $prof_id, $roleid));
			}
		}
	}

	//delete from vtiger_profile vtiger_table;
	$sql9 = 'delete from vtiger_profile where profileid=?';
	$adb->pquery($sql9, array($prof_id));
	$log->debug('< deleteProfile');
}

/** Function to get all the role information
 * @return array will contain the details of all the roles. Role ID will be the key
 */
function getAllRoleDetails() {
	global $log, $adb;
	$log->debug('> getAllRoleDetails');
	$role_det = array();
	$immediatesubordinates = 'select roleid from vtiger_role where parentrole like ? and depth=?';
	$result = $adb->pquery('select * from vtiger_role', array());
	$num_rows = $adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$each_role_det = array();
		$roleid=$adb->query_result($result, $i, 'roleid');
		$rolename=$adb->query_result($result, $i, 'rolename');
		$roledepth=$adb->query_result($result, $i, 'depth');
		$sub_roledepth=$roledepth + 1;
		$parentrole=$adb->query_result($result, $i, 'parentrole');
		$sub_role='';

		$res1 = $adb->pquery($immediatesubordinates, array($parentrole.'::%', $sub_roledepth));
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
	$log->debug('< getAllRoleDetails');
	return $role_det;
}

/** Function to get all the profile information
 * @return array will contain the details of all the profiles. Profile ID will be the key
 */
function getAllProfileInfo() {
	global $log, $adb;
	$log->debug('> getAllProfileInfo');
	$query='select * from vtiger_profile';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	$prof_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$profileid=$adb->query_result($result, $i, 'profileid');
		$profilename=$adb->query_result($result, $i, 'profilename');
		$prof_details[$profileid]=$profilename;
	}
	$log->debug('< getAllProfileInfo');
	return $prof_details;
}

/** Function to get the role information of the specified role
 * @param string Role ID
 * @return array Role Information array in the following format
 *       array($roleId=>array($rolename, $parentrole, $roledepth, $immediateParent));
 */
function getRoleInformation($roleid) {
	global $log, $adb;
	$log->debug('> getRoleInformation '.$roleid);
	$result = $adb->pquery('select * from vtiger_role where roleid=?', array($roleid));
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
	$log->debug('< getRoleInformation');
	return $roleInfo;
}

/** Function to get the role related profiles
 * @param string Role ID
 * @return array Role Related Profile array in the following format:
 *       $roleProfiles=Array($profileId1=>$profileName,$profileId2=>$profileName,........,$profileIdn=>$profileName));
 */
function getRoleRelatedProfiles($roleId) {
	global $log, $adb;
	$log->debug('> getRoleRelatedProfiles '.$roleId);
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
	$log->debug('< getRoleRelatedProfiles');
	return $roleRelatedProfiles;
}

/** Function to get the role related users
 * @param string Role ID
 * @return array Role Related User array in the following format:
 *       $roleUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleUsers($roleId) {
	global $log, $adb;
	$log->debug('> getRoleUsers '.$roleId);
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
	$log->debug('< getRoleUsers');
	return $roleRelatedUsers;
}

/** Function to get the role related user ids
 * @param string Role ID
 * @return array Role Related User array in the following format:
 *       $roleUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleUserIds($roleId) {
	global $log, $adb;
	$log->debug('> getRoleUserIds '.$roleId);
	$query = 'select vtiger_user2role.*,vtiger_users.user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid where roleid=?';
	$result = $adb->pquery($query, array($roleId));
	$num_rows=$adb->num_rows($result);
	$roleRelatedUsers=array();
	for ($i=0; $i<$num_rows; $i++) {
		$roleRelatedUsers[]=$adb->query_result($result, $i, 'userid');
	}
	$log->debug('< getRoleUserIds');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate users
 * @param string Role ID
 * @return array Role and Subordinates Related Users array in the following format:
 *       $roleSubUsers=Array($userId1=>$userName,$userId2=>$userName,........,$userIdn=>$userName));
 */
function getRoleAndSubordinateUsers($roleId) {
	global $log, $adb;
	$log->debug('> getRoleAndSubordinateUsers '.$roleId);
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
	$log->debug('< getRoleAndSubordinateUsers');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate user ids
 * @param string Role ID
 * @return array Role and Subordinates Related Users array in the following format:
 *       $roleSubUserIds=Array($userId1,$userId2,........,$userIdn);
 */
function getRoleAndSubordinateUserIds($roleId) {
	global $log, $adb;
	$log->debug('> getRoleAndSubordinateUserIds '.$roleId);
	$roleInfoArr=getRoleInformation($roleId);
	$parentRole=$roleInfoArr[$roleId][1];
	$query = 'select vtiger_user2role.userid
		from vtiger_user2role
		inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid
		where vtiger_role.parentrole like ?';
	$result = $adb->pquery($query, array($parentRole.'%'));
	$roleRelatedUsers=array();
	while ($row = $adb->getNextRow($result, false)) {
		$roleRelatedUsers[] = $row[0];
	}
	$log->debug('< getRoleAndSubordinateUserIds');
	return $roleRelatedUsers;
}

/** Function to get the role and subordinate Information for the specified role ID
 * @param string Role ID
 * @return array Role and Subordinates Information array in the following format:
 *       $roleSubInfo=Array($roleId1=>Array($rolename,$parentrole,$roledepth,$immediateParent), $roleId2=>Array($rolename,$parentrole,$roledepth,$immediateParent),.....);
 */
function getRoleAndSubordinatesInformation($roleId) {
	global $log, $adb;
	$log->debug('> getRoleAndSubordinatesInformation '.$roleId);
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
	$log->debug('< getRoleAndSubordinatesInformation');
	return $roleInfo;
}

/** Function to get the role and subordinate role IDs
 * @param string Role ID
 * @return array Role and Subordinates Role IDs array in the following format:
 *       $roleSubRoleIds=Array($roleId1,$roleId2,........,$roleIdn);
 */
function getRoleAndSubordinatesRoleIds($roleId) {
	global $log, $adb;
	$log->debug('> getRoleAndSubordinatesRoleIds '.$roleId);
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
	$log->debug('< getRoleAndSubordinatesRoleIds');
	return $roleInfo;
}

/** Function to get the role and subordinate hierarchy
 * @return array Role and Subordinates Role IDs in an array tree of hierarchy dependencies
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
		if (empty($hrarray)) {
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

/** Function to get delete the specified role
 * @param string RoleId
 * @param string RoleId to which users of the role that is being deleted are transferred
 */
function deleteRole($roleId, $transferRoleId) {
	global $log, $adb;
	$log->debug('> deleteRole '.$roleId.','.$transferRoleId);
	$roleInfo=getRoleAndSubordinatesInformation($roleId);
	foreach ($roleInfo as $roleid => $roleDetArr) {
		$sql1 = 'update vtiger_user2role set roleid=? where roleid=?';
		$adb->pquery($sql1, array($transferRoleId, $roleid));
		//delete from vtiger_role2profile table
		$sql2 = 'delete from vtiger_role2profile where roleid=?';
		$adb->pquery($sql2, array($roleid));
		//delete handling for groups
		$sql10 = 'delete from vtiger_group2role where roleid=?';
		$adb->pquery($sql10, array($roleid));
		$sql11 = 'delete from vtiger_group2rs where roleandsubid=?';
		$adb->pquery($sql11, array($roleid));
		//delete handling for sharing rules
		deleteRoleRelatedSharingRules($roleid);
		//delete from vtiger_role table;
		$sql9 = 'delete from vtiger_role where roleid=?';
		$adb->pquery($sql9, array($roleid));
	}
	$log->debug('< deleteRole');
}

/** Function to delete the role related sharing rules
 * @param string RoleId
 */
function deleteRoleRelatedSharingRules($roleId) {
	global $log, $adb;
	$log->debug('> deleteRoleRelatedSharingRules '.$roleId);
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
	$log->debug('< deleteRoleRelatedSharingRules');
}

/** Function to delete the group related sharing rules
 * @param string Role Id
 */
function deleteGroupRelatedSharingRules($grpId) {
	global $log, $adb;
	$log->debug('> deleteGroupRelatedSharingRules '.$grpId);
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
	$log->debug('< deleteGroupRelatedSharingRules');
}

/** Function to get userid and username of all users
  * @return array User array in the following format:
  * $userArray=Array($userid1=>$username, $userid2=>$username,............,$useridn=>$username);
 */
function getAllUserName() {
	global $log, $adb;
	$log->debug('> getAllUserName');
	$result = $adb->pquery('select id,email1,ename from vtiger_users where deleted=0', array());
	$num_rows=$adb->num_rows($result);
	$user_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$userid=$adb->query_result($result, $i, 'id');
		$username=getFullNameFromQResult($result, $i, 'Users');
		$user_details[$userid]=$username;
	}
	$log->debug('< getAllUserName');
	return $user_details;
}

/** Function to get groupid and groupname of all groups
  * @return array Group array in the following format:
  * $grpArray=Array($grpid1=>$grpname, $grpid2=>$grpname,............,$grpidn=>$grpname);
 */
function getAllGroupName() {
	global $log, $adb;
	$log->debug('> getAllGroupName');
	$result = $adb->pquery('select groupid, groupname from vtiger_groups', array());
	$num_rows=$adb->num_rows($result);
	$group_details=array();
	for ($i=0; $i<$num_rows; $i++) {
		$grpid=$adb->query_result($result, $i, 'groupid');
		$grpname=$adb->query_result($result, $i, 'groupname');
		$group_details[$grpid]=$grpname;
	}
	$log->debug('< getAllGroupName');
	return $group_details;
}

/** Function to get groupid and groupname of all for the given groupid
 * @return array Group array in the following format:
 * $grpArray=Array($grpid1=>$grpname);
 */
function getGroupDetails($id) {
	global $log, $adb;
	$log->debug('> getAllGroupDetails');
	$result = $adb->pquery('select * from vtiger_groups where groupid=?', array($id));
	$num_rows=$adb->num_rows($result);
	if ($num_rows < 1) {
		return null;
	}
	$group_details=array();
	$grpid=$adb->query_result($result, 0, 'groupid');
	$grpname=$adb->query_result($result, 0, 'groupname');
	$grpdesc=$adb->query_result($result, 0, 'description');
	$group_details=array($grpid,$grpname,$grpdesc);
	$log->debug('< getAllGroupDetails');
	return $group_details;
}

/** Function to get group information of all groups
 * @return array Group Informaton array in the following format:
 * $grpInfoArray=Array($grpid1=>Array($grpname,description) $grpid2=>Array($grpname,description),............,$grpidn=>Array($grpname,description));
 */
function getAllGroupInfo() {
	global $log, $adb;
	$log->debug('> getAllGroupInfo');
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
	$log->debug('< getAllGroupInfo');
	return $group_details;
}

/** Function to create a group
 * @param string Group Name
 * @param array Group Members (Groups,Roles,RolesAndsubordinates,Users)
 * @param string Group Description
 * @return integer Group Id
 */
function createGroup($groupName, $groupMemberArray, $description) {
	global $log, $adb;
	$log->debug('> createGroup', [$groupName, $groupMemberArray, $description]);
	$groupId=$adb->getUniqueId('vtiger_users');
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
	$log->debug('< createGroup');
	return $groupId;
}

/** Function to insert group to group relation
 * @param integer Group Id
 * @param integer Group Id
 */
function insertGroupToGroupRelation($groupId, $containsGroupId) {
	global $log, $adb;
	$log->debug('> insertGroupToGroupRelation '.$groupId.','.$containsGroupId);
	$adb->pquery('insert into vtiger_group2grouprel values(?,?)', array($groupId, $containsGroupId));
	$log->debug('< insertGroupToGroupRelation');
}

/** Function to insert group to role relation
 * @param integer Group Id
 * @param string Role Id
 */
function insertGroupToRoleRelation($groupId, $roleId) {
	global $log, $adb;
	$log->debug('> insertGroupToRoleRelation '.$groupId.','.$roleId);
	$adb->pquery('insert into vtiger_group2role values(?,?)', array($groupId, $roleId));
	$log->debug('< insertGroupToRoleRelation');
}

/** Function to insert group to role & subordinate relation
 * @param integer Group Id
 * @param string Role Sub Id
 */
function insertGroupToRsRelation($groupId, $rsId) {
	global $log, $adb;
	$log->debug('> insertGroupToRsRelation '.$groupId.','.$rsId);
	$adb->pquery('insert into vtiger_group2rs values(?,?)', array($groupId, $rsId));
	$log->debug('< insertGroupToRsRelation');
}

/** Function to insert group to user relation
 * @param integer Group Id
 * @param string User Id
 */
function insertGroupToUserRelation($groupId, $userId) {
	global $log, $adb;
	$log->debug('> insertGroupToUserRelation '.$groupId.','.$userId);
	$adb->pquery('insert into vtiger_users2group values(?,?)', array($groupId, $userId));
	$log->debug('< insertGroupToUserRelation');
}

/** Function to get the group Information of the specified group
 * @param integer Group Id
 * @return array Group Detail array in the following format:
 *   $groupDetailArray=Array($groupName,$description,$groupMembers);
 */
function getGroupInfo($groupId) {
	global $log, $adb;
	$log->debug('> getGroupInfo '.$groupId);
	$groupDetailArr=array();
	$groupMemberArr=array();
	//Retreving the group Info
	$result = $adb->pquery('select * from vtiger_groups where groupid=?', array($groupId));
	$groupName=$adb->query_result($result, 0, 'groupname');
	$description=$adb->query_result($result, 0, 'description');

	//Retreving the Group RelatedMembers
	$groupMemberArr=getGroupMembers($groupId);
	$groupDetailArr[]=$groupName;
	$groupDetailArr[]=$description;
	$groupDetailArr[]=$groupMemberArr;

	//Returning the Group Detail Array
	$log->debug('< getGroupInfo');
	return $groupDetailArr;
}

/** Function to fetch the group name of the specified group
  * @param integer Group Id
  * @return string Group Name
 */
function fetchGroupName($groupId) {
	global $log, $adb;
	$log->debug('> fetchGroupName '.$groupId);
	//Retreving the group Info
	$result = $adb->pquery('select groupname from vtiger_groups where groupid=?', array($groupId));
	$groupName=decode_html($adb->query_result($result, 0, 'groupname'));
	$log->debug('< fetchGroupName');
	return $groupName;
}

/** Function to fetch the group members of the specified group
  * @param integer Group Id
  * @return array Group Member array in the follwing format:
  *  $groupMemberArray=Array([groups]=>Array(groupid1,groupid2,groupid3,.....,groupidn),
  *                          [roles]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [rs]=>Array(roleid1,roleid2,roleid3,.....,roleidn),
  *                          [users]=>Array(useridd1,userid2,userid3,.....,groupidn))
 */
function getGroupMembers($groupId) {
	global $log;
	$log->debug('> getGroupMembers '.$groupId);
	$groupMemberArr=array();
	$roleGroupArr=getGroupRelatedRoles($groupId);
	$rsGroupArr=getGroupRelatedRoleSubordinates($groupId);
	$groupGroupArr=getGroupRelatedGroups($groupId);
	$userGroupArr=getGroupRelatedUsers($groupId);

	$groupMemberArr['groups']=$groupGroupArr;
	$groupMemberArr['roles']=$roleGroupArr;
	$groupMemberArr['rs']=$rsGroupArr;
	$groupMemberArr['users']=$userGroupArr;

	$log->debug('< getGroupMembers');
	return($groupMemberArr);
}

/** Function to get the group related roles of the specified group
  * @param integer Group Id
  * @return array Group Related Role array in the follwing format:
  *  $groupRoles=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoles($groupId) {
	global $log, $adb;
	$log->debug('> getGroupRelatedRoles '.$groupId);
	$roleGroupArr=array();
	$result = $adb->pquery('select roleid from vtiger_group2role where groupid=?', array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$roleId=$adb->query_result($result, $i, 'roleid');
		$roleGroupArr[]=$roleId;
	}
	$log->debug('< getGroupRelatedRoles');
	return $roleGroupArr;
}

/** Function to get the group related roles and subordinates of the specified group
  * @param integer Group Id
  * @return array Group Related Roles & Subordinate array in the follwing format:
  *  $groupRoleSubordinates=Array(roleid1,roleid2,roleid3,.....,roleidn);
 */
function getGroupRelatedRoleSubordinates($groupId) {
	global $log, $adb;
	$log->debug('> getGroupRelatedRoleSubordinates '.$groupId);
	$rsGroupArr=array();
	$result = $adb->pquery('select roleandsubid from vtiger_group2rs where groupid=?', array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$roleSubId=$adb->query_result($result, $i, 'roleandsubid');
		$rsGroupArr[]=$roleSubId;
	}
	$log->debug('< getGroupRelatedRoleSubordinates');
	return $rsGroupArr;
}

/** Function to get the group related groups
  * @param integer Group Id
  * @return array Group Related Groups array in the follwing format:
  *  $groupGroups=Array(grpid1,grpid2,grpid3,.....,grpidn);
 */
function getGroupRelatedGroups($groupId) {
	global $log, $adb;
	$log->debug('> getGroupRelatedGroups '.$groupId);
	$groupGroupArr=array();
	$result = $adb->pquery('select containsgroupid from vtiger_group2grouprel where groupid=?', array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$relGroupId=$adb->query_result($result, $i, 'containsgroupid');
		$groupGroupArr[]=$relGroupId;
	}
	$log->debug('< getGroupRelatedGroups');
	return $groupGroupArr;
}

/** Function to get the group related users
  * @param integer User Id
  * @return array Group Related Users array in the follwing format:
  *  $groupUsers=Array(userid1,userid2,userid3,.....,useridn);
 */
function getGroupRelatedUsers($groupId) {
	global $log, $adb;
	$log->debug('> getGroupRelatedUsers '.$groupId);
	$userGroupArr=array();
	$result = $adb->pquery('select userid from vtiger_users2group where groupid=?', array($groupId));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$userId=$adb->query_result($result, $i, 'userid');
		$userGroupArr[]=$userId;
	}
	$log->debug('< getGroupRelatedUsers');
	return $userGroupArr;
}

/** Function to update the group
  * @param integer Group Id
  * @param string Group Name
  * @param array Group Members
  * @param string Description
 */
function updateGroup($groupId, $groupName, $groupMemberArray, $description) {
	global $log, $adb;
	$log->debug('> updateGroup', [$groupId, $groupName, $groupMemberArray, $description]);
	$query='update vtiger_groups set groupname=?, description=? where groupid=?';
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
	$log->debug('< updateGroup');
}

/** Function to delete the specified group
  * @param integer Group Id
  * @param integer Id of the group/user to which record ownership is to be transferred
 */
function deleteGroup($groupId, $transferId) {
	global $log, $adb;
	$log->debug('> deleteGroup '.$groupId);
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
	$log->debug('< deleteGroup');
}

/** Function to transfer the ownership of records owned by a particular group to the specified group
  * @param integer Group Id of the group which's record ownership has to be transferred
  * @param integer Id of the group/user to which record ownership is to be transferred
 */
function tranferGroupOwnership($groupId, $transferId) {
	global $log, $adb;
	$log->debug('> tranferGroupOwnership '.$groupId);
	$denormModules = getDenormalizedModules();
	if (count($denormModules) > 0) {
		foreach ($denormModules as $table) {
			$adb->pquery('update '.$table.' set smownerid=? where smownerid=?', array($transferId, $groupId));
		}
	}
	$adb->pquery('update vtiger_crmentity set smownerid=? where smownerid=?', array($transferId, $groupId));
	$adb->pquery('update vtiger_crmobject set smownerid=? where smownerid=?', array($transferId, $groupId));
	if (Vtiger_Utils::CheckTable('vtiger_customerportal_prefs')) {
		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($transferId, 'defaultassignee', $groupId);
		$adb->pquery($query, $params);
		$query = 'UPDATE vtiger_customerportal_prefs SET prefvalue = ? WHERE prefkey = ? AND prefvalue = ?';
		$params = array($transferId, 'userid', $groupId);
		$adb->pquery($query, $params);
	}
	$log->debug('< tranferGroupOwnership');
}

/** Function to delete group to group relation of the  specified group
 * @param integer Group Id
 */
function deleteGroupRelatedGroups($groupId) {
	global $log, $adb;
	$log->debug('> deleteGroupRelatedGroups '.$groupId);
	$adb->pquery('delete from vtiger_group2grouprel where groupid=?', array($groupId));
	$log->debug('< deleteGroupRelatedGroups');
}

/** Function to delete group to role relation of the  specified group
 * @param integer Group Id
 */
function deleteGroupRelatedRoles($groupId) {
	global $log, $adb;
	$log->debug('> deleteGroupRelatedRoles '.$groupId);
	$adb->pquery('delete from vtiger_group2role where groupid=?', array($groupId));
	$log->debug('< deleteGroupRelatedRoles');
}

/** Function to delete group to role and subordinates relation of the  specified group
 * @param integer Group Id
 */
function deleteGroupRelatedRolesAndSubordinates($groupId) {
	global $log, $adb;
	$log->debug('> deleteGroupRelatedRolesAndSubordinates '.$groupId);
	$adb->pquery('delete from vtiger_group2rs where groupid=?', array($groupId));
	$log->debug('< deleteGroupRelatedRolesAndSubordinates');
}

/** Function to delete group to user relation of the  specified group
 * @param integer Group Id
 */
function deleteGroupRelatedUsers($groupId) {
	global $log, $adb;
	$log->debug('> deleteGroupRelatedUsers '.$groupId);
	$adb->pquery('delete from vtiger_users2group where groupid=?', array($groupId));
	$log->debug('< deleteGroupRelatedUsers');
}

/** This function returns the Default Organisation Sharing Action Name
  * @param integer It takes the Default Organisation Sharing ActionId as input
  * @return string sharing Action Name
  */
function getDefOrgShareActionName($share_action_id) {
	global $log, $adb;
	$log->debug('> getDefOrgShareActionName '.$share_action_id);
	$result=$adb->pquery('select share_action_name from vtiger_org_share_action_mapping where share_action_id=?', array($share_action_id));
	$share_action_name=$adb->query_result($result, 0, 'share_action_name');
	$log->debug('< getDefOrgShareActionName');
	return $share_action_name;
}

/** This function returns the Default Organisation Sharing Action Array for the specified Module
  * @param integer the module tabid
  * @return array consists of the 'Default Organisation Sharing Id'=>'Default Organisation Sharing Action' mapping for all the sharing actions available for the module
  * The output Array will be in the following format:
  *    Array = (Default Org ActionId1=>Default Org ActionName1,
  *             Default Org ActionId2=>Default Org ActionName2,
  *                     |
  *                     |
  *              Default Org ActionIdn=>Default Org ActionNamen)
  */
function getModuleSharingActionArray($tabid) {
	global $log, $adb;
	$log->debug('> getModuleSharingActionArray '.$tabid);
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
	$log->debug('< getModuleSharingActionArray');
	return $share_action_arr;
}

/** This function adds a organisation level sharing rule for the specified Module
  * @param integer module tabid
  * @param string Entity Type may be groups,roles,rs and users
  * @param string Entity Type may be groups,roles,rs and users
  * @param integer The id of the group,role,rs,user to be shared
  * @param integer id of the group,role,rs,user to which the specified entity is to be shared
  * @param integer This can have the following values:
  *               0 - Read Only
  *               1 - Read/Write
  * @return integer shareid
  */
function addSharingRule($tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission) {
	global $log, $adb;
	$log->debug('> addSharingRule '.$tabid.','.$shareEntityType.','.$toEntityType.','.$shareEntityId.','.$toEntityId.','.$sharePermission);
	$shareid=$adb->getUniqueId('vtiger_datashare_module_rel');

	if ($shareEntityType == 'groups' && $toEntityType == 'groups') {
		$type_string='GRP::GRP';
		$query = 'insert into vtiger_datashare_grp2grp values(?,?,?,?)';
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'roles') {
		$type_string='GRP::ROLE';
		$query = 'insert into vtiger_datashare_grp2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'rs') {
		$type_string='GRP::RS';
		$query = 'insert into vtiger_datashare_grp2rs values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'groups') {
		$type_string='ROLE::GRP';
		$query = 'insert into vtiger_datashare_role2group values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'roles') {
		$type_string='ROLE::ROLE';
		$query = 'insert into vtiger_datashare_role2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'rs') {
		$type_string='ROLE::RS';
		$query = 'insert into vtiger_datashare_role2rs values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'groups') {
		$type_string='RS::GRP';
		$query = 'insert into vtiger_datashare_rs2grp values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'roles') {
		$type_string='RS::ROLE';
		$query = 'insert into vtiger_datashare_rs2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'rs') {
		$type_string='RS::RS';
		$query = 'insert into vtiger_datashare_rs2rs values(?,?,?,?)';
	}
	$query1 = 'insert into vtiger_datashare_module_rel values(?,?,?)';
	$adb->pquery($query1, array($shareid, $tabid, $type_string));
	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);
	$log->debug('< addSharingRule');
	return $shareid;
}

/** This function is to update the organisation level sharing rule
  * @param integer Id of the Sharing Rule to be updated
  * @param integer module tabid
  * @param string Entity Type may be groups,roles,rs and users
  * @param string Entity Type may be groups,roles,rs and users
  * @param integer id of the group,role,rs,user to be shared
  * @param integer id of the group,role,rs,user to which the specified entity is to be shared
  * @param integer This can have the following values:
  *                0 - Read Only
  *                1 - Read/Write
  * @return integer shareid
  */
function updateSharingRule($shareid, $tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission) {
	global $log, $adb;
	$log->debug("> updateSharingRule $shareid, $tabid, $shareEntityType, $toEntityType, $shareEntityId, $toEntityId, $sharePermission");
	$res=$adb->pquery('select * from vtiger_datashare_module_rel where shareid=?', array($shareid));
	$typestr=$adb->query_result($res, 0, 'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$adb->pquery('delete from '.$tabname.' where shareid=?', array($shareid));

	if ($shareEntityType == 'groups' && $toEntityType == 'groups') {
		$type_string='GRP::GRP';
		$query = 'insert into vtiger_datashare_grp2grp values(?,?,?,?)';
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'roles') {
		$type_string='GRP::ROLE';
		$query = 'insert into vtiger_datashare_grp2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'groups' && $toEntityType == 'rs') {
		$type_string='GRP::RS';
		$query = 'insert into vtiger_datashare_grp2rs values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'groups') {
		$type_string='ROLE::GRP';
		$query = 'insert into vtiger_datashare_role2group values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'roles') {
		$type_string='ROLE::ROLE';
		$query = 'insert into vtiger_datashare_role2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'roles' && $toEntityType == 'rs') {
		$type_string='ROLE::RS';
		$query = 'insert into vtiger_datashare_role2rs values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'groups') {
		$type_string='RS::GRP';
		$query = 'insert into vtiger_datashare_rs2grp values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'roles') {
		$type_string='RS::ROLE';
		$query = 'insert into vtiger_datashare_rs2role values(?,?,?,?)';
	} elseif ($shareEntityType == 'rs' && $toEntityType == 'rs') {
		$type_string='RS::RS';
		$query = 'insert into vtiger_datashare_rs2rs values(?,?,?,?)';
	}
	$query1 = 'update vtiger_datashare_module_rel set relationtype=? where shareid=?';
	$adb->pquery($query1, array($type_string, $shareid));
	$params = array($shareid, $shareEntityId, $toEntityId, $sharePermission);
	$adb->pquery($query, $params);
	$log->debug('< updateSharingRule');
	return $shareid;
}

/** This function is to delete the organisation level sharing rule
  * @param integer Id of the Sharing Rule to be updated
  */
function deleteSharingRule($shareid) {
	global $log, $adb;
	$log->debug('> deleteSharingRule '.$shareid);
	$res=$adb->pquery('select * from vtiger_datashare_module_rel where shareid=?', array($shareid));
	$typestr=$adb->query_result($res, 0, 'relationtype');
	$tabname=getDSTableNameForType($typestr);
	$adb->pquery("delete from $tabname where shareid=?", array($shareid));
	$adb->pquery('delete from vtiger_datashare_module_rel where shareid=?', array($shareid));
	//deleting the releated module sharing permission
	$adb->pquery('delete from vtiger_datashare_relatedmodule_permission where shareid=?', array($shareid));
	$log->debug('< deleteSharingRule');
}

/** Function get the Data Share Table and their columns
  * @return array Data Share Table and Column array in the following format:
  *  $dataShareTableColArr=Array();
  */
function getDataShareTableandColumnArray() {
	global $log;
	$log->debug('> getDataShareTableandColumnArray');
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
	$log->debug('< getDataShareTableandColumnArray');
	return $dataShareTableColArr;
}

/** Function get the Data Share Column Names for the specified Table Name
 *  @param string DataShare Table Name
 *  @return string Column Name
 */
function getDSTableColumns($tableName) {
	global $log;
	$log->debug('> getDSTableColumns '.$tableName);
	$dataShareTableColArr=getDataShareTableandColumnArray();
	$dsTableCols=$dataShareTableColArr[$tableName];
	$dsTableColsArr=explode('::', $dsTableCols);
	$log->debug('< getDSTableColumns');
	return $dsTableColsArr;
}

/** Function get the Data Share Table Names
 *  @return array Date Share Table Name array:
 *  $dataShareTableColArr=Array();
 */
function getDataShareTableName() {
	global $log;
	$log->debug('> getDataShareTableName');
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
	$log->debug('< getDataShareTableName');
	return $dataShareTableColArr;
}

/** Function to get the Data Share Table Name from the speciified type string
 *  @param string Datashare Type
 *  @return string Table Name
 */
function getDSTableNameForType($typeString) {
	global $log;
	$log->debug('> getDSTableNameForType '.$typeString);
	$dataShareTableColArr=getDataShareTableName();
	$tableName=$dataShareTableColArr[$typeString];
	$log->debug('< getDSTableNameForType');
	return $tableName;
}

/** Function to get the Entity type from the specified DataShare Table Column Name
 *  @param string Datashare Table Column Name
 *  @return string The entity type. The entity type may be vtiger_groups or vtiger_roles or rs
 */
function getEntityTypeFromCol($colName) {
	global $log;
	$log->debug('> getEntityTypeFromCol '.$colName);
	if ($colName == 'share_groupid' || $colName == 'to_groupid') {
		$entity_type='groups';
	} elseif ($colName =='share_roleid' || $colName =='to_roleid') {
		$entity_type='roles';
	} elseif ($colName == 'share_roleandsubid' || $colName == 'to_roleandsubid') {
		$entity_type='rs';
	}
	$log->debug('< getEntityTypeFromCol');
	return $entity_type;
}

/** Function to get the Entity Display Link
 *  @param integer Entity Id
 *  @param string The entity type may be groups or roles or rs
 *  @return string the Entity Display link
 */
function getEntityDisplayLink($entityType, $entityid) {
	global $log;
	$log->debug('> getEntityDisplayLink '.$entityType.','.$entityid);
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
	$log->debug('< getEntityDisplayLink');
	return $display_out;
}

/** Function to get the Sharing rule Info
 *  @param integer Sharing Rule ID
 *  @return array Sharing Rule Information in the following format:
 *    $shareRuleInfoArr=Array($shareId, $tabid, $type, $share_ent_type, $to_ent_type, $share_entity_id, $to_entity_id,$permission);
 */
function getSharingRuleInfo($shareId) {
	global $log;
	$log->debug('> getSharingRuleInfo '.$shareId);
	global $adb;
	$shareRuleInfoArr=array();
	$query='select tabid, relationtype from vtiger_datashare_module_rel where shareid=?';
	$result=$adb->pquery($query, array($shareId));
	//Retrieving the Sharing Tabid
	$tabid=$adb->query_result($result, 0, 'tabid');
	$type=$adb->query_result($result, 0, 'relationtype');

	//Retrieving the Sharing Table Name
	$tableName=getDSTableNameForType($type);

	//Retrieving the Sharing Col Names
	$dsTableColArr=getDSTableColumns($tableName);
	$share_ent_col=$dsTableColArr[0];
	$to_ent_col=$dsTableColArr[1];

	//Retrieving the Sharing Entity Col Types
	$share_ent_type=getEntityTypeFromCol($share_ent_col);
	$to_ent_type=getEntityTypeFromCol($to_ent_col);

	//Retrieving the Value from Table
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

	$log->debug('< getSharingRuleInfo');
	return $shareRuleInfoArr;
}

/** This function is to retrieve the list of related sharing modules for the specifed module
 * @param integer module tab id
 * @return array related tab id indexed array of sharing table database key
 */
function getRelatedSharingModules($tabid) {
	global $log, $adb;
	$log->debug('> getRelatedSharingModules '.$tabid);
	$relatedSharingModuleArray=array();
	$result=$adb->pquery('select * from vtiger_datashare_relatedmodules where tabid=?', array($tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$ds_relmod_id=$adb->query_result($result, $i, 'datashare_relatedmodule_id');
		$rel_tabid=$adb->query_result($result, $i, 'relatedto_tabid');
		$relatedSharingModuleArray[$rel_tabid]=$ds_relmod_id;
	}
	$log->debug('< getRelatedSharingModules');
	return $relatedSharingModuleArray;
}

/** This function is to add the related module sharing permission for a particulare Sharing Rule
  * @param integer Sharing Rule Id
  * @param integer module tabid
  * @param integer related module tabid
  * @param integer This can have the following values:
  *                0 - Read Only
  *                1 - Read/Write
  */
function addRelatedModuleSharingPermission($shareid, $tabid, $relatedtabid, $sharePermission) {
	global $log, $adb;
	$log->debug('> addRelatedModuleSharingPermission '.$shareid.','.$tabid.','.$relatedtabid.','.$sharePermission);
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid, $relatedtabid);
	$adb->pquery('insert into vtiger_datashare_relatedmodule_permission values(?,?,?)', array($shareid, $relatedModuleSharingId, $sharePermission));
	$log->debug('< addRelatedModuleSharingPermission');
}

/** This function is to update the related module sharing permission for a particulare Sharing Rule
  * @param integer Sharing Rule Id
  * @param integer module tabid
  * @param integer related module tabid
  * @param integer This can have the following values:
  *                0 - Read Only
  *                1 - Read/Write
  */
function updateRelatedModuleSharingPermission($shareid, $tabid, $relatedtabid, $sharePermission) {
	global $log, $adb;
	$log->debug('> updateRelatedModuleSharingPermission '.$shareid.','.$tabid.','.$relatedtabid.','.$sharePermission);
	$relatedModuleSharingId=getRelatedModuleSharingId($tabid, $relatedtabid);
	$query='update vtiger_datashare_relatedmodule_permission set permission=? where shareid=? and datashare_relatedmodule_id=?';
	$adb->pquery($query, array($sharePermission, $shareid, $relatedModuleSharingId));
	$log->debug('< updateRelatedModuleSharingPermission');
}

/** Retrieve the Related Module Sharing Id
 * @param integer module tabid
 * @param integer related module tabid
 * @return integer the Related Module Sharing ID
*/
function getRelatedModuleSharingId($tabid, $related_tabid) {
	global $log, $adb;
	$log->debug('> getRelatedModuleSharingId '.$tabid.','.$related_tabid);
	$query='select datashare_relatedmodule_id from vtiger_datashare_relatedmodules where tabid=? and relatedto_tabid=?';
	$result=$adb->pquery($query, array($tabid, $related_tabid));
	$relatedModuleSharingId=$adb->query_result($result, 0, 'datashare_relatedmodule_id');
	$log->debug('< getRelatedModuleSharingId');
	return $relatedModuleSharingId;
}

/** Retrieve the Related Module Sharing Permissions for the specified Sharing Rule
 * @param integer Sharing Rule ID
 * @return array Related Module Sharing permissions in the following format:
 *     $PermissionArray=(
 *        $relatedTabid1=>$sharingPermission1,
 *          |
 *        $relatedTabid-n=>$sharingPermission-n,
 *     )
*/
function getRelatedModuleSharingPermission($shareid) {
	global $log, $adb;
	$log->debug('> getRelatedModuleSharingPermission '.$shareid);
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
	$log->debug('< getRelatedModuleSharingPermission');
	return $relatedSharingModulePermissionArray;
}

/** This function is to retrieve the profiles associated with the given user
 * @param integer User ID
 * @return array profiles associated to the specified user in the following format:
 *     $userProfileArray=(profileid1,profileid2,profileid3,...,profileidn);
 */
function getUserProfile($userId) {
	global $log, $adb;
	$log->debug('> getUserProfile '.$userId);
	$key = 'getUserProfile' . $userId;
	list($profArr,$cached) = VTCacheUtils::lookupCachedInformation($key);
	if ($cached) {
		return $profArr;
	}
	$roleId=fetchUserRole($userId);
	$profArr=array();
	$result1 = $adb->pquery('select profileid from vtiger_role2profile where roleid=?', array($roleId));
	$num_rows=$adb->num_rows($result1);
	for ($i=0; $i<$num_rows; $i++) {
		$profArr[] = $adb->query_result($result1, $i, 'profileid');
	}
	VTCacheUtils::updateCachedInformation($key, $profArr);
	$log->debug('< getUserProfile');
	return $profArr;
}

/** Retrieve the global permission of the specifed user from the various profiles associated with the user
 * @param integer User ID
 * @return array user global permission in the following format:
 *     $gloabalPerrArray=(view all action id=>permission, edit all action id=>permission)
 */
function getCombinedUserGlobalPermissions($userId) {
	global $log;
	$log->debug('> getCombinedUserGlobalPermissions '.$userId);
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
	$log->debug('< getCombinedUserGlobalPermissions');
	return $userGlobalPerrArr;
}

/** retrieve the tab permissions of the specifed user from the various profiles associated with the user
 * @param integer User ID
 * @return array user global permission in the following format:
 *     $tabPerrArray=(tabid1=>permission, tabid2=>permission)
 */
function getCombinedUserTabsPermissions($userId) {
	global $log;
	$log->debug('> getCombinedUserTabsPermissions '.$userId);
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
	$log->debug('< getCombinedUserTabsPermissions');
	return $userTabPerrArr;
}

/** To retrieve the tab action permissions of the specifed user from the various profiles associated with the user
 * @param integer User ID
 * @return array user global permission in the following format:
 *     $actionPerrArray=(tabid1=>permission, tabid2=>permission);
*/
function getCombinedUserActionPermissions($userId) {
	global $log;
	$log->debug('> getCombinedUserActionPermissions '.$userId);
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
	$log->debug('< getCombinedUserActionPermissions');
	return $actionPerrArr;
}

/** Retrieve the parent role of the specified role
 * @param string Role ID
 * @return array parent role array in the following format:
 *     $parentRoleArray=(roleid1,roleid2,.......,roleidn);
*/
function getParentRole($roleId) {
	global $log;
	$log->debug('> getParentRole '.$roleId);
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
	$log->debug('< getParentRole');
	return $parentRoleArr;
}

/** Retrieve the subordinate roles of the specified parent role
 * @param string Role ID
 * @return array subordinate role array in the following format:
 *     $subordinateRoleArray=(roleid1,roleid2,.......,roleidn);
*/
function getRoleSubordinates($roleId) {
	global $log;
	$log->debug('> getRoleSubordinates '.$roleId);

	$roleSubordinates = VTCacheUtils::lookupRoleSubordinates($roleId);

	if ($roleSubordinates === false) {
		global $adb;
		$roleDetails=getRoleInformation($roleId);
		$roleInfo=$roleDetails[$roleId];
		$roleParentSeq=$roleInfo[1];

		$result=$adb->pquery('select roleid from vtiger_role where parentrole like ? order by parentrole asc', array($roleParentSeq.'::%'));
		$num_rows=$adb->num_rows($result);
		$roleSubordinates=array();
		for ($i=0; $i<$num_rows; $i++) {
			$roleid=$adb->query_result($result, $i, 'roleid');
			$roleSubordinates[]=$roleid;
		}
		// Update cache for re-use
		VTCacheUtils::updateRoleSubordinates($roleId, $roleSubordinates);
	}
	$log->debug('< getRoleSubordinates');
	return $roleSubordinates;
}

/** Retrieve the subordinate roles and users of the specified parent role
 * @param string Role ID
 * @return array subordinate role array in the following format:
 *     $subordinateRoleUserArray=(
 *        roleid1=>Array(userid1,userid2,userid3),
 *        |
 *        roleidn=>Array(userid1,userid2,userid3),
 *     );
*/
function getSubordinateRoleAndUsers($roleId, $users = true) {
	global $log;
	$log->debug('> getSubordinateRoleAndUsers '.$roleId);
	$subRoleAndUsers=array();
	$subordinateRoles=getRoleSubordinates($roleId);
	$userArray = array();
	foreach ($subordinateRoles as $subRoleId) {
		if ($users) {
			$userArray=getRoleUsers($subRoleId);
		}
		$subRoleAndUsers[$subRoleId]=$userArray;
	}
	$log->debug('< getSubordinateRoleAndUsers');
	return $subRoleAndUsers;
}

function getCurrentUserProfileList() {
	global $adb,$log,$current_user;
	$log->debug('> getCurrentUserProfileList');
	$userprivs = $current_user->getPrivileges();
	$profList = array();
	$profListTypeNoMobile = array();
	foreach ($userprivs->getProfiles() as $profid) {
		$profilename = '';
		$resprofile = $adb->pquery('SELECT profilename FROM vtiger_profile WHERE profileid=?', array($profid));
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
	$log->debug('< getCurrentUserProfileList');
	return $profList;
}

function getCurrentUserGroupList() {
	global $log,$current_user;
	$log->debug('>< getCurrentUserGroupList');
	$userprivs = $current_user->getPrivileges();
	return $userprivs->getGroups();
}

function getSubordinateUsersList() {
	global $log, $current_user;
	$log->debug('> getSubordinateUsersList');
	$user_array=array();
	$userprivs = $current_user->getPrivileges();

	foreach ($userprivs->getSubordinateRoles2Users() as $userArray) {
		foreach ($userArray as $userid) {
			if (!in_array($userid, $user_array)) {
				$user_array[]=$userid;
			}
		}
	}
	$subUserList = constructList($user_array, 'INTEGER');
	$log->debug('< getSubordinateUsersList');
	return $subUserList;
}

function getReadSharingUsersList($module) {
	global $log, $adb, $current_user;
	$log->debug('> getReadSharingUsersList '.$module);
	$user_array=array();
	$tabid=getTabid($module);
	$result=$adb->pquery('select shareduserid from vtiger_tmp_read_user_sharing_per where userid=? and tabid=?', array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$user_id=$adb->query_result($result, $i, 'shareduserid');
		$user_array[]=$user_id;
	}
	$shareUserList=constructList($user_array, 'INTEGER');
	$log->debug('< getReadSharingUsersList');
	return $shareUserList;
}

function getReadSharingGroupsList($module) {
	global $log, $adb, $current_user;
	$log->debug('> getReadSharingGroupsList '.$module);
	$grp_array=array();
	$tabid=getTabid($module);
	$result=$adb->pquery('select sharedgroupid from vtiger_tmp_read_group_sharing_per where userid=? and tabid=?', array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$grp_id=$adb->query_result($result, $i, 'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array, 'INTEGER');
	$log->debug('< getReadSharingGroupsList');
	return $shareGrpList;
}

function getWriteSharingGroupsList($module) {
	global $log, $adb, $current_user;
	$log->debug('> getWriteSharingGroupsList '.$module);
	$grp_array=array();
	$tabid=getTabid($module);
	$result=$adb->pquery('select sharedgroupid from vtiger_tmp_write_group_sharing_per where userid=? and tabid=?', array($current_user->id, $tabid));
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$grp_id=$adb->query_result($result, $i, 'sharedgroupid');
		$grp_array[]=$grp_id;
	}
	$shareGrpList=constructList($grp_array, 'INTEGER');
	$log->debug('< getWriteSharingGroupsList');
	return $shareGrpList;
}

function constructList($array, $data_type) {
	global $log;
	$log->debug('> constructList', [$array, $data_type]);
	$list= array();
	if (count($array) > 0) {
		$i=0;
		foreach ($array as $value) {
			if ($data_type == 'INTEGER') {
				$list[] = $value;
			} elseif ($data_type == 'VARCHAR') {
				$list[] = "'".$value."'";
			}
			$i++;
		}
	}
	$log->debug('< constructList');
	return $list;
}

function getListViewSecurityParameter($module) {
	global $log, $current_user;
	$log->debug('> getListViewSecurityParameter '.$module);
	if ($module == 'Emails') {
		$sec_query = ' and vtiger_crmentity.smownerid='.$current_user->id.' ';
	} else {
		$modObj = CRMEntity::getInstance($module);
		$sec_query = $modObj->getListViewSecurityParameter($module);
	}
	$log->debug('< getListViewSecurityParameter');
	return $sec_query;
}

function get_current_user_access_groups($module) {
	global $log,$adb,$noof_group_rows;
	$log->debug('> get_current_user_access_groups '.$module);
	$current_user_group_list=getCurrentUserGroupList();
	$sharing_write_group_list=getWriteSharingGroupsList($module);
	$query ='select groupname,groupid from vtiger_groups';
	$params = array();
	$result = null;
	if (count($current_user_group_list) > 0 && count($sharing_write_group_list) > 0) {
		$query .= ' where (groupid in ('. generateQuestionMarks($current_user_group_list) .') or groupid in ('. generateQuestionMarks($sharing_write_group_list) .'))';
		array_push($params, $current_user_group_list, $sharing_write_group_list);
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	} elseif (count($current_user_group_list) > 0) {
		$query .= ' where groupid in ('. generateQuestionMarks($current_user_group_list) .')';
		$params[] = $current_user_group_list;
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	} elseif (count($sharing_write_group_list) > 0) {
		$query .= ' where groupid in ('. generateQuestionMarks($sharing_write_group_list) .')';
		$params[] = $sharing_write_group_list;
		$result = $adb->pquery($query, $params);
		$noof_group_rows=$adb->num_rows($result);
	}
	$log->debug('< get_current_user_access_groups');
	return $result;
}

/** Function to get the Group Id for a given group groupname
 *  @param string Group name
 *  @return integer Group Id
 */
function getGrpId($groupname) {
	global $log, $adb;
	$log->debug('> getGrpId '.$groupname);

	$result = $adb->pquery('select groupid from vtiger_groups where groupname=?', array($groupname));
	if ($result && $adb->num_rows($result)>0) {
		$groupid = $adb->query_result($result, 0, 'groupid');
	} else {
		$groupid = 0;
	}
	$log->debug('< getGrpId');
	return $groupid;
}

/** Function to check permission to access a field for a given user
  * @param string module
  * @param integer user ID
  * @param string field name
  * @param string access mode :: readonly or anything else
  * @return string 0 | 1 :: if visible or not
 */
function getFieldVisibilityPermission($fld_module, $userid, $fieldname, $accessmode = 'readonly') {
	global $log,$adb, $current_user;
	$log->debug('> getFieldVisibilityPermission '.$fld_module.','. $userid.','. $fieldname.','.$accessmode);

	// Check if field is in-active
	if (!isFieldActive($fld_module, $fieldname)) {
		return '1';
	}

	if (empty($userid)) {
		$userid = $current_user->id;
	}
	$userprivs = $current_user->getPrivileges();

	/* Users with View all and Edit all permission will also have visibility permission for all fields */
	if ($userprivs->hasGlobalReadPermission()) {
		$log->debug('< getFieldVisibilityPermission');
		return '0';
	} else {
		//get profile list using userid
		$profilelist = $userprivs->getProfiles();

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
		$log->debug('< getFieldVisibilityPermission');
		if ($adb->num_rows($result) == 0) {
			return '1';
		}
		return ($adb->query_result($result, 0, 'visible').'');
	}
}

/** Function to check permission to access the column for a given user
 * @param integer User Id
 * @param string tablename
 * @param string columnname
 * @param string Module Name
 */
function getColumnVisibilityPermission($userid, $columnname, $module, $accessmode = 'readonly') {
	global $adb;
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
  * @return array The field Access module array
 */
function getFieldModuleAccessArray() {
	global $log, $adb;
	$log->debug('> getFieldModuleAccessArray');

	$fldModArr=array();
	$query = 'select distinct(name) from vtiger_profile2field inner join vtiger_tab on vtiger_tab.tabid=vtiger_profile2field.tabid';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$mod_name = $adb->query_result($result, $i, 'name');
		$fldModArr[$mod_name] = $mod_name;
	}
	$log->debug('< getFieldModuleAccessArray');
	return $fldModArr;
}

/** Function to get the module access array
  * @return array The Module Access array
 */
function getModuleAccessArray() {
	global $log, $adb;
	$log->debug('> getModuleAccessArray');

	$fldModArr=array();
	$query = 'SELECT distinct(name) FROM vtiger_profile2field INNER JOIN vtiger_tab ON vtiger_tab.tabid=vtiger_profile2field.tabid WHERE vtiger_tab.presence IN (0, 2)';
	$result = $adb->pquery($query, array());
	$num_rows=$adb->num_rows($result);
	for ($i=0; $i<$num_rows; $i++) {
		$mod_name = $adb->query_result($result, $i, 'name');
		$fldModArr[$mod_name] = $mod_name;
	}
	$log->debug('< getModuleAccessArray');
	return $fldModArr;
}

/**
 * this function returns presence information of all active modules
 * @return array
 */
function getTabSequence() {
	global $adb;
	$tab_seq_array = VTCacheUtils::getTabSequence();
	if (is_array($tab_seq_array)) {
		return $tab_seq_array;
	}

	$result = $adb->pquery('select tabid,presence from vtiger_tab where presence in (0,2) order by tabid', array());
	$seq_array = array();
	while ($tp = $adb->fetch_array($result)) {
		$seq_array[(string)$tp['tabid']] = (int)$tp['presence'];
	}
	VTCacheUtils::setTabSequence($seq_array);
	return $seq_array;
}

/** Function to get the permitted module name Array with presence as 0
  * @return array permitted module name array
 */
function getPermittedModuleNames() {
	global $log, $adb, $current_user;
	$log->debug('> getPermittedModuleNames');
	$permittedModules = array();
	$userprivs = $current_user->getPrivileges();
	$profileTabsPermission = $userprivs->getprofileTabsPermission();
	$tab_seq_array = getTabSequence();

	if (defined('COREBOS_INSIDE_MOBILE')) {
		foreach ($userprivs->getProfiles() as $profid) {
			$profilename = '';
			$resprofile = $adb->pquery('SELECT profilename FROM vtiger_profile WHERE profileid = ?', array($profid));
			$profilename = $adb->query_result($resprofile, 0, 'profilename');
			if (strpos($profilename, 'Mobile::') !== false) {
				$profileTabsPermission=getProfileTabsPermission($profid);
			}
		}
	}

	if (!$userprivs->hasGlobalReadPermission()) {
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
	$log->debug('< getPermittedModuleNames');
	return $permittedModules;
}

/** Function to get the permitted module id Array with presence as 0
 * @global Users $current_user
 * @return array array of accessible tabids.
 */
function getPermittedModuleIdList() {
	global $current_user, $adb;
	$permittedModules=array();
	$userprivs = $current_user->getPrivileges();
	$profileTabsPermission = $userprivs->getprofileTabsPermission();
	$tab_seq_array = getTabSequence();

	if (defined('COREBOS_INSIDE_MOBILE')) {
		foreach ($userprivs->getProfiles() as $profid) {
			$profilename = '';
			$resprofile = $adb->pquery('SELECT profilename FROM vtiger_profile WHERE profileid = ?', array($profid));
			$profilename = $adb->query_result($resprofile, 0, 'profilename');
			if (strpos($profilename, 'Mobile::') !== false) {
				$profileTabsPermission=getProfileTabsPermission($profid);
			}
		}
	}

	if (!$userprivs->hasGlobalReadPermission()) {
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

/** Function to recalculate the Sharing Rules for all the users */
function RecalculateSharingRules($roleId = 0) {
	global $log;
	$log->debug('> RecalculateSharingRules');
	require_once 'modules/Users/UserPrivilegesWriter.php';
	UserPrivilegesWriter::flushAllPrivileges($roleId);
	$log->debug('< RecalculateSharingRules');
}

/** Function to get the list of module for which the user defined sharing rules can be defined
  * @return array
  */
function getSharingModuleList($eliminateModules = false) {
	global $adb;

	$sharingModuleArray = array();

	if (empty($eliminateModules)) {
		$eliminateModules = array();
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
	$crmEntityTable = CRMEntity::getcrmEntityTableAlias('cbCalendar');
	$query = "select 1
			from vtiger_sharedcalendar
			where sharedid=? and
			userid in (
				select smownerid as usrid
				from vtiger_activity
				inner join $crmEntityTable on vtiger_crmentity.crmid=vtiger_activity.activityid
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

/** Function to populate default entries for the picklist while creating a new role */
function insertRole2Picklist($roleid, $parentroleid) {
	global $adb,$log;
	$log->debug("> insertRole2Picklist $roleid,$parentroleid");
	$sql = "insert into vtiger_role2picklist select '".$roleid."',picklistvalueid,picklistid,sortid from vtiger_role2picklist where roleid=?";
	$adb->pquery($sql, array($parentroleid));
	$log->debug('< insertRole2Picklist');
}

/** Function to delete group to report relation of the  specified group
 * @param integer Group Id
 */
function deleteGroupReportRelations($groupId) {
	global $log, $adb;
	$log->debug('> deleteGroupReportRelations '.$groupId);
	$adb->pquery("delete from vtiger_reportsharing where shareid=? and setype='groups'", array($groupId));
	$log->debug('< deleteGroupReportRelations');
}

/** Function to check if the field is Active
 * @param string Module Name
 * @param string Field Name
 * @return boolean true if field is active, false if not
 */
function isFieldActive($modulename, $fieldname) {
	$fieldid = getFieldid(getTabid($modulename), $fieldname, true);
	return ($fieldid !== false);
}
?>
