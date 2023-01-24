<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

/* Get the value of a Discount Line record for the given context */
function cbws_getDiscountLinePrice($product, $account, $contact, $module, $user) {
	global $log, $adb;

	$webserviceObject = VtigerWebserviceObject::fromName($adb, 'DiscountLine');
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();

	if ($meta->hasReadAccess()!==true) {
		throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, 'Permission to perform the operation is denied');
	}

	require_once 'modules/DiscountLine/DiscountLine.php';
	$productid = vtws_getCRMID($product);
	$accountid = vtws_getCRMID($account);
	$contactid = vtws_getCRMID($contact);
	$moduleid = vtws_getCRMID($module);
	return DiscountLine::getDiscount($productid, $accountid, $contactid, $moduleid);
}
?>
