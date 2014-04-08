<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ConfigEditor {
	
	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		
		$registerLink = false; 
		
		if($event_type == 'module.postinstall') {
			$registerLink = true;
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
			$registerLink = false;
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled
			$registerLink = true;
		} else if($event_type == 'module.preuninstall') {
			return;
		} else if($event_type == 'module.preupdate') {
			return;
		} else if($event_type == 'module.postupdate') {
			return;
		}
		
		$displayLabel = 'Configuration Editor';
		
		global $adb;
		if ($registerLink) {
			$blockid = $adb->query_result( 
				$adb->pquery("SELECT blockid FROM vtiger_settings_blocks WHERE label='LBL_OTHER_SETTINGS'",array()),
				0, 'blockid');
			$sequence = (int)$adb->query_result(
				$adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_field WHERE blockid=?",array($blockid)),
				0, 'sequence') + 1;
			$fieldid = $adb->getUniqueId('vtiger_settings_field');
			$adb->pquery("INSERT INTO vtiger_settings_field (fieldid,blockid,sequence,name,iconpath,description,linkto)
				VALUES (?,?,?,?,?,?,?)", array($fieldid, $blockid,$sequence,$displayLabel,'migrate.gif','Update configuration file of the application', 'index.php?module=ConfigEditor&action=index'));
		} else {
			$adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array($displayLabel));
		}
	}
}

?>