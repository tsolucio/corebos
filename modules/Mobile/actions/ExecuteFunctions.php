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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once __DIR__ . '/../api/ws/Controller.php';
include_once __DIR__ . '/../api/ws/Utils.php';
include_once 'include/Webservices/Query.php';

global $adb, $log;

class crmtogo_UI_ExecuteFunctions extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		global  $current_language, $current_user;

		if (empty($current_language)) {
			$current_language = crmtogo_WS_Controller::sessionGet('language');
		}
		$response = new crmtogo_API_Response();
		$functiontocall = vtlib_purify($request->get('functiontocall'));

		switch ($functiontocall) {
			case 'getFieldValuesFromRecord':
				$ret = array();
				$crmid = vtlib_purify($request->get('getFieldValuesFrom'));
				if (!empty($crmid)) {
					$module = getSalesEntityType($crmid);
					$fields = vtlib_purify($request->get('getTheseFields'));
					$fields = explode(',', $fields);

					$focus = CRMEntity::getInstance($module);
					$focus->id = $crmid;
					$focus->retrieve_entity_info($focus->id, $module);
					$handler = vtws_getModuleHandlerFromName($module, $current_user);
					$meta = $handler->getMeta();
					$focus->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focus->column_fields, $meta);

					foreach ($fields as $field) {
						$ret[$field]=$focus->column_fields[$field];
					}
				}
				break;
			case 'getModuleWebseriviceID':
				$wsmod = vtlib_purify($request->get('wsmodule'));
				if (!empty($wsmod)) {
					$ret = vtws_getEntityId($wsmod);
				} else {
					$ret = '';
				}
				break;
			case 'detectModulenameFromRecordId':
				$ret = array();
				$wsrecordid = vtlib_purify($request->get('wsrecordid'));
				$ret['name'] = crmtogo_WS_Utils::detectModulenameFromRecordId($wsrecordid);
				break;
			case 'getTranslatedStrings':
				global $currentModule;
				$i18nm = empty($request->get('i18nmodule')) ? $currentModule : vtlib_purify($request->get('i18nmodule'));
				$tkeys = vtlib_purify($request->get('tkeys'));
				$tkeys = explode(';', $tkeys);
				$ret = array();
				foreach ($tkeys as $tr) {
					$ret[$tr] = getTranslatedString($tr, $i18nm);
				}
				break;
			case 'ismoduleactive':
			default:
				$mod = vtlib_purify($request->get('checkmodule'));
				$rdo = vtlib_isModuleActive($mod);
				$ret = array('isactive'=>$rdo);
				break;
		}

		$sResult = json_encode($ret);
		$response->setResult($sResult);
		return $response;
	}
}
?>
