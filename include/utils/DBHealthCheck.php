<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

class DBHealthCheck {
	public $db;
	public $dbType;
	public $dbName;
	public $dbHostName;
	public $recommendedEngineType = 'InnoDB';

	public function __construct($db) {
		$this->db = $db;
		$this->dbType = $db->databaseType;
		$this->dbName = $db->databaseName;
		$this->dbHostName = $db->host;
	}

	public function isMySQL() {
		return (stripos($this->dbType, 'mysql') === 0);
	}

	public function isOracle() {
		return $this->dbType=='oci8';
	}

	public function isPostgres() {
		return $this->dbType=='pgsql';
	}

	public function isDBHealthy() {
		$tablesList = $this->getUnhealthyTablesList();
		return !(count($tablesList) > 0);
	}

	public function getUnhealthyTablesList() {
		$tablesList = array();
		if ($this->isMySql()) {
			$tablesList = $this->_mysql_getUnhealthyTables();
		}
		return $tablesList;
	}

	public function updateTableEngineType($tableName) {
		if ($this->isMySql()) {
			$this->_mysql_updateEngineType($tableName);
		}
	}

	public function updateAllTablesEngineType() {
		if ($this->isMySql()) {
			$this->_mysql_updateEngineTypeForAllTables();
		}
	}

	public function _mysql_getUnhealthyTables() {
		$tablesResult = $this->db->_Execute("SHOW TABLE STATUS FROM `$this->dbName`");
		$noOfTables = $tablesResult->NumRows($tablesResult);
		$unHealthyTables = array();
		$i=0;
		for ($j=0; $j<$noOfTables; ++$j) {
			$tableInfo = $tablesResult->GetRowAssoc(0);
			$isHealthy = false;
			// If already InnoDB type, skip it.
			if ($tableInfo['engine'] == 'InnoDB') {
				$isHealthy = true;
			}
			// If table is a sequence table, then skip it.
			$tableNameParts = explode("_", $tableInfo['name']);
			$tableNamePartsCount = count($tableNameParts);
			if ($tableNameParts[$tableNamePartsCount-1] == 'seq') {
				$isHealthy = true;
			}
			if (!$isHealthy) {
				$unHealthyTables[$i]['name'] = $tableInfo['name'];
				$unHealthyTables[$i]['engine'] = $tableInfo['engine'];
				$unHealthyTables[$i]['autoincrementValue'] = $tableInfo['auto_increment'];
				$tableCollation = $tableInfo['collation'];
				$unHealthyTables[$i]['characterset'] = substr($tableCollation, 0, strpos($tableCollation, '_'));
				$unHealthyTables[$i]['collation'] = $tableCollation;
				$unHealthyTables[$i]['createOptions'] = $tableInfo['create_options'];
				++$i;
			}
			$tablesResult->MoveNext();
		}
		return $unHealthyTables;
	}

	public function _mysql_updateEngineType($tableName) {
		$this->db->_Execute("ALTER TABLE $tableName ENGINE=$this->recommendedEngineType");
	}

	public function _mysql_updateEngineTypeForAllTables() {
		$unHealthyTables = $this->_mysql_getUnhealthyTables();
		foreach ($unHealthyTables as $table) {
			$tableName = $table['name'];
			$this->db->_Execute("ALTER TABLE $tableName ENGINE=$this->recommendedEngineType");
		}
	}
}
?>