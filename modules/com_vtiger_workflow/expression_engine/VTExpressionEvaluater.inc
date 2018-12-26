<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

interface VTExpressionEnv {
	public function get($var);
}

class VTFieldExpressionEvaluater {

	public function __construct($expr) {
		global $adb;
		$this->debug = array();
		$this->operators = array(
			'+'  => '__vt_add',
			'-'  => '__vt_sub',
			'*'  => '__vt_mul',
			'/'  => '__vt_div',
			'==' => '__vt_equals',
			'!=' => '__cb_distinct',
			'<=' => '__vt_ltequals',
			'>=' => '__vt_gtequals',
			'<' => '__vt_lt',
			'>' => '__vt_gt',
		);
		$this->functions = array(
			'concat'=>'__vt_concat',
			'coalesce'=>'__cb_coalesce',
			'time_diff' => '__vt_time_diff',
			'time_diffdays' => '__vt_time_diffdays',
			'time_diffyears' => '__cb_time_diffyears',
			'time_diffweekdays' => '__cb_getWeekdayDifference',
			'aggregate_time' => '__cb_aggregate_time',
			'add_days' => '__vt_add_days',
			'sub_days' => '__vt_sub_days',
			'add_months' => '__vt_add_months',
			'sub_months' => '__vt_sub_months',
			'get_date' => '__vt_get_date',
			'get_nextdate' => '__cb_next_date',
			'get_nextdatelaborable' => '__cb_next_dateLaborable',
			'format_date' => '__cb_format_date',
			'add_time' => '__vt_add_time',
			'sub_time' => '__vt_sub_time',
			'power' => '__vt_power',
			'substring' => '__vt_substring',
			'stringposition' => '__cb_stringposition',
			'stringlength' => '__cb_stringlength',
			'stringreplace' => '__cb_stringreplace',
			'uppercase' => '__vt_uppercase',
			'lowercase' => '__vt_lowercase',
			'uppercasefirst' => '__vt_uppercasefirst',
			'uppercasewords' => '__vt_uppercasewords',
			'num2str' => '__cb_num2str',
			'translate' => '__cb_translate',
			'round' => '__vt_round',
			'ceil' => '__vt_ceil',
			'floor' => '__vt_floor',
			'modulo' => '__cb_modulo',
			'hash' => '__cb_hash',
			'globalvariable' => '__cb_globalvariable',
			'aggregation' => '__cb_aggregation',
			'aggregation_fields_operation' => '__cb_aggregation_operation',
			'getimageurl' => '__cbwf_getimageurl',
			'getCurrentUserID' => '__getCurrentUserID',
			'getCurrentUserName' => '__getCurrentUserName',
			'getCurrentUserField' => '__getCurrentUserField',
			'getEntityType' => '__cbwf_setype',
			'getLatitude' => '__cb_getLatitude',
			'getLongitude' => '__cb_getLongitude',
			'getLongitudeLatitude' => '__cb_getLongitudeLatitude',
			'getGEODistance' => '__cb_getGEODistance',
			'getGEODistanceFromCompanyAddress' => '__cb_getGEODistanceFromCompanyAddress',
			'getGEODistanceFromUserAddress' => '__cb_getGEODistanceFromUserAddress',
			'getGEODistanceFromUser2AccountBilling' => '__cb_getGEODistanceFromUser2AccountBilling',
			'getGEODistanceFromAssignUser2AccountBilling' => '__cb_getGEODistanceFromAssignUser2AccountBilling',
			'getGEODistanceFromUser2AccountShipping' => '__cb_getGEODistanceFromUser2AccountShipping',
			'getGEODistanceFromAssignUser2AccountShipping' => '__cb_getGEODistanceFromAssignUser2AccountShipping',
			'getGEODistanceFromUser2ContactBilling' => '__cb_getGEODistanceFromUser2ContactBilling',
			'getGEODistanceFromAssignUser2ContactBilling' => '__cb_getGEODistanceFromAssignUser2ContactBilling',
			'getGEODistanceFromUser2ContactShipping' => '__cb_getGEODistanceFromUser2ContactShipping',
			'getGEODistanceFromAssignUser2ContactShipping' => '__cb_getGEODistanceFromAssignUser2ContactShipping',
			'getGEODistanceFromCoordinates' => '__cb_getGEODistanceFromCoordinates'
		);
		$this->operationsSQL = array(
			'+'  => 'cbexpsql_add',
			'-'  => 'cbexpsql_sub',
			'*'  => 'cbexpsql_mul',
			'/'  => 'cbexpsql_div',
			'==' => 'cbexpsql_equals',
			'!=' => 'cbexpsql_distinct',
			'<=' => 'cbexpsql_ltequals',
			'>=' => 'cbexpsql_gtequals',
			'<' => 'cbexpsql_lt',
			'>' => 'cbexpsql_gt',
			'ifelse' => 'cbexpsql_ifelse',
			'concat'=>'cbexpsql_concat',
			'coalesce'=>'cbexpsql_coalesce',
			'time_diff' => 'cbexpsql_time_diff',
			'time_diffdays' => 'cbexpsql_time_diffdays',
			'time_diffyears' => 'cbexpsql_time_diffyears',
			'time_diffweekdays' => 'cbexpsql_getWeekdayDifference',
			'aggregate_time' => 'cbexpsql_aggregate_time',
			'add_days' => 'cbexpsql_add_days',
			'sub_days' => 'cbexpsql_sub_days',
			'add_months' => 'cbexpsql_add_months',
			'sub_months' => 'cbexpsql_sub_months',
			'get_date' => 'cbexpsql_get_date',
			'get_nextdate' => 'cbexpsql_next_date',
			'get_nextdatelaborable' => 'cbexpsql_next_dateLaborable',
			'format_date' => 'cbexpsql_format_date',
			'add_time' => 'cbexpsql_add_time',
			'sub_time' => 'cbexpsql_sub_time',
			'power' => 'cbexpsql_power',
			'substring' => 'cbexpsql_substring',
			'stringposition' => 'cbexpsql_stringposition',
			'stringlength' => 'cbexpsql_stringlength',
			'stringreplace' => 'cbexpsql_stringreplace',
			'uppercase' => 'cbexpsql_uppercase',
			'lowercase' => 'cbexpsql_lowercase',
			'uppercasefirst' => 'cbexpsql_uppercasefirst',
			'uppercasewords' => 'cbexpsql_uppercasewords',
			'num2str' => 'cbexpsql_num2str',
			'translate' => 'cbexpsql_translate',
			'round' => 'cbexpsql_round',
			'ceil' => 'cbexpsql_ceil',
			'floor' => 'cbexpsql_floor',
			'modulo' => 'cbexpsql_modulo',
			'hash' => 'cbexpsql_hash',
			'globalvariable' => 'cbexpsql_globalvariable',
			'aggregation' => 'cbexpsql_aggregation',
			'aggregation_fields_operation' => 'cbexpsql_aggregation_operation',
			'getimageurl' => 'cbexpsql_getimageurl',
			'getCurrentUserID' => 'cbexpsql_getCurrentUserID',
			'getCurrentUserName' => 'cbexpsql_getCurrentUserName',
			'getCurrentUserField' => 'cbexpsql_getCurrentUserField',
			'getEntityType' => 'cbexpsql_setype',
			'getLatitude' => 'cbexpsql_getLatitude',
			'getLongitude' => 'cbexpsql_getLongitude',
			'getLongitudeLatitude' => 'cbexpsql_getLongitudeLatitude',
			'getGEODistance' => 'cbexpsql_getGEODistance',
			'getGEODistanceFromCompanyAddress' => 'cbexpsql_getGEODistanceFromCompanyAddress',
			'getGEODistanceFromUserAddress' => 'cbexpsql_getGEODistanceFromUserAddress',
			'getGEODistanceFromUser2AccountBilling' => 'cbexpsql_getGEODistanceFromUser2AccountBilling',
			'getGEODistanceFromAssignUser2AccountBilling' => 'cbexpsql_getGEODistanceFromAssignUser2AccountBilling',
			'getGEODistanceFromUser2AccountShipping' => 'cbexpsql_getGEODistanceFromUser2AccountShipping',
			'getGEODistanceFromAssignUser2AccountShipping' => 'cbexpsql_getGEODistanceFromAssignUser2AccountShipping',
			'getGEODistanceFromUser2ContactBilling' => 'cbexpsql_getGEODistanceFromUser2ContactBilling',
			'getGEODistanceFromAssignUser2ContactBilling' => 'cbexpsql_getGEODistanceFromAssignUser2ContactBilling',
			'getGEODistanceFromUser2ContactShipping' => 'cbexpsql_getGEODistanceFromUser2ContactShipping',
			'getGEODistanceFromAssignUser2ContactShipping' => 'cbexpsql_getGEODistanceFromAssignUser2ContactShipping',
			'getGEODistanceFromCoordinates' => 'cbexpsql_getGEODistanceFromCoordinates',
			'sum' => 'cbexpsql_sum',
			'min' => 'cbexpsql_min',
			'max' => 'cbexpsql_max',
			'avg' => 'cbexpsql_avg',
			'count' => 'cbexpsql_count'
		);
		$this->rawparams = array(
			'__cb_getGEODistanceFromUser2AccountBilling',
			'__cb_getGEODistanceFromAssignUser2AccountBilling',
			'__cb_getGEODistanceFromUser2AccountShipping',
			'__cb_getGEODistanceFromAssignUser2AccountShipping',
			'__cb_getGEODistanceFromUser2ContactBilling',
			'__cb_getGEODistanceFromAssignUser2ContactBilling',
			'__cb_getGEODistanceFromUser2ContactShipping',
			'__cb_getGEODistanceFromAssignUser2ContactShipping',
			'__cbwf_setype',
		);
		$this->needscontext = array(
			'__cb_aggregation',
			'__cb_aggregation_operation',
			'__cbwf_getimageurl',
			'__cb_aggregate_time',
			'cbexpsql_aggregation',
		);
		$wfexpfuncs = $adb->query('select expname,funcname,rawparams,needscontext from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$this->functions[$func['expname']] = $func['funcname'];
			if ($func['rawparams'] == 1) {
				$this->rawparams[] = $func['funcname'];
			}
			if ($func['needscontext'] == 1) {
				$this->needscontext[] = $func['funcname'];
			}
		}
		$this->operations = array_merge($this->functions, $this->operators);
		$this->expr = $expr;
	}

