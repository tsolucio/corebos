<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *  Module       : Business Mappings:: Condition Expression
 *  Version      : 1.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is a direct expression from the workflow expression engine
 <map>
  <expression>uppercase('this string')</expression>
 </map>

 <map>
  <expression>accountname</expression>
 </map>

 <map>
  <expression>employees + 10</expression>
 </map>

 <map>
  <expression>if employees > 10 then 'true' else 'false' end</expression>
 </map>

 <map>
  <function>
	<name>isPermitted</name>
	<parameters>
		<parameter>Accounts</parameter>
		<parameter>CreateView</parameter>
		<parameter>record_id</parameter>
	</parameters>
  </function>
 </map>

 <map>
	<template>The user assigned to the record is: $(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name)</template>
 </map>
 *************************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/com_vtiger_workflow/expression_functions/cbexpSQL.php';

class ConditionExpression extends processcbMap {

	public function processMap($arguments) {
		global $current_user;
		$xml = $this->getXMLContent();
		$holduser = $current_user;
		$current_user = Users::getActiveAdminUser(); // in order to retrieve all entity data for evaluation
		if (is_array($arguments[0])) {
			$entityId = empty($arguments[0]['record_id']) ? 0 : $arguments[0]['record_id'];
		} else {
			$entityId = $arguments[0];
		}
		if (!empty($entityId)) {
			$entity = new VTWorkflowEntity($current_user, $entityId, true);
			if (is_null($entity->data) && !is_array($arguments[0])) { // invalid context
				$current_user = $holduser;
				return false;
			}
			if (is_array($arguments[0])) {
				$entity->setData(array_merge($entity->data, $arguments[0]));
			}
		} else {
			if (empty($arguments[0]['module']) && empty($_REQUEST['module'])) {
				$mapFields = $this->getMap();
				$inModule = $mapFields->column_fields['targetname'];
			} else {
				$inModule = empty($arguments[0]['module']) ? vtlib_purify($_REQUEST['module']) : $arguments[0]['module'];
			}
			$entity = new cbexpsql_environmentstub($inModule, 0);
			$entity->setData($arguments[0]);
		}
		$current_user = $holduser;
		if (isset($xml->expression)) {
			$testexpression = (string)$xml->expression;
			$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
			$expression = $parser->expression();
			$exprEvaluater = new VTFieldExpressionEvaluater($expression);
			$exprEvaluation = $exprEvaluater->evaluate($entity);
		} elseif (isset($xml->function)) {
			if (!empty($entity->data) && !empty($entity->data['id'])) {
				list($void,$entity->data['record_id']) = explode('x', $entity->data['id']);
				$entity->data['record_module'] = $entity->getModuleName();
			}
			$function = (string)$xml->function->name;
			$params = array();
			if (isset($xml->function->parameters) && isset($xml->function->parameters->parameter)) {
				$GLOBALS['currentuserID'] = $current_user->id;
				foreach ($xml->function->parameters->parameter as $v) {
					if (isset($entity->data[(string)$v])) {
						$params[] = $entity->data[(string)$v];
					} elseif (isset($GLOBALS[(string)$v])) {
						$params[] = $GLOBALS[(string)$v];
					} else {
						$params[] = (string)$v;
					}
				}
				unset($GLOBALS['currentuserID']);
			}
			$exprFunction = function ($f, $p) {
				return call_user_func_array($f, $p);
			};
			$exprEvaluation = $exprFunction($function, $params);
		} elseif (isset($xml->template)) {
			$testexpression = (string)$xml->template;
			$entityCache = new VTEntityCache($current_user);
			$ct = new VTSimpleTemplate($testexpression);
			$exprEvaluation = $ct->render($entityCache, $entityId);
		}
		return $exprEvaluation;
	}
}
?>