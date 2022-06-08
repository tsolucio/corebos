<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'config.inc.php';
include_once 'include/utils/utils.php';

/**
 * Provides few utility functions
 * @package vtlib
 */
class Vtiger_Utils {

	/**
	 * Check if given value is a number or not
	 * @param mixed String or Integer
	 */
	public static function isNumber($value) {
		return is_numeric($value)? (int)$value == $value : false;
	}

	/**
	 * Implode the prefix and suffix as string for given number of times
	 * @param string prefix to use
	 * @param integer Number of times
	 * @param string suffix to use (optional)
	 */
	public static function implodestr($prefix, $count, $suffix = false) {
		$strvalue = '';
		for ($index = 0; $index < $count; ++$index) {
			$strvalue .= $prefix;
			if ($suffix && $index != ($count-1)) {
				$strvalue .= $suffix;
			}
		}
		return $strvalue;
	}

	/**
	 * Function to check the file access is made within web root directory as well as is safe for php inclusion
	 * @param string File path to check
	 * @param boolean False to avoid die() if check fails
	 */
	public static function checkFileAccessForInclusion($filepath, $dieOnFail = true) {
		global $root_directory;
		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(__DIR__.'/../../.');
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

		if (stripos($realfilepath, $rootdirpath) !== 0 || in_array($filePathParts[0], $unsafeDirectories)) {
			if ($dieOnFail) {
				global $default_charset;
				echo 'Sorry! Attempt to access restricted file.<br>';
				echo 'We are looking for this file path: '.htmlspecialchars($filepath, ENT_QUOTES, $default_charset).'<br>';
				echo 'We are looking here:<br> Real file path: '.htmlspecialchars($realfilepath, ENT_QUOTES, $default_charset).'<br>';
				echo 'Root dir path: '.htmlspecialchars($rootdirpath, ENT_QUOTES, $default_charset).'<br>';
				die();
			}
			return false;
		}
		return true;
	}

	/**
	 * Function to check the file access is made within web root directory.
	 * @param string File path to check
	 * @param boolean False to avoid die() if check fails
	 */
	public static function checkFileAccess($filepath, $dieOnFail = true) {
		global $root_directory;

		// Set the base directory to compare with
		$use_root_directory = $root_directory;
		if (empty($use_root_directory)) {
			$use_root_directory = realpath(__DIR__.'/../../.');
		}

		$realfilepath = realpath($filepath);

		/** Replace all \\ with \ first */
		$realfilepath = str_replace('\\\\', '\\', $realfilepath);
		$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

		/** Replace all \ with / now */
		$realfilepath = str_replace('\\', '/', $realfilepath);
		$rootdirpath  = str_replace('\\', '/', $rootdirpath);

		if (stripos($realfilepath, $rootdirpath) !== 0) {
			if ($dieOnFail) {
				global $default_charset;
				echo 'Sorry! Attempt to access restricted file.<br>';
				echo 'We are looking for this file path: '.htmlspecialchars($filepath, ENT_QUOTES, $default_charset).'<br>';
				echo 'We are looking here:<br> Real file path: '.htmlspecialchars($realfilepath, ENT_QUOTES, $default_charset).'<br>';
				echo 'Root dir path: '.htmlspecialchars($rootdirpath, ENT_QUOTES, $default_charset).'<br>';
				die();
			}
			return false;
		}
		return true;
	}

	/**
	 * Log the debug message
	 * @param string Log message
	 * @param boolean true to append end-of-line, false otherwise
	 */
	public static function Log($message, $delimit = true) {
		global $Vtiger_Utils_Log, $log;

		$log->debug($message);
		if (!isset($Vtiger_Utils_Log) || !$Vtiger_Utils_Log) {
			return;
		}

		print_r($message);
		if ($delimit) {
			if (isset($_REQUEST)) {
				echo '<BR>';
			} else {
				echo "\n";
			}
		}
	}

