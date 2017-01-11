<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * Provides API to work with zip file extractions
 * @package vtlib
 */
class Vtiger_Unzip {

	public $fileName;
	private $zipa;
	private static $compressedList = array();

	public function __construct($filename){
		$this->fileName = $filename;
		$this->zipa = new ZipArchive();
		$ret = $this->zipa->open($filename);
		if ($this->zipa->open($filename)!==TRUE) {
			throw new Exception("cannot open <$filename>");
		}
	}

	public function close() {
		$this->zipa->close();
	}

	/**
	 * Check existence of path in the given array
	 * @access private
	 */
	function __checkPathInArray($path, $pathArray) {
		foreach($pathArray as $checkPath) {
			if(strpos($path, $checkPath) === 0)
				return true;
		}
		return false;
	}

	/**
	 * Check if the file path is directory
	 * @param String Zip file path
	 */
	function isdir($filepath) {
		if(substr($filepath, -1, 1) == "/") return true;
		return false;
	}

	/**
	 * Extended unzipAll function
	 * Allows you to rename while unzipping and handle exclusions.
	 */
	public function unzipAllEx($targetDir=false, $includeExclude=false, $renamePaths=false, $ignoreFiles=false, $baseDir="", $applyChmod=0775){

		// We want to always maintain the structure
		$maintainStructure = true;

		if($targetDir === false)
			$targetDir = dirname(__FILE__)."/";

		if($renamePaths === false) $renamePaths = Array();

		/*
		 * Setup includeExclude parameter
		 * FORMAT:
		 * Array(
		 * 'include'=> Array('zipfilepath1', 'zipfilepath2', ...),
		 * 'exclude'=> Array('zipfilepath3', ...)
		 * )
		 *
		 * DEFAULT: If include is specified only files under the specified path will be included.
		 * If exclude is specified folders or files will be excluded. 
		 */
		if($includeExclude === false) $includeExclude = Array();

		$lista = $this->getList();
		if(sizeof($lista)) {
			foreach($lista as $fileName=>$trash){
			// Should the file be ignored?
			if(isset($includeExclude['include']) && $includeExclude['include'] && 
				!$this->__checkPathInArray($fileName, $includeExclude['include'])) {
					// Do not include something not specified in include
					continue;
			}
			if(isset($includeExclude['exclude']) && $includeExclude['exclude'] && 
				$this->__checkPathInArray($fileName, $includeExclude['exclude'])) {
					// Do not include something not specified in include
					continue;
			}
			// END

			$dirname  = dirname($fileName);

			// Rename the path with the matching one (as specified)
			if(!empty($renamePaths)) {
				foreach($renamePaths as $lookup => $replace) {
					if(strpos($dirname, $lookup) === 0) {
						$dirname = substr_replace($dirname, $replace, 0, strlen($lookup));
						break;
					}
				}
			}
			// END

			$outDN = "$targetDir/$dirname";
			
			if(substr($dirname, 0, strlen($baseDir)) != $baseDir)
				continue;
			
			if(!is_dir($outDN) && $maintainStructure){
				$str = "";
				$folders = explode("/", $dirname);
				foreach($folders as $folder){
					$str = $str?"$str/$folder":$folder;
					if(!is_dir("$targetDir/$str")){
						mkdir("$targetDir/$str");
						if($applyChmod)
							@chmod("$targetDir/$str", $applyChmod);
					}
				}
			}
			if(substr($fileName, -1, 1) == "/")
				continue;

			$this->unzip($fileName, "$targetDir/$dirname/".basename($fileName), 0664);
		} // foreach
		} // has list
	}

	public function getList(){
		if(sizeof($this->compressedList)){
			return $this->compressedList;
		}
		for ($f=0; $f<$this->zipa->numFiles;$f++) {
			$details = $this->zipa->statIndex($f);
			$filename = $details['name'];
			$this->compressedList[$filename]['file_name']          = $filename;
			$this->compressedList[$filename]['compression_method'] = $details['comp_method'];
			$this->compressedList[$filename]['lastmod_datetime']   = $details['mtime'];
			$this->compressedList[$filename]['crc']                = $details['crc'];
			$this->compressedList[$filename]['compressed_size']    = $details['comp_size'];
			$this->compressedList[$filename]['uncompressed_size']  = $details['size'];
		}
		return $this->compressedList;
	}

	public function each($EachCallback){
		// $EachCallback(filename, fileinfo);
		if(!is_callable($EachCallback))
			die(get_class($this).":: You called 'each' method, but failed to provide a Callback as argument. Usage: \$zip->each(function(\$filename, \$fileinfo) use (\$zip){ ... \$zip->unzip(\$filename, 'uncompress/\$filename'); }).");

		$lista = $this->getList();
		if(sizeof($lista)) foreach($lista as $fileName=>$fileInfo){
			if(false === call_user_func($EachCallback, $fileName, $fileInfo)){
				return false;
			}
		}
		return true;
	}

	public function unzip($compressedFileName, $targetFileName=false, $applyChmod=0664) {
		if(!sizeof($this->compressedList)){
			$this->getList();
		}

		$fdetails = $this->compressedList[$compressedFileName];
		if(!isset($this->compressedList[$compressedFileName])){
			throw new Exception("File '$compressedFileName' is not in the zip ".$this->fileName);
		}
		if(substr($compressedFileName, -1) == "/"){
			throw new Exception("Trying to unzip a folder name '$compressedFileName'.");
		}
		$destPath = ($targetFileName ? $targetFileName : $compressedFileName);
		$destInfo = pathinfo($destPath);
		$destDir = $destInfo['dirname'];
		$destFile = $destInfo['basename'];
		global $log;$log->fatal(array($destDir, $compressedFileName));
		$ret = $this->zipa->extractTo($destDir, array($compressedFileName));
		if ($newname!=$compressedFileName) {
			rename($destDir.'/'.$compressedFileName, $destPath);
		}
		if($applyChmod && $ret)
			@chmod($destPath, $applyChmod == 0755 ? 0664 : $applyChmod);
		return $ret;
	}

	public function unzipAll($targetDir=false, $baseDir='', $maintainStructure=true, $applyChmod=0775) {
		if($targetDir === false)
			$targetDir = dirname(__FILE__).'/';
		$ret = $this->zipa->extractTo($targetDir);
		return $ret;
	}

}
?>
