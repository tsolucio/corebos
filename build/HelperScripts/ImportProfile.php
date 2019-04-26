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
*  Module       : Import Profile Process
*  Version      : 5.4.0
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Users/Users.php';
require_once 'include/logging.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/utils/utils.php';
include_once 'config.inc.php';
global $adb;

//$xmlstr = file_get_contents("build/data/$lang/profile.xml");
//$if = getImportDataFileName('profile.xml',$lang);
$xmlreader = new SimpleXMLElement('build/profile.xml', 0, true);

foreach ($xmlreader->vtcrm_profile as $profile) {
	$prfname = html_entity_decode((string)$profile->vtcrm_definition->vtcrm_profilename, ENT_QUOTES, 'UTF-8');
	$prfdesc = html_entity_decode((string)$profile->vtcrm_definition->vtcrm_profiledescription, ENT_QUOTES, 'UTF-8');

	//$pfexist = $adb->getone("select count(*) as cnt from vtiger_profile where profilename='".addslashes($prfname)."'");
	$pfrs = $adb->pquery('select count(*) as cnt from vtiger_profile where profilename=?', array($prfname));
	$pfcnt = $adb->fetch_array($pfrs);
	if (!empty($pfcnt['cnt'])) {
		echo "$prfname already exists!";
		continue;
	}
	$profile_id = $adb->getUniqueID("vtiger_profile");
	//Inserting values into Profile Table
	$sql1 = "insert into vtiger_profile(profileid, profilename, description) values(?,?,?)";
	$adb->pquery($sql1, array($profile_id,$prfname, $prfdesc));

	$sql4="insert into vtiger_profile2globalpermissions values(?,?,?)";
	foreach ($profile->vtcrm_profile2glbs->vtcrm_profile2glb as $pf2glb) {
		$adb->pquery($sql4, array($profile_id,(string)$pf2glb,(string)$pf2glb->attributes()->permission));
	}

	// default values
	$sql4="insert into vtiger_profile2tab values(?,?,?)";
	$rstab = $adb->query('select tabid from vtiger_tab');
	while ($tb = $adb->fetch_array($rstab)) {
		$adb->pquery($sql4, array($profile_id, $tb['tabid'], 0));
	}
	// import values
	$sql4="update vtiger_profile2tab set permissions=? where profileid=? and tabid=?";
	foreach ($profile->vtcrm_profile2tabs->vtcrm_profile2tab as $pf2tab) {
		if ((string)$pf2tab->attributes()->permission!=0) {
			$tab_id=getTabid((string)$pf2tab);
			if (!empty($tab_id)) {
				$adb->pquery($sql4, array((string)$pf2tab->attributes()->permission, $profile_id, $tab_id));
			}
		}
	}

	// default values
	$sql4="insert into vtiger_profile2standardpermissions values(?,?,?,?)";
	$rstab = $adb->query('select tabid from vtiger_tab');
	while ($tb = $adb->fetch_array($rstab)) {
		for ($action=0; $action<5; $action++) {  // count 5 actions
			$adb->pquery($sql4, array($profile_id, $tb['tabid'], $action, 0));
		}
	}
	// import values
	$sql7="update vtiger_profile2standardpermissions set permissions=? where profileid=? and tabid=? and operation=?";
	foreach ($profile->vtcrm_profile2stds->vtcrm_profile2std as $pf2tab) {
		if ((string)$pf2tab->attributes()->permission!=0) {
			$tab_id=getTabid((string)$pf2tab);
			if (!empty($tab_id)) {
				$adb->pquery($sql7, array((string)$pf2tab->attributes()->permission, $profile_id, $tab_id, (string)$pf2tab->attributes()->operation));
			}
		}
	}

	// import values
	$importedtabs = array();
	//$sql9="insert IGNORE into vtiger_profile2utility values(?,?,?,?)";
	$sql9="insert into vtiger_profile2utility values(?,?,?,?)";
	foreach ($profile->vtcrm_profile2utils->vtcrm_profile2util as $pf2tab) {
		$tab_id=getTabid((string)$pf2tab);
		if (!empty($tab_id)) {
			$importedtabs[] = $tab_id;
			$adb->pquery($sql9, array($profile_id, $tab_id, (string)$pf2tab->attributes()->activityid, (string)$pf2tab->attributes()->permission));
		}
	}
	// default values
	$rstab = $adb->query('select tabid,isentitytype from vtiger_tab');
	while ($tb = $adb->fetch_array($rstab)) {
		if (!in_array($tb['tabid'], $importedtabs)) {
			if ($tb['isentitytype']!=0) {
				$adb->pquery($sql9, array($profile_id, $tb['tabid'], 5, 0));
				$adb->pquery($sql9, array($profile_id, $tb['tabid'], 6, 0));
				$adb->pquery($sql9, array($profile_id, $tb['tabid'], 8, 0));
				$adb->pquery($sql9, array($profile_id, $tb['tabid'], 10, 0));
			}
		}
	}

	// default values
	insertProfile2field($profile_id);  // set default values for all fields
	// import values
	$p2fins="INSERT INTO vtiger_profile2field (profileid, tabid, fieldid, visible, readonly) VALUES(?,?,?,?,?)";
	$p2fupd="UPDATE vtiger_profile2field set visible=?, readonly=? where profileid=? and tabid=? and fieldid=?";
	$lasttabname='';
	foreach ($profile->vtcrm_profile2fields->vtcrm_profile2field as $pf2tab) {
		if ((string)$pf2tab->attributes()->tabname!=$lasttabname) {
			$lasttabname=(string)$pf2tab->attributes()->tabname;
			$tab_id=getTabid($lasttabname);
		}
		if ((string)$pf2tab->attributes()->visible!='0' || (string)$pf2tab->attributes()->readonly!='0') {
			list($fname,$cname) = explode('::::', (string)$pf2tab);
			$fieldid = $adb->getone("select fieldid from vtiger_field where fieldname='$fname' and columnname='$cname' and tabid=$tab_id");
			if (!empty($fieldid)) {
				$adb->pquery($p2fupd, array((string)$pf2tab->attributes()->visible, (string)$pf2tab->attributes()->readonly, $profile_id, $tab_id, $fieldid));
			}
		}
	}
	echo "$prfname imported!";
}
?>