	/**
	 * Escape the string to avoid SQL Injection attacks.
	 * @param string Sql statement string
	 */
	public static function SQLEscape($value) {
		if ($value == null) {
			return $value;
		}
		global $adb;
		return $adb->sql_escape_string($value);
	}

	/**
	 * Check if table is present in database
	 * @param string tablename to check
	 */
	public static function CheckTable($tablename) {
		global $adb;
		$old_dieOnError = $adb->dieOnError;
		$adb->dieOnError = false;

		$tablename = Vtiger_Utils::SQLEscape($tablename);
		$tablecheck = $adb->pquery('SHOW TABLES LIKE ?', array($tablename));

		$tablePresent = true;
		if (empty($tablecheck) || $adb->num_rows($tablecheck) === 0) {
			$tablePresent = false;
		}

		$adb->dieOnError = $old_dieOnError;
		return $tablePresent;
	}

	/**
	 * Create table (supressing failure)
	 * @param string tablename to create
	 * @param string table creation criteria like '(columnname columntype, ....)'
	 * @param string Optional suffix to add during table creation will be appended to CREATE TABLE $tablename SQL
	 */
	public static function CreateTable($tablename, $criteria, $suffixTableMeta = false) {
		global $adb;

		$org_dieOnError = $adb->dieOnError;
		$adb->dieOnError = false;
		$sql = 'CREATE TABLE ' . $tablename . $criteria;
		if ($suffixTableMeta !== false) {
			if ($suffixTableMeta === true) {
				if ($adb->isMySQL()) {
					$suffixTableMeta = ' ENGINE=InnoDB DEFAULT CHARSET=utf8';
				} else {
					$suffixTableMeta = ''; // other database types
				}
			}
			$sql .= $suffixTableMeta;
		}
		$adb->pquery($sql, array());
		$adb->dieOnError = $org_dieOnError;
	}

	/**
	 * Alter existing table
	 * @param string tablename to alter
	 * @param string alter criteria like ' ADD columnname columntype' <br> will be appended to ALTER TABLE $tablename SQL
	 */
	public static function AlterTable($tablename, $criteria) {
		global $adb;
		$adb->query('ALTER TABLE ' . $tablename . $criteria);
	}

	/**
	 * Add column to existing table
	 * @param string tablename to alter
	 * @param string columnname to add
	 * @param string columntype (criteria like 'VARCHAR(100)')
	 */
	public static function AddColumn($tablename, $columnname, $criteria) {
		global $adb;
		if (!in_array($columnname, $adb->getColumnNames($tablename))) {
			self::AlterTable($tablename, " ADD COLUMN $columnname $criteria");
		}
	}

	/**
	 * Get SQL query
	 * @param string SQL query statement
	 */
	public static function ExecuteQuery($sqlquery, $supressdie = false) {
		global $adb;
		$old_dieOnError = $adb->dieOnError;

		if ($supressdie) {
			$adb->dieOnError = false;
		}

		$rs = $adb->pquery($sqlquery, array());

		$adb->dieOnError = $old_dieOnError;
		return $rs;
	}

	/**
	 * Get CREATE SQL for given table
	 * @param string tablename for which CREATE SQL is requried
	 */
	public static function CreateTableSql($tablename) {
		global $adb;
		$sql = '';
		$create_table = $adb->pquery("SHOW CREATE TABLE $tablename", array());
		if ($create_table) {
			$sql = decode_html($adb->query_result($create_table, 0, 1));
		}
		return $sql;
	}

	/**
	 * Check if the given SQL is a CREATE statement
	 * @param string SQL String
	 */
	public static function IsCreateSql($sql) {
		if (preg_match('/(CREATE TABLE)/', strtoupper($sql))) {
			return true;
		}
		return false;
	}

	/**
	 * Check if the given SQL is destructive (DELETE's DATA)
	 * @param string SQL String
	 */
	public static function IsDestructiveSql($sql) {
		if (preg_match(
			'/(DROP TABLE)|(DROP COLUMN)|(DELETE FROM)/',
			strtoupper($sql)
		)) {
			return true;
		}
		return false;
	}
}
?>
