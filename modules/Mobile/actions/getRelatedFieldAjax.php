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
include_once __DIR__ . '/../api/ws/Controller.php';
include_once __DIR__ . '/../api/ws/Utils.php';
include_once __DIR__ . '/../views/models/SearchFilter.php';
include_once 'include/Webservices/Query.php';

class crmtogo_UI_getRelatedFieldAjax extends crmtogo_WS_Controller {

	public function process(crmtogo_API_Request $request) {
		global $current_language,$current_user,$adb;
		if (empty($current_language)) {
			$current_language = crmtogo_WS_Controller::sessionGet('language');
		}
		$response = new crmtogo_API_Response();
		$searchvalue = vtlib_purify($request->get('searchvalue'));
		$module = vtlib_purify($request->get('modulename'));
		$parentselector = vtlib_purify($request->get('parentselector'));
		$parentid=  str_replace('_selector', '', $parentselector);

		$searchresult = array();
		//HelpDesk special case with Product.
		if ($module == 'HelpDesk' && $parentid == 'product_id') {
			$result = $adb->pquery('SELECT modulename,fieldname FROM vtiger_entityname WHERE entityidcolumn = ?', array($parentid));
			$modulename = $adb->query_result($result, 0, 'modulename');
			$fieldname = $adb->query_result($result, 0, 'fieldname');
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$limit = $config['NavigationLimit'];
			$searchqueryresult = vtws_query('SELECT '.$fieldname.' FROM '.$modulename.' WHERE '.$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.';', $current_user);
			for ($i=0; $i<count($searchqueryresult); $i++) {
				$searchresult[] = array($searchqueryresult[$i]['id'],decode_html(getTranslatedString($modulename).' :: '.$searchqueryresult[$i][$fieldname]));
			}
		} elseif ($parentid == 'account_id' && ($module == 'Contacts' || $module == 'Accounts')) {  //Support to uitype 51
			$result = $adb->pquery("SELECT fieldname FROM vtiger_entityname WHERE entityidcolumn = ? AND modulename = 'Accounts'", array($parentid));
			$modulename = 'Accounts';
			$fieldname = $adb->query_result($result, 0, 'fieldname');
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$limit = $config['NavigationLimit'];
			$searchqueryresult = vtws_query('SELECT '.$fieldname.' FROM '.$modulename.' WHERE '.$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.';', $current_user);
			for ($i=0; $i<count($searchqueryresult); $i++) {
				$searchresult[] = array($searchqueryresult[$i]['id'],decode_html(getTranslatedString($modulename).' :: '.$searchqueryresult[$i][$fieldname]));
			}
		}
		//get relmodule
		$res_fmrel = $adb->pquery(
			'SELECT relmodule
				FROM `vtiger_fieldmodulerel`
				INNER JOIN vtiger_field ON vtiger_field.fieldid = vtiger_fieldmodulerel.fieldid
				WHERE module = ? AND fieldname = ?
				ORDER BY vtiger_fieldmodulerel.sequence ASC',
			array($module,$parentid)
		);
		// get module fields
		$res_index = 0;
		$query = 'SELECT fieldname FROM vtiger_entityname WHERE modulename = ?';
		for ($i = 0; $i<$adb->num_rows($res_fmrel); $i++) {
			$modulename = $adb->query_result($res_fmrel, $i, 'relmodule');
			$result = $adb->pquery($query, array($modulename));
			$fieldname = $adb->query_result($result, 0, 'fieldname');
			$config = crmtogo_WS_Controller::getUserConfigSettings();
			$limit = $config['NavigationLimit'];
			if (vtlib_isModuleActive($modulename)) {
			//START DATABASE SEARCH
				switch ($modulename) {
					case 'Contacts':
					case 'Leads':
						$searchqueryresult = vtws_query(
							'SELECT firstname, lastname FROM '.$modulename." WHERE lastname like '%".$searchvalue."%' OR firstname like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.decode_html($searchqueryresult[$i]['lastname']).', '.decode_html($searchqueryresult[$res_index]['firstname']))
							);
						}
						break;
					case 'Users':
						$searchqueryresult = vtws_query(
							'SELECT first_name, last_name FROM '.$modulename." WHERE last_name like '%".$searchvalue."%' OR first_name like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.decode_html($searchqueryresult[$i]['last_name']).', '.decode_html($searchqueryresult[$res_index]['first_name']))
							);
						}
						break;
					case 'HelpDesk':
						if ($fieldname == 'title') {
							$fieldname = 'ticket_title';
						}
						$searchqueryresult = vtws_query(
							'SELECT '.$fieldname.' FROM '.$modulename.' WHERE '.$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.$searchqueryresult[$res_index][$fieldname])
							);
							$res_index++;
						}
						break;
					case 'Documents':
						if ($fieldname == 'title') {
							$fieldname = 'notes_title';
						}
						$searchqueryresult = vtws_query(
							'SELECT '.$fieldname.' FROM '.$modulename.' WHERE '.$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.$searchqueryresult[$res_index][$fieldname])
							);
							$res_index++;
						}
						break;
					case 'CobroPago':
						$searchqueryresult = vtws_query(
							'SELECT reference, cyp_no FROM '.$modulename." WHERE reference like '%".$searchvalue."%' OR cyp_no like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.decode_html($searchqueryresult[$i]['reference']).', '.decode_html($searchqueryresult[$res_index]['cyp_no']))
							);
						}
						break;
					default:
						$searchqueryresult = vtws_query(
							'SELECT '.$fieldname.' FROM '.$modulename.' WHERE '.$fieldname." like '%".$searchvalue."%' LIMIT ".$limit.';',
							$current_user
						);
						for ($j=0; $j<count($searchqueryresult); $j++) {
							$searchresult[] = array(
								$searchqueryresult[$res_index]['id'],
								decode_html(getTranslatedString($modulename).' :: '.$searchqueryresult[$res_index][$fieldname])
							);
							$res_index++;
						}
						break;
				}
			}
		}
		if (is_null($searchresult)) {
			$sResult = '';
		} else {
			$sResult = $searchresult;
		}
		$sResult = json_encode($sResult);
		$response->setResult($sResult);
		return $response;
	}
}
?>
