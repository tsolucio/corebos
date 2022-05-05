<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
include_once 'modules/com_vtiger_workflow/WorkFlowScheduler.php';
include_once 'modules/com_vtiger_workflow/VTSimpleTemplateOnData.inc';

function cbexpsql_supportedFunctions() {
	return array(
		'add' => '+',
		'sub' => '-',
		'mul' => '*',
		'div' => '/',
		'power' => 'power(base,exponential)',
		'round' => 'round(numericfield,decimals)',
		'ceil' => 'ceil(numericfield)',
		'floor' => 'floor(numericfield)',
		'modulo' => 'modulo(numericfield)',
		'concat' => 'concat(a,b)',
		'stringposition' => 'stringposition(haystack,needle)',
		'stringlength' => 'stringlength(string)',
		'stringreplace' => 'stringreplace(search,replace,subject)',
		'regexreplace' => 'regexreplace(pattern,replace,subject)',
		'substring' => 'substring(stringfield,start,length)',
		'randomstring' => 'randomstring(length)',
		'uppercase'=>'uppercase(stringfield)',
		'lowercase'=>'lowercase(stringfield)',
		//'uppercasefirst'=>'uppercasefirst(stringfield)',
		//'uppercasewords'=>'uppercasewords(stringfield)',
		'time_diff(a)' => 'time_diff(a)',
		'time_diff(a,b)' => 'time_diff(a,b)',
		'time_diffdays(a)' => 'time_diffdays(a)',
		'time_diffdays(a,b)' => 'time_diffdays(a,b)',
		'time_diffyears(a)' => 'time_diffyears(a)',
		'time_diffyears(a,b)' => 'time_diffyears(a,b)',
		//'time_diffweekdays(a)' => 'time_diffweekdays(a)',
		//'time_diffweekdays(a,b)' => 'time_diffweekdays(a,b)',
		'networkdays' => 'networkdays(startDate, endDate, holidays)',
		'add_days' => 'add_days(datefield, noofdays)',
		'sub_days' => 'sub_days(datefield, noofdays)',
		'add_months' => 'add_months(datefield, noofmonths)',
		'sub_months' => 'sub_months(datefield, noofmonths)',
		'add_time' => 'add_time(timefield, minutes)',
		'sub_time' => 'sub_time(timefield, minutes)',
		'today' => "get_date('today')",
		'tomorrow' => "get_date('tomorrow')",
		'yesterday' => "get_date('yesterday')",
		//'time' => "get_date('time')",
		'sum' => 'sum(fieldname)',
		'min' => 'min(fieldname)',
		'max' => 'max(fieldname)',
		'avg' => 'avg(fieldname)',
		'count' => 'count(fieldname)',
		'group_concat' => 'group_concat(fieldname)',
		'aggregation'=>'aggregation(operation,RelatedModule,relatedFieldToAggregate,conditions)',
		'aggregation_fields_operation'=>'aggregation_fields_operation(operation,RelatedModule,relatedFieldsToAggregateWithOperation,conditions)',
		'aggregate_time' => 'aggregate_time(relatedModuleName, relatedModuleField, conditions)',
		'isString' => 'isString(fieldname)',
		'isNumeric' => 'isNumeric(fieldname)',
		'distinct' => '!=',
		'ltequals' => '<=',
		'gtequals' => '>=',
		'lt' => '<',
		'gt' => '>',
		'ifelse' => 'if else then end',
		'coalesce' => 'coalesce(a,...,n)',
		'hash' => 'hash(field, method)',
		'getEntityType' => 'getEntityType(field)',
		'number_format' => 'number_format(number, format)',
		// 'add_workdays' => 'add_workdays(date, numofdays, addsaturday, holidays)',
		// 'format_date' => 'format_date(date,format)',
		// 'next_date' => "get_nextdate(startDate,days,holidays,include_weekend)",
		// 'next_date_laborable' => "get_nextdatelaborable(startDate,days,holidays,saturday_laborable)",
		// 'num2str' => 'num2str(number|field, language)',
		// 'translate' => 'translate(string|field)',
		// 'globalvariable' => 'globalvariable(gvname)',
		// 'getCurrentUserID' => 'getCurrentUserID()',
		// 'getCurrentUserName' => 'getCurrentUserName({full})',
		// 'getCurrentUserField' => 'getCurrentUserField(fieldname)',
		'getCRMIDFromWSID' => 'getCRMIDFromWSID(id)',
		// 'getEntityType'=>'getEntityType(field)',
		// 'getimageurl'=>'getimageurl(field)',
		// 'getLatitude' => 'getLatitude(address)',
		// 'getLongitude' => 'getLongitude(address)',
		// 'getGEODistance' => 'getGEODistance(address_from,address_to)',
		// 'getGEODistanceFromCompanyAddress' => 'getGEODistanceFromCompanyAddress(address)',
		// 'getGEODistanceFromUserAddress' => 'getGEODistanceFromUserAddress(address)',
		// 'getGEODistanceFromUser2AccountBilling' => 'getGEODistanceFromUser2AccountBilling(account,address_specification)',
		// 'getGEODistanceFromAssignUser2AccountBilling' => 'getGEODistanceFromAssignUser2AccountBilling(account,assigned_user,address_specification)',
		// 'getGEODistanceFromUser2AccountShipping' => 'getGEODistanceFromUser2AccountShipping(account,address_specification)',
		// 'getGEODistanceFromAssignUser2AccountShipping' => 'getGEODistanceFromAssignUser2AccountShipping(account,assigned_user,address_specification)',
		// 'getGEODistanceFromUser2ContactBilling' => 'getGEODistanceFromUser2ContactBilling(contact,address_specification)',
		// 'getGEODistanceFromAssignUser2ContactBilling' => 'getGEODistanceFromAssignUser2ContactBilling(contact,assigned_user,address_specification)',
		// 'getGEODistanceFromUser2ContactShipping' => 'getGEODistanceFromUser2ContactShipping(contact,address_specification)',
		// 'getGEODistanceFromAssignUser2ContactShipping' => 'getGEODistanceFromAssignUser2ContactShipping(contact,assigned_user,address_specification)',
		// 'getGEODistanceFromCoordinates' => 'getGEODistanceFromCoordinates({lat1},{long1},{lat2},{long2})',
		'getIDof' => 'getIDof(module, searchon, searchfor)',
		'executeSQL' => 'executeSQL(query, parameters...)',
		//'getRelatedIDs' => 'getRelatedIDs(module)',
		// 'getRelatedMassCreateArray' => 'getRelatedMassCreateArray(module,recordid)',
		// 'getRelatedMassCreateArrayConverting' => 'getRelatedMassCreateArrayConverting(module, MainModuleDestination, RelatedModuleDestination, recordid)',
		// 'getRelatedRecordCreateArrayConverting' => 'getRelatedRecordCreateArrayConverting(module, RelatedModuleDestination, recordid)',
		// 'getISODate' => 'getISODate(year,weeks, dayInweek)',
		// 'getFromContext' => 'getFromContext(variablename)',
		// 'getFromContextSearching' => 'getFromContextSearching(variablename, searchon, searchfor, returnthis)',
		// 'setToContext' => 'setToContext(variablename, value)',
		'getSetting' => "getSetting('setting_key', 'default')",
		// 'setSetting' => 'setSetting('setting_key', value)',
		// 'delSetting' => 'delSetting('setting_key')',
		// 'getCRUDMode' => 'getCRUDMode()',
		// 'OR' => 'OR(condition1, condition2)',
		// 'AND' => 'AND(condition1, condition2)',
		// 'exists' => 'exists(fieldname, value)',
		// 'existsrelated' => 'existsrelated(relatedmodule, fieldname, value)',
		// 'allrelatedare' => 'allrelatedare(relatedmodule, fieldname, value)',
		// 'allrelatedarethesame' => 'allrelatedarethesame(relatedmodule, fieldname, value)',
		'average' => 'average(number,...)'
	);
}

