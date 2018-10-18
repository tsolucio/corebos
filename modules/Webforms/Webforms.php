<?php
/* +********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ****************************************************************************** */
require_once 'modules/Webforms/model/WebformsModel.php';
require_once 'include/Webservices/DescribeObject.php';

class Webforms {

	public $LBL_WEBFORMS='Webforms';

	// Cache to speed up describe information store
	protected static $moduleDescribeCache = array();

	public function vtlib_handler($moduleName, $eventType) {
		require_once 'include/utils/utils.php';
		global $adb;

		if ($eventType == 'module.postinstall') {
			// Mark the module as Standard module
			// Mark the module as Standard module
			$this->updateSettings();
			$adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($this->LBL_WEBFORMS));
		} elseif ($eventType == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
			global $log,$adb;
			$adb->pquery('UPDATE vtiger_settings_field SET active= 1  WHERE  name= ?', array($this->LBL_WEBFORMS));
		} elseif ($eventType == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
			global $log,$adb;
			$adb->pquery('UPDATE vtiger_settings_field SET active= 0  WHERE  name= ?', array($this->LBL_WEBFORMS));
		} elseif ($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} elseif ($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} elseif ($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
			$this->updateSettings();
		}
	}

	public function updateSettings() {
		global $adb;

		$fieldid = $adb->getUniqueID('vtiger_settings_field');
		$blockid = getSettingsBlockId('LBL_OTHER_SETTINGS');
		$seq_res = $adb->pquery('SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid = ?', array($blockid));
		if ($adb->num_rows($seq_res) > 0) {
			$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
			if ($cur_seq != null) {
				$seq = $cur_seq + 1;
			}
		}

		$result=$adb->pquery('SELECT 1 FROM vtiger_settings_field WHERE name=?', array($this->LBL_WEBFORMS));
		if (!$adb->num_rows($result)) {
			$desc = 'Allows you to manage Webforms';
			$url = 'index.php?module=Webforms&action=index&parenttab=Settings';
			$adb->pquery(
				'INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) VALUES (?,?,?,?,?,?,?)',
				array($fieldid, $blockid, $this->LBL_WEBFORMS , 'modules/Webforms/img/Webform.png', $desc, $url, $seq)
			);
		}
	}

	public static function checkAdminAccess($user) {
		if (is_admin($user) || isPermitted('Webforms', '')=='yes') {
			return;
		}
		require_once 'Smarty_setup.php';
		$smarty = new vtigerCRM_Smarty();
		global $app_strings;
		$smarty->assign('APP', $app_strings);
		$smarty->display('modules/Vtiger/OperationNotPermitted.tpl');
		exit;
	}

	public static function getModuleDescribe($module) {
		if (!isset(self::$moduleDescribeCache[$module])) {
			global $current_user;
			self::$moduleDescribeCache[$module] = vtws_describe($module, $current_user);
		}
		return self::$moduleDescribeCache[$module];
	}

	public static function getFieldInfo($module, $fieldname) {
		$describe = self::getModuleDescribe($module);
		foreach ($describe['fields'] as $fieldInfo) {
			if ($fieldInfo['name'] == $fieldname) {
				return $fieldInfo;
			}
		}
		return false;
	}

	public static function getFieldInfos($module) {
		$describe = self::getModuleDescribe($module);
		foreach ($describe['fields'] as $index => $fieldInfo) {
			if ($fieldInfo['name'] == 'id') {
				unset($describe['fields'][$index]);
			}
		}
		return $describe['fields'];
	}
}
?>
