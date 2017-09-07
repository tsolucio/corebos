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
 *************************************************************************************************/
require_once('include/logging.php');
include('include/adodb/adodb.inc.php');
require_once("include/adodb/adodb-xmlschema.inc.php");

$log = LoggerManager::getLogger('VT');
$logsqltm = LoggerManager::getLogger('SQLTIME');

// Callback class useful to convert PreparedStatement Question Marks to SQL value
// See function convertPS2Sql in PearDatabase below
class PreparedQMark2SqlValue {
	// Constructor
	function __construct($vals){
		$this->ctr = 0;
		$this->vals = $vals;
	}
	function call($matches){
		/**
		* If ? is found as expected in regex used in function convert2sql
		* /('[^']*')|(\"[^\"]*\")|([?])/
		*
		*/
		if($matches[3]=='?'){
			$this->ctr++;
			return $this->vals[$this->ctr-1];
		}else{
			return $matches[0];
		}
	}
}

/**
 * Performance perference API
 * @deprecated use Global Variables
 */
class PerformancePrefs {
	/**
	 * Get performance parameter configured value or default one
	 */
	static function get($key, $defvalue=false) {
		return $defvalue;
	}
	/** Get boolean value */
	static function getBoolean($key, $defvalue=false) {
		return self::get($key, $defvalue);
	}
	/** Get Integer value */
	static function getInteger($key, $defvalue=false) {
		return (int)self::get($key, $defvalue);
	}
}

/**
 * Cache Class for PearDatabase
 */
class PearDatabaseCache {
	var $_queryResultCache = Array();
	var $_parent;

	// Cache the result if rows is less than this
	var $_CACHE_RESULT_ROW_LIMIT;

	/**
	 * Constructor
	 */
	function __construct($parent) {
		$this->_parent = $parent;
		$this->_CACHE_RESULT_ROW_LIMIT = PerformancePrefs::getInteger('CACHE_RESULT_ROW_LIMIT', 100);
	}

	/**
	 * Reset the cache contents
	 */
	function resetCache() {
		unset($this->_queryResultCache);
		$this->_queryResultCache = Array();
	}

	/**
	 * Cache SQL Query Result (perferably only SELECT SQL)
	 */
	function cacheResult($result, $sql, $params=false) {
		// We don't want to cache NON-SELECT query results now
		if(stripos(trim($sql), 'SELECT ') !== 0) {
			return;
		}
		// If the result is too big, don't cache it
		if($this->_parent->num_rows($result) > $this->_CACHE_RESULT_ROW_LIMIT) {
			global $log;
			$log->fatal("[" . get_class($this) . "] Cannot cache result! $sql [Exceeds limit ".
				$this->_CACHE_RESULT_ROW_LIMIT . ", Total Rows " . $this->_parent->num_rows($result) . "]");
			return false;
		}
		$usekey = $sql;
		if(!empty($params)) $usekey = $this->_parent->convert2Sql($sql, $this->_parent->flatten_array($params));
		$this->_queryResultCache[$usekey] = $result;
	}

	/**
	 * Get the cached result for re-use
	 */
	function getCacheResult($sql, $params=false) {
		$result = false;
		$usekey = $sql;
		if(!empty($params)) $usekey = $this->_parent->convert2Sql($sql, $this->_parent->flatten_array($params));
		$result = $this->_queryResultCache[$usekey];
		// Rewind the result for re-use
		if($result) {
			// If result not in use rewind it
			if($result->EOF) $result->MoveFirst();
			else if($result->CurrentRow() != 0) {
				global $log;
				$log->fatal("[" . get_class($this) . "] Cannot reuse result! $usekey [Rows Total " .
					$this->_parent->num_rows($result) . ", Currently At: " . $result->CurrentRow() . "]");
				// Do no allow result to be re-used if it is in use.
				$result = false;
			}
		}
		return $result;
	}
}

class PearDatabase{
	var $database = null;
	var $dieOnError = false;
	var $dbType = null;
	var $dbHostName = null;
	var $dbName = null;
	var $userName=null;
	var $userPassword=null;
	var $query_time = 0;
	var $log = null;
	var $lastmysqlrow = -1;
	var $enableSQLlog = false;
	var $continueInstallOnError = true;

	// If you want to avoid executing PreparedStatement, set this to true
	// PreparedStatement will be converted to normal SQL statement for execution
	var $avoidPreparedSql = false;

	/**
	 * Performance tunning parameters
	 * See the constructor for initialization
	 */
	var $isdb_default_utf8_charset = true;
	var $enableCache = false;