function __cbexpsql_functionparams($func, $arr, $mmodule) {
	$sql = $func.'(';
	foreach ($arr as $prm) {
		$sql .= __cbexpsql_functionparamsvalue($prm, $mmodule).',';
	}
	$sql = rtrim($sql, ',');
	return new VTExpressionSymbol($sql.')', 'function');
}

function __cbexpsql_functionparamsvalue($prm, $mmodule) {
	if (is_object($prm) && isset($prm->value)) {
		preg_match('/(\w+) : \((\w+)\) (\w+)/', $prm->value, $valuematches);
		if (count($valuematches) != 0) {
			list($rdo, $isfield) = WorkFlowScheduler::getColumnFromField($prm->value, true);
		} else {
			preg_match('/^[a-z_]+[a-z0-9_]*$/', $prm->value, $funcmatches);
			if (count($funcmatches) != 0) {
				list($rdo, $isfield) = WorkFlowScheduler::getColumnFromField('$(nofield : ('.$mmodule.') '.$prm->value.')', false);
				if (!$isfield) {
					$rdo = "'".$prm->value."'";
				}
			} elseif ($prm->type=='string') {
				$rdo = "'".$prm->value."'";
			} else {
				$rdo = $prm->value;
			}
		}
	} else {
		if (is_numeric($prm)) {
			$rdo = $prm;
		} else {
			$rdo = "'".$prm."'";
		}
	}
	return $rdo;
}

