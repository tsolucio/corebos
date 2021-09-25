<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 *************************************************************************************************
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
include_once 'vtlib/Vtiger/Link.php';
global $adb, $log, $current_user;

$functiontocall = vtlib_purify($_REQUEST['functiontocall']);

switch ($functiontocall) {
	case 'getFieldAutocomplete':
		include_once 'include/Webservices/CustomerPortalWS.php';
		$searchinmodule = vtlib_purify($_REQUEST['searchinmodule']);
		$fields = vtlib_purify($_REQUEST['fields']);
		$returnfields = vtlib_purify($_REQUEST['returnfields']);
		$limit = vtlib_purify($_REQUEST['limit']);
		$filter = vtlib_purify($_REQUEST['filter']);
		if (is_array($filter)) {
			// Filter array format looks like this:
			/**************************************
			[filter] => Array(
				[logic] => and
				[filters] => Array(
					[0] => Array(
						[value] => {value to search}
						[operator] => startswith
						[field] => crmname
						[ignoreCase] => true
					)
				)
			)
			***************************************/
			$term = $filter['filters'][0]['value'];
			$op = isset($filter['filters'][0]['operator']) ? $filter['filters'][0]['operator'] : 'startswith';
		} else {
			$term = vtlib_purify($_REQUEST['term']);
			$op = empty($filter) ? 'startswith' : $filter;
		}
		$retvals = getFieldAutocomplete($term, $op, $searchinmodule, $fields, $returnfields, $limit, $current_user);
		$ret = array();
		foreach ($retvals as $value) {
			$ret[] = array('crmid'=>$value['crmid'],'crmname'=>implode(',', $value['crmfields']));
		}
		break;
	case 'getReferenceAutocomplete':
		include_once 'include/Webservices/CustomerPortalWS.php';
		$searchinmodule = vtlib_purify($_REQUEST['searchinmodule']);
		$limit = vtlib_purify($_REQUEST['limit']);
		$filter = vtlib_purify($_REQUEST['filter']);
		if (is_array($filter)) {
			$term = $filter['filters'][0]['value'];
			$op = isset($filter['filters'][0]['operator']) ? $filter['filters'][0]['operator'] : 'startswith';
		} else {
			$term = vtlib_purify($_REQUEST['term']);
			$op = empty($filter) ? 'startswith' : $filter;
		}
		$ret = getReferenceAutocomplete($term, $op, $searchinmodule, $limit, $current_user);
		break;
	case 'getProductServiceAutocomplete':
		include_once 'include/Webservices/CustomerPortalWS.php';
		$limit =  isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 5;
		$ret = getProductServiceAutocomplete($_REQUEST['term'], array(), $limit);
		break;
	case 'getFieldValuesFromRecord':
		$ret = array();
		$crmid = vtlib_purify($_REQUEST['getFieldValuesFrom']);
		if (!empty($crmid)) {
			$module = getSalesEntityType($crmid);
			$fields = vtlib_purify($_REQUEST['getTheseFields']);
			$fields = explode(',', $fields);
			$queryGenerator = new QueryGenerator($module, $current_user);
			$queryGenerator->setFields($fields);
			$queryGenerator->addCondition('id', $crmid, 'e');
			$query = $queryGenerator->getQuery();
			$queryres=$adb->pquery($query, array());
			if ($adb->num_rows($queryres)>0) {
				$col=0;
				foreach ($fields as $field) {
					$ret[$field]=$adb->query_result($queryres, 0, $col++);
				}
			}
		}
		break;
	case 'getMergedDescription':
		$tpl = vtlib_purify($_REQUEST['template']);
		$crmid = vtlib_purify($_REQUEST['crmid']);
		$ret = '';
		if (!empty($tpl) && !empty($crmid)) {
			$ret = getMergedDescription(urldecode($tpl), $crmid, 0);
			$searchModule = (1 == GlobalVariable::getVariable('Application_B2B', '1')) ? 'Accounts' : 'Contacts';
			$relid = getRelatedAccountContact($crmid, $searchModule);
			if (!empty($relid)) {
				$ret = getMergedDescription($ret, $relid, $searchModule);
			}
		}
		break;
	case 'getEmailTemplateDetails':
		$emltplid = vtlib_purify($_REQUEST['templateid']);
		$emltpl = getTemplateDetails($emltplid);
		$ret = array();
		if (!empty($emltpl)) {
			$ret['subject'] = $emltpl[2];
			$ret['body'] = $emltpl[1];
			$ret['from_email'] = $emltpl[3];
		}
		break;
	case 'exportUserComments':
		$recordid = vtlib_purify($_REQUEST['record']);
		$module = vtlib_purify($_REQUEST['module']);
		include_once 'include/utils/ExportUtils.php';
		if (GlobalVariable::getVariable('ModComments_Export_Format', 'CSV', $module) == 'CSV') {
			header('Content-Disposition:attachment;filename="Comments'.$recordid.'.csv"');
			header('Content-Type:text/csv;charset=UTF-8');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: post-check=0, pre-check=0', false);
			exportUserCommentsForModule($module, $recordid, 'CSV');
		} else {
			global $root_directory, $cache_dir;
			$fname = tempnam($root_directory.$cache_dir, 'comm.xls');
			$xlsobject = exportUserCommentsForModule($module, $recordid, 'XLS');
			$xlsobject->save($fname);
			header('Content-Type: application/x-msexcel');
			header('Content-Length: '.@filesize($fname));
			header('Content-disposition: attachment; filename="Comments'.$recordid.'.xls"');
			$fh=fopen($fname, 'rb');
			fpassthru($fh);
		}
		exit;
		break;
	case 'ValidationExists':
		$valmod = vtlib_purify($_REQUEST['valmodule']);
		if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
			echo 'yes';
		} else {
			include_once 'modules/cbMap/processmap/Validations.php';
			if (Validations::ValidationsExist($valmod)) {
				echo 'yes';
			} elseif (recordIsAssignedToInactiveUser(vtlib_purify($_REQUEST['crmid']))) {
				echo 'yes';
			} else {
				$lnks = Vtiger_Link::getAllByType(getTabid($valmod), 'PRESAVE', array('MODULE'=>$valmod, 'ACTION'=>'Save'));
				if (count($lnks)>0) {
					echo 'yes';
				} else {
					echo 'no';
				}
			}
		}
		die();
		break;
	case 'getWeekendDates':
		$startDate = vtlib_purify($_REQUEST['startFrom']);
		$endDate = vtlib_purify($_REQUEST['endFrom']);
		$format = isset($_REQUEST['dateFormat']) ? vtlib_purify($_REQUEST['dateFormat']) : 'Y-m-d';
		$ret =  DateTimeField::getWeekendDates($startDate, $endDate, $format);
		break;
	case 'ValidationLoad':
		$valmod = vtlib_purify($_REQUEST['valmodule']);
		include_once 'modules/cbMap/processmap/Validations.php';
		if (Validations::recordIsAssignedToInactiveUser()) {
			echo getTranslatedString('RecordIsAssignedToInactiveUser');
			die();
		}
		$validation = Validations::processAllValidationsFor($valmod);
		if ($validation!==true) {
			echo Validations::formatValidationErrors($validation, $valmod);
			die();
		}
		if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
			include "modules/{$valmod}/{$valmod}Validation.php";
		} else {
			$lnks = Vtiger_Link::getAllByType(getTabid($valmod), 'PRESAVE', array('MODULE'=>$valmod, 'ACTION'=>'Save'));
			if (count($lnks)>0) {
				$screen_values = json_decode($_REQUEST['structure'], true);
				$rdo = '';
				foreach ($lnks as $lnk) {
					$rdo .= vtlib_process_widget($lnk, $screen_values);
				}
				echo $rdo;
			} else {
				echo '%%%OK%%%';
			}
		}
		die();
		break;
	case 'getModuleWebseriviceID':
		$wsmod = vtlib_purify($_REQUEST['wsmodule']);
		if (!empty($wsmod)) {
			$ret = vtws_getEntityId($wsmod);
		} else {
			$ret = '';
		}
		break;
	case 'updateBrowserTabSession':
		$newssid = vtlib_purify($_REQUEST['newtabssid']);
		$oldssid = vtlib_purify($_REQUEST['oldtabssid']);
		if (!empty($oldssid)) {
			foreach ($_SESSION as $key => $value) {
				if (strpos($key, $oldssid) !== false && strpos($key, $oldssid.'__prev') === false) {
					$newkey = str_replace($oldssid, $newssid, $key);
					coreBOS_Session::set($newkey, $value);
					coreBOS_Session::set($key, (isset($_SESSION[$key.'__prev']) ? $_SESSION[$key.'__prev'] : ''));
				}
			}
		}
		$ret = '';
		break;
	case 'getEmailTemplateVariables':
		$module = vtlib_purify($_REQUEST['module_from']);
		$allOptions=getEmailTemplateVariables(array($module,'Accounts'));
		$ret = array_merge($allOptions[0], $allOptions[1], $allOptions[2]);
		break;
	case 'downloadfile':
		include_once 'include/utils/downloadfile.php';
		die();
		break;
	case 'delImage':
		Vtiger_Request::validateRequest();
		include_once 'include/utils/DelImage.php';
		$id = vtlib_purify($_REQUEST['recordid']);
		$id = preg_replace('/[^0-9]/', '', $id);
		if (isset($_REQUEST['attachmodule']) && $_REQUEST["attachmodule"]=='Emails') {
			DelAttachment($id);
		} else {
			DelImage($id);
		}
		echo 'SUCCESS';
		die();
		break;
	case 'saveAttachment':
		include_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';
		include_once 'modules/MailManager/src/controllers/UploadController.php';
		$allowedFileExtension = array();
		$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize', 3000000, 'Emails');
		$upload = new MailManager_Uploader($allowedFileExtension, $upload_maxsize);
		if ($upload) {
			$filePath = decideFilePath();
			$ret = $upload->handleUpload($filePath, false);
		} else {
			$ret = '';
		}
		break;
	case 'getNumberDisplayValue':
		$value = vtlib_purify($_REQUEST['val']);
		if (empty($value)) {
			$ret = '0';
		} else {
			$currencyField = new CurrencyField($value);
			$decimals = vtlib_purify($_REQUEST['decimals']);
			$currencyField->initialize($current_user);
			$currencyField->setNumberofDecimals(min($decimals, $currencyField->getCurrencyDecimalPlaces()));
			$ret = $currencyField->getDisplayValue(null, true, true);
		}
		break;
	case 'getGloalSearch':
	case 'getGlobalSearch':
		include_once 'include/Webservices/CustomerPortalWS.php';
		$data = json_decode($_REQUEST['data'], true);
		$searchin = vtlib_purify($data['searchin']);
		$limit = isset($data['maxresults']) ? vtlib_purify($data['maxresults']) : '';
		$term = vtlib_purify($data['term']);
		$retvals = getGlobalSearch($term, $searchin, $limit, $current_user);
		$ret = array();
		if (!empty($retvals['data'])) {
			foreach ($retvals['data'] as $value) {
				$ret[] = array(
					'crmid' => $value['crmid'],
					'crmmodule' => $value['crmmodule'],
					'query_string' => $value['query_string'],
					'total' => $retvals['total']
				) + $value['crmfields'];
			}
		}
		break;
	case 'getRelatedListInfo':
		$sql = 'SELECT rl.tabid,rl.related_tabid,rl.label,tab.name as name, tabrel.name as relname
			FROM vtiger_relatedlists rl
			LEFT JOIN vtiger_tab tab ON rl.tabid=tab.tabid
			LEFT JOIN vtiger_tab tabrel ON rl.related_tabid=tabrel.tabid
			WHERE relation_id=?';
		$res = $adb->pquery($sql, array($_REQUEST['relation_id']));
		$ret = array();
		if ($adb->num_rows($res) > 0) {
			$tabid = $adb->query_result($res, 0, 'tabid');
			$tabidrel = $adb->query_result($res, 0, 'related_tabid');
			$label = $adb->query_result($res, 0, 'label');
			$mod = $adb->query_result($res, 0, 'name');
			$modrel = $adb->query_result($res, 0, 'relname');
			$ret = array(
				'tabid'=>$tabid,
				'tabidrel'=>$tabidrel,
				'label'=>$label,
				'module'=>$mod,
				'modulerel'=>$modrel,
			);
		}
		break;
	case 'getSetting':
		$skey = vtlib_purify($_REQUEST['skey']);
		if (!empty($_REQUEST['default'])) {
			$default = vtlib_purify($_REQUEST['default']);
			$ret = coreBOS_Settings::getSetting($skey, $default);
		} else {
			$ret = coreBOS_Settings::getSetting($skey, null);
		}
		break;
	case 'setSetting':
		Vtiger_Request::validateRequest();
		$skey = vtlib_purify($_REQUEST['skey']);
		$svalue = vtlib_purify($_REQUEST['svalue']);
		coreBOS_Settings::setSetting($skey, $svalue);
		$ret = '';
		break;
	case 'delSetting':
		Vtiger_Request::validateRequest();
		$skey = vtlib_purify($_REQUEST['skey']);
		coreBOS_Settings::delSetting($skey);
		$ret = '';
		break;
	case 'getTranslatedStrings':
		global $currentModule;
		$i18nm = empty($_REQUEST['i18nmodule']) ? $currentModule : vtlib_purify($_REQUEST['i18nmodule']);
		$tkeys = vtlib_purify($_REQUEST['tkeys']);
		$tkeys = explode(';', $tkeys);
		$ret = array();
		foreach ($tkeys as $tr) {
			$ret[$tr] = getTranslatedString($tr, $i18nm);
		}
		break;
	case 'execwf':
		include_once 'include/Webservices/ExecuteWorkflow.php';
		$wfid = vtlib_purify($_REQUEST['wfid']);
		$ids = explode(';', vtlib_purify($_REQUEST['ids']));
		$id = reset($ids);
		$wsid = vtws_getEntityId(getSalesEntityType($id)).'x';
		$crmids = array();
		foreach ($ids as $crmid) {
			$crmids[] = $wsid.$crmid;
		}
		if (empty($_REQUEST['ctx'])) {
			$context = '[]';
		} else {
			$context = vtlib_purify($_REQUEST['ctx']);
		}
		try {
			$ret = cbwsExecuteWorkflowWithContext($wfid, json_encode($crmids), $context, $current_user);
		} catch (Exception $e) {
			$ret = false;
		}
		break;
	case 'setDetailViewBlockStatus':
		if (GlobalVariable::getVariable('Application_DetailView_Sticky_BlockStatus', '0')=='1') {
			$blockssesion = coreBOS_Session::get('DVBLOCKSTATUS^'.$_REQUEST['dvmodule']);
			$blocks = getBlockOpenClosedStatus($_REQUEST['dvmodule'], 'detail');
			foreach ($blocks as $blabel => $bstatus) {
				if ('tbl'.str_replace(' ', '', $blabel)==$_REQUEST['dvblock']) {
					$blockssesion[$blabel] = vtlib_purify($_REQUEST['dvstatus']);
					break;
				}
			}
			coreBOS_Session::set('DVBLOCKSTATUS^'.$_REQUEST['dvmodule'], $blockssesion);
		}
		$ret = true;
		break;
	case 'ispermitted':
		$mod = vtlib_purify($_REQUEST['checkmodule']);
		$act = vtlib_purify($_REQUEST['checkaction']);
		$rec = isset($_REQUEST['checkrecord']) ? vtlib_purify($_REQUEST['checkrecord']) : '';
		$rdo = isPermitted($mod, $act, $rec)=='yes';
		$ret = array('isPermitted'=>$rdo);
		break;
	case 'getUserName':
		$ret = getUserName(vtlib_purify($_REQUEST['userid']));
		break;
	case 'getImageInfoFor':
		$id = vtlib_purify($_REQUEST['record']);
		$imageinfo = array();
		if (isPermitted(getSalesEntityType($id), 'DetailView', $id)=='yes') {
			require_once 'include/Webservices/getRecordImages.php';
			$imageinfo = cbws_getrecordimageinfo($id, $current_user);
		}
		header('Content-Type: application/json');
		if ((int)$imageinfo['results'] > 0) {
			$ret = $imageinfo;
		} else {
			$ret = '';
		}
		break;
	case 'isAdmin':
		if (is_admin($current_user)) {
			$ret = array('admin' => 'on');
		} else {
			$ret = array('admin' => 'off');
		}
		break;
	case 'setNewPassword':
		Vtiger_Request::validateRequest();
		require_once 'modules/Users/Users.php';
		require_once 'include/utils/UserInfoUtil.php';
		$userid = vtlib_purify($_REQUEST['record']);
		if (is_admin($current_user) || $current_user->id==$userid) {
			$focus = new Users();
			$focus->mode='edit';
			$focus->id = $userid;
			$focus->retrieve_entity_info($userid, 'Users');
			$ret = $focus->change_password('old_password', vtlib_purify($_REQUEST['new_password']));
			if ($ret) {
				$ret = array('password'=>$ret);
			} else {
				$ret = array('password'=>false);
			}
		} else {
			$ret = array('password'=>false);
		}
		break;
	case 'listViewJSON':
		include_once 'include/ListView/ListViewJSON.php';
		if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'updateDataListView') {
			updateDataListView();
			$ret = array();
		} else {
			$orderBy = ' DESC';
			$entries = GlobalVariable::getVariable('Application_ListView_PageSize', 20, $currentModule);
			$formodule = isset($_REQUEST['formodule']) ? vtlib_purify($_REQUEST['formodule']) : '';
			$columns = isset($_REQUEST['columns']) ? vtlib_purify($_REQUEST['columns']) : '';
			$beforeFilter = isset($_REQUEST['beforeFilter']) ? vtlib_purify($_REQUEST['beforeFilter']) : '';
			if (isset($_REQUEST['perPage'])) {
				//get data
				$perPage = isset($_REQUEST['perPage']) ? vtlib_purify($_REQUEST['perPage']) : $entries;
				$sortAscending = isset($_REQUEST['sortAscending']) ? vtlib_purify($_REQUEST['sortAscending']) : '';
				$sortColumn = isset($_REQUEST['sortColumn']) ? vtlib_purify($_REQUEST['sortColumn']) : '';
				$page = isset($_REQUEST['page']) ? vtlib_purify($_REQUEST['page']) : 1;
				$search = isset($_REQUEST['search']) ? vtlib_purify($_REQUEST['search']) : '';
				$searchtype = isset($_REQUEST['searchtype']) ? vtlib_purify($_REQUEST['searchtype']) : '';
				if ($sortAscending == 'true') {
					$orderBy = ' ASC';
				}
				if ($perPage == 0) {
					$perPage = $list_max_entries_per_page;
				}
				$LV = getListViewJSON($formodule, $perPage, $orderBy, $sortColumn, $page, $search, $searchtype);
			} else {
				//get headers
				$LV = getListViewJSON($formodule);
			}
			if (isset($columns) && $columns == 'true') {
				$ret = array($LV['headers'], $LV['data']['customview'], $LV['data']['export_where']);
			} else {
				$ret = $LV['data'];
			}
		}
		break;
	case 'ismoduleactive':
		$mod = vtlib_purify($_REQUEST['checkmodule']);
		$rdo = vtlib_isModuleActive($mod);
		$ret = array('isactive'=>$rdo);
		break;
	default:
		$ret = '';
		break;
}
echo json_encode($ret);
die();
?>