	var $_cacheinstance = false; // Will be auto-matically initialized if $enableCache is true
	/**
	 * API's to control cache behavior
	 */
	function __setCacheInstance($cacheInstance) {
		$this->_cacheinstance = $cacheInstance;
	}
	/** Return the cache instance reference (using &) */
	function &getCacheInstance() {
		return $this->_cacheinstance;
	}
	function isCacheEnabled() {
		return ($this->enableCache && ($this->getCacheInstance() != false));
	}
	function clearCache() {
		if($this->isCacheEnabled()) $this->getCacheInstance()->resetCache();
	}
	function toggleCache($newstatus) {
		$oldstatus = $this->enableCache;
		$this->enableCache = $newstatus;
		return $oldstatus;
	}

	/**
	 * Manage instance usage of this class
	 */
	static function &getInstance() {
		global $adb, $log;

		if(!isset($adb)) {
			$adb = new self();
		}
		return $adb;
	}

	/*
	 * Reset query result for resuing if cache is enabled.
	 */
	function resetQueryResultToEOF(&$result) {
		if($result) {
			if($result->MoveLast()) {
				$result->MoveNext();
			}
		}
	}

	function isMySQL() { return (stripos($this->dbType ,'mysql') === 0);}
	function isOracle() { return $this->dbType=='oci8'; }
	function isPostgres() { return $this->dbType=='pgsql'; }

	function println($msg) {
		require_once('include/logging.php');
		$log1 = LoggerManager::getLogger('VT');
		if(is_array($msg)) {
			$log1->info("PearDatabse ->".print_r($msg,true));
		} else {
			$log1->info("PearDatabase ->".$msg);
		}
		return $msg;
	}

	function setDieOnError($value){	 $this->dieOnError = $value; }
	function setDatabaseType($type){ $this->dbType = $type; }
	function setUserName($name){ $this->userName = $name; }

	function setOption($name, $value){
		if(isset($this->database)) $this->database->setOption($name, $value);
	}

	function setUserPassword($pass){ $this->userPassword = $pass; }
	function setDatabaseName($db){ $this->dbName = $db;	}
	function setDatabaseHost($host){ $this->dbHostName = $host;	}

	function getDataSourceName(){
		return 	$this->dbType. "://".$this->userName.":".$this->userPassword."@". $this->dbHostName . "/". $this->dbName;
	}

	function startTransaction() {
		if($this->isPostgres()) return;
		$this->checkConnection();
		$this->println("TRANS Started");
		$this->database->StartTrans();
	}

	function completeTransaction() {
		if($this->isPostgres()) return;
		if($this->database->HasFailedTrans()) $this->println("TRANS Rolled Back");
		else $this->println("TRANS Commited");

		$this->database->CompleteTrans();
		$this->println("TRANS Completed");
	}

	function hasFailedTransaction(){ return $this->database->HasFailedTrans(); }

	function checkError($msg='', $dieOnError=false) {
		if($this->dieOnError || $dieOnError) {
			$bt = debug_backtrace();
			$ut = array();
			foreach ($bt as $t) {
				$ut[] = array('file'=>$t['file'],'line'=>$t['line'],'function'=>$t['function']);
			}
			$this->println("ADODB error ".$msg."->[".$this->database->ErrorNo()."]".$this->database->ErrorMsg());
			die($msg."ADODB error ".$msg."->".$this->database->ErrorMsg());
		} else {
			$this->println("ADODB error ".$msg."->[".$this->database->ErrorNo()."]".$this->database->ErrorMsg());
		}
		return false;
	}

	function change_key_case($arr) {
		return is_array($arr)?array_change_key_case($arr):$arr;
	}

	var $req_flist;
	function checkConnection(){
		global $log;
		if(!isset($this->database)) {
			$this->println("TRANS creating new connection");
			$this->connect(false);
		} else {
			//$this->println("checkconnect using old connection");
		}
	}

