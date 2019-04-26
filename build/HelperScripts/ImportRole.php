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
*  Module       : Import Role Process
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

$xmlreader = new SimpleXMLElement('build/role.xml', 0, true);

// order is very important, dependent roles appear later in the XML so we can count on having them created
foreach ($xmlreader->vtcrm_role as $role) {
	$rlname = html_entity_decode((string)$role->vtcrm_definition->vtcrm_rolename, ENT_QUOTES, 'UTF-8');

	//$pfexist = $adb->getone("select count(*) as cnt from vtiger_role where rolename='".addslashes($rlname)."'");
	$pfrs = $adb->pquery('select count(*) as cnt from vtiger_role where rolename=?', array($rlname));
	$pfcnt = $adb->fetch_array($pfrs);
	if (!empty($pfcnt['cnt'])) {
		echo "$rlname already exists!";
		continue;
	}

	if (strrpos((string)$role->vtcrm_definition->vtcrm_parentrole, '::')===false) {
		$prole=(string)$role->vtcrm_definition->vtcrm_parentrole;
	} else {
		// eliminate las role which is the same as we are creating
		$prole=substr((string)$role->vtcrm_definition->vtcrm_parentrole, 0, strrpos((string)$role->vtcrm_definition->vtcrm_parentrole, '::'));
		if (strrpos($prole, '::')!==false) {
			$prole=substr($prole, strrpos($prole, '::')+2);
		}
	}
	$parentRoleId = $adb->getone("select roleid from vtiger_role where rolename='$prole'");  // this one must exist if XML is sorted correctly
	$profile_array = array();
	foreach ($role->vtcrm_role2profiles->vtcrm_role2pf as $profile) {
		$profile_array[] = $adb->getone("select profileid from vtiger_profile where profilename='".(string)$profile."'");
	}
	$roleId = createRole($rlname, $parentRoleId, $profile_array);
	if (!empty($roleId)) {
		insertRole2Picklist($roleId, $parentRoleId);
	}
	echo "$rlname imported!";
}
?>
