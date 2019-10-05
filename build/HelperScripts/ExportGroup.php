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
*  Module       : Export Group Process
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
require_once 'include/logging.php';
require_once 'include/utils/UserInfoUtil.php';
include_once 'config.inc.php';
global $adb,$root_directory;
$uri = 'build/group.xml';
$xmlwriter = new XMLWriter();
if (file_exists($uri)) {
	unlink($uri);
}
touch($uri);
$uri = realpath($uri);
$xmlwriter->openURI($uri);
$xmlwriter->setIndent(true);

$xmlwriter->startDocument('1.0', 'UTF-8');

// Start the parent group element
$xmlwriter->startElement('vtcrm_groups');

// order is very important for importing
$pfrs = $adb->query('select * from vtiger_groups order by groupid');
while ($pf = $adb->fetch_array($pfrs)) {
	$xmlwriter->startElement('vtcrm_group');
	// Start the element for group definition
	$xmlwriter->startElement('vtcrm_definition');

	// Start the element for name field
	$xmlwriter->startElement('vtcrm_groupname');
	$xmlwriter->writeAttribute('groupid', $pf['groupid']);
	echo $pf['groupname']."<br>";
	$xmlwriter->writeCdata($pf['groupname']);
	$xmlwriter->endElement(); //end groupname
	$xmlwriter->startElement('vtcrm_groupdescription');
	$xmlwriter->writeCdata($pf['description']);
	$xmlwriter->endElement(); //end description

	$xmlwriter->endElement();// end definition

	$first_groupid = $pf['groupid'];

	// Start the parent group2role element
	$xmlwriter->startElement('vtcrm_group2roles');

	$tab_perr_result = $adb->pquery("select * from vtiger_group2role where groupid=?", array($first_groupid));
	while ($p2t = $adb->fetch_array($tab_perr_result)) {
		$xmlwriter->startElement('vtcrm_group2role');
		$rolename = $adb->getone("select rolename from vtiger_role where roleid='".$p2t['roleid']."'");
		$xmlwriter->text($rolename);
		$xmlwriter->endElement(); //end group2role
	}
	$xmlwriter->endElement();// end group2roles

	// Start the parent group2rolesub element
	$xmlwriter->startElement('vtcrm_group2rolesubs');

	$tab_perr_result = $adb->pquery("select * from vtiger_group2rs where groupid=?", array($first_groupid));
	while ($p2t = $adb->fetch_array($tab_perr_result)) {
		$xmlwriter->startElement('vtcrm_group2rolesub');
		$rolename = $adb->getone("select rolename from vtiger_role where roleid='".$p2t['roleandsubid']."'");
		$xmlwriter->text($rolename);
		$xmlwriter->endElement(); //end group2rolesub
	}
	$xmlwriter->endElement();// end group2rolesubs

	// Start the parent group2group element
	$xmlwriter->startElement('vtcrm_group2groups');

	$tab_perr_result = $adb->pquery("select * from vtiger_group2grouprel where groupid=?", array($first_groupid));
	while ($p2t = $adb->fetch_array($tab_perr_result)) {
		$xmlwriter->startElement('vtcrm_group2group');
		$grpname = $adb->getone("select groupname from vtiger_groups where groupid='".$p2t['containsgroupid']."'");
		$xmlwriter->text($grpname);
		$xmlwriter->endElement(); //end group2group
	}
	$xmlwriter->endElement();// end group2groups

	$xmlwriter->endElement();// end group
}

$xmlwriter->endElement();// end groups

// Output the xml
$xmlwriter->flush();
echo "Groups Exported!";