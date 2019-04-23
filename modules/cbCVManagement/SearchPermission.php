<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Module       : CV Permission Tester
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

require_once 'modules/cbCVManagement/cbCVManagement.php';
$cvid = vtlib_purify($_REQUEST['cvid']);
$cvuserid = vtlib_purify($_REQUEST['cvuserid']);
$cvmod = vtlib_purify($_REQUEST['cvmodule']);
$retval = vtlib_purify($_REQUEST['returnvalidation']);
$startTime = microtime(true);
if ($cvid==-1) {
	$rdo = cbCVManagement::getDefaultView($cvmod, $cvuserid);
} else {
	$rdo = cbCVManagement::getPermission($cvid, $cvuserid);
}
$counter = (microtime(true) - $startTime);
$ret = array($cvid=>$rdo);
if ($retval) {
	$validationinfo = cbCVManagement::getValidationInfo();
	if ($cvid==-1) {
		$vi = "<a href='index.php?module=".$cvmod."&action=ListView&start=1&viewname=".$rdo."' target=_blank>$rdo</a>";
	} else {
		$vi = nl2br(var_export($rdo, true));
	}
	$validationinfo[] = "<h2 align='center'>RESULT: ".$vi.'</H2>';
	$ret['validation'] = $validationinfo;
	$ret['timespent'] = round($counter*1000, 1);
}
echo json_encode($ret);
?>