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

function cbexpsql_add_days($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol($arr[1]);
	return __cbexpsql_functionparams('ADDDATE', $arr, $mmodule);
}

function cbexpsql_sub_days($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol($arr[1]);
	return __cbexpsql_functionparams('SUBDATE', $arr, $mmodule);
}

function cbexpsql_add_months($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$arr[1].' month', 'constant');
	return __cbexpsql_functionparams('DATE_ADD', $arr, $mmodule);
}

function cbexpsql_sub_months($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$arr[1].' month', 'constant');
	return __cbexpsql_functionparams('DATE_SUB', $arr, $mmodule);
}

function cbexpsql_add_time($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$arr[1].' MINUTE', 'constant');
	return __cbexpsql_functionparams('DATE_ADD', $arr, $mmodule);
}

function cbexpsql_sub_time($arr, $mmodule) {
	$arr[1] = new VTExpressionSymbol('INTERVAL '.$arr[1].' MINUTE', 'constant');
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
		if ($crmid[0] == "'") {
			$crmid = trim($crmid, "'");
		}
		preg_match('/[0-9]+x[0-9]+/', $crmid, $crmidmatches);
		if (count($crmidmatches)>0) {
			list($void, $crmid) = explode('x', $crmid);
		}
		$ret = '(select setype from vtiger_crmentity where vtiger_crmentity.crmid='.$crmid.')';
	}
	return $ret;
}

// Aggregations
function cbexpsql_aggregation($arr, $mmodule) {
	$arr[4] = new cbexpsql_environmentstub($mmodule, '0x::#');
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
	return 'TRUE';
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

class cbexpsql_environmentstub {
	private $crmid;
	private $module;
	private $data;

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

	public function get($value) {
		return $value;
	}
}
?>
