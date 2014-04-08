<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
interface VTEnv{
	function get($var);
}

function _vt_add($arr){
	if(sizeof($arr)==1){
		return $arr[0];
	}else{
		return $arr[0]+$arr[1];
	}
}

function _vt_sub($arr){
	if(sizeof($arr)==1){
		return -$arr[0];
	}else{
		return $arr[0]-$arr[1];
	}
}

function _vt_mul($arr){
	return $arr[0]*$arr[1];
}

function _vt_div($arr){
	return $arr[0]/$arr[1];
}

function _vt_equals($arr){
	return $arr[0] == $arr[1];
}

function _vt_ltequals($arr) {
	return $arr[0] <= $arr[1];
}

function _vt_gtequals($arr) {
	return $arr[0] >= $arr[1];
}

function _vt_lt($arr) {
	return $arr[0] < $arr[1];
}

function _vt_gt($arr) {
	return $arr[0] > $arr[1];
}

function _vt_concat($arr){
	return implode($arr);
}
/* Date difference between (input times) or (current time and input time)
 *
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int difference timestamp
 */

function _vt_time_diff($arr) {

    $time_operand1 = $time_operand2 = 0;
    if(count($arr) > 1) {
            $time_operand1 = $arr[0];
            $time_operand2 = $arr[1];
    } else {
            $time_operand1 = date('Y-m-d H:i:s'); // Current time
            $time_operand2 = $arr[0];
    }

    if(empty($time_operand1) || empty($time_operand2)) return 0;

    $time_operand1 = getValidDBInsertDateValue($time_operand1);
	$time_operand2 = getValidDBInsertDateValue($time_operand2);

    return (strtotime($time_operand1) - strtotime($time_operand2));
}
/**
 * Calculate the time difference (input times) or (current time and input time) and
 * convert it into number of days.
 * @param Array $a $a[0] - Input time1, $a[1] - Input time2
 * (if $a[1] is not available $a[0] = Current Time, $a[1] = Input time1)
 * @return int number of days
 */
function _vt_time_diffdays($arr) {
    $timediff  = _vt_time_diff($arr);
    $days_diff = floor($timediff / (60 * 60 * 24));
    return $days_diff;
}


/** END **/
class VTExpressionEvaluater{
	function __construct($expr){
			
		$this->operators = array(
				'+'  => '_vt_add',
				'-'  => '_vt_sub',
				'*'  => '_vt_mul',
				'/'  => '_vt_div',
				'==' => '_vt_equals',
				'<=' => '_vt_ltequals',
				'>=' => '_vt_gtequals',
				'<' => '_vt_lt',
				'>' => '_vt_gt',
		);
		$this->functions = array(
				'concat'=>'_vt_concat',
				'time_diff' => '_vt_time_diff',
 	            'time_diffdays' => '_vt_time_diffdays'
				);
					
				$this->operations = array_merge($this->functions, $this->operators);
				$this->expr = $expr;
					
	}

	function evaluate($env){
		$this->env = $env;
		return $this->exec($this->expr);
	}

	function exec($expr){
		if($expr instanceof Symbol){
			return $this->env($expr);
		}else if($expr instanceof VTTreeNode){
			$op = $expr->getName();
			if($op->value=='if'){
				$params = $expr->getParams();
				$cond = $this->exec($params[0]);
				if($cond){
					return $this->exec($params[1]);
				}else{
					return $this->exec($params[2]);
				}
			}else{
				$params = array_map(array($this, 'exec'), $expr->getParams());
				$func = $this->operations[$op->value];
				return $func($params);
			}
		}else{
			return $expr;
		}
	}

	function env($sym){
		return $this->env->get($sym->value);
	}
}
?>