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
	public function vtlib_handler($modulename, $event_type) {

		$registerLink = false;

		if ($event_type == 'module.postinstall') {
			$registerLink = true;
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
			$registerLink = false;
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled
			$registerLink = true;
		} elseif ($event_type == 'module.preuninstall') {
			return;
		} elseif ($event_type == 'module.preupdate') {
			return;
		} elseif ($event_type == 'module.postupdate') {
			return;
		}
	}
}
?>