<?php
/*************************************************************************************************
 * Copyright 2015 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : coreBOS Translations
 *  Version      : 1.0
 *  Author       : OpenCubed
 *************************************************************************************************/
global $adb, $log;
$allrecords = substr($_REQUEST['allrecords'], 0, -1);
$allids = str_replace(';', ',', $allrecords);
$allids = explode(',', $allids);
if (!empty($allids)) {
	$filename = 'cbtranslationExport.json';
	$fp = fopen('php://output', 'w');
	header('Content-type: application/json');
	header('Content-Disposition: attachment; filename=' . $filename);
	$queryString = 'SELECT translation_module,translation_key,i18n
		FROM vtiger_cbtranslation
		INNER JOIN vtiger_crmentity ce ON ce.crmid=vtiger_cbtranslation.cbtranslationid
		WHERE vtiger_cbtranslation.cbtranslationid IN (' . generateQuestionMarks($allids) . ')';
	$cbtranslationQuery = $adb->pquery($queryString, $allids);
	if ($cbtranslationQuery && $adb->num_rows($cbtranslationQuery) > 0) {
		$csvContent = array();
		while ($cbtranslationQuery && $row = $adb->fetch_array($cbtranslationQuery)) {
			$tranlation_module = $row['translation_module'];
			$translation_key = $row['translation_key'];
			$i18n = $row['i18n'];
			$columnString = "$tranlation_module::$translation_key";
			$csvContent[$columnString] = $i18n;
		}
		print json_encode($csvContent);
		exit;
	} else {
		echo getTranslatedString('LBL_RECORD_NOT_FOUND');
	}
} else {
	echo getTranslatedString('LBL_RECORD_NOT_FOUND');
}
