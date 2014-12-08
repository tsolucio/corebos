<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/

class VTExpressionException extends Exception{}

class VTThunk{
	function __construct($env, $expr){
		$this->expr = new VTExpressionEvaluater($expr);
		$this->env = $env;
	}

	function get(){
		if(!isset($this->val)){
			$this->val = $this->expr->evaluate($this->env);
		}
		return $this->val;
	}
}

class VTExpressionEngineEnv implements VTEnv{
	function __construct($data){
		$this->data = $data;
		$this->vars = array();
	}

	function bind($name, $expr){
		$this->vars[$name] = new VTThunk($this, $expr);
	}

	function get($var){
		if(array_key_exists($var, $this->vars)){
			return $this->vars[$var]->get();
		}else{
			return $this->data[$var];
		}
	}
}

class VTExpressionEngine{
	function __construct(){
		$this->data = array();
		$this->expressions = array();
		$this->unparsedExpressions = array();
		$this->vars = array();
	}


	/**
	 * Load dynamic fields and their expressions
	 *
	 */
	function loadExpressions($arr){
		$this->unparsedExpressions = array_merge($this->unparsedExpressions, $arr);
		$this->expressions = array_merge($this->expressions, array_map(array($this, 'expression'), $arr));
		$this->regenerate();
	}

	function removeExpression($fieldName){
		unset($this->unparsedExpressions[$fieldName]);
		unset($this->expressions[$fieldName]);
		$this->regenerate();
	}

	private function regenerate(){
		$this->vars = array_keys($this->expressions);
			
		$dyn_vars = array();
		foreach($this->expressions as $var => $expr){
			$syms = array_keys($this->getSyms($expr));
			$dyn_vars[$var] = $syms;
		}
			
		$independents = array_keys(array_filter($dyn_vars, array($this, 'isEmpty')));
		$arr = array();
		foreach($dyn_vars as $var => $parents){
			foreach($parents as $parent){
				$arr[$parent][$var] = $var;
			}
		}
		$dependents = array_map('array_keys', $arr);
		$this->independents = $independents;
		$this->dependents = $dependents;
		$this->checkForCycles();
	}


	/**
	 * Evaluate the expressions using the data in the array
	 * provided.
	 *
	 * @param $data An array of bound variables containing the
	 *              the variable name as the key and the value as data.
	 * @return An array containing the variables bound to the expressions
	 *         and their values.
	 */
	function evaluate($data){
		$env = new VTExpressionEngineEnv($data);
		foreach($this->expressions as $var=>$expr){
			$env->bind($var, $expr);
		}
			
		$out = array();
		foreach($this->vars as $var){
			$out[$var] = $env->get($var);
		}
		return $out;
	}

	private function checkForCycles(){
		foreach($this->independents as $independent){
			$this->testCall($independent, array(), sizeof($this->dependents));
		}
	}

	private function testCall($cur, $stack, $n){
		if($n<0){
			throw new VTExpressionException('There appears to be a loop in ('.implode(", ", $stack).')');
		}
		$dependents = $this->dependents;
		if(array_key_exists($cur, $dependents)){
			foreach($dependents[$cur] as $var){
				$this->testCall($var, array_merge($stack, array($cur)), $n-1);
			}
		}
	}

	private function getSyms($expr){
		if($expr instanceof VTTreeNode){
			$params = $expr->getParams();
			if(sizeof($params)!=0){
				$sym_arr = array_map(array($this, "getSyms"), $params);
				if(sizeof($sym_arr)>1){
					$syms = call_user_func_array('array_merge', $sym_arr);

				}else{
					$syms = $sym_arr[0];
				}
			}else{
				$syms = array();
			}
			return $syms;
		}else if($expr instanceof Symbol && in_array($expr->value, $this->vars)){
			return array($expr->value=>$expr);
		}else{
			return array();
		}
	}

	private function loadExpression($var, $expr){
		$this->expressions[$var] = $value;
	}

	private function parse($str){
		$parser = new VTParser(new SpaceFilter(new VTTokenizer($str)));
		return $parser->expression();
	}

	private function isEmpty($arr){
		return sizeof($arr)!=0;
	}

	private function expression($str){
		$parser = new VTParser(new SpaceFilter(new VTTokenizer($str)));
		return $parser->expression();
	}
}
?>