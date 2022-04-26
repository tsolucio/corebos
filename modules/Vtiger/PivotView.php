<?php
/*************************************************************************************************
 * Copyright 2021 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
*************************************************************************************************/

global $adb;
if (isset($_REQUEST['bmapname'])) {
	$bmapname = vtlib_purify($_REQUEST['bmapname']);
} else {
	$bmapname = $currentModule.'_Pivot';
}
$cbMapid = GlobalVariable::getVariable('BusinessMapping_'.$bmapname, cbMap::getMapIdByName($bmapname), $currentModule);
if ($cbMapid) {
	$cbMap = cbMap::getMapByID($cbMapid);
	$cbMapKb = $cbMap->Pivot();
	if (empty($cbMapKb)) {
		$smarty->assign('showDesert', true);
	} else {
		function getPivotValue($field, $value) {
			global $fieldsinfo;
			if (!empty($fieldsinfo[$field])) {
				if ($fieldsinfo[$field]->isReferenceField()) {
					$value = getEntityName(getSalesEntityType($value), $value)[$value];
				} elseif ($fieldsinfo[$field]->isOwnerField()) {
					$value = getUserFullName($value);
				} else {
					switch ($fieldsinfo[$field]->getUIType()) {
						case '1613':
						case '1614':
						case '1615':
						case '3313':
						case '3314':
						case '1024':
							$value = getTranslatedString(decode_html($value), $value);
							break;
						case '15':
						case '33':
							$value = getTranslatedString(decode_html($value));
							break;
						default:
							$value = decode_html($value);
							break;
					}
				}
			}
			return addslashes($value);
		}
		$smarty->assign('showDesert', false);
		$viewid = $cbMapKb['filter'];
		$fieldaggr = $cbMapKb['aggregate'];
		$aggregations = $cbMapKb['aggregations'];
		$aggregatorName = $cbMapKb['aggregatorName'];
		$rendererName = $cbMapKb['rendererName'];
		$rows = $cbMapKb['rows'];
		$cols = $cbMapKb['cols'];

		$namerow = array();
		$namecol = array();
		$record = array();

		foreach ($rows as $rw) {
			$namerow[] = $rw['name'];
			$namelabelrow[] = getTranslatedString($rw['label']);
		}
		foreach ($cols as $cl) {
			$namecol[] = $cl['name'];
			$namelabelcol[] = getTranslatedString($cl['label']);
			$namecolaggr[] = $cl['name'];
		}
		if (isset($fieldaggr) && $fieldaggr!='') {
			$aggreg='aggregator: sum(intFormat)(["'.$fieldaggr.'"]),';
			$namecolaggr[] = $fieldaggr;
		} else {
			$aggreg = '';
		}
		$aggcols = array();
		if (!empty($aggregations)) {
			foreach ($aggregations as $agg) {
				$aggcols[] = $agg['arguments'][0];
			}
		}
		$queryGenerator = new QueryGenerator($currentModule, $current_user);
		if ($viewid != '0') {
			$queryGenerator->initForCustomViewById($viewid);
		} else {
			$queryGenerator->initForDefaultCustomView();
		}
		$fields2get = array_merge($queryGenerator->getFields(), $namerow, $namecolaggr, $aggcols);
		$fieldsinfo = [];
		$cmodobj = Vtiger_Module::getInstance($currentModule);
		foreach ($fields2get as $fld) {
			if ($fld=='smownerid') {
				$fobj = Vtiger_Field::getInstance('assigned_user_id', $cmodobj);
			} else {
				$fobj = Vtiger_Field::getInstance($fld, $cmodobj);
			}
			if ($fobj) {
				$fieldsinfo[$fld] = $fobj->getWebserviceFieldObject();
			} else {
				$fieldsinfo[$fld] = null;
			}
		}
		$queryGenerator->setFields($fields2get);
		$list_query = $adb->pquery($queryGenerator->getQuery(), array());
		$count = $adb->num_rows($list_query);
		for ($i = 0; $i < $count; $i++) {
			$rec = 0;
			foreach ($rows as $rw) {
				$value = $adb->query_result($list_query, $i, $rw['name']);
				$record[$rec] = '"'.getTranslatedString($rw['label']).'":"'.getPivotValue($rw['name'], $value).'"';
				$rec++;
			}
			foreach ($cols as $cl) {
				$value = $adb->query_result($list_query, $i, $cl['name']);
				$record[$rec] = '"'.getTranslatedString($cl['label']).'":"'.getPivotValue($cl['name'], $value).'"';
				$rec++;
			}
			if (isset($fieldaggr) && $fieldaggr!='') {
				$record[$rec] = '"'.$fieldaggr.'":"'.$adb->query_result($list_query, $i, $fieldaggr).'"';
			}
			$rec++;
			$mainfield = getEntityField($currentModule)['fieldname'];
			$record[$rec] = '"Name":"'.addslashes(getTranslatedString(decode_html($adb->query_result($list_query, $i, $mainfield)))).'"';
			if (!empty($aggregations)) {
				$currentRow = array();
				foreach ($aggregations as $agg) {
					$value = $adb->query_result($list_query, $i, $agg['arguments'][0]);
					$currentRow[] = '"'.$agg['name'].'":'.(is_numeric($value) ? (float)$value : '"'.addslashes($value).'"');
				}
				$record[$rec] .= ','.implode(',', $currentRow);
			}
			$records[$i] = implode(',', $record);
		}
		$recordsimpl = '{'.implode('},{', $records).'}';
		$namerw = '"'.implode('","', $namelabelrow).'"';
		$namecl = '"'.implode('","', $namelabelcol).'"';
		$smarty->assign('aggreg', $aggreg);
		$smarty->assign('aggregations', json_encode($aggregations));
		$smarty->assign('aggregatorName', $aggregatorName);
		$smarty->assign('rendererName', $rendererName);
		$smarty->assign('ROWS', $namerw);
		$smarty->assign('COLS', $namecl);
		$smarty->assign('RECORDS', $recordsimpl);
		$smarty->assign('bmapname', $bmapname);
	}
} else {
	$smarty->assign('showDesert', true);
}

$smarty->assign('moduleView', 'Pivot');
$smarty->assign('moduleShowSearch', false);
?>
