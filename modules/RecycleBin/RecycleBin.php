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
	public $moduleIcon = array('library' => 'utility', 'containerClass' => 'slds-icon_container slds-icon-standard-account', 'class' => 'slds-icon', 'icon'=>'undelete');

	/**
	* Invoked when special actions are performed on the module.
	* @param string Module name
	* @param string Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		global $adb;

		if ($eventType == 'module.postinstall') {
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
		} elseif ($eventType == 'module.disabled') {
		// Handle actions when this module is disabled.
		} elseif ($eventType == 'module.enabled') {
		// Handle actions when this module is enabled.
		} elseif ($eventType == 'module.preuninstall') {
		// Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
		// Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
		// Handle actions after this module is updated.
		}
	}
}
?>
