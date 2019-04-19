<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'modules/com_vtiger_workflow/WorkflowScheduler.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/Users/Users.php';

class WorkFlowScheduler {
	private $user;
	private $db;

	public function __construct($adb) {
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();
		$this->user = $adminUser;
		$this->db = $adb;
	}

	public function getWorkflowQuery($workflow, $fields = array(), $addID = true, $user = null) {
		if (is_null($user)) {
			$user = $this->user;
		}
		$moduleName = $workflow->moduleName;

		$queryGeneratorSelect = new QueryGenerator($moduleName, $user);
		$queryGenerator = new QueryGenerator($moduleName, $user);

		$conditions = json_decode(decode_html($workflow->test));
		$selectExpressions = json_decode(decode_html($workflow->select_expressions));

		if ($selectExpressions) {
			$substExpsSelect = $this->addWorkflowConditionsToQueryGenerator($queryGeneratorSelect, $selectExpressions);

			$selectFields = [];
			$selectExpsCounter = 1;
			foreach ($selectExpressions as $selectExpression) {
				if ($selectExpression->valuetype == 'fieldname') {
					preg_match('/(\w+) : \((\w+)\) (\w+)/', $selectExpression->fieldname, $valuematches);
					if (count($valuematches) != 0) {
						$queryGenerator->setReferenceFieldsManually($valuematches[1], $valuematches[2], $valuematches[3]);
					} else {
						$queryGenerator->addWhereField($selectExpression->fieldname);
					}
					$selectFields[] = $queryGeneratorSelect->getSQLColumn($selectExpression->value);
				} elseif ($selectExpression->valuetype == 'expression') {
					preg_match('/(\w+) : \((\w+)\) (\w+)/', $selectExpression->value, $valuematches);
					if (count($valuematches) != 0) {
						$queryGenerator->setReferenceFieldsManually($valuematches[1], $valuematches[2], $valuematches[3]);
					}
					$selectFields[] = $substExpsSelect["::#$selectExpsCounter"]." AS $selectExpression->fieldname";
					$selectExpsCounter++;
				} else {
					$selectFields[] = $selectExpression->value;
				}
			}

			$selectSql = implode(",", $selectFields);
		} else {
			if ($addID) {
				$queryGenerator->setFields(array_merge(array('id'), $fields));
			} else {
				$queryGenerator->setFields($fields);
			}
		}

		$substExps = $this->addWorkflowConditionsToQueryGenerator($queryGenerator, $conditions);

		if ($moduleName == 'Calendar' || $moduleName == 'Events') {
			if ($conditions) {
				$queryGenerator->addConditionGlue('AND');
			}
			// We should only get the records related to proper activity type
			if ($moduleName == 'Calendar') {
				$queryGenerator->addCondition('activitytype', 'Emails', 'n');
				$queryGenerator->addCondition('activitytype', 'Task', 'e', 'AND');
			} elseif ($moduleName == "Events") {
				$queryGenerator->addCondition('activitytype', 'Emails', 'n');
				$queryGenerator->addCondition('activitytype', 'Task', 'n', 'AND');
			}
		}

		if ($selectExpressions) {
			$query = 'SELECT '.$selectSql;
			$query .= $queryGenerator->getFromClause();
			$query .= $queryGenerator->getWhereClause();
		} else {
			$query = $queryGenerator->getQuery();
		}

		if (count($substExps)>0) {
			foreach ($substExps as $subst => $val) {
				if (substr($subst, 0, 3)=='::#') {
					// we substitute first with quotes, then without to catch both cases of string and numeric field types
					$query = str_replace("'".$subst."'", $val, $query);
					$query = str_replace($subst, $val, $query);
				} else {
					$query = str_replace($subst, $val, $query);
				}
			}
		}
		return $query;
	}

	public function getEligibleWorkflowRecords($workflow) {
		$adb = $this->db;
		$query = $this->getWorkflowQuery($workflow);
		// echo $query."\n"; // for debugging > get query on screen
		$result = $adb->query($query);
		$noOfRecords = $adb->num_rows($result);
		$recordsList = array();
		for ($i = 0; $i < $noOfRecords; ++$i) {
			$recordsList[] = $adb->query_result($result, $i, 0);
		}
		$result = null;
		return $recordsList;
	}

