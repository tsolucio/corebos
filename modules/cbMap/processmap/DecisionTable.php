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
 *  Module       : Business Mappings:: DecisionTable
 *  Version      : 5.4.0
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************
 * The accepted format is:
<decision>
<hitPolicy></hitPolicy>  <!-- U F C A R G -->
<aggregate></aggregate>  <!-- only available if hitPolicy=G: sum,min,max,count -->
<rules>
<rule>
  <sequence></sequence>
  <expression></expression>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
<rule>
  <sequence></sequence>
  <mapid></mapid>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
<rule>
  <sequence></sequence>
  <decisionTable>
	<module></module>
	<conditions>  <!-- QueryGenerator conditions -->
		<condition>
		  <input></input>  <!-- context variable name -->
		  <operation></operation>  <!-- QueryGenerator operators -->
		  <field></field>  <!-- fieldname of module -->
		</condition>
	</conditions>
	<orderby></orderby>  <!-- column to order the records by -->
	<searches>
	  <search>
		<condition>
		  <input></input>  <!-- context variable name -->
		  <operation></operation>  <!-- QueryGenerator operators -->
		  <field></field>  <!-- fieldname of module -->
		</condition>
	  </search>
	</searches>
	<output></output>  <!-- fieldname -->
  </decisionTable>
  <output></output>  <!-- ExpressionResult, FieldValue, crmObject -->
</rule>
</rules>
</decision>
 *************************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc';
require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
require_once 'modules/com_vtiger_workflow/VTSimpleTemplate.inc';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';

class DecisionTable extends processcbMap {

	public function processMap($arguments) {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		$entityId = $arguments[0];
		$holduser = $current_user;
		$current_user = Users::getActiveAdminUser(); // in order to retrieve all entity data for evaluation
		if (!empty($entityId)) {
			$entity = new VTWorkflowEntity($current_user, $entityId, true);
			if (is_null($entity->data)) { // invalid context
				$current_user = $holduser;
				return false;
			}
		}
		$current_user = $holduser;
		$outputs = array();
		$hitpolicy = (String)$xml->hitPolicy;
		if ($hitpolicy == 'G') {
			$aggregate = (String)$xml->aggregate;
		}
		foreach ($xml->rules->rule as $key => $value) {
			$sequence = (String)$value->sequence;
			$eval = "";
			if (isset($value->expression)) {
				$testexpression = (String)$value->expression;
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$exprEvaluation = $exprEvaluater->evaluate($entity);
				$eval = $exprEvaluation;
			} elseif (isset($value->mapid)) {
				$mapid = (String)$value->mapid;
				$eval = coreBOS_Rule::evaluate($mapid, $entityId);
			} elseif (isset($value->decisionTable)) {
				$module = (String)$value->decisionTable->module;
				$queryGenerator = new QueryGenerator($module, $current_user);
				if (isset($value->decisionTable->conditions)) {
					foreach ($value->decisionTable->conditions->condition as $k => $v) {
						$queryGenerator->addCondition((String)$v->field, (String)$v->input, (String)$v->operation, $queryGenerator::$AND);
					}
				}
				if (isset($value->decisionTable->searches)) {
					foreach ($value->decisionTable->searches->search as $k => $v) {
						foreach ($v->condition as $k => $v) {
							$queryGenerator->addCondition((String)$v->field, (String)$v->input, (String)$v->operation, $queryGenerator::$AND);
						}
					}
				}
				$output = (String)$value->decisionTable->output;
				$queryGenerator->setFields(array($output));
				$query = $queryGenerator->getQuery();
				$result = $adb->pquery($query, array());
				if ($result) {
					$row = $adb->fetch_array($result);
					if (isset($row[$output])) {
						$eval = $row[$output];
					}
				}
			}
			$ruleOutput = (String)$value->output;
			if ($ruleOutput == 'ExpressionResult') {
				$outputs[] = $eval;
			} elseif ($ruleOutput == 'crmObject') {
				$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
				$crmobj->retrieve_entity_info($eval);
				$outputs[] = $crmobj;
			} elseif ($ruleOutput == 'FieldValue') {
				$outputs[] = $entity->data[$eval];
			} else {
				$outputs[] = '__DoesNotPass__';
			}
		}
		return $outputs;
	}
}