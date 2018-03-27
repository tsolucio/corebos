<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L.  --  This file is a part of coreBOS.
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

/* Get the value of a Global Variable for the connected user and optionally for a given module
 * Parameters
 *  $gvname: name of the variable for which we want to know the value
 *  $defaultvalue: value to return if no value is defined in the application
 *  $gvmodule: module name to look for the variable, can be left empty
 * Returns
 *  value of the variable: if a variable record is found
 *  the default value given: if a variable record is not found or the current user does not have access to the Global Variable module
*/
function cbws_SearchGlobalVar($gvname, $defaultvalue, $gvmodule, $user) {
	global $log, $adb;

	$entityName = 'GlobalVariable';
	$webserviceObject = VtigerWebserviceObject::fromName($adb, $entityName);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();

	require_once $handlerPath;

	$handler = new $handlerClass($webserviceObject, $user, $adb, $log);
	$meta = $handler->getMeta();

	if ($meta->hasReadAccess()!==true) {
		return $defaultvalue;
	}

	require_once 'modules/GlobalVariable/GlobalVariable.php';
	$rdo = GlobalVariable::getVariable($gvname, $defaultvalue, $gvmodule, $user->id);
	VTWS_PreserveGlobal::flush();
	return $rdo;
}
?>
