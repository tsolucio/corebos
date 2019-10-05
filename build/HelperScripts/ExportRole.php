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
*  Module       : Export Role Process
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
require_once 'include/logging.php';
require_once 'include/utils/UserInfoUtil.php';
include_once 'config.inc.php';
global $adb,$root_directory;

$uri = 'build/role.xml';
$xmlwriter = new XMLWriter();
if (file_exists($uri)) {
	unlink($uri);
}
touch($uri);
$uri = realpath($uri);
$xmlwriter->openURI($uri);
$xmlwriter->setIndent(true);

$xmlwriter->startDocument('1.0', 'UTF-8');

// Start the parent role element
$xmlwriter->startElement('vtcrm_roles');

$pfrs = $adb->query('select * from vtiger_role order by depth');  // Order is VERY important for import
while ($pf = $adb->fetch_array($pfrs)) {
	$xmlwriter->startElement('vtcrm_role');
	// Start the element for role definition
	$xmlwriter->startElement('vtcrm_definition');

	// Start the element for name field
	$xmlwriter->startElement('vtcrm_rolename');
	$xmlwriter->text($pf['rolename']);
	$xmlwriter->endElement(); //end rolename
	$xmlwriter->startElement('vtcrm_parentrole');
	$pr = explode('::', $pf['parentrole']);
	$prnames = array();
	foreach ($pr as $prole) {
		$prnames[] = $adb->getone("select rolename from vtiger_role where roleid='$prole'");
	}
	$prn = implode('::', $prnames);
	$xmlwriter->text($prn);
	$xmlwriter->endElement(); //end parentrole
	$xmlwriter->startElement('vtcrm_depth');
	$xmlwriter->text($pf['depth']);
	$xmlwriter->endElement(); //end depth

	$xmlwriter->endElement();// end definition

	$first_roleid = $pf['roleid'];

	// Start the parent role2profile element
	$xmlwriter->startElement('vtcrm_role2profiles');

	$tab_perr_result = $adb->pquery("select * from vtiger_role2profile where roleid=?", array($first_roleid));
	while ($p2t = $adb->fetch_array($tab_perr_result)) {
		$xmlwriter->startElement('vtcrm_role2pf');
		$pfname = $adb->getone('select profilename from vtiger_profile where profileid='.$p2t['profileid']);
		$xmlwriter->text($pfname);
		$xmlwriter->endElement(); //end role2pf
	}
	$xmlwriter->endElement();// end role2profiles

	$xmlwriter->endElement();// end role
}

$xmlwriter->endElement();// end roles

// Output the xml
$xmlwriter->flush();
echo "Roles Exported!";