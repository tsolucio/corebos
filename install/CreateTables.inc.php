<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$new_tables = 0;

require_once('config.php');
require_once('include/logging.php');
require_once('modules/Leads/Leads.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Accounts/Accounts.php');
require_once('modules/Potentials/Potentials.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('modules/Users/Users.php');
require_once('modules/Users/LoginHistory.php');
require_once('data/Tracker.php');
require_once('include/utils/utils.php');
require_once('modules/Users/DefaultDataPopulator.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');

// load the config_override.php file to provide default user settings
if (is_file("config_override.php")) {
	require_once("config_override.php");
}

$adb = PearDatabase::getInstance();
$log =& LoggerManager::getLogger('INSTALL');

function create_default_users_access() {
      	global $log, $adb;
        global $admin_email;
        global $admin_password;

        $role1_id = $adb->getUniqueID("vtiger_role");
		$role2_id = $adb->getUniqueID("vtiger_role");
		$role3_id = $adb->getUniqueID("vtiger_role");
		$role4_id = $adb->getUniqueID("vtiger_role");
		$role5_id = $adb->getUniqueID("vtiger_role");

		$profile1_id = $adb->getUniqueID("vtiger_profile");
		$profile2_id = $adb->getUniqueID("vtiger_profile");
		$profile3_id = $adb->getUniqueID("vtiger_profile");
		$profile4_id = $adb->getUniqueID("vtiger_profile");

		$adb->query("insert into vtiger_role values('H".$role1_id."','Organisation','H".$role1_id."',0)");
        $adb->query("insert into vtiger_role values('H".$role2_id."','CEO','H".$role1_id."::H".$role2_id."',1)");
        $adb->query("insert into vtiger_role values('H".$role3_id."','Vice President','H".$role1_id."::H".$role2_id."::H".$role3_id."',2)");
        $adb->query("insert into vtiger_role values('H".$role4_id."','Sales Manager','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."',3)");
        $adb->query("insert into vtiger_role values('H".$role5_id."','Sales Man','H".$role1_id."::H".$role2_id."::H".$role3_id."::H".$role4_id."::H".$role5_id."',4)");

		//Insert into vtiger_role2profile
		$adb->query("insert into vtiger_role2profile values ('H".$role2_id."',".$profile1_id.")");
		$adb->query("insert into vtiger_role2profile values ('H".$role3_id."',".$profile2_id.")");
	  	$adb->query("insert into vtiger_role2profile values ('H".$role4_id."',".$profile2_id.")");
		$adb->query("insert into vtiger_role2profile values ('H".$role5_id."',".$profile2_id.")");

		//New Security Start
		//Inserting into vtiger_profile vtiger_table
		$adb->query("insert into vtiger_profile values ('".$profile1_id."','Administrator','Admin Profile')");
		$adb->query("insert into vtiger_profile values ('".$profile2_id."','Sales Profile','Profile Related to Sales')");
		$adb->query("insert into vtiger_profile values ('".$profile3_id."','Support Profile','Profile Related to Support')");
		$adb->query("insert into vtiger_profile values ('".$profile4_id."','Guest Profile','Guest Profile for Test Users')");

		//Inserting into vtiger_profile2gloabal permissions
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile1_id."',1,0)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile1_id."',2,0)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile2_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile2_id."',2,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile3_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile3_id."',2,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile4_id."',1,1)");
		$adb->query("insert into vtiger_profile2globalpermissions values ('".$profile4_id."',2,1)");

		//Inserting into vtiger_profile2tab
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",25,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile1_id.",27,0)");

		//Inserting into vtiger_profile2tab
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",25,0)");
        $adb->query("insert into vtiger_profile2tab values (".$profile2_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile2_id.",27,0)");

		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",25,0)");
        $adb->query("insert into vtiger_profile2tab values (".$profile3_id.",26,0)");
       	$adb->query("insert into vtiger_profile2tab values (".$profile3_id.",27,0)");

		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",1,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",2,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",3,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",4,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",6,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",7,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",8,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",9,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",10,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",13,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",14,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",15,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",16,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",18,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",19,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",20,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",21,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",22,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",23,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",24,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",25,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",26,0)");
		$adb->query("insert into vtiger_profile2tab values (".$profile4_id.",27,0)");
		//Inserting into vtiger_profile2standardpermissions  Adminsitrator

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",23,4,0)");

        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile1_id.",26,4,0)");

		//Insert into Profile 2 std permissions for Sales User
		//Help Desk Create/Delete not allowed. Read-Only
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",23,4,0)");


        	$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile2_id.",26,4,0)");

		//Inserting into vtiger_profile2std for Support Profile
		// Potential is read-only
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",18,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",19,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",23,4,0)");


        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,0,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,1,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,2,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile3_id.",26,4,0)");

		//Inserting into vtiger_profile2stdper for Profile Guest Profile
		//All Read-Only
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",2,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",4,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",6,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",7,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",8,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",9,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",13,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",14,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",15,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",16,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",18,4,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",19,4,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",20,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",21,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",22,4,0)");

		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",23,4,0)");


        $adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,0,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,1,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,2,1)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,3,0)");
		$adb->query("insert into vtiger_profile2standardpermissions values (".$profile4_id.",26,4,0)");

		//Inserting into vtiger_profile 2 utility Admin
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",8,6,0)");
       	$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,5,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,5,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,6,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,5,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,6,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile1_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile1_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Sales Profile
		//Import Export Not Allowed.
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",8,6,1)");
       	$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile2_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile2_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Support Profile
		//Import Export Not Allowed.
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",8,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,8,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,8,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile3_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile3_id.",18,10,0)");

		//Inserting into vtiger_profile2utility Guest Profile Read-Only
		//Import Export BusinessCar Not Allowed.
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",8,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,8,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,8,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,8,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,5,1)");
    	$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,8,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,5,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,6,1)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,9,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,5,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,6,1)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",7,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",6,10,0)");
        $adb->query("insert into vtiger_profile2utility values (".$profile4_id.",4,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",2,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",13,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",14,10,0)");
		$adb->query("insert into vtiger_profile2utility values (".$profile4_id.",18,10,0)");

		 // Invalidate any cached information
    	VTCacheUtils::clearRoleSubordinates();

        // create default admin user
    	$user = new Users();
        $user->column_fields["last_name"] = 'Administrator';
        $user->column_fields["user_name"] = 'admin';
        $user->column_fields["status"] = 'Active';
        $user->column_fields["is_admin"] = 'on';
        $user->column_fields["user_password"] = $admin_password;
        $user->column_fields["tz"] = 'Europe/Berlin';
        $user->column_fields["holidays"] = 'de,en_uk,fr,it,us,';
        $user->column_fields["workdays"] = '0,1,2,3,4,5,6,';
        $user->column_fields["weekstart"] = '1';
        $user->column_fields["namedays"] = '';
        $user->column_fields["currency_id"] = 1;
        $user->column_fields["reminder_interval"] = '1 Minute';
        $user->column_fields["reminder_next_time"] = date('Y-m-d H:i');
		$user->column_fields["date_format"] = 'yyyy-mm-dd';
		$user->column_fields["hour_format"] = 'am/pm';
		$user->column_fields["start_hour"] = '08:00';
		$user->column_fields["end_hour"] = '23:00';
		$user->column_fields["imagename"] = '';
		$user->column_fields["internal_mailer"] = '1';
		$user->column_fields["activity_view"] = 'This Week';
		$user->column_fields["lead_view"] = 'Today';
        //added by philip for default admin emailid
		if($admin_email == '')
			$admin_email ="admin@corebos.org";
        $user->column_fields["email1"] = $admin_email;
		$role_query = "select roleid from vtiger_role where rolename='CEO'";
		$adb->checkConnection();
		$adb->database->SetFetchMode(ADODB_FETCH_ASSOC);
		$role_result = $adb->query($role_query);
		$role_id = $adb->query_result($role_result,0,"roleid");
		$user->column_fields["roleid"] = $role_id;

        $user->save("Users");
        $admin_user_id = $user->id;

		//Inserting into vtiger_groups table
		$group1_id = $adb->getUniqueID("vtiger_users");
		$group2_id = $adb->getUniqueID("vtiger_users");
		$group3_id = $adb->getUniqueID("vtiger_users");

		$adb->query("insert into vtiger_groups values ('".$group1_id."','Team Selling','Group Related to Sales')");
		$adb->query("insert into vtiger_group2role values ('".$group1_id."','H".$role4_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group1_id."','H".$role5_id."')");

		$adb->query("insert into vtiger_groups values ('".$group2_id."','Marketing Group','Group Related to Marketing Activities')");
		$adb->query("insert into vtiger_group2role values ('".$group2_id."','H".$role2_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group2_id."','H".$role3_id."')");

		$adb->query("insert into vtiger_groups values ('".$group3_id."','Support Group','Group Related to providing Support to Customers')");
		$adb->query("insert into vtiger_group2role values ('".$group3_id."','H".$role3_id."')");
		$adb->query("insert into vtiger_group2rs values ('".$group3_id."','H".$role3_id."')");

		// Setting user group relation for admin user
	 	$adb->pquery("insert into vtiger_users2group values (?,?)", array($group2_id, $admin_user_id));

		//Creating the flat files for admin user
		createUserPrivilegesfile($admin_user_id);
		createUserSharingPrivilegesfile($admin_user_id);

		//Insert into vtiger_profile2field
		insertProfile2field($profile1_id);
        insertProfile2field($profile2_id);
        insertProfile2field($profile3_id);
        insertProfile2field($profile4_id);

	insert_def_org_field();

}

