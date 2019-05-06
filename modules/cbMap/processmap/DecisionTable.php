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

	public function processMap($ctx) {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		$context = $ctx[0];
		$holduser = $current_user;
		$current_user = Users::getActiveAdminUser(); // in order to retrieve all entity data for evaluation
		if (!empty($context['record_id'])) {
			if (strpos($context['record_id'], 'x')===false) {
				$context['record_id'] = vtws_getEntityId(getSalesEntityType($context['record_id'])).'x'.$context['record_id'];
			}
			$entity = new VTWorkflowEntity($current_user, $context['record_id'], true);
			if (is_array($entity->data)) { // valid context
				$context = array_merge($entity->data, $context);
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
			$ruleOutput = (String)$value->output;
			$eval = '';
			if (isset($value->expression)) {
				$testexpression = (String)$value->expression;
				if (is_array($context)) {
					foreach ($context as $key => $value) {
						$testexpression = str_ireplace('$['.$key.']', $value, $testexpression);
					}
				}
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$exprEvaluation = $exprEvaluater->evaluate($entity);
				$eval = $exprEvaluation;
				if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
					$outputs[$sequence] = $eval;
				} elseif ($ruleOutput == 'crmObject') {
					$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
					$crmobj->retrieve_entity_info($eval);
					$outputs[$sequence] = $crmobj;
				} else {
					$outputs[$sequence] = '__DoesNotPass__';
				}
			} elseif (isset($value->mapid)) {
				$mapid = (String)$value->mapid;
				$eval = coreBOS_Rule::evaluate($mapid, $context);
				if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
					$outputs[$sequence] = $eval;
				} elseif ($ruleOutput == 'crmObject') {
					$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
					$crmobj->retrieve_entity_info($eval);
					$outputs[$sequence] = $crmobj;
				} else {
					$outputs[$sequence] = '__DoesNotPass__';
				}
			} elseif (isset($value->decisionTable)) {
				$module = (String)$value->decisionTable->module;
				$queryGenerator = new QueryGenerator($module, $current_user);
				if (isset($value->decisionTable->conditions)) {
					foreach ($value->decisionTable->conditions->condition as $k => $v) {
						$cval = isset($context[(String)$v->input]) ? $context[(String)$v->input] : (String)$v->input;
						$queryGenerator->addCondition((String)$v->field, $cval, (String)$v->operation, $queryGenerator::$AND);
					}
				}
				if (isset($value->decisionTable->searches)) {
					foreach ($value->decisionTable->searches->search as $k => $v) {
						foreach ($v->condition as $k => $v) {
							$queryGenerator->addCondition((String)$v->field, $context[(String)$v->input], (String)$v->operation, $queryGenerator::$AND);
						}
					}
				}
				$field = (String)$value->decisionTable->output;
				$orderby = (String)$value->decisionTable->orderby;
				$queryFields = array($field);
				if (!empty($orderby)) {
					$queryFields[] = $orderby;
				}
				$queryGenerator->setFields($queryFields);
				$query = $queryGenerator->getQuery();
				if (!empty($orderby)) {
					$query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderby);
				}
				$result = $adb->pquery($query, array());
				$seqcnt = 1;
				while ($row = $adb->fetch_array($result)) {
					if (isset($row[$field])) {
						$eval = $row[$field];
						$seqidx = $sequence.'_'.sprintf("%'.04d", $seqcnt++);
						if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
							$outputs[$seqidx] = $eval;
						} elseif ($ruleOutput == 'crmObject') {
							$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
							$crmobj->retrieve_entity_info($eval);
							$outputs[$seqidx] = $crmobj;
						} else {
							$outputs[$seqidx] = '__DoesNotPass__';
						}
					}
				}
			}
		}
		// Checking hitpolicy
		$output = null;
		if ($hitpolicy == 'U') {
			$desiredoutput = null;
			$unique = false;
			$count = 0;
			foreach ($outputs as $k => $v) {
				if ($v != '__DoesNotPass__') {
					if (!$desiredoutput) {
						$desiredoutput = $v;
						$unique = true;
					}
					$count++;
					if ($count > 1) {
						$unique = false;
						break;
					}
				}
			}
			if ($unique) {
				$output = $desiredoutput;
			}
		} elseif ($hitpolicy == 'F') {
			foreach ($outputs as $k => $v) {
				if ($v != '__DoesNotPass__') {
					$output = $v;
					break;
				}
			}
		} elseif ($hitpolicy == 'C') {
			foreach ($outputs as $k => $v) {
				if ($v != '__DoesNotPass__') {
					$output[] = $v;
				}
			}
		} elseif ($hitpolicy == 'A') {
			$desiredoutput = null;
			$sameoutput = false;
			foreach ($outputs as $k => $v) {
				if ($v != '__DoesNotPass__') {
					if (!$desiredoutput) {
						$desiredoutput = $v;
						$sameoutput = true;
					}
					if ($v != $desiredoutput) {
						$sameoutput = false;
					}
				}
			}
			if ($sameoutput) {
				$output = $desiredoutput;
			}
		} elseif ($hitpolicy == 'R') {
			ksort($outputs);
			foreach ($outputs as $k => $v) {
				if ($v != '__DoesNotPass__') {
					$output[] = $v;
				}
			}
		} elseif ($hitpolicy == 'G') {
			if (isset($aggregate)) {
				if ($aggregate == 'sum') {
					$sum = 0;
					foreach ($outputs as $k => $v) {
						if (is_numeric($v)) {
							$sum += $v;
						}
					}
					$output = $sum;
				} elseif ($aggregate == 'min') {
					$min = null;
					foreach ($outputs as $k => $v) {
						if (is_numeric($v)) {
							if (!$min) {
								$min = $v;
							}
							if ($v < $min) {
								$min = $v;
							}
						}
					}
					$output = $min;
				} elseif ($aggregate == 'max') {
					$max = null;
					foreach ($outputs as $k => $v) {
						if (is_numeric($v)) {
							if (!$max) {
								$max = $v;
							}
							if ($v > $max) {
								$max = $v;
							}
						}
					}
					$output = $max;
				} elseif ($aggregate == 'count') {
					$count = 0;
					foreach ($outputs as $k => $v) {
						if (is_numeric($v)) {
							$count++;
						}
					}
					$output = $count;
				}
			}
		}
		if (!$output) {
			$output = '__DoesNotPass__';
		}
		return $output;
	}
}