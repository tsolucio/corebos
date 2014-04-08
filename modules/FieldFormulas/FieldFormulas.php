<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *******************************************************************************/
 
class FieldFormulas {
 	
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
 	function vtlib_handler($moduleName, $eventType) {
 					
		require_once('include/utils/utils.php');			
		global $adb;
		
 		if($eventType == 'module.postinstall') {		
			$fieldid = $adb->getUniqueID('vtiger_settings_field');
			$blockid = getSettingsBlockId('LBL_MODULE_MANAGER');
			
			$seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field");
			$seq = 1;
			if ($adb->num_rows($seq_res) > 0) {
				$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
				if ($cur_seq != null)	$seq = $cur_seq + 1;
			}
			
			$adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
				VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_FIELDFORMULAS', 'modules/FieldFormulas/resources/FieldFormulas.png', 'LBL_FIELDFORMULAS_DESCRIPTION', 'index.php?module=FieldFormulas&action=index&parenttab=Settings', $seq));
			
			$tabid = getTabid('FieldFormulas');
			if(isset($tabid) && $tabid!='') {
				$adb->pquery('DELETE FROM vtiger_profile2tab WHERE tabid = ?', array($tabid));
			}
			
			// Mark the module as Standard module
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));
			
		} else if($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('VTFieldFormulasEventHandler');
			
		} else if($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('VTFieldFormulasEventHandler');

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