$modules = array("DefaultDataPopulator");
$focus=0;
$success = $adb->createTables("schema/DatabaseSchema.xml");

//Postgres8 fix - create sequences.
//   This should be a part of "createTables" however ...
 if( $adb->dbType == "pgsql" ) {
     $sequences = array(
 	"vtiger_leadsource_seq",
 	"vtiger_accounttype_seq",
 	"vtiger_industry_seq",
 	"vtiger_leadstatus_seq",
 	"vtiger_rating_seq",
 	"vtiger_opportunity_type_seq",
 	"vtiger_salutationtype_seq",
 	"vtiger_sales_stage_seq",
 	"vtiger_ticketstatus_seq",
 	"vtiger_ticketpriorities_seq",
 	"vtiger_ticketseverities_seq",
 	"vtiger_ticketcategories_seq",
 	"vtiger_duration_minutes_seq",
 	"vtiger_eventstatus_seq",
 	"vtiger_taskstatus_seq",
 	"vtiger_taskpriority_seq",
 	"vtiger_manufacturer_seq",
 	"vtiger_productcategory_seq",
 	"vtiger_activitytype_seq",
 	"vtiger_currency_seq",
 	"vtiger_faqcategories_seq",
 	"vtiger_usageunit_seq",
 	"vtiger_glacct_seq",
 	"vtiger_quotestage_seq",
 	"vtiger_carrier_seq",
 	"vtiger_taxclass_seq",
 	"vtiger_recurringtype_seq",
 	"vtiger_faqstatus_seq",
 	"vtiger_invoicestatus_seq",
 	"vtiger_postatus_seq",
 	"vtiger_sostatus_seq",
 	"vtiger_visibility_seq",
 	"vtiger_campaigntype_seq",
 	"vtiger_campaignstatus_seq",
 	"vtiger_expectedresponse_seq",
 	"vtiger_status_seq",
 	"vtiger_activity_view_seq",
 	"vtiger_lead_view_seq",
 	"vtiger_date_format_seq",
 	"vtiger_users_seq",
 	"vtiger_role_seq",
 	"vtiger_profile_seq",
 	"vtiger_field_seq",
 	"vtiger_def_org_share_seq",
 	"vtiger_datashare_relatedmodules_seq",
 	"vtiger_relatedlists_seq",
 	"vtiger_notificationscheduler_seq",
 	"vtiger_inventorynotification_seq",
 	"vtiger_currency_info_seq",
 	"vtiger_emailtemplates_seq",
 	"vtiger_inventory_tandc_seq",
 	"vtiger_selectquery_seq",
 	"vtiger_customview_seq",
 	"vtiger_crmentity_seq",
 	"vtiger_seactivityrel_seq",
 	"vtiger_freetags_seq",
 	"vtiger_shippingtaxinfo_seq",
 	"vtiger_inventorytaxinfo_seq"
 	);

     foreach ($sequences as $sequence ) {
 	$log->info( "Creating sequence ".$sequence);
 	$adb->query( "CREATE SEQUENCE ".$sequence." INCREMENT BY 1 NO MAXVALUE NO MINVALUE CACHE 1;");
     }
 }


