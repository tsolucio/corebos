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
*  Module       : Export Profile Process
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
require_once 'include/logging.php';
require_once 'include/utils/UserInfoUtil.php';
include_once 'config.inc.php';
global $adb;
if (!empty($_REQUEST['language'])) {
	$lang = $_REQUEST['language'];
} else {
	$lang = 'en';
}
$category = vtlib_purify($_REQUEST['category']);
$filename = "export_".strtolower($category).".xml";
header('Content-Type: text/xml/force-download');
header("Content-Disposition: attachment; filename=$filename");
$xmlwriter = new XMLWriter();
$xmlwriter->openURI("php://output");
$xmlwriter->setIndent(true);

$xmlwriter->startDocument('1.0', 'UTF-8');

if (!empty($category) && $category == 'ROLE') {
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
}
if (!empty($category) && $category == 'PROFILE') {
	// Start the parent profile element
	$xmlwriter->startElement('vtcrm_profiles');

	$pfrs = $adb->query("select * from vtiger_profile");
	while ($pf = $adb->fetch_array($pfrs)) {
		$xmlwriter->startElement('vtcrm_profile');
		// Start the element for profile definition
		$xmlwriter->startElement('vtcrm_definition');

		// Start the element for name field
		$xmlwriter->startElement('vtcrm_profilename');
		$xmlwriter->text($pf['profilename']);
		$xmlwriter->endElement(); //end profilename
		$xmlwriter->startElement('vtcrm_profiledescription');
		$xmlwriter->writeCdata($pf['description']);
		$xmlwriter->endElement(); //end description

		$xmlwriter->endElement();// end definition

		$first_prof_id = $pf['profileid'];

		// Start the parent profile2glb element
		$xmlwriter->startElement('vtcrm_profile2glbs');

		$tab_perr_result = $adb->pquery("select * from vtiger_profile2globalpermissions where profileid=?", array($first_prof_id));
		while ($p2t = $adb->fetch_array($tab_perr_result)) {
			$xmlwriter->startElement('vtcrm_profile2glb');
			$xmlwriter->writeAttribute('permission', $p2t['globalactionpermission']);
			$xmlwriter->text($p2t['globalactionid']);
			$xmlwriter->endElement(); //end profile2glb
		}
		$xmlwriter->endElement();// end profile2glbs

		// Start the parent profile2tab element
		$xmlwriter->startElement('vtcrm_profile2tabs');

		$tab_perr_result = $adb->pquery("select * from vtiger_profile2tab where profileid=?", array($first_prof_id));
		while ($p2t = $adb->fetch_array($tab_perr_result)) {
			$xmlwriter->startElement('vtcrm_profile2tab');
			$xmlwriter->writeAttribute('permission', $p2t['permissions']);
			$tabname = $adb->getone('select name from vtiger_tab where tabid='.$p2t['tabid']);
			$xmlwriter->text($tabname);
			$xmlwriter->endElement(); //end profile2tab
		}
		$xmlwriter->endElement();// end profile2tabs

		// Start the parent profile2std element
		$xmlwriter->startElement('vtcrm_profile2stds');

		$act_perr_result = $adb->pquery("select * from vtiger_profile2standardpermissions where profileid=?", array($first_prof_id));
		while ($p2s = $adb->fetch_array($act_perr_result)) {
			$xmlwriter->startElement('vtcrm_profile2std');
			$xmlwriter->writeAttribute('operation', $p2s['operation']);
			$xmlwriter->writeAttribute('permission', $p2s['permissions']);
			$tabname = $adb->getone('select name from vtiger_tab where tabid='.$p2s['tabid']);
			$xmlwriter->text($tabname);
			$xmlwriter->endElement(); //end profile2std
		}
		$xmlwriter->endElement();// end profile2stds

		// Start the parent profile2util element
		$xmlwriter->startElement('vtcrm_profile2utils');

		$act_utility_result = $adb->pquery("select * from vtiger_profile2utility where profileid=?", array($first_prof_id));
		while ($p2u = $adb->fetch_array($act_utility_result)) {
			$xmlwriter->startElement('vtcrm_profile2util');
			$xmlwriter->writeAttribute('activityid', $p2u['activityid']);
			$xmlwriter->writeAttribute('permission', $p2u['permission']);
			$tabname = $adb->getone('select name from vtiger_tab where tabid='.$p2u['tabid']);
			$xmlwriter->text($tabname);
			$xmlwriter->endElement(); //end profile2util
		}
		$xmlwriter->endElement();// end profile2utils

		// Start the parent profile2field element
		$xmlwriter->startElement('vtcrm_profile2fields');

		$p2fld_result = $adb->pquery("select * from vtiger_profile2field where profileid=?", array($first_prof_id));
		while ($p2f = $adb->fetch_array($p2fld_result)) {
			$xmlwriter->startElement('vtcrm_profile2field');
			$xmlwriter->writeAttribute('visible', $p2f['visible']);
			$xmlwriter->writeAttribute('readonly', $p2f['readonly']);
			$tabname = $adb->getone('select name from vtiger_tab where tabid='.$p2f['tabid']);
			$xmlwriter->writeAttribute('tabname', $tabname);
			$fieldname = $adb->getone('select fieldname from vtiger_field where fieldid='.$p2f['fieldid']);
			$colname = $adb->getone('select columnname from vtiger_field where fieldid='.$p2f['fieldid']);
			$xmlwriter->text($fieldname.'::::'.$colname);
			$xmlwriter->endElement(); //end profile2field
		}
		$xmlwriter->endElement();// end profile2fields

		$xmlwriter->endElement();// end profile
	}

	$xmlwriter->endElement();// end profiles

	// Output the xml
	$xmlwriter->flush();
}
if (!empty($category) && $category == 'GROUP') {
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
}
