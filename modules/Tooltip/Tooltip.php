<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
* 
 ********************************************************************************/


 
class Tooltip {
 	
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
 					
		require_once('include/utils/utils.php');			
		require_once('vtlib/Vtiger/Module.php');
		global $adb,$mod_strings;
 		
 		if($eventType == 'module.postinstall') {			
		
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
		
			$name = 'LBL_TOOLTIP_MANAGEMENT';
			$blockname = 'LBL_MODULE_MANAGER';
			$icon = 'quickview.png';
			$description = 'LBL_TOOLTIP_MANAGEMENT_DESCRIPTION';
			$links = 'index.php?module=Tooltip&action=QuickView&parenttab=Settings';
		
			$adb->query("INSERT INTO vtiger_settings_field (fieldid, blockid, name, iconpath, description, linkto) 
							VALUES (".$adb->getUniqueID('vtiger_settings_field').", ".getSettingsBlockId($blockname).", '$name', '$icon', '$description', '$links')");
 		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
			$moduleInstance = Vtiger_Module::getInstance('Tooltip');
			$moduleInstance->deleteLink('HEADERSCRIPT','ToolTip_HeaderScript','modules/Tooltip/TooltipHeaderScript.js');
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		$moduleInstance = Vtiger_Module::getInstance('Tooltip');
			$moduleInstance->addLink('HEADERSCRIPT','ToolTip_HeaderScript','modules/Tooltip/TooltipHeaderScript.js');
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}
}
?>