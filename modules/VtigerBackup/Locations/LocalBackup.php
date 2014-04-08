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

require_once 'modules/VtigerBackup/Locations/Location.php';

/**
 * Description of LocalBackup
 *
 * @author MAK
 */
class Vtiger_LocalBackup extends Vtiger_Location{
	protected $path;
	public function __construct($limit) {
		parent::__construct($limit);
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT * FROM vtiger_systems WHERE server_type = ?",
				array('local_backup'));
        $this->path = $db->query_result($result,0,'server_path');
		$this->path = $this->addTrailingSlash($this->path);
	}

	public function getPath() {
		return $this->path;
	}

	public function limitbackup() {
		$directoryPath = $this->getPath();
		$fileList = $this->getBackupTimeList();
		for ($index=0; count($fileList) > $this->limit -1 ; ++$index) {
			$fileName = Vtiger_BackupZip::getDefaultFileName($fileList[$index]);
			unlink($directoryPath.$fileName);
			unset($fileList[$index]);
		}
	}

	public function getBackupTimeList() {
		$fileList = array();
		$directoryPath = $this->getPath();
		// initialize an iterator
		// pass it the directory to be processed
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath),
				RecursiveIteratorIterator::SELF_FIRST);

		// iterate over the directory
		// add each file found to the archive
		foreach ($iterator as $file) {
			$file = realpath($file);
			if (is_file($file) === true) {
				$fileName = $this->getFileName($file);
				$fileName = explode('-',$fileName);
				$fileName = $fileName[1];
				$date = substr($fileName, 0, strrpos($fileName,'.'));
				$date = str_replace('_',':',$date);
				if(strtotime($date) !== false) {
					$fileList[] = strtotime($date);
				}
			}
		}
		sort($fileList);
		return $fileList;
	}

	public function getBackupFileList() {
		$fileList = array();
		$directoryPath = $this->getPath();
		// initialize an iterator
		// pass it the directory to be processed
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath),
				RecursiveIteratorIterator::SELF_FIRST);

		// iterate over the directory
		// add each file found to the archive
		foreach ($iterator as $file) {
			$file = realpath($file);
			if (is_file($file) === true) {
				$origFileName = $this->getFileName($file);
				$fileName = explode('-',$origFileName);
				$fileName = $fileName[1];
				$date = substr($fileName, 0, strrpos($fileName,'.'));
				$date = str_replace('_',':',$date);
				if(strtotime($date) !== false) {
					$fileList[] = $origFileName;
				}
			}
		}
		return $fileList;
	}
	
	public function getBackupFileInfoList() {
		$fileInfoList = array();
		$directoryPath = $this->getPath();
		// initialize an iterator
		// pass it the directory to be processed
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryPath),
				RecursiveIteratorIterator::SELF_FIRST);

		// iterate over the directory
		// add each file found to the archive
		foreach ($iterator as $file) {
			$info = array();
			$file = realpath($file);
			if (is_file($file) === true) {
				$origFileName = $this->getFileName($file);
				$fileName = explode('-',$origFileName);
				$fileName = $fileName[1];
				$date = substr($fileName, 0, strrpos($fileName,'.'));
				$date = str_replace('_',':',$date);
				if(strtotime($date) !== false) {
					$info['time'] = $date;
					$info['name'] = $origFileName;
					$info['size'] = filesize($file).' Bytes';
					$fileInfoList[$date] = $info;
				}
			}
		}
		ksort($fileInfoList);
		return $fileInfoList;
	}

	public function addTrailingSlash($path) {
		return (strrpos($path, DIRECTORY_SEPARATOR) !== strlen($path) - 1)?
				$path.DIRECTORY_SEPARATOR:$path;
	}

	public function save($source) {
		//Nothing to do, as the correct path is already given
	}
}
?>