<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/

/**
 * Description of Location
 *
 * @author MAK
 */
abstract class Vtiger_Location {
	protected static $ftpBackup;
	protected static $localBackup;
	public static $FTP = 1;
	public static $LOCAL = 0;
	public $limit = 14;

	public function __construct($limit) {
		$this->limit = $limit;
	}

	public static function getInstance($type, $limit){
		if($type == self::$FTP) {
			if(empty(self::$ftpBackup)) {
				self::$ftpBackup = new Vtiger_FTPBackup($limit);
			}
			return self::$ftpBackup;
		}
		if(empty(self::$localBackup)) {
			self::$localBackup = new Vtiger_LocalBackup($limit);
		}
		return self::$localBackup;
	}

	protected function getFileName($filePath, $sep = DIRECTORY_SEPARATOR) {
		do {
			$done = false;
			$index = strrpos($filePath, $sep);
			if($index !== false && $filePath[$index - 1] == '\\'.$sep) {
				$done = true;
			}
		}while($done);
		if($index == -1) {
			return $filePath;
		}
		return substr($filePath, $index+1);
	}

	//abstract function getPath();
	abstract public function limitbackup();
	abstract public function save($source);
	abstract public function getBackupTimeList();
	abstract public function getBackupFileList();
}
?>