function __cbexpsql_mathparams($func, $arr, $mmodule) {
	return new VTExpressionSymbol(__cbexpsql_functionparamsvalue($arr[0], $mmodule).$func.__cbexpsql_functionparamsvalue($arr[1], $mmodule), 'math');
}

function cbexpsql_concat($arr, $mmodule) {
	return __cbexpsql_functionparams('concat', $arr, $mmodule);
}

function cbexpsql_coalesce($arr, $mmodule) {
	return __cbexpsql_functionparams('coalesce', $arr, $mmodule);
}

function cbexpsql_time_diff($arr, $mmodule) {
	if (count($arr) == 1) {
		$arr[1] = $arr[0];
		$arr[0] = new VTExpressionSymbol('now()', 'function'); // Current time
	}
	return __cbexpsql_functionparams('timediff', $arr, $mmodule);
}

function cbexpsql_time_diffdays($arr, $mmodule) {
	if (count($arr) == 1) {
		$arr[1] = $arr[0];
		$arr[0] = new VTExpressionSymbol('now()', 'function'); // Current time
	}
	return __cbexpsql_functionparams('datediff', $arr, $mmodule);
}

function cbexpsql_time_diffyears($arr, $mmodule) {
	if (count($arr) == 1) {
		$arr[1] = $arr[0];
		$arr[0] = new VTExpressionSymbol('now()', 'function'); // Current time
	}
	$arr[2] = $arr[1];
	$arr[1] = $arr[0];
	$arr[0] = new VTExpressionSymbol('YEAR', 'constant');
	return __cbexpsql_functionparams('TIMESTAMPDIFF', $arr, $mmodule);
}

function cbexpsql_networkdays($arr, $mmodule) {
	$s = $arr[0]->value;
	$e = $arr[1]->value;
	// https://stackoverflow.com/questions/1828948/mysql-function-to-find-the-number-of-working-days-between-two-dates
	// 0123444401233334012222340111123400001234000123440 > I increment one day to match our function
	return "(SELECT CASE
		WHEN '$e' < '$s' THEN -5 * (DATEDIFF('$e', '$s') DIV 7) + SUBSTRING('1234555512344445123333451222234511112345001234550', 7 * WEEKDAY('$e') + WEEKDAY('$s') + 1, 1)
		ELSE 5 * (DATEDIFF('$e', '$s') DIV 7) + SUBSTRING('1234555512344445123333451222234511112345001234550', 7 * WEEKDAY('$s') + WEEKDAY('$e') + 1, 1)
	END)";
}

function cbexpsql_add_days($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol($arr[1]);
	return __cbexpsql_functionparams('ADDDATE', $arr, $mmodule);
}

function cbexpsql_sub_days($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol($arr[1]);
	return __cbexpsql_functionparams('SUBDATE', $arr, $mmodule);
}

function cbexpsql_add_months($arr, $mmodule) {
	$val = is_object($arr[1]) ? $arr[1]->value : $arr[1];
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$val.' month', 'constant');
	return __cbexpsql_functionparams('DATE_ADD', $arr, $mmodule);
}

function cbexpsql_sub_months($arr, $mmodule) {
	$val = is_object($arr[1]) ? $arr[1]->value : $arr[1];
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$val.' month', 'constant');
	return __cbexpsql_functionparams('DATE_SUB', $arr, $mmodule);
}

function cbexpsql_add_time($arr, $mmodule) {
	$val = is_object($arr[1]) ? $arr[1]->value : $arr[1];
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$val.' MINUTE', 'constant');
	return __cbexpsql_functionparams('DATE_ADD', $arr, $mmodule);
}

function cbexpsql_sub_time($arr, $mmodule) {
	$val = is_object($arr[1]) ? $arr[1]->value : $arr[1];
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$val.' MINUTE', 'constant');
	return __cbexpsql_functionparams('DATE_SUB', $arr, $mmodule);
}

