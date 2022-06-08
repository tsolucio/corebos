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

	const DOESNOTPASS = '__DoesNotPass__';

	public function processMap($ctx) {
		global $adb, $current_user;
		$xml = $this->getXMLContent();
		if (empty($xml)) {
			return self::DOESNOTPASS;
		}
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
				$entity->setData($context);
			}
		} else {
			if (empty($context['module'])) {
				$context['module'] = 'Accounts'; // should be set, but... so we just pick one
			}
			$entity = new cbexpsql_environmentstub($context['module'], 0);
			$entity->setData($context);
		}
		$current_user = $holduser;
		$outputs = array();
		$hitpolicy = (string)$xml->hitPolicy;
		$mapvalues = array(
			'context' => $context,
			'hitpolicy' => $hitpolicy,
		);
		if ($hitpolicy == 'G') {
			$aggregate = (string)$xml->aggregate;
		}
		$rules = array();
		foreach ($xml->rules->rule as $value) {
			$sequence = (string)$value->sequence;
			$ruleOutput = (string)$value->output;
			$rule = array(
				'sequence' => $sequence,
				'ruleOutput' => $ruleOutput,
			);
			$eval = '';
			if (isset($value->expression)) {
				$this->mapExecutionInfo['type'] = 'Expression';
				$testexpression = (string)$value->expression;
				$rule['type'] = 'expression';
				$rule['valueraw'] = $testexpression;
				if (is_array($context)) {
					foreach ($context as $ctxkey => $ctxvalue) {
						if (!is_array($ctxvalue) && !is_object($ctxvalue)) {
							$testexpression = str_ireplace('$['.$ctxkey.']', $ctxvalue, $testexpression);
						}
					}
				}
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($testexpression)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				$eval = $exprEvaluater->evaluate($entity);
				$rule['valueevaluate'] = $testexpression;
				$rule['valueresult'] = $eval;
				if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
					$outputs[$sequence] = $eval;
				} elseif ($ruleOutput == 'crmObject') {
					$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
					$crmobj->retrieve_entity_info($eval);
					$outputs[$sequence] = $crmobj;
				} else {
					$outputs[$sequence] = self::DOESNOTPASS;
				}
			} elseif (isset($value->mapid)) {
				$this->mapExecutionInfo['type'] = 'Map';
				$mapid = (string)$value->mapid;
				$eval = coreBOS_Rule::evaluate($mapid, $context);
				$rule['type'] = 'map';
				$rule['valueraw'] = $mapid;
				$rule['valueresult'] = $eval;
				if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
					$outputs[$sequence] = $eval;
				} elseif ($ruleOutput == 'crmObject') {
					$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
					$crmobj->retrieve_entity_info($eval);
					$outputs[$sequence] = $crmobj;
				} else {
					$outputs[$sequence] = self::DOESNOTPASS;
				}
			} elseif (isset($value->decisionTable)) {
				$this->mapExecutionInfo['type'] = 'DecisionTable';
				$this->mapExecutionInfo['queries'] = array();
				$module = (string)$value->decisionTable->module;
				$queryGenerator = new QueryGenerator($module, $current_user);
				if (isset($value->decisionTable->conditions)) {
					foreach ($value->decisionTable->conditions->condition as $v) {
						$cval = isset($context[(string)$v->input]) ? $context[(string)$v->input] : (string)$v->input;
						$queryGenerator->addCondition((string)$v->field, $cval, (string)$v->operation, $queryGenerator::$AND);
					}
				}
				if (isset($value->decisionTable->searches)) {
					foreach ($value->decisionTable->searches->search as $s) {
						foreach ($s->condition as $v) {
							if (isset($context[(string)$v->input]) && $context[(string)$v->input]!='__IGNORE__') {
								if (empty($v->preprocess)) {
									$conditionvalue = $context[(string)$v->input];
								} else {
									if (is_array($context)) {
										$v->preprocess = (string)$v->preprocess;
										foreach ($context as $ckey => $cval) {
											if (is_array($cval)) {
												continue;
											}
											$v->preprocess = str_ireplace('$['.$ckey.']', $cval, $v->preprocess);
										}
									}
									$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer((string)$v->preprocess)));
									$expression = $parser->expression();
									$exprEvaluater = new VTFieldExpressionEvaluater($expression);
									$conditionvalue = $exprEvaluater->evaluate($entity);
								}
								$uitype = getUItypeByFieldName($module, (string)$v->field);
								$queryGenerator->startGroup($queryGenerator::$AND);
								if ($uitype==10) {
									if (strpos($conditionvalue, 'x') > 0) {
										list($wsid, $crmid) = explode('x', $conditionvalue);
									} else {
										$crmid = $conditionvalue;
									}
										$relmod = getSalesEntityType($crmid);
										$queryGenerator->addReferenceModuleFieldCondition($relmod, (string)$v->field, 'id', $crmid, (string)$v->operation);
										$queryGenerator->addReferenceModuleFieldCondition($relmod, (string)$v->field, 'id', '', 'y', $queryGenerator::$OR);
								} else {
									$queryGenerator->addCondition((string)$v->field, $conditionvalue, (string)$v->operation);
									$queryGenerator->addCondition((string)$v->field, '__IGNORE__', 'e', $queryGenerator::$OR);
								}
								$queryGenerator->endGroup();
							}
						}
					}
				}
				$field = (string)$value->decisionTable->output;
				$orderby = (string)$value->decisionTable->orderby;
				if (strpos($field, ',')) {
					$queryFields = explode(',', $field);
				} else {
					$queryFields = array($field);
				}
				if (!empty($orderby)) {
					$queryFields[] = $orderby;
				}
				$queryGenerator->setFields($queryFields);
				$query = $queryGenerator->getQuery();
				if (!empty($orderby)) {
					$query .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderby);
				}
				$result = $adb->pquery($query, array());
				$this->mapExecutionInfo['queries'][] = $query;
				$rule['type'] = 'module';
				$rule['valueraw'] = $module;
				$rule['valueevaluate'] = $query;
				$rule['valueresult'] = $adb->num_rows($result);
				$seqcnt = 1;
				$numfields = $result ? $adb->num_fields($result) : 0;
				if ($field=='id') {
					$finfo = getEntityField($module);
					$field = $finfo['entityid'];
				}
				while ($result && $row = $adb->fetch_array($result)) {
					if ($ruleOutput == 'Row') {
						$seqidx = $sequence.'_'.sprintf("%'.04d", $seqcnt++);
						$ret = $row;
						for ($col=0; $col < $numfields; $col++) {
							unset($ret[$col]);
						}
						$outputs[$seqidx] = $ret;
					} elseif (isset($row[$field])) {
						$eval = $row[$field];
						$seqidx = $sequence.'_'.sprintf("%'.04d", $seqcnt++);
						if ($ruleOutput == 'ExpressionResult' || $ruleOutput == 'FieldValue') {
							$outputs[$seqidx] = $eval;
						} elseif ($ruleOutput == 'crmObject') {
							$crmobj = CRMEntity::getInstance(getSalesEntityType($eval));
							$crmobj->retrieve_entity_info($eval);
							$outputs[$seqidx] = $crmobj;
						} else {
							$outputs[$seqidx] = self::DOESNOTPASS;
						}
					}
				}
			}
			$rules[] = $rule;
		}
		$mapvalues['rules'] = $rules;

		// Checking hitpolicy
		$output = null;
		if ($hitpolicy == 'U') {
			$desiredoutput = null;
			$unique = false;
			$count = 0;
			foreach ($outputs as $v) {
				if ($v != self::DOESNOTPASS) {
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
			foreach ($outputs as $v) {
				if ($v != self::DOESNOTPASS) {
					$output = $v;
					break;
				}
			}
		} elseif ($hitpolicy == 'C') {
			foreach ($outputs as $v) {
				if ($v != self::DOESNOTPASS) {
					$output[] = $v;
				}
			}
		} elseif ($hitpolicy == 'A') {
			$desiredoutput = null;
			$sameoutput = false;
			foreach ($outputs as $v) {
				if ($v != self::DOESNOTPASS) {
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
			foreach ($outputs as $v) {
				if ($v != self::DOESNOTPASS) {
					$output[] = $v;
				}
			}
		} elseif ($hitpolicy == 'G') {
			if (isset($aggregate)) {
				if ($aggregate == 'sum') {
					$sum = 0;
					foreach ($outputs as $v) {
						if (is_numeric($v)) {
							$sum += $v;
						}
					}
					$output = $sum;
				} elseif ($aggregate == 'min') {
					$min = null;
					foreach ($outputs as $v) {
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
					foreach ($outputs as $v) {
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
					foreach ($outputs as $v) {
						if (is_numeric($v)) {
							$count++;
						}
					}
					$output = $count;
				}
			}
		}
		if (!$output) {
			$output = self::DOESNOTPASS;
		}
		cbEventHandler::do_action('corebos.audit.decision', array($current_user->id, $ctx, $mapvalues, $output, date('Y-m-d H:i:s')));
		return $output;
	}
}
