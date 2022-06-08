<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
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
 *************************************************************************************************/
require_once 'include/logging.php';
require_once 'include/database/PearDatabase.php';
require_once 'include/integrations/clickhouse/clickhouse.php';

$log = LoggerManager::getLogger('APPLICATION');
$logsqltm = LoggerManager::getLogger('SQLTIME');

class ClickHouseDatabase extends PearDatabase {
	public $dbType = 'clickhouse';
	public $chdatabase;
	public $lastResult;

	/**
	 * Manage instance usage of this class
	 */
	public static function &getInstance() {
		global $cdb;
		if (!isset($cdb)) {
			$cdb = new self();
		}
		return $cdb;
	}

	/**
	 * Reset query result for reusing if cache is enabled
	 */
	public function resetQueryResultToEOF(&$result) {
		if ($result) {
			$result->iterator = $result->count();
		}
	}

	public function setOption($name, $value) {
		if (isset($this->chdatabase)) {
			$this->chdatabase->settings()->set($name, $value);
		}
	}

	public function startTransaction() {
		// ClickHouse doesn't support transactions
	}

	public function completeTransaction() {
		// ClickHouse doesn't support transactions
	}

	public function hasFailedTransaction() {
		return false;
	}

	public function getErrorMsg() {
		try {
			return $this->lastResult->error();
		} catch (\Throwable $th) {
			return $th;
		}
	}

	public function checkError($msg = '', $dieOnError = false) {
		$err = $this->lastResult->error();
		$ErrorNo = $err['code'];
		$ErrorMsg = $err['message'];
		if ($this->dieOnError || $dieOnError) {
			$bt = debug_backtrace();
			$ut = array();
			foreach ($bt as $t) {
				$ut[] = array('file'=>$t['file'],'line'=>$t['line'],'function'=>$t['function']);
			}
			$this->println('CHDB error '.$msg.'->['.$ErrorNo.']'.$ErrorMsg);
			die($msg.' DB error '.$msg.'->'.$ErrorMsg);
		} else {
			$this->println('CHDB error '.$msg.'->['.$ErrorNo.']'.$ErrorMsg);
		}
		return false;
	}

	public function checkConnection() {
		if (!isset($this->chdatabase)) {
			$this->println('TRANS creating new connection');
			$this->connect(false);
		}
	}

