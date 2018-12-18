<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class VTJsonCondition {

	public function __construct() {
	}

	public function evaluate($condition, $entityCache, $id) {
		global $logbg;
		$logbg->debug("Entering evaluate($condition,$id) method ...");
		$expr = json_decode($condition, true);
		$entityData = $entityCache->forId($id);
		$data = $entityData->getData();

		$groupResults = array();
		$expressionResults = array();
		$i = 0;
		foreach ($expr as $cond) {
			$conditionGroup = isset($cond['groupid']) ? $cond['groupid'] : '';
			if (empty($conditionGroup)) {
				$conditionGroup = 0;
			}
			preg_match('/(\w+) : \((\w+)\) (\w+)/', $cond['fieldname'], $matches);
			if (count($matches) == 0) {
				$expressionResults[$conditionGroup][$i]['result'] = $this->checkCondition($entityData, $cond);
			} else {
				list($full, $referenceField, $referenceModule, $fieldname) = $matches;
				$referenceFieldId = $data[$referenceField];
				if ($referenceFieldId != 0) {
					$entity = $entityCache->forId($data[$referenceField]);
					if ($entity->getModuleName() == $referenceModule) {
						$cond['fieldname'] = $fieldname;
						$expressionResults[$conditionGroup][$i]['result'] = $this->checkCondition($entity, $cond, $entity);
					} else {
						$expressionResults[$conditionGroup][$i]['result'] = ($cond['operation'] == 'is empty' || ($cond['operation'] == 'is' && $cond['value'] == ''));
					}
				} else {
					$expressionResults[$conditionGroup][$i]['result'] = ($cond['operation'] == 'is empty' || ($cond['operation'] == 'is' && $cond['value'] == ''));
				}
			}
			$expressionResults[$conditionGroup][$i + 1]['logicaloperator'] = (!empty($cond['joincondition'])) ? $cond['joincondition'] : 'and';
			$groupResults[$conditionGroup]['logicaloperator'] = (!empty($cond['groupjoin'])) ? $cond['groupjoin'] : 'and';
			$i++;
		}

		foreach ($expressionResults as $groupId => $groupExprResultSet) {
			$groupResult = true;
			foreach ($groupExprResultSet as $exprResult) {
				if (isset($exprResult['result'])) { // Condition to skip last condition
					$result = $exprResult['result'];
					if (!empty($exprResult['logicaloperator'])) {
						switch ($exprResult['logicaloperator']) {
							case 'and':
								$groupResult = ($groupResult && $result);
								break;
							case 'or':
								$groupResult = ($groupResult || $result);
								break;
						}
					} else { // Case for the first condition
						$groupResult = $result;
					}
				}
			}
			$groupResults[$groupId]['result'] = $groupResult;
		}

		$finalResult = true;
		foreach ($groupResults as $groupId => $groupResult) {
			if (isset($groupResult['result'])) { // Condition to skip last condition
				$result = $groupResult['result'];
				if (!empty($groupResult['logicaloperator'])) {
					switch ($groupResult['logicaloperator']) {
						case 'and':
							$finalResult = ($finalResult && $result);
							break;
						case 'or':
							$finalResult = ($finalResult || $result);
							break;
					}
				} else { // Case for the first condition
					$finalResult = $result;
				}
			}
		}
		$logbg->debug('expressionResults: '.var_export($expressionResults, true));
		$logbg->debug('groupResults: '.var_export($groupResults, true));
		$logbg->debug('Exiting evaluate(): '.var_export($finalResult, true));
		return $finalResult;
	}

	public function startsWith($str, $subStr) {
		$sl = strlen($str);
		$ssl = strlen($subStr);
		if ($sl >= $ssl) {
			return substr_compare($str, $subStr, 0, $ssl) == 0;
		} else {
			return false;
		}
	}

	public function endsWith($str, $subStr) {
		$sl = strlen($str);
		$ssl = strlen($subStr);
		if ($sl >= $ssl) {
			return substr_compare($str, $subStr, $sl - $ssl, $ssl) == 0;
		} else {
			return false;
		}
	}

	public function checkCondition($entityData, $cond, $referredEntityData = null) {
		$data = $entityData->getData();

		$condition = $cond['operation'];
		if (empty($condition)) {
			return false;
		}
		$fieldValue = $data[$cond['fieldname']];
		$value = trim(html_entity_decode($cond['value']));
		$expressionType = isset($cond['valuetype']) ? $cond['valuetype'] : '';

		if ($expressionType == 'fieldname') {
			if ($referredEntityData != null) {
				$referredData = $referredEntityData->getData();
				preg_match('/(\w+) : \((\w+)\) (\w+)/', $value, $valuematches);
				if (isset($referredEntityData->user) && count($valuematches) > 0 && $valuematches[1]=='assigned_user_id') {
					$referredData[$value] = $referredEntityData->user->column_fields[$valuematches[3]];
				} elseif (count($valuematches) > 0) {
					$value = $valuematches[3];
				}
			} else {
				$referredData = $data;
			}
			$value = $referredData[$value];
		} elseif ($expressionType == 'expression') {
			require_once 'modules/com_vtiger_workflow/expression_engine/include.inc';
			if (trim($value)=='') {
				$value = '';
			} else {
				$parser = new VTExpressionParser(new VTExpressionSpaceFilter(new VTExpressionTokenizer($value)));
				$expression = $parser->expression();
				$exprEvaluater = new VTFieldExpressionEvaluater($expression);
				if ($referredEntityData != null) {
					$value = $exprEvaluater->evaluate($referredEntityData);
				} else {
					$value = $exprEvaluater->evaluate($entityData);
				}
			}
		} else {
			if ($value=='true:boolean') {
				$value = '1';
			}
			if ($value=='false:boolean') {
				$value = '0';
			}
		}

		// If both fieldValue and value are formatted as HH:mm:ss or HH:mm, convert them to a no. of seconds
		$convert_time_conds = array('less than', 'greater than', 'less than or equal to', 'greater than or equal to');
		if ((preg_match('/(\d{1,3}):(\d{1,2}):?(\d{1,2})?/', $fieldValue) === 1 && preg_match('/(\d{1,3}):(\d{1,2}):?(\d{1,2})?/', $value) === 1) && in_array($condition, $convert_time_conds)) {
			preg_match('/(\d{1,3}):(\d{1,2}):?(\d{1,2})?/', $fieldValue, $fieldValue_matches);
			preg_match('/(\d{1,3}):(\d{1,2}):?(\d{1,2})?/', $value, $value_matches);
			if (count($fieldValue_matches) == 3) {
				$fieldValue = (int)($fieldValue_matches[1] * 3600) + (int)($fieldValue_matches[2] * 60);
			} elseif (count($fieldValue_matches == 4)) {
				$fieldValue = (int)($fieldValue_matches[1] * 3600) + (int)($fieldValue_matches[2] * 60) + (int)$fieldValue_matches[3];
			}
			if (count($value_matches) == 3) {
				$value = (int)($value_matches[1] * 3600) + (int)($value_matches[2] * 60);
			} elseif (count($value_matches == 4)) {
				$value = (int)($value_matches[1] * 3600) + (int)($value_matches[2] * 60) + (int)$value_matches[3];
			}
		}

		switch ($condition) {
			case 'exists':
				global $current_user,$adb;
				$qg = new QueryGenerator($entityData->getModuleName(), $current_user);
				$qg->addCondition($cond['fieldname'], $value, 'e');
				list($mid,$crmid) = explode('x', $data['id']);
				$qg->addCondition('id', $crmid, 'n', $qg::$AND);
				$qe = 'SELECT EXISTS(SELECT 1 '.$qg->getFromClause().$qg->getWhereClause().')';
				$rs = $adb->query($qe);
				if ($rs) {
					$ex = $adb->query_result($rs, 0, 0);
					return ($ex == '1');
				} else {
					return false;
				}
			case 'equal to':
				return $fieldValue == $value;
			case 'less than':
				return $fieldValue < $value;
			case 'greater than':
				return $fieldValue > $value;
			case 'does not equal':
				return $fieldValue != $value;
			case 'less than or equal to':
				return $fieldValue <= $value;
			case 'greater than or equal to':
				return $fieldValue >= $value;
			case 'is':
				if (preg_match('/([^:]+):boolean$/', $value, $match)) {
					$value = $match[1];
					if ($value == 'true') {
						return $fieldValue === 'on' || $fieldValue === 1 || $fieldValue === '1';
					} else {
						return $fieldValue === 'off' || $fieldValue === 0 || $fieldValue === '0' || $fieldValue === '';
					}
				} else {
					global $current_user,$adb;
					$tabid = getTabid($entityData->getModuleName());
					$result = $adb->pquery("select *, '0' as readonly from vtiger_field where tabid = ? and fieldname=?", array($tabid, $cond['fieldname']));
					$webserviceField = WebserviceField::fromQueryResult($adb, $result, 0);
					$ftype = $webserviceField->getFieldDataType();
					if ($ftype=='multipicklist') {
						$flvals = explode('|##|', $fieldValue);
						$flvals = array_map('trim', $flvals);
						return in_array($value, $flvals);
					} elseif ($ftype=='reference') {
						$isthesame = false;
						$rlist = $webserviceField->getReferenceList();
						foreach ($rlist as $modName) {
							$wsidrs = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name=?', array($modName));
							if ($wsidrs && $adb->num_rows($wsidrs)>0) {
								$wsid = $adb->query_result($wsidrs, 0, 0);
								$isthesame = ($fieldValue == $wsid.'x'.$value);
							}
							if ($isthesame) {
								break;
							}
						}
						return $isthesame || ($fieldValue == $value);
					} else {
						return $fieldValue == $value;
					}
				}
			case 'is not':
				if (preg_match('/([^:]+):boolean$/', $value, $match)) {
					$value = $match[1];
					if ($value == 'true') {
						return $fieldValue === 'off' || $fieldValue === 0 || $fieldValue === '0' || $fieldValue === '';
					} else {
						return $fieldValue === 'on' || $fieldValue === 1 || $fieldValue === '1';
					}
				} else {
					global $current_user,$adb;
					$tabid = getTabid($entityData->getModuleName());
					$result = $adb->pquery("select *, '0' as readonly from vtiger_field where tabid = ? and fieldname=?", array($tabid, $cond['fieldname']));
					$webserviceField = WebserviceField::fromQueryResult($adb, $result, 0);
					$ftype = $webserviceField->getFieldDataType();
					if ($ftype=='multipicklist') {
						$flvals = explode('|##|', $fieldValue);
						$flvals = array_map('trim', $flvals);
						return !in_array($value, $flvals);
					} elseif ($ftype=='reference') {
						$isthesame = false;
						$rlist = $webserviceField->getReferenceList();
						foreach ($rlist as $modName) {
							$wsidrs = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name=?', array($modName));
							if ($wsidrs && $adb->num_rows($wsidrs)>0) {
								$wsid = $adb->query_result($wsidrs, 0, 0);
								$isthesame = ($fieldValue == $wsid.'x'.$value);
							}
							if ($isthesame) {
								break;
							}
						}
						return !$isthesame && ($fieldValue != $value);
					} else {
						return $fieldValue != $value;
					}
				}
			case 'contains':
				if (is_array($value)) {
					return in_array($fieldValue, $value);
				}
				return strpos($fieldValue, $value) !== false;
			case 'does not contain':
				if (empty($value)) {
					unset($value);
				}
				if (is_array($value)) {
					return !in_array($fieldValue, $value);
				}
				return strpos($fieldValue, $value) === false;
			case 'starts with':
				return $this->startsWith($fieldValue, $value);
			case 'ends with':
				return $this->endsWith($fieldValue, $value);
			case 'does not start with':
				$val = subStr($fieldValue, 0, 1);
				$val2 = subStr($value, 0, 1);
				if ($val === $val2) {
					return false;
				} else {
					return !in_array($fieldValue, $value);
				}
			case 'does not end with':
				$val = subStr($fieldValue, -1);
				$val2 = subStr($value, -1);
				if ($val === $val2) {
					return false;
				} else {
					return !in_array($fieldValue, $value);
				}
			case 'matches':
				return preg_match($value, $fieldValue);
			case 'has changed':
				$entityDelta = new VTEntityDelta();
				$idParts = vtws_getIdComponents($entityData->getId());
				return $entityDelta->hasChanged($entityData->getModuleName(), $idParts[1], $cond['fieldname']);
			case 'has changed to':
				$entityDelta = new VTEntityDelta();
				$idParts = vtws_getIdComponents($entityData->getId());
				return $entityDelta->hasChanged($entityData->getModuleName(), $idParts[1], $cond['fieldname'], $value);
			case 'was':
				$entityDelta = new VTEntityDelta();
				$idParts = vtws_getIdComponents($entityData->getId());
				return $entityDelta->getOldEntityValue($entityData->getModuleName(), $idParts[1], $cond['fieldname'])==$value;
			case 'is empty':
				if (empty($fieldValue)) {
					return true;
				}
				return false;
			case 'is not empty':
				if (empty($fieldValue)) {
					return false;
				}
				return true;
			case 'before':
				if (empty($fieldValue)) {
					return false;
				}
				if ($fieldValue < $value) {
					return true;
				}
				return false;
			case 'after':
				if (empty($fieldValue)) {
					return false;
				}
				if ($fieldValue > $value) {
					return true;
				}
				return false;
			case 'between':
				if (empty($fieldValue)) {
					return false;
				}
				$values = explode(',', $value);
				$values = array_map('getValidDBInsertDateValue', $values);
				if ($fieldValue > $values[0] && $fieldValue < $values[1]) {
					return true;
				}
				return false;
			case 'is today':
				$today = date('Y-m-d');
				if ($cond['fieldname'] == 'birthday') {
					$fieldValue = date('m-d', strtotime($fieldValue));
					$today = date('m-d');
				}
				if ($fieldValue == $today) {
					return true;
				}
				return false;
			case 'less than days ago':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$today = date('Y-m-d');
				$olderDate = date('Y-m-d', strtotime('-'.$value.' days'));
				if ($olderDate <= $fieldValue && $fieldValue <= $today) {
					return true;
				}
				return false;
			case 'more than days ago':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$olderDate = date('Y-m-d', strtotime('-'.$value.' days'));
				if ($fieldValue <= $olderDate) {
					return true;
				}
				return false;
			case 'in less than':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$today = date('Y-m-d');
				$futureDate = date('Y-m-d', strtotime('+'.$value.' days'));
				if ($today <= $fieldValue && $fieldValue <= $futureDate) {
					return true;
				}
				return false;
			case 'in more than':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$futureDate = date('Y-m-d', strtotime('+'.$value.' days'));
				if ($fieldValue >= $futureDate) {
					return true;
				}
				return false;
			case 'days ago':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$olderDate = date('Y-m-d', strtotime('-'.$value.' days'));
				if ($fieldValue == $olderDate) {
					return true;
				}
				return false;
			case 'days later':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$futureDate = date('Y-m-d', strtotime('+'.$value.' days'));
				if ($fieldValue == $futureDate) {
					return true;
				}
				return false;

			case 'less than hours before':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$currentTime = date('Y-m-d H:i:s');
				$olderDateTime = date('Y-m-d H:i:s', strtotime('-'.$value.' hours'));
				if ($olderDateTime <= $fieldValue && $fieldValue <= $currentTime) {
					return true;
				}
				return false;

			case 'less than hours later':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$currentTime = date('Y-m-d H:i:s');
				$futureDateTime = date('Y-m-d H:i:s', strtotime('+'.$value.' hours'));
				if ($currentTime <= $fieldValue && $fieldValue <= $futureDateTime) {
					return true;
				}
				return false;

			case 'more than hours before':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$olderDateTime = date('Y-m-d H:i:s', strtotime('-'.$value.' hours'));
				if ($fieldValue <= $olderDateTime) {
					return true;
				}
				return false;
			case 'more than hours later':
				if (empty($fieldValue) || !isset($value)) {
					return false;
				}
				$futureDateTime = date('Y-m-d H:i:s', strtotime('+'.$value.' hours'));
				if ($fieldValue >= $futureDateTime) {
					return true;
				}
				return false;
			default:
				//Unexpected condition
				throw new Exception('Found an unexpected condition: ' . $condition);
		}
	}
}
?>
