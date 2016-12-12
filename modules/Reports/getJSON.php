<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
 * Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
require_once 'include/utils/utils.php';
require_once('modules/Reports/Reports.php');
include_once('modules/Reports/ReportRun.php');

$response = array(
	'total' => 0,
	'data' => array(),
	'error' => true,
);

$reportid = (isset($_REQUEST['record']) ? vtlib_purify($_REQUEST['record']) : 0);
$sql = 'select * from vtiger_report where reportid=?';
$res = $adb->pquery($sql, array($reportid));
if($res and $adb->num_rows($res) > 0) {
	$reporttype = $adb->query_result($res,0,'reporttype');

	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$ogReport = new Reports($reportid);
	$primarymodule = $ogReport->primodule;
	$restrictedmodules = array();
	if($ogReport->secmodule!='')
		$rep_modules = explode(":",$ogReport->secmodule);
	else
		$rep_modules = array();

	array_push($rep_modules,$primarymodule);
	$modules_permitted = true;
	$modules_export_permitted = true;
	foreach($rep_modules as $mod){
		if(isPermitted($mod,'index')!= 'yes' || vtlib_isModuleActive($mod)==false){
			$modules_permitted = false;
			$restrictedmodules[] = $mod;
		}
		if(isPermitted("$mod",'Export','')!='yes')
			$modules_export_permitted = false;
	}

	if(isPermitted($primarymodule,'index') == 'yes' && $modules_permitted == true) {
		$oReportRun = new ReportRun($reportid);
		$advft_criteria = coreBOS_Session::get('ReportAdvCriteria'.$_COOKIE['corebos_browsertabID'], '');
		if(!empty($advft_criteria)) $advft_criteria = json_decode($advft_criteria,true);
		$advft_criteria_groups = coreBOS_Session::get('ReportAdvCriteriaGrp'.$_COOKIE['corebos_browsertabID'], '');
		if(!empty($advft_criteria_groups)) $advft_criteria_groups = json_decode($advft_criteria_groups,true);

		$filtersql = $oReportRun->RunTimeAdvFilter($advft_criteria,$advft_criteria_groups);
		if (isset($_REQUEST['page'])) {
			$oReportRun->page = vtlib_purify($_REQUEST['page']);
			$output = 'JSONPAGED';
		} else {
			$oReportRun->page = 1;
			$output = 'JSON';
		}
		$response = $oReportRun->GenerateReport($output, $filtersql, false);
	} else {
		$response['error_message'] = getTranslatedString('LBL_NO_ACCESS', 'Reports');
		$response = json_encode($response);
	}
} else {
	$response['error_message'] = getTranslatedString('ERR_INCORRECT_REPORTID', 'Reports');
	$response = json_encode($response);
}
echo $response;