	public function evaluate($env, $getSQL = false) {
		$this->env = $env;
		return ($getSQL ? $this->execSQL($this->expr) : $this->exec($this->expr));
	}

	public function exec($expr) {
		global $logbg;
		if ($expr instanceof VTExpressionSymbol) {
			$loginfo = print_r($expr, true);
			$logbg->debug("VTExpressionSymbol: $loginfo");
			$this->debug[] = $loginfo;
			return $this->env($expr);
		} elseif ($expr instanceof VTExpressionTreeNode) {
			$op = $expr->getName();
			if ($op->value=='if') {
				$params = $expr->getParams();
				$cond = $this->exec($params[0]);
				$loginfo = print_r($params, true);
				$logbg->debug("VTExpressionTreeNode: if $loginfo");
				$logbg->debug('VTExpressionTreeNode: condition => '.($cond?'true':'false'));
				$this->debug[] = $loginfo;
				$this->debug[] = $cond;
				if ($cond) {
					return $this->exec($params[1]);
				} else {
					return $this->exec($params[2]);
				}
			} else {
				$func = $this->operations[$op->value];
				if (in_array($func, $this->rawparams)) {
					$relto = $expr->getParams();
					$params = array();
					foreach ($relto as $value) {
						if (is_object($value)) {
							$params[] = $this->env($value, true);
						} else {
							$params[] = $value;
						}
					}
				} elseif ($func == '__cb_aggregation' || $func == '__cb_aggregation_operation') {
					$params = array_map(array($this, 'exec'), $expr->getParams());
					if (count($params)==3) {
						$params[] = ''; // no conditions
					}
				} else {
					$params = array_map(array($this, 'exec'), $expr->getParams());
				}
				$debugparams = $params;
				if (in_array($func, $this->needscontext)) {
					$debugparams[] = 'context is added';
					$params[] = $this->env;
				}
				$loginfo = print_r($debugparams, true);
				$logbg->debug("VTExpressionTreeNode: function $func $loginfo");
				$this->debug[] = $loginfo;
				return $func($params);
			}
		} else {
			$loginfo = print_r($expr, true);
			$logbg->debug("VTExpression: $loginfo");
			$this->debug[] = $loginfo;
			return $expr;
		}
	}