	/**
	 * Put out the SQL timing information
	 */
	function logSqlTiming($startat, $endat, $sql, $params=false) {
		global $logsqltm, $SQL_LOG_INCLUDE_CALLER;
		// Specifically for timing the SQL execution, you need to enable DEBUG in log4php.properties
		if($logsqltm->isDebugEnabled()){
			if (!empty($SQL_LOG_INCLUDE_CALLER)) {
				$callers = debug_backtrace();
				$callerscount = count($callers);
				$callerfunc = '';
				for($calleridx = 0; $calleridx < $callerscount; ++$calleridx) {
					if($calleridx == 0) {
						// Ignore the first caller information, it will be generally from this file!
						continue;
					}
					// Caller function will be in next information block
					if($calleridx < $callerscount) {
						$callerfunc = $callers[$calleridx+1]['function'];
						if(!empty($callerfunc)) $callerfunc = " ($callerfunc) ";
					}
					$logsqltm->debug(
						"CALLER: (" . $callers[$calleridx]['line'] . ') ' .
						$callers[$calleridx]['file'] . $callerfunc);
				}
			}
			$logsqltm->debug("SQL: " . $sql);
			if($params != null && count($params) > 0) $logsqltm->debug("PARAMS: [" . implode(",", $params) . "]");
			$logsqltm->debug("EXEC: " . ($endat - $startat) ." micros [START=$startat, END=$endat]");
			$logsqltm->debug("");
		}
	}

	/**
	 * Execute SET NAMES UTF-8 on the connection based on configuration.
	 */
	function executeSetNamesUTF8SQL($force = false) {
		global $default_charset;
		// Performance Tuning: If database default charset is UTF-8, we don't need this
		if($default_charset == 'UTF-8' && ($force || !$this->isdb_default_utf8_charset)) {

			$sql_start_time = microtime(true);

			$setnameSql = "SET NAMES utf8";
			$this->database->Execute($setnameSql);
			$this->logSqlTiming($sql_start_time, microtime(true), $setnameSql);
		}
	}

	/**
	 * Execute query in a batch.
	 *
	 * For example:
	 * INSERT INTO TABLE1 VALUES (a,b);
	 * INSERT INTO TABLE1 VALUES (c,d);
	 *
	 * like: INSERT INTO TABLE1 VALUES (a,b), (c,d)
	 */
	function query_batch($prefixsql, $valuearray) {
		if(PerformancePrefs::getBoolean('ALLOW_SQL_QUERY_BATCH')) {
			$sql = $prefixsql;
			$suffixsql = $valuearray;
			if(!is_array($valuearray)) $suffixsql = implode(',', $valuearray);
			$this->query($prefixsql . $suffixsql);
		} else {
			if(is_array($valuearray) && !empty($valuearray)) {
				foreach($valuearray as $suffixsql) {
					$this->query($prefixsql . $suffixsql);
				}
			}
		}
	}

	function query($sql, $dieOnError=false, $msg='') {
		global $log, $default_charset;
		// Performance Tuning: Have we cached the result earlier?
		if($this->isCacheEnabled()) {
			$fromcache = $this->getCacheInstance()->getCacheResult($sql);
			if($fromcache) {
				$log->debug("Using query result from cache: $sql");
				return $fromcache;
			}
		}
		$log->debug('query being executed : '.$sql);
		$this->checkConnection();

		$this->executeSetNamesUTF8SQL();

		$sql_start_time = microtime(true);
		$result = $this->database->Execute($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), $sql);