function cbexpsql_get_date($arr, $mmodule) {
	if (is_object($arr[0])) {
		$arr[0] = $arr[0]->value;
	}
	switch (strtolower($arr[0])) {
		case 'tomorrow':
			return __cbexpsql_functionparams('adddate', array(__cbexpsql_functionparams('CURDATE', array(), $mmodule),'1'), $mmodule);
			break;
		case 'yesterday':
			return __cbexpsql_functionparams('subdate', array(__cbexpsql_functionparams('CURDATE', array(), $mmodule),'1'), $mmodule);
			break;
		case 'time':
			return __cbexpsql_functionparams('CURTIME', array(), $mmodule);
			break;
		case 'today':
		default:
			return __cbexpsql_functionparams('CURDATE', array(), $mmodule);
			break;
	}
}

function cbexpsql_power($arr, $mmodule) {
	return __cbexpsql_functionparams('pow', $arr, $mmodule);
}

function cbexpsql_round($arr, $mmodule) {
	return __cbexpsql_functionparams('round', $arr, $mmodule);
}

function cbexpsql_ceil($arr, $mmodule) {
	return __cbexpsql_functionparams('CEILING', $arr, $mmodule);
}

function cbexpsql_floor($arr, $mmodule) {
	return __cbexpsql_functionparams('FLOOR', $arr, $mmodule);
}

function cbexpsql_modulo($arr, $mmodule) {
	return __cbexpsql_functionparams('MOD', $arr, $mmodule);
}

function cbexpsql_isstring($arr, $mmodule) {
	return __cbexpsql_functionparams("concat('',".__cbexpsql_functionparamsvalue($arr[0], $mmodule).'*1)!=', $arr, $mmodule);
}

function cbexpsql_isnumber($arr, $mmodule) {
	return __cbexpsql_functionparams("concat('',".__cbexpsql_functionparamsvalue($arr[0], $mmodule).'*1)=', $arr, $mmodule);
}

function cbexpsql_hash($arr, $mmodule) {
	if (count($arr)==1) {
		$arr[1] = 'sha1';
	}
	if (is_object($arr[1])) {
		$arr[1] = $arr[1]->value;
	}
	switch ($arr[1]) {
		case 'md5':
			$func = 'MD5';
			break;
		default:
			$func = 'SHA';
	}
	unset($arr[1]);
	return __cbexpsql_functionparams($func, $arr, $mmodule);
}

function cbexpsql_substring($arr, $mmodule) {
	return __cbexpsql_functionparams('SUBSTRING', $arr, $mmodule);
}

function cbexpsql_stringposition($arr, $mmodule) {
	return __cbexpsql_functionparams('INSTR', $arr, $mmodule);
}

function cbexpsql_stringlength($arr, $mmodule) {
	return __cbexpsql_functionparams('LENGTH', $arr, $mmodule);
}

function cbexpsql_stringreplace($arr, $mmodule) {
	return __cbexpsql_functionparams('REPLACE', $arr, $mmodule);
}

function cbexpsql_regexreplace($arr, $mmodule) {
	return __cbexpsql_functionparams('REGEXP_REPLACE', array($arr[2], $arr[0], $arr[1]), $mmodule);
}

function cbexpsql_randomstring($arr, $mmodule) {
	if (empty($arr) || empty($arr[0])) {
		$arr[0] = 10;
	}
	return 'SUBSTRING(HEX(CONCAT(NOW(), RAND(), UUID())), 1, '.$arr[0].')';
}

function cbexpsql_uppercase($arr, $mmodule) {
	return __cbexpsql_functionparams('UPPER', $arr, $mmodule);
}

function cbexpsql_lowercase($arr, $mmodule) {
	return __cbexpsql_functionparams('LOWER', $arr, $mmodule);
}

function cbexpsql_setype($arr, $mmodule) {
	$ret = '';
	if (!empty($arr[0])) {
		$crmid = __cbexpsql_functionparamsvalue($arr[0], $mmodule);
		if (is_string($crmid) && $crmid[0] == "'") {
			$crmid = trim($crmid, "'");
		}
		preg_match('/[0-9]+x[0-9]+/', $crmid, $crmidmatches);
		if (count($crmidmatches)>0) {
			list($void, $crmid) = explode('x', $crmid);
		}
		$ret = '(select setype from vtiger_crmobject where vtiger_crmobject.crmid='.$crmid.')';
	}
	return $ret;
}

