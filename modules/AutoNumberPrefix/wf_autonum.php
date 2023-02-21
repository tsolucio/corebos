<?php
/*************************************************************************************************
 * Copyright 2017 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS customizations.
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
 *  Version      : 1.1
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/

function __cb_autonumber_get($tosearch) {
	global $adb;
	if (is_numeric($tosearch)) {
		$check = $adb->pquery(
			'select autonumberprefixid,current,prefix,format,isworkflowexpression from vtiger_autonumberprefix where autonumberprefixid=? FOR UPDATE',
			array($tosearch)
		);
	} elseif (vtlib_isModuleActive($tosearch)) { // default record for module
		$check = $adb->pquery(
			'select autonumberprefixid,current,prefix,format,isworkflowexpression
				from vtiger_autonumberprefix
				inner join vtiger_crmentity on crmid=autonumberprefixid
				where deleted=0 and semodule=? and active=1 order by default1 DESC LIMIT 1 FOR UPDATE',
			array($tosearch)
		);
	} else {
		$check = $adb->pquery(
			'select autonumberprefixid,current,prefix,format,isworkflowexpression
				from vtiger_autonumberprefix
				inner join vtiger_crmentity on crmid=autonumberprefixid
				where deleted=0 and autonumberprefixno=? FOR UPDATE',
			array($tosearch)
		);
	}
	return $check;
}

function __cb_autonumber_inc($arr) {
	if (!empty($arr[0])) {
		global $adb, $default_charset;
		$check = __cb_autonumber_get($arr[0]);
		if ($check && $adb->num_rows($check)>0) {
			$anpid = $adb->query_result($check, 0, 'autonumberprefixid');
			$prefix = $adb->query_result($check, 0, 'prefix');
			$prefix = html_entity_decode($prefix, ENT_QUOTES, $default_charset);
			$curid = $adb->query_result($check, 0, 'current');
			$format = $adb->query_result($check, 0, 'format');
			$format = html_entity_decode($format, ENT_QUOTES, $default_charset);
			$isworkflowexpression = $adb->query_result($check, 0, 'isworkflowexpression');
			if ($isworkflowexpression) {
				$format = sprintf($format, $curid);
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($format)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$prev_inv_no = $prefix . $exprEvaluater->evaluate($arr[count($arr)-1]);
			} else {
				if (is_numeric($format)) {
					$fmtlen = strlen($format);
					$temp = str_repeat('0', $fmtlen);
					$numchars = max(strlen($curid), $fmtlen);
					$prev_inv_no = $prefix . substr($temp.$curid, -$numchars);
				} else {
					$prev_inv_no = $prefix . sprintf(date($format, time()), $curid);
				}
			}
			$adb->pquery('UPDATE vtiger_autonumberprefix SET current=current+1 where autonumberprefixid=?', array($anpid));
			return decode_html($prev_inv_no);
		}
	}
	return '';
}

function __cb_autonumber_dec($arr) {
	if (!empty($arr[0])) {
		global $adb;
		$check = __cb_autonumber_get($arr[0]);
		if ($check && $adb->num_rows($check)>0) {
			$anpid = $adb->query_result($check, 0, 'autonumberprefixid');
			$adb->pquery('UPDATE vtiger_autonumberprefix SET current=current-1 where autonumberprefixid=?', array($anpid));
		}
	}
	return '';
}