	public function execSQL($expr) {
		global $logbg;
		if ($expr instanceof VTExpressionSymbol) {
			$loginfo = print_r($expr, true);
			$logbg->debug("VTExpressionSymbol: $loginfo");
			$this->debug[] = $loginfo;
			if ($expr->type == 'math') {
				return '('.$expr->value.')';
			} else {
				return $this->env($expr);
			}
		} elseif ($expr instanceof VTExpressionTreeNode) {
			$op = $expr->getName();
			$func = $this->operationsSQL[$op->value];
			if (in_array($func, $this->rawparams)) {
				$relto = $expr->getParams();
				$params = array();
				foreach ($relto as $value) {
					if (is_object($value)) {
						$params[] = $this->env($value, true);
					} else {
						$params[] = $value;
					}
				}
			} elseif ($func == 'cbexpsql_aggregation' || $func == 'cbexpsql_aggregation_operation' || $func == 'cbexpsql_aggregate_time') {
				$params = array_map(array($this, 'exec'), $expr->getParams());
				if (count($params)==3) {
					$params[] = ''; // no conditions
				}
			} else {
				$params = array_map(array($this, 'execSQL'), $expr->getParams());
				$prms = array();
				foreach ($params as $pval) {
					if (!is_object($pval)) {
						preg_match('/[\+\-\/\*<>=]/', $pval, $parammatches);
						if (count($parammatches)>0 && in_array($op->value, array('+', '-', '*', '/'))) {
							$prms[] = new VTExpressionSymbol('('.$pval.')', 'constant');
						} elseif (trim($pval)=='' || is_numeric($pval)) {
							$prms[] = $pval;
						} else {
							$parentparams = $expr->getParams();
							if (false && $parentparams[0] instanceof VTExpressionTreeNode && isset($this->operationsSQL[$parentparams[0]->arr[0]->value])) {
								$prms[] = new VTExpressionSymbol($pval, 'constant');
							} else {
								$prms[] = new VTExpressionSymbol($pval);
							}
						}
					} else {
						if ($pval instanceof VTExpressionSymbol && $pval->type == 'math') {
							$pval->value = '('.$pval->value.')';
						}
						$prms[] = $pval;
					}
				}
				$params = $prms;
				//$params = $expr->getParams();
			}
			$debugparams = $params;
			if (in_array($func, $this->needscontext)) {
				$debugparams[] = 'context is added';
				$params[] = $this->env;
			}
			$loginfo = print_r($debugparams, true);
			$logbg->debug("VTExpressionTreeNode: function $func $loginfo");
			$this->debug[] = $loginfo;
			if (isset($this->env->moduleName)) {
				$mainmodule = $this->env->moduleName;
			} elseif ($this->env) {
				$mainmodule = $this->env->getModuleName();
			} else {
				$mainmodule = '';
			}
			return $func($params, $mainmodule);
		} else {
			$loginfo = print_r($expr, true);
			$logbg->debug("VTExpression: $loginfo");
			$this->debug[] = $loginfo;
			return $expr;
		}
	}