function cbexpsql_getidof($arr, $mmodule) {
	global $current_user;
	$ret = '';
	if (!empty($arr[0])) {
		$mod = trim(__cbexpsql_functionparamsvalue($arr[0], $mmodule), "'");
		$fld = trim(__cbexpsql_functionparamsvalue($arr[1], $mmodule), "'");
		$val = trim(__cbexpsql_functionparamsvalue($arr[2], $mmodule), "'");
		$qg = new QueryGenerator($mod, $current_user);
		$qg->setFields(array('id'));
		$qg->addCondition($fld, $val, 'e');
		$ret = 'coalesce(('.$qg->getQuery(false, 1).'), 0)';
	}
	return $ret;
}

function cbexpsql_getsetting($arr, $mmodule) {
	$ret = '';
	if (!empty($arr[0])) {
		$skey = __cbexpsql_functionparamsvalue($arr[0], $mmodule);
		if (isset($arr[1])) {
			$default = __cbexpsql_functionparamsvalue($arr[1], $mmodule);
			if (is_string($default) && $default[0] == "'") {
				$default = trim($default, "'");
			}
		} else {
			$default = '';
		}
		$ret = 'coalesce((select setting_value from cb_settings where setting_key='.$skey.'), "'.$default.'")';
	}
	return $ret;
}

// Aggregations
function cbexpsql_aggregation($arr, $mmodule) {
	$arr[4] = new cbexpsql_environmentstub($mmodule, '0x::#');
	$arr[4]->returnReferenceValue = false;
	$return = __cb_aggregation_getQuery($arr, true);
	$mmod = CRMEntity::getInstance($mmodule);
	$return = str_replace($mmod->table_name.'.', $mmod->table_name.'aggop.', $return);
	$return = str_replace($mmod->table_name.' ', $mmod->table_name.' as '.$mmod->table_name.'aggop ', $return);
	$return = str_replace('::#', $mmod->table_name.'.'.$mmod->table_index, $return);
	return '('.$return.')';
}

function cbexpsql_aggregation_operation($arr, $mmodule) {
	return cbexpsql_aggregation($arr, $mmodule);
}

// aggregate_time(operation, relatedModuleName, relatedModuleField, conditions)
function cbexpsql_aggregate_time($arr, $mmodule) {
	array_unshift($arr, 'time_to_sec');
	return cbexpsql_aggregation($arr, $mmodule);
}

// Math operations
function cbexpsql_add($arr, $mmodule) {
	return __cbexpsql_mathparams('+', $arr, $mmodule);
}

function cbexpsql_sub($arr, $mmodule) {
	return __cbexpsql_mathparams('-', $arr, $mmodule);
}

function cbexpsql_mul($arr, $mmodule) {
	return __cbexpsql_mathparams('*', $arr, $mmodule);
}

function cbexpsql_div($arr, $mmodule) {
	return __cbexpsql_mathparams('/', $arr, $mmodule);
}

function cbexpsql_equals($arr, $mmodule) {
	if (count($arr)==2) {
		return ($arr[0]->type=='string' ? "'".$arr[0]->value."'" : $arr[0]->value).'='.($arr[1]->type=='string' ? "'".$arr[1]->value."'" : $arr[1]->value);
	} else {
		return 'TRUE';
	}
}

function cbexpsql_distinct($arr, $mmodule) {
	$rdo = __cbexpsql_mathparams('!=', $arr, $mmodule);
	return new VTExpressionSymbol('('.$rdo->value.')', 'constant');
}

function cbexpsql_ltequals($arr, $mmodule) {
	$rdo = __cbexpsql_mathparams('<=', $arr, $mmodule);
	return new VTExpressionSymbol('('.$rdo->value.')', 'constant');
}

function cbexpsql_gtequals($arr, $mmodule) {
	$rdo = __cbexpsql_mathparams('>=', $arr, $mmodule);
	return new VTExpressionSymbol('('.$rdo->value.')', 'constant');
}

function cbexpsql_lt($arr, $mmodule) {
	$rdo = __cbexpsql_mathparams('<', $arr, $mmodule);
	return new VTExpressionSymbol('('.$rdo->value.')', 'constant');
}

function cbexpsql_gt($arr, $mmodule) {
	$rdo = __cbexpsql_mathparams('>', $arr, $mmodule);
	return new VTExpressionSymbol('('.$rdo->value.')', 'constant');
}

// if-else
function cbexpsql_ifelse($arr, $mmodule) {
	$arr[0]->type = 'constant';
	return __cbexpsql_functionparams('IF', $arr, $mmodule);
}

function cbexpsql_sum($arr, $mmodule) {
	return __cbexpsql_functionparams('SUM', $arr, $mmodule);
}

