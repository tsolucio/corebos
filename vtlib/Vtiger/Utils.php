<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('config.inc.php');
include_once('include/utils/utils.php');

/**
 * Provides few utility functions
 * @package vtlib
 */
class Vtiger_Utils {

	/**
	 * Check if given value is a number or not
	 * @param mixed String or Integer
	 */
	static function isNumber($value) {
		return is_numeric($value)? intval($value) == $value : false;
	}

	/**
	 * Implode the prefix and suffix as string for given number of times
	 * @param String prefix to use
	 * @param Integer Number of times 
	 * @param String suffix to use (optional)
	 */
	static function implodestr($prefix, $count, $suffix=false) {
		$strvalue = '';
		for($index = 0; $index < $count; ++$index) {
			$strvalue .= $prefix;
			if($suffix && $index != ($count-1)) {
				$strvalue .= $suffix;
			}
		}
		return $strvalue;
	}

	/**
	 * Function to check the file access is made within web root directory as well as is safe for php inclusion
	 * @param String File path to check
	 * @param Boolean False to avoid die() if check fails
	 */
	static function checkFileAccessForInclusion($filepath, $dieOnFail=true) {
		global $root_directory;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if(empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__).'/../../.');
		}

		$unsafeDirectories = array('storage', 'cache', 'test');

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath  = str_replace('\\', '/', $rootdirpath);

		$relativeFilePath = str_replace($rootdirpath, '', $realfilepath);
		$filePathParts = explode('/', $relativeFilePath);

		if(stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
			if($dieOnFail) {
				echo "Sorry! Attempt to access restricted file.<br>";
				echo "We are looking for this file path: $filepath<br>";
				echo "We are looking here:<br> Real file path: $realfilepath<br>";
				echo "Root dir path: $rootdirpath<br>";
				die();
			}
			return false;
		}
		return true;
	}

	/** 
	 * Function to check the file access is made within web root directory. 
	 * @param String File path to check
	 * @param Boolean False to avoid die() if check fails
	 */
	static function checkFileAccess($filepath, $dieOnFail=true) {
		global $root_directory;

		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if(empty($use_root_directory)) {
			$use_root_directory = realpath(dirname(__FILE__).'/../../.');
		}

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath  = str_replace('\\', '/', $rootdirpath);

		if(stripos($realfilepath, $rootdirpath) !== 0) {
			if($dieOnFail) {
				echo "Sorry! Attempt to access restricted file.<br>";
				echo "We are looking for this file path: $filepath<br>";
				echo "We are looking here:<br> Real file path: $realfilepath<br>";
				echo "Root dir path: $rootdirpath<br>";
				die();
			}
			return false;
		}
		return true;
	}

	/**
	 * Log the debug message 
	 * @param String Log message
	 * @param Boolean true to append end-of-line, false otherwise
	 */
	static function Log($message, $delimit=true) {
		global $Vtiger_Utils_Log, $log;
		
		$log->debug($message);
		if(!isset($Vtiger_Utils_Log) || $Vtiger_Utils_Log == false) return;

		print_r($message);
		if($delimit) {
			if(isset($_REQUEST)) echo "<BR>";
			else echo "\n";
		}
	}

	/**
	 * Escape the string to avoid SQL Injection attacks.
	 * @param String Sql statement string
	 */
	static function SQLEscape($value) {
		if($value == null) return $value;
		global $adb;
		return $adb->sql_escape_string($value);
	}

	/**
	 * Check if table is present in database
	 * @param String tablename to check
	 */
	static function CheckTable($tablename) {
		global $adb;
		$old_dieOnError = $adb->dieOnError;
		$adb->dieOnError = false;

		$tablename = Vtiger_Utils::SQLEscape($tablename);
		$tablecheck = $adb->pquery("SELECT 1 FROM $tablename LIMIT 1", array());

		$tablePresent = true;
		if(empty($tablecheck))
			$tablePresent = false;

		$adb->dieOnError = $old_dieOnError;
		return $tablePresent;
	}

	/**
	 * Create table (supressing failure)
	 * @param String tablename to create
	 * @param String table creation criteria like '(columnname columntype, ....)' 
	 * @param String Optional suffix to add during table creation
	 * <br>
	 * will be appended to CREATE TABLE $tablename SQL
	 */
	static function CreateTable($tablename, $criteria, $suffixTableMeta=false) {
		global $adb;

		$org_dieOnError = $adb->dieOnError;
		$adb->dieOnError = false;
		$sql = "CREATE TABLE " . $tablename . $criteria;
		if($suffixTableMeta !== false) {
			if($suffixTableMeta === true) {
				if($adb->isMySQL()) {
					$suffixTableMeta = ' ENGINE=InnoDB DEFAULT CHARSET=utf8';
				} else {
					// TODO Handle other database types.
				}
			}
			$sql .= $suffixTableMeta;
		}
		$adb->pquery($sql, array());
		$adb->dieOnError = $org_dieOnError;	
	}

	/**
	 * Alter existing table
	 * @param String tablename to alter
	 * @param String alter criteria like ' ADD columnname columntype' <br>
	 * will be appended to ALTER TABLE $tablename SQL
	 */
	static function AlterTable($tablename, $criteria) {
		global $adb;
		$adb->pquery("ALTER TABLE " . $tablename . $criteria, array());
	}

	/**
	 * Add column to existing table
	 * @param String tablename to alter
	 * @param String columnname to add
	 * @param String columntype (criteria like 'VARCHAR(100)') 
	 */
	static function AddColumn($tablename, $columnname, $criteria) {
		global $adb;
		if(!in_array($columnname, $adb->getColumnNames($tablename))) {
			self::AlterTable($tablename, " ADD COLUMN $columnname $criteria");
		}
	}

	/**
	 * Get SQL query
	 * @param String SQL query statement
	 */
	static function ExecuteQuery($sqlquery, $supressdie=false) {
		global $adb;
		$old_dieOnError = $adb->dieOnError;

		if($supressdie) $adb->dieOnError = false;

		$adb->pquery($sqlquery, array());

		$adb->dieOnError = $old_dieOnError;
	}

	/**
	 * Get CREATE SQL for given table
	 * @param String tablename for which CREATE SQL is requried
	 */
	static function CreateTableSql($tablename) {
		global $adb;

		$create_table = $adb->pquery("SHOW CREATE TABLE $tablename", array());
		$sql = decode_html($adb->query_result($create_table, 0, 1));
		return $sql;
	}

	/**
	 * Check if the given SQL is a CREATE statement
	 * @param String SQL String
	 */
	static function IsCreateSql($sql) {
		if(preg_match('/(CREATE TABLE)/', strtoupper($sql))) {
			return true;
		}
		return false;
	}

	/**
	 * Check if the given SQL is destructive (DELETE's DATA)
	 * @param String SQL String
	 */
	static function IsDestructiveSql($sql) {
		if(preg_match('/(DROP TABLE)|(DROP COLUMN)|(DELETE FROM)/', 
			strtoupper($sql))) {
			return true;
		}
		return false;
	}
}
?>