// TODO HTML
if($success==0)
	die("Error: Tables not created.  Table creation failed.\n");
elseif ($success==1)
	die("Error: Tables partially created.  Table creation failed.\n");

foreach ($modules as $module ) {
	$focus = new $module();
	$focus->create_tables();
}

create_default_users_access();

// create and populate combo tables
require_once('include/PopulateComboValues.php');
$combo = new PopulateComboValues();
$combo->create_tables();
$combo->create_nonpicklist_tables();
//Writing tab data in flat file
create_tab_data_file();
create_parenttab_data_file();

// default report population
require_once('modules/Reports/PopulateReports.php');

// default customview population
require_once('modules/CustomView/PopulateCustomView.php');

// ensure required sequences are created (adodb creates them as needed, but if
// creation occurs within a transaction we get problems
$adb->getUniqueID("vtiger_crmentity");
$adb->getUniqueID("vtiger_seactivityrel");
$adb->getUniqueID("vtiger_freetags");

//Master currency population
//Insert into vtiger_currency vtiger_table
$adb->pquery("insert into vtiger_currency_info values(?,?,?,?,?,?,?,?)", array($adb->getUniqueID("vtiger_currency_info"),$currency_name,$currency_code,$currency_symbol,1,'Active','-11','0'));

// Register All the Events
registerEvents($adb);

