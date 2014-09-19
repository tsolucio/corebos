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

require_once 'modules/VtigerBackup/Zip/ExtensionZip.php';
require_once 'modules/VtigerBackup/Zip/PHPZip.php';

/**
 * Description of BackupZip
 *
 * @author MAK
 */
abstract class Vtiger_BackupZip {
	protected $fileName;
	protected static $defaultPath;
	private static $isUserPath;
	private static $filePrefix = 'Vtiger-';
	abstract public function addFile($filePath,$parentDirectory);
	abstract public function addDirectory($directoryPath,$zipPath);
	abstract public function close();

	public static function getInstance($folder = null, $filename=null) {
		if(empty($filename)) {
			$filename = self::getDefaultFileName();
		}
		self::$defaultPath = 'backup'.DIRECTORY_SEPARATOR;
		if(empty($folder)) {
			self::$isUserPath = false;
			$folder = self::getDefaultFolderPath();
		} else {
			self::$isUserPath = true;
			$folder = self::addTrailingSlash($folder);
			self::$defaultPath = $folder;
		}
		$filename = $folder.$filename;
		if (extension_loaded('zip') === true) {
			return new Vtiger_ExtensionZip($filename);
		}
		return new Vtiger_PHPZip($filename);
	}

	public static function addTrailingSlash($path) {
		return (strrpos($path, DIRECTORY_SEPARATOR) === strlen($path) - 1)?
				$path.DIRECTORY_SEPARATOR:$path;
	}

	public static function getDefaultFileName($time = null) {
		if(empty($time)) {
			$time = gmmktime();
		}
		return self::$filePrefix.gmdate('d_M_Y-H_i_s-T',$time).'.zip';
	}

	public static function getDefaultFolderPath() {
		if(self::$isUserPath === true) {
			return self::$defaultPath;
		}
		require 'config.inc.php';
		$rootPath = self::addTrailingSlash($root_directory);
		return $rootPath.self::$defaultPath;
	}

	public function getBackupFileName() {
		return $this->fileName;
	}

}
?>