	public function queueScheduledWorkflowTasks() {
		global $default_timezone;
		$adb = $this->db;

		$vtWorflowManager = new VTWorkflowManager($adb);
		$taskQueue = new VTTaskQueue($adb);
		$entityCache = new VTEntityCache($this->user);

		// set the time zone to the admin's time zone, this is needed so that the scheduled workflow will be triggered
		// at admin's time zone rather than the systems time zone. This is specially needed for Hourly and Daily scheduled workflows
		$admin = Users::getActiveAdminUser();
		$adminTimeZone = $admin->time_zone;
		@date_default_timezone_set($adminTimeZone);
		$currentTimestamp = date("Y-m-d H:i:s");
		@date_default_timezone_set($default_timezone);

		$scheduledWorkflows = $vtWorflowManager->getScheduledWorkflows($currentTimestamp);
		foreach ($scheduledWorkflows as $workflow) {
			$tm = new VTTaskManager($adb);
			$tasks = $tm->getTasksForWorkflow($workflow->id);
			if ($tasks) {
				$records = $this->getEligibleWorkflowRecords($workflow);
				$noOfRecords = count($records);
				for ($j = 0; $j < $noOfRecords; ++$j) {
					$recordId = $records[$j];
					// We need to pass proper module name to get the webservice
					if ($workflow->moduleName == 'Calendar') {
						$moduleName = vtws_getCalendarEntityType($recordId);
					} else {
						$moduleName = $workflow->moduleName;
					}
					$wsEntityId = vtws_getWebserviceEntityId($moduleName, $recordId);
					$entityData = $entityCache->forId($wsEntityId);
					$data = $entityData->getData();
					foreach ($tasks as $task) {
						if ($task->active) {
							$trigger = (empty($task->trigger) ? null : $task->trigger);
							$wfminutes=$workflow->schminuteinterval;
							if ($wfminutes!=null) {
								$time = time();
								$delay=$time;
							} else {
								if ($trigger != null) {
									$delay = strtotime($data[$trigger['field']]) + $trigger['days'] * 86400;
								} else {
									$delay = 0;
								}
							}
							if ($task->executeImmediately == true && $wfminutes==null) {
								if (empty($task->test) || $task->evaluate($entityCache, $entityData->getId())) {
									$task->doTask($entityData);
								}
							} else {
								$taskQueue->queueTask($task->id, $entityData->getId(), $delay);
							}
						}
					}
				}
			}
			$vtWorflowManager->updateNexTriggerTime($workflow);
		}
		$scheduledWorkflows = null;
	}

