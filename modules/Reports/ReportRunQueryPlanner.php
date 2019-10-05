<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
require_once 'modules/Reports/ReportRunQueryDependencyMatrix.php';

class ReportRunQueryPlanner {

	// Turn-off the query planning to revert back - backward compatiblity
	protected $disablePlanner = false;
	protected $tables = array();
	protected $tempTables = array();
	protected $tempTablesInitialized = false;
	// Turn-off in case the query result turns-out to be wrong.
	protected $allowTempTables = true;
	protected $tempTablePrefix = 'vtiger_reptmptbl_';
	protected static $tempTableCounter = 0;
	protected $registeredCleanup = false;
	public $reportRun = false;

	public function disablePlanner() {
		$this->disablePlanner = true;
		$this->allowTempTables = false;
	}

	public function enablePlanner() {
		$this->disablePlanner = false;
		$this->allowTempTables = true;
	}

	public function isDisabled() {
		return $this->disablePlanner;
	}

	public function disableTempTables() {
		$this->allowTempTables = false;
		$this->tempTables = array();
	}

	public function enableTempTables() {
		$this->allowTempTables = true;
		$this->tempTables = array();
	}

	public function addTable($table) {
		if (!empty($table)) {
			$this->tables[$table] = $table;
		}
	}

	public function requireTable($table, $dependencies = null) {

		if ($this->disablePlanner) {
			return true;
		}

		if (isset($this->tables[$table])) {
			return true;
		}
		if (is_array($dependencies)) {
			foreach ($dependencies as $dependentTable) {
				if (isset($this->tables[$dependentTable])) {
					return true;
				}
			}
		} elseif ($dependencies instanceof ReportRunQueryDependencyMatrix) {
			$dependents = $dependencies->getDependents($table);
			if ($dependents) {
				return count(array_intersect($this->tables, $dependents)) > 0;
			}
		}
		return false;
	}

	public function getTables() {
		return $this->tables;
	}

	public function getTemporaryTables() {
		return $this->tempTables;
	}

	public function newDependencyMatrix() {
		return new ReportRunQueryDependencyMatrix();
	}

	public function registerTempTable($query, $keyColumns, $module = null) {
		if ($this->allowTempTables && !$this->disablePlanner) {
			global $current_user;

			$keyColumns = is_array($keyColumns) ? array_unique($keyColumns) : array($keyColumns);

			// Minor optimization to avoid re-creating similar temporary table.
			$uniqueName = null;
			foreach ($this->tempTables as $tmpUniqueName => $tmpTableInfo) {
				if (strcasecmp($query, $tmpTableInfo['query']) === 0 && $tmpTableInfo['module'] == $module) {
					// Capture any additional key columns
					$tmpTableInfo['keycolumns'] = array_unique(array_merge($tmpTableInfo['keycolumns'], $keyColumns));
					$uniqueName = $tmpUniqueName;
					break;
				}
			}

			// Nothing found?
			if ($uniqueName === null) {
				// TODO Adding randomness in name to avoid concurrency
				// even when same-user opens the report multiple instances at same-time.
				$uniqueName = $this->tempTablePrefix . str_replace('.', '', uniqid($current_user->id, true)) . (self::$tempTableCounter++);
				$this->tempTables[$uniqueName] = array(
					'query' => $query,
					'keycolumns' => is_array($keyColumns) ? array_unique($keyColumns) : array($keyColumns),
					'module' => $module
				);
			}
			return $uniqueName;
		}
		return "($query)";
	}

	public function initializeTempTables() {
		global $adb;

		$oldDieOnError = $adb->dieOnError;
		$adb->dieOnError = false; // If query planner is re-used there could be attempt for temp table...
		foreach ($this->tempTables as $uniqueName => $tempTableInfo) {
			$reportConditions = $this->getReportConditions($tempTableInfo['module']);
			if ($tempTableInfo['module'] == 'Emails') {
				$query1 = sprintf('CREATE TEMPORARY TABLE %s AS %s', $uniqueName, $tempTableInfo['query']);
			} else {
				$query1 = sprintf('CREATE TEMPORARY TABLE %s AS %s %s', $uniqueName, $tempTableInfo['query'], $reportConditions);
			}
			$adb->pquery($query1, array());

			$keyColumns = $tempTableInfo['keycolumns'];
			foreach ($keyColumns as $keyColumn) {
				$query2 = sprintf('ALTER TABLE %s ADD INDEX (%s)', $uniqueName, $keyColumn);
				$adb->pquery($query2, array());
			}
		}

		$adb->dieOnError = $oldDieOnError;

		// Trigger cleanup of temporary tables when the execution of the request ends.
		// NOTE: This works better than having in __destruct
		// (as the reference to this object might end pre-maturely even before query is executed)
		if (!$this->registeredCleanup) {
			register_shutdown_function(array($this, 'cleanup'));
			// To avoid duplicate registration on this instance.
			$this->registeredCleanup = true;
		}
	}

