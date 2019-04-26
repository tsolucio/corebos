<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/

class RecycleBin {

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		global $adb;

		if ($eventType == 'module.postinstall') {
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
		} elseif ($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} elseif ($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} elseif ($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
	}
}
?>
