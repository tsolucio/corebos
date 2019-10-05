<?php
/*************************************************************************************************
* Copyright 2013 JPL TSolucio, S.L.  --  This file is a part of Attorney BackOffice.
* You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
* Vizsage Public License (the "License"). You may not use this file except in compliance with the
* License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
* and share improvements. However, for proper details please read the full License, available at
* http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
* the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
* applicable law or agreed to in writing, any software distributed under the License is distributed
* on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and limitations under the
* License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Module       : Import Group Process
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
require_once 'include/logging.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';
include_once 'config.inc.php';
global $adb,$root_directory;

$xmlreader = new SimpleXMLElement('build/group.xml', 0, true);

$missingGroups = array();
foreach ($xmlreader->vtcrm_group as $group) {
	$grpname = html_entity_decode((string)$group->vtcrm_definition->vtcrm_groupname, ENT_QUOTES, 'UTF-8');
	$grpdesc = html_entity_decode((string)$group->vtcrm_definition->vtcrm_groupdescription, ENT_QUOTES, 'UTF-8');

	//$pfexist = $adb->getone("select count(*) as cnt from vtiger_groups where groupname='".addslashes($grpname)."'");
	$pfrs = $adb->pquery('select count(*) as cnt from vtiger_groups where groupname=?', array($grpname));
	$pfcnt = $adb->fetch_array($pfrs);
	if (!empty($pfcnt['cnt'])) {
		echo "$grpname already exists!";
		continue;
	}

	$grpMembers = array();

	$grpm = array();
	$missgrp = array();
	foreach ($group->vtcrm_group2groups->vtcrm_group2group as $grp) {
		$grprs = $adb->pquery('select groupid from vtiger_groups where groupname=?', array((string)$grp));
		if ($adb->num_rows($grprs)==0) {
			$missgrp[] = (string)$grp;
		} else {
			$getgrpid = $adb->fetch_array($grprs);
			$grpm[] = $getgrpid['groupid'];
		}
	}
	$grpMembers['groups'] = $grpm;

	$grpm = array();
	foreach ($group->vtcrm_group2roles->vtcrm_group2role as $role) {
		$grpm[] = $adb->getone("select roleid from vtiger_role where rolename='".addslashes((string)$role)."'");
	}
	$grpMembers['roles'] = $grpm;

	$grpm = array();
	foreach ($group->vtcrm_group2rolesubs->vtcrm_group2rolesub as $role) {
		$grpm[] = $adb->getone("select roleid from vtiger_role where rolename='".addslashes((string)$role)."'");
	}
	$grpMembers['rs'] = $grpm;

	$grpMembers['users'] = array();

	$groupId = createGroup($grpname, $grpMembers, $grpdesc);

	if (count($missgrp)>0) {
		$missingGroups[$groupId] = $missgrp;
	}

	echo "$grpname imported!";
}

// due to the possibility of groups depending on groups in any combinations
// it is possible to create a group before having created all it's depending
// groups, for this case we fill in the $missingGroups during creation and add them now
foreach ($missingGroups as $grpid => $miss) {
	foreach ($miss as $grpname) {
		$containsGroupId = $adb->getone("select groupid from vtiger_groups where groupname='".addslashes($grpname)."'");
		if (!empty($containsGroupId)) {
			insertGroupToGroupRelation($grpid, $containsGroupId);
		}
	}
}
?>
