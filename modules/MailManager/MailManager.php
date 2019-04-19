<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'include/Webservices/Query.php';

class MailManager {

	public static function updateMailAssociation($mailuid, $emailid, $crmid) {
		global $adb;
		$adb->pquery('INSERT INTO vtiger_mailmanager_mailrel (mailuid, emailid, crmid) VALUES (?,?,?)', array($mailuid, $emailid, $crmid));
	}

	public static function lookupMailInVtiger($searchTerm, $user) {
		$handler = vtws_getModuleHandlerFromName('Emails', $user);
		$meta = $handler->getMeta();
		$moduleFields = $meta->getModuleFields();
		$parentIdFieldInstance = $moduleFields['parent_id'];
		$referenceModules = $parentIdFieldInstance->getReferenceList();

		$filteredResult = array();
		foreach ($referenceModules as $referenceModule) {
			$referenceModuleHandler = vtws_getModuleHandlerFromName($referenceModule, $user);
			$referenceModuleMeta = $referenceModuleHandler->getMeta();
			$referenceModuleEmailFields = $referenceModuleMeta->getEmailFields();
			$referenceModuleEntityFields = $referenceModuleMeta->getNameFields();
			$referenceModuleEntityFieldsArray = explode(',', $referenceModuleEntityFields);
			$searchFieldList = array_merge($referenceModuleEmailFields, $referenceModuleEntityFieldsArray);
			if (!empty($searchFieldList) && !empty($referenceModuleEmailFields)) {
				$searchFieldListString = implode(',', $referenceModuleEmailFields);
				$where = null;
				for ($i=0; $i<count($searchFieldList); $i++) {
					if ($i == count($searchFieldList) - 1) {
						$where .= sprintf($searchFieldList[$i]." like '%s'", $searchTerm);
					} else {
						$where .= sprintf($searchFieldList[$i]." like '%s' or ", $searchTerm);
					}
				}
				if (!empty($where)) {
					$where = "WHERE $where";
				}
				$result = vtws_query("select $searchFieldListString from $referenceModule $where;", $user);
				foreach ($result as $record) {
					foreach ($searchFieldList as $searchField) {
						if (!empty($record[$searchField])) {
							$filteredResult[] = array(
								'id' => $record[$searchField],
								'name' => $record[$searchField].' - '.getTranslatedString($referenceModule, $referenceModule),
								'record' => $record['id'],
								'module' => $referenceModule
							);
						}
					}
				}
			}
		}
		return $filteredResult;
	}

	public static function lookupMailAssociation($mailuid) {
		global $adb;

		// Mail could get associated with two-or-more records if they get deleted after linking.
		$result = $adb->pquery(
			'SELECT vtiger_mailmanager_mailrel.*
				FROM vtiger_mailmanager_mailrel
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_mailmanager_mailrel.crmid
					AND vtiger_crmentity.deleted=0 AND vtiger_mailmanager_mailrel.mailuid=?
				LIMIT 1',
			array(decode_html($mailuid))
		);
		if ($adb->num_rows($result)) {
			return $adb->fetch_array($result);
		}
		return false;
	}

	public static function isEMailAssociatedWithCRMID($mailuid, $crmid) {
		global $adb;
		$result = $adb->pquery(
			'SELECT vtiger_mailmanager_mailrel.*
				FROM vtiger_mailmanager_mailrel
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_mailmanager_mailrel.crmid
					AND vtiger_crmentity.deleted=0 AND vtiger_mailmanager_mailrel.mailuid=? and vtiger_mailmanager_mailrel.crmid=?
				LIMIT 1',
			array(decode_html($mailuid),$crmid)
		);
		return ($adb->num_rows($result)>0);
	}

	public static function checkModuleWriteAccessForCurrentUser($module) {
		return (isPermitted($module, 'EditView') == 'yes' && vtlib_isModuleActive($module));
	}

	public static function checkModuleCreateAccessForCurrentUser($module) {
		return (isPermitted($module, 'CreateView') == 'yes' && vtlib_isModuleActive($module));
	}

	/**
	 * function to check the read access for the current user
	 * @global Users Instance $current_user
	 * @param String $module - Name of the module
	 * @return Boolean
	 */
	public static function checkModuleReadAccessForCurrentUser($module) {
		return (isPermitted($module, 'DetailView') == 'yes' && vtlib_isModuleActive($module));
	}

	/**
	 * Invoked when special actions are performed on the module.
	 * @param String $modulename - Module name
	 * @param String $event_type - Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	public function vtlib_handler($modulename, $event_type) {
		if ($event_type == 'module.postinstall') {
			// TODO Handle actions when this module is installed.
		} elseif ($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} elseif ($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
		} elseif ($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($event_type == 'module.postupdate') {
			// TODO Handle actions when this module is updated.
		}
	}
}
?>
