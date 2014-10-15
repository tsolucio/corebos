<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/logging.php');
require_once('modules/Users/Users.php');
require_once('include/database/PearDatabase.php');
global $adb ,$mod_strings, $current_user;

$local_log =& LoggerManager::getLogger('UsersAjax');
$ajaxaction = vtlib_purify($_REQUEST["ajxaction"]);
if($ajaxaction == "DETAILVIEW")
{
	$userid = vtlib_purify($_REQUEST["recordid"]);
	$tablename = vtlib_purify($_REQUEST["tableName"]);
	$fieldname = vtlib_purify($_REQUEST["fldName"]);
	$fieldvalue = utf8RawUrlDecode(vtlib_purify($_REQUEST["fieldValue"])); 
	if(empty($_SESSION['Users_FORM_TOKEN']) || $_SESSION['Users_FORM_TOKEN'] !== (int)$_REQUEST['form_token'] ||
		(!is_admin($current_user) && $current_user->id != $userid)) {
		echo ":#:ERR".($app_strings['LBL_PERMISSION']);
		die;
	}
	if($userid != "")
	{
		$userObj = new Users();
		$userObj->retrieve_entity_info($userid,"Users");
		$userObj->column_fields[$fieldname] = $fieldvalue;

		if($fieldname=='asterisk_extension' and trim($fieldvalue)!=''){
			$query = "select 1 from vtiger_asteriskextensions
                     inner join vtiger_users on vtiger_users.id=vtiger_asteriskextensions.userid
                     where status='Active' and asterisk_extension =? and vtiger_users.id!=?";
			$params = array(trim($fieldvalue),$userid);
			
			$result = $adb->pquery($query, $params);
			if($adb->num_rows($result) > 0)
			{
				echo ":#:ERR".$mod_strings['LBL_ASTERISKEXTENSIONS_EXIST'];
				return false;
			}
		}
		if($fieldname == 'internal_mailer'){
			
			if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $userObj->column_fields['internal_mailer'])
				$_SESSION['internal_mailer'] = $userObj->column_fields['internal_mailer'];
		}
		$userObj->id = $userid;
		$userObj->mode = "edit";
		$userObj->homeorder_array[] = 'Tag Cloud';
		$homeStuffOrder = $userObj->getHomeStuffOrder($userid);
		foreach ($homeStuffOrder as $widget=>$visible) {
			$_REQUEST[$widget] = $visible;
		}
		$_REQUEST['tagcloudview'] = $homeStuffOrder['Tag Cloud'];
		$userObj->save("Users");
		if($userObj->id != "")
		{
			echo ":#:SUCCESS";
		}else
		{
			echo ":#:FAILURE";
		}   
	}else
	{
		echo ":#:FAILURE";
	}
}
?>