function cbexpsql_min($arr, $mmodule) {
	return __cbexpsql_functionparams('MIN', $arr, $mmodule);
}

function cbexpsql_max($arr, $mmodule) {
	return __cbexpsql_functionparams('MAX', $arr, $mmodule);
}

function cbexpsql_avg($arr, $mmodule) {
	return __cbexpsql_functionparams('AVG', $arr, $mmodule);
}

function cbexpsql_count($arr, $mmodule) {
	return __cbexpsql_functionparams('COUNT', $arr, $mmodule);
}

function cbexpsql_groupconcat($arr, $mmodule) {
	return __cbexpsql_functionparams('GROUP_CONCAT', $arr, $mmodule);
}

function cbexpsql_number_format($arr, $mmodule) {
	if (!empty($arr)) {
		$number = $arr[0];
		$decimals = isset($arr[1]) ? $arr[1] : 0;
		$dec_points = isset($arr[2]) ? $arr[2] : '.';
		$thousands_sep = isset($arr[3]) ? $arr[3] : ',';
		return 'REPLACE(REPLACE(REPLACE(FORMAT('.__cbexpsql_functionparamsvalue($number, $mmodule).', '.$decimals.'), ".", "@"), ",", '.__cbexpsql_functionparamsvalue($thousands_sep, $mmodule).'), "@", '.__cbexpsql_functionparamsvalue($dec_points, $mmodule).')';
	}
	return '""';
}

//// UNSUPPORTED FUNCTIONS
function cbexpsql_uppercasefirst($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_uppercasewords($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getWeekdayDifference($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_next_date($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_format_date($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_next_dateLaborable($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_num2str($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_translate($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_globalvariable($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getimageurl($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getCurrentUserID($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getCurrentUserName($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getCurrentUserField($arr, $mmodule) {
	return 'TRUE';
}

function cbexpsql_getCRMIDFromWSID($arr, $mmodule) {
	return 'crmid';
}

function cbexpsql_average($arr, $mmodule) {
	$expression = __cbexpsql_functionparams('', $arr, $mmodule);
	$values = explode(',', trim($expression->value, '()'));
	$select = '(SELECT avg(nums) FROM (';
	foreach ($values as $exp) {
		$select .= '(select '.$exp.' as nums) union ';
	}
	return substr($select, 0, strlen($select)-7).') as setofnums)';
}

function cbexpsql_executesql($arr, $mmodule) {
	global $adb;
	$params = array();
	foreach (array_slice($arr, 1) as $value) {
		$params[] = trim(__cbexpsql_functionparamsvalue($value, $mmodule), "'");
	}
	return '('.$adb->convert2SQL(trim(__cbexpsql_functionparamsvalue($arr[0], $mmodule), "'"), $params).')';
}

function cbexpsql_getLatitude($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getLongitude($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getLongitudeLatitude($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistance($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromCompanyAddress($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromUserAddress($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromUser2AccountBilling($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromAssignUser2AccountBilling($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromUser2AccountShipping($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromAssignUser2AccountShipping($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromUser2ContactBilling($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromAssignUser2ContactBilling($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromUser2ContactShipping($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromAssignUser2ContactShipping($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_getGEODistanceFromCoordinates($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_or($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_and($arr, $mmodule) {
	return 'TRUE';
}
function cbexpsql_not($arr, $mmodule) {
	return 'FALSE';
}

class cbexpsql_environmentstub {
	private $crmid;
	private $module;
	private $data;
	public $returnReferenceValue = true;

	public function __construct($module, $crmid) {
		$this->crmid = $crmid;
		$this->module = $module;
		$this->data = array('id'=>$crmid);
	}

	public function getModuleName() {
		return $this->module;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function getId() {
		return $this->crmid;
	}

	public function get($fieldName) {
		preg_match('/\((\w+) : \(([_\w]+)\) (\w+)\)/', $fieldName, $matches);
		if ($this->returnReferenceValue && count($matches)>0) {
			global $current_user;
			$ct = new VTSimpleTemplateOnData($fieldName);
			$entityCache = new VTEntityCache($current_user);
			return $ct->render($entityCache, $this->module, $this->data);
		}
		return (isset($this->data[$fieldName]) ? $this->data[$fieldName] : $fieldName);
	}

	public function set($fieldName, $value) {
		$this->data[$fieldName] = $value;
	}

	public function getContext() {
		return $this->WorkflowContext;
	}

	public function setContext($WorkflowContext) {
		$this->WorkflowContext = $WorkflowContext;
	}
}
?>
