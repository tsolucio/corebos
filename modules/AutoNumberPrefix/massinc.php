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
 *  Module       : AutoNumberPrefix
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
global $currentModule, $adb;
$msgtype = 'cb-alert-danger';
$STATUSMSG = getTranslatedString('LBL_MASSINCERROR', 'AutoNumberPrefix');
$record = (empty($_REQUEST['record']) || !is_numeric($_REQUEST['record'])) ? 0 : (int)vtlib_purify($_REQUEST['record']);
$mrs = $adb->pquery('select semodule from vtiger_autonumberprefix where autonumberprefixid=?', array($record));
if ($mrs && $adb->num_rows($mrs)>0) {
	$selectedModule = $adb->query_result($mrs, 0, 0);
	$focus = CRMEntity::getInstance($selectedModule);
	$resultinfo = $focus->updateMissingSeqNumber($selectedModule);
	if (!empty($resultinfo)) {
		$msgtype = 'cb-alert-success';
		if ($resultinfo['totalrecords'] != $resultinfo['updatedrecords']) {
			$msgtype = 'cb-alert-danger';
		}
		$STATUSMSG = getTranslatedString('LBL_TOTAL', 'Settings').
			$resultinfo['totalrecords'] . ', '.getTranslatedString('LBL_UPDATE', 'Settings').
			' '.getTranslatedString('LBL_DONE', 'Settings').':'.$resultinfo['updatedrecords'];
	}
}
header("location: index.php?action=DetailView&module=AutoNumberPrefix&record=$record&error_msg=$STATUSMSG&error_msgclass=$msgtype");
?>
