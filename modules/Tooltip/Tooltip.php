<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

class Tooltip {

	/**
	* Invoked when special actions are performed on the module.
	* @param string Module name
	* @param string Event Type
	*/
	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		require_once 'vtlib/Vtiger/Module.php';
		global $adb;

		if ($eventType == 'module.postinstall') {
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
			$name = 'LBL_TOOLTIP_MANAGEMENT';
			$blockname = 'LBL_MODULE_MANAGER';
			$icon = 'quickview.png';
			$description = 'LBL_TOOLTIP_MANAGEMENT_DESCRIPTION';
			$links = 'index.php?module=Tooltip&action=QuickView';
			$adb->pquery(
				'INSERT INTO vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto) VALUES (?,?,?,?,?,?)',
				array($adb->getUniqueID('vtiger_settings_field'), getSettingsBlockId($blockname), $name, $icon, $description, $links)
			);
		} elseif ($eventType == 'module.disabled') {
		// Handle actions when this module is disabled.
			$moduleInstance = Vtiger_Module::getInstance('Tooltip');
			$moduleInstance->deleteLink('HEADERSCRIPT', 'ToolTip_HeaderScript', 'modules/Tooltip/TooltipHeaderScript.js');
		} elseif ($eventType == 'module.enabled') {
		// Handle actions when this module is enabled.
			$moduleInstance = Vtiger_Module::getInstance('Tooltip');
			$moduleInstance->addLink('HEADERSCRIPT', 'ToolTip_HeaderScript', 'modules/Tooltip/TooltipHeaderScript.js');
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