	public function write($sql, $dieOnError = false, $msg = '') {
		global $log, $cdb;
		$log->debug('> write '.$sql);
		$this->checkConnection();
		$sql_start_time = microtime(true);
		$result = $cdb->chdatabase->write($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), $sql);
		if (!$result) {
			$this->checkError($msg.' Write Failed:' . $sql . '::', $dieOnError);
		}
	}

	public function query($sql, $dieOnError = false, $msg = '') {
		global $log, $cdb;
		$log->debug('> query '.$sql);
		$this->checkConnection();
		$sql_start_time = microtime(true);
		$result = $cdb->chdatabase->select($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), $sql);
		if (!$result) {
			$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);
		} else {
			$result->fields = $result->fetchOne();
		}
		$this->lastResult = $result;
		return $result;
	}

	/** ADODB prepared statement Execution
	* @param string Prepared sql statement
	* @param array Parameters for the prepared statement
	* @param boolean dieOnError when query execution fails
	* @param string Error message on query execution failure
	*/
	public function pquery($sql, $params, $dieOnError = false, $msg = '') {
		global $log, $cdb;
		if (!isset($params)) {
			$params = array();
		}
		$log->debug('> pquery '.$sql);
		$this->checkConnection();

		$sql_start_time = microtime(true);
		$params = $this->flatten_array($params);
		if (!is_null($params) && is_array($params)) {
			$log->debug('parameters', $params);
		}

		if (!empty($params)) {
			$sql = $this->convert2Sql($sql, $params);
		}
		$result = $cdb->chdatabase->select($sql);
		$sql_end_time = microtime(true);
		$this->logSqlTiming($sql_start_time, $sql_end_time, $sql, $params);
		$this->lastmysqlrow = -1;
		if (!$result) {
			$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);
		} else {
			$result->fields = $result->fetchOne();
		}
		$this->lastResult = $result;
		return $result;
	}

	public function updateBlob($tablename, $colname, $id, $data) {
		return false;
	}

	public function updateBlobFile($tablename, $colname, $id, $filename) {
		return false;
	}

	public function limitQuery($sql, $start, $count, $dieOnError = false, $msg = '') {
		global $log, $cdb;
		$log->debug('> limitQuery '.$sql .','.$start .','.$count);
		$this->checkConnection();

		$sql_start_time = microtime(true);
		$nrows = (int) $start;
		$offset = (int) $count-1;
		$offsetStr = ($offset >= 0) ? "$offset," : '';
		if ($nrows < 0) {
			$nrows = '18446744073709551615';
		}
		$sql = $sql . " LIMIT $offsetStr$nrows";
		$result = $cdb->chdatabase->select($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), "$sql LIMIT $count, $start");
		if (!$result) {
			$this->checkError($msg.' Limit Query Failed:' . $sql . '::', $dieOnError);
		} else {
			$result->fields = $result->fetchOne();
		}
		$this->lastResult = $result;
		return $result;
	}

	public function getOne($sql, $dieOnError = false, $msg = '') {
		global $cdb;
		$this->println('CHDB getOne sql='.$sql);
		$this->checkConnection();
		$sql_start_time = microtime(true);
		$result = $cdb->chdatabase->select($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), "$sql GetONE");
		if (!$result) {
			$this->checkError($msg.' Get one Query Failed:' . $sql . '::', $dieOnError);
			$rdo = null;
		} else {
			$result->fields = $result->fetchOne();
			$rdo = reset($result->fields);
		}
		$this->lastResult = $result;
		return $rdo;
	}

	public function getFieldsDefinition(&$result) {
		if (!isset($result) || empty($result)) {
			return 0;
		}
		$rsf = $this->chdatabase->select('DESCRIBE ('.substr($result->sql(), 0, stripos($result->sql(), ' FORMAT ')).')');
		return $rsf->rows();
	}

	public function getFieldsArray(&$result) {
		if (!isset($result) || empty($result)) {
			return 0;
		}
		$rsf = $this->chdatabase->select('DESCRIBE ('.substr($result->sql(), 0, stripos($result->sql(), ' FORMAT ')).')');
		$field_array = array();
		while ($meta = $rsf->FetchRow()) {
			$field_array[] = $meta['name'];
		}
		return $field_array;
	}

	public function getRowCount(&$result) {
		if (empty($result)) {
			$rows = 0;
		} else {
			$rows = $result->count();
		}
		return $rows;
	}

	public function num_fields(&$result) {
		$rsf = $this->chdatabase->select('DESCRIBE ('.substr($result->sql(), 0, stripos($result->sql(), ' FORMAT ')).')');
		return $rsf->count();
	}

	public function fetch_array(&$result) {
		if (empty($result) || !is_object($result) || $this->EOF($result)) {
			return null;
		}
		return $result->fetchRow();
	}

	public function run_insert_data($table, $data) {
		$query = $this->sql_insert_data($table, $data);
		$this->chdatabase->write($query);
	}

	public function run_query_record($query) {
		$result = $this->chdatabase->select($query);
		if (!$result) {
			return false;
		}
		if (!is_object($result)) {
			throw new InvalidArgumentException("query $query failed: ".json_encode($result));
		}
		return $result->fetchOne();
	}

	public function run_query_record_html($query) {
		return $this->run_query_record($query);
	}

	public function run_query_allrecords($query) {
		$result = $this->chdatabase->select($query);
		return $result->rows();
	}

	public function result_get_next_record($result) {
		return $result->FetchRow();
	}

	public function query_result(&$result, $row, $col = 0) {
		if (!is_object($result)) {
			throw new InvalidArgumentException('result is not an object');
		}
		$result->iterator = $row;
		$rowdata = $result->FetchRow();
		if (is_numeric($col)) {
			$element = reset($rowdata);
			$idx = 0;
			while ($idx<count($rowdata) && $idx<$col) {
				$element = next($rowdata);
				$idx++;
			}
			return $element;
		} else {
			return (isset($rowdata[$col]) ? $rowdata[$col] : '');
		}
	}

	// Function to get particular row from the query result
	public function query_result_rowdata(&$result, $row = 0) {
		if (!is_object($result)) {
			throw new InvalidArgumentException('result is not an object');
		}
		$result->iterator = $row;
		return $result->FetchRow();
	}

	/**
	 * Get an array representing a row in the result set
	 * Unlike it's non raw siblings this method will not escape html entities in return strings.
	 *
	 * The case of all the field names is converted to lower case, as with the other methods.
	 *
	 * @param object The query result to fetch from
	 * @param integer The row number to fetch. It's default value is 0
	 *
	 */
	public function raw_query_result_rowdata(&$result, $row = 0) {
		return $this->query_result_rowdata($result, $row);
	}

	/**
	 * WARNING: this method returns false for SELECT statements
	 */
	public function getAffectedRowCount(&$result) {
		return false;
	}

	public function requireSingleResult($sql, $dieOnError = false, $msg = '', $encode = true) {
		$result = $this->query($sql, $dieOnError, $msg);
		if ($this->getRowCount($result) == 1) {
			return $result;
		}
		$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
		return '';
	}

	/** function which extends requireSingleResult api to execute prepared statement */
	public function requirePsSingleResult($sql, $params, $dieOnError = false, $msg = '', $encode = true) {
		$result = $this->pquery($sql, $params, $dieOnError, $msg);
		if ($this->getRowCount($result) == 1) {
			return $result;
		}
		$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
		return '';
	}

	public function EOF($result) {
		return ($result->iterator>=$result->count());
	}

	public function fetchByAssoc(&$result, $rowNum = -1, $encode = true) {
		if (empty($result) || !is_object($result) || $this->EOF($result)) {
			$this->println('CHDB fetchByAssoc return null');
			return null;
		}
		if ($rowNum>-1) {
			$currentPosition = $result->iterator;
			$result->iterator = $rowNum;
		}
		$return = $result->fetchRow();
		if ($rowNum>-1) {
			$result->iterator = $currentPosition;
		}
		return $return;
	}

	public function getNextRow(&$result, $encode = true) {
		if (isset($result)) {
			return $result->FetchRow();
		}
		return null;
	}

	public function field_name(&$result, $col) {
		$flds = $this->getFieldsDefinition($result);
		if (isset($flds[$col])) {
			return $flds[$col];
		}
		return null;
	}

	public function getDBDateString($datecolname) {
		global $adb;
		return $adb->getDBDateString($datecolname);
	}

	public function formatDate($datetime, $strip_quotes = false) {
		global $adb;
		return $adb->formatDate($datetime, $strip_quotes);
	}

	public function sql_quote($data) {
		global $adb;
		return $adb->sql_quote($data);
	}

	public function connect($dieOnError = false, $dbname = 'default') {
		global $adb;
		$this->database = $adb->database;
		$this->chdatabase = corebos_clickhouse::connectToClickhouse();
		$this->chdatabase->database($dbname);
	}

	/**
	 * Constructor
	 */
	public function __construct($dbtype = '', $host = '', $dbname = '', $username = '', $passwd = '') {
		$this->log = LoggerManager::getLogger('CHDB');
	}

	public function quote($string) {
		global $adb;
		return $adb->Quote($string);
	}

	public function disconnect() {
		global $cdb;
		$this->println('CHDB disconnect');
		unset($cdb);
	}

	public function createTables($schemaFile, $dbHostName = false, $userName = false, $userPassword = false, $dbName = false, $dbType = false) {
		global $adb;
		$this->println('CHDB createTables '.$schemaFile);
		if ($dbHostName) {
			$this->dbHostName=$dbHostName;
		}
		if ($userName) {
			$this->userName=$userPassword;
		}
		if ($userPassword) {
			$this->userPassword=$userPassword;
		}
		if ($dbName) {
			$this->dbName=$dbName;
		}
		if ($dbType) {
			$this->dbType=$dbType;
		}

		$this->checkConnection();
		$db = $adb->database;
		$schema = new adoSchema($db);
		//Debug Adodb XML Schema
		$schema->XMLS_DEBUG = true;
		//Debug Adodb
		$schema->debug = true;
		$sql = $schema->ParseSchema($schemaFile);

		$this->println('--------------Starting the table creation------------------');
		$result = $this->chdatabase->write($sql);
		if ($result) {
			print $db->errorMsg();
		}
		// needs to return in a decent way
		$this->println('CHDB createTables '.$schemaFile.' status='.$result);
		return $result;
	}

	public function changeDataTypeCase($flds) {
		return str_replace(
			array(
				'INT8', 'INT16', 'INT32', 'INT64', 'INT128', 'INT256',
				'UINT8', 'UINT16', 'UINT32', 'UINT64', 'UINT128', 'UINT256',
				'FLOAT32', 'FLOAT64',
			),
			array(
				'Int8', 'Int16', 'Int32', 'Int64', 'Int128', 'Int256',
				'UInt8', 'UInt16', 'UInt32', 'UInt64', 'UInt128', 'UInt256',
				'Float32', 'Float64',
			),
			$flds
		);
	}

	public function createTable($tablename, $flds) {
		global $adb;
		$this->println('CHDB createTable table='.$tablename.' flds='.$flds);
		$this->checkConnection();
		$dict = NewDataDictionary($adb->database);
		$sqlarray = $dict->CreateTableSQL($tablename, $flds);
		$sql = $this->changeDataTypeCase($sqlarray[0]);
		if (stripos($sql, ' ENGINE ')===false) {
			$fields = explode(',', $flds);
			$fieldtype = $fields[0];
			$field = explode(' ', $fieldtype);
			$sql .= ' ENGINE=MergeTree() order by '. $field[0];
		}
		return $this->chdatabase->write($sql);
	}

	public function alterTable($tablename, $flds, $oper) {
		global $adb;
		$this->println('CHDB alterTableTable table='.$tablename.' flds='.$flds.' oper='.$oper);
		$this->checkConnection();
		$dict = NewDataDictionary($adb->database);
		if ($oper == 'Add_Column') {
			$dict->addCol .= ' COLUMN';
			$sqlarray = $dict->AddColumnSQL($tablename, $flds);
		} elseif ($oper == 'Delete_Column') {
			$sqlarray = $dict->DropColumnSQL($tablename, $flds);
		}
		if (!empty($sqlarray)) {
			$this->println($sqlarray);
			foreach ($sqlarray as $sql) {
				$rdo = $this->chdatabase->write($sql);
			}
			return $rdo;
		}
		return false;
	}

	public function getColumnNames($tablename) {
		$rsf = $this->chdatabase->select("DESCRIBE $tablename;");
		try {
			$rsf->error();
		} catch (\Throwable $th) {
			$this->lastResult = $rsf;
			return false;
		}
		$field_array = array();
		while ($meta = $rsf->FetchRow()) {
			$field_array[] = $meta['name'];
		}
		return $field_array;
	}

	public function formatString($tablename, $fldname, $str) {
		$this->checkConnection();
		$adoflds = $this->getColumnNames($tablename);
		foreach ($adoflds as $fld) {
			if (strcasecmp($fld['name'], $fldname)==0) {
				$fldtype =strtoupper($fld['type']);
				if (strcmp($fldtype, 'STRING')==0 || strcmp($fldtype, 'CHAR')==0 || strcmp($fldtype, 'VARCHAR')==0 ||
					strcmp($fldtype, 'VARCHAR2')==0 || strcmp($fldtype, 'LONGTEXT')==0 || strcmp($fldtype, 'TEXT')==0
				) {
					return $this->Quote($str);
				} elseif (strcmp($fldtype, 'DATETIME') ==0 || strcmp($fldtype, 'DATE') ==0 || strcmp($fldtype, 'TIMESTAMP')==0) {
					return $this->formatDate($str);
				} else {
					return $str;
				}
			}
		}
		$this->println('format String Illegal field name '.$fldname);
		return $str;
	}

	public function getUniqueID($seqname) {
		return false;
	}

	public function get_tables() {
		return $this->chdatabase->select('show tables;');
	}

	//To get a function name with respect to the database type which escapes strings in given text
	public function sql_escape_string($str) {
		if (is_null($str)) {
			return 'NULL';
		}
		global $adb;
		return substr($adb->quote($str), 1, -1);
	}

	// Function to get the last insert id based on the type of database
	public function getLastInsertID($seqname = '') {
		return false;
	}

	// Function to escape the special characters in database name based on database type.
	public function escapeDbName($dbName = '') {
		if ($dbName == '') {
			$dbName = $this->dbName;
		}
		return "`{$dbName}`";
	}
}

if (empty($cdb)) {
	$cdb = new ClickHouseDatabase();
	$cdb->connect();
}
?>