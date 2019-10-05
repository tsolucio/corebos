<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_SaveSettings_Action {

	public function process($request) {
		$sourceModule = $request['sourcemodule'];
		$fieldMapping = $request['fieldmapping'];
		Google_Utils_Helper::saveSettings($request);
		Google_Utils_Helper::saveFieldMappings($sourceModule, $fieldMapping);
	}
}
?>