// Register All the Entity Methods
registerEntityMethods($adb);

// Populate Default Workflows
populateDefaultWorkflows($adb);

// Populate Links
populateLinks();

// Set Help Information for Fields
setFieldHelpInfo();

// Register Cron Jobs
registerCronTasks();

// Register all the Cron Tasks
function registerCronTasks() {
	include_once 'vtlib/Vtiger/Cron.php';

	Vtiger_Cron::register( 'Workflow', 'cron/modules/com_vtiger_workflow/com_vtiger_workflow.service', 900, 'com_vtiger_workflow', 1, 1, 'Recommended frequency for Workflow is 15 mins');
	Vtiger_Cron::register( 'RecurringInvoice', 'cron/modules/SalesOrder/RecurringInvoice.service', 43200, 'SalesOrder', 1, 2, 'Recommended frequency for RecurringInvoice is 12 hours');
	Vtiger_Cron::register( 'SendReminder', 'cron/SendReminder.service', 900, 'Calendar', 1, 3, 'Recommended frequency for SendReminder is 15 mins');
	Vtiger_Cron::register( 'ScheduleReports', 'cron/modules/Reports/ScheduleReports.service', 900, 'Reports', 1, 4, 'Recommended frequency for ScheduleReports is 15 mins');
	Vtiger_Cron::register( 'MailScanner', 'cron/MailScanner.service', 900, 'Settings', 1, 5, 'Recommended frequency for MailScanner is 15 mins');
}

// Register all the events here
function registerEvents($adb) {
	require_once('include/events/include.inc');
	$em = new VTEventsManager($adb);

	// Registering event for Recurring Invoices
	$em->registerHandler('vtiger.entity.aftersave', 'modules/SalesOrder/RecurringInvoiceHandler.php', 'RecurringInvoiceHandler');

	//Registering Entity Delta handler for before save and after save events of the record to track the field value changes
	$em->registerHandler('vtiger.entity.beforesave', 'data/VTEntityDelta.php', 'VTEntityDelta');
	$em->registerHandler('vtiger.entity.aftersave', 'data/VTEntityDelta.php', 'VTEntityDelta');

	// Workflow manager
	$dependentEventHandlers = array('VTEntityDelta');
	$dependentEventHandlersJson = Zend_Json::encode($dependentEventHandlers);
	$em->registerHandler('vtiger.entity.aftersave', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler',
								'',$dependentEventHandlersJson);

	//Registering events for On modify
	$em->registerHandler('vtiger.entity.afterrestore', 'modules/com_vtiger_workflow/VTEventHandler.inc', 'VTWorkflowEventHandler');

	// Registering event for HelpDesk - To reset from_portal value
	$em->registerHandler('vtiger.entity.aftersave.final', 'modules/HelpDesk/HelpDeskHandler.php', 'HelpDeskHandler');
}

