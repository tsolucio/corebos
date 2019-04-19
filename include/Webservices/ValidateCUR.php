<?php
/***********************************************************************************
 * Copyright 2012-2018 JPL TSolucio, S.L.  --  This file is a part of coreBOS
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
 ************************************************************************************/
include_once 'include/Webservices/ValidateInformation.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';
include_once 'include/Webservices/Revise.php';

function __cbwsCURValidation($element, $user) {
	list($wsid, $record) = explode('x', $element['id']);
	$elementType = getSalesEntityType($record);
	$context = $element;
	$context['record'] = $record;
	$context['module'] = $elementType;
	return cbwsValidateInformation(json_encode($context), $user);
}

function cbwsCreateWithValidation($elementType, $element, $user) {
	$context = $element;
	$context['record'] = '';
	$context['module'] = $elementType;
	$validation = cbwsValidateInformation(json_encode($context), $user);
	if ($validation===true) {
		$validation = vtws_create($elementType, $element, $user);
	}
	return $validation;
}

function cbwsUpdateWithValidation($element, $user) {
	$validation = __cbwsCURValidation($element, $user);
	if ($validation===true) {
		$validation = vtws_update($element, $user);
	}
	return $validation;
}

function cbwsReviseWithValidation($element, $user) {
	$validation = __cbwsCURValidation($element, $user);
	if ($validation===true) {
		$validation = vtws_revise($element, $user);
	}
	return $validation;
}