	public function env($sym, $raw = false) {
		if ($this->env) {
			$val = $this->env->get($sym->value);
			if (isset($this->env->moduleName)) {
				$module = Vtiger_Module::getInstance($this->env->moduleName);
			} else {
				$module = Vtiger_Module::getInstance($this->env->getModuleName());
			}
			$field = Vtiger_Field::getInstance($sym->value, $module);
			if ($field && !$raw) {
				global $adb;
				$rsfld = $adb->pquery('select * from vtiger_field where fieldid=?', array($field->id));
				$field = WebserviceField::fromQueryResult($adb, $rsfld, 0);
				$fieldType = $field->getFieldDataType();
				if ($fieldType=='reference' || $fieldType=='owner') {
					preg_match("/([0-9]+)x([0-9]+)/", $val, $results);
					if (count($results)>0) {  // if it has the form of a webserviceid
						$query = 'select name from vtiger_ws_entity where id = ?';
						$rs = $adb->pquery($query, array($results[1]));
						if ($rs && $adb->num_rows($rs)==1) {  // if the first part is a valid webservice entity id
							$modname = $adb->query_result($rs, 0, 'name');
							$modinfo = getEntityName($modname, array($results[2]));
							if (is_array($modinfo) && count($modinfo)>0) {  // if the second part is recognized as a module record
								$val = $modinfo[$results[2]];
							}
						}
					}
				}
			}
			return $val;
		} else {
			return $sym->value;
		}
	}
}

// Load expression functions
foreach (glob('modules/com_vtiger_workflow/expression_functions/*.{php}', GLOB_BRACE) as $fcode) {
	include $fcode;
}
?>