// Register all the entity methods here
function registerEntityMethods($adb) {
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");
	$emm = new VTEntityMethodManager($adb);

	// Registering method for Updating Inventory Stock
	$emm->addEntityMethod("SalesOrder","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating SalesOrder
	$emm->addEntityMethod("Invoice","UpdateInventory","include/InventoryHandler.php","handleInventoryProductRel");//Adding EntityMethod for Updating Products data after creating Invoice

	// Register Entity Method for Customer Portal Login details email notification task
	$emm->addEntityMethod("Contacts","SendPortalLoginDetails","modules/Contacts/ContactsHandler.php","Contacts_sendCustomerPortalLoginDetails");

	// Register Entity Method for Email notification on ticket creation from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOnPortalTicketCreation","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_nofifyOnPortalTicketCreation");

	// Register Entity Method for Email notification on ticket comment from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOnPortalTicketComment","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyOnPortalTicketComment");

	// Register Entity Method for Email notification to Record Owner on ticket change, which is not from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyOwnerOnTicketChange","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyOwnerOnTicketChange");

	// Register Entity Method for Email notification to Related Customer on ticket change, which is not from Customer portal
	$emm->addEntityMethod("HelpDesk","NotifyParentOnTicketChange","modules/HelpDesk/HelpDeskHandler.php","HelpDesk_notifyParentOnTicketChange");
}

function populateDefaultWorkflows($adb) {
	require_once("modules/com_vtiger_workflow/include.inc");
	require_once("modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc");
	require_once("modules/com_vtiger_workflow/VTEntityMethodManager.inc");

	// add default workflow types
	$taskTypes = array();
	$defaultModules = array('include' => array(), 'exclude'=>array());
	$createToDoModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns","Quotes","PurchaseOrder","SalesOrder","Invoice"), 'exclude'=>array("Calendar", "FAQ", "Events"));
	$createEventModules = array('include' => array("Leads","Accounts","Potentials","Contacts","HelpDesk","Campaigns"), 'exclude'=>array("Calendar", "FAQ", "Events"));
		
	$taskTypes[] = array("name"=>"VTEmailTask", "label"=>"Send Mail", "classname"=>"VTEmailTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEmailTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEmailTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTEntityMethodTask", "label"=>"Invoke Custom Function", "classname"=>"VTEntityMethodTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTEntityMethodTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTCreateTodoTask", "label"=>"Create Todo", "classname"=>"VTCreateTodoTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateTodoTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateTodoTask.tpl", "modules"=>$createToDoModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTCreateEventTask", "label"=>"Create Event", "classname"=>"VTCreateEventTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateEventTask.tpl", "modules"=>$createEventModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTUpdateFieldsTask", "label"=>"Update Fields", "classname"=>"VTUpdateFieldsTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTUpdateFieldsTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTUpdateFieldsTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTCreateEntityTask", "label"=>"Create Entity", "classname"=>"VTCreateEntityTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTCreateEntityTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTCreateEntityTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'');
	$taskTypes[] = array("name"=>"VTSMSTask", "label"=>"SMS Task", "classname"=>"VTSMSTask", "classpath"=>"modules/com_vtiger_workflow/tasks/VTSMSTask.inc", "templatepath"=>"com_vtiger_workflow/taskforms/VTSMSTask.tpl", "modules"=>$defaultModules, "sourcemodule"=>'SMSNotifier');
		
	foreach ($taskTypes as $taskType) {
		VTTaskType::registerTaskType($taskType);
	}
	
	// Creating Workflow for Updating Inventory Stock for Invoice
	$vtWorkFlow = new VTWorkflowManager($adb);
	$invWorkFlow = $vtWorkFlow->newWorkFlow("Invoice");
	$invWorkFlow->test = '[{"fieldname":"subject","operation":"does not contain","value":"`!`"}]';
	$invWorkFlow->description = "UpdateInventoryProducts On Every Save";
	$invWorkFlow->defaultworkflow = 1;
	$vtWorkFlow->save($invWorkFlow);

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEntityMethodTask', $invWorkFlow->id);
	$task->active=true;
	$task->methodName = "UpdateInventory";
	$tm->saveTask($task);


	// Creating Workflow for Accounts when Notifyowner is true

	$vtaWorkFlow = new VTWorkflowManager($adb);
	$accWorkFlow = $vtaWorkFlow->newWorkFlow("Accounts");
	$accWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$accWorkFlow->description = "Send Email to user when Notifyowner is True";
	$accWorkFlow->executionCondition=2;
	$accWorkFlow->defaultworkflow = 1;
	$vtaWorkFlow->save($accWorkFlow);
	$id1=$accWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$accWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Account Creation";
	$task->content = "An Account has been assigned to you on vtigerCRM<br>Details of account are :<br><br>".
			"AccountId:".'<b>$account_no</b><br>'."AccountName:".'<b>$accountname</b><br>'."Rating:".'<b>$rating</b><br>'.
			"Industry:".'<b>$industry</b><br>'."AccountType:".'<b>$accounttype</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."Thank You<br>Admin";
	$task->summary="An account has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));

	// Creating Workflow for Contacts when Notifyowner is true

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conWorkFlow = 	$vtcWorkFlow->newWorkFlow("Contacts");
	$conWorkFlow->summary="A contact has been created ";
	$conWorkFlow->executionCondition=2;
	$conWorkFlow->test = '[{"fieldname":"notify_owner","operation":"is","value":"true:boolean"}]';
	$conWorkFlow->description = "Send Email to user when Notifyowner is True";
	$conWorkFlow->defaultworkflow = 1;
	$vtcWorkFlow->save($conWorkFlow);
	$id1=$conWorkFlow->id;
	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conWorkFlow->id);
	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Contact Creation";
	$task->content = "An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>".
			"Contact Id:".'<b>$contact_no</b><br>'."LastName:".'<b>$lastname</b><br>'."FirstName:".'<b>$firstname</b><br>'.
			"Lead Source:".'<b>$leadsource</b><br>'.
			"Department:".'<b>$department</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."Thank You<br>Admin";
	$task->summary="An contact has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));


	// Creating Workflow for Contacts when PortalUser is true

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$conpuWorkFlow = $vtcWorkFlow->newWorkFlow("Contacts");
	$conpuWorkFlow->test = '[{"fieldname":"portal","operation":"is","value":"true:boolean"}]';
	$conpuWorkFlow->description = "Send Email to user when Portal User is True";
	$conpuWorkFlow->executionCondition=2;
	$conpuWorkFlow->defaultworkflow = 1;
	$vtcWorkFlow->save($conpuWorkFlow);
	$id1=$conpuWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$conpuWorkFlow->id);

	$task->active=true;
	$task->methodName = "NotifyOwner";
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Contact Assignment";
	$task->content = "An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>".
			"Contact Id:".'<b>$contact_no</b><br>'."LastName:".'<b>$lastname</b><br>'."FirstName:".'<b>$firstname</b><br>'.
			"Lead Source:".'<b>$leadsource</b><br>'.
			"Department:".'<b>$department</b><br>'.
			"Description:".'<b>$description</b><br><br><br>'."And <b>CustomerPortal Login Details</b> is sent to the " .
			"EmailID :-".'$email<br>'."<br>Thank You<br>Admin";

	$task->summary="An contact has been created ";
	$tm->saveTask($task);
	$adb->pquery("update com_vtiger_workflows set defaultworkflow=? where workflow_id=?",array(1,$id1));

	// Creating Workflow for Potentials

	$vtcWorkFlow = new VTWorkflowManager($adb);
	$potentialWorkFlow = $vtcWorkFlow->newWorkFlow("Potentials");
	$potentialWorkFlow->description = "Send Email to users on Potential creation";
	$potentialWorkFlow->executionCondition=1;
	$potentialWorkFlow->defaultworkflow = 1;
	$vtcWorkFlow->save($potentialWorkFlow);
	$id1=$potentialWorkFlow->id;

	$tm = new VTTaskManager($adb);
	$task = $tm->createTask('VTEmailTask',$potentialWorkFlow->id);

	$task->active=true;
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Regarding Potential Assignment";
	$task->content = "An Potential has been assigned to you on vtigerCRM<br>Details of Potential are :<br><br>".
			"Potential No:".'<b>$potential_no</b><br>'."Potential Name:".'<b>$potentialname</b><br>'.
			"Amount:".'<b>$amount</b><br>'.
			"Expected Close Date:".'<b>$closingdate</b><br>'.
			"Type:".'<b>$opportunity_type</b><br><br><br>'.
			"Description :".'$description<br>'."<br>Thank You<br>Admin";

	$task->summary="An Potential has been created ";
	$tm->saveTask($task);

	$workflowManager = new VTWorkflowManager($adb);
	$taskManager = new VTTaskManager($adb);

	// Contact workflow on creation/modification
	$contactWorkFlow = $workflowManager->newWorkFlow("Contacts");
	$contactWorkFlow->test = '';
	$contactWorkFlow->description = "Workflow for Contact Creation or Modification";
	$contactWorkFlow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$contactWorkFlow->defaultworkflow = 1;
	$workflowManager->save($contactWorkFlow);

	$task = $taskManager->createTask('VTEntityMethodTask', $contactWorkFlow->id);
	$task->active = true;
	$task->summary = 'Email Customer Portal Login Details';
	$task->methodName = "SendPortalLoginDetails";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on creation from Customer Portal
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
	$helpDeskWorkflow->description = "Workflow for Ticket Created from Portal";
	$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_FIRST_SAVE;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	$task->summary = 'Notify Record Owner and the Related Contact when Ticket is created from Portal';
	$task->methodName = "NotifyOnPortalTicketCreation";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on ticket update from Customer Portal
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"true:boolean"}]';
	$helpDeskWorkflow->description = "Workflow for Ticket Updated from Portal";
	$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_MODIFY;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	$task->summary = 'Notify Record Owner when Comment is added to a Ticket from Customer Portal';
	$task->methodName = "NotifyOnPortalTicketComment";
	$taskManager->saveTask($task);

	// Trouble Tickets workflow on ticket change, which is not from Customer Portal - Both Record Owner and Related Customer
	$helpDeskWorkflow = $workflowManager->newWorkFlow("HelpDesk");
	$helpDeskWorkflow->test = '[{"fieldname":"from_portal","operation":"is","value":"false:boolean"}]';
	$helpDeskWorkflow->description = "Workflow for Ticket Change, not from the Portal";
	$helpDeskWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$helpDeskWorkflow->defaultworkflow = 1;
	$workflowManager->save($helpDeskWorkflow);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	$task->summary = 'Notify Record Owner on Ticket Change, which is not done from Portal';
	$task->methodName = "NotifyOwnerOnTicketChange";
	$taskManager->saveTask($task);

	$task = $taskManager->createTask('VTEntityMethodTask', $helpDeskWorkflow->id);
	$task->active = true;
	$task->summary = 'Notify Related Customer on Ticket Change, which is not done from Portal';
	$task->methodName = "NotifyParentOnTicketChange";
	$taskManager->saveTask($task);

	// Events workflow when Send Notification is checked
	$eventsWorkflow = $workflowManager->newWorkFlow("Events");
	$eventsWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
	$eventsWorkflow->description = "Workflow for Events when Send Notification is True";
	$eventsWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$eventsWorkflow->defaultworkflow = 1;
	$workflowManager->save($eventsWorkflow);

	$task = $taskManager->createTask('VTEmailTask', $eventsWorkflow->id);
	$task->active = true;
	$task->summary = 'Send Notification Email to Record Owner';
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Event :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Activity Notification Details:</b><br/>'
					.'Subject             : $subject<br/>'
					.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Status              : $eventstatus <br/>'
					.'Priority            : $taskpriority <br/>'
					.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
											.'$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Location            : $location <br/>'
					.'Description         : $description';
	$taskManager->saveTask($task);

	// Calendar workflow when Send Notification is checked
	$calendarWorkflow = $workflowManager->newWorkFlow("Calendar");
	$calendarWorkflow->test = '[{"fieldname":"sendnotification","operation":"is","value":"true:boolean"}]';
	$calendarWorkflow->description = "Workflow for Calendar Todos when Send Notification is True";
	$calendarWorkflow->executionCondition = VTWorkflowManager::$ON_EVERY_SAVE;
	$calendarWorkflow->defaultworkflow = 1;
	$workflowManager->save($calendarWorkflow);

	$task = $taskManager->createTask('VTEmailTask', $calendarWorkflow->id);
	$task->active = true;
	$task->summary = 'Send Notification Email to Record Owner';
	$task->recepient = "\$(assigned_user_id : (Users) email1)";
	$task->subject = "Task :  \$subject";
	$task->content = '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
					.'<b>Task Notification Details:</b><br/>'
					.'Subject : $subject<br/>'
					.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'End date and time   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
					.'Status              : $taskstatus <br/>'
					.'Priority            : $taskpriority <br/>'
					.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
					.'$(parent_id         : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
					.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
					.'Location            : $location <br/>'
					.'Description         : $description';
	$taskManager->saveTask($task);
}

