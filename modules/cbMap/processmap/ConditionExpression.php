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

 *************************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
require_once 'include/Webservices/Retrieve.php';

class ConditionExpression extends processcbMap {

	public function processMap($arguments) {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		$entityId = $arguments[0];
		$holduser = $current_user;
		$current_user = Users::getActiveAdminUser(); // evaluate condition as admin user
		if (!empty($entityId)) {
			$entity = new VTWorkflowEntity($current_user, $entityId, true);
			if (is_null($entity->data)) { // invalid context
				return false;
			}
		}
		$current_user = $holduser;
		if (isset($xml->expression)) {
			$testexpression = (String)$xml->expression;
			$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
			$expression = $parser->expression();
			$exprEvaluater = new VTFieldExpressionEvaluater($expression);
			$exprEvaluation = $exprEvaluater->evaluate($entity);
		} elseif (isset($xml->function)) {
			if (!empty($entity->data)) {
				list($void,$entity->data['record_id']) = explode('x', $entity->data['id']);
				$entity->data['record_module'] = $entity->getModuleName();
			}
			$function = (String)$xml->function->name;
			$testexpression = '$exprEvaluation = ' . $function . '(';
			if (isset($xml->function->parameters) && isset($xml->function->parameters->parameter)) {
				foreach ($xml->function->parameters->parameter as $k => $v) {
					if (isset($entity->data[(String)$v])) {
						$testexpression.= "'" . $entity->data[(String)$v] . "',";
					} elseif (isset($GLOBALS[(String)$v])) {
						$testexpression.= "'" . $GLOBALS[(String)$v] . "',";
					} else {
						$testexpression.= "'" . (String)$v . "',";
					}
				}
			}
			$testexpression = trim($testexpression, ',') . ');';
			eval($testexpression);
		}
		return $exprEvaluation;
	}
}
?>