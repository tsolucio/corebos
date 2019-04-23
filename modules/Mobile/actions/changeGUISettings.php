<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
include_once __DIR__ . '/../api/ws/Controller.php';

class crmtogo_UI_ChangeSettings extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		global $current_user,$adb;
		$adb = PearDatabase::getInstance();
		$response = new crmtogo_API_Response();
		$settings_operation = vtlib_purify($request->get('operation'));
		$current_user = $this->getActiveUser();
		if ($settings_operation == 'changeorder') {
			$idsInOrder = vtlib_purify($request->get('idsInOrder'));
			//ignore first (header) entry
			array_shift($idsInOrder);
			$query = 'UPDATE berli_crmtogo_modules SET order_num = ? WHERE crmtogo_user = ? AND crmtogo_module = ?';
			$k=0;
			foreach ($idsInOrder as $modulename) {
				$adb->pquery($query, array($k, $current_user->id, $modulename));
				$k=$k+1;
			}
			$response->setResult(json_encode('OK'));
		} elseif ($settings_operation == 'changenavi') {
			$navi_limit = vtlib_purify($request->get('sliderVar'));
			$adb->pquery('UPDATE berli_crmtogo_config SET navi_limit = ? where crmtogouser =?', array($navi_limit, $current_user->id));
			$response->setResult(json_encode('OK'));
		} elseif ($settings_operation == 'changetheme') {
			$theme = vtlib_purify($request->get('theme'));
			$adb->pquery('UPDATE berli_crmtogo_config SET theme_color = ? where crmtogouser =?', array($theme,$current_user->id));
			$response->setResult(json_encode('OK'));
		} elseif ($settings_operation == 'changemodule') {
			$moduleid = vtlib_purify($request->get('moduleid'));
			$checkvalue = vtlib_purify($request->get('checkvalue'));
			if ($checkvalue=='true') {
				$moduleactive = 1;
			} else {
				$moduleactive = 0;
			}
			$module_info = explode('_', $moduleid);
			$adb->pquery(
				'UPDATE berli_crmtogo_modules SET crmtogo_active = ? where crmtogo_module=? and crmtogo_user=?',
				array($moduleactive, $module_info[1], $current_user->id)
			);
			$response->setResult(json_encode('OK'));
		} else {
			$response->setResult('ERROR');
		}
		return $response;
	}
}
?>