// Function to populate Links
function populateLinks() {
	include_once('vtlib/Vtiger/Module.php');

	// Links for Accounts module
	$accountInstance = Vtiger_Module::getInstance('Accounts');
	// Detail View Custom link
	$accountInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
	$accountInstance->addLink('DETAILVIEWBASIC', 'LBL_SHOW_ACCOUNT_HIERARCHY', 'index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$');

	$leadInstance = Vtiger_Module::getInstance('Leads');
	$leadInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);

	$contactInstance = Vtiger_Module::getInstance('Contacts');
	$contactInstance->addLink(
		'DETAILVIEWBASIC', 'LBL_ADD_NOTE',
		'index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$',
		'themes/images/bookMark.gif'
	);
}

function setFieldHelpInfo() {
	// Added Help Info for Hours and Days fields of HelpDesk module.
	require_once('vtlib/Vtiger/Module.php');
	$tt_module = Vtiger_Module::getInstance('HelpDesk');
	$field1 = Vtiger_Field::getInstance('hours',$tt_module);
	$field2 = Vtiger_Field::getInstance('days',$tt_module);

	$field1->setHelpInfo('This gives the estimated hours for the Ticket.'.
				'<br>When the same ticket is added to a Service Contract,'.
				'based on the Tracking Unit of the Service Contract,'.
				'Used units is updated whenever a ticket is Closed.');

	$field2->setHelpInfo('This gives the estimated days for the Ticket.'.
				'<br>When the same ticket is added to a Service Contract,'.
				'based on the Tracking Unit of the Service Contract,'.
				'Used units is updated whenever a ticket is Closed.');

	$usersModuleInstance = Vtiger_Module::getInstance('Users');
	$field1 = Vtiger_Field::getInstance('currency_grouping_pattern', $usersModuleInstance);
	$field2 = Vtiger_Field::getInstance('currency_decimal_separator', $usersModuleInstance);
	$field3 = Vtiger_Field::getInstance('currency_grouping_separator', $usersModuleInstance);
	$field4 = Vtiger_Field::getInstance('currency_symbol_placement', $usersModuleInstance);

	$field1->setHelpInfo("<b>Currency - Digit Grouping Pattern</b> <br/><br/>".
								"This pattern specifies the format in which the currency separator will be placed.");
	$field2->setHelpInfo("<b>Currency - Decimal Separator</b> <br/><br/>".
										"Decimal separator specifies the separator to be used to separate ".
										"the fractional values from the whole number part. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.45 <br/>".
										", => 123,45 <br/>".
										"' => 123'45 <br/>".
										"  => 123 45 <br/>".
										"$ => 123$45 <br/>");
	$field3->setHelpInfo("<b>Currency - Grouping Separator</b> <br/><br/>".
										"Grouping separator specifies the separator to be used to group ".
										"the whole number part into hundreds, thousands etc. <br/>".
										"<b>Eg:</b> <br/>".
										". => 123.456.789 <br/>".
										", => 123,456,789 <br/>".
										"' => 123'456'789 <br/>".
										"  => 123 456 789 <br/>".
										"$ => 123$456$789 <br/>");
	$field4->setHelpInfo("<b>Currency - Symbol Placement</b> <br/><br/>".
										"Symbol Placement allows you to configure the position of the ".
										"currency symbol with respect to the currency value.<br/>".
										"<b>Eg:</b> <br/>".
										"$1.0 => $123,456,789.50 <br/>".
										"1.0$ => 123,456,789.50$ <br/>");
}

?>
