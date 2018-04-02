<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class PBXManagerAfterSaveCreateActivity extends VTEventHandler {

	public function handleEvent($eventName, $entityData) {
		global $adb, $current_user;
		$moduleName = $entityData->getModuleName();
		if ($eventName == 'vtiger.entity.aftersave' && isset($_REQUEST['cbcustominfo1']) &&
			in_array($moduleName, array('Accounts','Contacts','Leads','HelpDesk','Potentials'))
		) {
			$act = urldecode($_REQUEST['cbcustominfo1']);
			$act = unserialize($act);
			if (is_array($act) && isset($act['action']) && $act['action']=='asterisk_addToActivityHistory') {
				require_once 'modules/PBXManager/AsteriskUtils.php';
				$callerName = $act['callerName'];
				$callerNumber = $act['callerNumber'];
				$callerType = $act['callerType'];
				$relcrmid = $entityData->getId();
				asterisk_addToActivityHistory($callerName, $callerNumber, $callerType, $adb, $current_user->id, $relcrmid, $act);
			}
		}
	}
}
?>
