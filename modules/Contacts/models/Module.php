<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_Module_Model {

	public static function removeSync($module, $id) {
		$db = PearDatabase::getInstance();
		$db->pquery('DELETE FROM its4you_googlesync4you_access WHERE service = ? AND userid = ?', array($module, $id));
	}

	/**
	 * Function to delete google synchronization completely. Deletes all mapping information stored.
	 * @param <string> $module - Module Name
	 * @param <integer> $user - User Id
	 */
	public static function deleteSync($module, $user) {
		if ($module == 'Contacts' || $module == 'Calendar') {
			$name = 'Vtiger_Google'.$module;
		} else {
			return;
		}
		$db = PearDatabase::getInstance();
		$db->pquery('DELETE FROM its4you_googlesync4you_access WHERE service = ? AND userid = ?', array('Google'.$module, $user));
		$db->pquery('DELETE FROM vtiger_google_sync WHERE googlemodule = ? AND user = ?', array($module, $user));

		$result = $db->pquery('SELECT stateencodedvalues FROM vtiger_wsapp_sync_state WHERE name = ? AND userid = ?', array($name, $user));
		$stateValuesJson = $db->query_result($result, 0, 'stateencodedvalues');
		$stateValues = json_decode(decode_html($stateValuesJson), true);
		$appKey = $stateValues['synctrackerid'];

		$result = $db->pquery('SELECT appid FROM vtiger_wsapp WHERE appkey = ?', array($appKey));
		$appId = $db->query_result($result, 0, 'appid');

		$db->pquery('DELETE FROM vtiger_wsapp_recordmapping WHERE appid = ?', array($appId));
		$db->pquery('DELETE FROM vtiger_wsapp WHERE appid = ?', array($appId));
		$db->pquery('DELETE FROM vtiger_wsapp_sync_state WHERE name = ? AND userid = ?', array($name, $user));
		if ($module == 'Contacts') {
			$db->pquery('DELETE FROM vtiger_google_sync_settings WHERE user = ?', array($user));
			$db->pquery('DELETE FROM vtiger_google_sync_fieldmapping WHERE user = ?', array($user));
		}
		return;
	}

	/*
	* Function to get supported utility actions for a module
	*/
	public function getUtilityActionsNames() {
		return array();
	}
}
?>