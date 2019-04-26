<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
require_once 'include/utils/utils.php';
require_once 'modules/PickList/PickListUtils.php';

$values = vtlib_purify($_REQUEST['values']);
$val = json_decode($values, true);
HomeDefaultWidgets::saveSelected($val);

class HomeDefaultWidgets {
	public static function saveSelected($values) {
		global $adb, $current_user;
		// To show the default Homestuff on the the Home Page
		$userId = $current_user->id;
		$query = 'update vtiger_homestuff,vtiger_homedefault set vtiger_homestuff.visible=0
			where vtiger_homestuff.stuffid=vtiger_homedefault.stuffid and vtiger_homestuff.userid=? and vtiger_homedefault.hometype=?';
		for ($i = 0; $i < count($values); $i++) {
			if ($values[$i] != null) {
				$adb->pquery($query, array($userId, $values[$i]));
			}
		}
	}
}
?>