	public function cleanup() {
		global $adb;

		$oldDieOnError = $adb->dieOnError;
		$adb->dieOnError = false; // To avoid abnormal termination during shutdown...
		foreach ($this->tempTables as $uniqueName => $tempTableInfo) {
			$adb->pquery('DROP TABLE ' . $uniqueName, array());
		}
		$adb->dieOnError = $oldDieOnError;
		$this->tempTables = array();
	}

	/**
	 * Function to get report condition query for generating temporary table based on condition given on report.
	 * It generates condition query by considering fields of $module's base table or vtiger_crmentity table fields.
	 * It doesn't add condition for reference fields in query.
	 * @param String $module Module name for which temporary table is generated (Reports secondary module)
	 * @return string Returns condition query for generating temporary table.
	 */
	private function getReportConditions($module) {
		$db = PearDatabase::getInstance();
		$moduleModel = CRMEntity::getInstance($module);
		$moduleBaseTable = $moduleModel->table_name;
		$reportId = $this->reportRun->reportid;
		if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'generate') {
			$advanceFilter = $_REQUEST['advanced_filter'];
			$advfilterlist = $this->reportRun->generateAdvFilterSql(json_decode($advanceFilter, true));
		} else {
			$advfilterlist = $this->reportRun->getAdvFilterList($reportId);
		}
		$newAdvFilterList = array();
		$k = 0;

		foreach ($advfilterlist as $i => $columnConditions) {
			$conditionGroup = $advfilterlist[$i]['columns'];
			reset($conditionGroup);
			$firstConditionKey = key($conditionGroup);
			$oldColumnCondition = $advfilterlist[$i]['columns'][$firstConditionKey]['column_condition'];
			foreach ($columnConditions['columns'] as $j => $condition) {
				$columnName = $condition['columnname'];
				$columnParts = explode(':', $columnName);
				list($moduleName, $fieldLabel) = explode('_', $columnParts[2], 2);
				$fieldInfo = getFieldByReportLabel($moduleName, $columnParts[3], 'name');
				if (!empty($fieldInfo)) {
					$fieldInstance = WebserviceField::fromArray($db, $fieldInfo);
					$dataType = $fieldInstance->getFieldDataType();
					$uiType = $fieldInfo['uitype'];
					$fieldTable = $fieldInfo['tablename'];
					$allowedTables = array('vtiger_crmentity', $moduleBaseTable);
					$columnCondition = $advfilterlist[$i]['columns'][$j]['column_condition'];
					if (!in_array($fieldTable, $allowedTables) || $moduleName != $module || isReferenceUIType($uiType) || $columnCondition == 'or' ||
							$oldColumnCondition == 'or' || in_array($dataType, array('reference', 'multireference'))) {
						$oldColumnCondition = $advfilterlist[$i]['columns'][$j]['column_condition'];
					} else {
						$columnParts[0] = $fieldTable;
						$newAdvFilterList[$i]['columns'][$k]['columnname'] = implode(':', $columnParts);
						$newAdvFilterList[$i]['columns'][$k]['comparator'] = $advfilterlist[$i]['columns'][$j]['comparator'];
						$newAdvFilterList[$i]['columns'][$k]['value'] = $advfilterlist[$i]['columns'][$j]['value'];
						$newAdvFilterList[$i]['columns'][$k++]['column_condition'] = $oldColumnCondition;
					}
				}
			}
			if (count($newAdvFilterList) && count($newAdvFilterList[$i])) {
				$newAdvFilterList[$i]['condition'] = $advfilterlist[$i]['condition'];
			}
			if (isset($newAdvFilterList[$i]['columns'][$k - 1])) {
				$newAdvFilterList[$i]['columns'][$k - 1]['column_condition'] = '';
			}
			if (count($newAdvFilterList) && count($newAdvFilterList[$i]) != 2) {
				unset($newAdvFilterList[$i]);
			}
		}
		end($newAdvFilterList);
		$lastConditionsGrpKey = key($newAdvFilterList);
		if (count($newAdvFilterList) && count($newAdvFilterList[$lastConditionsGrpKey])) {
			$newAdvFilterList[$lastConditionsGrpKey]['condition'] = '';
		}

		$advfiltersql = $this->reportRun->generateAdvFilterSql($newAdvFilterList);
		if ($advfiltersql && !empty($advfiltersql)) {
			$advfiltersql = ' AND ' . $advfiltersql;
		}
		return $advfiltersql;
	}
}
?>