		$this->lastmysqlrow = -1;
		if(!$result)$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);

		// Performance Tuning: Cache the query result
		if($this->isCacheEnabled()) {
			$this->getCacheInstance()->cacheResult($result, $sql);
		}
		return $result;
	}

	/**
	 * Convert PreparedStatement to SQL statement
	 */
	function convert2Sql($ps, $vals) {
		if(empty($vals)) { return $ps; }
		// TODO: Checks need to be added array out of bounds situations
		for($index = 0; $index < count($vals); $index++) {
			// Package import pushes data after XML parsing, so type-cast it
			if(is_a($vals[$index], 'SimpleXMLElement')) {
				$vals[$index] = (string) $vals[$index];
			}
			if(is_string($vals[$index])) {
				if($vals[$index] == '') {
					$vals[$index] = $this->database->Quote($vals[$index]);
				}
				else {
					$vals[$index] = "'".$this->sql_escape_string($vals[$index]). "'";
				}
			}
			if($vals[$index] === null) {
				$vals[$index] = "NULL";
			}
		}
		$sql = preg_replace_callback("/('[^']*')|(\"[^\"]*\")|([?])/", array(new PreparedQMark2SqlValue($vals),"call"), $ps);
		return $sql;
	}

	/* ADODB prepared statement Execution
	* @param $sql -- Prepared sql statement
	* @param $params -- Parameters for the prepared statement
	* @param $dieOnError -- Set to true, when query execution fails
	* @param $msg -- Error message on query execution failure
	*/
	function pquery($sql, $params, $dieOnError=false, $msg='') {
		global $log, $default_charset;
		if (!isset($params)) $params = array();
		// Performance Tuning: Have we cached the result earlier?
		if($this->isCacheEnabled()) {
			$fromcache = $this->getCacheInstance()->getCacheResult($sql, $params);
			if($fromcache) {
				$log->debug("Using query result from cache: $sql");
				return $fromcache;
			}
		}
		// END
		$log->debug('Prepared sql query being executed : '.$sql);
		$this->checkConnection();

		$this->executeSetNamesUTF8SQL();

		$sql_start_time = microtime(true);
		$params = $this->flatten_array($params);
		if (count($params) > 0) {
			$log->debug('Prepared sql query parameters : [' . implode(",", $params) . ']');
		}

		if($this->avoidPreparedSql || empty($params)) {
			$sql = $this->convert2Sql($sql, $params);
			$result = $this->database->Execute($sql);
		} else {
			$result = $this->database->Execute($sql, $params);
		}
		$sql_end_time = microtime(true);
		$this->logSqlTiming($sql_start_time, $sql_end_time, $sql, $params);

		$this->lastmysqlrow = -1;
		if(!$result)$this->checkError($msg.' Query Failed:' . $sql . '::', $dieOnError);

		// Performance Tuning: Cache the query result
		if($this->isCacheEnabled()) {
			$this->getCacheInstance()->cacheResult($result, $sql, $params);
		}
		return $result;
	}

	/**
	 * Flatten the composite array into single value.
	 * Example:
	 * $input = array(10, 20, array(30, 40), array('key1' => '50', 'key2'=>array(60), 70));
	 * returns array(10, 20, 30, 40, 50, 60, 70);
	 */
	function flatten_array($input, $output=null) {
		if($input == null) return null;
		if($output == null) $output = array();
		foreach($input as $value) {
			if(is_array($value)) {
				$output = $this->flatten_array($value, $output);
			} else {
				$output[] = $value;
			}
		}
		return $output;
	}

	function getEmptyBlob($is_string=true) {
		if ($is_string) return 'null';
		return null;
	}

	function updateBlob($tablename, $colname, $id, $data) {
		$this->println("updateBlob t=".$tablename." c=".$colname." id=".$id);
		$this->checkConnection();
		$this->executeSetNamesUTF8SQL();

		$sql_start_time = microtime(true);
		$result = $this->database->UpdateBlob($tablename, $colname, $data, $id);
		$this->logSqlTiming($sql_start_time, microtime(true), "Update Blob $tablename, $colname, $id");

		$this->println("updateBlob t=".$tablename." c=".$colname." id=".$id." status=".$result);
		return $result;
		}

	function updateBlobFile($tablename, $colname, $id, $filename) {
		$this->println("updateBlobFile t=".$tablename." c=".$colname." id=".$id." f=".$filename);
		$this->checkConnection();
		$this->executeSetNamesUTF8SQL();

		$sql_start_time = microtime(true);
		$result = $this->database->UpdateBlobFile($tablename, $colname, $filename, $id);
		$this->logSqlTiming($sql_start_time, microtime(true), "Update Blob $tablename, $colname, $id");

		$this->println("updateBlobFile t=".$tablename." c=".$colname." id=".$id." f=".$filename." status=".$result);
		return $result;
	}

	function limitQuery($sql,$start,$count, $dieOnError=false, $msg='') {
		global $log;
		$log->debug(' limitQuery sql = '.$sql .' st = '.$start .' co = '.$count);
		$this->checkConnection();

		$this->executeSetNamesUTF8SQL();

		$sql_start_time = microtime(true);
		$result = $this->database->SelectLimit($sql,$count,$start);
		$this->logSqlTiming($sql_start_time, microtime(true), "$sql LIMIT $count, $start");

		if(!$result) $this->checkError($msg.' Limit Query Failed:' . $sql . '::', $dieOnError);
		return $result;
	}

	function getOne($sql, $dieOnError=false, $msg='') {
		$this->println("ADODB getOne sql=".$sql);
		$this->checkConnection();
		$this->executeSetNamesUTF8SQL();
		$sql_start_time = microtime(true);
		$result = $this->database->GetOne($sql);
		$this->logSqlTiming($sql_start_time, microtime(true), "$sql GetONE");
		if(!$result) $this->checkError($msg.' Get one Query Failed:' . $sql . '::', $dieOnError);
		return $result;
	}

	function getFieldsDefinition(&$result) {
		//$this->println("ADODB getFieldsArray");
		$field_array = array();
		if(!isset($result) || empty($result)) {
			return 0;
		}

		$i = 0;
		$n = $result->FieldCount();
		while ($i < $n) {
			$meta = $result->FetchField($i);
			if (!$meta) {
				return 0;
			}
			$field_array[] = $meta;
			$i++;
		}

		//$this->println($field_array);
		return $field_array;
	}

	function getFieldsArray(&$result) {
		//$this->println("ADODB getFieldsArray");
		$field_array = array();
		if(! isset($result) || empty($result)) {
			return 0;
		}

		$i = 0;
		$n = $result->FieldCount();
		while ($i < $n) {
			$meta = $result->FetchField($i);
			if (!$meta) {
				return 0;
			}
			$field_array[] = $meta->name;
			$i++;
		}

		//$this->println($field_array);
		return $field_array;
	}

	function getRowCount(&$result){
		global $log;
		if (isset($result) && !empty($result)) {
			$rows = $result->RecordCount();
		} else {
			$rows = 0;
		}
		return $rows;
	}

	/* ADODB newly added. replacement for mysql_num_rows */
	function num_rows(&$result) {
		return $this->getRowCount($result);
	}

	/* ADODB newly added. replacement form mysql_num_fields */
	function num_fields(&$result) {
		return $result->FieldCount();
	}

	/* ADODB newly added. replacement for mysql_fetch_array() */
	function fetch_array(&$result) {
		if($result->EOF) {
			//$this->println("ADODB fetch_array return null");
			return NULL;
		}
		$arr = $result->FetchRow();
		if(is_array($arr))
			$arr = array_map('to_html', $arr);
			return $this->change_key_case($arr);
	}

	// adds new functions to the PearDatabase class to come around the whole broken query_result() idea
	// Code-Contribution given by weigelt@metux.de - Starts
	function run_query_record_html($query) {
		if (!is_array($rec = $this->run_query_record($query)))
			return $rec;
		foreach ($rec as $walk => $cur)
			$r[$walk] = to_html($cur);
		return $r;
	}

	function sql_quote($data) {
		if (is_array($data)) {
			switch($data{'type'}) {
			case 'text':
			case 'numeric':
			case 'integer':
			case 'oid':
				return $this->quote($data{'value'});
				break;
			case 'timestamp':
				return $this->formatDate($data{'value'});
				break;
			default:
				throw new Exception("unhandled type: ".serialize($cur));
			}
		} else
			return $this->quote($data);
	}

	function sql_insert_data($table, $data) {
		if (!$table)
			throw new Exception("missing table name");
		if (!is_array($data))
			throw new Exception("data must be an array");
		if (!count($table))
			throw new Exception("no data given");

		$sql_fields = '';
		$sql_data = '';
		foreach($data as $walk => $cur) {
			$sql_fields .= ($sql_fields?',':'').$walk;
			$sql_data   .= ($sql_data?',':'').$this->sql_quote($cur);
		}
		return 'INSERT INTO '.$table.' ('.$sql_fields.') VALUES ('.$sql_data.')';
	}

	function run_insert_data($table,$data) {
		$query = $this->sql_insert_data($table,$data);
		$res = $this->query($query);
		$this->query("commit;");
	}

	function run_query_record($query) {
		$result = $this->query($query);
		if (!$result)
			return;
		if (!is_object($result))
			throw new Exception("query \"$query\" failed: ".serialize($result));
		$res = $result->FetchRow();
		return $this->change_key_case($res);
	}

	function run_query_allrecords($query) {
		$result = $this->query($query);
		$records = array();
		$sz = $this->num_rows($result);
		for ($i=0; $i<$sz; $i++)
			$records[$i] = $this->change_key_case($result->FetchRow());
		return $records;
	}

	function run_query_field($query,$field='') {
		$rowdata = $this->run_query_record($query);
		if(isset($field) && $field != '')
			return $rowdata{$field};
		else
			return array_shift($rowdata);
	}

	function run_query_list($query,$field){
		$records = $this->run_query_allrecords($query);
		foreach($records as $walk => $cur)
			$list[] = $cur{$field};
	}

	function run_query_field_html($query,$field){
		return to_html($this->run_query_field($query,$field));
	}

	function result_get_next_record($result){
		return $this->change_key_case($result->FetchRow());
	}

	// create an IN expression from an array/list
	function sql_expr_datalist($a) {
		if (!is_array($a))
			throw new Exception("not an array");
		if (!count($a))
			throw new Exception("empty arrays not allowed");
		$l = '';
		foreach($a as $walk => $cur)
			$l .= ($l?',':'').$this->quote($cur);
		return ' ( '.$l.' ) ';
	}

	// create an IN expression from an record list, take $field within each record
	function sql_expr_datalist_from_records($a,$field) {
		if (!is_array($a))
			throw new Exception("not an array");
		if (!$field)
			throw new Exception("missing field");
		if (!count($a))
			throw new Exception("empty arrays not allowed");
		foreach($a as $walk => $cur)
			$l .= ($l?',':'').$this->quote($cur{$field});
		return ' ( '.$l.' ) ';
	}

	function sql_concat($list) {
		switch ($this->dbType) {
			case 'mysql':
			case 'mysqli':
				return 'concat('.implode(',',$list).')';
			case 'pgsql':
				return '('.implode('||',$list).')';
			default:
				throw new Exception("unsupported dbtype \"".$this->dbType."\"");
		}
	}
	// Code-Contribution given by weigelt@metux.de - Ends

	/* ADODB newly added. replacement for mysql_result() */
	function query_result(&$result, $row, $col=0) {
		if (!is_object($result))
			throw new Exception("result is not an object");
		$result->Move($row);
		$rowdata = $this->change_key_case($result->FetchRow());
		//$this->println($rowdata);
		//Commented strip_selected_tags and added to_html function for HTML tags vulnerability
		if ($col == 'fieldlabel')
			$coldata = $rowdata[$col];
		else
			$coldata = (isset($rowdata[$col]) ? to_html($rowdata[$col]) : '');
		return $coldata;
	}

	// Function to get particular row from the query result
	function query_result_rowdata(&$result, $row=0) {
		if (!is_object($result))
			throw new Exception("result is not an object");
		$result->Move($row);
		$rowdata = $this->change_key_case($result->FetchRow());

		foreach($rowdata as $col => $coldata) {
			if($col != 'fieldlabel')
				$rowdata[$col] = to_html($coldata);
		}
		return $rowdata;
	}

	/**
	 * Get an array representing a row in the result set
	 * Unlike it's non raw siblings this method will not escape
	 * html entities in return strings.
	 *
	 * The case of all the field names is converted to lower case.
	 * as with the other methods.
	 *
	 * @param &$result The query result to fetch from.
	 * @param $row The row number to fetch. It's default value is 0
	 *
	 */
	function raw_query_result_rowdata(&$result, $row=0) {
		if (!is_object($result))
			throw new Exception("result is not an object");
		$result->Move($row);
		return $this->change_key_case($result->FetchRow());
	}

	function getAffectedRowCount(&$result){
		global $log;
		$log->debug('getAffectedRowCount');
		$rows =$this->database->Affected_Rows();
		$log->debug('getAffectedRowCount rows = '.$rows);
		return $rows;
	}

	function requireSingleResult($sql, $dieOnError=false,$msg='', $encode=true) {
		$result = $this->query($sql, $dieOnError, $msg);

		if($this->getRowCount($result ) == 1)
			return $result;
		$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
		return '';
	}

	/* function which extends requireSingleResult api to execute prepared statment
	 */
	function requirePsSingleResult($sql, $params, $dieOnError=false,$msg='', $encode=true) {
		$result = $this->pquery($sql, $params, $dieOnError, $msg);
		if($this->getRowCount($result ) == 1)
			return $result;
		$this->log->error('Rows Returned:'. $this->getRowCount($result) .' More than 1 row returned for '. $sql);
		return '';
	}

	function fetchByAssoc(&$result, $rowNum = -1, $encode=true) {
		if($result->EOF) {
			$this->println("ADODB fetchByAssoc return null");
			return NULL;
		}
		if(isset($result) && $rowNum < 0) {
			$row = $this->change_key_case($result->GetRowAssoc(false));
			$result->MoveNext();
			if($encode && is_array($row))
				return array_map('to_html', $row);
			return $row;
		}

		if($this->getRowCount($result) > $rowNum) {
			$result->Move($rowNum);
		}
		$this->lastmysqlrow = $rowNum;
		$row = $this->change_key_case($result->GetRowAssoc(false));
		$result->MoveNext();
		$this->println($row);

		if($encode && is_array($row))
			return array_map('to_html', $row);
		return $row;
	}

	function getNextRow(&$result, $encode=true){
		global $log;
		$log->info('getNextRow');
		if(isset($result)){
			$row = $this->change_key_case($result->FetchRow());
			if($row && $encode && is_array($row))
				return array_map('to_html', $row);
			return $row;
		}
		return null;
	}

	function fetch_row(&$result, $encode=true) {
		return $this->getNextRow($result);
	}

	function field_name(&$result, $col) {
		return $result->FetchField($col);
	}

	function getQueryTime(){
		return $this->query_time;
	}

	function connect($dieOnError = false) {
		global $dbconfig;
		if(!isset($this->dbType)) {
			$this->println("ADODB Connect : DBType not specified");
			return;
		}
		$this->database = ADONewConnection($this->dbType);

		if (isset($dbconfig['persistent']) and $dbconfig['persistent']) {
			$this->database->PConnect($this->dbHostName, $this->userName, $this->userPassword, $this->dbName);
		} else {
			$this->database->Connect($this->dbHostName, $this->userName, $this->userPassword, $this->dbName);
		}
		$this->database->LogSQL($this->enableSQLlog);

		// 'SET NAMES UTF8' needs to be executed even if database has default CHARSET UTF8
		// as mysql server might be running with different charset!
		// We will notice problem reading UTF8 characters otherwise.
		if($this->isdb_default_utf8_charset) {
			$this->executeSetNamesUTF8SQL(true);
		}
	}

	/**
	 * Constructor
	 */
	function __construct($dbtype='',$host='',$dbname='',$username='',$passwd='') {
		global $currentModule;
		$this->log = LoggerManager::getLogger('PearDatabase_'. $currentModule);
		$this->resetSettings($dbtype,$host,$dbname,$username,$passwd);

		// Initialize performance parameters
		$this->enableCache = PerformancePrefs::getBoolean('CACHE_QUERY_RESULT', false);

		if(!isset($this->dbType)) {
			$this->println("ADODB Connect : DBType not specified");
			return;
		}
		// Initialize the cache object to use.
		if(isset($this->enableCache) && $this->enableCache) {
			$this->__setCacheInstance(new PearDatabaseCache($this));
		}
	}

	function resetSettings($dbtype,$host,$dbname,$username,$passwd){
		global $dbconfig;

		if($host == '') {
			$this->disconnect();
			$this->setDatabaseType($dbconfig['db_type']);
			$this->setUserName($dbconfig['db_username']);
			$this->setUserPassword($dbconfig['db_password']);
			$this->setDatabaseHost( $dbconfig['db_hostname']);
			$this->setDatabaseName($dbconfig['db_name']);
			if($dbconfig['log_sql'])
				$this->enableSQLlog = ($dbconfig['log_sql'] == true);
		} else {
			$this->disconnect();
			$this->setDatabaseType($dbtype);
			$this->setDatabaseName($dbname);
			$this->setUserName($username);
			$this->setUserPassword($passwd);
			$this->setDatabaseHost( $host);
		}
	}

	function quote($string){
		return $this->database->qstr($string);
	}

	function disconnect() {
		$this->println("ADODB disconnect");
		if(isset($this->database)){
			if($this->dbType == "mysql"){
				mysql_close($this->database);
			} elseif($this->dbType == 'mysqli'){
				mysqli_close($this->database);
			} else {
				$this->database->disconnect();
			}
			unset($this->database);
		}
	}

	function setDebug($value) {
		$this->database->debug = $value;
	}

	// ADODB newly added methods
	function createTables($schemaFile, $dbHostName=false, $userName=false, $userPassword=false, $dbName=false, $dbType=false) {
		$this->println("ADODB createTables ".$schemaFile);
		if($dbHostName!=false) $this->dbHostName=$dbHostName;
		if($userName!=false) $this->userName=$userPassword;
		if($userPassword!=false) $this->userPassword=$userPassword;
		if($dbName!=false) $this->dbName=$dbName;
		if($dbType!=false) $this->dbType=$dbType;

		$this->checkConnection();
		$db = $this->database;
		$schema = new adoSchema( $db );
		//Debug Adodb XML Schema
		$schema->XMLS_DEBUG = TRUE;
		//Debug Adodb
		$schema->debug = true;
		$sql = $schema->ParseSchema( $schemaFile );

		$this->println("--------------Starting the table creation------------------");
		$result = $schema->ExecuteSchema( $sql, $this->continueInstallOnError );
		if($result) print $db->errorMsg();
		// needs to return in a decent way
		$this->println("ADODB createTables ".$schemaFile." status=".$result);
		return $result;
	}

	function createTable($tablename, $flds) {
		$this->println("ADODB createTable table=".$tablename." flds=".$flds);
		$this->checkConnection();
		$dict = NewDataDictionary($this->database);
		$sqlarray = $dict->CreateTableSQL($tablename, $flds);
		$result = $dict->ExecuteSQLArray($sqlarray);
		$this->println("ADODB createTable table=".$tablename." flds=".$flds." status=".$result);
		return $result;
	}

	function alterTable($tablename, $flds, $oper) {
		$this->println("ADODB alterTableTable table=".$tablename." flds=".$flds." oper=".$oper);
		$this->checkConnection();
		$dict = NewDataDictionary($this->database);

		if($oper == 'Add_Column') {
			$sqlarray = $dict->AddColumnSQL($tablename, $flds);
		} else if($oper == 'Delete_Column') {
			$sqlarray = $dict->DropColumnSQL($tablename, $flds);
		}
		$this->println("sqlarray");
		$this->println($sqlarray);

		$result = $dict->ExecuteSQLArray($sqlarray);

		$this->println("ADODB alterTableTable table=".$tablename." flds=".$flds." oper=".$oper." status=".$result);
		return $result;
	}

	function getColumnNames($tablename) {
		$this->println("ADODB getColumnNames table=".$tablename);
		$this->checkConnection();
		$adoflds = $this->database->MetaColumns($tablename);
		$i=0;
		$colNames = array();
		if (is_array($adoflds))
		foreach($adoflds as $fld) {
			$colNames[$i] = $fld->name;
			$i++;
		}
		return $colNames;
	}

	function formatString($tablename,$fldname, $str) {
		$this->checkConnection();
		$adoflds = $this->database->MetaColumns($tablename);
		foreach ( $adoflds as $fld ) {
			if(strcasecmp($fld->name,$fldname)==0) {
				$fldtype =strtoupper($fld->type);
				if(strcmp($fldtype,'CHAR')==0 || strcmp($fldtype,'VARCHAR') == 0 || strcmp($fldtype,'VARCHAR2') == 0 || strcmp($fldtype,'LONGTEXT')==0 || strcmp($fldtype,'TEXT')==0) {
					return $this->database->Quote($str);
				} else if(strcmp($fldtype,'DATE') ==0 || strcmp($fldtype,'TIMESTAMP')==0) {
					return $this->formatDate($str);
				} else {
					return $str;
				}
			}
		}
		$this->println("format String Illegal field name ".$fldname);
		return $str;
	}

	function formatDate($datetime, $strip_quotes=false) {
		$this->checkConnection();
		$db = $this->database;
		$date = $db->DBTimeStamp($datetime);
		/* Asha: Stripping single quotes to use the date as parameter for Prepared statement */
		if($strip_quotes == true) {
			return trim($date, "'");
		}
		return $date;
	}

	function getDBDateString($datecolname) {
		$this->checkConnection();
		$db = $this->database;
		return $db->SQLDate('Y-m-d, H:i:s' ,$datecolname);
	}

	function getUniqueID($seqname) {
		$this->checkConnection();
		return $this->database->GenID($seqname."_seq",1);
	}

	function get_tables() {
		$this->checkConnection();
		$result = $this->database->MetaTables('TABLES');
		$this->println($result);
		return $result;
	}

	//To get a function name with respect to the database type which escapes strings in given text
	function sql_escape_string($str)
	{
		if (is_null($str)) return 'NULL';
		$result_data = $this->database->qstr($str);
		$result_data = substr($result_data, 1, -1);
		return $result_data;
	}

	// Function to get the last insert id based on the type of database
	function getLastInsertID($seqname = '') {
		if($this->isPostgres()) {
			$result = pg_query("SELECT currval('".$seqname."_seq')");
			if($result)
			{
				$row = pg_fetch_row($result);
				$last_insert_id = $row[0];
			}
		} else {
			$last_insert_id = $this->database->Insert_ID();
		}
		return $last_insert_id;
	}

	// Function to escape the special characters in database name based on database type.
	function escapeDbName($dbName='') {
		if ($dbName == '') $dbName = $this->dbName;
		if($this->isMySql()) {
			$dbName = "`{$dbName}`";
		}
		return $dbName;
	}
} /* End of class */

if(empty($adb)) {
	$adb = new PearDatabase();
	$adb->connect();
}
//$adb->database->setFetchMode(ADODB_FETCH_NUM);

?>