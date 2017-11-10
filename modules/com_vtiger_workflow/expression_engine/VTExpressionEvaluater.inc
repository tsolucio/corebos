<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

interface VTExpressionEnv{
	function get($var);
}

class VTFieldExpressionEvaluater{

	function __construct($expr) {
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
				'add_days' => '__vt_add_days',
				'sub_days' => '__vt_sub_days',
				'get_date' => '__vt_get_date',
				'get_nextdate' => '__cb_next_date',
				'get_nextdatelaborable' => '__cb_next_dateLaborable',
				'format_date' => '__cb_format_date',
				'add_time' => '__vt_add_time',
				'sub_time' => '__vt_sub_time',
				'substring' => '__vt_substring',
				'stringposition' => '__cb_stringposition',
				'stringlength' => '__cb_stringlength',
				'stringreplace' => '__cb_stringreplace',
				'uppercase' => '__vt_uppercase',
				'lowercase' => '__vt_lowercase',
				'uppercasefirst' => '__vt_uppercasefirst',
				'uppercasewords' => '__vt_uppercasewords',
				'round' => '__vt_round',
				'ceil' => '__vt_ceil',
				'floor' => '__vt_floor',
				'modulo' => '__cb_modulo',
				'aggregation' => '__cb_aggregation',
				'aggregation_fields_operation' => '__cb_aggregation_operation',
				'getCurrentUserID' => '__getCurrentUserID',
				'getCurrentUserName' => '__getCurrentUserName',
				'getCurrentUserField' => '__getCurrentUserField',
				'getEntityType' => '__cbwf_setype',
				'getLatitude' => '__cb_getLatitude',
				'getLongitude' => '__cb_getLongitude',
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
		$wfexpfuncs = $adb->query('select expname,funcname,rawparams from com_vtiger_workflows_expfunctions');
		while ($func = $adb->fetch_array($wfexpfuncs)) {
			$this->functions[$func['expname']] = $func['funcname'];
			if ($func['rawparams'] == 1) {
				$this->rawparams[] = $func['funcname'];
			}
		}
		$this->operations = array_merge($this->functions, $this->operators);
		$this->expr = $expr;
	}

	function evaluate($env){
		$this->env = $env;
		return $this->exec($this->expr);
	}

	function exec($expr){
		global $logbg;
		if($expr instanceof VTExpressionSymbol){
			$loginfo = print_r($expr,true);
			$logbg->debug("VTExpressionSymbol: $loginfo");
			$this->debug[] = $loginfo;
			return $this->env($expr);
		}else if($expr instanceof VTExpressionTreeNode){
			$op = $expr->getName();
			if($op->value=='if'){
				$params = $expr->getParams();
				$cond = $this->exec($params[0]);
				$loginfo = print_r($params,true);
				$logbg->debug("VTExpressionTreeNode: if $loginfo");
				$logbg->debug('VTExpressionTreeNode: condition => '.($cond?'true':'false'));
				$this->debug[] = $loginfo;
				$this->debug[] = $cond;
				if($cond){
					return $this->exec($params[1]);
				}else{
					return $this->exec($params[2]);
				}
			}else{
				$func = $this->operations[$op->value];
				if (in_array($func, $this->rawparams)) {
					$relto = $expr->getParams();
					$params = array();
					foreach ($relto as $key => $value) {
						if (is_object($value)) {
							$params[] = $this->env($value,true);
						} else {
							$params[] = $value;
						}
					}
				} elseif ($func == '__cb_aggregation' || $func == '__cb_aggregation_operation') {
					$params = array_map(array($this, 'exec'), $expr->getParams());
					if (count($params)==3) $params[] = ''; // no conditions
					$params[] = $this->env;
				} else {
					$params = array_map(array($this, 'exec'), $expr->getParams());
				}
				$loginfo = print_r($params,true);
				$logbg->debug("VTExpressionTreeNode: function $func $loginfo");
				$this->debug[] = $loginfo;
				return $func($params);
			}
		}else{
			$loginfo = print_r($expr,true);
			$logbg->debug("VTExpression: $loginfo");
			$this->debug[] = $loginfo;
			return $expr;
		}
	}

	function env($sym,$raw=false){
		if($this->env) {
			$val = $this->env->get($sym->value);
			if (isset($this->env->moduleName)) {
				$module = Vtiger_Module::getInstance($this->env->moduleName);
			} else {
				$module = Vtiger_Module::getInstance($this->env->getModuleName());
			}
			$field = Vtiger_Field::getInstance($sym->value,$module);
			if ($field and !$raw) {
				global $adb,$log;
				$rsfld = $adb->pquery('select * from vtiger_field where fieldid=?',array($field->id));
				$field = WebserviceField::fromQueryResult($adb, $rsfld, 0);
				$fieldType = $field->getFieldDataType();
				if ($fieldType=='reference' or $fieldType=='owner') {
					preg_match("/([0-9]+)x([0-9]+)/", $val, $results);
					if (count($results)>0) {  // if it has the form of a webserviceid
						$query = 'select name from vtiger_ws_entity where id = ?';
						$rs = $adb->pquery($query, array($results[1]));
						if ($rs and $adb->num_rows($rs)==1) {  // if the first part is a valid webservice entity id
							$modname = $adb->query_result($rs, 0, 'name');
							$modinfo = getEntityName($modname, array($results[2]));
							if (is_array($modinfo) and count($modinfo)>0) {  // if the second part is recognized as a module record
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
foreach (glob('modules/com_vtiger_workflow/expression_functions/*.{php}',GLOB_BRACE) as $fcode) {
	include $fcode;
}

?>