<?php
/*************************************************************************************************
 * Copyright 2019 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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
*************************************************************************************************/

function executeBusinessAction($businessactionid, $context, $user) {
	global $currentModule;
	$businessactionid = vtws_getWSID($businessactionid);
	$context = json_decode($context, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context');
	}
	$wscrmid = empty($context['ID']) ? (empty($context['RECORDID']) ?  (empty($context['RECORD']) ? 0 : $context['RECORD']) : $context['RECORDID']) : $context['ID'];
	if ($wscrmid==0) {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (no ID)');
	}
	if (strpos($wscrmid, 'x')===false) {
		if (!is_numeric($wscrmid)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (invalid ID)');
		}
		$ctx_MODULE = getSalesEntityType($wscrmid);
		if (empty($ctx_MODULE)) {
			throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: context (invalid ID)');
		}
		$wscrmid = vtws_getEntityId($ctx_MODULE).'x'.$wscrmid;
		if (empty($context['MODULE'])) {
			$context['MODULE'] = $ctx_MODULE;
			$context['module'] = $ctx_MODULE;
		}
	}
	list($wsid, $crmid) = explode('x', $wscrmid);
	$context['ID'] = $context['RECORDID'] = $context['RECORD'] = $crmid;
	$context['id'] = $context['recordid'] = $context['record'] = $crmid;
	if (empty($context['MODULE'])) {
		$context['MODULE'] = getSalesEntityType($crmid);
		$context['module'] = getSalesEntityType($crmid);
	}
	$currentModule = $context['module'];
	if (empty($context['MODE'])) {
		$context['MODE'] = 'edit';
		$context['mode'] = 'edit';
	}
	//$context['FIELDS']
	$businessAction = (object) vtws_retrieve($businessactionid, $user);
	$ba = (array) $businessAction;
	$strtemplate = new Vtiger_StringTemplate();
	foreach ($context as $key => $value) {
		$strtemplate->assign($key, $value);
	}
	$ba['linkurl'] = $strtemplate->merge($ba['linkurl']);
	$ba['businessactionsid'] = $businessactionid;
	$ba['elementtype_action'] = $ba['linktype'];
	$ba['status'] = $ba['active'];
	$lnk = BusinessActions::convertToObject(BusinessActions::IGNORE_MODULE, $ba);
	if (preg_match("/^block:\/\/(.*)/", $ba['linkurl'], $matches)) {
		return vtlib_process_widget($lnk, $context);
	} else {
		throw new WebServiceException(WebServiceErrorCode::$INVALID_PARAMETER, 'Invalid parameter: business action (only block detail view widgets supported)');
	}
	return $return;
}