	public function addWorkflowConditionsToQueryGenerator($queryGenerator, $conditions) {
		$conditionMapping = array(
			'equal to' => 'e',
			'less than' => 'l',
			'greater than' => 'g',
			'does not equal' => 'n',
			'less than or equal to' => 'm',
			'greater than or equal to' => 'h',
			'is' => 'e',
			'contains' => 'c',
			'does not contain' => 'k',
			'starts with' => 's',
			'ends with' => 'ew',
			'is not' => 'n',
			'is empty' => 'y',
			'is not empty' => 'ny',
			'before' => 'l',
			'after' => 'g',
			'between' => 'bw',
			'less than days ago' => 'bw',
			'more than days ago' => 'l',
			'in less than' => 'bw',
			'in more than' => 'g',
			'days ago' => 'e',
			'days later' => 'e',
			'less than hours before' => 'bw',
			'less than hours later' => 'bw',
			'more than hours before' => 'l',
			'more than hours later' => 'g',
			'is today' => 'e',
			'exists' => 'exists',
			'does not start with' => 'dnsw',
			'does not end with' => 'dnew',
		);
		$noOfConditions = is_array($conditions) ? count($conditions) : 0;
		//Algorithm :
		//1. If the query has already where condition then start a new group with and condition, else start a group
		//1.5 Open a global parenthesis to encapsulate the whole condition (required to get the or joins correct)
		//2. Foreach of the condition, if its a condition in the same group just append with the existing joincondition
		//3. If its a new group, then start the group with the group join.
		//4. And for the first condition in the new group, dont append any joincondition.
		$substExpressions = array();
		$substExpressionsIndex = 1;
		if ($noOfConditions > 0) {
			if ($queryGenerator->conditionInstanceCount > 0) {
				$queryGenerator->startGroup(QueryGenerator::$AND);
			} else {
				$queryGenerator->startGroup('');
			}
			$queryGenerator->startGroup('');
			$previous_condition = array();
			foreach ($conditions as $index => $condition) {
				$condition = get_object_vars($condition);  // to convert object to array
				$operation = $condition['operation'];

				//Cannot handle this condition for scheduled workflows
				if ($operation == 'has changed' || $operation == 'has changed to' || $operation == 'was') {
					continue;
				}

				$value = $condition['value'];
				$valueType = $condition['valuetype'];
				if (in_array($operation, $this->_specialDateTimeOperator())) {
					$value = $this->_parseValueForDate($condition);
					$valueType = 'rawtext';
				}
				$columnCondition = $condition['joincondition'];
				$groupId = $condition['groupid'];
				$groupJoin = (isset($condition['groupjoin']) ? $condition['groupjoin'] : '');
				$operator = $conditionMapping[$operation];
				$fieldname = $condition['fieldname'];

				if ($index > 0 && $groupId != $previous_condition['groupid']) { // if new group, end older group and start new
					$queryGenerator->endGroup();
					if ($groupJoin) {
						$queryGenerator->startGroup($groupJoin);
					} else {
						$queryGenerator->startGroup(QueryGenerator::$AND);
					}
				}

				if (empty($columnCondition) || $index > 0) {
					$columnCondition = $previous_condition['joincondition'];
				}
				if ($index > 0 && $groupId != $previous_condition['groupid']) {	//if first condition in new group, send empty condition to append
					$columnCondition = null;
				}
				$referenceField = null;
				if ($valueType=='expression') {
					$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($value)));
					$expression = $parser->expression();
					$exprEvaluater = new VTFieldExpressionEvaluater($expression);
					if ($expression instanceof VTExpressionTreeNode) {
						$params = $expression->getParams();
						foreach ($params as $param) {
							if (is_object($param) && isset($param->value)) {
								preg_match('/(\w+) : \((\w+)\) (\w+)/', $param->value, $parammatches);
								if (count($parammatches) != 0) {
									$queryGenerator->setReferenceFieldsManually($parammatches[1], $parammatches[2], $parammatches[3]);
								}
							}
						}
					}
					$wfscenv = new cbexpsql_environmentstub($queryGenerator->getModule(), '0x0');
					$substExpressions['::#'.$substExpressionsIndex] = $exprEvaluater->evaluate($wfscenv, true);
					if (is_object($substExpressions['::#'.$substExpressionsIndex])) {
						$substExpressions['::#'.$substExpressionsIndex] = $substExpressions['::#'.$substExpressionsIndex]->value;
					}
					$value = '::#'.$substExpressionsIndex;
					$substExpressionsIndex++;
				} else {
					$value = html_entity_decode($value);
					preg_match('/(\w+) : \((\w+)\) (\w+)/', $condition['fieldname'], $matches);
					if (count($matches) != 0) {
						list($full, $referenceField, $referenceModule, $fieldname) = $matches;
					} else {
						if ($value=='true:boolean') {
							$value = '1';
						}
						if ($value=='false:boolean') {
							$value = '0';
						}
					}
					preg_match('/(\w+) : \((\w+)\) (\w+)/', $value, $valuematches);
					if (count($valuematches) != 0) {
						list($value, $isfield) = self::getColumnFromField($value, true);
						$queryGenerator->setReferenceFieldsManually($valuematches[1], $valuematches[2], $valuematches[3]);
					} elseif ($valueType=='fieldname' && strpos($value, '.')===false) {
						list($value, $isfield) = self::getColumnFromField('$(nofield : ('.$queryGenerator->getModule().') '.$value.')', false);
					}
				}
				if ($referenceField) {
					$queryGenerator->addReferenceModuleFieldCondition($referenceModule, $referenceField, $fieldname, $value, $operator, $columnCondition);
				} else {
					$queryGenerator->addCondition($fieldname, $value, $operator, $columnCondition);
				}
				$previous_condition = $condition;
			}
			$queryGenerator->endGroup();
			$queryGenerator->endGroup();
		}
		return $substExpressions;
	}

	/**
	 * Special Date functions
	 * @return <Array>
	 */
	private function _specialDateTimeOperator() {
		return array('less than days ago', 'more than days ago', 'in less than', 'in more than', 'days ago', 'days later',
			'less than hours before', 'less than hours later', 'more than hours later', 'more than hours before', 'is today');
	}

	/**
	 * Function parse the value based on the condition
	 * @param <Array> $condition
	 * @return <String>
	 */
	private function _parseValueForDate($condition) {
		$value = $condition['value'];
		$operation = $condition['operation'];

		// based on the admin users time zone, since query generator expects datetime at user timezone
		global $default_timezone;
		$admin = Users::getActiveAdminUser();
		$adminTimeZone = $admin->time_zone;
		@date_default_timezone_set($adminTimeZone);

		switch ($operation) {
			case 'less than days ago':		//between current date and (currentdate - givenValue)
				$days = $condition['value'];
				$value = date('Y-m-d', strtotime('-'.$days.' days')).','.date('Y-m-d', strtotime('+1 day'));
				break;

			case 'more than days ago':		// less than (current date - givenValue)
				$days = $condition['value']-1;
				$value = date('Y-m-d', strtotime('-'.$days.' days'));
				break;

			case 'in less than':			// between current date and future date(current date + givenValue)
				$days = $condition['value']+1;
				$value = date('Y-m-d', strtotime('-1 day')).','.date('Y-m-d', strtotime('+'.$days.' days'));
				break;

			case 'in more than':			// greater than future date(current date + givenValue)
				$days = $condition['value']-1;
				$value = date('Y-m-d', strtotime('+'.$days.' days'));
				break;

			case 'days ago':
				$days = $condition['value'];
				$value = date('Y-m-d', strtotime('-'.$days.' days'));
				break;

			case 'days later':
				$days = $condition['value'];
				$value = date('Y-m-d', strtotime('+'.$days.' days'));
				break;

			case 'is today':
				$value = date('Y-m-d');
				break;

			case 'less than hours before':
				$hours = $condition['value'];
				$value = date('Y-m-d H:i:s', strtotime('-'.$hours.' hours')).','.date('Y-m-d H:i:s');
				break;

			case 'less than hours later':
				$hours = $condition['value'];
				$value = date('Y-m-d H:i:s').','.date('Y-m-d H:i:s', strtotime('+'.$hours.' hours'));
				break;

			case 'more than hours later':
				$hours = $condition['value'];
				$value = date('Y-m-d H:i:s', strtotime('+'.$hours.' hours'));
				break;

			case 'more than hours before':
				$hours = $condition['value'];
				$value = date('Y-m-d H:i:s', strtotime('-'.$hours.' hours'));
				break;
		}
		@date_default_timezone_set($default_timezone);
		return $value;
	}

	public static function getColumnFromField($fieldspec, $addfieldname = true) {
		preg_match('/(\w+) : \((\w+)\) (\w+)/', $fieldspec, $matches);
		list($full, $referenceField, $referenceModule, $fieldname) = $matches;
		$mod = Vtiger_Module::getInstance($referenceModule);
		if (!$mod) {
			return array($fieldname, false);
		}
		$fld = Vtiger_Field::getInstance($fieldname, $mod);
		if (!$fld) {
			return array($fieldname, false);
		}
		return array($fld->table.($addfieldname ? $referenceField : '').'.'.$fld->column, true);
	}
}