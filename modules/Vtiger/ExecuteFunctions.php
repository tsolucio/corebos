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
require_once 'include/utils/CommonUtils.php';
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
	case 'getFieldValuesFromRecord':
		$ret = array();
		$crmid = vtlib_purify($_REQUEST['getFieldValuesFrom']);
		if (!empty($crmid)) {
			$module = getSalesEntityType($crmid);
			$fields = vtlib_purify($_REQUEST['getTheseFields']);
			$fields = explode(',',$fields);
			$queryGenerator = new QueryGenerator($module, $current_user);
			$queryGenerator->setFields($fields);
			$queryGenerator->addCondition('id',$crmid,'e');
			$query = $queryGenerator->getQuery();
			$queryres=$adb->pquery($query,array());
			if ($adb->num_rows($queryres)>0) {
				$col=0;
				foreach ($fields as $field) {
					$ret[$field]=$adb->query_result($queryres,0,$col++);
				}
			}
		}
		break;
	case 'getEmailTemplateDetails':
		$emltplid = vtlib_purify($_REQUEST['templateid']);
		$emltpl = getTemplateDetails($emltplid);
		$ret = array();
		if (count($emltpl)>0) {
			$ret['subject'] = $emltpl[2];
			$ret['body'] = $emltpl[1];
			$ret['from_email'] = $emltpl[3];
		}
		break;
	case 'ValidationExists':
		$valmod = vtlib_purify($_REQUEST['valmodule']);
		if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
			echo 'yes';
		} else {
			include_once 'modules/cbMap/processmap/Validations.php';
			if (Validations::ValidationsExist($valmod)) {
				echo 'yes';
			} else {
				echo 'no';
			}
		}
		die();
		break;
	case 'ValidationLoad':
		$valmod = vtlib_purify($_REQUEST['valmodule']);
		include_once 'modules/cbMap/processmap/Validations.php';
		if (Validations::ValidationsExist($valmod)) {
			$validation = Validations::processAllValidationsFor($valmod);
			if ($validation!==true) {
				echo Validations::formatValidationErrors($validation,$valmod);
				die();
			}
		}
		if (file_exists("modules/{$valmod}/{$valmod}Validation.php")) {
			include "modules/{$valmod}/{$valmod}Validation.php";
		} else {
			echo '%%%OK%%%';
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
		foreach ($_SESSION as $key => $value) {
			if (strpos($key, $oldssid) !== false and strpos($key, $oldssid.'__prev') === false) {
				$newkey = str_replace($oldssid, $newssid, $key);
				coreBOS_Session::set($newkey, $value);
				coreBOS_Session::set($key, (isset($_SESSION[$key.'__prev']) ? $_SESSION[$key.'__prev'] : ''));
			}
		}
		$ret = '';
		break;
	case 'getEmailTemplateVariables':
		$module = vtlib_purify($_REQUEST['module_from']);
		$allOptions=getEmailTemplateVariables(array($module,'Accounts'));
		$ret = array_merge($allOptions[0],$allOptions[1],$allOptions[2]);
		break;
	case 'saveAttachment':
		include_once 'modules/Settings/MailScanner/core/MailAttachmentMIME.php';
		include_once 'modules/MailManager/src/controllers/UploadController.php';
		$allowedFileExtension = array();
		$upload_maxsize = GlobalVariable::getVariable('Application_Upload_MaxSize',3000000,'Emails');
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
			$currencyField->setNumberofDecimals(min($decimals,$currencyField->getCurrencyDecimalPlaces()));
			$ret = $currencyField->getDisplayValue(null,true,true);
		}
		break;
	case 'getGloalSearch':
		include_once 'include/Webservices/CustomerPortalWS.php';
		$data = json_decode(file_get_contents('php://input'), TRUE);
		$searchin = vtlib_purify($data['searchin']);
		$limit = isset($data['maxResults']) ? vtlib_purify($data['maxResults']) : '';
		$term = vtlib_purify($data['term']);
		$retvals = getGlobalSearch($term, $searchin, $limit, $current_user);
		$ret = array();
		foreach ($retvals as $value) {
			$ret[] = array('crmid'=>$value['crmid'],'crmmodule'=>$value['crmmodule'],'query_string'=>$value['query_string'])+ $value['crmfields'];
		}
		break;
	case 'ismoduleactive':
	default:
		$mod = vtlib_purify($_REQUEST['checkmodule']);
		$rdo = vtlib_isModuleActive($mod);
		$ret = array('isactive'=>$rdo);
		break;
}

echo json_encode($ret);
die();
?>